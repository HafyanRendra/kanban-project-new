<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Role; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate; 

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        Gate::authorize('createNewRoles', Role::class);
        $pageTitle = 'Users List';
        $users = User::all();
        return view('users.index', [
            'pageTitle' => $pageTitle,
            'users' => $users,
        ]);
    }

    public function editRole($id)
    {
        Gate::authorize('createNewRoles', Role::class);
        $pageTitle = 'Edit User Role';
        $user = User::findOrFail($id);
        $roles = Role::all();

        return view('users.edit_role', [
            'pageTitle' => $pageTitle,
            'user' => $user,
            'roles' => $roles,
        ]);
    }

    public function updateRole($id, Request $request)
    {
        Gate::authorize('createNewRoles', Role::class);
        $user = User::findOrFail($id);
        $user->update([
            'role_id' => $request->role_id,
        ]);

        return redirect()->route('users.index');
    }
}
