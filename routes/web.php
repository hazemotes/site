<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('home');
});

Route::get('/projects', function(){
    return view('projects.projects');
});

Route::get('/projects/snake', function(){
    return view('projects.snake');
});

Route::get('/projects/simon', function(){
    return view('projects.simon');
});

Route::get('/projects/heatmap', function(){
    return view('projects.heatmap');
});

Route::get('/blog', function (){
   return view('blog.blog');
});

Route::get('/fiction', function (){
   return view('fiction.fiction');
});

Route::get('/fiction/christmas', function (){
    return view('fiction.christmas');
});

Route::get('/fiction/lucky', function (){
    return view('fiction.lucky');
});

Route::get('/fiction/weeds', function (){
    return view('fiction.weeds');
});

Route::post('/projects/movies/register', 'MovieController@register');
Route::post('/projects/movies/login', 'MovieController@login');
Route::post('/projects/movies/save', 'MovieController@save');
Route::get('/projects/movies/list', 'MovieController@getList');
Route::get('/projects/movies', 'MovieController@index');
