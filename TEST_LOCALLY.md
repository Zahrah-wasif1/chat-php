# Local Testing Guide (Roman Urdu)

## Local Pe Test Karne Ke Liye

### 1. PHP Server Start Karo
```powershell
cd server\public
php -S localhost:8000
```

Phir browser mein kholo: `http://localhost:8000`

### 2. Database Check Karo
- XAMPP/WAMP mein MySQL start karo
- Database `chat` create karo
- `.env` file mein local credentials dalo

### 3. Composer Dependencies
```powershell
cd server
composer install
```

### 4. Socket Server (Agar Chahiye)
```powershell
cd sock
npm install
node server.js
```

---

## Common Local Errors:

### Error: "vendor/autoload.php not found"
**Solution:** `composer install` run karo

### Error: "Database connection failed"
**Solution:** 
- MySQL service start karo
- `.env` file mein correct credentials dalo
- Database create karo

### Error: "Class 'PDO' not found"
**Solution:** PHP mein PDO extension enable karo (usually by default enabled hota hai)

---

## Cloudways Pe Deploy Karte Waqt:

1. Files upload karo
2. `.env` file create karo (credentials dalo)
3. `composer install` run karo
4. Document root set karo
5. Socket server PM2 se run karo

