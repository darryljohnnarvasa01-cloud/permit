# Valencia City Motorela Permit System

A comprehensive web-based system for managing motorela (tricycle) permits and renewals in Valencia City.

## 🚀 Features

- **Secure Authentication** - Role-based access for Admin & Staff
- **Permit Management** - Apply, renew, approve, and track motorela permits
- **Category Management** - Flexible admin-controlled categories
- **PDF Generation** - Generate printable permits with QR codes
- **Activity Logs** - Track all system activities
- **Modern UI** - Clean, responsive design with TailwindCSS

## 📋 Requirements

- PHP >= 7.4
- MySQL >= 5.7
- Composer
- Node.js & npm (for TailwindCSS)

## 🔧 Installation

1. **Clone/Copy to XAMPP htdocs:**
   ```
   c:\xampp\htdocs\permit
   ```

2. **Install PHP Dependencies:**
   ```bash
   composer install
   ```

3. **Install Node Dependencies:**
   ```bash
   npm install
   ```

4. **Configure Environment:**
   - Copy `.env` and update database credentials
   
5. **Create Database:**
   - Import `database/migrations/schema.sql`
   - Import `database/seeds/default_data.sql`

6. **Build CSS:**
   ```bash
   npm run build
   ```

7. **Start Development:**
   - Start XAMPP (Apache + MySQL)
   - Access: `http://localhost/permit`

## 👤 Default Login

**Admin:**
- Email: admin@valenciacity.gov.ph
- Password: Admin123!

**Staff:**
- Email: staff@valenciacity.gov.ph
- Password: Staff123!

## 📁 Project Structure

```
motorela-permit/
├── public/           # Public accessible files
├── app/             # Application core
├── resources/       # Views and templates
├── database/        # Migrations and seeds
└── vendor/          # Dependencies
```

## 🔒 Security

- Password hashing with `password_hash()`
- Session-based authentication
- SQL injection prevention with prepared statements
- CSRF protection
- Input validation and sanitization

## 📝 License

Proprietary - Valencia City Government

## 📧 Contact

For support, contact Valencia City Hall
