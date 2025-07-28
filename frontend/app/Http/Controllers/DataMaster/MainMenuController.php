<?php

namespace App\Http\Controllers\DataMaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\AuthMenuService;
use App\Services\MainMenuService;

class MainMenuController extends Controller
{
    protected $mainMenuCode = 'DM';
    protected $subMenuCode = 'MENU';
    protected $levelMenuCode = 'MMENU';
    protected $userLevelId = false;
    protected $breadcrumb = [];
    protected $roleMenuAccess = "";
    protected $menuDinamis = [];
    protected $urlMenu = [];
    protected $request;

    // service
    protected $mainMenuService;

    public function __construct(AuthMenuService $autMenuService, MainMenuService $mainMenuService, Request $request)
    {
        parent::__construct($autMenuService);
        $this->mainMenuService = $mainMenuService;
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
				'url' => route('data_master.main_menu.view'),
				'active' => false
			],
            [
				'name' => 'Main Menu',
				'url' => route('data_master.main_menu.view'),
				'active' => true
			],
        ];
    }

    public function viewMainMenu()
    {
        if ($redirect = $this->authMenuService->checkView()) return $redirect;

        $data = [
			'title' =>  'Main Menu',
			'menuDinamis' => $this->menuDinamis,
			'breadcrumb' => $this->breadcrumb,
            'urlMenu' => $this->urlMenu = [
                'link' => route('data_master.main_menu.view'),
                'link_json' => route('data_master.main_menu.json_data'),
            ]
		];
        return view('pages.data_master.main_menu', $data);
    }

    public function showMainMenuById($idMenu)
    {
        if ($this->request->ajax() && $this->request->expectsJson()) {
            if ($redirect = $this->authMenuService->checkJsonView()) return $redirect;
            return $this->mainMenuService->getDataMainMenuJsonByIdMainMenu($idMenu);
        } else {
            abort(403, 'Akses Ditolak!');
        }

    }


    public function jsonDataMainMenu()
    {
        if ($this->request->ajax() && $this->request->expectsJson()) {
            if ($redirect = $this->authMenuService->checkJsonView()) return $redirect;
            return $this->mainMenuService->getDataMainMenuJson();
        } else {
            abort(403, 'Akses Ditolak!');
        }

    }

    public function createDataMainMenu()
    {
        if ($this->request->ajax() && $this->request->expectsJson()) {
            if ($redirect = $this->authMenuService->checkJsonCreate()) return $redirect;
            $data = $this->request->only(['menu_code', 'menu_name', 'menu_type', 'status']);
            return $this->mainMenuService->createDataMainMenu($data);
        } else {
            abort(403, 'Akses Ditolak!');
        }
    }

    public function updateDataMainMenu($idMenu)
    {
        if ($this->request->ajax() && $this->request->expectsJson()) {
            if ($redirect = $this->authMenuService->checkJsonUpdate()) return $redirect;
            $data = $this->request->only(['menu_code', 'menu_name', 'menu_type', 'status']);
            return $this->mainMenuService->updateDataMainMenu($data, $idMenu);
        } else {
            abort(403, 'Akses Ditolak!');
        }
    }

    public function deleteDataMainMenu($idMenu)
    {
        if ($this->request->ajax() && $this->request->expectsJson()) {
            if ($redirect = $this->authMenuService->checkJsonDelete()) return $redirect;
            return $this->mainMenuService->deleteDataMainMenu($idMenu);
        } else {
            abort(403, 'Akses Ditolak!');
        }
    }
}
