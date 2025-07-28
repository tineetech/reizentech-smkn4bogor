<?php

namespace App\Http\Controllers\DataMaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\AuthMenuService;
use App\Services\RoleMenuService;

class RoleMenuController extends Controller
{
    protected $mainMenuCode = 'DM';
    protected $subMenuCode = 'MENU';
    protected $levelMenuCode = 'MENUROLE';
    protected $userLevelId = false;
    protected $breadcrumb = [];
    protected $roleMenuAccess = "";
    protected $menuDinamis = [];
    protected $urlMenu = [];
    protected $request;

    // service
    protected $roleMenuService;

    public function __construct(AuthMenuService $autMenuService, RoleMenuService $roleMenuService, Request $request)
    {
        parent::__construct($autMenuService);
        $this->roleMenuService = $roleMenuService;
        $this->request = $request;

        $this->userLevelId = session()->get($this->sessionName.".user_level_id");
        $this->authMenuService->checkAuthMenu($this->userLevelId, $this->mainMenuCode, $this->subMenuCode, $this->levelMenuCode);

        $this->roleMenuAccess = session()->get($this->sessionName.".role_menu_access");
        $this->menuDinamis = $this->authMenuService->getMenuDinamisLogged($this->roleMenuAccess, $this->mainMenuCode, $this->subMenuCode, $this->levelMenuCode);
        $this->breadcrumb = [
            [
				'name' => 'Data Master',
				'url' => '#',
				'active' => false
			],
            [
				'name' => 'Menu',
				'url' => route('data_master.role_menu.view'),
				'active' => false
			],
            [
				'name' => 'Role Menu Akses',
				'url' => route('data_master.role_menu.view'),
				'active' => true
			],
        ];
    }

    public function viewRoleMenu(Request $request)
    {
        if ($redirect = $this->authMenuService->checkView()) return $redirect;

        $data = [
			'title' =>  'Role Menu Akses',
			'menuDinamis' => $this->menuDinamis,
			'breadcrumb' => $this->breadcrumb,
            'urlMenu' => $this->urlMenu = [
                'link' => route('data_master.role_menu.view'),
                'link_json' => '',
                'link_tambah' => '',
                'link_options' => route('data_master.role_menu.options'),
            ],
            'options' => [
                'appData' => $this->roleMenuService->getFormOptionApp(),
                'appDataValue' => false,
                'userLevelData' => [],
                'userLevelDataValue' => false,
            ]
		];

        $idApp = $request->query('app_id');

        // dd($idApp);

        if($idApp) {
            $idUserLevel = $request->query('user_level_id');
            $appData = $this->roleMenuService->getDataAppByIdApp($idApp);
            if(!$appData) {
                abort(404, 'Data tidak ditemukan!');
            }
            $data['options']['appDataValue'] = $idApp;
            $userLevelData = $this->roleMenuService->getFormOptionUserLevel($idApp, $idUserLevel);
            $data['options']['userLevelData'] = $userLevelData;
            if($idUserLevel) {
                $appData = $this->roleMenuService->getDataUserLevelByIdAppId($idApp, $idUserLevel);
                $data['options']['userLevelDataValue'] = $idUserLevel;
                $data['urlMenu']['link_tambah'] = route('data_master.role_menu.create', ['idUserLevel' => $idUserLevel]);
                $data['urlMenu']['link_json'] = route('data_master.role_menu.json_data', ['idUserLevel' => $idUserLevel]);
            }
        }

        return view('pages.data_master.role_menu', $data);
    }

    public function showRoleMenuById($idUserLevel, $idRoleMenu)
    {
        if ($this->request->ajax() && $this->request->expectsJson()) {
            if ($redirect = $this->authMenuService->checkJsonView()) return $redirect;
            return $this->roleMenuService->getDataRoleMenuJsonByIdRoleMenu($idUserLevel, $idRoleMenu);
        } else {
            abort(403, 'Akses Ditolak!');
        }

    }

    public function getFormOptionsRoleMenu()
    {
        if ($this->request->ajax() && $this->request->expectsJson()) {
            if ($redirect = $this->authMenuService->checkJsonView()) return $redirect;
            return $this->roleMenuService->getFormOptionsRoleMenu();
        } else {
            abort(403, 'Akses Ditolak!');
        }

    }

    public function jsonDataRoleMenu($idUserLevel)
    {
        if ($this->request->ajax() && $this->request->expectsJson()) {
            if ($redirect = $this->authMenuService->checkJsonView()) return $redirect;
            return $this->roleMenuService->getDataRoleMenuJson($idUserLevel);
        } else {
            abort(403, 'Akses Ditolak!');
        }

    }

    public function createDataRoleMenu($idUserLevel)
    {
        if ($this->request->ajax() && $this->request->expectsJson()) {
            if ($redirect = $this->authMenuService->checkJsonCreate()) return $redirect;
            $data = $this->request->only(['menu_id', 'parent_menu_code', 'menu_icon', 'menu_url', 'role_view', 'role_create', 'role_update', 'role_delete', 'role_order', 'status']);
            return $this->roleMenuService->createRoleMenu($data, $idUserLevel);
        } else {
            abort(403, 'Akses Ditolak!');
        }
    }

    public function updateDataRoleMenu($idUserLevel, $idRoleMenu)
    {
        if ($this->request->ajax() && $this->request->expectsJson()) {
            if ($redirect = $this->authMenuService->checkJsonUpdate()) return $redirect;
            $data = $this->request->only(['menu_id', 'parent_menu_code', 'menu_icon', 'menu_url', 'role_view', 'role_create', 'role_update', 'role_delete', 'role_order', 'status']);
            return $this->roleMenuService->updateRoleMenu($data, $idUserLevel, $idRoleMenu);
        } else {
            abort(403, 'Akses Ditolak!');
        }
    }

    public function deleteDataRoleMenu($idUserLevel, $idRoleMenu)
    {
        if ($this->request->ajax() && $this->request->expectsJson()) {
            if ($redirect = $this->authMenuService->checkJsonDelete()) return $redirect;
            return $this->roleMenuService->deleteRoleMenu($idUserLevel, $idRoleMenu);
        } else {
            abort(403, 'Akses Ditolak!');
        }
    }
}
