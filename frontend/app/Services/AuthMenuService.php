<?php

namespace App\Services;
use App\Models\MenuRole;

class AuthMenuService extends Service
{
    protected $menuRoleModel;
    protected $redirectNotAccess = 'dashboard';

    public function __construct(MenuRole $menuRoleModel)
    {
        $this->menuRoleModel = $menuRoleModel;
    }

    public function checkAuthMenu($userLevelId, $mainMenuCode, $subMenuCode, $levelMenuCode)
    {
        $menuCode = $mainMenuCode;
        $parentMenuCode = null;
        // cek level menu
        if(!is_null($subMenuCode) && is_null($levelMenuCode)) {
            $menuCode = $subMenuCode;
            $parentMenuCode = $mainMenuCode;
        } else if(!is_null($subMenuCode) && !is_null($levelMenuCode)) {
            $menuCode = $levelMenuCode;
            $parentMenuCode = $subMenuCode;
        }

        $authMenu = $this->menuRoleModel->getAuthMenu($userLevelId, $menuCode, $parentMenuCode);
        if(!$authMenu) {
            return redirect()->route($this->redirectNotAccess)->with('warning', 'anda tidak punya hak akses!');
        } else {
            session()->flash('auth_menu_permissions', [
                'view' => $authMenu->role_view,
                'create' => $authMenu->role_create,
                'update' => $authMenu->role_update,
                'delete' => $authMenu->role_delete,
            ]);
        }
    }

    public function getMenuDinamisLogged($roleMenuAccess, $mainMenuCode, $subMenuCode, $levelMenuCode)
    {
        $data = "";
        if($roleMenuAccess != "") {
            foreach($roleMenuAccess as $menu) {
                $active = $mainMenuCode == $menu['menu_code'] ? 'active' : '';

                if($menu['sub_menu'] != '') {
                    $data .= '<li class="nav-item '. $active .' dropdown">
                        <a
                        class="nav-link dropdown-toggle"
                        href="#navbar-base"
                        data-bs-toggle="dropdown"
                        data-bs-auto-close="outside"
                        role="button"
                        aria-expanded="false"
                        >
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                '.$menu['menu_icon'].'
                            </span>
                            <span class="nav-link-title"> '. $menu['menu_name'] .' </span>
                        </a>
                        <div class="dropdown-menu">
                            <div class="dropdown-menu-columns">';

                            $data .= $this->getSubMenuDinamisLogged($menu['sub_menu'], $subMenuCode, $levelMenuCode);

                    $data .= '</div>
                            </div>
                        </li>';
                } else {
                    $data .= '<li class="nav-item '. $active .' ">
                                    <a class="nav-link" href="'. url($menu['menu_url']) .'">
                                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        '.$menu['menu_icon'].'
                                        </span>
                                        <span class="nav-link-title">
                                            '. $menu['menu_name'] .'
                                        </span>
                                    </a>
                                </li>';
                }
            }
        }
        return $data;
    }

    public function getSubMenuDinamisLogged($roleMenuAccess, $subMenuCode, $levelMenuCode)
    {
        $data = "";
        if($roleMenuAccess != "") {
            $data = "";
            $count = 0; // Inisialisasi penghitung

            $data .= '<div class="dropdown-menu-column">';
            foreach($roleMenuAccess as $menu) {
                $active = $subMenuCode == $menu['menu_code'] ? 'active' : '';
                if($menu['sub_menu'] != '') {
                    $data .= '<div class="dropend">
                                            <a class="dropdown-item '. $active .' dropdown-toggle " href="#" data-bs-toggle="dropdown"
                                                data-bs-auto-close="outside" role="button" aria-expanded="false">
                                                '.$menu['menu_name'].'
                                            </a>
                                        <div class="dropdown-menu" data-bs-popper="static">';

                    $data .= $this->getSubMenuDinamisLogged($menu['sub_menu'], $subMenuCode, $levelMenuCode);

                    $data .= "</div>
                        </div>";
                } else {
                    if(!is_null($levelMenuCode))  $active = $levelMenuCode == $menu['menu_code'] ? 'active' : '';
                    $data .= '<a class="dropdown-item '. $active .'" href="'.url($menu['menu_url']).'">
                                        '.$menu['menu_name'].'
                                </a>';
                }

                $count++; // Tambahkan penghitung setiap kali menu ditambahkan

                // Jika sudah 10 elemen, tutup div dan buat div baru
                if ($count == 10) {
                        $data .= '</div><div class="dropdown-menu-column">';
                        $count = 0; // Setel ulang penghitung
                }
            }
            // Tutup div terakhir
            $data .= "</div>";

        }
        return $data;
    }

    public function checkView()
    {
        $permissions = session('auth_menu_permissions');
        if (!$permissions['view']) {
            return redirect()->route($this->redirectNotAccess)->with('warning', 'anda tidak punya hak akses!');
        }
        return false;
    }

    public function checkCreate()
    {
        $permissions = session('auth_menu_permissions');
        if (!$permissions['create']) {
            return redirect()->route($this->redirectNotAccess)->with('warning', 'anda tidak punya hak akses!');
        }
        return false;
    }

    public function checkUpdate()
    {
        $permissions = session('auth_menu_permissions');
        if (!$permissions['update']) {
            return redirect()->route($this->redirectNotAccess)->with('warning', 'anda tidak punya hak akses!');
        }
        return false;
    }

    public function checkDelete()
    {
        $permissions = session('auth_menu_permissions');
        if (!$permissions['delete']) {
            return redirect()->route($this->redirectNotAccess)->with('warning', 'anda tidak punya hak akses!');
        }
        return false;
    }

    public function checkJsonView()
    {
        $permissions = session('auth_menu_permissions');
        if (!$permissions['view']) {
            return $this->responseJson([
                'status' => false,
                'message' => 'anda tidak punya hak akses!',
            ], 403);
        }
        return false;
    }

    public function checkJsonCreate()
    {
        $permissions = session('auth_menu_permissions');
        if (!$permissions['create']) {
            return $this->responseJson([
                'status' => false,
                'message' => 'anda tidak punya hak akses!',
            ], 403);
        }
        return false;
    }

    public function checkJsonUpdate()
    {
        $permissions = session('auth_menu_permissions');
        if (!$permissions['update']) {
             return $this->responseJson([
                'status' => false,
                'message' => 'anda tidak punya hak akses!',
            ], 403);
        }
        return false;
    }

    public function checkJsonDelete()
    {
        $permissions = session('auth_menu_permissions');
        if (!$permissions['delete']) {
             return $this->responseJson([
                'status' => false,
                'message' => 'anda tidak punya hak akses!',
            ], 403);
        }
        return false;
    }

    public function getAuthLevelMenu($userLevelId)
    {
        $userMenuRole = $this->menuRoleModel->getMainMenuByUserLevelId($userLevelId);
        if($userMenuRole->isEmpty()) return "";
        $data = [];
        foreach($userMenuRole as $menu) {
            $data[] = [
                'menu_code' => $menu->menu_code,
                'menu_name' => $menu->menu_name,
                'menu_type' => $menu->menu_type,
                'parent_menu_code' => $menu->parent_menu_code,
                'menu_icon' => $menu->menu_icon,
                'menu_url' => $menu->menu_url,
                'sub_menu' => $this->getAuthLevelSubMenu($userLevelId, $menu->menu_code),
            ];
        }
        return $data;
    }

    public function getAuthLevelSubMenu($userLevelId, $parentMenuCode)
    {
        if(is_null($parentMenuCode)) return "";
        $userMenuRole = $this->menuRoleModel->getSubMenuByUserLevelIdMenuCode($userLevelId, $parentMenuCode);
        if($userMenuRole->isEmpty()) return "";
        $data = [];
        foreach($userMenuRole as $menu) {
            $data[] = [
                'menu_code' => $menu->menu_code,
                'menu_name' => $menu->menu_name,
                'menu_type' => $menu->menu_type,
                'parent_menu_code' => $menu->parent_menu_code,
                'menu_icon' => $menu->menu_icon,
                'menu_url' => $menu->menu_url,
                'sub_menu' => $this->getAuthLevelSubMenu($userLevelId, $menu->menu_code),
            ];
        }
        return $data;
    }

}
