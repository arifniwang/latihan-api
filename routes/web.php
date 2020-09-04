<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/
use Illuminate\Support\Str;

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('/key', function(){
    $key = Str::random(32);
    return $key;
});


// API METHOD


// auth
$router->post('/register', 'AuthController@postRegister');
$router->post('/login', 'AuthController@postLogin');

// note
$router->post('/note/save', 'NoteController@postSave');
$router->get('/note/list', 'NoteController@getList');
$router->post('/note/update', 'NoteController@postUpdate');
$router->post('/note/delete', 'NoteController@postDelete');
