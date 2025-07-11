<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-db', function() {
    try {
        DB::connection()->getPdo();
        return "DB connected successfully!";
    } catch (\Exception $e) {
        return "DB connection failed: " . $e->getMessage();
    }
});
