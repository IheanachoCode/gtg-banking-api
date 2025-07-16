<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\TransactionController;
use App\Http\Controllers\API\AccountController;
use App\Http\Controllers\API\StaffController;
use App\Http\Controllers\API\VendorController;
use App\Http\Controllers\API\VendorTVController;
use App\Http\Controllers\API\MeterTypeController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\LoanController;
use App\Http\Controllers\API\ProductRequestController;
use App\Http\Controllers\API\DescoController;
use App\Http\Controllers\API\WithdrawalController;
use App\Http\Controllers\API\TransferController;
use App\Http\Controllers\API\StaffLoginController;
use App\Http\Controllers\API\InsuranceController;
use App\Http\Controllers\API\StatementController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\StaffRequestController;
use App\Http\Controllers\API\CustomerController;
use App\Http\Controllers\API\BillPaymentController;
use App\Http\Controllers\API\PinController;
use App\Http\Controllers\API\DeviceController;
use App\Http\Controllers\API\PasswordController;
use App\Http\Controllers\API\FingerprintController;
use App\Http\Controllers\API\BiometricController;
use App\Http\Controllers\API\OtpController;
use App\Http\Controllers\API\SliderController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\SubCategoryController;
use App\Http\Controllers\API\ItemController;
use App\Http\Controllers\API\CategorySearchController;
use App\Http\Controllers\API\PasswordResetController;
use App\Http\Controllers\API\EmailVerificationController;
use App\Http\Controllers\API\PhoneVerificationController;
use App\Http\Controllers\API\OtpVerificationController;
use App\Http\Controllers\API\SignupVerificationController;
use App\Http\Controllers\API\PhoneSignupController;
use App\Http\Controllers\API\LgaController;
use App\Http\Controllers\API\StateController;
use App\Http\Controllers\API\PassportController;
use App\Http\Controllers\API\TransferHistoryController;
use App\Http\Controllers\API\TransactionCountController;

use App\Http\Controllers\API\TicketController;
use App\Http\Controllers\API\AccountOfficerController;

use App\Http\Controllers\API\RatingController;

use App\Http\Controllers\API\FeedbackController;
use App\Http\Controllers\API\FeedbackImageController;








// API version 1 group - Public routes (require only API key)
Route::middleware('api.key')->prefix('v1')->group(function () {
    // Basic public info
    Route::get('/', function () {
        return response()->json([
            'message' => 'Welcome to the API v1',
            'version' => '1.0',
            'status' => 'active'
        ], 200);
    });

    // Public routes with rate limiting
    Route::middleware(['throttle:60,1'])->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);

        // Public information endpoints
        Route::get('/vendors', [VendorController::class, 'getAllVendors']);
        Route::get('/vendors/tv', [VendorTVController::class, 'getAllVendors']);
        Route::get('/meter-types', [MeterTypeController::class, 'getAllMeterTypes']);

        Route::get('/fetch-desco', [DescoController::class, 'getAllDesco']);

        Route::post('/staff/login', [StaffController::class, 'login']);


    });
});

// Protected routes (require both API key and Sanctum authentication)
Route::middleware(['api.key', 'auth:sanctum'])->prefix('v1')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/fetch-account-name', [AccountController::class, 'getAccountName']);
    Route::post('/fetch-accounts', [AccountController::class, 'getAllAccounts']);


    // Account operations
    Route::prefix('account')->group(function () {


        //Route::get('/name/{userID}', [AccountController::class, 'getAccountName']);
        Route::post('/balance', [AccountController::class, 'getAccountBalance']);
        Route::get('/statement/{account_no}', [AccountController::class, 'getStatement']);
        Route::get('/transactions/{account_no}', [AccountController::class, 'getTransactions']);
        Route::post('/transfer', [AccountController::class, 'transfer']);
        Route::post('/withdrawal', [AccountController::class, 'withdrawal']);

    });

    Route::post('/account/statement', [StatementController::class, 'getStatement']);


    // Products and Orders
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'getAllProducts']);
        Route::post('/info', [ProductController::class, 'getProductInfo']);
        Route::post('/purchase', [ProductRequestController::class, 'purchaseProduct']);
    });


    Route::post('/user/orders', [OrderController::class, 'getUserOrders']);
    Route::post('user/orders/active', [OrderController::class, 'getActiveOrders']);

    Route::post('/withdrawal-request', [WithdrawalController::class, 'createWithdrawalRequest']);

    // Loans
    Route::prefix('loans')->group(function () {
        Route::post('/history', [LoanController::class, 'getLoanHistory']);
        Route::post('/active', [LoanController::class, 'getActiveLoans']);
    });

    // Transactions

    Route::post('/transfer', action: [TransferController::class, 'transfer']);

    Route::post('/transfers/history', [TransactionController::class, 'getUserTransfers']);
    Route::get('/history/{account_no}', [TransactionController::class, 'getHistory']);
    Route::get('/between-dates', [TransactionController::class, 'getBetweenDates']);
    Route::post('/bill-payment', [TransactionController::class, 'billPayment']);
    // Route::prefix('transactions')->group(function () {

    // });

    // Staff operations

    Route::get('/staff/name', [StaffController::class, 'getStaffName']);
    Route::get('/staff/transactions', [StaffController::class, 'getRecentTransactions']);

    Route::get('/staff/daily-transactions', [StaffController::class, 'getDailyTransactions']);
    Route::get('/staff/daily-deposits', [StaffController::class, 'getDailyDeposit']);
    Route::get('/staff/daily-withdrawals', [StaffController::class, 'getDailyWithdrawals']);
    Route::get('/staff/overage', [StaffController::class, 'getOverage']);
    Route::get('/staff/shortage', [StaffController::class, 'getShortage']);
    Route::get('/staff/aggregate', [StaffController::class, 'getAggregate']);
    Route::get('/transactions/recent', [TransactionController::class, 'getRecentTransactions']);
    Route::get('/transactions/range', [TransactionController::class, 'getTransactionsByDateRange']);

    Route::get('/account/name', [AccountController::class, 'getAccountNameByAccountNo']);
    Route::get('/payment-modes', [PaymentController::class, 'getAllPaymentModes']);

    Route::get('/transactions/daily', [TransactionController::class, 'getDailyTransactions']);
    Route::post('/staff/request', [StaffRequestController::class, 'store']);

    Route::get('/staff/transactions/unverified', [StaffController::class, 'getUnverifiedTransactions']);

    Route::get('/staff/transactions/verified', [StaffController::class, 'getVerifiedTransactions']);

    Route::get('/products/available', [ProductController::class, 'getAvailableProducts']);
    Route::post('/customer/register', [CustomerController::class, 'register']);

    Route::post('/withdrawal/mobile', [WithdrawalController::class, 'requestWithdrawal']);
    Route::post('/bill-payment/mobile', [BillPaymentController::class, 'processBillPayment']);

    Route::put('/payment/verify', [PaymentController::class, 'verifyPayment']);
    Route::post('/pin/validate', [PinController::class, 'validatePin']);

    Route::post('/staff/pin/validate', [StaffController::class, 'validatePin']);
    Route::post('/device/setup', [DeviceController::class, 'setup']);
    Route::post('/password/forgot', [PasswordController::class, 'forgotPassword']);

    Route::post('/pin/change', [PinController::class, 'changePin']);

    Route::post('/fingerprint/validate', [FingerprintController::class, 'validateFingerprint']);
    Route::get('/biometric/fetch', [BiometricController::class, 'fetchBiometric']);

    Route::post('/otp/generate', [OtpController::class, 'generate']);
    Route::post('/otp/verify', [OtpController::class, 'verify']);
    Route::get('/sliders', [SliderController::class, 'getSlides']);

    Route::get('/categories', [CategoryController::class, 'index']);
    Route::post('/subcategories', [SubCategoryController::class, 'getSubCategories']);
    Route::post('/items/category', [ItemController::class, 'getCategoryItems']);
    Route::get('/items/details', [ItemController::class, 'getItemDetails']);
    Route::post('/categories/search', [CategorySearchController::class, 'search']);

    Route::post('/orders', [OrderController::class, 'store']);
    Route::post('/password/forget-otp', [PasswordResetController::class, 'sendOtp']);
    Route::post('/password/verify-otp', [PasswordResetController::class, 'verifyOtp']);
    Route::post('/password/create-new', [PasswordResetController::class, 'createNewPassword']);
    Route::post('/email/verify', [EmailVerificationController::class, 'sendOtp']);

    Route::post('/phone/verify', [PhoneVerificationController::class, 'sendOtp']);

    Route::post('/otp/verify-check', [OtpVerificationController::class, 'verifyOtp']);

    Route::post('/signup/verify-email', [SignupVerificationController::class, 'sendVerificationEmail']);


    Route::post('/signup/verify-phone', [PhoneSignupController::class, 'sendVerificationOtp']);

    Route::post('/email/verify-otp', [EmailVerificationController::class, 'verifyOtp']);

    Route::post('/phone/verify-otp', [PhoneVerificationController::class, 'verifyOtp']);

    Route::post('/lgas', [LgaController::class, 'index']);

    Route::get('/states', [StateController::class, 'index']);

    Route::post('/passport/upload', [PassportController::class, 'upload']);
    Route::post('/account/type', [AccountController::class, 'getType']);
    Route::post('/account/status', [AccountController::class, 'getStatus']);

    Route::post('/transactions/dates', [TransactionController::class, 'getDates']);
    Route::post('/transfers/history', [TransferHistoryController::class, 'index']);
    Route::post('/transactions/dates/limited', [TransactionController::class, 'getLimitedDates']);

    Route::post('/transfers/history/between-dates', [TransferHistoryController::class, 'betweenDates']);
    Route::post('/transactions/count', [TransactionCountController::class, 'count']);

    Route::post('/account/number', [AccountController::class, 'getNumber']);

    Route::post('/tickets', [TicketController::class, 'store']);
    Route::get('/fetch_tickets', [TicketController::class, 'index']);
    Route::get('/account/officer', [AccountOfficerController::class, 'getName']);
    Route::get('/account/officer/phone', [AccountOfficerController::class, 'getPhone']);
    Route::get('/account/officer/email', action: [AccountOfficerController::class, 'getEmail']);
    Route::get('/account-officers', [AccountOfficerController::class, 'index']);
    Route::get('/account-officers/all', [AccountOfficerController::class, 'all']);
    Route::post('/account_officer_ratings', [RatingController::class, 'store']);

    Route::post('/feedbacks', [FeedbackController::class, 'store']);
    Route::post('/feedbacks/images', [FeedbackImageController::class, 'store']);

    Route::get('/customers/name', [CustomerController::class, 'getName']);

    //Insurance
    Route::post('/insurance/view',  [InsuranceController::class, 'viewInsurance']);
    Route::post('/insurance/active', [InsuranceController::class, 'viewActiveInsurance']);
    Route::post('/statements/pdf', [StatementController::class, 'sendPdf']);

    // Feedback
    Route::prefix('feedback')->group(function () {
        Route::post('/rate', [StaffController::class, 'rateAccountOfficer']);
        Route::post('/submit', [StaffController::class, 'submitFeedback']);
        Route::post('/upload-images', [StaffController::class, 'uploadFeedbackImages']);
    });
});

// API version 2 group (for future use)
Route::prefix('v2')->group(function () {
    Route::get('/', function () {
        return response()->json([
            'message' => 'Welcome to the API v2',
            'version' => '2.0',
            'status' => 'development'
        ], 200);
    });
});
