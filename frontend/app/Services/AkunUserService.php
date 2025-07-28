<?php

namespace App\Services;
use App\Models\User;
use App\Models\UserRole;
use App\Models\App;
use App\Models\UserLevel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AkunUserService extends Service
{
    protected $usersModel;
    protected $userRoleModel;
    protected $appModel;
    protected $userLevelModel;

    public function __construct(User $usersModel, UserRole $userRoleModel, App $appModel, UserLevel $userLevelModel)
    {
        $this->usersModel = $usersModel;
        $this->userRoleModel = $userRoleModel;
        $this->appModel = $appModel;
        $this->userLevelModel = $userLevelModel;
    }

    // OPTIONS
    public function getFormOptionApp()
    {
        return  $this->appModel->getAppStatusTrue();
    }

    public function getFormOptionUserLevel($idApp, $idUserLevel = false)
    {
        return $this->userLevelModel->getUserLevelStatusTrue($idApp, $idUserLevel);
    }

    // AKUN USER

    public function getDataAkunUserJson()
    {
        $no = 1;
        $data = [];
        $permissions = session('auth_menu_permissions');
        $akunUserData = $this->usersModel->getUsers();
        if(!$akunUserData->isEmpty()) {
            foreach ($akunUserData as $key => $value) {
                $status = $this->status_akun($value->status);
                $btn = '';
                if($permissions['update']) {
                    $btn .= '<a href="javascript:void(0);" class="cursor-pointer btn-edit" data-url-show="'. route('data_master.users.akun_user.view.id', ['idUser' => $value->id]) .'" data-url-update="'. route('data_master.users.akun_user.update', ['idUser' => $value->id]) .'" data-url-role="'. route('data_master.users.user_role.json_data', ['idUser' => $value->id]) .'">Edit</i></a>';
                }
                if($permissions['delete']) {
                    $btn .= '<a href="javascript:void(0);" data-url-delete="'. route('data_master.users.akun_user.delete', ['idUser' => $value->id]) .'" class="cursor-pointer btn-delete ps-2">Hapus</a>';
                }

                $data[] = [
                    $no++,
                    $value->username,
                    $value->email,
                    $value->phone_number,
                    $value->roles,
                    $status,
                    $btn
                ];
            }
        }

        return $this->responseJson([
            'status' => true,
            'data' => $data,
            'message' => 'status ok',
        ], 200);
    }

    public function getDataAkunUserJsonByIdUser($idUser)
    {
        try {
            $akunUserData = $this->usersModel->getUsersById($idUser);
            if(!$akunUserData) {
                return $this->responseJson([
                    'status' => false,
                    'message' => 'Data tidak ditemukan',
                ], 404);
            }

            $data = [
                'id' => $akunUserData->id,
                'username' => $akunUserData->username,
                'email' => $akunUserData->email,
                'phone_number' => $akunUserData->phone_number,
                'status' => $akunUserData->status,
            ];

            return $this->responseJson([
                'status' => true,
                'data' => $data,
                'message' => 'status ok',
            ], 200);
        } catch (\Exception $e) {
            // Tangkap semua error seperti query gagal, koneksi error, dll
            return $this->responseJson([
                'status' => false,
                'message' => 'Terjadi kesalahan pada server.',
                // //'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function createDataAkunUser($request)
    {
        try {

            $rules = [
                'username' => ['required', 'regex:/^[^<>]+$/', 'min:3', 'max:255', 'unique:users,username'],
                'email' => ['nullable', 'email', 'regex:/^[^<>]+$/', 'max:255', 'unique:users,email'],
                'phone_number' => ['nullable', 'regex:/^\d+$/', 'max:255', 'unique:users,phone_number'],
                'password' => ['required', 'regex:/^[^<>]+$/', 'min:8', 'max:255'],
                'status' => ['required', 'in:active,non-activate,banned'],
            ];

            $message = [
                'required' => ':attribute tidak boleh kosong.',
                'email' => 'format :attribute tidak valid.',
                'phone_number.regex' => 'format :attribute tidak valid hanya menerima angka.',
                'unique' => ':attribute telah dipakai.',
                'min' => ':attribute minimal :min.',
                'max' => ':attribute maksimal :max.',
                'in' => ':attribute tidak valid, hanya boleh yang tersedia.',
                'regex' => ':attribute mengandung karakter terlarang!'
            ];

            $validator = Validator::make($request, $rules, $message);


            // jika gagal validasi input kirim variabel messages, penanda bahwa messages berisi array
            if($validator->fails()) {
                return $this->responseJson([
                    'status' => false,
                    'messages' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();
            $validated['password'] = Hash::make($validated['password'] . env('APP_SECRET_KEY_HASH'));
            // Ambil field yang nullable
            $nullableFields = collect($rules)
                ->filter(fn($r) => in_array('nullable', $r))
                ->keys()
                ->all();

            // Hapus field nullable jika kosong
            $request = array_filter($validated, function ($value, $key) use ($nullableFields) {
                return !in_array($key, $nullableFields) || ($value !== null && $value !== '');
            }, ARRAY_FILTER_USE_BOTH);

            $this->usersModel->insertUsers($request);
            return $this->responseJson([
                'status' => true,
                'message' => 'berhasil menambah data!',
            ], 200);
        } catch (\Exception $e) {
            // Tangkap semua error seperti query gagal, koneksi error, dll
            return $this->responseJson([
                'status' => false,
                'message' => 'gagal menambah data!',
                // //'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateDataAkunUser($request, $idUser)
    {
        try {
            $akunUserData = $this->usersModel->getUsersById($idUser);

            $rules = [
                'username' => ['required', 'regex:/^[^<>]+$/', 'min:3', 'max:255', 'unique:users,username,' . $akunUserData->id],
                'email' => ['nullable', 'email', 'regex:/^[^<>]+$/', 'max:255', 'unique:users,email,' . $akunUserData->id],
                'phone_number' => ['nullable', 'regex:/^\d+$/', 'max:255', 'unique:users,phone_number,' . $akunUserData->id],
                'password' => ['nullable', 'regex:/^[^<>]+$/', 'min:8', 'max:255'],
                'status' => ['required', 'in:active,non-activate,banned'],
            ];

            $message = [
                'required' => ':attribute tidak boleh kosong.',
                'email' => 'format :attribute tidak valid.',
                'phone_number.regex' => 'format :attribute tidak valid hanya menerima angka.',
                'unique' => ':attribute telah dipakai.',
                'min' => ':attribute minimal :min.',
                'max' => ':attribute maksimal :max.',
                'in' => ':attribute tidak valid, hanya boleh yang tersedia.',
                'regex' => ':attribute mengandung karakter terlarang!'
            ];

            $validator = Validator::make($request, $rules, $message);

            // jika gagal validasi input kirim variabel messages, penanda bahwa messages berisi array
            if($validator->fails()) {
                return $this->responseJson([
                    'status' => false,
                    'messages' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();
            if($validated['password'] != '') {
                $validated['password'] = Hash::make($validated['password'] . env('APP_SECRET_KEY_HASH'));
            }
            // Ambil field yang nullable
            $nullableFields = collect($rules)
                ->filter(fn($r) => in_array('nullable', $r))
                ->keys()
                ->all();

            // Hapus field nullable jika kosong
            $request = array_filter($validated, function ($value, $key) use ($nullableFields) {
                return !in_array($key, $nullableFields) || ($value !== null && $value !== '');
            }, ARRAY_FILTER_USE_BOTH);

            $this->usersModel->updateUsers($request, $idUser);
            return $this->responseJson([
                'status' => true,
                'message' => 'berhasil mengubah data!',
            ], 200);
        } catch (\Exception $e) {
            // Tangkap semua error seperti query gagal, koneksi error, dll
            return $this->responseJson([
                'status' => false,
                'message' => 'gagal mengubah data!',
                //'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function deleteDataAkunUser($idUser)
    {
        try {
            $this->usersModel->deleteUsers($idUser);
                return $this->responseJson([
                    'status' => true,
                    'message' => 'berhasil menghapus data!',
                ], 200);
        } catch (\Exception $e) {
            // Tangkap semua error seperti query gagal, koneksi error, dll
            return $this->responseJson([
                'status' => false,
                'message' => 'gagal menghapus data.',
                // //'error' => $e->getMessage(),
            ], 500);
        }

    }

    // USER ROLE

    public function getDataUserRoleJson($idUser)
    {
        $no = 1;
        $data = [];
        $permissions = session('auth_menu_permissions');
        $userRoleData = $this->userRoleModel->getUserRoleByIdUser($idUser);
        if(!$userRoleData->isEmpty()) {
            foreach ($userRoleData as $key => $value) {
                $status = $this->status_data($value->status);
                $btn = '';
                if($permissions['update']) {
                    $btn .= '<a href="javascript:void(0);" class="cursor-pointer btn-edit" data-url-show="'. route('data_master.users.user_role.view.id', ['idUserRole' => $value->id]) .'" data-url-update="'. route('data_master.users.user_role.update', ['idApp' => $value->app_id, 'idUserRole' => $value->id]) .'">Edit</i></a>';
                }
                if($permissions['delete']) {
                    $btn .= '<a href="javascript:void(0);" data-url-delete="'. route('data_master.users.user_role.delete', ['idUserRole' => $value->id]) .'" class="cursor-pointer btn-delete ps-2">Hapus</a>';
                }

                $data[] = [
                    $no++,
                    $value->app_name,
                    $value->biodata_ref_name,
                    $value->user_level_name,
                    $status,
                    $btn
                ];
            }
        }

        return $this->responseJson([
            'status' => true,
            'data' => $data,
            'message' => 'status ok',
        ], 200);
    }

    public function getDataUserRoleJsonByIdUserRole($idUserRole)
    {
        try {
            $userRoleData = $this->userRoleModel->getUserRoleById($idUserRole);
            if(!$userRoleData) {
                return $this->responseJson([
                    'status' => false,
                    'message' => 'Data tidak ditemukan',
                ], 404);
            }

            $data = [
                'id' => $userRoleData->id,
                'user_id' => $userRoleData->user_id,
                'user_level_id' => $userRoleData->user_level_id,
                'status' => $userRoleData->status,
            ];

            return $this->responseJson([
                'status' => true,
                'data' => $data,
                'message' => 'status ok',
            ], 200);
        } catch (\Exception $e) {
            // Tangkap semua error seperti query gagal, koneksi error, dll
            return $this->responseJson([
                'status' => false,
                'message' => 'Terjadi kesalahan pada server.',
                // //'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function createDataUserRole($request, $idApp, $idUser)
    {
        try {
            $request['user_id'] = $idUser;
            $userLevelExist = $this->userLevelModel->getUserLevelStatusTrue($idApp);
            $userRoleUnique = $this->userRoleModel->getUserRoleUnique($request);
            $rules = [
                'user_level_id' => ['required', 'in:'. $userLevelExist->pluck('id')->implode(','), function ($attribute, $value, $fail) use ($userRoleUnique) {
                    try {
                        if($userRoleUnique) $fail($attribute . ' sudah digunakan');
                    } catch (\Exception $e) {
                        $fail('terjadi kesalahan ' . $e->getMessage());
                    }

                }],
                'status' => ['required', 'in:1,0'],
            ];

            $message = [
                'required' => ':attribute tidak boleh kosong.',
                'in' => ':attribute tidak valid, hanya boleh yang tersedia.',
            ];

            $validator = Validator::make($request, $rules, $message);


            // jika gagal validasi input kirim variabel messages, penanda bahwa messages berisi array
            if($validator->fails()) {
                return $this->responseJson([
                    'status' => false,
                    'messages' => $validator->errors()
                ], 422);
            }

            $request = $validator->validated();
            $request['user_id'] = $idUser;

            $this->userRoleModel->insertUserRole($request);
            return $this->responseJson([
                'status' => true,
                'message' => 'berhasil menambah data!',
            ], 200);
        } catch (\Exception $e) {
            // Tangkap semua error seperti query gagal, koneksi error, dll
            return $this->responseJson([
                'status' => false,
                'message' => 'gagal menambah data!',
                // //'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateDataUserRole($request, $idApp, $idUserRole)
    {
        try {
            $userRoleData = $this->userRoleModel->getUserRoleById($idUserRole);
            $request['user_id'] = $userRoleData->user_id;
            $userLevelExist = $this->userLevelModel->getUserLevelStatusTrue($idApp, $userRoleData->user_level_id);
            $userRoleUnique = $this->userRoleModel->getUserRoleUnique($request, $idUserRole);
            $rules = [
                'user_level_id' => ['required', 'in:'. $userLevelExist->pluck('id')->implode(','), function ($attribute, $value, $fail) use ($userRoleUnique) {
                    try {
                        if($userRoleUnique) $fail($attribute . ' sudah digunakan');
                    } catch (\Exception $e) {
                        $fail('terjadi kesalahan ' . $e->getMessage());
                    }

                }],
                'status' => ['required', 'in:1,0'],
            ];

            $message = [
                'required' => ':attribute tidak boleh kosong.',
                'in' => ':attribute tidak valid, hanya boleh yang tersedia.',
            ];

            $validator = Validator::make($request, $rules, $message);

            // jika gagal validasi input kirim variabel messages, penanda bahwa messages berisi array
            if($validator->fails()) {
                return $this->responseJson([
                    'status' => false,
                    'messages' => $validator->errors()
                ], 422);
            }

            $request = $validator->validated();

            $this->userRoleModel->updateUserRole($request, $idUserRole);
            return $this->responseJson([
                'status' => true,
                'message' => 'berhasil mengubah data!',
            ], 200);
        } catch (\Exception $e) {
            // Tangkap semua error seperti query gagal, koneksi error, dll
            return $this->responseJson([
                'status' => false,
                'message' => 'gagal mengubah data!',
                //'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function deleteDataUserRole($idUserRole)
    {
        try {
            $this->userRoleModel->deleteUserRole($idUserRole);
            return $this->responseJson([
                'status' => true,
                'message' => 'berhasil menghapus data!',
            ], 200);
        } catch (\Exception $e) {
            // Tangkap semua error seperti query gagal, koneksi error, dll
            return $this->responseJson([
                'status' => false,
                'message' => 'gagal menghapus data!',
                // //'error' => $e->getMessage(),
            ], 500);
        }

    }
}
