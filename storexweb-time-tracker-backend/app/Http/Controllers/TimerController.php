<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Auth;
use Carbon\Carbon;
use App\Http\Resources\TimerResource;
use App\Http\Controllers\Api\ApiController;
use DB;

class TimerController extends ApiController
{

    public function startTimer(Request $request)
    {
        $user = Auth::user();
        $runningTimer = DB::table('timers')
        ->where('user_id', '=', $user->id)
        ->whereColumn('created_at' ,'updated_at')
        ->first();
        if(!$runningTimer){

            $timer =  $user->timers()->create([
                'name' => $request->name,
                'user_id' => $user->id,
                ]);
                $result =  new TimerResource($timer);
                $message = "Timer started";
                return $this->successResponse($result,  $message);
        }
        else{

            $now = Carbon::now();
            $formatted_now = Carbon::parse($now);
            $formatted_created_at = Carbon::parse($runningTimer->created_at);
            $diff = ['value' => $formatted_now->diffInSeconds($formatted_created_at)];
            $id = (['id' => $runningTimer->id]);
            $data = array_merge($diff, $id);
            return $this->errorResponse ($data, "you can't run more than one timer", 404);
        }
    }

    public function stopTimer($id)
    {
        $user = Auth::user();
        $timer = $user->timers()->find($id);
        if ($timer ) {

            $timer->update([$timer->updated_at = carbon::now()]);
        }
        else{

            return $this->errorResponse( "failed", 404);
        }
        $result =  new TimerResource($timer);
        $message = "Timer stopped";
        return $this->successResponse($result, $message);
    }

    // public function getRange(Request $request)
    // {
    //     $startDate = $request->start_date;
    //     $endDate = $request->end_date;
    //     // $startDate = date("2022-06-25");
    //     // $endDate = date("2022-06-28");
    //     $timers = DB::table('timers')
    //     ->whereBetween('created_at', [$startDate, $endDate])
    //     ->get();
    //     $result=[];
    //     foreach($timers as $timer){

    //         if (!empty($tateimer->updd_at) &&
    //             !empty($timer->created_at)&&
    //             $timer->updated_at > $timer->created_at){
    //                 $formatted_updated_at=Carbon::parse($timer->created_at);
    //                 $formatted_created_at=Carbon::parse($timer->updated_at);
    //                 $result[$timer->created_at] =  $formatted_updated_at->diffInMinutes($formatted_created_at);
    //             }
    //     }
    //     return $this->successResponse($result);
    // }

    public function getAllDayTimer(){

        $user = Auth::user();
        $date = date(Carbon::today());
        $timers = DB::table('timers')
        ->where('user_id', '=', $user->id)
        ->whereDate('created_at' ,$date)
        ->get()->ToArray();
        // dd($timers);
        if($timers){
            $result=[];
            foreach($timers as $timer){

                if (!empty($timer->updated_at) &&
                    !empty($timer->created_at)&&
                    $timer->updated_at > $timer->created_at){

                        $formatted_created_at=Carbon::parse($timer->created_at);
                        $formatted_updated_at=Carbon::parse($timer->updated_at);
                        $day = $formatted_created_at->format('d M');
                        $hour = $formatted_created_at->format('H:i');
                        $value =  $formatted_updated_at->diffInSeconds($formatted_created_at) ;
                        $result[$timer->id]=[
                            'hour'=>$hour,
                            'day'=>$day,
                            'value' =>$value
                        ];
                    }
            }
            return $this->successResponse($result);
        }

        else{
            return $this->successResponse();
        }
    }

    public function getDayTimer(Request $request){

        $date = $request->date;
        // $date = date("2022-06-27");
        $counter = 0;
        $items = DB::table('timers')->select('*')
                ->whereDate('created_at',$date)
                ->get();
        foreach($items as $item){

            if (
            !empty($item->updated_at) &&
            !empty($item->created_at)&&
            $item->updated_at > $item->created_at){

                $formatted_updated_at=Carbon::parse($item->created_at);
                $formatted_created_at=Carbon::parse($item->updated_at);
                $diff = $formatted_updated_at->diffInMinutes($formatted_created_at);
                $counter+= $diff;
            }
        }
        return $this->successResponse($counter);
    }
}















