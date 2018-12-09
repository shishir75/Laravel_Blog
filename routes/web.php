<?php

	use App\Category;
	use Illuminate\Support\Facades\Route;
	use Illuminate\Support\Facades\View;


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

Route::get('/', 'HomeController@index')->name('home');

// subscriber route
Route::post('subscriber', 'SubscriberController@store')->name('subscriber.store');


Auth::routes();


// for favorite
Route::group(['middleware' => ['auth']], function (){
	Route::post('favorite/{post}/add', 'FavoriteController@add')->name('post.favorite');

	// for comment
	Route::post('comment/{post}', 'CommentController@store')->name('comment.store');

});

// for post details
Route::get('post/{slug}', 'PostController@details')->name('post.details');

// for all posts
Route::get('posts', 'PostController@index')->name('posts.index');

// post by Category
Route::get('category/{slug}', 'PostController@postByCategory')->name('category.posts');

// post by Tag
Route::get('tag/{slug}', 'PostController@postByTag')->name('tag.posts');

// search
Route::get('search', 'SearchController@search')->name('search');

// for author profile
Route::get('profile/{username}', 'AuthorController@profile')->name('author.profile');



// For admin
Route::group(['as'=>'admin.' ,'prefix' => 'admin', 'namespace'=> 'Admin', 'middleware'=> ['auth', 'admin']], function(){

	Route::get('dashboard', 'DashboardController@index')->name('dashboard');

	Route::get('settings', 'SettingsController@index')->name('settings');
	Route::put('profile-update', 'SettingsController@profileUpdate')->name('profile.update');
	Route::put('password-update', 'SettingsController@passwordUpdate')->name('password.update');

	Route::resource('tag', 'TagController');
	Route::resource('category', 'CategoryController');
	Route::resource('post', 'PostController');

	Route::get('pending/post', 'PostController@pending')->name('post.pending');
	Route::put('/post/{id}/approve', 'PostController@approval')->name('post.approve');

	Route::get('/subscriber', 'SubscriberController@index')->name('subscriber.index');
	Route::delete('/subscriber/{id}', 'SubscriberController@destroy')->name('subscriber.destroy');

	Route::get('/favorite', 'FavoriteController@index')->name('favorite.index');

	// admin dashboard comments
	Route::get('comments', 'CommentController@index')->name('comments.index');
	Route::delete('comments/{id}', 'CommentController@destroy')->name('comments.destroy');

	// author section
	Route::get('authors', 'AuthorController@index')->name('authors.index');
	Route::delete('author/{id}', 'AuthorController@destroy')->name('author.destroy');

});


// for author
Route::group(['as'=>'author.','prefix' =>'author', 'namespace'=>'Author','middleware'=>['auth','author']],function (){

	Route::get('/dashboard', 'DashboardController@index')->name('dashboard');

	Route::get('settings', 'SettingsController@index')->name('settings');
	Route::put('profile-update', 'SettingsController@profileUpdate')->name('profile.update');
	Route::put('password-update', 'SettingsController@passwordUpdate')->name('password.update');

	Route::resource('post', 'PostController');

	Route::get('/favorite', 'FavoriteController@index')->name('favorite.index');

	// author dashboard comments
	Route::get('comments', 'CommentController@index')->name('comments.index');
	Route::delete('comments/{id}', 'CommentController@destroy')->name('comments.destroy');
});

// view composer
View::composer('layouts.frontend.partial.footer', function ($view){
	$categories = Category::all();
	$view->with('categories', $categories);
});








