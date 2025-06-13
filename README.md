# 🗳️ Family Media Voting

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
├── data/                # JSON storage (users, votes, categories, admin credentials)
├── lang/                # Language files (en.php, zh.php)
├── inc/                 # Helper library (i18n)
├── Files/               # Uploaded media (created at runtime, not tracked)
└── .htaccess            # Security rules for Apache
```

---

## 🛠 Requirements

- PHP 8 or later
- A web server such as Apache or the built‑in PHP development server

### Running locally

```bash
php -S localhost:8000
```
Then open `http://localhost:8000` in your browser.

Admin credentials are defined in `data/admin.json`. Use the dashboard to create categories and generate access codes for participants.

---

## 🔒 Security Notes

The provided `.htaccess` file blocks direct access to the `data/` directory and disables directory listings. If you deploy with another server, apply similar rules to keep vote data private.

---

## 🌐 Adding New Languages

Translation files live in the `lang/` directory and return an associative PHP array. To add a language:

1. Create a new file like `lang/fr.php` that returns an array of translated strings.
2. Set `$_SESSION['lang']` or the `lang` cookie to the language code.
3. The `t()` helper in `inc/i18n.php` will load the appropriate file.

---

This project aims to stay simple and self‑contained. Suggestions or improvements are welcome!
