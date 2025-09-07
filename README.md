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
- English and Chinese options on the login page  
- Users can access using pre-assigned codes; no registration is required   
- Download vote results as an HTML report  

---

## ⬆️ Uploading Media Files

Once a voting category is created via the admin dashboard, you can upload media directly into the corresponding subfolder under the Files/ directory.

This can be done using FTP, SFTP, or any method that provides access to your server’s file system. Simply place supported files (e.g., .jpg, .mp4, .pdf) into the appropriate category folder—these files will automatically appear on the relevant voting page.

Alternatively, a upload button (beta) is available to users, allowing them to submit media files without server access. Files uploaded this way will be stored in a staging folder. Admins can then manually review and move these files to the appropriate category folders for them to become visible on the voting pages.

---

## ▶️ Adding YouTube Links

To embed a YouTube video as a voting item:

1. Create a plain text file with any name ending in `.yt`, for example: `funnyvideo.yt`
2. Paste the full YouTube URL on a single line:
   ```
   https://www.youtube.com/watch?v=SAMPLELINK
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

Use the admin dashboard to create categories and generate user access codes.

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

## License

This project is licensed under the [MIT License](LICENSE).

---

This project aims to be simple, flexible, and self-contained. Suggestions and improvements are always welcome!
