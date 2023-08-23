<?php

namespace App\Http\Controllers;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $pageTitle = 'Role Lists';
        $roles = Role::all();

        return view('roles.index', [
            'pageTitle' => $pageTitle,
            'roles' => $roles,
        ]);
    }

    public function create()
    {
        $pageTitle = 'Add Role';
        $permissions = Permission::all();
        return view('roles.create', [
            'pageTitle' => $pageTitle,
            'permissions' => $permissions,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required'],
            'permissionIds' => ['required'],
        ]);

        DB::beginTransaction();
        try {
            $role = Role::create([
                'name' => $request->name,
            ]);

            $role->permissions()->sync($request->permissionIds);

            DB::commit();

            return redirect()->route('roles.index');
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function edit($id)
    {
        $pageTitle = 'Edit Role';
        $role = role::findOrFail($id);
        $permissions = Permission::all();

        return view('roles.edit', [
            'pageTitle' => $pageTitle,
            'role' => $role,
            'permissions' => $permissions,
        ]);
    }

    public function update($id, Request $request)
    {
        $role = role::findOrFail($id);
        $permissions = Permission::all();
        $role->update([
            'name' => $request->name,
            $role->permissions()->sync($request->permissionIds),
           
        ]);
        return redirect()->route('roles.index');
    }

    public function delete($id)
    {
        $pageTitle = 'Delete role'; 
         $role = role::findOrFail($id); 
        

         return view('roles.delete', ['pageTitle' => $pageTitle, 'role' => $role]);
    }

    public function destroy($id)
    {
        $role = role::findorFail($id);
        $role->delete();
        return redirect()->route('roles.index');
    }
}
