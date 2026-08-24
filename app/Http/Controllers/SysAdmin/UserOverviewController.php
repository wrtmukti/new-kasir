<?php

namespace App\Http\Controllers\SysAdmin;

use App\Http\Controllers\Controller;
use App\Models\SysAdmin\SystemUser;
use App\Models\SysAdmin\Client;
use Illuminate\Http\Request;

class UserOverviewController extends Controller
{
    /**
     * Tampilkan daftar Pengguna Platform & Tenant Owner
     */
    public function index(Request $request)
    {
        $systemUsers = SystemUser::where('delete_status', 0)->latest('id')->get();
        $clients = Client::where('delete_status', 0)->latest('id')->get();

        return view('sys_admin.users.index', [
            'activeMenu' => 'users',
            'systemUsers' => $systemUsers,
            'clients' => $clients,
        ]);
    }
}
