<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;

class RolePermissionController extends Controller
{   
    use HasRoles;
    public function index(User $user){

        $user = Auth::user();
        //$roles= $user->getRoleNames();
        $permissions = Permission::all();
        $users = User::all();
        $user = auth()->user($users);
        $role = auth()->user();
        if ($role != null){
            return $role = auth()->user()->getRoleNames();
        }else{
            return $role = [];
        }        
    }
}
