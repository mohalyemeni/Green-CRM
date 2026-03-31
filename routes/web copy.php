<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Backend\BackendController;
use App\Http\Controllers\Backend\CategoryController;
use App\Http\Controllers\Backend\ProductController;
use App\Http\Controllers\Backend\TagController;
use Spatie\PdfToText\Pdf;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\Http;
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


function readPDF($pdfFile){
    $parser = new Parser();
    $pdf = $parser->parseFile(public_path($pdfFile));

    $text = $pdf->getText();

        // الآن يمكنك التعامل مع النص (مثلاً البحث عن كلمة معينة)
        return $text;
}

function getUETR($txt){
    preg_match('/00065\s*(.*?)\s*00066/', $txt, $match);
    $result = $match[1] ?? null;
    if(!$result){
        preg_match('/00042\s*(.*?)\s*00043/', $txt, $match);
        $result = $match[1] ?? null;
    }
    return $result;
}



function getSwiftInfo($UETR)
{
    $baseUrl = "https://www.commbank.com.au/business-banking/paymenttracker/";

    // فتح الصفحة للحصول على cookies
    $session = Http::withHeaders([
        'User-Agent' => 'Mozilla/5.0'
    ])->get($baseUrl);

    // تحويل cookies إلى مصفوفة صحيحة
    $cookies = [];

    foreach ($session->cookies() as $cookie) {
        $cookies[$cookie->getName()] = $cookie->getValue();
    }

    // رابط API الحقيقي
    $url = "https://www.commbank.com.au/business-banking/paymenttracker/v1/payments/".$UETR."/status-summary";

    $response = Http::withHeaders([
        'User-Agent' => 'Mozilla/5.0',
        'Accept' => 'application/json',
        'Referer' => $baseUrl,
        'X-Requested-With' => 'XMLHttpRequest'
    ])
    ->withCookies($cookies, 'www.commbank.com.au')
    ->get($url);

    return $response->json();
}

Route::get('/uetr',function(){
   $text = readPDF('SWIFT_COPY_1773450703043.pdf');
    $UETR = getUETR($text);

    $swiftInfo = json_decode(json_encode(getSwiftInfo($UETR)));

return $swiftInfo->data->status;
});

function reverseArabic($text)
{
    return implode('', array_reverse(preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY)));
}


function extractArabicDetails($text)
{
    $data = [];

    preg_match('/ﻞﻴﻤﻌﻟﺍ ﻢﺳﺇ(.+?)ﺦﻳﺭﺎﺘﻟﺍ/u', $text, $client);

    preg_match('/:ﻡﺪﺨﺘﺴﻤﻟﺍ ﻢﺳﺍ(.+?)ﺕﺎﻴﻠﻤﻌﻟﺍ/u', $text, $user);

    $data['client_name'] = isset($client[1]) ? trim(reverseArabic($client[1])) : null;

    $data['username'] = isset($user[1]) ? trim(reverseArabic($user[1])) : null;

    return $data;
}


function parseSwiftDetails($text)
{
    $data = [];

    preg_match('/GOLDEN FOREST PRODUCTS SDN BHD/', $text, $beneficiary);
    preg_match('/RHB ISLAMIC BANK BERHAD/', $text, $bank);
    preg_match('/USD\s*([\d,\.]+)/', $text, $amount_usd);
    preg_match('/([\d,\.]+)\s*SAR/', $text, $amount_sar);
    preg_match('/1USD=([\d\.]+)SAR/', $text, $rate);
    preg_match('/\b\d{11}\b/', $text, $reference);
    preg_match('/\b\d{14}\b/', $text, $sender_account);
    preg_match('/\b\d{14,}\b/', $text, $beneficiary_account);
    preg_match('/(\d{2}\/\d{2}\/\d{4}\s*\d{2}:\d{2})/', $text, $date);

    $arabic = extractArabicDetails($text);

    $data = [
        "client_name" => $arabic['client_name'],
        "username" => $arabic['username'],
        "beneficiary_name" => $beneficiary[0] ?? null,
        "bank_name" => $bank[0] ?? null,
        "amount_usd" => $amount_usd[1] ?? null,
        "amount_sar" => $amount_sar[1] ?? null,
        "exchange_rate" => $rate[1] ?? null,
        "reference_number" => $reference[0] ?? null,
        "sender_account" => $sender_account[0] ?? null,
        "beneficiary_account" => $beneficiary_account[0] ?? null,
        "date" => $date[1] ?? null
    ];

    return $data;
}


function parseSwiftFull($text)
{
    $data = [];

    // البيانات الإنجليزية
    preg_match('/GOLDEN FOREST PRODUCTS SDN BHD/', $text, $beneficiary);
    preg_match('/RHB ISLAMIC BANK BERHAD/', $text, $bank);
    preg_match('/USD\s*([\d,\.]+)/', $text, $amount);
    preg_match('/([\d,\.]+)\s*SAR/', $text, $sar);
    preg_match('/1USD=([\d\.]+)SAR/', $text, $rate);
    preg_match('/\b\d{11}\b/', $text, $reference);
    preg_match('/(\d{2}\/\d{2}\/\d{4}\s*\d{2}:\d{2})/', $text, $date);
$arabic = extractArabicDetails($text);
    // الحسابات
    preg_match_all('/\b\d{14}\b/', $text, $accounts);

    // الرسوم
    preg_match('/45\.00\s*SAR/', $text, $fee);
    preg_match('/6\.75\s*SAR/', $text, $vat);
    preg_match('/51\.75\s*SAR/', $text, $total_fee);

    // سبب التحويل
    preg_match('/ﺃﺳﺒﺎﺏ ﺍﻟﺘﺤﻮﻳﻞ(.+?)ﺭﺻﻴﺪ/u', $text, $reason);

    // الحالة
    preg_match('/ﺔﻟﺎﺤﻟﺍ(.+?)ﻊﺟﺮﻤﻟﺍ/u', $text, $status);

    $data = [
         "client_name" => $arabic['client_name'],
        "username" => $arabic['username'],
        "beneficiary_name" => $beneficiary[0] ?? null,
        "bank_name" => $bank[0] ?? null,
        "amount_usd" => $amount[1] ?? null,
        "amount_sar" => $sar[1] ?? null,
        "exchange_rate" => $rate[1] ?? null,
        "reference_number" => $reference[0] ?? null,
        "date" => $date[1] ?? null,
        "sender_account" => $accounts[0][0] ?? null,
        "beneficiary_account" => $accounts[0][1] ?? null,
        "fees" => $fee[0] ?? null,
        "vat" => $vat[0] ?? null,
        "total_fees" => $total_fee[0] ?? null,
        "transfer_reason" => isset($reason[1]) ? reverseArabic($reason[1]) : null,
        "status" => isset($status[1]) ? reverseArabic($status[1]) : null
    ];

    return $data;
}

Route::get('/get-details',function(){
   $text = readPDF('80500 GOLDEN FOREST PRODUCTS SDN BHD 4-3.pdf');
//    dd($text);
    $details = parseSwiftFull($text);

    // $swiftInfo = json_decode(json_encode(getSwiftInfo($UETR)));

return response()->json($details);
});





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
        Route::livewire('/leads', 'pages::leads.index')->name('leads.index');
        Route::livewire('/opportunities', 'pages::opportunities.index')->name('opportunities.index');
    });
    Route::resource('category', CategoryController::class);
});

Auth::routes(['verify' => true, 'register' => false]);

Route::get('/home', [HomeController::class, 'index'])->name('home');
