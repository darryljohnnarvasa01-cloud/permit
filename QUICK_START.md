# Quick Start Guide
## Get Running in 5 Minutes

### Prerequisites
- XAMPP installed and running
- Internet connection (for composer/npm)

### Quick Setup

#### 1. Database (2 minutes)
```
1. Open http://localhost/phpmyadmin
2. Click "Import"
3. Choose: database/migrations/schema.sql
4. Click "Go"
5. Choose: database/seeds/default_data.sql
6. Click "Go"
```

#### 2. Dependencies (2 minutes)
Open Command Prompt in `c:\xampp\htdocs\permit`:
```bash
composer install
npm install
npm run build
```

#### 3. Launch (1 minute)
```
1. Open XAMPP Control Panel
2. Start Apache & MySQL
3. Visit: http://localhost/permit
```

### Login
```
Admin:
Email: admin@valenciacity.gov.ph
Password: Admin123!

Staff:
Email: staff@valenciacity.gov.ph
Password: Staff123!
```

### First Steps
1. Login as admin
2. Go to "Categories" - review/add vehicle types
3. Go to "Permit Types" - check permit fees
4. Go to "Users" - create staff accounts
5. Test application flow as applicant

### Common Issues

**"Database connection failed"**
- Start MySQL in XAMPP
- Check .env file database credentials

**"Composer not found"**
- Download from: https://getcomposer.org

**"CSS not loading"**
- Run: `npm run build`
- Refresh browser with Ctrl+F5

**"Permission denied on upload"**
- Right-click public/uploads folder → Properties → Security
- Give "Full Control" to "Everyone" (development only)

### Next Steps
- Read FEATURES.md for complete feature list
- Read INSTALLATION.md for detailed setup
- Change default passwords
- Customize categories and permit types
- Start processing applications

### Support
Problems? Check INSTALLATION.md troubleshooting section.

### Development
Watch CSS changes: `npm run watch`
