# XAMPP Setup Guide

## 1. Copy project

Copy the folder to:

C:\xampp\htdocs\beauty-store

## 2. Start XAMPP

Start **Apache** and **MySQL** in the XAMPP Control Panel.

## 3. Create database

1. Open http://localhost/phpmyadmin
2. Click **New**
3. Name: beauty_store
4. Collation: utf8mb4_unicode_ci
5. Click **Create**

## 4. Import SQL (use schema-xampp.sql)

1. Click **beauty_store** in the left sidebar
2. Open the **Import** tab
3. Choose file: database/schema-xampp.sql
4. Click **Import**

Do NOT use schema.sql in phpMyAdmin. Use schema-xampp.sql instead.

## 5. Config

Edit config/database.php if needed:

- DB_HOST: localhost
- DB_NAME: beauty_store
- DB_USER: root
- DB_PASS: (leave empty for default XAMPP)

## 6. Open site

- Store: http://localhost/beauty-store/
- Admin: http://localhost/beauty-store/admin/
- Login: admin@beauty.com / password

## Import succeeded but you see red errors?

If you see **"Import has been successfully finished, 16 queries executed"** then the database is ready. Ignore any follow-up errors.

Those errors usually appear when you paste the same file into the **SQL** tab (not Import). phpMyAdmin static analysis breaks on pasted text or wrong encoding (UTF-16). You do not need to run the file again.

### Verify the database

In phpMyAdmin, select beauty_store, open SQL tab, run only this:

    SELECT COUNT(*) AS products FROM products;

You should get **8**.

### If import still fails

Use the **Import** tab only (do not paste into SQL). Select database beauty_store first.

## Why the old file failed

schema.sql runs CREATE DATABASE and USE commands. phpMyAdmin often rejects those during import.
