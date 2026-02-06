# Test Firebase Cookies

## Quick Test

After deploying the updated code, test if cookies are being set:

### Step 1: Check Cookies in Browser

1. Open your admin panel: `https://admin.lathatmadhmoun.com`
2. Open DevTools (F12)
3. Go to **Application** → **Cookies** → `https://admin.lathatmadhmoun.com`
4. Look for cookies starting with `XSRF-TOKEN-`

You should see:
- `XSRF-TOKEN-AK`
- `XSRF-TOKEN-AD`
- `XSRF-TOKEN-DU`
- `XSRF-TOKEN-PI`
- `XSRF-TOKEN-SB`
- `XSRF-TOKEN-MS`
- `XSRF-TOKEN-AI`
- `XSRF-TOKEN-MI` (optional, can be empty)

### Step 2: Test Cookie Decryption

In browser console (F12 → Console), run:

```javascript
// Check if jQuery cookie plugin is loaded
console.log('jQuery cookie:', typeof $.cookie);
console.log('jQuery decrypt:', typeof $.decrypt);

// Check cookies
var cookies = ['XSRF-TOKEN-AK', 'XSRF-TOKEN-AD', 'XSRF-TOKEN-DU', 'XSRF-TOKEN-PI', 'XSRF-TOKEN-SB', 'XSRF-TOKEN-MS', 'XSRF-TOKEN-AI', 'XSRF-TOKEN-MI'];
cookies.forEach(function(name) {
    var value = $.cookie(name);
    console.log(name + ':', value ? 'SET (' + value.length + ' chars)' : 'MISSING');
    if (value) {
        try {
            var decrypted = $.decrypt(value);
            console.log('  Decrypted:', decrypted);
        } catch(e) {
            console.error('  Decrypt error:', e);
        }
    }
});
```

### Step 3: Test Firebase Initialization

In browser console, run:

```javascript
// Check Firebase SDK
console.log('Firebase:', typeof firebase);
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

if (config.apiKey && config.authDomain && config.projectId && config.storageBucket) {
    if (firebase.apps.length === 0) {
        firebase.initializeApp(config);
        console.log('✅ Firebase initialized manually!');
        console.log('Project ID:', config.projectId);
        console.log('Storage Bucket:', config.storageBucket);
    } else {
        console.log('✅ Firebase already initialized');
    }
} else {
    console.error('❌ Missing config values');
    console.log('apiKey:', !!config.apiKey);
    console.log('authDomain:', !!config.authDomain);
    console.log('projectId:', !!config.projectId);
    console.log('storageBucket:', !!config.storageBucket);
}
```

## If Cookies Are Missing

### On VPS Server:

1. **Check .env file:**
   ```bash
   cd /var/www/admin-panel
   grep FIREBASE .env
   ```

2. **Clear Laravel cache:**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan config:cache
   ```

3. **Check if AppServiceProvider is registered:**
   ```bash
   php artisan tinker
   >>> app('App\Providers\AppServiceProvider')
   ```

4. **Test cookie setting manually:**
   Create a test route in `routes/web.php`:
   ```php
   Route::get('/test-cookies', function() {
       $apiKey = env('FIREBASE_APIKEY');
       setcookie('TEST-COOKIE', 'test-value', time() + 3600, '/');
       return response()->json([
           'apiKey_exists' => !empty($apiKey),
           'apiKey_value' => substr($apiKey, 0, 10) . '...',
           'cookies_set' => true
       ]);
   });
   ```
   
   Then visit: `https://admin.lathatmadhmoun.com/test-cookies`
   Check if `TEST-COOKIE` appears in browser cookies.

5. **Check PHP error logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

6. **Check web server error logs:**
   ```bash
   # For Nginx
   sudo tail -f /var/log/nginx/error.log
   
   # For Apache
   sudo tail -f /var/log/apache2/error.log
   ```

## Common Issues

### Issue 1: Cookies Not Being Set

**Possible causes:**
- Headers already sent (check for output before `setcookie()`)
- PHP version compatibility issue
- Web server configuration blocking cookies
- Browser blocking third-party cookies

**Solution:**
- Check `headers_sent()` in AppServiceProvider
- Verify PHP version: `php -v` (should be 7.4+)
- Check web server logs for errors

### Issue 2: Cookies Set But Empty

**Possible causes:**
- `.env` values are empty
- `env()` function not reading correctly
- Config cache has old values

**Solution:**
- Verify `.env` file has values
- Clear config cache: `php artisan config:clear && php artisan config:cache`
- Check if values are quoted correctly in `.env`

### Issue 3: Cookies Set But Can't Decrypt

**Possible causes:**
- `jquery.cookie.js` not loaded
- `$.decrypt` function not available
- Cookie value corrupted

**Solution:**
- Check Network tab - is `jquery.cookie.js` loading?
- Check if `$.decrypt` function exists in console
- Verify cookie values are hex-encoded

## Expected Results

After fixing, you should see in browser console:

```
Firebase initialized successfully
Project ID: lazatmadmoon-6c282
Storage Bucket: lazatmadmoon-6c282.firebasestorage.app
Dashboard Firebase initialized successfully
```

And NO errors about:
- "Firebase configuration incomplete"
- "Cannot read properties of undefined (reading 'collection')"
- "Unexpected token ')'"
