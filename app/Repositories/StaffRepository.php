<?php

namespace App\Repositories;

use App\Models\Staff;
use Illuminate\Support\Facades\Hash;

class StaffRepository
{
    protected $model;

    public function __construct(Staff $staff)
    {
        $this->model = $staff;
    }

    public function findByCredentials(string $staffID, string $password): ?Staff
    {
        $staff = $this->model->where('staffID', $staffID)->first();

        if (!$staff || !Hash::check($password, $staff->password)) {
            return null;
        }

        return $staff;
    }
}