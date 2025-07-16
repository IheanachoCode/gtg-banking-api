<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\BaseController;
use App\Http\Resources\UserResource;
use App\Models\ClientRegistration;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Dedoc\Scramble\Http\Annotations\Body;
use Dedoc\Scramble\Http\Annotations\Header;
use Dedoc\Scramble\Http\Annotations\Query;
use Dedoc\Scramble\Http\Annotations\Response;

class AuthController extends Controller
{
    use ApiResponse;

    /**
     * Maximum number of login attempts
     */
    private const MAX_LOGIN_ATTEMPTS = 5;

    /**
     * Login user and create token
     * 
     * Authenticate a user with their userID and password, returning an access token.
     * 
     * @header x-api-key string required API key for authentication
     * @body userID string required User's unique identifier
     * @body password string required User's password
     * @response 200 {
     *   "status": true,
     *   "message": "Login successful",
     *   "data": {
     *     "token": "1|abc123...",
     *     "token_type": "Bearer",
     *     "expires_in": 86400,
     *     "user": {
     *       "id": 1,
     *       "user_id": "USER123",
     *       "name": "John Doe",
     *       "email": "john@example.com",
     *       "phone": "08012345678",
     *       "account_type": "Savings",
     *       "verification_status": "Verified",
     *       "account_status": "Active"
     *     }
     *   }
     * }
     * @response 401 {
     *   "status": false,
     *   "message": "Invalid credentials",
     *   "data": null
     * }
     * @response 422 {
     *   "status": false,
     *   "message": "Validation failed",
     *   "errors": {
     *     "userID": ["The userID field is required."],
     *     "password": ["The password field is required."]
     *   }
     * }
     *
     * @security ApiKeyAuth
     */
    public function login(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'userID' => 'required|string',
                'password' => 'required|string'
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 422);
            }

            $user = ClientRegistration::where('user_id', $request->userID)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return $this->errorResponse('Invalid credentials', 401);
            }

            // Revoke any existing tokens
            $user->tokens()->delete();

            // Create new token with 24-hour expiry
            $token = $user->createToken('auth_token', ['*'], now()->addHours(24))->plainTextToken;

            // Update last login timestamp
            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip()
            ]);

            return $this->successResponse([
                'token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => 86400, // 24 hours in seconds
                'user' => new UserResource($user)
            ], 'Login successful');

        } catch (\Exception $e) {
            Log::error('Login error', [
                'error' => $e->getMessage()
            ]);
            return $this->errorResponse('An error occurred during login', 500);
        }
    }

    /**
     * Register a new user
     * 
     * Create a new user account with the provided personal and account details.
     * 
     * @header x-api-key string required API key for authentication
     * @body lastname string required User's last name
     * @body othernames string required User's other names
     * @body gender string required User's gender (Male/Female)
     * @body Nationality string required User's nationality
     * @body birthday date required User's date of birth (YYYY-MM-DD)
     * @body occupation string required User's occupation
     * @body phone string required User's phone number
     * @body email string required User's email address
     * @body residential_address string required User's residential address
     * @body residential_state string required User's residential state
     * @body residential_Local_govt string required User's residential local government
     * @body state_of_origin string required User's state of origin
     * @body local_govt_of_origin string required User's local government of origin
     * @body town_of_origin string required User's town of origin
     * @body bvn_no string required User's Bank Verification Number
     * @body marital_status string required User's marital status
     * @body account_type string required Type of account (Savings/Current)
     * @body means_of_identification string required Means of identification
     * @body identification_no string required Identification number
     * @body next_of_kin_name string required Next of kin's name
     * @body next_of_kin_othernames string required Next of kin's other names
     * @body next_of_kin_address string required Next of kin's address
     * @body relationship string required Relationship with next of kin
     * @body staffID_get string required Staff ID (can be "Null")
     * @body sms_notification string required SMS notification preference (Yes/No)
     * @body email_notification string required Email notification preference (Yes/No)
     * @body office_address string required User's office address
     * @response 200 {
     *   "status": true,
     *   "message": "Registration successful",
     *   "data": {
     *     "user_id": "John123456",
     *     "password": "John123456",
     *     "user": {
     *       "id": 1,
     *       "user_id": "John123456",
     *       "name": "John Doe",
     *       "email": "john@example.com",
     *       "phone": "08012345678",
     *       "account_type": "Savings",
     *       "verification_status": "Unverified",
     *       "account_status": "Unverified"
     *     }
     *   }
     * }
     * @response 422 {
     *   "status": false,
     *   "message": "Validation failed",
     *   "errors": {
     *     "email": ["The email has already been taken."],
     *     "bvn_no": ["The bvn no has already been taken."]
     *   }
     * }
     *
     * @security ApiKeyAuth
     */
    public function register(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'lastname' => 'required|string',
                'othernames' => 'required|string',
                'gender' => 'required|string',
                'Nationality' => 'required|string',
                'birthday' => 'required|date',
                'occupation' => 'required|string',
                'phone' => 'required|string',
                'email' => 'required|email|unique:client_registrations,email',
                'residential_address' => 'required|string',
                'residential_state' => 'required|string',
                'residential_Local_govt' => 'required|string',
                'state_of_origin' => 'required|string',
                'local_govt_of_origin' => 'required|string',
                'town_of_origin' => 'required|string',
                'bvn_no' => 'required|string|unique:client_registrations,BVN',
                'marital_status' => 'required|string',
                'account_type' => 'required|string',
                'means_of_identification' => 'required|string',
                'identification_no' => 'required|string',
                'next_of_kin_name' => 'required|string',
                'next_of_kin_othernames' => 'required|string',
                'next_of_kin_address' => 'required|string',
                'relationship' => 'required|string',
                'staffID_get' => 'required|string',
                'sms_notification' => 'required|string',
                'email_notification' => 'required|string',
                'office_address' => 'required|string',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            // Generate user_id and password
            $number = rand(1, 900000000);
            $user_id = substr($request->othernames, 0, 5) . $number;
            $password = $user_id;

            // Additional fields from old code
            $birthday_remINDERS = date('m-d', strtotime($request->birthday));
            $dateRegistered = date('Y-m-d');
            $verification_status = "Unverified";
            $account_status = "Unverified";
            $account_officer = "No Account Officer";
            $Pin = "0000";
            $email_verified_at = null;
            $file_source = "PASSPORT";
            $form = "Register";

            $user = ClientRegistration::create([
                'user_id' => $user_id,
                'password' => Hash::make($password),
                'lastname' => $request->lastname,
                'othernames' => $request->othernames,
                'gender' => $request->gender,
                'Nationality' => $request->Nationality,
                'birthday' => $request->birthday,
                'occupation' => $request->occupation,
                'phone' => $request->phone,
                'email' => $request->email,
                'residential_address' => $request->residential_address,
                'residential_state' => $request->residential_state,
                'Residential_LGA' => $request->residential_Local_govt,
                'state_of_origin' => $request->state_of_origin,
                'LGA_of_origin' => $request->local_govt_of_origin,
                'town_of_origin' => $request->town_of_origin,
                'BVN' => $request->bvn_no,
                'marital_status' => $request->marital_status,
                'account_type' => $request->account_type,
                'means_of_identification' => $request->means_of_identification,
                'identification_no' => $request->identification_no,
                'staff_id' => $request->staffID_get,
                'account_officer' => $account_officer,
                'next_of_kin_name' => $request->next_of_kin_name,
                'next_of_kin_othernames' => $request->next_of_kin_othernames,
                'next_of_kin_address' => $request->next_of_kin_address,
                'Relationship_with_Next_of_kin' => $request->relationship,
                'sms_notification' => $request->sms_notification,
                'email_notification' => $request->email_notification,
                'birthday_reminder' => $birthday_remINDERS,
                'Regdate' => $dateRegistered,
                'verification_status' => $verification_status,
                'account_status' => $account_status,
                'Pin' => $Pin,
                'email_verified_at' => $email_verified_at,
                'office_address' => $request->office_address,
            ]);

            return $this->successResponse([
                'user_id' => $user_id,
                'password' => $password,
                'user' => new UserResource($user)
            ], 'Registration successful');

        } catch (\Exception $e) {
            Log::error('Registration error: ' . $e->getMessage());
            return $this->errorResponse('An error occurred during registration', 500);
        }
    }

    /**
     * Logout user
     * 
     * Revoke the current user's access token.
     * 
     * @header Authorization string required Bearer token
     * @response 200 {
     *   "status": true,
     *   "message": "Successfully logged out",
     *   "data": null
     * }
     *
     * @security ApiKeyAuth
     * @security SanctumAuth
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return $this->successResponse(null, 'Successfully logged out');
        } catch (\Exception $e) {
            Log::error('Logout error: ' . $e->getMessage());
            return $this->errorResponse('An error occurred during logout', 500);
        }
    }

    /**
     * Send password reset OTP
     * 
     * Send a one-time password to the user's registered phone/email for password reset.
     * 
     * @header x-api-key string required API key for authentication
     * @body userID string required User's unique identifier
     * @response 200 {
     *   "status": true,
     *   "message": "OTP sent successfully",
     *   "data": null
     * }
     * @response 422 {
     *   "status": false,
     *   "message": "Validation failed",
     *   "errors": {
     *     "userID": ["The selected userID is invalid."]
     *   }
     * }
     *
     * @security ApiKeyAuth
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'userID' => 'required|string|exists:client_registrations,user_id',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            $user = ClientRegistration::where('user_id', $request->userID)->first();
            $otp = rand(100000, 999999);

            $user->update([
                'one_time_pasword' => $otp
            ]);

            // TODO: Send OTP via SMS or email

            return $this->successResponse(null, 'OTP sent successfully');
        } catch (\Exception $e) {
            Log::error('Forgot password error: ' . $e->getMessage());
            return $this->errorResponse('An error occurred during password reset', 500);
        }
    }

    /**
     * Verify OTP for password reset
     * 
     * Verify the one-time password sent to the user for password reset.
     * 
     * @header x-api-key string required API key for authentication
     * @body userID string required User's unique identifier
     * @body otp string required 6-digit OTP code
     * @response 200 {
     *   "status": true,
     *   "message": "OTP verified successfully",
     *   "data": null
     * }
     * @response 401 {
     *   "status": false,
     *   "message": "Invalid OTP",
     *   "data": null
     * }
     *
     * @security ApiKeyAuth
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'userID' => 'required|string|exists:client_registrations,user_id',
                'otp' => 'required|string|size:6',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            $user = ClientRegistration::where('user_id', $request->userID)
                ->where('one_time_pasword', $request->otp)
                ->first();

            if (!$user) {
                return $this->errorResponse('Invalid OTP', 401);
            }

            return $this->successResponse(null, 'OTP verified successfully');
        } catch (\Exception $e) {
            Log::error('OTP verification error: ' . $e->getMessage());
            return $this->errorResponse('An error occurred during OTP verification', 500);
        }
    }

    /**
     * Reset password with OTP
     * 
     * Reset the user's password using the verified OTP.
     * 
     * @header x-api-key string required API key for authentication
     * @body userID string required User's unique identifier
     * @body otp string required 6-digit OTP code
     * @body password string required New password (minimum 6 characters)
     * @body password_confirmation string required Password confirmation
     * @response 200 {
     *   "status": true,
     *   "message": "Password reset successful",
     *   "data": null
     * }
     * @response 401 {
     *   "status": false,
     *   "message": "Invalid OTP",
     *   "data": null
     * }
     *
     * @security ApiKeyAuth
     */
    public function resetPassword(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'userID' => 'required|string|exists:client_registrations,user_id',
                'otp' => 'required|string|size:6',
                'password' => 'required|string|min:6|confirmed',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            $user = ClientRegistration::where('user_id', $request->userID)
                ->where('one_time_pasword', $request->otp)
                ->first();

            if (!$user) {
                return $this->errorResponse('Invalid OTP', 401);
            }

            $user->update([
                'password' => Hash::make($request->password),
                'one_time_pasword' => null
            ]);

            return $this->successResponse(null, 'Password reset successful');
        } catch (\Exception $e) {
            Log::error('Password reset error: ' . $e->getMessage());
            return $this->errorResponse('An error occurred during password reset', 500);
        }
    }

    /**
     * Get the rate limiting throttle key for the request.
     *
     * @param Request $request
     * @return string
     */
    private function throttleKey(Request $request): string
    {
        return mb_strtolower($request->input('userID')) . '|' . $request->ip();
    }
}
