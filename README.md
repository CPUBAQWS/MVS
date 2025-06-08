# 🗳️ Family Media Voting Website

A lightweight, privacy-respecting voting platform designed for families to browse and vote on shared media — such as photos, videos, PDFs, and creative content — organized by category.

This project emphasizes ease of use, anonymous access, and zero dependencies on external databases.

---

## ✨ Features

### ✅ Voting Experience
- Simple, mobile-friendly interface
- Vote for images, videos, PDFs, and text
- Categories support different voting rules:
  - `single` - One vote per user
  - `multi_unique` - Multiple votes (only one per item)
  - `multi_repeat` - Multiple votes (allow repeats)
- Users can **cancel votes**
- Media can be **previewed or enlarged on click**

### 🔐 Authentication
- Access via **pre-assigned user codes**
- No account registration required
- Admin login for management tasks
- Votes are tracked anonymously using access codes

### 🗂 Categories & Media
- Each category is mapped to a folder of media files
- Supported formats: `jpg`, `png`, `mp4`, `pdf`, and more
- Voting can be enabled or disabled per category
- Invalid file types (e.g. `.zip`, `.exe`) are ignored

### 🛠 Admin Capabilities (Planned)
- Create and manage categories
- Upload or remove media
- View and export voting results
- Full site backup

---

## 📁 Project Structure

```
/public/
├── index.html          # Landing page for access code entry
├── voting.html         # Category list with vote progress
├── category.html       # Universal voting page (loaded via ?cat=...)
├── Files/1/            # Media folder for a specific category

/private/
├── votes.json          # Tracks user votes
├── users.json          # Stores valid access codes
├── categories.json     # Category definitions and settings
└── admin.json          # Admin credentials

app.py                  # Flask backend (planned)
```

---

## 🔒 Privacy & Simplicity
- No email, name, or personal info required
- Local JSON-based storage (no database)
- All voting data stays on your own server or device

---

## 🛠 Tech Stack

- **Frontend**: HTML + Tailwind CSS + JavaScript
- **Backend** (planned): Python + Flask
- **Storage**: Local JSON files (votes, users, categories)

---

## 🚧 Roadmap

- [x] Dynamic voting page per category
- [x] Anonymous voting with access code
- [x] Cancel vote functionality
- [ ] Flask backend for vote API
- [ ] Admin dashboard
- [ ] CSV/JSON result export
- [ ] Media upload via web interface

---

## 📦 Deployment

- Can run offline or on any basic web server
- Backend (Flask) can be hosted locally or on platforms like PythonAnywhere or Replit

---

## 👨‍👩‍👧‍👦 Use Case Example

Host a friendly **family contest** where everyone can:
- Submit their favorite travel photos
- Vote on each other’s cooking videos
- Share and read essays or PDF albums
- All without creating accounts or sharing personal data

---

## 📬 Feedback

This project is built with simplicity and privacy in mind.  
Suggestions, ideas, or improvements are always welcome!
