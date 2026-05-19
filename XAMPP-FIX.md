# Fix Internal Server Error (500)

## Step 1 - Delete .htaccess

In File Explorer open:

C:\xampp\htdocs\beauty-store

If you see a file named **.htaccess**, delete it.

(This file causes 500 errors on XAMPP.)

## Step 2 - Copy fresh project files

Copy the whole beauty-store folder from your project to:

C:\xampp\htdocs\beauty-store

Replace all files when asked.

## Step 3 - Fix encoding (if you saw PHP code as text before)

Double-click **fix-encoding.bat** in the project folder, then copy to htdocs again.

## Step 4 - Restart Apache

XAMPP Control Panel: Stop Apache, then Start Apache.

## Step 5 - Test in this order

1. http://localhost/beauty-store/test-php.php
   Must show: PHP works

2. http://localhost/beauty-store/install.php
   Click: Install database now

3. http://localhost/beauty-store/
   Store homepage

## Admin

http://localhost/beauty-store/admin/

Email: admin@beauty.com
Password: password
