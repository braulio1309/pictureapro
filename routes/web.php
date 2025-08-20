<?php

use App\Livewire\Welcome;
use App\Livewire\Pages\RegisterPage;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GoogleCalendarController;
use App\Http\Controllers\WebhooksController;
use App\Livewire\Pages\Dashboard\Users\AccountPage;
use App\Livewire\Pages\Dashboard\Users\ImagesPage;
use App\Livewire\Pages\Dashboard\Users\PaymentsPage;
use App\Livewire\Pages\DashboardPage;
use App\Livewire\Pages\EmailVerificationPage;
use App\Livewire\Pages\ForgotPasswordPage;
use App\Livewire\Pages\LoginPage;
use App\Livewire\Pages\ResetPasswordPage;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\CheckoutController;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::redirect('/', '/panel');

Route::post('/subscribe', [CheckoutController::class, 'subscribe'])->name('subscribe');
Route::get('/subscribe', \App\Livewire\Pages\Dashboard\Subscription\IndexPage::class)->name('subscribes');



// Webhook de Stripe (NO requiere auth)
Route::post('/stripe/webhook', [WebhooksController::class, 'handle'])
    ->name('stripe.webhook');

Route::middleware(['guest'])->group(function () {
    Route::get('/registro', RegisterPage::class)->name('register');
    Route::get('/ingresar', LoginPage::class)->name('login');

    Route::get('/recuperar-contraseña', ForgotPasswordPage::class)->name('password.request');
    Route::get('/recuperar-contraseña/{token}', ResetPasswordPage::class)->name('password.reset');
});

Route::post('/webhooks/stripe/{code}', [WebhooksController::class, 'stripe'])->name('webhooks.stripe');

Route::get('/calendario/{slug}', \App\Livewire\Pages\Public\Calendars\ShowPage::class)->name('public.calendars.show');

Route::middleware(['auth', 'subscribed'])->group(function () {
    Route::get('/salir', [AuthController::class, 'logout'])->name('logout');

    Route::get('/verificar-correo', EmailVerificationPage::class)->name('verification.notice');
    Route::get('/verificar-correo/{id}/{hash}', [AuthController::class, 'verifyEmail'])->middleware(['signed'])->name('verification.verify');
    Route::get('/verificar', [AuthController::class, 'verifyEmail']);

    Route::middleware(['verified', 'subscribed'])->prefix('panel')->group(function () {
        Route::get('/', DashboardPage::class)->name('dashboard');

        Route::get('/mi-cuenta', AccountPage::class)->name('account.details');
        Route::get('/mi-cuenta/imagenes', ImagesPage::class)->name('account.images');
        Route::get('/mi-cuenta/pagos', PaymentsPage::class)->name('account.payments');
        Route::get('/mi-cuenta/pagos-test', [AuthController::class, 'stripe']);

        Route::get('/servicios', \App\Livewire\Pages\Dashboard\Services\IndexPage::class)->name('dashboard.services.index');
        Route::get('/materiales', \App\Livewire\Pages\Dashboard\Materials\IndexPage::class)->name('dashboard.materials.index');
        Route::get('/productos', \App\Livewire\Pages\Dashboard\Products\IndexPage::class)->name('dashboard.products.index');
        Route::get('/galerias', \App\Livewire\Pages\Dashboard\Galleries\IndexPage::class)->name('dashboard.galleries.index');

        Route::get('/calendarios', \App\Livewire\Pages\Dashboard\Calendars\IndexPage::class)->name('dashboard.calendars.index');
        Route::get('/calendarios/{id}', \App\Livewire\Pages\Dashboard\Calendars\ShowPage::class)->name('dashboard.calendars.show');

        Route::get('/clientes', \App\Livewire\Pages\Dashboard\Clients\IndexPage::class)->name('dashboard.clients.index');
        Route::get('/clientes/{id}', \App\Livewire\Pages\Dashboard\Clients\ShowPage::class)->name('dashboard.clients.show');
        Route::get('/clients/import', \App\Livewire\Pages\Dashboard\Clients\ImportPage::class)->name('clients.import');

    });

    Route::middleware(['role:admin'])->prefix('admin')->group(function () {
        Route::get('/usuarios', \App\Livewire\Pages\Admin\Users\IndexPage::class)->name('admin.users.index');
        Route::get('/usuarios/{id}', \App\Livewire\Pages\Admin\Users\ShowPage::class)->name('admin.users.show');
    });
});

Route::middleware('auth')->group(function() {
    Route::get('/google/connect', [GoogleCalendarController::class, 'connect'])->name('google.connect');
    Route::get('/google/callback', [GoogleCalendarController::class, 'callback'])->name('google.callback');
    Route::get('/google/disconnect', [GoogleCalendarController::class, 'disconnect'])->name('google.disconnect');
});


