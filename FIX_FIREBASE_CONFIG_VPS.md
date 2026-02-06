# Fix Firebase Configuration on VPS Server

## Problem
Language images (and other uploads) are being stored in the wrong Firebase Storage bucket (gromart instead of lazatmadmoon).

## Root Cause
The `.env` file on your VPS server still has the old Firebase project credentials.

## Solution

### Step 1: Update .env File on VPS

SSH into your VPS server and edit the `.env` file:

```bash
cd /var/www/admin-panel
nano .env
```

Update these Firebase configuration values with your **lazatmadmoon** Firebase project credentials:

```env
FIREBASE_APIKEY="your-lazatmadmoon-api-key"
FIREBASE_AUTH_DOMAIN="lazatmadmoon-6c282.firebaseapp.com"
FIREBASE_DATABASE_URL="https://lazatmadmoon-6c282-default-rtdb.asia-southeast1.firebasedatabase.app"
FIREBASE_PROJECT_ID="lazatmadmoon-6c282"
FIREBASE_STORAGE_BUCKET="lazatmadmoon-6c282.firebasestorage.app"
FIREBASE_MESSAAGING_SENDER_ID="799516975591"
FIREBASE_APP_ID="1:799516975591:web:b61ce9452a36ac7280dd42"
FIREBASE_MEASUREMENT_ID="your-measurement-id"
```

**Important:** Make sure these values match your **lazatmadmoon** Firebase project, not the old gromart project.

### Step 2: Clear Laravel Configuration Cache

After updating `.env`, clear the configuration cache:

```bash
cd /var/www/admin-panel
php artisan config:clear
php artisan config:cache
```

### Step 3: Restart PHP-FPM (if needed)

```bash
sudo systemctl restart php8.1-fpm
# or
sudo systemctl restart php-fpm
```

### Step 4: Clear Browser Cookies

The Firebase configuration is stored in cookies. You need to either:

**Option A: Clear cookies manually**
1. Open browser DevTools (F12)
2. Go to Application/Storage tab → Cookies
3. Delete all cookies for your domain
4. Refresh the page

**Option B: Wait for cookies to expire**
- Cookies expire after 1 hour (3600 seconds)
- Wait 1 hour and refresh the page

**Option C: Clear cookies via browser console**
```javascript
// Run this in browser console
document.cookie.split(";").forEach(function(c) { 
    document.cookie = c.replace(/^ +/, "").replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/"); 
});
location.reload();
```

### Step 5: Verify Firebase Configuration

After clearing cookies, check the browser console (F12) and you should see:
- "Firebase services initialized successfully"
- "Storage bucket: lazatmadmoon-6c282.firebasestorage.app" (not gromart)

### Step 6: Test Upload

1. Try uploading a language image
2. Check Firebase Console → Storage → `language/` folder
3. Verify the image is in the **lazatmadmoon** project, not gromart

## Verification Commands

### Check current .env values:
```bash
cd /var/www/admin-panel
grep FIREBASE .env
```

### Verify Laravel is reading correct values:
```bash
php artisan tinker
>>> env('FIREBASE_PROJECT_ID')
>>> env('FIREBASE_STORAGE_BUCKET')
```

### Check which Firebase project is being used:
1. Open browser console (F12)
2. Run: `firebase.app().options.storageBucket`
3. Should show: `"lazatmadmoon-6c282.firebasestorage.app"`

## Troubleshooting

### Still uploading to wrong bucket?

1. **Check .env file is correct:**
   ```bash
   cat /var/www/admin-panel/.env | grep FIREBASE
   ```

2. **Clear all caches:**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan route:clear
   php artisan view:clear
   php artisan config:cache
   ```

3. **Check cookies are updated:**
   - Open browser DevTools → Application → Cookies
   - Check `XSRF-TOKEN-PI` cookie value
   - Decrypt it (it's hex-encoded) and verify it matches your project ID

4. **Force cookie refresh:**
   - Delete all cookies for your domain
   - Hard refresh the page (Ctrl+Shift+R or Cmd+Shift+R)

5. **Check AppServiceProvider:**
   - Verify `app/Providers/AppServiceProvider.php` is setting cookies correctly
   - Check that `env()` calls are reading from `.env` file

### Cookies not updating?

The cookies are set in `AppServiceProvider::boot()`. Make sure:
- `.env` file has correct values
- Config cache is cleared
- PHP-FPM is restarted
- Browser cookies are cleared

### Still seeing old Firebase project?

1. Check if there are multiple `.env` files:
   ```bash
   find /var/www/admin-panel -name ".env*" -type f
   ```

2. Verify you're editing the correct `.env` file:
   ```bash
   php artisan config:show | grep firebase
   ```

3. Check for cached config:
   ```bash
   ls -la bootstrap/cache/config.php
   rm bootstrap/cache/config.php  # Delete cached config
   php artisan config:cache
   ```

## Quick Fix Script

Run this on your VPS to quickly update and verify:

```bash
#!/bin/bash
cd /var/www/admin-panel

echo "Current Firebase config:"
grep FIREBASE .env | head -8

echo ""
echo "Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan config:cache

echo ""
echo "Verifying config..."
php artisan tinker --execute="echo 'Project ID: ' . env('FIREBASE_PROJECT_ID') . PHP_EOL; echo 'Storage Bucket: ' . env('FIREBASE_STORAGE_BUCKET') . PHP_EOL;"

echo ""
echo "Done! Now clear your browser cookies and refresh the page."
```

## Prevention

To prevent this in the future:

1. **Always update `.env` first** before deploying
2. **Clear config cache** after changing `.env`
3. **Test Firebase uploads** after deployment
4. **Document Firebase project** in deployment notes

## Summary

The fix is simple:
1. ✅ Update `.env` with correct Firebase credentials
2. ✅ Clear Laravel config cache
3. ✅ Clear browser cookies
4. ✅ Test upload

After these steps, all uploads should go to the correct Firebase Storage bucket (lazatmadmoon).
