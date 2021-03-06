 <?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    //return view('welcome');
    return redirect('/blog');
});

Route::get('blog','BlogController@index');
Route::get('blog/{slug}','BlogController@showPost');

// Admin area
Route::get('admin', function () {
  return redirect('/admin/post');
});
$router->group([
  'namespace' => 'Admin',
  'middleware' => 'auth',
], function () {
  Route::resource('admin/post', 'PostController', ['except' => 'show']);
  Route::resource('admin/tag', 'TagController', ['except' => 'show']);
  Route::get('admin/upload', 'UploadController@index');
  Route::post('admin/upload/file', 'UploadController@uploadFile');
  Route::delete('admin/upload/file', 'UploadController@deleteFile');
  Route::post('admin/upload/folder', 'UploadController@createFolder');
  Route::delete('admin/upload/folder', 'UploadController@deleteFolder');
});

// Logging in and out
Route::get('/auth/login', 'Auth\AuthController@getLogin');
Route::post('/auth/login', 'Auth\AuthController@postLogin');
Route::get('/auth/logout', 'Auth\AuthController@getLogout');
