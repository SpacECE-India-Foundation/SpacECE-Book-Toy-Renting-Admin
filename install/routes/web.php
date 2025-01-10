<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\TransitionController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\Web\AppSettingController;
use App\Http\Controllers\Web\AreaController;
use App\Http\Controllers\Web\Auth\LoginController;
use App\Http\Controllers\Web\Banners\BannerController;
use App\Http\Controllers\Web\Contacts\ContactController;
use App\Http\Controllers\Web\Customers\CustomerController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\Driver\DriverController;
use App\Http\Controllers\Web\FCMController;
use App\Http\Controllers\Web\InvoiceManageController;
use App\Http\Controllers\Web\LanguageController;
use App\Http\Controllers\Web\MailConfigurationController;
use App\Http\Controllers\Web\MapApiKeyUpdateController;
use App\Http\Controllers\Web\MobileAppUrl\MobileAppUrlController;
use App\Http\Controllers\Web\NotificationController;
use App\Http\Controllers\Web\OrderScheduleController;
use App\Http\Controllers\Web\PaymentController;
use App\Http\Controllers\Web\Products\CouponController;
use App\Http\Controllers\Web\Products\OrderController;
use App\Http\Controllers\Web\Products\ProductController;
use App\Http\Controllers\Web\Profile\ProfileController;
use App\Http\Controllers\Web\Report\ReportController;
use App\Http\Controllers\Web\Revenues\RevenueController;
use App\Http\Controllers\Web\Root\AdminController;
use App\Http\Controllers\Web\Root\ShopController;
use App\Http\Controllers\Web\Services\AdditionalServiceController;
use App\Http\Controllers\Web\Services\ServiceController;
use App\Http\Controllers\Web\Setting\SettingController;
use App\Http\Controllers\Web\SMSGatewaySetupController;
use App\Http\Controllers\Web\Social\SocialController;
use App\Http\Controllers\Web\StoreProfileController;
use App\Http\Controllers\Web\StripeKeyUpateController;
use App\Http\Controllers\Web\Variants\VariantController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\PosController;

/*
+--------------------------------------------------------------------------
+ Web Routes
+--------------------------------------------------------------------------
*/

Route::get('/login', [LoginController::class, 'index'])->middleware('guest')->name('login');
Route::post('/login', [LoginController::class, 'login'])->middleware('guest')->name('login');
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');
Route::get('pos', [PosController::class, 'index'])->name('pos.index');
Route::get('sales', [PosController::class, 'sales'])->name('pos.sales');

Route::controller(ServiceController::class)->group(function () {
    Route::get('/services/{service}/variants', 'getVariant')->name('service.getVariant');
});

Route::middleware(['auth', 'role:admin|visitor|root|vendor|store', 'check_permission'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('root');

    // Service routes
    Route::controller(ServiceController::class)->group(function () {
        Route::get('/services', 'index')->name('service.index');
        Route::get('/services/create', 'create')->name('service.create');
        Route::post('/services', 'store')->name('service.store');
        Route::get('/services/{service}/edit', 'edit')->name('service.edit');
        Route::put('/services/{service}', 'update')->name('service.update');
        Route::get('/services/{service}/toggle-status', 'toggleActivationStatus')->name('service.status.toggle');
    });

    // Variant routes
    Route::controller(VariantController::class)->group(function () {
        Route::get('/variants', 'index')->name('variant.index');
        Route::put('/variants/{variant}/', 'update')->name('variant.update');
        Route::post('/variants', 'store')->name('variant.store');
        Route::get('/variants/{variant}/products', 'productsVariant')->name('variant.products');
    });
    Route::get('/shops/{shopId}/services', [ServiceController::class, 'getServicesByShop'])->name('shops.services');


    // Notification routes
    Route::controller(NotificationController::class)->group(function () {
        Route::get('/notifications', 'index')->name('notification.index');
        Route::post('/send-notifications', 'sendNotification')->name('notification.send');
    });

    // Customer routes
    Route::controller(CustomerController::class)->group(function () {
        Route::get('/customers', 'index')->name('customer.index');
        Route::get('/customers/{customer}/show', 'show')->name('customer.show');
        Route::get('/customers/create', 'create')->name('customer.create');
        Route::post('/customers', 'store')->name('customer.store');
        Route::get('/customers/{customer}/edit', 'edit')->name('customer.edit');
        Route::put('/customers/{customer}', 'update')->name('customer.update');
        Route::get('/customers/{customer}/change-password', 'changePassword')->name('customer.change.password');
        Route::put('/customers/{customer}/update-password', 'updatePassword')->name('customer.update.password');
    });

    // Product routes
    Route::controller(ProductController::class)->group(function () {
        Route::get('/products', 'index')->name('product.index');
        Route::get('/products/create', 'create')->name('product.create');
        Route::post('/products', 'store')->name('product.store');
        Route::get('/products/{product}/show', 'show')->name('product.show');
        Route::get('/products/{product}/edit', 'edit')->name('product.edit');
        Route::put('/products/{product}', 'update')->name('product.update');
        Route::get('/products/{product}/toggle-status', 'toggleActivationStatus')->name('product.status.toggle');
        Route::put('/products/{product}/ordering', 'orderUpdate')->name('product.update.order');
    });

    // Banner Routes
    Route::controller(BannerController::class)->group(function () {
        Route::get('/web-banners', 'index')->name('banner.index');
        Route::get('/mobile-banners', 'getPromotional')->name('banner.promotional');
        Route::post('/banners', 'store')->name('banner.store');
        Route::get('/banners/{banner}/edit', 'edit')->name('banner.edit');
        Route::put('/banners/{banner}', 'update')->name('banner.update');
        Route::delete('/banners/{banner}', 'destroy')->name('banner.destroy');
        Route::get('/banners/{banner}/toggle-status', 'toggleActivationStatus')->name('banner.status.toggle');
    });

    // Order Routes
    Route::controller(OrderController::class)->group(function () {
        Route::get('/orders', 'index')->name('order.index');
        Route::get('/orders/{order}', 'show')->name('order.show');
        Route::get('/orders/{order}/update-status', 'statusUpdate')->name('order.status.change');
        Route::get('/orders/{order}/print/labels', 'printLabels')->name('order.print.labels');
        Route::get('/orders/{order}/print/invoice', 'printInvioce')->name('order.print.invioce');

        //INcomplete Order Route
        Route::get('/orders-incomplete', 'index')->name('orderIncomplete.index');
        Route::get('/orders/{order}/paid', 'orderPaid')->name('orderIncomplete.paid');
    });

    // Revenue Eoutes
    Route::controller(RevenueController::class)->group(function () {
        Route::get('revenues', 'index')->name('revenue.index');
        Route::get('revenues/generate-pdf', 'generatePDF')->name('revenue.generate.pdf');
        Route::get('reports/generate-pdf', 'generateInvoicePDF')->name('report.generate.pdf');
    });

    // Coupon Routes
    Route::controller(CouponController::class)->group(function () {
        Route::get('/coupons', 'index')->name('coupon.index');
        Route::get('/coupons/create', 'create')->name('coupon.create');
        Route::post('/coupons', 'store')->name('coupon.store');
        Route::get('/coupons/{coupon}/edit', 'edit')->name('coupon.edit');
        Route::put('/coupons/{coupon}', 'update')->name('coupon.update');
    });

    //Contact Routes
    Route::get('/contacts', [ContactController::class, 'index'])->name('contact');

    //Driver Routes
    Route::controller(DriverController::class)->group(function () {
        Route::get('/drivers', 'index')->name('driver.index');
        Route::get('/drivers/create', 'create')->name('driver.create');
        Route::post('/drivers/store', 'store')->name('driver.store');
        Route::get('/drivers-assign/{order}/{drive}', 'driverAssign')->name('driver.assign');
        Route::get('/drivers/{driver}/details', 'details')->name('driver.details');
        Route::get('/driver/{driver}/toggle-status', 'toggleStatus')->name('driver.status.toggle');
    });

    //Profile
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/setting/profile', 'index')->name('profile.index');
        Route::post('/setting/profile', 'update')->name('profile.update');
        Route::get('/setting/profile-edit', 'edit')->name('profile.edit');
        Route::post('/setting/profile/change-password', 'changePassword')->name('profile.change-password');
    });
    Route::get('/setting/profile/change-password', function () {
        return view('profile.change-password');
    })->name('profile.change-password');

    Route::controller(OrderScheduleController::class)->group(function () {
        Route::get('/{type}/scheduls', 'index')->name('schedule.index');
        Route::get('/schedules/{id}/toggle/update', 'updateStatus')->name('toggole.status.update');
        Route::put('/schedules/{orderSchedule}/update', 'update')->name('schedule.update');
    });

    Route::controller(WalletController::class)->group(function () {
        Route::get('wallet/store', 'store')->name('wallet.store');
        Route::post('wallet/{wallet}/update', 'update')->name('wallet.update');
    });

    // shop status toggle
    Route::controller(ShopController::class)->group(function () {
        Route::get('/shops/{user}/toggle', 'toggle')->name('shop.status.toggle');
    });

    Route::controller(TransitionController::class)->group(function () {
        Route::get('/transaction/{transaction}/update', 'update')->name('transaction.update');
    });

    //Report
    Route::controller(ReportController::class)->group(function () {
        Route::get('/report/shop-list/', 'index')->name('shoplist.index');
        Route::get('/report/{store}/generate-report', 'generateReport')->name('shop.generateReport');
        Route::get('/report/{store}/export-order', 'exportOrder')->name('shop.order.export');
        Route::get('/report/export-shop/{id?}', 'exportStore')->name('shop.store.export');
        Route::get('/report/shop-wise-list/{store}', 'details')->name('shoplist.details');
    });

    Route::get('/shops/map-view', [ShopController::class, 'storeOnMap'])->name('shops.map-view');

    Route::controller(InvoiceManageController::class)->group(function () {
        Route::get('/invoice-manage', 'index')->name(('invoiceManage.index'));
        Route::get('/invoice-preview/{name}', 'preview')->name(('invoiceManage.preview'));
        Route::post('/invoice-manage', 'update')->name(('invoiceManage.update'));
        Route::post('/invoice-pdf-update', 'pdfUpdate')->name(('invoiceManage.pdfUpdate'));
    });

    // About
    Route::controller(AboutController::class)->group(function () {
        Route::get('/about-us', 'index')->name(('about.index'));
        Route::get('/about-us/edit', 'edit')->name(('about.edit'));
        Route::post('/about-us/update/{about?}', 'update')->name(('about.update'));
    });
});

// access only shop
Route::middleware(['auth', 'check_permission', 'role:store'])->group(function () {
    // Additional service
    Route::controller(AdditionalServiceController::class)->prefix('additional-services')->group(function () {
        Route::get('/', 'index')->name('additional.index');
        Route::post('/', 'store')->name('additional.store');
        Route::get('/create', 'create')->name('additional.create');
        Route::get('/{additional}/edit', 'edit')->name('additional.edit');
        Route::put('/{additional}', 'update')->name('additional.update');
        Route::get('/{additional}/toggle-status', 'toggleActivationStatus')->name('additional.status.toggle');
    });

    //route for profile store
    Route::controller(StoreProfileController::class)->group(function () {
        Route::get('/profile', 'index')->name('store.index');
        Route::get('/profile/edit', 'edit')->name('store.edit');
        Route::put('/profile/{store}/update', 'update')->name('store.update');
        Route::put('/profile/location', 'location')->name('store.location-update');
        Route::put('/profile/{user}/user-update', 'userUpdate')->name('store.user.update');
        Route::put('/profile/{store}/address-update', 'updateAddress')->name('store.address.update');
    });
});

// Area Route
Route::controller(AreaController::class)->group(function () {
    Route::get('/areas', 'index')->name('area.index');
    Route::get('/areas/create', 'create')->name('area.create');
    Route::post('/areas/store', 'store')->name('area.store');
    Route::get('/areas/{area}/edit', 'edit')->name('area.edit');
    Route::post('/areas/{area}/update', 'update')->name('area.update');
    Route::get('/areas/{area}/toggle', 'toggle')->name('area.toggle');
    Route::get('/areas/{area}/delete', 'delete')->name('area.delete');
});

Route::middleware(['auth', 'check_permission', 'role:vendor|store'])->group(function () {
    //Route for wallet
    Route::controller(WalletController::class)->group(function () {
        Route::get('/wallet', 'index')->name('wallet.index');
        Route::get('/wallet/{wallet}/transaction', 'transaction')->name('wallet.transction');
        Route::get('/wallet/{wallet}/withdraw', 'withdraw')->name('wallet.withdraw');
    });
});

Route::middleware(['auth', 'check_permission', 'role:root|admin'])->group(function () {
    Route::controller(ShopController::class)->group(function () {
        Route::get('/shops', 'index')->name('shop.index');
        Route::get('/shops/create', 'create')->name('shop.create');
        Route::post('/shops/store', 'store')->name('shop.store');
        Route::get('/shops/{store}/edit', 'edit')->name('shop.edit');
        Route::put('/shops/{store}/update', 'update')->name('shop.update');
        Route::post('/shops/{store}/commission', 'commissionUpdate')->name('shop.commissionUpdate');
        Route::get('/shops/{store}/details', 'show')->name('shop.show');
        Route::get('/shops/{store}/order', 'order')->name('shop.order');
        Route::get('/shops/{store}/services', 'service')->name('shop.service');
        Route::get('/shops/{store}/product', 'product')->name('shop.product');
        Route::get('/shops/{product}/approve', 'statusToggle')->name('shop.product.approve');
        Route::post('/shops/{store}/services-update', 'serviceUpdate')->name('shop.service.update');
        Route::get('/shops/{wallet}/transaction', 'transaction')->name('shop.transaction');
    });
});

Route::get('/shop/locations', [ShopController::class, 'shopLocation']);

// access only root user.
Route::middleware(['auth', 'check_permission', 'role:root'])->group(function () {
    // Settings Routes
    Route::controller(SettingController::class)->group(function () {
        Route::get('/settings/{slug}', 'show')->name('setting.show');
        Route::get('/settings/{slug}/edit', 'edit')->name('setting.edit');
        Route::put('/settings/{setting}', 'update')->name('setting.update');
    });

    //Delivery Cost
    Route::controller(MobileAppUrlController::class)->group(function () {
        Route::get('/setting/mobile-app-link', 'index')->name('mobileApp');
        Route::post('/setting/mobile-app-link', 'updateOrCreate')->name('mobileApp');
    });

    //Social link
    Route::controller(SocialController::class)->group(function () {
        Route::get('/setting/social-link', 'index')->name('socialLink.index');
        Route::post('/setting/social-link', 'store')->name('socialLink.store');
        Route::post('/setting/social-link/{socialLink}', 'update')->name('socialLink.update');
        Route::get('/setting/social-link/{socialLink}/delete', 'delete')->name('socialLink.delete');
    });

    Route::controller(StripeKeyUpateController::class)->group(function () {
        Route::get('/stripe-key', 'index')->name('stripeKey.index');
        Route::post('/stripe-key/{stripeKey?}', 'update')->name('stripeKey.update');
    });

    Route::controller(AppSettingController::class)->group(function () {
        Route::get('/web-setting', 'index')->name(('appSetting.index'));
        Route::post('/web-setting/{appSetting?}', 'update')->name(('appSetting.update'));
    });

    Route::get('/customer/{customer}/delete', [CustomerController::class, 'delete'])->name('customer.delete');

    Route::controller(AdminController::class)->group(function () {
        Route::get('/admins', 'index')->name('admin.index');
        Route::get('/admins/{user}/toggle-status-update', 'toggleStatusUpdate')->name('admin.status-update');
        Route::get('/admins/create', 'create')->name('admin.create');
        Route::post('/admins', 'store')->name('admin.store');
        Route::get('/admins/{user}/edit', 'edit')->name('admin.edit');
        Route::put('/admins/{user}', 'update')->name('admin.update');
        Route::get('/admins/{user}/show', 'show')->name('admin.show');
        Route::post('/admins/{user}/set-permission', 'setPermission')->name('admin.set-permission');
    });

    //  SMS Gateway
    Route::controller(SMSGatewaySetupController::class)->group(function () {
        Route::get('/sms-gateway', 'index')->name('sms-gateway.index');
        Route::put('/sms-gateway', 'update')->name('sms-gateway.update');
    });

    //  mail configuration
    Route::controller(MailConfigurationController::class)->group(function () {
        Route::get('/mail-configuration', 'index')->name('mail-config.index');
        Route::put('/mail-configuration', 'update')->name('mail-config.update');
    });

    // Google map key
    Route::controller(MapApiKeyUpdateController::class)->group(function () {
        Route::get('/map-api-key', 'index')->name('mapApiKey.index');
        Route::post('/map-api-key/{mapApiKey?}', 'update')->name('mapApiKey.update');
    });

    // firebase cloud message
    Route::controller(FCMController::class)->group(function () {
        Route::get('/fcm-configuration', 'index')->name('fcm.index');
        Route::post('/fcm-configuration', 'update')->name('fcm.update');
    });
});

Route::get('/order-payment/{order}/{card}', [PaymentController::class, 'payment'])->name('payment');
Route::get('/setup-intents/{customer}/{card}/{amount}/{order}', [PaymentController::class, 'intent']);
Route::get('/order-update/{order}', [PaymentController::class, 'updatePayment']);

Route::controller(LanguageController::class)->group(function () {
    Route::get('/language', 'index')->name('language.index');
    Route::get('/language/create', 'create')->name('language.create');
    Route::post('/language/store', 'store')->name('language.store');
    Route::get('/language/{language}/edit', 'edit')->name('language.edit');
    Route::put('/language/{language}/update', 'update')->name('language.update');
    Route::get('/language/{language}/delete', 'delete')->name('language.delete');
});

Route::get('payment', [PaymentController::class, 'testIndex']);

Route::get('/change-language', function () {
    if (request()->ln) {
        App::setLocale(\request()->ln);
        session()->put('local', \request()->ln);
    }

    return back();
})->name('change.local');
