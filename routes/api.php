<?php

use App\Http\Controllers\Api\Admin\EqubDrawController;
use App\Http\Controllers\Api\Admin\EqubGroupController;
use App\Http\Controllers\Api\Admin\EqubMembershipController;
use App\Http\Controllers\Api\Admin\EqubPackageController;
use App\Http\Controllers\Api\Admin\EqubPaymentController;
use App\Http\Controllers\Api\Admin\MemberManagementController;
use App\Http\Controllers\Api\Admin\UserManagementController;
use App\Http\Controllers\Api\Agent\AgentCommissionsController;
use App\Http\Controllers\Api\Agent\AgentDashboardController;
use App\Http\Controllers\Api\Agent\AgentMembersController;
use App\Http\Controllers\Api\Agent\AgentPaymentsController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Member\AgentInfoController;
use App\Http\Controllers\Api\Member\EqubDrawController as MemberEqubDrawController;
use App\Http\Controllers\Api\Member\EqubGroupController as MemberEqubGroupController;
use App\Http\Controllers\Api\Member\EqubMembershipController as MemberEqubMembershipController;
use App\Http\Controllers\Api\Member\EqubPackageController as MemberEqubPackageController;
use App\Http\Controllers\Api\Member\EqubPaymentController as MemberEqubPaymentController;
use App\Http\Controllers\Api\Member\PaymentController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

// Public authentication routes
Route::prefix('auth')->group(function () {
    Route::post('send-otp', [AuthController::class, 'sendOtp']);
    Route::post('check-user', [AuthController::class, 'checkUser']);
    Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);
    Route::post('delete-account-by-phone', [AuthController::class, 'deleteAccountByPhone']);
    Route::post('refresh', [AuthController::class, 'refresh']);

});

Route::get('settings', [\App\Http\Controllers\Api\SettingsController::class, 'index']);
Route::get('faqs', [\App\Http\Controllers\Api\FaqController::class, 'index']);
Route::get('exchange-rates', [\App\Http\Controllers\Api\ExchangeRateController::class, 'index']);
Route::get('promotions', [\App\Http\Controllers\Api\PromoController::class, 'index']);

// Route::post('/payment/chapa/webhook', [MemberEqubPaymentController::class, 'webhook'])
//     ->name('payment.chapa.webhook');


// Protected routes (require Sanctum authentication)
Route::middleware(['jwt.auth', 'active.user'])->group(function () {
    Route::prefix('auth')->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('delete-account', [AuthController::class, 'deleteAccount']);
        Route::post('update', [UserController::class, 'update']);
        Route::post('fcm-token', [UserController::class, 'updateFcmToken']);
    });

    Route::prefix('admin')->middleware('admin.staff')->group(function () {
        Route::get('users', [UserManagementController::class, 'index']);
        Route::get('users/{user}', [UserManagementController::class, 'show']);
        Route::patch('users/{user}/status', [UserManagementController::class, 'updateStatus']);
        Route::patch('users/{user}/role', [UserManagementController::class, 'updateRole']);
        Route::patch('users/{user}/password', [UserManagementController::class, 'resetPassword']);

        Route::get('members', [MemberManagementController::class, 'index']);
        Route::post('members', [MemberManagementController::class, 'store']);
        Route::get('members/{member}', [MemberManagementController::class, 'show']);
        Route::patch('members/{member}', [MemberManagementController::class, 'update']);
        Route::delete('members/{member}', [MemberManagementController::class, 'destroy']);

        Route::apiResource('equb-packages', EqubPackageController::class);
        Route::apiResource('equb-groups', EqubGroupController::class);
        Route::post('equb-groups/{equbGroup}/open-registration', [EqubGroupController::class, 'openRegistration']);
        Route::post('equb-groups/{equbGroup}/close-registration', [EqubGroupController::class, 'closeRegistration']);
        Route::post('equb-groups/{equbGroup}/start-equb', [EqubGroupController::class, 'startEqub']);
        Route::post('equb-groups/{equbGroup}/run-draw', [EqubGroupController::class, 'runDraw']);
        Route::apiResource('equb-memberships', EqubMembershipController::class);
        Route::apiResource('equb-payments', EqubPaymentController::class);
        Route::post('equb-payments/{equbPayment}/initiate-chapa', [EqubPaymentController::class, 'initiateChapa']);
        Route::get('equb-draws', [EqubDrawController::class, 'index']);
        Route::get('equb-draws/{equbDraw}', [EqubDrawController::class, 'show']);
    });

    Route::prefix('agent')->middleware('agent.user')->group(function () {
        Route::get('dashboard', AgentDashboardController::class);
        Route::get('members', [AgentMembersController::class, 'index']);
        Route::post('members', [AgentMembersController::class, 'store']);
        Route::get('payouts', [AgentPaymentsController::class, 'payout']);
        Route::get('payments', [AgentPaymentsController::class, 'index']);
        Route::post('payments', [AgentPaymentsController::class, 'store']);
        Route::get('commissions', [AgentCommissionsController::class, 'index']);
    });

    Route::prefix('member')->middleware('member.user')->group(function () {
        Route::get('agents/{referralCode}', [AgentInfoController::class, 'show']);
        Route::post('payments', [PaymentController::class, 'store']);

        // Equb APIs for mobile
        Route::get('equb-packages', [MemberEqubPackageController::class, 'index']);
        Route::get('equb-packages/{equbPackage}', [MemberEqubPackageController::class, 'show']);
        Route::get('equb-groups', [MemberEqubGroupController::class, 'index']);
        Route::get('equb-groups/{equbGroup}', [MemberEqubGroupController::class, 'show']);
        Route::get('equb-memberships', [MemberEqubMembershipController::class, 'index']);
        Route::post('equb-memberships', [MemberEqubMembershipController::class, 'store']);
        Route::get('equb-memberships/{equbMembership}', [MemberEqubMembershipController::class, 'show']);
        Route::post('equb-memberships/{equbMembership}/leave', [MemberEqubMembershipController::class, 'leave']);
        Route::get('equb-payments', [MemberEqubPaymentController::class, 'index']);
        Route::post('equb-payments', [MemberEqubPaymentController::class, 'store']);
        Route::get('equb-payments/{equbPayment}', [MemberEqubPaymentController::class, 'show']);
        Route::get('equb-draws', [MemberEqubDrawController::class, 'index']);
        Route::get('equb-draws/{equbDraw}', [MemberEqubDrawController::class, 'show']);
    });
});
