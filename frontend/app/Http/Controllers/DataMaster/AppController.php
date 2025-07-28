<?php

namespace App\Http\Controllers\DataMaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\AuthMenuService;
use App\Services\AppService;

class AppController extends Controller
{
    protected $mainMenuCode = 'DM';
    protected $subMenuCode = 'APP';
    protected $levelMenuCode = null;
    protected $userLevelId = false;
    protected $breadcrumb = [];
    protected $roleMenuAccess = "";
    protected $menuDinamis = [];
    protected $urlMenu = [];
    protected $request;

    // service
    protected $appService;

    public function __construct(AuthMenuService $autMenuService, AppService $appService, Request $request)
    {
        parent::__construct($autMenuService);
        $this->appService = $appService;
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
				'name' => 'Aplikasi',
				'url' => route('data_master.app.view'),
				'active' => true
			],
        ];
    }

    public function viewApp()
    {
        if ($redirect = $this->authMenuService->checkView()) {
            return $redirect;
        }

        $data = [
			'title' =>  'Aplikasi',
			'menuDinamis' => $this->menuDinamis,
			'breadcrumb' => $this->breadcrumb,
            'urlMenu' => $this->urlMenu = [
                'link' => route('data_master.app.view'),
                'link_json' => route('data_master.app.json_data'),
            ]
		];
        return view('pages.data_master.aplikasi', $data);
    }

    public function showAppById(int $idApp)
    {
        if ($this->request->ajax() && $this->request->expectsJson()) {
            if ($redirect = $this->authMenuService->checkJsonView()) return $redirect;
            return $this->appService->getDataAppJsonByIdApp($idApp);
        } else {
            abort(403, 'Akses Ditolak!');
        }

    }

    public function jsonDataApp()
    {
        if ($this->request->ajax() && $this->request->expectsJson()) {
            if ($redirect = $this->authMenuService->checkJsonView()) return $redirect;
            return $this->appService->getDataAppJson();
        } else {
            abort(403, 'Akses Ditolak!');
        }

    }

    public function createDataApp()
    {
        if ($this->request->ajax() && $this->request->expectsJson()) {
            if ($redirect = $this->authMenuService->checkJsonCreate()) return $redirect;
            $data = $this->request->only(['app_name', 'desc', 'status']);
            return $this->appService->createDataApp($data);
        } else {
            abort(403, 'Akses Ditolak!');
        }
    }

    public function updateDataApp(int $idApp)
    {
        if ($this->request->ajax() && $this->request->expectsJson()) {
            if ($redirect = $this->authMenuService->checkJsonUpdate()) return $redirect;
            $data = $this->request->only(['app_name', 'desc', 'status']);
            return $this->appService->updateDataApp($data, $idApp);
        } else {
            abort(403, 'Akses Ditolak!');
        }
    }

    public function deleteDataApp(int $idApp)
    {
        if ($this->request->ajax() && $this->request->expectsJson()) {
            if ($redirect = $this->authMenuService->checkJsonDelete()) return $redirect;
            return $this->appService->deleteDataApp($idApp);
        } else {
            abort(403, 'Akses Ditolak!');
        }
    }
}
