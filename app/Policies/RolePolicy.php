<?php

namespace App\Policies;

use App\Models\User;

class RolePolicy
{
    /**
     * Create a new policy instance.
     */
    protected function getUserPermissions(user $user)
    {
        return $user->role()
            ->with('permissions')
            ->get()
            ->pluck('permissions')
            ->flatten()
            ->pluck('name');
    }

    public function before(user $user)
    {
    if ($user->role && $user->role->name == 'admin') {
        return true;
    }
    return null;

    }


    public function viewAnyRoles(user $user): bool{
        $permissions = $this->getUserPermissions($user);
        if ($permissions->contains('View-any-roles')){
            return true;
        }
        return false;
    }

    public function createNewRoles(user $user): bool
    {
        $permissions = $this->getUserPermissions($user);
        if ($permissions->contains('Create-new-roles')){
            return true;
        }
        return false; 
    }

    public function viewUsersAndRoles(user $user): bool
    {
        $permissions = $this->getUserPermissions($user);
        if ($permissions->contains('View-other-users-and-their-roles')){
            return true;
        }
        return false; 
    }

    public function manageUserRoles(user $user): bool
    {
        $permissions = $this->getUserPermissions($user);
        if ($permissions->contains('Manage-roles-of-users')){
            return true;
        }
        return false; 
    }
}
