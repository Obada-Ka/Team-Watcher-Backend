<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use App\Models\Role;
use Auth;
use App\Models\User;
use DB;

class RoleController extends ApiController
{


    public function getAllRoles(){

        $role = Role::all();
        return $this->successResponse($role);
    }

    public function assignRole($id, $roleId){

        $user = User::findOrFail($id);
        $role = Role::findOrFail($roleId);
        $row = DB::table('users_roles')
        ->where('user_id', '=',  $user->id)
        ->where('role_id', '=', $role->id)
        ->count();
        if(!$row){

        $roleAssign = $user->roles()->attach($roleId);
        $result = $roleAssign;
        $message = "role assigned to user";
        return $this->successResponse($result, $message);
        }
        else{

            $message = "User already has this role";
            $result = null;
            return $this->errorResponse($message,$result, 404);
        }
    }

    public function deleteAssignRole($id, $roleId){

        $user = User::findOrFail($id);
        $role = Role::findOrFail($roleId);
        $row = DB::table('users_roles')
        ->where('user_id', '=',  $user->id)
        ->where('role_id', '=', $role->id)
        ->count();
        if($row){

        $role = $user->roles()->detach($roleId);
        $result = $role;
        $message = "role deleted from user";
        return $this->successResponse($result, $message);
        }
        else {
            $message = "User hasn't got this role";
            $result = null;
            return $this->errorResponse($message,$result, 404);
        }
    }
}
