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

$router->group(['prefix' => 'api/'], function($router)
{
	$router->get('login/','UserController@authenticate');
	$router->get('User/','UserController@readUsers');
	$router->get('User/{id}','UserController@getUser');
	$router->post('User/createUser','UserController@createUser');
	$router->post('User/updateUser/{id}','UserController@updateUser');
	$router->post('User/Search/','UserController@searchUser');
});

$router->group(['prefix' => 'api/'], function($router)
{
	$router->get('Banner/','BannerController@readAllBanners');
	$router->post('Banner/Search/','BannerController@searchBanner');
	$router->get('Banneractive/','BannerController@activeBanner');
	$router->post('Banner/createBanner/','BannerController@createBanner');
	$router->post('Banner/updateBanner/{id}','BannerController@updateBanner');
});

$router->group(['prefix' => 'api/'], function($router)
{
	$router->get('Content/','ContentController@readAllContent');
	$router->get('Contentlist/','ContentController@contentPaginate');
});

//FB
$router->get('/redirectfb', 'SocialAuthController@redirectFB');
$router->get('/callbackfb', 'SocialAuthController@callbackFB');
// google+
$router->get('/redirectg', 'SocialAuthController@redirectG');
$router->get('/callbackg', 'SocialAuthController@callbackG');
