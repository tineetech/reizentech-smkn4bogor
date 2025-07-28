<?php

namespace App\Http\Controllers\DataMaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\AuthMenuService;
use App\Services\BiodataRefService;

class BiodataRefController extends Controller
{
    protected $mainMenuCode = 'DM';
    protected $subMenuCode = 'BIOREF';
    protected $levelMenuCode = null;
    protected $userLevelId = false;
    protected $breadcrumb = [];
    protected $roleMenuAccess = "";
    protected $menuDinamis = [];
    protected $urlMenu = [];
    protected $request;

    // service
    protected $biodataRefService;

    public function __construct(AuthMenuService $autMenuService, BiodataRefService $biodataRefService, Request $request)
    {
        parent::__construct($autMenuService);
        $this->biodataRefService = $biodataRefService;
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
				'name' => 'Biodata Referensi',
				'url' => route('data_master.biodata_ref.view'),
				'active' => true
			],
        ];
    }

    public function viewBiodataRef()
    {
        if ($redirect = $this->authMenuService->checkView()) return $redirect;

        $data = [
			'title' =>  'Biodata Referensi',
			'menuDinamis' => $this->menuDinamis,
			'breadcrumb' => $this->breadcrumb,
            'urlMenu' => $this->urlMenu = [
                'link' => route('data_master.biodata_ref.view'),
                'link_json' => route('data_master.biodata_ref.json_data'),
            ]
		];
        return view('pages.data_master.biodata_ref', $data);
    }

    public function showBiodataRefById(int $idBiodataRef)
    {
        if ($this->request->ajax() && $this->request->expectsJson()) {
            if ($redirect = $this->authMenuService->checkJsonView()) return $redirect;
            return $this->biodataRefService->getDataBiodataRefJsonByIdBiodataRef($idBiodataRef);
        } else {
            abort(403, 'Akses Ditolak!');
        }

    }

    public function jsonDataBiodataRef()
    {
        $this->authMenuService->checkJsonView();
        if ($this->request->ajax() && $this->request->expectsJson()) {
            return $this->biodataRefService->getDataBiodataRefJson();
        } else {
            abort(403, 'Akses Ditolak!');
        }

    }

    public function createDataBiodataRef()
    {
        if ($this->request->ajax() && $this->request->expectsJson()) {
            if ($redirect = $this->authMenuService->checkJsonCreate()) return $redirect;
            $data = $this->request->only(['biodata_ref_name', 'status']);
            return $this->biodataRefService->createDataBiodataRef($data);
        } else {
            abort(403, 'Akses Ditolak!');
        }
    }

    public function updateDataBiodataRef(int $idBiodataRef)
    {
        if ($this->request->ajax() && $this->request->expectsJson()) {
            if ($redirect = $this->authMenuService->checkJsonUpdate()) return $redirect;
            $data = $this->request->only(['biodata_ref_name', 'status']);
            return $this->biodataRefService->updateDataBiodataRef($data, $idBiodataRef);
        } else {
            abort(403, 'Akses Ditolak!');
        }
    }

    public function deleteDataBiodataRef(int $idBiodataRef)
    {
        if ($this->request->ajax() && $this->request->expectsJson()) {
            if ($redirect = $this->authMenuService->checkJsonDelete()) return $redirect;
            return $this->biodataRefService->deleteDataBiodataRef($idBiodataRef);
        } else {
            abort(403, 'Akses Ditolak!');
        }
    }
}
