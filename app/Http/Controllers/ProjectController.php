<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB; 
use App\Models\ProjectToUser; 
use App\Models\Project;
use App\Http\Requests\ProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Http\Requests\ProjectToUserRequest;
use Exception;
class ProjectController extends Controller
{
    // public function index()
    // {
    //     $project =DB::table('tbl_project')->get(); 
      
    //     foreach ($project as $val)
    //      {
    //        $userId =ProjectToUser::where('project_id',$val->id)->pluck('user_id'); 
    //        $users =DB::table('tbl_users')->select('id','first_name','last_name','email')->whereIn('id',$userId)->get();
    //        $val->members =$users; 
    //     }
    //     return response()->json([
    //         'success'=>true, 
    //         'data'=>$project
    //     ],200);
        
    // }

    // public function store(ProjectRequest $request)
    // {
    //     try{
    //         DB::beginTransaction();
    //         $project =new Project(); 
    //         $project->project_name =$request->project_name;
    //         $project->project_description =$request->project_description;
    //         $project->start_date =$request->start_date;
    //         $project->target_end_date =$request->target_end_date;
    //         $project->end_date =$request->end_date;
    //         $project->save();
        
    //         if($request->members && is_array($request->members)){
    //             foreach($request->members as $val){
    //                 $projectToUser =new ProjectToUser();
    //                 $projectToUser->user_id =$val;
    //                 $projectToUser->project_id =$project->id;
    //                 $projectToUser->save(); 
    //             }
    //         }

    //         DB::commit();
    //         return response()->json([
    //             'success'=>true,
    //             'message'=>'data successfully save'
    //         ],200);
    //     }
    //     catch(\Exception $e){
    //         DB::rollback();
    //         return response()->json([
    //             'message'=>'something went wrong ,Please try again ',
               
    //         ],402);
    //     };
    // }
        
    // public function destroy($id){
    //     $projectId =Project::findOrFail($id);
    //     if($projectId->delete())
    //     {
    //         $projectIdDeleted =new ProjectResource($projectId);
    //         if($projectId){
    //             return response()->json([
    //                 'success'=>true,
    //                 'message'=>'Data deleted successfully'
    //             ]);
    //         }
    //     }
    //     elseif (!$projectIdDeleted) {
    //       return response()->json([
    //           'success'=>false,
    //           'message'=>'Data not deleted'
    //       ]);
    //     }
    // }

    public function createUser(ProjectToUserRequest $request){
               $data= ProjectToUser::create([
               'project_id'=>$request->project_id,              
                'user_id'=>$request->user_id
            
            ]);
        return response()->json([
            'success'=>true,
            'message'=>'data inserted successfully',
            'data'=>$data
        ]);
    }
    public function readUser(){
        $data =DB::table('tbl_project_to_user')
        ->join('tbl_project','tbl_project_to_user.user_id','=','tbl_project.id')
        ->join('tbl_users','tbl_project_to_user.user_id','=','tbl_users.id')
        ->select('tbl_project.*','tbl_users.*')->get();

        return response()->json([
            'success'=>true,
            'data'=>$data
        ]);
    }
    public function DeleteUser($id){
        ProjectToUser::destroy($id);
        
        return response([
            'success'=>true,
            'message'=>'user deleted successfully'
        ]);
    }


}

