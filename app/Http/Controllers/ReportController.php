<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB; 

class ReportController extends Controller
{
   public function report($id){
        $responseData = [];
        //here getting a project_id;
        $projectToUser=DB::table('tbl_project_to_user')
            ->where('project_id',$id)->pluck('user_id');//here getting user_id;
        $taskToUser=DB::table('tbl_task_to_project')
                ->where('project_id',$id)->pluck('task_id');//here getting task_id;
        $project = DB::table('tbl_project')->where('id',$id)->first();
        foreach($projectToUser as $val) {
            $user= DB::table('tbl_users')->where('id',$val)->select('id','first_name','last_name','username')->first();//it will gives me id,first_name,last_name,username for the tbl_users..
            $user = get_object_vars($user);
            $taskToUserIndividual=DB::table('tbl_task_to_user')
                ->where('user_id', $val)
                ->whereIn('task_id', $taskToUser)
                ->pluck('task_id');
            $total_time_task = DB::table('tbl_task_time')
                ->where("user_id", $val)
                ->whereIn('task_id', $taskToUser)
                ->get();
            $user['total_task'] = count($taskToUserIndividual);
            $user["total_time"] = 0;
            $task_time = 0;
            foreach($total_time_task as $val)
            {
                $task_time+= $this->datediff($val->start_time, $val->end_time);
            }
        $hours=$task_time;
        $hour=intval($hours/60);
        $minute=$hours-($hour*60);
        $task_time = date_format(date_create($hour.'.'.$minute),'H:i:s');
        $user["total_time"]=$task_time;
        array_push($responseData, $user);
        }
        return response()->json([
            'project' => $project,
            'data' => $responseData
        ]);
   }
      public function datediff($start_time,$end_time){
        $now = new \DateTime($start_time);
        $target = new \DateTime($end_time);
        $hours = ($target->getTimestamp() - $now->getTimestamp())/60;
        return $hours;
    }
}
