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
	//login
	$router->post('login', ['middleware' => 'cors', 'uses' => 'AuthController@login']);
	//logout
	$router->post('logout', ['middleware' => 'auth', 'uses' => 'AuthController@logout']);
	//refresh token
	$router->post('refreshToken', ['middleware' => 'auth', 'uses' => 'AuthController@refresh']);
	//get User Info Authenticated by JWT
	$router->post('me', ['middleware' => 'auth', 'uses' => 'AuthController@me']);
	
	//register
	$router->post('User/Register/', ['middleware' => 'cors', 'uses' => 'UserController@createUser']);

	//User
	$router->get('User/{iduser}', ['middleware' => 'auth', 'uses' => 'AuthController@me']);
	$router->post('User/{iduser}/Update/', ['middleware' => 'auth', 'uses' => 'UserController@updateData']);
	$router->post('User/{iduser}/UpdatePassword/', ['middleware' => 'auth', 'uses' => 'UserController@updatePassword']);
	$router->post('User/{iduser}/Updatephoto/', ['middleware' => 'auth', 'uses' => 'UserInfoController@updateProfilePhoto']);
	$router->get('User/{iduser}/delete', ['middleware' => 'auth', 'uses' => 'UserController@deleteUser']);	
	$router->post('User/{iduser}/setPassword', ['middleware' => 'cors', 'uses' => 'UserController@setPassword']);
	
	//password reset
	$router->post('/password/email', ['middleware' => 'auth', 'uses' => 'PasswordController@postEmail']);
	$router->post('/password/reset/{token}', ['middleware' => 'auth', 'uses' => 'PasswordController@postReset']);
});

/**
 * API Route for Contents
 * 
 * @return $route
 */
$router->group(['prefix' => 'api/'], function($router)
{	
	$router->get('Content/New', ['middleware' => 'cors', 'uses' => 'ContentController@newContent']);
	$router->get('Content/Hot', ['middleware' => 'cors', 'uses' => 'ContentController@hotContent']);
	$router->get('Content/Trending', ['middleware' => 'cors', 'uses' => 'ContentController@trendingContent']);

	$router->get('Content/{idcontent}', ['middleware' => 'cors', 'uses' => 'ContentController@contentReadOne']);

	$router->post('Content/Create', ['middleware' => 'auth', 'uses' => 'ContentController@createContent']);
	$router->post('Content/createTemporary', ['middleware' => 'auth', 'uses' => 'ContentController@createTemporaryContent']);	
	$router->get('Content/saveTempContent/{iduser}', ['middleware' => 'auth', 'uses' => 'ContentController@saveTempContent']);
	$router->post('Content/{idcontent}/Update', ['middleware' => 'auth', 'uses' => 'ContentController@updateContent']);

	//like routes
	$router->post('Content/like/', ['middleware' => 'cors', 'uses' => 'LikeController@like']);
	$router->post('Content/dislike/', ['middleware' => 'cors', 'uses' => 'LikeController@dislike']);
	$router->get('Content/{idcontent}/countLike/', ['middleware' => 'cors', 'uses' => 'LikeController@countLike']);
	$router->get('Content/{idcontent}/countDislike/', ['middleware' => 'cors', 'uses' => 'LikeController@countDislike']);

	//comment routes
	$router->post('Comment/post/', ['middleware' => 'cors', 'uses' => 'CommentController@postComment']);
	$router->post('Comment/delete/', ['middleware' => 'cors', 'uses' => 'CommentController@deleteComment']);
	$router->get('Comment/count/{idcontent}', ['middleware' => 'cors', 'uses' => 'CommentController@countComment']);
	$router->get('Comment/{idcontent}', ['middleware' => 'cors', 'uses' => 'CommentController@loadComment']);

	//reply routes
	$router->post('Reply/post/', ['middleware' => 'auth', 'uses' => 'ReplyController@postReply']);
	$router->post('Reply/delete/', ['middleware' => 'auth', 'uses' => 'ReplyController@deleteReply']);
	$router->get('Reply/count/{idcomment}', ['middleware' => 'auth', 'uses' => 'ReplyController@countReply']);
	$router->get('Reply/{idcomment}', ['middleware' => 'auth', 'uses' => 'ReplyController@loadReply']);
});

/**
 * API Route for Subscription
 * 
 * @return $route
 */
$router->group(['prefix' => 'api/'], function($router)
{
	$router->get('Ads/New/', ['middleware' => 'cors', 'uses' => 'AdvertiseController@newAds']);
	$router->get('Ads/Popular/', ['middleware' => 'cors', 'uses' => 'AdvertiseController@popularAds']);
});

