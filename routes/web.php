<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');

Route::get('lang/change', [App\Http\Controllers\LangController::class, 'change'])->name('changeLang');

Route::post('payments/razorpay/createorder', [App\Http\Controllers\RazorPayController::class, 'createOrderid']);

Route::post('payments/getpaytmchecksum', [App\Http\Controllers\PaymentController::class, 'getPaytmChecksum']);

Route::post('payments/validatechecksum', [App\Http\Controllers\PaymentController::class, 'validateChecksum']);

Route::post('payments/initiatepaytmpayment', [App\Http\Controllers\PaymentController::class, 'initiatePaytmPayment']);

Route::get('payments/paytmpaymentcallback', [App\Http\Controllers\PaymentController::class, 'paytmPaymentcallback']);

Route::post('payments/paypalclientid', [App\Http\Controllers\PaymentController::class, 'getPaypalClienttoken']);

Route::post('payments/paypaltransaction', [App\Http\Controllers\PaymentController::class, 'createBraintreePayment']);

Route::post('payments/stripepaymentintent', [App\Http\Controllers\PaymentController::class, 'createStripePaymentIntent']);

Route::middleware(['permission:terms,termsAndConditions'])->group(function () {
    Route::get('termsAndConditions', [App\Http\Controllers\TermsAndConditionsController::class, 'index'])->name('termsAndConditions');
});
Route::middleware(['permission:privacy,privacyPolicy'])->group(function () {
    Route::get('privacyPolicy', [App\Http\Controllers\TermsAndConditionsController::class, 'privacyindex'])->name('privacyPolicy');
});

Route::middleware(['permission:users,users'])->group(function () {
    Route::get('/users', [App\Http\Controllers\HomeController::class, 'users'])->name('users');
    Route::get('/users', [App\Http\Controllers\UserController::class, 'index'])->name('users');

});
Route::middleware(['permission:users,users.edit'])->group(function () {
    Route::get('/users/edit/{id}', [App\Http\Controllers\UserController::class, 'edit'])->name('users.edit');

});
Route::middleware(['permission:users,users.create'])->group(function () {
    Route::get('/users/create', [App\Http\Controllers\UserController::class, 'create'])->name('users.create');

});
Route::middleware(['permission:users,users.view'])->group(function () {
    Route::get('/users/view/{id}', [App\Http\Controllers\UserController::class, 'view'])->name('users.view');

});
Route::middleware(['permission:vendors,vendors'])->group(function () {
    Route::get('/vendors', [App\Http\Controllers\StoreController::class, 'vendors'])->name('vendors');
});
Route::middleware(['permission:vendors,vendors.create'])->group(function () {
    Route::get('/vendors/create', [App\Http\Controllers\StoreController::class, 'vendorCreate'])->name('vendors.create');

});
Route::middleware(['permission:approve_vendors,approve.vendors.list'])->group(function () {
Route::get('/vendors/approved', [App\Http\Controllers\StoreController::class, 'vendors'])->name('vendors.approved');
});
Route::middleware(['permission:pending_vendors,pending.vendors.list'])->group(function () {
Route::get('/vendors/pending', [App\Http\Controllers\StoreController::class, 'vendors'])->name('vendors.pending');
});
Route::middleware(['permission:restaurants,stores'])->group(function () {
    Route::get('/stores', [App\Http\Controllers\StoreController::class, 'index'])->name('stores');

});
Route::middleware(['permission:restaurants,stores.create'])->group(function () {
    Route::get('/stores/create', [App\Http\Controllers\StoreController::class, 'create'])->name('stores.create');

});
Route::middleware(['permission:restaurants,stores.edit'])->group(function () {
    Route::get('/stores/edit/{id}', [App\Http\Controllers\StoreController::class, 'edit'])->name('stores.edit');
});
Route::middleware(['permission:restaurants,stores.view'])->group(function () {
    Route::get('/stores/view/{id}', [App\Http\Controllers\StoreController::class, 'view'])->name('stores.view');

});
Route::get('/stores/promos/{id}', [App\Http\Controllers\StoreController::class, 'promos'])->name('stores.promos');

Route::middleware(['permission:coupons,coupons'])->group(function () {
    Route::get('/coupons', [App\Http\Controllers\CouponController::class, 'index'])->name('coupons');
    Route::get('/coupon/{id}', [App\Http\Controllers\CouponController::class, 'index'])->name('stores.coupons');

});
Route::middleware(['permission:coupons,coupons.edit'])->group(function () {
    Route::get('/coupons/edit/{id}', [App\Http\Controllers\CouponController::class, 'edit'])->name('coupons.edit');

});
Route::middleware(['permission:coupons,coupons.create'])->group(function () {
    Route::get('/coupons/create', [App\Http\Controllers\CouponController::class, 'create'])->name('coupons.create');
    Route::get('/coupon/create/{id}', [App\Http\Controllers\CouponController::class, 'create']);
    Route::get('/coupons/create/{id}', [App\Http\Controllers\CouponController::class, 'create']);

});

Route::middleware(['permission:foods,items'])->group(function () {
    Route::get('/items', [App\Http\Controllers\ItemController::class, 'index'])->name('items');
    Route::get('/items/{id}', [App\Http\Controllers\ItemController::class, 'index'])->name('stores.items');

});
Route::middleware(['permission:foods,items.edit'])->group(function () {
    Route::get('/items/edit/{id}', [App\Http\Controllers\ItemController::class, 'edit'])->name('items.edit');

});
Route::middleware(['permission:foods,items.create'])->group(function () {
    Route::get('/item/create', [App\Http\Controllers\ItemController::class, 'create'])->name('items.create');
    Route::get('/item/create/{id}', [App\Http\Controllers\ItemController::class, 'create']);

});

Route::middleware(['permission:orders,orders'])->group(function () {
    Route::get('/orders/', [App\Http\Controllers\OrderController::class, 'index'])->name('orders');
    Route::get('/orders/{id}', [App\Http\Controllers\OrderController::class, 'index'])->name('stores.orders');

});
Route::middleware(['permission:orders,orders.edit'])->group(function () {
    Route::get('/orders/edit/{id}', [App\Http\Controllers\OrderController::class, 'edit'])->name('orders.edit');

});
Route::middleware(['permission:orders,vendors.orderprint'])->group(function () {
    Route::get('/orders/print/{id}', [App\Http\Controllers\OrderController::class, 'orderprint'])->name('vendors.orderprint');

});

Route::middleware(['permission:category,categories'])->group(function () {

    Route::get('/categories', [App\Http\Controllers\CategoryController::class, 'index'])->name('categories');
});
Route::middleware(['permission:category,categories.edit'])->group(function () {
    Route::get('/categories/edit/{id}', [App\Http\Controllers\CategoryController::class, 'edit'])->name('categories.edit');

});
Route::middleware(['permission:category,categories.create'])->group(function () {
    Route::get('/categories/create', [App\Http\Controllers\CategoryController::class, 'create'])->name('categories.create');

});

Route::middleware(['permission:drivers,drivers'])->group(function () {

    Route::get('/drivers', [App\Http\Controllers\DriverController::class, 'index'])->name('drivers');
});
Route::middleware(['permission:approve_drivers,approve.driver.list'])->group(function () {

    Route::get('/drivers/approved', [App\Http\Controllers\DriverController::class, 'index'])->name('drivers.approved');
});
Route::middleware(['permission:pending_drivers,pending.driver.list'])->group(function () {

    Route::get('/drivers/pending', [App\Http\Controllers\DriverController::class, 'index'])->name('drivers.pending');
});
Route::middleware(['permission:drivers,drivers.edit'])->group(function () {
    Route::get('/drivers/edit/{id}', [App\Http\Controllers\DriverController::class, 'edit'])->name('drivers.edit');

});
Route::middleware(['permission:drivers,drivers.create'])->group(function () {
    Route::get('/drivers/create', [App\Http\Controllers\DriverController::class, 'create'])->name('drivers.create');

});
Route::middleware(['permission:drivers,drivers.view'])->group(function () {
    Route::get('/drivers/view/{id}', [App\Http\Controllers\DriverController::class, 'view'])->name('drivers.view');

});


Route::get('/users/profile', [App\Http\Controllers\UserController::class, 'profile'])->name('users.profile');
Route::post('/users/profile/update/{id}', [App\Http\Controllers\UserController::class, 'update'])->name('users.profile.update');

Route::get('usersorders/{type}', [App\Http\Controllers\OrderController::class, 'index'])->name('usersorders');

Route::middleware(['permission:payments,payments'])->group(function () {
    Route::get('/payments', [App\Http\Controllers\AdminPaymentsController::class, 'index'])->name('payments');
});
Route::middleware(['permission:driver-payments,driver.driverpayments'])->group(function () {
    Route::get('driverpayments', [App\Http\Controllers\AdminPaymentsController::class, 'driverIndex'])->name('driver.driverpayments');
});
Route::middleware(['permission:restaurant-payouts,storesPayouts'])->group(function () {
    Route::get('storesPayouts', [App\Http\Controllers\StoresPayoutController::class, 'index'])->name('storesPayouts');
    Route::get('/storesPayout/{id}', [App\Http\Controllers\StoresPayoutController::class, 'index'])->name('stores.payout');

});
Route::middleware(['permission:restaurant-payouts,storesPayouts.create'])->group(function () {
    Route::get('storesPayouts/create', [App\Http\Controllers\StoresPayoutController::class, 'create'])->name('storesPayouts.create');
    Route::get('/storesPayouts/create/{id}', [App\Http\Controllers\StoresPayoutController::class, 'create']);

});

Route::middleware(['permission:driver-payouts,driversPayouts'])->group(function () {
    Route::get('driversPayouts', [App\Http\Controllers\DriversPayoutController::class, 'index'])->name('driversPayouts');
    Route::get('driverPayout/{id}', [App\Http\Controllers\DriversPayoutController::class, 'index'])->name('driver.payout');

});
Route::middleware(['permission:driver-payouts,driversPayouts.create'])->group(function () {
    Route::get('driversPayouts/create', [App\Http\Controllers\DriversPayoutController::class, 'create'])->name('driversPayouts.create');
    Route::get('driverPayout/create/{id}', [App\Http\Controllers\DriversPayoutController::class, 'create'])->name('driver.payout.create');

});
Route::middleware(['permission:wallet-transaction,walletstransaction'])->group(function () {
    Route::get('walletstransaction', [App\Http\Controllers\TransactionController::class, 'index'])->name('walletstransaction');
    Route::get('/walletstransaction/{id}', [App\Http\Controllers\TransactionController::class, 'index'])->name('users.walletstransaction');
});
Route::post('order-status-notification', [App\Http\Controllers\OrderController::class, 'sendNotification'])->name('order-status-notification');

Route::middleware(['permission:dynamic-notifications,dynamic-notification.index'])->group(function () {
    Route::get('dynamic-notification', [App\Http\Controllers\DynamicNotificationController::class, 'index'])->name('dynamic-notification.index');
});
Route::middleware(['permission:dynamic-notifications,dynamic-notification.save'])->group(function () {
    Route::get('dynamic-notification/save/{id?}', [App\Http\Controllers\DynamicNotificationController::class, 'save'])->name('dynamic-notification.save');

});
Route::middleware(['permission:dynamic-notifications,dynamic-notification.delete'])->group(function () {
    Route::get('dynamic-notification/delete/{id}', [App\Http\Controllers\DynamicNotificationController::class, 'delete'])->name('dynamic-notification.delete');
});
Route::middleware(['permission:god-eye,map'])->group(function () {
    Route::get('/map', [App\Http\Controllers\MapController::class, 'index'])->name('map');
    Route::post('/map/get_order_info', [App\Http\Controllers\MapController::class, 'getOrderInfo'])->name('map.getOrderInfo');
});
Route::prefix('settings')->group(function () {
    Route::middleware(['permission:currency,currencies'])->group(function () {
        Route::get('/currencies', [App\Http\Controllers\CurrencyController::class, 'index'])->name('currencies');
    });
    Route::middleware(['permission:currency,currencies.edit'])->group(function () {
        Route::get('/currencies/edit/{id}', [App\Http\Controllers\CurrencyController::class, 'edit'])->name('currencies.edit');
    });
    Route::middleware(['permission:currency,currencies.create'])->group(function () {
        Route::get('/currencies/create', [App\Http\Controllers\CurrencyController::class, 'create'])->name('currencies.create');
    });
    Route::middleware(['permission:global-setting,settings.app.globals'])->group(function () {
        Route::get('app/globals', [App\Http\Controllers\SettingsController::class, 'globals'])->name('settings.app.globals');
    });
    Route::middleware(['permission:admin-commission,settings.app.adminCommission'])->group(function () {
        Route::get('app/adminCommission', [App\Http\Controllers\SettingsController::class, 'adminCommission'])->name('settings.app.adminCommission');
    });
    Route::middleware(['permission:radius,settings.app.radiusConfiguration'])->group(function () {
        Route::get('app/radiusConfiguration', [App\Http\Controllers\SettingsController::class, 'radiosConfiguration'])->name('settings.app.radiusConfiguration');
    });
    Route::middleware(['permission:delivery-charge,settings.app.deliveryCharge'])->group(function () {
        Route::get('app/deliveryCharge', [App\Http\Controllers\SettingsController::class, 'deliveryCharge'])->name('settings.app.deliveryCharge');
    });
    Route::middleware(['permission:document-verification,settings.app.documentVerification'])->group(function () {
        Route::get('app/documentVerification', [App\Http\Controllers\SettingsController::class, 'documentVerification'])->name('settings.app.documentVerification');
    });

    Route::middleware(['permission:payment-method,payment-method'])->group(function () {
        Route::get('payment/stripe', [App\Http\Controllers\SettingsController::class, 'stripe'])->name('payment.stripe');
        Route::get('payment/applepay', [App\Http\Controllers\SettingsController::class, 'applepay'])->name('payment.applepay');
        Route::get('payment/razorpay', [App\Http\Controllers\SettingsController::class, 'razorpay'])->name('payment.razorpay');
        Route::get('payment/cod', [App\Http\Controllers\SettingsController::class, 'cod'])->name('payment.cod');
        Route::get('payment/paypal', [App\Http\Controllers\SettingsController::class, 'paypal'])->name('payment.paypal');
        Route::get('payment/paytm', [App\Http\Controllers\SettingsController::class, 'paytm'])->name('payment.paytm');
        Route::get('payment/wallet', [App\Http\Controllers\SettingsController::class, 'wallet'])->name('payment.wallet');
        Route::get('payment/payfast', [App\Http\Controllers\SettingsController::class, 'payfast'])->name('payment.payfast');
        Route::get('payment/paystack', [App\Http\Controllers\SettingsController::class, 'paystack'])->name('payment.paystack');
        Route::get('payment/flutterwave', [App\Http\Controllers\SettingsController::class, 'flutterwave'])->name('payment.flutterwave');
        Route::get('payment/mercadopago', [App\Http\Controllers\SettingsController::class, 'mercadopago'])->name('payment.mercadopago');
        Route::get('payment/xendit', [App\Http\Controllers\SettingsController::class, 'xendit'])->name('payment.xendit');
        Route::get('payment/orangepay', [App\Http\Controllers\SettingsController::class, 'orangepay'])->name('payment.orangepay');
        Route::get('payment/midtrans', [App\Http\Controllers\SettingsController::class, 'midtrans'])->name('payment.midtrans');
    });

    Route::middleware(['permission:language,settings.app.languages'])->group(function () {
        Route::get('app/languages', [App\Http\Controllers\SettingsController::class, 'languages'])->name('settings.app.languages');

    });
    Route::middleware(['permission:language,settings.app.languages.create'])->group(function () {
        Route::get('app/languages/create', [App\Http\Controllers\SettingsController::class, 'languagescreate'])->name('settings.app.languages.create');

    });
    Route::middleware(['permission:language,settings.app.languages.edit'])->group(function () {
        Route::get('app/languages/edit/{id}', [App\Http\Controllers\SettingsController::class, 'languagesedit'])->name('settings.app.languages.edit');

    });
    Route::middleware(['permission:special-offer,setting.specialOffer'])->group(function () {
        Route::get('app/specialOffer', [App\Http\Controllers\SettingsController::class, 'specialOffer'])->name('setting.specialOffer');
    });
    Route::middleware(['permission:cashback-offer,setting.cashbackOffer'])->group(function () {
        Route::get('app/cashbackOffer', [App\Http\Controllers\SettingsController::class, 'cashbackOffer'])->name('setting.cashbackOffer');
    });
    Route::get('app/story', [App\Http\Controllers\SettingsController::class, 'story'])->name('setting.story');
    Route::get('app/notifications', [App\Http\Controllers\SettingsController::class, 'notifications'])->name('settings.app.notifications');
    Route::get('mobile/globals', [App\Http\Controllers\SettingsController::class, 'mobileGlobals'])->name('settings.mobile.globals');
    Route::middleware(['permission:scheduleOrderNotification,settings.app.scheduleOrderNotification'])->group(function () {
        Route::get('app/scheduleOrderNotification', [App\Http\Controllers\SettingsController::class, 'scheduleOrderNotification'])->name('settings.app.scheduleOrderNotification');
    });
});
Route::post('/sendnotification', [App\Http\Controllers\BookTableController::class, 'sendnotification'])->name('sendnotification');

Route::middleware(['permission:general-notifications,notification'])->group(function () {
    Route::get('/notification', [App\Http\Controllers\NotificationController::class, 'index'])->name('notification');
});
Route::middleware(['permission:general-notifications,notification.send'])->group(function () {
    Route::get('/notification/send', [App\Http\Controllers\NotificationController::class, 'send'])->name('notification.send');

});
Route::post('broadcastnotification', [App\Http\Controllers\NotificationController::class, 'broadcastnotification'])->name('broadcastnotification');

Route::middleware(['permission:payout-request,payoutRequests.drivers'])->group(function () {
    Route::get('/payoutRequests/drivers', [App\Http\Controllers\PayoutRequestController::class, 'index'])->name('payoutRequests.drivers');
    Route::get('/payoutRequests/drivers/{id}', [App\Http\Controllers\PayoutRequestController::class, 'index'])->name('payoutRequests.drivers.view');
});
Route::middleware(['permission:payout-request,payoutRequests.stores'])->group(function () {
    Route::get('/payoutRequests/stores', [App\Http\Controllers\PayoutRequestController::class, 'store'])->name('payoutRequests.stores');
    Route::get('/payoutRequests/stores/{id}', [App\Http\Controllers\PayoutRequestController::class, 'store'])->name('payoutRequests.stores.view');

});
Route::get('order_transactions', [App\Http\Controllers\PaymentController::class, 'index'])->name('order_transactions');
Route::get('/order_transactions/{id}', [App\Http\Controllers\PaymentController::class, 'index'])->name('order_transactions.index');



Route::get('payment/success', [App\Http\Controllers\PaymentController::class, 'paymentsuccess'])->name('payment.success');
Route::get('payment/failed', [App\Http\Controllers\PaymentController::class, 'paymentfailed'])->name('payment.failed');
Route::get('payment/pending', [App\Http\Controllers\PaymentController::class, 'paymentpending'])->name('payment.pending');

Route::middleware(['permission:banners,setting.banners'])->group(function () {
    Route::get('/banners', [App\Http\Controllers\SettingsController::class, 'menuItems'])->name('setting.banners');
});
Route::middleware(['permission:banners,setting.banners.create'])->group(function () {
    Route::get('/banners/create', [App\Http\Controllers\SettingsController::class, 'menuItemsCreate'])->name('setting.banners.create');

});
Route::middleware(['permission:banners,setting.banners.edit'])->group(function () {
    Route::get('/banners/edit/{id}', [App\Http\Controllers\SettingsController::class, 'menuItemsEdit'])->name('setting.banners.edit');
});
Route::middleware(['permission:item-attribute,attributes'])->group(function () {
    Route::get('/attributes', [App\Http\Controllers\AttributeController::class, 'index'])->name('attributes');
});
Route::middleware(['permission:item-attribute,attributes.edit'])->group(function () {
    Route::get('/attributes/edit/{id}', [App\Http\Controllers\AttributeController::class, 'edit'])->name('attributes.edit');
});
Route::middleware(['permission:item-attribute,attributes.create'])->group(function () {
    Route::get('/attributes/create', [App\Http\Controllers\AttributeController::class, 'create'])->name('attributes.create');
});

Route::middleware(['permission:review-attribute,reviewattributes'])->group(function () {
    Route::get('/reviewattributes', [App\Http\Controllers\ReviewAttributeController::class, 'index'])->name('reviewattributes');
});
Route::middleware(['permission:review-attribute,reviewattributes.edit'])->group(function () {
    Route::get('/reviewattributes/edit/{id}', [App\Http\Controllers\ReviewAttributeController::class, 'edit'])->name('reviewattributes.edit');
});
Route::middleware(['permission:review-attribute,reviewattributes.create'])->group(function () {
    Route::get('/reviewattributes/create', [App\Http\Controllers\ReviewAttributeController::class, 'create'])->name('reviewattributes.create');
});

Route::middleware(['permission:footer,footerTemplate'])->group(function () {
    Route::get('footerTemplate', [App\Http\Controllers\SettingsController::class, 'footerTemplate'])->name('footerTemplate');
});
Route::middleware(['permission:home-page,homepageTemplate'])->group(function () {
    Route::get('/homepageTemplate', [App\Http\Controllers\SettingsController::class, 'homepageTemplate'])->name('homepageTemplate');
}); 
Route::middleware(['permission:cms,cms'])->group(function () {
    Route::get('cms', [App\Http\Controllers\CmsController::class, 'index'])->name('cms');
});
Route::middleware(['permission:cms,cms.edit'])->group(function () {
    Route::get('/cms/edit/{id}', [App\Http\Controllers\CmsController::class, 'edit'])->name('cms.edit');
});
Route::middleware(['permission:cms,cms.create'])->group(function () {
    Route::get('/cms/create', [App\Http\Controllers\CmsController::class, 'create'])->name('cms.create');
});
Route::middleware(['permission:reports,report.index'])->group(function () {
    Route::get('report/{type}', [App\Http\Controllers\ReportController::class, 'index'])->name('report.index');
});

Route::middleware(['permission:tax,tax'])->group(function () {
    Route::get('/tax', [App\Http\Controllers\TaxController::class, 'index'])->name('tax');
});
Route::middleware(['permission:tax,tax.edit'])->group(function () {
    Route::get('/tax/edit/{id}', [App\Http\Controllers\TaxController::class, 'edit'])->name('tax.edit');
});
Route::middleware(['permission:tax,tax.create'])->group(function () {
    Route::get('/tax/create', [App\Http\Controllers\TaxController::class, 'create'])->name('tax.create');
});

Route::middleware(['permission:email-template,email-templates.index'])->group(function () {
    Route::get('email-templates', [App\Http\Controllers\SettingsController::class, 'emailTemplatesIndex'])->name('email-templates.index');
});
Route::middleware(['permission:email-template,email-templates.edit'])->group(function () {
    Route::get('email-templates/save/{id?}', [App\Http\Controllers\SettingsController::class, 'emailTemplatesSave'])->name('email-templates.save');

});
Route::middleware(['permission:email-template,email-templates.delete'])->group(function () {
    Route::get('email-templates/delete/{id}', [App\Http\Controllers\SettingsController::class, 'emailTemplatesDelete'])->name('email-templates.delete');

});
Route::post('send-email', [App\Http\Controllers\SendEmailController::class, 'sendMail'])->name('sendMail');

Route::middleware(['permission:gift-cards,gift-card.index'])->group(function () {
    Route::get('gift-card', [App\Http\Controllers\GiftCardController::class, 'index'])->name('gift-card.index');
});
Route::middleware(['permission:gift-cards,gift-card.save'])->group(function () {
    Route::get('gift-card/save/{id?}', [App\Http\Controllers\GiftCardController::class, 'save'])->name('gift-card.save');

});
Route::middleware(['permission:gift-cards,gift-card.edit'])->group(function () {
    Route::get('gift-card/edit/{id}', [App\Http\Controllers\GiftCardController::class, 'save'])->name('gift-card.edit');
});

Route::middleware(['permission:roles,role.index'])->group(function () {
    Route::get('role', [App\Http\Controllers\RoleController::class, 'index'])->name('role.index');
});
Route::middleware(['permission:roles,role.save'])->group(function () {
    Route::get('role/save', [App\Http\Controllers\RoleController::class, 'save'])->name('role.save');
});
Route::middleware(['permission:roles,role.store'])->group(function () {
    Route::post('role/store', [App\Http\Controllers\RoleController::class, 'store'])->name('role.store');
});
Route::middleware(['permission:roles,role.delete'])->group(function () {
    Route::get('role/delete/{id}', [App\Http\Controllers\RoleController::class, 'delete'])->name('role.delete');
});
Route::middleware(['permission:roles,role.edit'])->group(function () {
    Route::get('role/edit/{id}', [App\Http\Controllers\RoleController::class, 'edit'])->name('role.edit');
});

Route::middleware(['permission:roles,role.update'])->group(function () {
    Route::post('role/update/{id}', [App\Http\Controllers\RoleController::class, 'update'])->name('role.update');

});
Route::middleware(['permission:admins,admin.users'])->group(function () {

    Route::get('admin-users', [App\Http\Controllers\UserController::class, 'adminUsers'])->name('admin.users');
});
Route::middleware(['permission:admins,admin.users.create'])->group(function () {
    Route::get('admin-users/create', [App\Http\Controllers\UserController::class, 'createAdminUsers'])->name('admin.users.create');
});
Route::middleware(['permission:admins,admin.users.store'])->group(function () {
    Route::post('admin-users/store', [App\Http\Controllers\UserController::class, 'storeAdminUsers'])->name('admin.users.store');

});
Route::middleware(['permission:admins,admin.users.delete'])->group(function () {
    Route::get('admin-users/delete/{id}', [App\Http\Controllers\UserController::class, 'deleteAdminUsers'])->name('admin.users.delete');

});
Route::middleware(['permission:admins,admin.users.edit'])->group(function () {
    Route::get('admin-users/edit/{id}', [App\Http\Controllers\UserController::class, 'editAdminUsers'])->name('admin.users.edit');

});
Route::middleware(['permission:admins,admin.users.update'])->group(function () {
    Route::post('admin-users/update/{id}', [App\Http\Controllers\UserController::class, 'updateAdminUsers'])->name('admin.users.update');

});
Route::middleware(['permission:admins,admin.users.delete'])->group(function () {
    Route::get('admin-users/delete/{id}', [App\Http\Controllers\UserController::class, 'deleteAdminUsers'])->name('admin.users.delete');

});
Route::middleware(['permission:zone,zone.list'])->group(function () {
    Route::get('zone', [App\Http\Controllers\ZoneController::class, 'index'])->name('zone');
});
Route::middleware(['permission:zone,zone.create'])->group(function () {
    Route::get('/zone/create', [App\Http\Controllers\ZoneController::class, 'create'])->name('zone.create');
});
Route::middleware(['permission:zone,zone.edit'])->group(function () {
    Route::get('/zone/edit/{id}', [App\Http\Controllers\ZoneController::class, 'edit'])->name('zone.edit');
});
Route::middleware(['permission:documents,documents.edit'])->group(function () {
    Route::get('/documents/edit/{id}', [App\Http\Controllers\DocumentController::class, 'edit'])->name('documents.edit');
});
Route::middleware(['permission:documents,documents.create'])->group(function () {
    Route::get('/documents/create', [App\Http\Controllers\DocumentController::class, 'create'])->name('documents.create');
});
Route::middleware(['permission:documents,documents.list'])->group(function () {
    Route::get('documents', [App\Http\Controllers\DocumentController::class, 'index'])->name('documents');
});
Route::middleware(['permission:vendors-document,vendor.document.list'])->group(function () {
    Route::get('vendors/document-list/{id}', [App\Http\Controllers\StoreController::class, 'DocumentList'])->name('vendors.document');
});
Route::middleware(['permission:vendors-document,vendor.document.edit'])->group(function () {
    Route::get('/vendors/document/upload/{driverId}/{id}', [App\Http\Controllers\StoreController::class, 'DocumentUpload'])->name('vendors.document.upload');
});
Route::middleware(['permission:drivers-document,driver.document.list'])->group(function () {
    Route::get('drivers/document-list/{id}', [App\Http\Controllers\DriverController::class, 'DocumentList'])->name('drivers.document');
});
Route::middleware(['permission:drivers-document,driver.document.edit'])->group(function () {
    Route::get('/drivers/document/upload/{driverId}/{id}', [App\Http\Controllers\DriverController::class, 'DocumentUpload'])->name('drivers.document.upload');
});
Route::post('send-notification', [App\Http\Controllers\NotificationController::class, 'sendNotification'])->name('send-notification');

Route::post('store-firebase-service', [App\Http\Controllers\HomeController::class,'storeFirebaseService'])->name('store-firebase-service');

Route::post('pay-to-user', [App\Http\Controllers\UserController::class,'payToUser'])->name('pay.user');
Route::post('check-payout-status', [App\Http\Controllers\UserController::class,'checkPayoutStatus'])->name('check.payout.status');

Route::middleware(['permission:on-board,onboard.list'])->group(function () {
    Route::get('/on-board', [App\Http\Controllers\OnBoardController::class, 'index'])->name('on-board');
});
Route::middleware(['permission:on-board,onboard.edit'])->group(function () {
    Route::get('/on-board/save/{id}', [App\Http\Controllers\OnBoardController::class, 'show'])->name('on-board.save');
});
Route::middleware(['permission:subscription-plans,subscription-plans'])->group(function () {
    Route::get('/subscription-plans', [App\Http\Controllers\SubscriptionPlanController::class, 'index'])->name('subscription-plans.index');
    Route::get('/current-subscriber/{id}', [App\Http\Controllers\StoreController::class, 'currentSubscriberList'])->name('current-subscriber.list');

});
Route::middleware(['permission:subscription-plans,subscription-plans.' . ((str_contains(Request::url(), 'save')) ? (explode("save", Request::url())[1] ? "edit" : "create") : Request::url())])->group(function () {
    Route::get('/subscription-plans/save/{id?}', [App\Http\Controllers\SubscriptionPlanController::class, 'save'])->name('subscription-plans.save');
});
Route::middleware(['permission:vendors,vendors.edit'])->group(function () {
    Route::get('/vendor/edit/{id}', [App\Http\Controllers\StoreController::class, 'vendorEdit'])->name('vendor.edit');
});
Route::middleware(['permission:subscription-history,subscription.history'])->group(function () {
    Route::get('/vendor/subscription-plan/history/{id?}', [App\Http\Controllers\StoreController::class, 'vendorSubscriptionPlanHistory'])->name('vendor.subscriptionPlanHistory');
});
Route::get('/storeFilters', [App\Http\Controllers\StoreFiltersController::class, 'index'])->name('storeFilters');
Route::get('/storeFilters/create', [App\Http\Controllers\StoreFiltersController::class, 'create'])->name('storeFilters.create');
Route::get('/storeFilters/edit/{id}', [App\Http\Controllers\StoreFiltersController::class, 'edit'])->name('storeFilters.edit');





Route::get('/create-package', function () {
    return view('new_ui.create-package'); 
});
Route::get('/add-subscription', function () {
    return view('new_ui.add-subscription');
});
Route::get('/change-subscription', function () {
    return view('new_ui.change-subscription');
});
Route::get('/edit-subscription', function () {
    return view('new_ui.edit-subscription');
});
Route::middleware(['permission:advertisements,advertisements.edit'])->group(function () {
    Route::get('/advertisements/edit/{id}', [App\Http\Controllers\AdvertisementsController::class, 'edit'])->name('advertisements.edit');
});
Route::middleware(['permission:advertisements,advertisements.create'])->group(function () {
    Route::get('/advertisements/create', [App\Http\Controllers\AdvertisementsController::class, 'create'])->name('advertisements.create');
});
Route::middleware(['permission:advertisements,advertisements'])->group(function () {
    Route::get('advertisements', [App\Http\Controllers\AdvertisementsController::class, 'index'])->name('advertisements');
});

Route::middleware(['permission:advertisements,advertisements'])->group(function () {
    Route::get('/advertisements/{id}', [App\Http\Controllers\AdvertisementsController::class, 'index'])->name('restaurants.advertisements');
});

Route::middleware(['permission:advertisements,advertisements.view'])->group(function () {
    Route::get('/advertisements/view/{id}', [App\Http\Controllers\AdvertisementsController::class, 'view'])->name('advertisements.view');
    Route::get('/advertisement/chat/{id}', [App\Http\Controllers\AdvertisementsController::class, 'chat'])->name('advertisement.chat');
});


Route::middleware(['permission:advertisements-list,advertisements.request'])->group(function () {
    Route::get('advertisements-list/requestes', [App\Http\Controllers\AdvertisementsController::class, 'requested'])->name('advertisements.request');
});

Route::middleware(['permission:deliveryman,deliveryman.edit'])->group(function () {
    Route::get('/deliveryman/edit/{id}', [App\Http\Controllers\DeliverymanController::class, 'edit'])->name('deliveryman.edit');
});
Route::middleware(['permission:deliveryman,deliveryman.create'])->group(function () {
    Route::get('/deliveryman/create', [App\Http\Controllers\DeliverymanController::class, 'create'])->name('deliveryman.create');
});
Route::middleware(['permission:deliveryman,deliveryman'])->group(function () {
    Route::get('deliveryman', [App\Http\Controllers\DeliverymanController::class, 'index'])->name('deliveryman');
});

Route::middleware(['permission:deliveryman,deliveryman'])->group(function () {
    Route::get('restaurant/deliveryman/{id}', [App\Http\Controllers\DeliverymanController::class, 'index'])->name('restaurants.deliveryman');
});
Route::post('/send-ad-notification', [App\Http\Controllers\AdvertisementsController::class, 'sendNotification'])->name('advertisement.sendnotification');

Route::middleware(['permission:cashback,cashback.list'])->group(function () {
    Route::get('cashback', [App\Http\Controllers\CashbackController::class, 'index'])->name('cashback.index');
});
Route::middleware(['permission:cashback,cashback.edit'])->group(function () {
    Route::get('/cashback/edit/{id}', [App\Http\Controllers\CashbackController::class, 'edit'])->name('cashback.edit');
});
Route::middleware(['permission:cashback,cashback.create'])->group(function () {
    Route::get('/cashback/create', [App\Http\Controllers\CashbackController::class, 'create'])->name('cashback.create');
});
Route::middleware(['permission:cashback,cashback.redeem.list'])->group(function () {
    Route::get('/cashback/redeem/{id}', [App\Http\Controllers\CashbackController::class, 'redeemList'])->name('cashback.redeem');
});

Route::middleware(['permission:drivers,drivers.chat'])->group(function () {
    Route::get('/drivers/chat/{id}', [App\Http\Controllers\DriverController::class, 'driverChat'])->name('drivers.chat');
});
Route::middleware(['permission:users,users.chat'])->group(function () {
        Route::get('/users/chat/{id}', [App\Http\Controllers\UserController::class, 'userChat'])->name('users.chat');
});
Route::middleware(['permission:supportHistory,supportHistory.list'])->group(function () {
        Route::get('/support', [App\Http\Controllers\SupportHistoryController::class, 'index'])->name('users.support');
});
Route::middleware(['permission:vendors,vendors.chat'])->group(function () {
        Route::get('/vendors/chat/{id}', [App\Http\Controllers\StoreController::class, 'vendorChat'])->name('vendors.chat');
});