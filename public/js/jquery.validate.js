
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

// Initialize Firebase only if all required config values are available
if (typeof firebase !== 'undefined') {
    try {
        // Check if Firebase is already initialized
        var apps = firebase.apps;
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
            if (firebaseConfig.apiKey && firebaseConfig.authDomain && firebaseConfig.projectId) {
                firebase.initializeApp(firebaseConfig);
            } else {
                console.warn('Firebase configuration incomplete. Some cookies may be missing. Please check your .env file for Firebase credentials.');
            }
        }
    } catch (error) {
        console.error('Error initializing Firebase:', error);
    }
} else {
    console.error('Firebase SDK not loaded. Please check that Firebase scripts are included before this file.');
} 