# Installation Guide
## Valencia City Motorela Permit System

### Prerequisites
- XAMPP (or similar) with PHP 7.4+ and MySQL 5.7+
- Composer (PHP dependency manager)
- Node.js and npm (for TailwindCSS)

### Step-by-Step Installation

#### 1. Database Setup

**Option A: Using phpMyAdmin**
1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Click "Import" tab
3. Import `database/migrations/schema.sql`
4. Import `database/seeds/default_data.sql`

**Option B: Using MySQL Command Line**
```bash
mysql -u root -p < database/migrations/schema.sql
mysql -u root -p motorela_permit_db < database/seeds/default_data.sql
```

#### 2. Configure Environment

Edit the `.env` file in the root directory:
```
DB_HOST=localhost
DB_NAME=motorela_permit_db
DB_USER=root
DB_PASS=
```

#### 3. Install PHP Dependencies

Open Command Prompt in the project directory:
```bash
cd c:\xampp\htdocs\permit
composer install
```

If composer is not installed, download it from: https://getcomposer.org/download/

#### 4. Install Node Dependencies and Build CSS

```bash
npm install
npm run build
```

This will compile TailwindCSS styles.

#### 5. Set File Permissions

Ensure these directories are writable:
- `public/uploads/requirements/`
- `public/uploads/receipts/`
- `public/qr/qr_images/`

#### 6. Start XAMPP

1. Open XAMPP Control Panel
2. Start Apache
3. Start MySQL

#### 7. Access the Application

Open your browser and navigate to:
```
http://localhost/permit
```

You'll be redirected to the login page.

### Default Login Credentials

**Administrator Account:**
- Email: `admin@valenciacity.gov.ph`
- Password: `Admin123!`

**Staff Account:**
- Email: `staff@valenciacity.gov.ph`
- Password: `Staff123!`

**Important:** Change these passwords immediately after first login!

### Troubleshooting

#### Error: "Database connection failed"
- Check if MySQL is running in XAMPP
- Verify database credentials in `.env` file
- Make sure the database `motorela_permit_db` exists

#### Error: "Class not found"
- Run `composer install` to install dependencies
- Clear browser cache

#### CSS not loading
- Run `npm install` and `npm run build`
- Check if `public/css/tailwind.css` exists
- Clear browser cache

#### File upload not working
- Check folder permissions for `public/uploads/`
- Ensure PHP `upload_max_filesize` is sufficient (20MB recommended)

#### QR Code not generating
- Run `composer install` to ensure QR library is installed
- Check if `public/qr/qr_images/` directory is writable

### Development Mode

To watch for CSS changes during development:
```bash
npm run watch
```

### Production Deployment

1. Change database credentials in `.env`
2. Set appropriate file permissions (755 for directories, 644 for files)
3. Disable error display in PHP:
   ```php
   error_reporting(0);
   ini_set('display_errors', 0);
   ```
4. Use minified CSS build:
   ```bash
   npm run build
   ```
5. Configure SSL certificate for HTTPS
6. Set up regular database backups

### Security Checklist

- [ ] Change default admin/staff passwords
- [ ] Update database credentials
- [ ] Restrict database user permissions
- [ ] Enable HTTPS in production
- [ ] Set proper file upload limits
- [ ] Configure firewall rules
- [ ] Regular security updates
- [ ] Enable PHP error logging (not display)

### Support

For issues or questions:
- Check the README.md file
- Review the troubleshooting section
- Contact: Valencia City Hall IT Department

### Database Backup

Regular backup command:
```bash
mysqldump -u root -p motorela_permit_db > backup_$(date +%Y%m%d).sql
```

### System Requirements

**Minimum:**
- PHP 7.4
- MySQL 5.7
- 512MB RAM
- 100MB disk space

**Recommended:**
- PHP 8.0+
- MySQL 8.0+
- 1GB RAM
- 500MB disk space
