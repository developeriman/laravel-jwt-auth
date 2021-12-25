<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\TaskFile;
use App\Http\Requests\TaskFileRequest;
use DB;
use Carbon\Carbon;

class fileUploadController extends Controller
{
   public function store(TaskFileRequest $request){

        $user = $request->user_id;
        $task_id = $request->task_id;
        $file_name = 'task/'.date('Y-m-d-H-i-s').'_'.$request->file->getClientOriginalName();
       $request->file->move(public_path('task'), $file_name);

           TaskFile::create([
            'task_id' => $task_id,
            'user_id' => $user,   
            'file' => $file_name,
        ]);
            return response()->json([
            'success' => true,
            'message' => 'File upload successfully!'
        ]);
   }

   public function destroy($id){
       TaskFile::destroy($id);
       
       return response()->json([
           'success'=>true,
           'message'=>'task file deleted successfully'
       ]);
   }
}
