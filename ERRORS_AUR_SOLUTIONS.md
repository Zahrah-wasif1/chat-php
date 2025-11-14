# Common Errors Aur Unke Solutions (Roman Urdu)

## Cloudways Par Deploy Karte Waqt Aane Wale Errors

### 1. **Database Connection Error**
```
Error: Database configuration is missing
OR
SQLSTATE[HY000] [2002] Connection refused
```

**Solution:**
- `.env` file check karo - `server` folder mein honi chahiye
- Database credentials Cloudways panel se verify karo
- `DB_HOST=127.0.0.1` ya `localhost` use karo (Cloudways pe dono kaam karte hain)

---

### 2. **404 Error - Route Not Found**
```
{"error": "Route not found"}
```

**Solution:**
- Document root `public_html/public` set karo Cloudways panel mein
- `.htaccess` file `public` folder mein honi chahiye
- Apache mod_rewrite enable hona chahiye

---

### 3. **Composer Dependencies Missing**
```
Fatal error: require_once(): Failed opening required 'vendor/autoload.php'
```

**Solution:**
SSH se run karo:
```bash
cd /home/master/applications/[app-id]/public_html
composer install --no-dev
```

---

### 4. **PHP Version Error**
```
Your Composer dependencies require a PHP version ">= 8.0.0"
```

**Solution:**
- Cloudways panel mein PHP version 8.0+ select karo
- Application Settings → PHP Version → 8.0 ya 8.1 select karo

---

### 5. **PDO Extension Missing**
```
Class 'PDO' not found
```

**Solution:**
- Cloudways pe by default PDO enabled hota hai
- Agar nahi hai, to Cloudways support se contact karo

---

### 6. **.env File Not Found**
```
Warning: file_get_contents(.env): failed to open stream
```

**Solution:**
- `server` folder mein `.env` file manually create karo
- `env.example` ko copy karke `.env` banao
- Credentials update karo

---

### 7. **Socket Server Connection Error**
```
Error: Failed to save message
OR
Network Error: Connection refused
```

**Solution:**
- Socket server port 3000 open hona chahiye
- PM2 se socket server run karo:
```bash
cd /path/to/sock
npm install
pm2 start server.js --name chat-socket
```
- `sock/.env` file mein `API_URL` set karo

---

### 8. **CORS Error (Frontend Se)**
```
Access to XMLHttpRequest blocked by CORS policy
```

**Solution:**
- `.htaccess` file mein CORS headers already hain
- Agar phir bhi error aaye, to `index.php` mein headers check karo

---

### 9. **Permission Denied Error**
```
Warning: mkdir(): Permission denied
```

**Solution:**
```bash
chmod -R 755 public_html/public
chmod -R 755 public_html/storage (agar hai)
```

---

### 10. **Git Push Error (Agar .env Commit Ho Jaye)**
```
.gitignore mein .env add karo
```

**Solution:**
- `.env` file ko Git se remove karo:
```bash
git rm --cached server/.env
git commit -m "Remove .env from Git"
```

---

## Quick Troubleshooting Steps

1. **Check .env File:**
   - Location: `server/.env`
   - Format: Key=Value (no spaces around =)
   - All credentials filled hain?

2. **Check Database:**
   - Cloudways panel → Access Details
   - Credentials match kar rahe hain?

3. **Check Document Root:**
   - `public_html/public` set hai?

4. **Check Logs:**
   - Cloudways panel → Logs
   - Ya SSH se: `tail -f /path/to/logs/error.log`

5. **Check PHP Version:**
   - Minimum PHP 8.0 required

---

## Testing Checklist

- [ ] `.env` file exists with correct credentials
- [ ] `composer install` successfully run ho gaya
- [ ] Document root `public` folder set hai
- [ ] `.htaccess` file `public` folder mein hai
- [ ] Database connection working hai
- [ ] Socket server running hai (PM2 se)
- [ ] PHP version 8.0+ hai
- [ ] All routes accessible hain

---

## Agar Phir Bhi Error Aaye

1. Cloudways panel mein error logs check karo
2. Browser console mein errors check karo
3. Network tab mein API calls verify karo
4. SSH se direct database connection test karo

