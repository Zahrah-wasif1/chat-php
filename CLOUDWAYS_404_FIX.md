# 404 Error Fix - Cloudways (Roman Urdu)

## Problem:
```
404 Not Found
The requested URL was not found on this server.
```

## Solutions:

### 1. **Document Root Set Karo (Most Important!)**

Cloudways Panel mein:
1. Application Settings → Domain Management
2. Document Root set karo: `public_html/public`
3. Ya agar `public_html` hi root hai, to files ka structure check karo

**File Structure Cloudways Pe:**
```
public_html/
├── public/          ← YEH DOCUMENT ROOT HONA CHAHIYE
│   ├── index.php
│   └── .htaccess
├── src/
├── vendor/
└── .env
```

### 2. **Apache mod_rewrite Enable Karo**

Cloudways pe by default enabled hota hai, lekin verify karo:
- SSH se: `a2enmod rewrite` (agar access ho)
- Ya Cloudways support se contact karo

### 3. **Test Karo:**

**Root URL:**
```
https://phpstack-1549611-5996589.cloudwaysapps.com/
```
Yeh JSON response dega: `{"message": "Chat API is running", ...}`

**API Endpoints:**
```
https://phpstack-1549611-5996589.cloudwaysapps.com/rooms
https://phpstack-1549611-5996589.cloudwaysapps.com/auth/register
```

### 4. **Agar Phir Bhi 404 Aaye:**

**Check Karo:**
- [ ] `.htaccess` file `public` folder mein hai?
- [ ] Document root `public` folder set hai?
- [ ] `index.php` file `public` folder mein hai?
- [ ] Apache error logs check karo

**SSH Se Check:**
```bash
cd /home/master/applications/[app-id]/public_html
ls -la public/
cat public/.htaccess
```

### 5. **Quick Test:**

Browser mein yeh URL try karo:
```
https://phpstack-1549611-5996589.cloudwaysapps.com/
```

Agar JSON response aaye, to sab theek hai!
Agar 404 aaye, to document root check karo.

---

## Common Mistakes:

❌ **Galat:** Document root = `public_html`
✅ **Sahi:** Document root = `public_html/public`

❌ **Galat:** Files directly `public_html` mein
✅ **Sahi:** Files `public_html/public` mein

❌ **Galat:** `.htaccess` missing
✅ **Sahi:** `.htaccess` `public` folder mein

---

## Agar Fix Nahi Hota:

1. Cloudways panel → Application Settings → Document Root
2. `public_html/public` set karo
3. Save karo
4. 5-10 seconds wait karo
5. Phir try karo

