<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\UserWorkingScreenShotRequest;
use App\Http\Requests\UserWorkingStoreRecordRequest;
use App\Models\UserWorkingStoreRecord; 
use App\Models\UserWorkingScreenshot; 

use App\Models\TaskTime;
use DB;

class UserWorkingController extends Controller
{
    public function fetchScreenshot(){

        $data =DB::table('tbl_user_working_screenshot')
        ->join('tbl_users','tbl_user_working_screenshot.user_id','=','tbl_users.id')->select('tbl_user_working_screenshot.*','tbl_users.username','tbl_users.usertype')->get(); 
       
        return response()->json([
            'success'=>true, 
            'data'=>$data
        ]);
  
    }

    public function userWorkingScreenshotStore(UserWorkingScreenShotRequest $request){

        // $screenshot =new UserWorkingScreenshot(); 
        
        // $screenshot->user_id =$request->user_id;
        // $screenshot->screenshot ='screenshot/user_id'.date('Y-m-d-H-i-s').'-'.$request->screenshot->getClientOriginalName(); 
        // $request->screenshot->move(public_path('screenshot/user_id'),$request->screenshot);
        // UserWorkingScreenshot::create([
        //     $request->user_id=>$request->user_id,
        //     $request->screenshot =$request->screenshot
        // ]);

         $user_id = $request->user_id;
        $screenshot = 'screenshot/user_id/'.date('Y-m-d-H-i-s').'_'.$request->screenshot->getClientOriginalName();  
    
        $request->screenshot->move(public_path('screenshot/user_id'), $screenshot);
    
        UserWorkingScreenshot::create([
            'user_id' => $request->user_id,
            'screenshot' => $screenshot
        ]);

        return response()->json([
            'success'=>true,
            'message'=>'Image upload successfully.'
        ]);     
    }

    public function userWorkingScreenshotDelete(Request $request,$id){
            UserWorkingScreenshot::destroy($id);

            return response()->json([
                'success'=>true, 
                'message'=>'User working screenshot deleted successfully'
            ]);
    }

    public function storeUserWorkingRecord(UserWorkingStoreRecordRequest $request){
             DB::beginTransaction();
             
             try{
                 $record =new UserWorkingStoreRecord();
                 $record->user_id  =$request->user_id;
                 $record->time_type=$request->time_type;
                 $record->start_time =$request->start_time; 
                 $request->end_time =$request->end_time;
                 $record->save();

                if($request->has('task_id') && $request->has('start_time')){
                    $time = new TaskTime();
                    $time->user_id = $request->user_id;
                    $time->task_id = $request->task_id;
                    $time->start_time = $request->start_time;
                    $time->end_time = $request->end_time;
                    $time->save();
                        }
                 DB::commit();

                 return response()->json([
                     'success'=>true,
                     'message'=>'date saved successfully'
                 ],200); 
             }
             catch(Exception $e){
                    DB::rollback();
                    return $e;
             }
    
    }

    public function DeleteUserWorkingRecord(Request $request, $id){
            
         UserWorkingStoreRecord::destroy($id);
         return response()->json([
             'success'=>true, 
             'message'=>'Record deleted successfully'
         ]); 
            
    }
}