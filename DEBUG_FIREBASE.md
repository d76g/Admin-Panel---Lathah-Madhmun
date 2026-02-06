# Debugging Firebase Initialization Issues

## Current Errors

1. **Firebase configuration incomplete** - Cookies may be missing
2. **Firebase is not initialized** - Some features may not work
3. **Cannot read properties of undefined (reading 'collection')** - Firebase not ready

## Quick Debugging Steps

### Step 1: Check .env File

Verify all Firebase credentials are set:

```bash
grep FIREBASE .env
```

You should see:
```
FIREBASE_APIKEY="..."
FIREBASE_AUTH_DOMAIN="..."
FIREBASE_DATABASE_URL="..."
FIREBASE_PROJECT_ID="..."
FIREBASE_STORAGE_BUCKET="..."
FIREBASE_MESSAAGING_SENDER_ID="..."
FIREBASE_APP_ID="..."
FIREBASE_MEASUREMENT_ID="..."
```

**Important:** Make sure values are NOT empty and NOT null.

### Step 2: Clear Laravel Cache

```bash
php artisan config:clear
php artisan cache:clear
php artisan config:cache
```

### Step 3: Check Browser Cookies

1. Open browser DevTools (F12)
2. Go to **Application** → **Cookies** → Your domain
3. Look for cookies starting with `XSRF-TOKEN-`:
   - `XSRF-TOKEN-AK`
   - `XSRF-TOKEN-AD`
   - `XSRF-TOKEN-DU`
   - `XSRF-TOKEN-PI`
   - `XSRF-TOKEN-SB`
   - `XSRF-TOKEN-MS`
   - `XSRF-TOKEN-AI`
   - `XSRF-TOKEN-MI`

4. If cookies are missing or empty, the issue is in `AppServiceProvider.php`

### Step 4: Check Browser Console

Open browser console (F12 → Console) and look for:

**Good signs:**
- "Firebase initialized successfully"
- "Project ID: lazatmadmoon-6c282"
- "Storage Bucket: lazatmadmoon-6c282.firebasestorage.app"

**Bad signs:**
- "Firebase configuration incomplete"
- "Some cookies may be missing"
- "Firebase SDK not loaded"

### Step 5: Test Cookie Decryption

In browser console, run:

```javascript
// Check if jQuery cookie plugin is loaded
console.log(typeof $.cookie);

// Check if decrypt function exists
console.log(typeof $.decrypt);

// Try to get a cookie
console.log($.cookie('XSRF-TOKEN-PI'));

// Try to decrypt it
if ($.cookie('XSRF-TOKEN-PI')) {
    console.log('Decrypted:', $.decrypt($.cookie('XSRF-TOKEN-PI')));
}
```

### Step 6: Manual Firebase Initialization Test

In browser console, run:

```javascript
// Check if Firebase SDK is loaded
console.log(typeof firebase);

// Check if Firebase is initialized
console.log('Firebase apps:', firebase.apps ? firebase.apps.length : 'undefined');

// Try manual initialization
var config = {
    apiKey: $.decrypt($.cookie('XSRF-TOKEN-AK')),
    authDomain: $.decrypt($.cookie('XSRF-TOKEN-AD')),
    databaseURL: $.decrypt($.cookie('XSRF-TOKEN-DU')),
    projectId: $.decrypt($.cookie('XSRF-TOKEN-PI')),
    storageBucket: $.decrypt($.cookie('XSRF-TOKEN-SB')),
    messagingSenderId: $.decrypt($.cookie('XSRF-TOKEN-MS')),
    appId: $.decrypt($.cookie('XSRF-TOKEN-AI')),
    measurementId: $.decrypt($.cookie('XSRF-TOKEN-MI'))
};

console.log('Config:', config);

if (config.apiKey && config.authDomain && config.projectId) {
    if (firebase.apps.length === 0) {
        firebase.initializeApp(config);
        console.log('Firebase initialized manually!');
    } else {
        console.log('Firebase already initialized');
    }
} else {
    console.error('Missing config values:', {
        apiKey: !!config.apiKey,
        authDomain: !!config.authDomain,
        projectId: !!config.projectId,
        storageBucket: !!config.storageBucket
    });
}
```

## Common Issues and Solutions

### Issue 1: Cookies Not Being Set

**Symptoms:** No `XSRF-TOKEN-*` cookies in browser

**Solution:**
1. Check `AppServiceProvider.php` - ensure it's setting cookies
2. Check if `.env` values are empty/null
3. Clear config cache: `php artisan config:clear && php artisan config:cache`
4. Restart PHP-FPM: `sudo systemctl restart php8.1-fpm`

### Issue 2: Cookies Are Empty

**Symptoms:** Cookies exist but values are empty

**Solution:**
1. Check `.env` file - ensure values are not empty
2. Make sure there are no quotes around empty values
3. Verify `env()` function is reading correctly

### Issue 3: Cookie Decryption Failing

**Symptoms:** Cookies exist but decryption fails

**Solution:**
1. Check if `jquery.cookie.js` is loaded
2. Check if `$.decrypt` function exists
3. Verify cookie values are hex-encoded (should be set by `bin2hex()`)

### Issue 4: Firebase SDK Not Loaded

**Symptoms:** "Firebase SDK not loaded" error

**Solution:**
1. Check `resources/views/layouts/app.blade.php`
2. Ensure Firebase scripts are included before `jquery.validate.js`
3. Check network tab for 404 errors on Firebase scripts

### Issue 5: Timing Issues

**Symptoms:** Firebase initializes but dashboard code runs before it

**Solution:**
- Already fixed in code - dashboard now waits for Firebase
- Check browser console for "Dashboard Firebase initialized successfully"

## Verification Checklist

- [ ] `.env` file has all Firebase credentials
- [ ] Laravel config cache is cleared
- [ ] Browser cookies are set and not empty
- [ ] Firebase SDK scripts are loaded (check Network tab)
- [ ] Browser console shows "Firebase initialized successfully"
- [ ] No JavaScript syntax errors in console
- [ ] Dashboard loads without errors

## Still Having Issues?

1. **Check server logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. **Check browser Network tab:**
   - Look for failed requests
   - Check if Firebase scripts are loading

3. **Test in incognito/private window:**
   - Rules out cookie caching issues

4. **Check PHP version:**
   ```bash
   php -v
   ```
   Should be PHP 8.1+

5. **Verify file permissions:**
   ```bash
   ls -la .env
   ```
   Should be readable by web server

## Quick Fix Script

Run this to quickly check everything:

```bash
#!/bin/bash
echo "=== Checking Firebase Configuration ==="
echo ""
echo "1. Checking .env file:"
grep FIREBASE .env | grep -v "^#" | head -8
echo ""
echo "2. Checking Laravel config:"
php artisan tinker --execute="echo 'API Key: ' . (env('FIREBASE_APIKEY') ? 'SET' : 'MISSING') . PHP_EOL; echo 'Project ID: ' . (env('FIREBASE_PROJECT_ID') ? env('FIREBASE_PROJECT_ID') : 'MISSING') . PHP_EOL;"
echo ""
echo "3. Clearing caches:"
php artisan config:clear
php artisan cache:clear
php artisan config:cache
echo ""
echo "Done! Now check browser cookies and console."
```
