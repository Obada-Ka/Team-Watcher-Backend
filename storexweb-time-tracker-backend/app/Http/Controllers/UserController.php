<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Api\ApiController;
use App\Models\User;
use Validator;
use Auth;
use DB;
use Carbon\Carbon;


class UserController extends ApiController
{

    public function getAllUsers(){
        $users = User::with('roles:name,id')->get();
        return $this->successResponse($users);
    }

    public function getStatus(){

        $result = [];
        $date = date(Carbon::today());
        $users = User::with('timers')->get();
        foreach($users as $user){

            $timers = DB::table('timers')
            ->where('user_id', '=', $user->id)
            ->whereDate('created_at' ,$date)
            ->count();
            if($timers){

                $online = DB::table('timers')
                ->where('user_id', '=', $user->id)
                ->whereColumn('created_at' ,'updated_at')
                ->count();

                if($online){
                    $status = "online";
                }
                else{
                    $status =  "await";
                }
            }
            else{
                $status =  "offline";
            }

            $result[]=[
                'name'=>$user->name,
                'status'=>$status,
                'id'=>$user->id
            ];
        }
        return $this->successResponse($result);
    }
}
