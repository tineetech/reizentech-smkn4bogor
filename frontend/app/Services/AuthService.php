<?php

namespace App\Services;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\UserRole;
use App\Models\UserBiodata;
use App\Services\AuthMenuService;
use App\Models\ActivityLog;

class AuthService extends Service
{

    protected $userModel;
    protected $userRoleModel;
    protected $userBiodataModel;
    protected $authMenuService;

    public function __construct(User $userModel, UserRole $userRoleModel, UserBiodata $userBiodataModel, AuthMenuService $authMenuService)
    {
        $this->userModel = $userModel;
        $this->userRoleModel = $userRoleModel;
        $this->userBiodataModel = $userBiodataModel;
        $this->authMenuService = $authMenuService;
    }

    public function attempt($request)
    {

        $validator = Validator::make($request, [
            'username' => ['required', 'regex:/^[^<>]+$/', 'min:3', 'max:255'],
            'password' => ['required', 'regex:/^[^<>]+$/', 'min:6', 'max:255'],
        ], [
            'required' => ':attribute tidak boleh kosong.',
            'min' => ':attribute minimal :min.',
            'max' => ':attribute maksimal :max.',
            'regex' => ':attribute mengandung karakter terlarang!'
        ]);

        // jika gagal validasi input kirim variabel messages, penanda bahwa messages berisi array
        if($validator->fails()) {
            return [
                'status' => false,
                'messages' => $validator->errors()
            ];
        }

        $request = $validator->validated();

        $user = $this->userModel->authWithUsername($request['username']);

        if(!$user) {
            return [
                'status' => false,
                'message' => 'username atau password salah!'
            ];
        }

        if($this->checkPassword($request['password'], $user->password)) {
            //  cek status akun user
            if($user->status != 'active') {
                return [
                    'status' => false,
                    'message' => 'akun telah dinonaktifkan!'
                ];
            }

            // cari level user berdasarkan aplikasi
            $userRole = $this->userRoleModel->getUserRoleByUserIdAppName($user->id, $this->appName());
            if($userRole->isEmpty()) {
                return [
                    'status' => false,
                    'message' => 'hak akses tidak ditemukan!'
                ];
            }

            // cek level user ada berapa, jika 1 tidak perlu milih level user
            if(count($userRole) == 1) {
                if($userRole[0]->biodata_ref_name == 'user_biodata') {
                    if(!$userRole[0]->status) {
                        return [
                            'status' => false,
                            'message' => 'hak akses telah dinonaktifkan!'
                        ];
                    }
                    $userBiodata = $this->userBiodataModel->getUserBiodataByUserId($user->id);
                    $userMenuRole = $this->authMenuService->getAuthLevelMenu($userRole[0]->user_level_id);
                    ActivityLog::create([
                        'user_role_id' => $userRole[0]->id,
                        'activity'     => 'login',
                        'description'=> 'berhasil login!',
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                    ]);
                    return [
                        'status' => true,
                        'message' => 'berhasil login!',
                        'data' => [
                            'user_id' => $user->id,
                            'user_role_id' => $userRole[0]->id,
                            'user_level_id' => $userRole[0]->user_level_id,
                            'username' => $user->username,
                            'biodata_ref' => $userRole[0]->biodata_ref_name,
                            'level' => $userRole[0]->user_level_name,
                            'name' => $userBiodata->name,
                            'role_menu_access' => $userMenuRole,
                        ]
                    ];
                }

            } else {

            }

        }


        return [
            'status' => false,
            'message' => 'username atau password salah!'
        ];
    }
}
