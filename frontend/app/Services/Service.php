<?php

namespace App\Services;
use Illuminate\Support\Facades\Hash;

class Service
{
    public function __construct()
    {
    }

    protected function appName()
    {
        return config('app.name');
    }

    protected function secretKeyHash()
    {
        return config('app.secret_key_hash');
    }

    protected function hashPassword($password)
    {

        return Hash::make($password . $this->secretKeyHash());
    }

    protected function checkPassword($plainPassword, $hashedPassword)
    {
        return Hash::check($plainPassword . $this->secretKeyHash(), $hashedPassword);
    }

    protected function status_data($value) {
        $status = '<span class="badge bg-danger me-1"></span>Tidak Aktif';
        if($value == true) $status = '<span class="badge bg-success me-1"></span>Aktif';
        return $status;
    }

    protected function status_akun($value) {
        $status = '<span class="badge bg-success me-1"></span>Aktif';
        if($value == 'non-activate') {
            $status = '<span class="badge bg-danger me-1"></span>Tidak Aktif';
        } else if($value == 'banned') {
            $status = '<span class="badge bg-warning me-1"></span>Akun di Ban';
        }
        return $status;
    }

    public function responseJson($response = [], $code = 200)
    {
        return response()->json($response, $code, [
            'Content-Type' => 'application/json; charset=utf-8',
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
