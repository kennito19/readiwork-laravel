<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\LoanEligibilityController;
use App\Http\Controllers\IdentityVerificationController;
use App\Http\Controllers\CreditScoreController;
use App\Http\Controllers\CrbBlacklistController;
use App\Http\Controllers\FullCreditReportController;
use App\Http\Controllers\CreditAccountHistoryController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Admin\AuthController as AdminAuth;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RequestsController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\AdminsController;
use App\Http\Controllers\Admin\AnnouncementsController;
use App\Http\Controllers\Admin\LogsController;
use App\Http\Controllers\Admin\ExportController;

// ── Public pages ──────────────────────────────────────────────────────────────
Route::get('/',              [HomeController::class,    'index'])->name('home');
Route::get('/services',      [PageController::class,    'services'])->name('services');
Route::get('/pricing',       [PageController::class,    'pricing'])->name('pricing');
Route::get('/about',         [PageController::class,    'about'])->name('about');
Route::get('/faq',           [PageController::class,    'faq'])->name('faq');
Route::get('/contact',       [PageController::class,    'contact'])->name('contact');
Route::post('/contact',      [PageController::class,    'contactSubmit'])->name('contact.submit');
Route::get('/business',      [PageController::class,    'business'])->name('business');
Route::get('/get-started',   [PageController::class,    'getStarted'])->name('get-started');
Route::get('/privacy',       [PageController::class,    'privacy'])->name('privacy');
Route::get('/terms',         [PageController::class,    'terms'])->name('terms');
Route::get('/cookies',       [PageController::class,    'cookies'])->name('cookies');
Route::get('/documentation', [PageController::class,    'documentation'])->name('documentation');

// ── Loan Eligibility ──────────────────────────────────────────────────────────
Route::get( '/loan-eligibility',             [LoanEligibilityController::class, 'index'])->name('loan-eligibility');
Route::post('/loan-eligibility/confirm',     [LoanEligibilityController::class, 'confirm'])->name('loan-eligibility.confirm');
Route::get( '/loan-eligibility/result',      [LoanEligibilityController::class, 'result'])->name('loan-eligibility.result');
Route::get( '/loan-eligibility/pdf/{rid}',   [LoanEligibilityController::class, 'downloadPdf'])->name('loan-eligibility.pdf');

// ── Identity Verification ─────────────────────────────────────────────────────
Route::get( '/identity-verification',         [IdentityVerificationController::class, 'index'])->name('identity-verification');
Route::post('/identity-verification/confirm', [IdentityVerificationController::class, 'confirm'])->name('identity-verification.confirm');
Route::get( '/identity-verification/result',  [IdentityVerificationController::class, 'result'])->name('identity-verification.result');
Route::get( '/identity-verification/pdf/{rid}', [IdentityVerificationController::class, 'downloadPdf'])->name('identity-verification.pdf');

// ── Credit Score ──────────────────────────────────────────────────────────────
Route::get( '/credit-score-check',         [CreditScoreController::class, 'index'])->name('credit-score-check');
Route::post('/credit-score-check/confirm', [CreditScoreController::class, 'confirm'])->name('credit-score-check.confirm');
Route::get( '/credit-score-check/result',  [CreditScoreController::class, 'result'])->name('credit-score-check.result');
Route::get( '/credit-score-check/pdf/{rid}', [CreditScoreController::class, 'downloadPdf'])->name('credit-score-check.pdf');

// ── CRB Blacklist ─────────────────────────────────────────────────────────────
Route::get( '/crb-blacklist-check',         [CrbBlacklistController::class, 'index'])->name('crb-blacklist-check');
Route::post('/crb-blacklist-check/confirm', [CrbBlacklistController::class, 'confirm'])->name('crb-blacklist-check.confirm');
Route::get( '/crb-blacklist-check/result',  [CrbBlacklistController::class, 'result'])->name('crb-blacklist-check.result');
Route::get( '/crb-blacklist-check/pdf/{rid}', [CrbBlacklistController::class, 'downloadPdf'])->name('crb-blacklist-check.pdf');

// ── Full Credit Report ────────────────────────────────────────────────────────
Route::get( '/full-credit-report',             [FullCreditReportController::class, 'index'])->name('full-credit-report');
Route::post('/full-credit-report/confirm',     [FullCreditReportController::class, 'confirm'])->name('full-credit-report.confirm');
Route::get( '/full-credit-report/result',      [FullCreditReportController::class, 'result'])->name('full-credit-report.result');
Route::get( '/full-credit-report/pdf/{rid}',   [FullCreditReportController::class, 'downloadPdf'])->name('full-credit-report.pdf');

// ── Credit Account History ────────────────────────────────────────────────────
Route::get( '/credit-account-history',             [CreditAccountHistoryController::class, 'index'])->name('credit-account-history');
Route::post('/credit-account-history/confirm',     [CreditAccountHistoryController::class, 'confirm'])->name('credit-account-history.confirm');
Route::get( '/credit-account-history/result',      [CreditAccountHistoryController::class, 'result'])->name('credit-account-history.result');
Route::get( '/credit-account-history/pdf/{rid}',   [CreditAccountHistoryController::class, 'downloadPdf'])->name('credit-account-history.pdf');

// ── Utility AJAX ─────────────────────────────────────────────────────────────
Route::post('/verify-id',  [\App\Http\Controllers\VerifyIdController::class,  'verify'])->name('verify-id');
Route::post('/chatbot',    [\App\Http\Controllers\ChatbotController::class,    'reply'])->middleware('throttle:30,1')->name('chatbot');

// ── Payment API (AJAX) ────────────────────────────────────────────────────────
Route::post('/stk-push',            [PaymentController::class, 'stkPush'])->name('stk-push');
Route::get( '/check-payment-status',[PaymentController::class, 'checkStatus'])->name('check-payment-status');

// ── Admin ─────────────────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get( '/login',  [AdminAuth::class, 'showLogin'])->name('login');
    Route::post('/login',  [AdminAuth::class, 'login'])->middleware('throttle:10,1')->name('login.post');
    Route::post('/logout', [AdminAuth::class, 'logout'])->name('logout');

    Route::middleware('admin.auth')->group(function () {
        Route::get('/',              [DashboardController::class,   'index'])->name('dashboard');
        Route::get('/requests',      [RequestsController::class,    'index'])->name('requests');
        Route::get('/requests/{id}', [RequestsController::class,    'show'])->name('requests.show');
        Route::get('/transactions',  [RequestsController::class,    'transactions'])->name('transactions');
        Route::get('/analytics',     [DashboardController::class,   'analytics'])->name('analytics');
        Route::get('/settings',              [SettingsController::class,    'index'])->name('settings');
        Route::post('/settings',             [SettingsController::class,    'update'])->name('settings.update');
        Route::post('/settings/logo',        [SettingsController::class,    'uploadLogo'])->name('settings.logo');
        Route::post('/settings/password',    [SettingsController::class,    'changePassword'])->name('settings.password');
        Route::get('/admins',        [AdminsController::class,      'index'])->name('admins');
        Route::post('/admins',       [AdminsController::class,      'store'])->name('admins.store');
        Route::delete('/admins/{id}',[AdminsController::class,      'destroy'])->name('admins.destroy');
        Route::get('/announcements', [AnnouncementsController::class,'index'])->name('announcements');
        Route::post('/announcements',[AnnouncementsController::class,'store'])->name('announcements.store');
        Route::delete('/announcements/{id}',[AnnouncementsController::class,'destroy'])->name('announcements.destroy');
        Route::get('/logs',          [LogsController::class,        'index'])->name('logs');
        Route::get('/login-history', [LogsController::class,        'loginHistory'])->name('login-history');
        Route::get('/export',        [ExportController::class,      'export'])->name('export');
    });
});
