
// Helper function to safely decrypt cookie values
function getDecryptedCookie(cookieName) {
    var cookieValue = $.cookie(cookieName);
    if (!cookieValue) {
        return null;
    }
    try {
        return $.decrypt(cookieValue);
    } catch (e) {
        console.error('Error decrypting cookie ' + cookieName + ':', e);
        return null;
    }
}

// Initialize Firebase with retry logic
var firebaseInitializationAttempts = 0;
var maxFirebaseInitAttempts = 10;

function initializeFirebaseApp() {
    if (typeof firebase === 'undefined') {
        firebaseInitializationAttempts++;
        if (firebaseInitializationAttempts < maxFirebaseInitAttempts) {
            console.warn('Firebase SDK not loaded yet, retrying... (' + firebaseInitializationAttempts + '/' + maxFirebaseInitAttempts + ')');
            setTimeout(initializeFirebaseApp, 500);
        } else {
            console.error('Firebase SDK not loaded after ' + maxFirebaseInitAttempts + ' attempts. Please check that Firebase scripts are included.');
        }
        return;
    }
    
    try {
        // Check if Firebase is already initialized
        var apps = firebase.apps || [];
        if (apps.length === 0) {
            var firebaseConfig = {
                apiKey: getDecryptedCookie('XSRF-TOKEN-AK'),
                authDomain: getDecryptedCookie('XSRF-TOKEN-AD'),
                databaseURL: getDecryptedCookie('XSRF-TOKEN-DU'),
                projectId: getDecryptedCookie('XSRF-TOKEN-PI'),
                storageBucket: getDecryptedCookie('XSRF-TOKEN-SB'),
                messagingSenderId: getDecryptedCookie('XSRF-TOKEN-MS'),
                appId: getDecryptedCookie('XSRF-TOKEN-AI'),
                measurementId: getDecryptedCookie('XSRF-TOKEN-MI')
            };

            // Validate that all required config values are present
            if (firebaseConfig.apiKey && firebaseConfig.authDomain && firebaseConfig.projectId && firebaseConfig.storageBucket) {
                firebase.initializeApp(firebaseConfig);
                console.log('Firebase initialized successfully');
                console.log('Project ID:', firebaseConfig.projectId);
                console.log('Storage Bucket:', firebaseConfig.storageBucket);
                
                // Trigger custom event for other scripts to listen to
                if (typeof window !== 'undefined') {
                    window.dispatchEvent(new CustomEvent('firebaseInitialized'));
                }
            } else {
                console.error('Firebase configuration incomplete. Missing values:');
                if (!firebaseConfig.apiKey) console.error('  - FIREBASE_APIKEY');
                if (!firebaseConfig.authDomain) console.error('  - FIREBASE_AUTH_DOMAIN');
                if (!firebaseConfig.projectId) console.error('  - FIREBASE_PROJECT_ID');
                if (!firebaseConfig.storageBucket) console.error('  - FIREBASE_STORAGE_BUCKET');
                console.warn('Please check your .env file for Firebase credentials and ensure cookies are set.');
            }
        } else {
            console.log('Firebase already initialized');
        }
    } catch (error) {
        console.error('Error initializing Firebase:', error);
    }
}

// Start initialization when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeFirebaseApp);
} else {
    // DOM is already ready
    initializeFirebaseApp();
} 