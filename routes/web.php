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

$router->get('/', function () use ($router) {
    return $router->app->version();
});

/**
 * API Route for User
 * 
 * @return $route
 */
$router->group(['prefix' => 'api/'], function($router)
{
	$router->get('login/','UserController@authenticate');
	$router->get('User/','UserController@readUsers');
	$router->get('User/{id}','UserController@getUser');
	$router->post('User/create','UserController@createUser');
	$router->post('User/update/{id}','UserController@updateUser');
	$router->post('User/updatePassword/{id}','UserController@updatePassword');
	$router->post('User/Search/','UserController@searchUser');
});

/**
 * API Route for Banner
 * 
 * @return $route
 */
$router->group(['prefix' => 'api/'], function($router)
{
	$router->get('Banner/','BannerController@readAllBanners');
	$router->post('Banner/Search/','BannerController@searchBanner');
	$router->get('Banner/active/','BannerController@activeBanner');
	$router->post('Banner/create/','BannerController@createBanner');
	$router->post('Banner/update/{id}','BannerController@updateBanner');
});

/**
 * API Route for Content
 * 
 * @return $route
 */
$router->group(['prefix' => 'api/'], function($router)
{
	$router->get('Content/','ContentController@readAllContent');
	$router->get('Contentlist/','ContentController@contentPaginate');
	$router->get('Content/{id}','ContentController@contentReadOne');
	$router->post('Content/Search','ContentController@searchContent');
	$router->post('Content/create','ContentController@createContent');
	$router->post('Content/update/{id}','ContentController@updateContent');
});
