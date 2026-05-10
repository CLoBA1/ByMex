<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\TourController as WebTourController;
use App\Http\Controllers\Web\ReservationController as WebReservationController;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\TourController as AdminTourController;
use App\Http\Controllers\Admin\ClientController as AdminClientController;
use App\Http\Controllers\Admin\ReservationController as AdminReservationController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\BoardingPointController as AdminBoardingPointController;
use App\Http\Controllers\Admin\BannerController as AdminBannerController;

use App\Http\Controllers\Api\SeatController as ApiSeatController;
use App\Http\Controllers\Api\NotificationController as ApiNotificationController;

use Illuminate\Support\Facades\Route;

// Rutas Públicas (Web)
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/servicios', [HomeController::class, 'services'])->name('services');

Route::get('/tours', [WebTourController::class, 'index'])->name('tours.index');
Route::get('/tours/{id}', [WebTourController::class, 'show'])->name('tours.show');

// Rutas de Reservación (Web)
Route::post('/reservations', [WebReservationController::class, 'store'])->name('reservations.store');
Route::get('/reservations/{token}/success', [WebReservationController::class, 'success'])->name('reservations.success');
Route::get('/reservations/{token}/ticket', [WebReservationController::class, 'downloadTicket'])->name('reservations.ticket');
Route::get('/reservations/{token}/voucher/{paymentId}', [WebReservationController::class, 'downloadVoucher'])->name('reservations.voucher');
Route::post('/reservations/{token}/passengers/{passengerId}/document', [WebReservationController::class, 'uploadPublicDocument'])->name('reservations.passenger.document');
Route::get('/reservations/{token}/documents/{documentId}/download', [WebReservationController::class, 'downloadPublicDocument'])->name('reservations.passenger.document.download');

// API Pública
Route::get('/api/seats/{id}', [ApiSeatController::class, 'getSeats']);

// Panel de Admin
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Tours CRUD
    Route::get('/admin/tours', [AdminTourController::class, 'index'])->name('admin.tours.index');
    Route::get('/admin/tours/create', [AdminTourController::class, 'create'])->name('admin.tours.create');
    Route::post('/admin/tours', [AdminTourController::class, 'store'])->name('admin.tours.store');
    Route::get('/admin/tours/{id}', [AdminTourController::class, 'show'])->name('admin.tours.show');
    Route::get('/admin/tours/{id}/edit', [AdminTourController::class, 'edit'])->name('admin.tours.edit');
    Route::put('/admin/tours/{id}', [AdminTourController::class, 'update'])->name('admin.tours.update');
    Route::delete('/admin/tours/{id}', [AdminTourController::class, 'destroy'])->name('admin.tours.destroy');
    
    Route::post('/admin/reservations/{id}/status', [AdminReservationController::class, 'updateStatus'])->name('admin.reservations.status');
    Route::post('/admin/reservations/{id}/payment', [AdminReservationController::class, 'storePayment'])->name('admin.reservations.payment');
    Route::get('/admin/payments/{id}/voucher', [AdminReservationController::class, 'downloadVoucher'])->name('admin.payments.voucher');
    Route::get('/admin/reservations/{id}/ticket', [AdminReservationController::class, 'downloadTicket'])->name('admin.reservations.ticket');
    Route::get('/admin/reservations/surplus', [AdminReservationController::class, 'surplusList'])->name('admin.reservations.surplus');
    Route::get('/admin/reservations/{id}', [AdminReservationController::class, 'show'])->name('admin.reservations.show');
    Route::post('/admin/passengers/{id}/validate', [AdminReservationController::class, 'validatePassenger'])->name('admin.passengers.validate');
    Route::post('/admin/passengers/{id}/status', [AdminReservationController::class, 'updatePassengerStatus'])->name('admin.passengers.status');
    Route::post('/admin/passengers/{id}/type', [AdminReservationController::class, 'updatePassengerType'])->name('admin.passengers.type');
    // Autobuses (Flota)
    Route::resource('admin/buses', \App\Http\Controllers\Admin\BusController::class)->names([
        'index' => 'admin.buses.index',
        'create' => 'admin.buses.create',
        'store' => 'admin.buses.store',
        'edit' => 'admin.buses.edit',
        'update' => 'admin.buses.update',
        'destroy' => 'admin.buses.destroy',
    ]);
    Route::delete('/admin/buses/images/{image}', [\App\Http\Controllers\Admin\BusController::class, 'destroyImage'])->name('admin.buses.images.destroy');
    Route::post('/admin/buses/images/{image}/primary', [\App\Http\Controllers\Admin\BusController::class, 'setPrimaryImage'])->name('admin.buses.images.primary');

    Route::post('/admin/reservations/{id}/adjustment', [AdminReservationController::class, 'storeAdjustment'])->name('admin.reservations.adjustment');
    Route::post('/admin/passengers/{id}/document', [AdminReservationController::class, 'uploadDocument'])->name('admin.passengers.document.upload');
    Route::get('/admin/documents/{id}/download', [AdminReservationController::class, 'downloadDocument'])->name('admin.documents.download');
    Route::delete('/admin/documents/{id}', [AdminReservationController::class, 'deleteDocument'])->name('admin.documents.destroy');
    Route::delete('/admin/reservations/{id}', [AdminReservationController::class, 'destroy'])->name('admin.reservations.destroy');

    // Banners CRUD
    Route::resource('admin/banners', AdminBannerController::class)->names([
        'index' => 'admin.banners.index',
        'create' => 'admin.banners.create',
        'store' => 'admin.banners.store',
        'edit' => 'admin.banners.edit',
        'update' => 'admin.banners.update',
        'destroy' => 'admin.banners.destroy',
    ]);

    // Clientes
    Route::get('/admin/clients', [AdminClientController::class, 'index'])->name('admin.clients.index');

    // Configuración
    Route::get('/admin/settings', [AdminSettingController::class, 'index'])->name('admin.settings');

    // Módulo de Pagos y Bancos
    Route::get('/admin/settings/payments', [\App\Http\Controllers\Admin\PaymentSettingController::class, 'index'])->name('admin.settings.payments');
    Route::post('/admin/settings/payments', [\App\Http\Controllers\Admin\PaymentSettingController::class, 'updateSettings'])->name('admin.settings.payments.update');
    Route::post('/admin/settings/banks', [\App\Http\Controllers\Admin\PaymentSettingController::class, 'storeBank'])->name('admin.settings.banks.store');
    Route::put('/admin/settings/banks/{bank}', [\App\Http\Controllers\Admin\PaymentSettingController::class, 'updateBank'])->name('admin.settings.banks.update');
    Route::delete('/admin/settings/banks/{bank}', [\App\Http\Controllers\Admin\PaymentSettingController::class, 'destroyBank'])->name('admin.settings.banks.destroy');

    // Puntos de Abordaje
    Route::get('/admin/boarding-points', [AdminBoardingPointController::class, 'index'])->name('admin.boarding-points.index');
    Route::post('/admin/boarding-points', [AdminBoardingPointController::class, 'store'])->name('admin.boarding-points.store');
    Route::put('/admin/boarding-points/{id}', [AdminBoardingPointController::class, 'update'])->name('admin.boarding-points.update');
    Route::delete('/admin/boarding-points/{id}', [AdminBoardingPointController::class, 'destroy'])->name('admin.boarding-points.destroy');

    // Notification API
    Route::get('/api/admin/notifications', [ApiNotificationController::class, 'index'])->name('admin.notifications.api');
    Route::post('/api/admin/notifications/read', [ApiNotificationController::class, 'markRead'])->name('admin.notifications.read');
});
// Pagos en línea (Mercado Pago)
Route::get('/reservations/{token}/pay', [\App\Http\Controllers\Web\PaymentController::class, 'checkout'])->name('reservations.pay');

// Mercado Pago Webhook (excluido de CSRF)
Route::post('/mercadopago/webhook', [\App\Http\Controllers\Api\MercadoPagoWebhookController::class, 'handle'])->name('mercadopago.webhook');

// Stripe Webhook (Legacy / desactivado)
Route::post('/stripe/webhook', [\App\Http\Controllers\Api\StripeWebhookController::class, 'handle'])->name('stripe.webhook');

// Mock de Pago (Fallback para pruebas sin Stripe)
if (app()->environment('local', 'testing')) {
    Route::post('/reservations/{token}/mock-pay', [WebReservationController::class, 'mockPay'])->name('reservations.mockPay');
}

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
