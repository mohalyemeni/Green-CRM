<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Backend\BackendController;
use App\Http\Controllers\Backend\CategoryController;
use App\Http\Controllers\Backend\ProductController;
use App\Http\Controllers\Backend\TagController;

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

Route::get('/', [BackendController::class, 'login']);

// Route::get('/', [FrontendController::class, 'index'])->name('frontend.index');
// Route::get('/cart', [FrontendController::class, 'cart'])->name('frontend.cart');
// Route::get('/checkout', [FrontendController::class, 'checkout'])->name('frontend.checkout');
// Route::get('/details', [FrontendController::class, 'details'])->name('frontend.details');
// Route::get('/shop', [FrontendController::class, 'shop'])->name('frontend.shop');


Route::prefix('admin')->name('admin.')->group(function () {

    Route::middleware('guest')->group(function () {
        Route::get('/login', [BackendController::class, 'login'])->name('login');
        Route::get('/forgot-password', [BackendController::class, 'forgetPassword'])->name('forget.password');
    });
    Route::middleware(['role:Admin|Supervisor'])->group(function () {
        Route::get('/', [BackendController::class, 'index'])->name('index_route');
        Route::get('/index', [BackendController::class, 'index'])->name('index');
        Route::resource('categories', CategoryController::class);
        Route::resource('products', ProductController::class);
        Route::resource('tags', TagController::class);
        Route::livewire('/customers', 'pages::customers.index')->name('customers.index');
        Route::livewire('/currencies', 'pages::currencies.index')->name('currencies.index');
        Route::livewire('/companies', 'pages::companies.index')->name('companies.index');
        Route::livewire('/countries', 'pages::countries.index')->name('countries.index');
        Route::livewire('/branches', 'pages::branches.index')->name('branches.index');
        Route::livewire('/customer-groups', 'pages::customer-groups.index')->name('customer-groups.index');
        Route::livewire('/industries', 'pages::industries.index')->name('industries.index');
        Route::livewire('/lead-sources', 'pages::leadsources.index')->name('lead-sources.index');
        Route::livewire('/opportunity-sources', 'pages::opportunitysources.index')->name('opportunity-sources.index');
        Route::livewire('/lost-reasons', 'pages::lost-reasons.index')->name('lost-reasons.index');
        Route::livewire('/pipeline-stages', 'pages::pipeline-stages.index')->name('pipeline-stages.index');
        Route::livewire('/lead-statuses', 'pages::lead-statuses.index')->name('lead-statuses.index');


        Route::livewire('/service-groups', 'pages::service-groups.index')->name('service-groups.index');
        Route::livewire('/services', 'pages::services.index')->name('services.index');

        // عروض الأسعار
        Route::livewire('/quotations', 'pages::quotations.index')->name('quotations.index');
        Route::livewire('/quotations/create', 'pages::quotations.create')->name('quotations.create');
        Route::livewire('/quotations/{quotation}/edit', 'pages::quotations.edit')->name('quotations.edit');
        Route::livewire('/quotations/{quotation}', 'pages::quotations.show')->name('quotations.show');

        // العملاء المحتملين (Leads)
        Route::livewire('/leads', 'pages::leads.index')->name('leads.index');
        Route::livewire('/leads/create', 'pages::leads.create')->name('leads.create');
        Route::livewire('/leads/{lead}/edit', 'pages::leads.edit')->name('leads.edit');
        Route::livewire('/leads/{lead}', 'pages::leads.show')->name('leads.show');

        // الفرص البيعية (Opportunities)
        Route::livewire('/opportunities', 'pages::opportunities.index')->name('opportunities.index');
        Route::livewire('/opportunities/create', 'pages::opportunities.create')->name('opportunities.create');
        Route::livewire('/opportunities/{opportunity}/edit', 'pages::opportunities.edit')->name('opportunities.edit');
        Route::livewire('/opportunities/{opportunity}', 'pages::opportunities.show')->name('opportunities.show');

        // أرشيف المرفقات (Attachments Archive)
        Route::livewire('/attachments', 'pages::attachments.index')->name('attachments.index');
    });
    Route::resource('category', CategoryController::class);
});

Auth::routes(['verify' => true, 'register' => false]);

Route::get('/home', [HomeController::class, 'index'])->name('home');
