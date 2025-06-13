# 🗳️ Media Voting System (MVS)

A lightweight, self-hosted voting platform for families or friends to share and vote on media files. The project is written in **PHP** and stores all data in JSON files so there is no external database requirement.

---

## ✨ Features

- Mobile friendly interface  
- Vote on images, videos, PDFs, YouTube links and text snippets  
- Supports two voting rules:
  - `single` – one vote per user
  - `multi_unique` – multiple votes per category with no duplicates  
- Users can cancel votes  
- Media previews with image zoom, video playback and PDF/YouTube embeds  
- English and Chinese translations (switchable on every page)  
- Admin interface to create categories, upload/remove media and generate user codes  
- Download vote results as an HTML report  

---

## 📁 Directory Layout

```
/                Project root
├── index.php            # Login page for users and admin
├── voting.php           # Category list page
├── category.php         # Voting page for a single category
├── admin.php            # Admin dashboard
├── data/                # JSON storage
├── lang/                # Language files (en.php, zh.php)
├── inc/                 # Helper library (i18n)
├── Files/               # Uploaded media (created at runtime, not tracked)
└── .htaccess            # Security rules for Apache
```

---

## ⬆️ Uploading Media Files

Once a voting category is created via the admin dashboard, you can upload media directly into the corresponding subfolder under the `Files/` directory.

To do this, use **FTP**, **SFTP**, or any method to access your server's file system. Simply place supported files (e.g., `.jpg`, `.mp4`, `.pdf`) into the appropriate category folder. These files will automatically be shown on the relevant voting page.

---

## ▶️ Adding YouTube Links

To embed a YouTube video as a voting item:

1. Create a plain text file with any name ending in `.yt`, for example: `funnyvideo.yt`
2. Paste the full YouTube URL on a single line:
   ```
   https://www.youtube.com/watch?v=dQw4w9WgXcQ
   ```
3. Upload this `.yt` file into the correct category folder under `Files/`.

The system will automatically display the video using an embedded YouTube player.

---

## 🛠 Requirements

- PHP 8 or later  
- A web server such as Apache, Nginx, or the built‑in PHP development server  

---

## ▶️ Running Locally or On a PHP Host

1. **Download** the repository as a ZIP.
2. **Unzip** it to a folder on your local computer or web server.
3. **Copy** the unzipped folder to a PHP-enabled server directory (e.g., XAMPP, WAMP, cPanel hosting).
4. Open `data/admin.json` in a text editor and define your admin login credentials:
   ```json
   {
     "username": "admin",
     "password": "yourpassword"
   }
   ```
5. Open your browser and navigate to `http://localhost` or your deployed server URL.

Use the admin dashboard to create categories and generate participant access codes.

---

## 🔒 Security Notes

The included `.htaccess` file restricts access to the `data/` directory and disables directory listings. If you're using a web server other than Apache, make sure to replicate these protections to prevent unauthorized access to voting data.

---

## 🌐 Adding New Languages

Translation files are stored in the `lang/` directory. To add a new language:

1. Create a new file like `lang/fr.php` and return an associative array of translated strings.
2. Set the language code in a session or cookie using `$_SESSION['lang']` or `setcookie('lang', 'fr')`.
3. The `t()` helper in `inc/i18n.php` will load the correct translation file automatically.

---

This project aims to be simple, flexible, and self-contained. Suggestions and improvements are always welcome!
