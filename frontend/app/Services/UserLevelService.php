<?php

namespace App\Services;
use App\Models\UserLevel;
use App\Models\BiodataRef;
use App\Models\App;
use Illuminate\Support\Facades\Validator;

class UserLevelService extends Service
{
    protected $userLevelModel;
    protected $biodataRefModel;
    protected $appModel;

    public function __construct(UserLevel $userLevelModel, BiodataRef $biodataRefModel, App $appModel)
    {
        $this->userLevelModel = $userLevelModel;
        $this->biodataRefModel = $biodataRefModel;
        $this->appModel = $appModel;
    }

    // FORM
    public function getFormOptionsUserLevel()
    {
        $biodataRefData = $this->biodataRefModel->getBiodataRefStatusTrue();
        $appData = $this->appModel->getAppStatusTrue();

        return $this->responseJson([
            'status' => true,
            'data' => [
                'biodata_ref' => $biodataRefData,
                'app' => $appData,
            ],
            'message' => 'status ok',
        ], 200);
    }

    public function getDataUserLevelJson()
    {
        $no = 1;
        $data = [];
        $permissions = session('auth_menu_permissions');
        $userLevelData = $this->userLevelModel->getUserLevel();
        if(!$userLevelData->isEmpty()) {
            foreach ($userLevelData as $key => $value) {
                $status = $this->status_data($value->status);
                $btn = '';
                if($permissions['update']) {
                    $btn .= '<a href="javascript:void(0);" class="cursor-pointer btn-edit" data-url-show="'. route('data_master.user_level.view.id', ['idUserLevel' => $value->id]) .'" data-url-update="'. route('data_master.user_level.update', ['idUserLevel' => $value->id]) .'">Edit</i></a>';
                }
                if($permissions['delete']) {
                    $btn .= '<a href="javascript:void(0);" data-url-delete="'. route('data_master.user_level.delete', ['idUserLevel' => $value->id]) .'" class="cursor-pointer btn-delete ps-2">Hapus</a>';
                }

                $data[] = [
                    $no++,
                    $value->user_level_name,
                    $value->biodata_ref_name,
                    $value->app_name,
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

    public function getDataUserLevelJsonByIdUserLevel($idUserLevel)
    {
        try {
            $userLevelData = $this->userLevelModel->getUserLevelById($idUserLevel);
            if(!$userLevelData) {
                return $this->responseJson([
                    'status' => false,
                    'message' => 'Data tidak ditemukan',
                ], 404);
            }

            $data = [
                'id' => $userLevelData->id,
                'biodata_ref_id' => $userLevelData->biodata_ref_id,
                'app_id' => $userLevelData->app_id,
                'user_level_name' => $userLevelData->user_level_name,
                'status' => $userLevelData->status,
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
                // 'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function createDataUserLevel($request)
    {
        try {
            $biodataRefExist = $this->biodataRefModel->getBiodataRefStatusTrue();
            $appExist = $this->appModel->getAppStatusTrue();
            $userLevelNameUnique = $this->userLevelModel->getUserLevelNameUnique($request);
            $validator = Validator::make($request, [
                'biodata_ref_id' => ['required', 'in:'. $biodataRefExist->pluck('id')->implode(',')],
                'app_id' => ['required', 'in:' . $appExist->pluck('id')->implode(',')],
                'user_level_name' => ['required', 'regex:/^[^<>]+$/', 'max:255', function ($attribute, $value, $fail) use ($userLevelNameUnique) {
                    try {
                        if($userLevelNameUnique) $fail($attribute . ' sudah digunakan');
                    } catch (\Exception $e) {
                        $fail('terjadi kesalahan ' . $e->getMessage());
                    }

                }],
                'status' => ['required', 'in:1,0'],
            ], [
                'required' => ':attribute tidak boleh kosong.',
                'unique' => ':attribute telah dipakai.',
                'max' => ':attribute maksimal :max.',
                'in' => ':attribute tidak valid, hanya boleh yang tersedia.',
                'regex' => ':attribute mengandung karakter terlarang!'
            ]);


            // jika gagal validasi input kirim variabel messages, penanda bahwa messages berisi array
            if($validator->fails()) {
                return $this->responseJson([
                    'status' => false,
                    'messages' => $validator->errors()
                ], 422);
            }

            $request = $validator->validated();

            if($this->userLevelModel->insertUserLevel($request)) {
                return $this->responseJson([
                    'status' => true,
                    'message' => 'berhasil menambah data!',
                ], 200);
            } else {
                return $this->responseJson([
                    'status' => false,
                    'message' => 'gagal menambah data!',
                ], 500);
            }
        } catch (\Exception $e) {
            // Tangkap semua error seperti query gagal, koneksi error, dll
            return $this->responseJson([
                'status' => false,
                'message' => 'Terjadi kesalahan pada server.',
                // 'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateDataUserLevel($request, $idUserLevel)
    {
        try {
            $userLevelData = $this->userLevelModel->getUserLevelById($idUserLevel);
            // cek dulu apakah ada data yg mau di update
            if(!$userLevelData) {
                return $this->responseJson([
                    'status' => false,
                    'message' => 'Data tidak ditemukan',
                ], 404);
            }

            $biodataRefExist = $this->biodataRefModel->getBiodataRefStatusTrue($request['biodata_ref_id']);
            $appExist = $this->appModel->getAppStatusTrue($request['app_id']);
            $userLevelNameUnique = $this->userLevelModel->getUserLevelNameUnique($request, $idUserLevel);
            $validator = Validator::make($request, [
                'biodata_ref_id' => ['required', 'in:'. $biodataRefExist->pluck('id')->implode(',')],
                'app_id' => ['required', 'in:' . $appExist->pluck('id')->implode(',')],
                'user_level_name' => ['required', 'regex:/^[^<>]+$/', 'max:255', function ($attribute, $value, $fail) use ($userLevelNameUnique) {
                    try {
                        if($userLevelNameUnique) $fail($attribute . ' sudah digunakan');
                    } catch (\Exception $e) {
                        $fail('terjadi kesalahan ' . $e->getMessage());
                    }

                }],
                'status' => ['required', 'in:1,0'],
            ], [
                'required' => ':attribute tidak boleh kosong.',
                'unique' => ':attribute telah dipakai.',
                'max' => ':attribute maksimal :max.',
                'in' => ':attribute tidak valid, hanya boleh yang tersedia.',
                'regex' => ':attribute mengandung karakter terlarang!'
            ]);

            // jika gagal validasi input kirim variabel messages, penanda bahwa messages berisi array
            if($validator->fails()) {
                return $this->responseJson([
                    'status' => false,
                    'messages' => $validator->errors()
                ], 422);
            }

            $request = $validator->validated();

            if($this->userLevelModel->updateUserLevel($request, $idUserLevel)) {
                return $this->responseJson([
                    'status' => true,
                    'message' => 'berhasil mengubah data!',
                ], 200);
            } else {
                return $this->responseJson([
                    'status' => false,
                    'message' => 'gagal mengubah data!',
                ], 500);
            }
        } catch (\Exception $e) {
            // Tangkap semua error seperti query gagal, koneksi error, dll
            return $this->responseJson([
                'status' => false,
                'message' => 'Terjadi kesalahan pada server.',
                // 'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function deleteDataUserLevel($idUserLevel)
    {
        try {
            if($this->userLevelModel->deleteUserLevel($idUserLevel)) {
                return $this->responseJson([
                    'status' => true,
                    'message' => 'berhasil menghapus data!',
                ], 200);
            } else {
                return $this->responseJson([
                    'status' => false,
                    'message' => 'gagal menghapus data!',
                ], 500);
            }
        } catch (\Exception $e) {
            // Tangkap semua error seperti query gagal, koneksi error, dll
            return $this->responseJson([
                'status' => false,
                'message' => 'Terjadi kesalahan pada server.',
                // 'error' => $e->getMessage(),
            ], 500);
        }

    }

}
