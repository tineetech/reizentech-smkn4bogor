<?php

namespace App\Http\Controllers;
use App\Services\AuthService;
use App\Services\AuthMenuService;
use Illuminate\Http\Request;
use App\Models\ActivityLog;

class AuthController extends Controller
{
    public function __construct(AuthMenuService $authMenuService, AuthService $authService)
    {
        parent::__construct($authMenuService);
        $this->authService = $authService;
    }

    public function viewLogin()
    {
        $data = [
            'title' => 'Halaman Login'
        ];
        return view('pages.auth.login', $data);
    }

    public function actionLogin(Request $request)
    {
        $data = $request->only(['username', 'password']);
        $attempt = $this->authService->attempt($data);
        // jika berhasil
        if($attempt['status']) {
            $request->session()->regenerate();  // Regenerasi sesi untuk keamanan
            $request->session()->put($this->sessionName, $attempt['data']);

            return redirect()->route('dashboard')->with('success', $attempt['message']);
        } else {
            // validasi input
            if(isset($attempt['messages'])) {
                return back()->withErrors($attempt['messages'])->withInput();
            } else {
                return back()->withErrors(['errors' => $attempt['message']]);
            }
        }
    }

    public function actionLogout(Request $request)
    {
        $userRoleId = $request->session()->get($this->sessionName.".user_role_id");
        $request->session()->invalidate(); // Hapus session ID
        $request->session()->regenerateToken(); // Regenerasi CSRF token
        ActivityLog::create([
            'user_role_id' => $userRoleId,
            'activity'     => 'logout',
            'description'=> 'berhasil logout!',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
        return redirect()->route('auth.login')->with('success', 'berhasil logout!');
    }
}
