# Discover Saudi · اكتشف السعودية

Course project for **CSC457 — Internet Technologies**, King Saud University.

An Arabic website introducing the 13 regions of Saudi Arabia.

🌐 **Live demo:** [saudi.is-great.net](https://saudi.is-great.net)

## Tech stack

![PHP](https://img.shields.io/badge/PHP_8-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)
![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)
![Apache](https://img.shields.io/badge/Apache-D22128?style=for-the-badge&logo=apache&logoColor=white)

## Setup

1. Install [Laragon](https://laragon.org) or [XAMPP](https://www.apachefriends.org).
2. Start Apache and MySQL from the control panel.
3. Run these in a terminal (use `C:\xampp\htdocs` if you chose XAMPP):

```bat
cd C:\laragon\www
git clone https://github.com/FerasMad/discover-saudi.git
cd discover-saudi
copy public\includes\db.example.php public\includes\db.php
```

4. Open `http://localhost/phpmyadmin` and import these two files in order:
   - `sql/schema.sql`
   - `sql/seed_regions.sql`
5. Open the site: `http://localhost/discover-saudi/public/`

## Admin login

| Field | Value |
|---|---|
| URL | `/admin/login.php` |
| Username | `admin` |
| Password | `admin123` |

## Project structure

```
sql/             database schema and seed
public/          web root
  index.php      home
  gallery.php    regions gallery
  place.php      region details
  admin/         admin pages (login, dashboard, add, update, delete)
  assets/        css, js, images, fonts
  includes/      db, helpers, auth, header, footer
```

## Team

- فراس مدخلي
- تميم العصيمي

## Photos & fonts

Region photos are bundled locally from Unsplash under its free license. Thmanyah typeface used under the license bundled with its family. Full credits in [`NOTICE.md`](./NOTICE.md).
