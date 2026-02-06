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
        if (!$this->app->runningInConsole()) {
            $cookieOptions = [
                'expires' => time() + 3600,
                'path' => '/',
                'domain' => null,
                'secure' => request()->secure(), // true for HTTPS
                'httponly' => false, // Must be false for JavaScript to read
                'samesite' => 'Lax'
            ];
            
            // Only set cookies if values exist and are not empty
            $apiKey = env('FIREBASE_APIKEY');
            $authDomain = env('FIREBASE_AUTH_DOMAIN');
            $databaseUrl = env('FIREBASE_DATABASE_URL');
            $projectId = env('FIREBASE_PROJECT_ID');
            $storageBucket = env('FIREBASE_STORAGE_BUCKET');
            $messagingSenderId = env('FIREBASE_MESSAAGING_SENDER_ID');
            $appId = env('FIREBASE_APP_ID');
            $measurementId = env('FIREBASE_MEASUREMENT_ID');
            
            if ($apiKey) setcookie('XSRF-TOKEN-AK', bin2hex($apiKey), $cookieOptions);
            if ($authDomain) setcookie('XSRF-TOKEN-AD', bin2hex($authDomain), $cookieOptions);
            if ($databaseUrl) setcookie('XSRF-TOKEN-DU', bin2hex($databaseUrl), $cookieOptions);
            if ($projectId) setcookie('XSRF-TOKEN-PI', bin2hex($projectId), $cookieOptions);
            if ($storageBucket) setcookie('XSRF-TOKEN-SB', bin2hex($storageBucket), $cookieOptions);
            if ($messagingSenderId) setcookie('XSRF-TOKEN-MS', bin2hex($messagingSenderId), $cookieOptions);
            if ($appId) setcookie('XSRF-TOKEN-AI', bin2hex($appId), $cookieOptions);
            if ($measurementId) setcookie('XSRF-TOKEN-MI', bin2hex($measurementId), $cookieOptions);
        }
    }
}
