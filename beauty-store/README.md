# Beauty Store

PHP/MySQL ecommerce demo inspired by The Ordinary.

## XAMPP setup (use this, not phpMyAdmin)

1. Copy the project folder to: `C:\xampp\htdocs\beauty-store`
2. In XAMPP Control Panel, start **Apache** and **MySQL**
3. Open in your browser: **http://localhost/beauty-store/install.php**
4. Click **Install database now**
5. Open the store: **http://localhost/beauty-store/**

**Do not import SQL files in phpMyAdmin.** Use install.php only.

### Admin

- URL: http://localhost/beauty-store/admin/
- Email: admin@beauty.com
- Password: password

### If install fails

- Check `config/database.php` (default XAMPP: user `root`, empty password)
- Make sure the folder name in the URL matches the folder in `htdocs`
