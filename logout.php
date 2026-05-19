<?php
require_once __DIR__ . '/includes/init.php';
clearRememberCookie();
session_unset();
session_destroy();
session_start();
redirect('index.php');
