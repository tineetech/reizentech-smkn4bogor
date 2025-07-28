<?php

namespace App\Services;
use App\Models\MenuRole;
use App\Models\Menu;
use App\Models\App;
use App\Models\UserLevel;
use Illuminate\Support\Facades\Validator;

class UserBiodataService extends Service
{
    protected $menuRoleModel;
    protected $menuModel;
    protected $appModel;
    protected $userLevelModel;

    public function __construct(MenuRole $menuRoleModel, Menu $menuModel, App $appModel, UserLevel $userLevelModel)
    {
        $this->menuRoleModel = $menuRoleModel;
        $this->menuModel = $menuModel;
        $this->appModel = $appModel;
        $this->userLevelModel = $userLevelModel;
    }

    // FORM
    public function getFormOptionsRoleMenu()
    {
        $menuData = $this->menuModel->getMenuStatusTrue();
        $parentMenuData = $this->menuModel->getParentMenuStatusTrue();


        return $this->responseJson([
            'status' => true,
            'data' => [
                'menu' => $menuData,
                'parent_menu' => $parentMenuData,
            ],
            'message' => 'status ok',
        ], 200);
    }

    public function getFormOptionApp()
    {
        $appData = $this->appModel->getAppStatusTrue();
        return  $appData;
    }

    public function getDataAppByIdApp($idApp)
    {
        try {
            $appData = $this->appModel->getAppById($idApp);
            return $appData;
        } catch (\Exception $e) {
           abort(500, 'Terjadi kesalahan pada server.');
        }
    }

    public function getDataUserLevelByIdAppId($idApp, $idUserLevel)
    {
        try {
            $userLevelData = $this->userLevelModel->getUserLevelByIdAppId($idApp, $idUserLevel);
            return $userLevelData;
        } catch (\Exception $e) {
           abort(500, 'Terjadi kesalahan pada server.');
        }
    }

    public function getFormOptionUserLevel($idApp, $idUserLevel = false)
    {
        $userLevelData = $this->userLevelModel->getUserLevelStatusTrue($idApp, $idUserLevel);

        return $userLevelData;
    }

    private function getDataRoleMenuByIdUserLevel($idUserLevel)
    {
        $userMenuRole = $this->menuRoleModel->getMainMenuRoleByUserLevelId($idUserLevel);
        if($userMenuRole->isEmpty()) return "";
        $data = [];
        foreach($userMenuRole as $menu) {
            $data[] = (object) [
                'id' => $menu->id,
                'menu_code' => $menu->menu_code,
                'menu_name' => $menu->menu_name,
                'menu_type' => $menu->menu_type,
                'parent_menu_code' => $menu->parent_menu_code,
                'menu_icon' => $menu->menu_icon,
                'menu_url' => $menu->menu_url,
                'role_view' => $menu->role_view,
                'role_create' => $menu->role_create,
                'role_update' => $menu->role_update,
                'role_delete' => $menu->role_delete,
                'role_order' => $menu->role_order,
                'status' => $menu->status,
                'sub_menu' => $this->getDataRoleSubMenuByIdUserLevel($idUserLevel, $menu->menu_code),
            ];
        }
        return (object) $data;
    }

    private function getDataRoleSubMenuByIdUserLevel($idUserLevel, $parentMenuCode)
    {
        if(is_null($parentMenuCode)) return "";
        $userMenuRole = $this->menuRoleModel->getSubMenuRoleByUserLevelIdMenuCode($idUserLevel, $parentMenuCode);
        if($userMenuRole->isEmpty()) return "";
        $data = [];
        foreach($userMenuRole as $menu) {
            $data[] = (object) [
                'id' => $menu->id,
                'menu_code' => $menu->menu_code,
                'menu_name' => $menu->menu_name,
                'menu_type' => $menu->menu_type,
                'parent_menu_code' => $menu->parent_menu_code,
                'menu_icon' => $menu->menu_icon,
                'menu_url' => $menu->menu_url,
                'role_view' => $menu->role_view,
                'role_create' => $menu->role_create,
                'role_update' => $menu->role_update,
                'role_delete' => $menu->role_delete,
                'role_order' => $menu->role_order,
                'status' => $menu->status,
                'sub_menu' => $this->getDataRoleSubMenuByIdUserLevel($idUserLevel, $menu->menu_code),
            ];
        }
        return (object) $data;
    }

    private function getDataRoleSubMenu($idUserLevel, $subMenu, $parentOrder)
    {
        $data = [];
        $permissions = session('auth_menu_permissions');
        foreach($subMenu as $key => $value) {
            $status = $this->status_data($value->status);
                $btn = '';
                if($permissions['update']) {
                    $btn .= '<a href="javascript:void(0);" class="cursor-pointer btn-edit" data-url-show="'. route('data_master.role_menu.view.id', ['idUserLevel' => $idUserLevel, 'idRoleMenu' => $value->id]) .'" data-url-update="'. route('data_master.role_menu.update', ['idUserLevel' => $idUserLevel, 'idRoleMenu' => $value->id]) .'">Edit</i></a>';
                }
                if($permissions['delete']) {
                    $btn .= '<a href="javascript:void(0);" data-url-delete="'. route('data_master.role_menu.delete', ['idUserLevel' => $idUserLevel, 'idRoleMenu' => $value->id]) .'" class="cursor-pointer btn-delete ps-2">Hapus</a>';
                }

                $row = [
                    $parentOrder . '.' .$value->role_order,
                    $value->menu_code,
                    $value->menu_name,
                    $value->menu_type,
                    $value->parent_menu_code,
                    $value->menu_icon,
                    $value->menu_url,
                    $value->role_view,
                    $value->role_create,
                    $value->role_update,
                    $value->role_delete,
                    $status,
                    $btn
                ];

                $data[] = $row;
                if($value->sub_menu != '') {
                    $data_sub = $this->getDataRoleSubMenu($idUserLevel, $value->sub_menu, $parentOrder . '.'.$value->role_order);
                    // Gabungkan submenu ke data utama
                    $data = array_merge($data, $data_sub);
                }
        }
        return $data;
    }

    public function getDataRoleMenuJson($idUserLevel)
    {
        $data = [];
        $permissions = session('auth_menu_permissions');
        $roleMenuData = $this->getDataRoleMenuByIdUserLevel($idUserLevel);
        if($roleMenuData) {
            foreach ($roleMenuData as $key => $value) {
                $status = $this->status_data($value->status);
                $btn = '';
                if($permissions['update']) {
                    $btn .= '<a href="javascript:void(0);" class="cursor-pointer btn-edit" data-url-show="'. route('data_master.role_menu.view.id', ['idUserLevel' => $idUserLevel, 'idRoleMenu' => $value->id]) .'" data-url-update="'. route('data_master.role_menu.update', ['idUserLevel' => $idUserLevel, 'idRoleMenu' => $value->id]) .'">Edit</i></a>';
                }
                if($permissions['delete']) {
                    $btn .= '<a href="javascript:void(0);" data-url-delete="'. route('data_master.role_menu.delete', ['idUserLevel' => $idUserLevel, 'idRoleMenu' => $value->id]) .'" class="cursor-pointer btn-delete ps-2">Hapus</a>';
                }

                $row = [
                    $value->role_order,
                    $value->menu_code,
                    $value->menu_name,
                    $value->menu_type,
                    $value->parent_menu_code,
                    $value->menu_icon,
                    $value->menu_url,
                    $value->role_view,
                    $value->role_create,
                    $value->role_update,
                    $value->role_delete,
                    $status,
                    $btn
                ];

                $data[] = $row;
                if($value->sub_menu != '') {
                    $data_sub = $this->getDataRoleSubMenu($idUserLevel, $value->sub_menu, $value->role_order);
                    // Gabungkan submenu ke data utama
                    $data = array_merge($data, $data_sub);
                }
            }
        }

        return $this->responseJson([
            'status' => true,
            'data' => $data,
            'message' => 'status ok',
        ], 200);
    }

    public function getDataRoleMenuJsonByIdRoleMenu($idUserLevel, $idRoleMenu)
    {
        try {
            $roleMenuData = $this->menuRoleModel->getMenuRoleById($idUserLevel, $idRoleMenu);
            if(!$roleMenuData) {
                return $this->responseJson([
                    'status' => false,
                    'message' => 'Data tidak ditemukan',
                ], 404);
            }

            $data = [
                'id' => $roleMenuData->id,
                'menu_id' => $roleMenuData->menu_id,
                'user_level_id' => $roleMenuData->user_level_id,
                'parent_menu_code' => $roleMenuData->parent_menu_code,
                'menu_icon' => $roleMenuData->menu_icon,
                'menu_url' => $roleMenuData->menu_url,
                'role_view' => $roleMenuData->role_view,
                'role_create' => $roleMenuData->role_create,
                'role_update' => $roleMenuData->role_update,
                'role_delete' => $roleMenuData->role_delete,
                'role_order' => $roleMenuData->role_order,
                'status' => $roleMenuData->status,
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
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function createRoleMenu($request, $idUserLevel)
    {
        try {
            $menuExist = $this->menuModel->getMenuStatusTrue();
            $parentMenuExist = $this->menuModel->getParentMenuStatusTrue();
            $request['user_level_id'] = $idUserLevel; // masukan nilai sementara, karena dia tidak di validasi
            $menuRoleUnique = $this->menuRoleModel->getMenuRoleUnique($request);
            $validator = Validator::make($request, [
                'menu_id' => ['required', 'in:'. $menuExist->pluck('id')->implode(','), function ($attribute, $value, $fail) use ($menuRoleUnique) {
                    try {
                        if($menuRoleUnique) $fail($attribute . ' sudah digunakan');
                    } catch (\Exception $e) {
                        $fail('terjadi kesalahan ' . $e->getMessage());
                    }

                }],
                'parent_menu_code' => ['nullable', 'in:'. $parentMenuExist->pluck('menu_code')->implode(',')],
                'menu_icon' => ['nullable', 'max:255'],
                'menu_url' => ['nullable', 'regex:/^[^<>]+$/', 'max:255'],
                'role_view' => ['required', 'in:1,0'],
                'role_create' => ['required', 'in:1,0'],
                'role_update' => ['required', 'in:1,0'],
                'role_delete' => ['required', 'in:1,0'],
                'role_order' => ['required', 'integer', 'between:1,100'],
                'status' => ['required', 'in:1,0'],
            ], [
                'required' => ':attribute tidak boleh kosong.',
                'unique' => ':attribute telah dipakai.',
                'max' => ':attribute maksimal :max.',
                'between' => ':attribute hanya boleh dari :min hingga :max.',
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

            $request['user_level_id'] = $idUserLevel;

            if($this->menuRoleModel->insertMenuRole($request)) {
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

    public function updateRoleMenu($request, $idUserLevel, $idRoleMenu)
    {
        try {
            $roleMenuData = $this->menuRoleModel->getMenuRoleById($idUserLevel, $idRoleMenu);
            if(!$roleMenuData) {
                return $this->responseJson([
                    'status' => false,
                    'message' => 'Data tidak ditemukan',
                ], 404);
            }

            $menuExist = $this->menuModel->getMenuStatusTrue($request['menu_id']);
            $parentMenuExist = $this->menuModel->getParentMenuStatusTrue($request['parent_menu_code']);
            $request['user_level_id'] = $roleMenuData->user_level_id;
            $menuRoleUnique = $this->menuRoleModel->getMenuRoleUnique($request, $idRoleMenu);
            $validator = Validator::make($request, [
                'menu_id' => ['required', 'in:'. $menuExist->pluck('id')->implode(','), function ($attribute, $value, $fail) use ($menuRoleUnique) {
                    try {
                        if($menuRoleUnique) $fail($attribute . ' sudah digunakan');
                    } catch (\Exception $e) {
                        $fail('terjadi kesalahan ' . $e->getMessage());
                    }

                }],
                'parent_menu_code' => ['nullable', 'in:'. $parentMenuExist->pluck('menu_code')->implode(',')],
                'menu_icon' => ['nullable', 'max:255'],
                'menu_url' => ['nullable', 'regex:/^[^<>]+$/', 'max:255'],
                'role_view' => ['required', 'in:1,0'],
                'role_create' => ['required', 'in:1,0'],
                'role_update' => ['required', 'in:1,0'],
                'role_delete' => ['required', 'in:1,0'],
                'role_order' => ['required', 'integer', 'between:1,100'],
                'status' => ['required', 'in:1,0'],
            ], [
                'required' => ':attribute tidak boleh kosong.',
                'unique' => ':attribute telah dipakai.',
                'max' => ':attribute maksimal :max.',
                'between' => ':attribute hanya boleh dari :min hingga :max.',
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

            if($this->menuRoleModel->updateMenuRole($request, $idRoleMenu)) {
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
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function deleteRoleMenu($idUserLevel, $idRoleMenu)
    {
        try {
            if($this->menuRoleModel->deleteMenuRole($idUserLevel, $idRoleMenu)) {
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
                 'error' => $e->getMessage(),
            ], 500);
        }
    }

}
