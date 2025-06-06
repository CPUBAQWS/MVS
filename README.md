# Family Voting Web App

This is a simple PHP-based web application designed for small groups (like families) to host media voting events. Users can vote on various types of entries (images, videos, PDFs, and text blocks) across different categories, each with customizable voting rules.

## 🔐 Authentication

- **Admin Login:** Username & password stored in `admin.json`.
- **User Access:** Codes are generated and stored in `users.json`, no registration required.
- **Login Page:** Users and admins both log in from the main landing page.

## 🗃️ Categories

Each category:
- Is assigned a **random unique ID** (folder name, e.g. `B2sIF`).
- Has a **descriptive name** and a **voting rule**:
  - `single`: 單一票
  - `multi_unique`: 多票（不可重複）
  - `multi_multi`: 多票（可重複）
- Stores its media files in `Files/{CategoryID}/`.
- Can be renamed or deleted by admin.

## 📂 Media Support

- **Allowed types:** `jpg`, `jpeg`, `png`, `gif`, `mp4`, `pdf`, text block entries.
- **Blocked types:** `exe`, `zip`, `msi`, and others can be customized.
- Admin can **upload/delete** files through the interface.
- Media are previewable via modal popup.

## 🗳️ Voting Logic

- Each user can vote based on the category's rule.
- Votes are stored in `votes/{user_code}.json`.
- Users can **cancel** votes and reallocate (if rules allow).
- Vote buttons automatically reflect current state and enforce limits client-side and server-side.

## 🛠 Admin Panel

Available via `admin.php` after login:
- Manage categories (add, rename, delete).
- Generate user codes.
- View category list with folder, rules, and file count.
- Upload or remove media.
- All changes reflected immediately on the site.

## 📱 Responsive Design

- Designed using Tailwind CSS.
- Layout adapts for phone, tablet, and desktop.
- Voting pages use responsive grid (1 column on small screens, 2-4 on larger).

## 📁 Folder Structure

```
family2025/
├── Files/
│   ├── B2sIF/              # Media folder for category
│   │   └── image.jpg
├── data/
│   ├── categories.json     # Category definitions
│   ├── admin.json          # Admin credentials
│   └── users.json          # User access codes
├── votes/
│   └── [usercode].json     # Per-user voting data
├── index.html              # Landing/login page
├── admin.php               # Admin panel
├── voting.php              # Main voting menu
├── category.php            # Individual category view
└── save_vote.php           # Vote backend logic
```

## ✅ Getting Started

1. Upload the files to your PHP-capable web host.
2. Set `data/` and `votes/` directories writable.
3. Edit `admin.json` to set admin credentials.
4. Use the admin panel to create categories and generate user codes.
5. Share `index.html` link and access code with users.

---

Enjoy your private and secure family voting site!
