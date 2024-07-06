<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrganisationController;


Route::group(['prefix' => 'auth'], function () {

    Route::post('/register', [AuthController::class, 'signUp']);

    Route::post('/login', [AuthController::class, 'signIn']);


});

Route::group(['middleware' => 'auth:api'], function(){

    Route::group(['prefix' => 'users'], function(){

        Route::get('/{id}', [UserController::class, 'index']);


    });



    Route::group(['prefix' => 'organisations'], function(){

        Route::get('/', [UserController::class, 'getUserOrganizations']);

        Route::post('/', [OrganisationController::class, 'store']);

        Route::post('/{orgId}', [OrganisationController::class, 'addUser']);

        Route::get('/{id}', [OrganisationController::class, 'index']);

    });







});
