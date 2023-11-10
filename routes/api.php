<?php

use App\Http\Controllers\GetDetailsProcess;
use App\Http\Controllers\MoodleHubProccessor;
use App\Http\Controllers\ProcessHubController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => ['api']], function ($router) {
    Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);
    Route::post('/register', [App\Http\Controllers\AuthController::class, 'register']);

    Route::post('/users', [App\Http\Controllers\UserController::class, 'update']);
    // Route::post('/login/');
});

/**
 * For WHMCS Router and actions
 */
Route::post('/process/{param}', [ProcessHubController::class, 'process']);
Route::post('/get-product/{id}', [ProcessHubController::class, 'getProductById']);
Route::get('/get-all-product', [GetDetailsProcess::class, 'getProducts']);
Route::get('/get-all-client', [GetDetailsProcess::class, 'getClient']);

/**
 * Moodle Resource Router
 */
Route::get('/get-all-course', [MoodleHubProccessor::class, 'getCourse']);

Route::get('/get-course/{id}', [MoodleHubProccessor::class, 'getCourseById']);

/**
 * Login register with moodle
 */
Route::post('/login-moodle', [MoodleHubProccessor::class, 'login']);

Route::post('/enrole-moodle', [MoodleHubProccessor::class, 'enrollUserInCourse']);
Route::post('/register-moodle', [MoodleHubProccessor::class, 'createUser']);
Route::get('/get-all-enrolment' , [MoodleHubProccessor::class, 'getAllEnrollmentMethods']);
