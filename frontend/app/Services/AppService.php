<?php

namespace App\Services;
use App\Models\App;
use Illuminate\Support\Facades\Validator;

class AppService extends Service
{
    protected $appModel;

    public function __construct(App $appModel)
    {
        $this->appModel = $appModel;
    }

    public function getDataAppJson()
    {
        $no = 1;
        $data = [];
        $permissions = session('auth_menu_permissions');
        $appData = $this->appModel->getApp();
        if(!$appData->isEmpty()) {
            foreach ($appData as $key => $value) {
                $status = $this->status_data($value->status);
                $btn = '';
                if($permissions['update']) {
                    $btn .= '<a href="javascript:void(0);" class="cursor-pointer btn-edit" data-url-show="'. route('data_master.app.view.id', ['idApp' => $value->id]) .'" data-url-update="'. route('data_master.app.update', ['idApp' => $value->id]) .'">Edit</i></a>';
                }
                if($permissions['delete']) {
                    $btn .= '<a href="javascript:void(0);" data-url-delete="'. route('data_master.app.delete', ['idApp' => $value->id]) .'" class="cursor-pointer btn-delete ps-2">Hapus</a>';
                }

                $data[] = [
                    $no++,
                    $value->app_name,
                    $value->desc,
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

    public function getDataAppJsonByIdApp($idApp)
    {
        try {
            $appData = $this->appModel->getAppById($idApp);
            if(!$appData) {
                return $this->responseJson([
                    'status' => false,
                    'message' => 'Data tidak ditemukan',
                ], 404);
            }

            $data = [
                'id' => $appData->id,
                'app_name' => $appData->app_name,
                'desc' => $appData->desc,
                'status' => $appData->status,
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


    public function createDataApp($request)
    {
        try {
            $validator = Validator::make($request, [
                'app_name' => ['required', 'regex:/^[^<>]+$/', 'unique:app,app_name', 'max:255'],
                'desc' => ['required', 'regex:/^[^<>]+$/', 'max:255'],
                'status' => ['required', 'in:1,0'],
            ], [
                'required' => ':attribute tidak boleh kosong.',
                'unique' => ':attribute telah dipakai.',
                'max' => ':attribute maksimal :max.',
                'in' => ':attribute tidak valid, hanya boleh :values.',
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

            if($this->appModel->insertApp($request)) {
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

    public function updateDataApp($request, $idApp)
    {
        try {
            $appData = $this->appModel->getAppById($idApp);
            // cek dulu apakah ada data yg mau di update
            if(!$appData) {
                return $this->responseJson([
                    'status' => false,
                    'message' => 'Data tidak ditemukan',
                ], 404);
            }

            $validator = Validator::make($request, [
                'app_name' => ['required', 'regex:/^[^<>]+$/', 'unique:app,app_name,' . $appData->id, 'max:255'],
                'desc' => ['required', 'regex:/^[^<>]+$/', 'max:255'],
                'status' => ['required', 'in:1,0'],
            ], [
                'required' => ':attribute tidak boleh kosong.',
                'unique' => ':attribute telah dipakai.',
                'max' => ':attribute maksimal :max.',
                'in' => ':attribute tidak valid, hanya boleh :values.',
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

            if($this->appModel->updateApp($request, $idApp)) {
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

    public function deleteDataApp($idApp)
    {
        try {
            if($this->appModel->deleteApp($idApp)) {
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
