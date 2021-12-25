<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Exception; 
use App\Models\ProjectToUser;
use App\Models\TaskToUser;
use App\Models\TaskToProject;
use App\Models\TaskStatus;
use App\Models\TaskToStatus;
use App\Models\Task;
use App\Models\TaskTime;
use App\Models\TaskComment;
use App\Http\Requests\TaskRequest;
use App\Http\Requests\taskCommentRequest;
use App\Http\Requests\storeTaskProjectRequest;
use Validator;
use Validation; 

class TaskController extends Controller
    {
    public function UserToProject($id)
    {
        $data =DB::table('tbl_users')->where('id',$id)->select('tbl_users.id','tbl_users.username')->get();
        
        foreach ($data as $val) {
        $projectId =ProjectToUser::where('user_id',$val->id)->pluck('project_id');
        $project =DB::table('tbl_project')->select('tbl_project.*')->whereIn('id',$projectId)->get();
        $val->projects =$project;
        } 
        return response()->json([
            'message'=>'success',
            'data'=>$data
        ]);

    }
    public function indexProjectTask($id){
        $data =DB::table('tbl_project')
        ->join('tbl_task_to_project','tbl_project.id','=','tbl_task_to_project.project_id')
        ->join('tbl_task','tbl_task_to_project.task_id','=','tbl_task.id')
        ->where('tbl_project.id','=',$id)
        ->select('tbl_project.project_name','tbl_task.*')->get();
        
        foreach ($data as $val) {
           $data =DB::table('tbl_task_to_user')
           ->join('tbl_users','tbl_task_to_user.user_id','=','tbl_users.id')
           ->where('tbl_task_to_user.task_id','=',$val->id)
           ->select('tbl_users.id','tbl_users.first_name','tbl_users.last_name')->get();
           $val->members =$data; 

        }
        return response()->json([
            'data'=>$data
        ]);
    }

    public function indexAllTask($id){
        $data =DB::table('tbl_task_to_user')
        ->join('tbl_task','tbl_task_to_user.task_id','=','tbl_task.id')
        ->where('tbl_task_to_user.user_id',$id)
        ->select('tbl_task.id','tbl_task.task_title')->get();
        return $data; 
    }
//NOT Understand properly

    public function storeTask(TaskRequest $request)
    {
        DB::beginTransaction();

        try{
            $data =new Task; 
            $data->task_title =$request->task_title;
            $data->task_details =$request->task_details;
            $data->priority=$request->priority ? $request->priority:1;
            $data->save();
        if($request->members && is_array($request->members)){
            foreach ($request->members as $val) {
              $taskToUser =new TaskToUser();
              $taskToUser->user_id=$val;
              $taskToUser->task_id=$data->id; 
              $taskToUser->save();
            }
        }
        DB::commit();
        return response()->json([
            'message'=>'task created successfully'
        ]);
    }catch(\Exception $e){
            DB::rollback();
            return $e; 
            }
    }
    public function updateTask(Request $request,$id){
        $request->validate([
            'task_title'=>'required',
            'task_details'=>'required'
        ]);
        DB::beginTransaction();
        try{
            $data =Task::findOrFail($id);
            $data->task_title =$request->task_title;
            $data->task_details=$request->task_details; 
            $data->priority=$request->priority;
            $data->save();

            TaskToUser::where('task_id','=',$id)->delete(); 
        if($request->members && is_array($request->members)){
            foreach ($request->members as $val) {
              $taskToUser =new TaskToUser();
              $taskToUser->user_id=$val;
              $taskToUser->task_id=$data->id; 
              $taskToUser->save();
            }
            }
            DB::commit();
            return response()->json([
                'message'=>'Task Updated successfully'
            ]);
        }
        catch(\Exception $e){
            DB::rollback();
            return $e;
        }
    }
    public function destroyTask($id){
        DB::beginTransaction();
    try{
       $deleted  = DB::table('tbl_task')->where('id',$id)->delete();
        DB::table('tbl_task_comments')->where('user_id',$id)->delete();

        DB::commit();
        return response()->json([
            'message'=>'Data deleted successfully',
            'deleted'=>  $deleted 
        ]);
      
    }
      catch(\Exception $e){
            DB::rollback();
            $e;
        }
    }
  public function indexTaskToProject(){
        $data=DB::table('tbl_task_to_project')
        ->join('tbl_task','tbl_task_to_project.id','=','tbl_task.id')
        ->join('tbl_project','tbl_task_to_project.project_id','=','tbl_project.id')
        ->select('tbl_task_to_project.*','tbl_project.project_name','tbl_project.project_description','tbl_task.task_title','tbl_task.task_details')->get();
        return response()->json([
            'success'=>true,
            'data'=>$data
        ],200);
  }

  public function storeTaskToProject(Request $request){
      try{
          $request->validate([
              'task_id'=>'required',
              'project_id'=>'required'
          ]);
        DB::beginTransaction();
        $data =new TaskToProject();
        $data->task_id =$request->task_id;
        $data->project_id=$request->project_id;
        $data->save();
        DB::commit();

        return response()->json([
            'success'=>true,
            'message'=>'store task completed', 
            'data'=>$data
        ]);
      }
      catch(Exception $e){
          DB::rollback(); 
          return $e; 
      }
  
  }
    public function deleteTaskToProject(Request $request,$id){
        TaskToProject::destroy($id);

        return response()->json([
            'data'=>'task to project deleted successfully'
        ]);
    }
    public function taskStatus(){
        $data=taskStatus::get();
        return response()->json([
            'success'=>true,
            'data'=>$data
        ]);
    }
    public function taskToStatus(){
        $data =DB::table('tbl_task_to_status')
        ->join('tbl_task','tbl_task_to_status.task_id','=','tbl_task.id')
        ->join('tbl_task_status','tbl_task_to_status.status_id','=','tbl_task_status.id')
        ->select('tbl_task_status.*','tbl_task.task_title')->get();
        return response()->json([
            'success'=>true,
            'data'=>$data
        ]);
    } 
    public function taskToStatusStore(Request $request){
        TaskToStatus::create([
            'task_id'=>$request->task_id,
            'status_id'=>$request->status_id
        ]);
        return response()->json([
            'success'=>true, 
            'message'=>'task to status successfully'
        ]);
    }
      public function taskToStatusDelete(Request $req, $id){
        TaskToStatus::destroy($id);
        return response()->json([
            'success' => true,
            'message' => 'Task-to-Status delete successfully.'
        ]);
    }
    public function storeTaskTime(Request $request){
        if($request->end_time){
           $endTimeUpdate = DB::table('tbl_task_time')
           ->where('end_time','=',null)
           ->where('user_id',$request->user_id)
           ->orderBy('user_id','desc')->take(1)
           ->update(['end_time'=>$request->end_time]);
           return response()->json([
               'success'=>true,
               'message'=>'End time updated successfully'
           ]);
        }else{
            $time =new TaskTime();
            $time->task_id =$request->task_id;
            $time->user_id=$request->user_id;
            $time->start_time =$request->start_time;
            $time->save();
            return response()->json([
                'success'=>true,
                'message'=>'task to time created successfully'
            ]);
        }
    }
    public function deleteTaskTime(Request $request,$id){
        TaskTime::destroy($id);
        return response()->json([
            'success'=>true,
            'data'=>'task-to-time deleted successfully'
        ]);
    }
    public function indexTaskComment($id){
        $data =DB::table('tbl_task_comments')
               ->join('tbl_task','tbl_task_comments.task_id','=','tbl_task.id')
               ->join('tbl_users','tbl_task_comments.user_id','=','tbl_users.id')
               ->where('tbl_task_comments.task_id',$id)
               ->select('tbl_task_comments.comment','tbl_task_comments.file','tbl_task_comments.created_at','tbl_users.username')
               ->get();
               return response()->json([
                   'data'=>$data
               ]);
    }
    public function storeTaskComment(taskCommentRequest $request){
        $user =$request->user_id;
        $task_id =$request->task_id;
        $comment =$request->comment;
        if($request->hasFile('file')){
            $file_name =date('Y-m-d H-i-s').'-'.$request->file->getClientOriginalName();
            $request->file->move(public_path('comment/user_id'),$file_name);
        }
        else{
            $file_name ='';
        }

        TaskComment::create([
            'user_id'=>$user,
            'task_id'=>$task_id,
            'comment'=>$comment ? $comment:'',
            'file'=>$file_name
        ]);
        return response()->json([
            'success'=>true,
            'message'=>'task comment added successfully'
        ]);
    }
      public function deleteTaskComment(Request $req, $id){
        TaskComment::destroy($id);
        return response()->json([
            'success' => true,
            'message' => 'Task-comment delete successfully.'
        ]);
    }

    }