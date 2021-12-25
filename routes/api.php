<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;  

use App\Http\Controllers\{
    ProjectController,
    AuthController,
    ForgotPasswordController,
    fileUploadController,
    UserWorkingController,
    TaskController,
    UserController,
    ReportController
}; 

    Route::get('/',function(){
        dd("Api working"); 
    });

     Route::post('/register', [AuthController::class, 'register']);
     Route::post('/login', [AuthController::class, 'login']);

     //UserController 
    Route::get('user/task/{id}',[UserController::class,'userTask']);
    Route::get('users',[UserController::class,'getAllUser']);
    Route::post('user/change-status',[UserController::class,'changeStatus']);

     Route::post('/fileupload', [fileUploadController::class, 'store']);
     Route::get('/filedelete/{id}', [fileUploadController::class, 'destroy']);

     Route::get('/filedelete/{id}', [fileUploadController::class, 'destroy']);
     //Project Controller 
     Route::post('project/store',[ProjectController::class,'store']);
     Route::get('project-to-user',[ProjectController::class,'readUser']);

     Route::get('project/delete-user/{id}',[ProjectController::class,'DeleteUser']);
     //ReportController 
     Route::get('project/report/{id}',[ReportController::class,'report']);

     //taskController
    Route::get('user/project/{id}',[TaskController::class,'UserToProject']);
    Route::get('project/{id}',[TaskController::class,'indexProjectTask']);
    Route::get('task/all/{id}',[TaskController::class,'indexAllTask']);
    Route::post('task/store',[TaskController::class,'storeTask']);
    Route::put('task/update/{id}',[TaskController::class,'updateTask']);
    Route::delete('task/delete/{id}',[TaskController::class,'destroyTask']);
    Route::get('task-to-project',[TaskController::class,'indexTaskToProject']);
    Route::post('task-to-project/store',[TaskController::class,'storeTaskToProject']);
    Route::delete('task-to-project/delete/{id}',[TaskController::class,'deleteTaskToProject']);
    Route::get('task-status',[TaskController::class,'taskStatus']);
    Route::get('task-to-status',[TaskController::class,'taskToStatus']);
    Route::post('task-to-status/store',[TaskController::class,'taskToStatusStore']); 
    Route::delete('task-to-status/delete/{id}',[TaskController::class,'taskToStatusDelete']);
    Route::post('task-time/store',[TaskController::class,'storeTaskTime']);
    Route::delete('task-time/delete/{id}',[TaskController::class,'deleteTaskTime']);
    Route::get('task-comment/{id}',[TaskController::class,'indexTaskComment']);
    Route::post('task-comment/store',[TaskController::class,'storeTaskComment']);
    Route::delete('task-comment/delete/{id}',[TaskController::class,'deleteTaskComment']);


     //user working screenshot 
     Route::get('user-working-screenshot',[UserWorkingController::class,'fetchScreenshot']);

    //user workingscreenshot  userWorkingScreenshotDelete
     Route::post('user-working-screenshot/store',[UserWorkingController::class,'userWorkingScreenshotStore']);
     Route::get('userWorkingScreenshotDelete/{id}',[UserWorkingController::class,'userWorkingScreenshotDelete']);

    Route::post('/storeUserWorkingRecord', [UserWorkingController::class, 'storeUserWorkingRecord']);
    Route::get('/storeUserWorkingRecordDelete/{id}', [UserWorkingController::class, 'DeleteUserWorkingRecord']);

    //ProjectController 
    Route::get('/project',[ProjectController::class,'index']);

  
    
        Route::group([
            'middleware' => 'api',
            'prefix' => 'auth'

        ], function ($router) {
            Route::post('forget-password',[ForgotPasswordController::class,'forgetPassword']);
            Route::post('reset-password',[ForgotPasswordController::class,'resetPassword']);
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::post('/refresh', [AuthController::class, 'refresh']);
            Route::get('/user-profile', [AuthController::class, 'userProfile']);     
                
        });



        // Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        //     return $request->user();
        // });
