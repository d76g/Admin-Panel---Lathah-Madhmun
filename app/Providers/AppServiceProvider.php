<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $countries_data = [];
        $get_countries_json = file_get_contents(public_path('countriesdata.json'));
        if($get_countries_json != ''){
            $countries_data = json_decode($get_countries_json);
        }
        view()->composer('*', function($view) use($countries_data) {
            $view->with('countries_data', $countries_data);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Only set cookies when running in web context, not CLI
        if (!$this->app->runningInConsole() && !headers_sent()) {
            $expires = time() + 3600; // 1 hour
            $path = '/';
            $domain = null;
            // Don't use secure flag to avoid issues - cookies will work on both HTTP and HTTPS
            $secure = false;
            $httponly = false; // Must be false for JavaScript to read
            
            // Helper function to set cookie
            $setCookie = function($name, $value) use ($expires, $path, $domain, $secure, $httponly) {
                if ($value && $value !== '') {
                    if (PHP_VERSION_ID >= 70300) {
                        setcookie($name, bin2hex($value), [
                            'expires' => $expires,
                            'path' => $path,
                            'domain' => $domain,
                            'secure' => $secure,
                            'httponly' => $httponly,
                            'samesite' => 'Lax'
                        ]);
                    } else {
                        setcookie($name, bin2hex($value), $expires, $path, $domain, $secure, $httponly);
                    }
                }
            };
            
            // Set all Firebase cookies
            $setCookie('XSRF-TOKEN-AK', env('FIREBASE_APIKEY'));
            $setCookie('XSRF-TOKEN-AD', env('FIREBASE_AUTH_DOMAIN'));
            $setCookie('XSRF-TOKEN-DU', env('FIREBASE_DATABASE_URL'));
            $setCookie('XSRF-TOKEN-PI', env('FIREBASE_PROJECT_ID'));
            $setCookie('XSRF-TOKEN-SB', env('FIREBASE_STORAGE_BUCKET'));
            $setCookie('XSRF-TOKEN-MS', env('FIREBASE_MESSAAGING_SENDER_ID'));
            $setCookie('XSRF-TOKEN-AI', env('FIREBASE_APP_ID'));
            $setCookie('XSRF-TOKEN-MI', env('FIREBASE_MEASUREMENT_ID'));
        }
    }
}
