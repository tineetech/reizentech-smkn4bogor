<?php

namespace App\Services;
use App\Models\Menu;
use Illuminate\Support\Facades\Validator;

class MainMenuService extends Service
{
    protected $menuModel;

    public function __construct(Menu $menuModel)
    {
        $this->menuModel = $menuModel;
    }

    public function getDataMainMenuJson()
    {
        $no = 1;
        $data = [];
        $permissions = session('auth_menu_permissions');
        $menuData = $this->menuModel->getMenu();
        if(!$menuData->isEmpty()) {
            foreach ($menuData as $key => $value) {
                $status = $this->status_data($value->status);
                $btn = '';
                if($permissions['update']) {
                    $btn .= '<a href="javascript:void(0);" class="cursor-pointer btn-edit" data-url-show="'. route('data_master.main_menu.view.id', ['idMainMenu' => $value->id]) .'" data-url-update="'. route('data_master.main_menu.update', ['idMainMenu' => $value->id]) .'">Edit</i></a>';
                }
                if($permissions['delete']) {
                    $btn .= '<a href="javascript:void(0);" data-url-delete="'. route('data_master.main_menu.delete', ['idMainMenu' => $value->id]) .'" class="cursor-pointer btn-delete ps-2">Hapus</a>';
                }

                $data[] = [
                    $no++,
                    $value->menu_code,
                    $value->menu_name,
                    $value->menu_type,
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

    public function getDataMainMenuJsonByIdMainMenu($idMainMenu)
    {
        try {
            $menuData = $this->menuModel->getMenuById($idMainMenu);
            if(!$menuData) {
                return $this->responseJson([
                    'status' => false,
                    'message' => 'Data tidak ditemukan',
                ], 404);
            }

            $data = [
                'id' => $menuData->id,
                'menu_code' => $menuData->menu_code,
                'menu_name' => $menuData->menu_name,
                'menu_type' => $menuData->menu_type,
                'status' => $menuData->status,
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


    public function createDataMainMenu($request)
    {
        try {
            $validator = Validator::make($request, [
                'menu_code' => ['required', 'regex:/^[^<>]+$/', 'unique:menu,menu_code', 'max:255'],
                'menu_name' => ['required', 'regex:/^[^<>]+$/', 'max:255'],
                'menu_type' => ['required', 'in:main,sub'],
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

            if($this->menuModel->insertMenu($request)) {
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

    public function updateDataMainMenu($request, $idMainMenu)
    {
        try {
            $menuData = $this->menuModel->getMenuById($idMainMenu);
            // cek dulu apakah ada data yg mau di update
            if(!$menuData) {
                return $this->responseJson([
                    'status' => false,
                    'message' => 'Data tidak ditemukan',
                ], 404);
            }

            $validator = Validator::make($request, [
                'menu_code' => ['required', 'regex:/^[^<>]+$/', 'unique:menu,menu_code,' . $menuData->id, 'max:255'],
                'menu_name' => ['required', 'regex:/^[^<>]+$/', 'max:255'],
                'menu_type' => ['required', 'in:main,sub'],
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

            if($this->menuModel->updateMenu($request, $idMainMenu)) {
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

    public function deleteDataMainMenu($idMainMenu)
    {
        try {
            if($this->menuModel->deleteMenu($idMainMenu)) {
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
