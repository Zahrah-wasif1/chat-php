# Cloudways Deployment Guide

## Prerequisites
- Cloudways account with a server
- SSH access to your Cloudways server
- Database credentials from Cloudways panel

## Step 1: Upload Files to Cloudways

1. Connect to your Cloudways server via SFTP or SSH
2. Navigate to your application's `public_html` directory
3. Upload all files from the `server` folder to `public_html`

## Step 2: Configure Environment Variables

1. In Cloudways panel, go to your application settings
2. Create a `.env` file in the root of your application (same level as `public` folder)
3. Copy the contents from `env.example` and update with your Cloudways credentials:

```env
APP_ENV=production
APP_KEY=your-secret-key-here

APP_URL=https://yourdomain.com

DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_cloudways_database_name
DB_USERNAME=your_cloudways_database_user
DB_PASSWORD=your_cloudways_database_password

SOCKET_PORT=3000
API_URL=https://yourdomain.com
```

**Important:** Get your database credentials from Cloudways panel → Application Management → Access Details

## Step 3: Install PHP Dependencies

SSH into your Cloudways server and run:

```bash
cd /home/master/applications/[your-app-id]/public_html
composer install --no-dev --optimize-autoloader
```

## Step 4: Set Permissions

```bash
chmod -R 755 public
chmod -R 755 storage
```

## Step 5: Configure Web Server

Cloudways uses Apache. The `.htaccess` file in `public` folder is already configured for routing.

Make sure your document root points to: `public_html/public`

## Step 6: Setup Socket Server (Node.js)

1. Upload the `sock` folder to your server (outside public_html for security)
2. SSH into your server
3. Navigate to the `sock` directory
4. Create a `.env` file:

```env
SOCKET_PORT=3000
API_URL=https://yourdomain.com
```

5. Install dependencies:
```bash
npm install
```

6. Install PM2 to keep the socket server running:
```bash
npm install -g pm2
pm2 start server.js --name chat-socket
pm2 save
pm2 startup
```

## Step 7: Configure Firewall

In Cloudways panel, make sure port 3000 is open for your socket server.

## Step 8: Database Setup

1. Import your database schema using phpMyAdmin or MySQL command line
2. Verify database connection by checking your application logs

## Important Notes

- **Document Root:** Should be set to `public_html/public` in Cloudways
- **PHP Version:** Cloudways supports PHP 7.4+, ensure your app is compatible
- **Socket Server:** Runs on port 3000, make sure it's accessible
- **SSL:** Cloudways provides free SSL, enable it in the panel
- **Environment Variables:** Never commit `.env` files to git

## Troubleshooting

1. **404 Errors:** Check that document root is set to `public` folder
2. **Database Connection:** Verify credentials in `.env` file
3. **Socket Not Working:** Check if port 3000 is open and PM2 is running
4. **CORS Issues:** Update `Access-Control-Allow-Origin` in `.htaccess` if needed

## File Structure on Cloudways

```
/home/master/applications/[app-id]/
├── public_html/          (Your PHP application)
│   ├── public/          (Document root)
│   │   ├── index.php
│   │   └── .htaccess
│   ├── src/
│   ├── vendor/
│   └── .env
└── sock/                (Socket server - outside public_html)
    ├── server.js
    ├── package.json
    └── .env
```

