<?php

use Illuminate\Support\Facades\Route;

Route::view('/error', 'error');
Route::group(['middleware' => ['guest']], function() {
    Route::match(['get','post'],'/login','AuthController@login')->name('login');
    Route::match(['get','post'],'/forgot-password','AuthController@forgot_password')->name('forgot-password');
    Route::match(['get','post'],'/reset-password','AuthController@reset_password')->name('reset-password');
});

Route::group(['middleware' => ['auth', 'check-access', 'log-activity']], function() {
    Route::post('/logout', 'AuthController@logout')->name('logout');
    Route::get('/', 'DashboardController@index');
    Route::get('/recent-helpdesk-requests', 'DashboardController@recent_helpdesk_requests');
    Route::get('/recent-item-requests', 'DashboardController@recent_item_requests');
    Route::get('/staff-activities', 'DashboardController@activities');

    Route::get('/profile', 'StaffController@profile');
    Route::get('/reports', 'ReportController@index');
    Route::get('/activities', 'ActivityLogController@index');
    Route::post('/reports/generate', 'ReportController@generate');
    Route::get('/get-record/{table}/{column}/{id}', 'OthersController@get_record')->name('get-record');
    Route::get('/get-options/{type}/{id}', 'OthersController@get_options');
    Route::post('/item-requests/authorize', 'ItemRequestController@authorize_request');

    Route::match(['get','post'],'/change-password','AuthController@change_password');
    Route::apiResources([
        'ddds' => 'DDDController',
        'offices' => 'OfficeController',
        'staff' => 'StaffController',
        'items' => 'ItemController',
        'inventory' => 'InventoryController',
        'item-requests' => 'ItemRequestController',
        'item-distributions' => 'ItemDistributionController',
        'helpdesk-supports' => 'HelpdeskSupportController',
        'helpdesk-requests' => 'HelpdeskRequestController'
    ]);
});
