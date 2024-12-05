<?php

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\UserController;
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

// Add Employee
Route::post('/add/employees', [ApiController::class, 'AddEmployee']);

// List Employee
Route::get('/list/employees', [ApiController::class, 'ListEmployee']);

// Single Employee
Route::get('/single/employees/{id}', [ApiController::class, 'SingleEmployee']);

// Update Employee
Route::post('/update/employees/{id}', [ApiController::class, 'UpdateEmployee']);

// Delete Employee
Route::get('/delete/employees/{id}', [ApiController::class, 'DeleteEmployee']);




// Register route
Route::post('/register', [UserController::class, 'Register']);

// Login route
Route::post('/login', [UserController::class, 'Login']);

Route::group([
    "middleware" => ["auth:api"]
], function(){
    Route::get("/profile", [UserController::class, 'Profile']);
    Route::get("/refresh/token", [UserController::class, 'RefreshToken']);
    Route::get("/logout", [UserController::class, 'Logout']);

    // Course routes
    Route::post("/course/enroll", [CourseController::class, 'CourseEnroll']);
    Route::get("/list/course", [CourseController::class, 'ListCourse']);
    Route::get("/delete/course/{id}", [CourseController::class, 'DeleteCourse']);
});
