<?php


namespace App\Services;

use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use App\Models\Account;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class CustomerService
{
    public function registerCustomer(array $data): array
    {
        try {
            // Check for existing phone
            if (Customer::where('phone', $data['phone'])->exists()) {
                return [
                    'status' => false,
                    'message' => 'Phone number already exists.',
                    'data' => null
                ];
            }
            // Check for existing email
            if (Customer::where('Email', $data['email'])->exists()) {
                return [
                    'status' => false,
                    'message' => 'Email already exists.',
                    'data' => null
                ];
            }
            // Check for existing BVN
            if (Customer::where('BVN', $data['bvn_no'])->exists()) {
                return [
                    'status' => false,
                    'message' => 'BVN already exists.',
                    'data' => null
                ];
            }
            // Hash password if provided
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }
            $customer = DB::transaction(function () use ($data) {
                return Customer::create([
                    'lastname' => $data['lastname'],
                    'othernames' => $data['othername'],
                    'gender' => $data['gender'],
                    'nationality' => $data['Nationality'],
                    'birthday' => $data['birthday'],
                    'occupation' => $data['occupation'],
                    'phone' => $data['phone'],
                    'Email' => $data['email'],
                    'Residential_Address' => $data['residential_address'],
                    'Residential_state' => $data['residential_state'],
                    'Residential_LGA' => $data['residential_Local_govt'],
                    'state_of_origin' => $data['state_of_origin'],
                    'LGA_of_origin' => $data['local_govt_of_origin'],
                    'Town_of_origin' => $data['town_of_origin'],
                    'BVN' => $data['bvn_no'],
                    'marital_status' => $data['marital_status'],
                    'account_type' => $data['account_type'],
                    'means_of_identification' => $data['means_of_identification'],
                    'identification_no' => $data['identification_no'],
                    'marketer' => $data['staffID_get'],
                    'staff_ID' => $data['staffID_get'],
                    'Next_of_kin_name' => $data['next_of_kin_name'],
                    'Next_of_kin_othernames' => $data['next_of_kin_othernames'],
                    'Next_of_kin_address' => $data['next_of_kin_address'],
                    'Relationship_with_Next_of_kin' => $data['relationship'],
                    'sms_notification' => $data['sms_notification'],
                    'email_notification' => $data['email_notification'],
                    'office_address' => $data['office_address'],
                    // Save hashed password if present
                    'password' => $data['password'] ?? null
                ]);
            });
            return [
                'status' => true,
                'message' => 'Customer registered successfully.',
                'data' => $customer->toArray()
            ];
        } catch (\Exception $e) {
            Log::error('Customer registration failed', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            return [
                'status' => false,
                'message' => 'Failed to register customer.',
                'data' => null
            ];
        }
    }


    public function getCustomerName(string $accountNo): array
    {
        try {
            $account = Account::with('user')
                ->where('account_no', $accountNo)
                ->first();
            if (!$account || !$account->user) {
                return [
                    'status' => false,
                    'message' => 'Customer not found.',
                    'data' => null
                ];
            }
            return [
                'status' => true,
                'message' => 'Customer name fetched successfully.',
                'data' => ['full_name' => $account->user->full_name]
            ];
        } catch (\Exception $e) {
            Log::error('Customer name fetch failed', [
                'error' => $e->getMessage(),
                'account_no' => $accountNo
            ]);
            return [
                'status' => false,
                'message' => 'Failed to fetch customer name.',
                'data' => null
            ];
        }
    }




}