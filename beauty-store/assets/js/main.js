(function () {
    'use strict';

    function getBaseUrl() {
        var link = document.querySelector('link[href*="assets/css/style.css"]');
        if (!link) return '/';
        var href = link.getAttribute('href');
        var marker = 'assets/css/style.css';
        var idx = href.indexOf(marker);
        return idx >= 0 ? href.slice(0, idx) : '/';
    }

    var baseUrl = getBaseUrl();

    function showToast(message) {
        var container = document.querySelector('.toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container';
            document.body.appendChild(container);
        }
        var toast = document.createElement('div');
        toast.className = 'toast';
        toast.textContent = message;
        container.appendChild(toast);
        setTimeout(function () {
            toast.classList.add('toast-out');
            setTimeout(function () {
                toast.remove();
            }, 300);
        }, 2800);
    }

    function updateCartBadge(count) {
        var badge = document.querySelector('.cart-badge');
        var btn = document.querySelector('.cart-btn');
        if (!btn) return;
        if (count > 0) {
            if (!badge) {
                badge = document.createElement('span');
                badge.className = 'cart-badge';
                btn.appendChild(badge);
            }
            badge.textContent = count;
        } else if (badge) {
            badge.remove();
        }
    }

    function cartApi(action, data) {
        var form = new FormData();
        form.append('action', action);
        Object.keys(data).forEach(function (key) {
            form.append(key, data[key]);
        });
        return fetch(baseUrl + 'api/cart.php', {
            method: 'POST',
            body: form,
            credentials: 'same-origin'
        }).then(function (res) {
            return res.json();
        });
    }

    function formatMoney(amount) {
        return Number(amount).toFixed(2) + ' DZD';
    }

    var header = document.querySelector('.site-header');
    if (header) {
        window.addEventListener('scroll', function () {
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
    }

    var revealElements = document.querySelectorAll('.reveal');
    if ('IntersectionObserver' in window) {
        var revealObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('active');
                    revealObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.15 });

        revealElements.forEach(function (el) {
            revealObserver.observe(el);
        });
    } else {
        revealElements.forEach(function (el) {
            el.classList.add('active');
        });
    }

    var toggle = document.getElementById('mobileToggle');
    var nav = document.getElementById('mainNav');
    if (toggle && nav) {
        toggle.addEventListener('click', function () {
            nav.classList.toggle('is-open');
        });
    }

    document.querySelectorAll('.thumb-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var image = btn.getAttribute('data-image');
            var mainImage = document.getElementById('main-product-image');
            if (image && mainImage) {
                mainImage.src = image;
            }
            document.querySelectorAll('.thumb-btn').forEach(function (item) {
                item.style.borderColor = 'transparent';
            });
            btn.style.borderColor = 'var(--text-main)';
        });
    });

    document.querySelectorAll('.btn-add-cart').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var id = btn.getAttribute('data-id');
            btn.disabled = true;
            cartApi('add', { product_id: id, quantity: 1 })
                .then(function (data) {
                    if (data.ok) {
                        updateCartBadge(data.count);
                        showToast(data.message || 'Added to cart');
                    } else {
                        showToast(data.error || 'Could not add to cart');
                    }
                })
                .catch(function () {
                    showToast('Network error');
                })
                .finally(function () {
                    btn.disabled = false;
                });
        });
    });

    var addForm = document.querySelector('.add-to-cart-form');
    if (addForm) {
        addForm.addEventListener('submit', function (e) {
            e.preventDefault();
            var id = addForm.getAttribute('data-id');
            var qtyInput = addForm.querySelector('[name="quantity"]');
            var qty = qtyInput ? parseInt(qtyInput.value, 10) : 1;
            var submitBtn = addForm.querySelector('button[type="submit"]');
            if (submitBtn) submitBtn.disabled = true;
            cartApi('add', { product_id: id, quantity: qty || 1 })
                .then(function (data) {
                    if (data.ok) {
                        updateCartBadge(data.count);
                        showToast('Added to cart');
                    } else {
                        showToast(data.error || 'Could not add to cart');
                    }
                })
                .catch(function () {
                    showToast('Network error');
                })
                .finally(function () {
                    if (submitBtn) submitBtn.disabled = false;
                });
        });
    }

    document.querySelectorAll('.cart-qty-input').forEach(function (input) {
        input.addEventListener('change', function () {
            var id = input.getAttribute('data-id');
            var qty = parseInt(input.value, 10) || 1;
            cartApi('update', { product_id: id, quantity: qty })
                .then(function (data) {
                    if (data.ok) {
                        updateCartBadge(data.count);
                        if (typeof data.subtotal === 'number') {
                            var sub = document.getElementById('cartSubtotal');
                            if (sub) sub.textContent = formatMoney(data.subtotal);
                        }
                        window.location.reload();
                    } else {
                        showToast(data.error || 'Update failed');
                    }
                });
        });
    });

    document.querySelectorAll('.cart-remove').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var id = btn.getAttribute('data-id');
            cartApi('remove', { product_id: id })
                .then(function (data) {
                    if (data.ok) {
                        updateCartBadge(data.count);
                        var row = btn.closest('.cart-item');
                        if (row) row.remove();
                        window.location.reload();
                    }
                });
        });
    });
})();
