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
	$router->get('login/', ['middleware' => 'cors', 'uses' => 'UserController@authenticate']);
	$router->get('User/', ['middleware' => 'cors', 'uses' => 'UserController@readUsers']);
	$router->get('User/{id}', ['middleware' => 'cors', 'uses' => 'UserController@getUser']);
	$router->post('User/create', ['middleware' => 'cors', 'uses' => 'UserController@createUser']);
	$router->post('User/update/{id}', ['middleware' => 'cors', 'uses' => 'UserController@updateUser']);
	$router->post('User/updatePassword/{id}', ['middleware' => 'cors', 'uses' => 'UserController@updatePassword']);
	$router->post('User/Search/', ['middleware' => 'cors', 'uses' => 'UserController@searchUser']);
	$router->post('User/delete/{id}', ['middleware' => 'cors', 'uses' => 'UserController@deleteUser']);	
});

/**
 * API Route for Banner
 * 
 * @return $route
 */
$router->group(['prefix' => 'api/'], function($router)
{
	$router->get('Banner/', ['middleware' => 'cors', 'uses' => 'BannerController@readAllBanners']);
	$router->post('Banner/Search/', ['middleware' => 'cors', 'uses' => 'BannerController@searchBanner']);
	$router->get('Banner/active/', ['routeMiddleware' => 'cors', 'uses' => 'BannerController@activeBanner']);
	$router->post('Banner/create/', ['middleware' => 'cors', 'uses' => 'BannerController@createBanner']);
	$router->post('Banner/update/{id}', ['middleware' => 'cors', 'uses' => 'BannerController@createBanner']);
});

/**
 * API Route for Content
 * 
 * @return $route
 */
$router->group(['prefix' => 'api/'], function($router)
{
	$router->get('Content/', ['middleware' => 'cors', 'uses' => 'ContentController@readAllContent']);
	$router->get('Contentlist/', ['middleware' => 'cors', 'uses' => 'ContentController@contentPaginate']);
	$router->get('Content/{id}', ['middleware' => 'cors', 'uses' => 'ContentController@contentReadOne']);
	$router->post('Content/Search', ['middleware' => 'cors', 'uses' => 'ContentController@searchContent']);
	$router->post('Content/create', ['middleware' => 'cors', 'uses' => 'ContentController@createContent']);
	$router->post('Content/update/{id}', ['middleware' => 'cors', 'uses' => 'ContentController@updateContent']);
	$router->post('Content/delete/{id}', ['middleware' => 'cors', 'uses' => 'ContentController@deleteContent']);
});

/**
 * API Route for Likes
 * 
 * @return $route
 */
$router->group(['prefix' => 'api/'], function($router)
{
	$router->get('Likes/','LikeController@all');
});
