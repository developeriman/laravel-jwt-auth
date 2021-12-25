<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Exception; 
use App\Models\User; 


class UserController extends Controller
{
    public function UserTask($id){
        $data =DB::table('tbl_task')
                ->join('tbl_task_to_user','tbl_task.id','=','tbl_task_to_user.task_id')
                ->where('tbl_task_to_user.user_id',$id)
                ->select('tbl_task.id','tbl_task.task_title')->distinct()->get();
                return response()->json([
                    'success'=>true,
                    'data'=>$data
                ]);
    }
    public function getAllUser(){
        $user =User::paginate(20);

        return response()->json([
            'success'=>true,
            'user'=>$user
        ]);

    }
     public function changeStatus(Request $req){
            DB::beginTransaction();
            try{
                User::where('id',$req->user_id)->update(['status'=>$req->status]);
                DB::commit();
                return response()->json([
                    'success'=> true,
                    'message' => "User status change successfully"
                ]);
            }catch( Exception $e){
                DB::rollback();
                return $e;
            }
    } 
    
}
