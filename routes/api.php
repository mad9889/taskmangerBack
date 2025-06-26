<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::get('/users', [TaskController::class, 'searchUsers']);
    Route::get('/validate-token', function() {
    return ['valid' => auth()->check()];
    });

    Route::group(['prefix' => 'tasks'], function() {
    Route::get('/', [TaskController::class, 'index']);
    Route::post('/add', [TaskController::class, 'create']);
    Route::patch('/{task}/complete', [TaskController::class, 'complete']);
    Route::patch('/{task}/delete', [TaskController::class, 'delete']);
    Route::post('/add-task', [TaskController::class, 'create']);
    Route::post('tasks/{task}/participants', [TaskController::class, 'addParticipant']);
    Route::delete('tasks/{task}/participants', [TaskController::class, 'removeParticipant']);
});

Route::get('/notifications', [NotificationController::class, 'index']);
Route::post('/notification/enable', [NotificationController::class, 'enableNotifications']);
Route::post('/notification/disable', [NotificationController::class, 'disableNotifications']);
Route::get('/notification-status/{uid}', [NotificationController::class, 'status']);


Route::post('/change-password', [ProfileController::class, 'changePassword']);
Route::post('/upload-profile', [ProfileController::class, 'uploadProfile']);
});
