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
	$router->post('logout', ['middleware' => 'cors', 'uses' => 'AuthController@logout']);
	//refresh token
	$router->post('refreshToken', ['middleware' => 'cors', 'uses' => 'AuthController@refresh']);
	//get User Info Authenticated by JWT
	$router->post('me', ['middleware' => 'cors', 'uses' => 'AuthController@me']);
	
	//register
	$router->post('User/Register/', ['middleware' => 'cors', 'uses' => 'UserController@createUser']);

	//User
	$router->get('User/{iduser}', ['middleware' => 'cors', 'uses' => 'AuthController@me']);
	$router->post('User/{iduser}/Update/', ['middleware' => 'cors', 'uses' => 'UserController@updateData']);
	$router->post('User/{iduser}/UpdatePassword/', ['middleware' => 'cors', 'uses' => 'UserController@updatePassword']);
	$router->post('User/{iduser}/Updatephoto/', ['middleware' => 'cors', 'uses' => 'UserInfoController@updateProfilePhoto']);
	$router->get('User/{iduser}/delete', ['middleware' => 'cors', 'uses' => 'UserController@deleteUser']);	
	$router->post('User/{iduser}/setPassword', ['middleware' => 'cors', 'uses' => 'UserController@setPassword']);
	
	//password reset
	$router->post('/password/email', ['middleware' => 'cors', 'uses' => 'PasswordController@postEmail']);
	$router->post('/password/reset/{token}', ['middleware' => 'cors', 'uses' => 'PasswordController@postReset']);

	//check email for duplicate
	$router->post('/checkemail', ['middleware' => 'cors', 'uses' => 'UserController@findDuplicateEmail']);
	$router->post('/checknickname', ['middleware' => 'cors', 'uses' => 'UserController@findDuplicateNickname']);
});

/**
 * SNS routes
 */
$router->group(['prefix' => '/'], function($router){
	//web app
    $router->get('google', ['middleware' => 'cors', 'uses' => 'SnsController@redirectToGoogle']);		
	$router->get('google/callback', ['middleware' => 'cors', 'uses' => 'SnsController@googleCallback']);

	//mobile app
    $router->get('googleMobile', ['middleware' => 'cors', 'uses' => 'SnsController@redirectToGoogleMobile']);		
	$router->get('googleMobile/callback', ['middleware' => 'cors', 'uses' => 'SnsController@googleCallbackMobile']);

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

	$router->post('Content/Create', ['middleware' => 'cors', 'uses' => 'ContentController@createContent']);
	$router->post('Content/createTemporary', ['middleware' => 'cors', 'uses' => 'ContentController@createTemporaryContent']);	
	$router->get('Content/saveTempContent/{iduser}', ['middleware' => 'cors', 'uses' => 'ContentController@saveTempContent']);
	$router->post('Content/{idcontent}/Update', ['middleware' => 'cors', 'uses' => 'ContentController@updateContent']);

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
	$router->post('Reply/post/', ['middleware' => 'cors', 'uses' => 'ReplyController@postReply']);
	$router->post('Reply/delete/', ['middleware' => 'cors', 'uses' => 'ReplyController@deleteReply']);
	$router->get('Reply/count/{idcomment}', ['middleware' => 'cors', 'uses' => 'ReplyController@countReply']);
	$router->get('Reply/{idcomment}', ['middleware' => 'cors', 'uses' => 'ReplyController@loadReply']);

	//tag routes
	$router->get('contentTag', ['middleware' => 'cors', 'uses' => 'TagController@loadAllContentTag']);
});

/**
 * API Route for Subscription and Ads
 * 
 * @return $route
 */
$router->group(['prefix' => 'api/'], function($router)
{
	$router->post('Ads/New', ['middleware' => 'cors', 'uses' => 'AdvertiseController@newAds']);
	$router->post('Ads/Popular', ['middleware' => 'cors', 'uses' => 'AdvertiseController@popularAds']);

	//tag routes
	$router->get('adsTag', ['middleware' => 'cors', 'uses' => 'TagController@loadAlladsTag']);

	//Ads CRUD
	$router->post('Ads/create', ['middleware' => 'cors', 'uses' => 'AdvertiseController@createAds']);
	$router->post('Ads/update', ['middleware' => 'cors', 'uses' => 'AdvertiseController@updateAds']);	

	//Subscription, subscribe and unsubscribe
	$router->get('User/{iduser}/SubscriptionList', ['middleware' => 'cors', 'uses' => 'AdvertiseController@subscriptionList']);
	$router->get('User/{iduser}/SubscriptionHistory', ['middleware' => 'cors', 'uses' => 'AdvertiseController@subscriptionHistory']);
	$router->post('Ads/Subscribe', ['middleware' => 'cors', 'uses' => 'AdsSubscriptionController@adsSubscribe']);
	$router->post('Ads/Unsubscribe', ['middleware' => 'cors', 'uses' => 'AdsSubscriptionController@adsUnsubscribe']);
});

/**
 * API Route for banner
 * 
 * @return $route
 */
$router->group(['prefix' => 'api/'], function($router)
{
	$router->get('Banner', ['middleware' => 'cors', 'uses' => 'BannerController@readAllBanners']);
	$router->get('Banner/read/{idbanner}', ['middleware' => 'cors', 'uses' => 'BannerController@readBanner']);
	$router->get('Banner/active/', ['middleware' => 'cors', 'uses' => 'BannerController@activeBanner']);
	$router->post('Banner/Search/', ['middleware' => 'cors', 'uses' => 'BannerController@searchBanner']);	
	$router->post('Banner/create/', ['middleware' => 'cors', 'uses' => 'BannerController@createBanner']);
	$router->post('Banner/update/{idbanner}', ['middleware' => 'cors', 'uses' => 'BannerController@updateBanner']);
});

/**
 * API Route for Advertiser
 * 
 * @return $route
 */
$router->group(['prefix' => 'api/'], function($router)
{
	//advertiser banner
	$router->get('Advertiser/{idadvertiser}/Banner', ['middleware' => 'cors', 'uses' => 'AdvertiserBannerController@bannerListbyAdvertiser']);
	$router->get('Advertiser/{idadvertiser}/Banner/{idbanner}', ['middleware' => 'cors', 'uses' => 'AdvertiserBannerController@loadSingleBanner']);
	$router->post('Advertiser/{idadvertiser}/Banner/create', ['middleware' => 'cors', 'uses' => 'AdvertiserBannerController@createAdvertiserBanner']);
	$router->post('Advertiser/{idadvertiser}/Banner/{idbanner}/update', ['middleware' => 'cors', 'uses' => 'AdvertiserBannerController@updateAdvertiserBanner']);
	$router->get('Advertiser/{idadvertiser}/Banner/{idbanner}/delete', ['middleware' => 'cors', 'uses' => 'AdvertiserBannerController@deleteAdvertiserBanner']);

	//advertiser
	$router->get('Advertiser/{idadvertiser}', ['middleware' => 'cors', 'uses' => 'AdvertiserController@advertiserInfo']);
	$router->get('Advertiser/{idadvertiser}/Ads/{idads}', ['middleware' => 'cors', 'uses' => 'AdvertiseController@readAds']);

	//tag routes
	$router->get('advertiserTag', ['middleware' => 'cors', 'uses' => 'TagController@loadAlladvertiserTag']);
	
});

/**
 * API Route for Redis
 * 
 * @return $route
 */
$router->group(['prefix' => 'api/'], function($router)
{
	$router->get('totalreward', ['middleware' => 'cors', 'uses' => 'RedisController@totalreward']);
	$router->get('loadContentTag/{idcontent}', ['middleware' => 'cors', 'uses' => 'RedisController@loadContentTag']);
	$router->get('loadAdsTag/{idads}', ['middleware' => 'cors', 'uses' => 'RedisController@loadAdsTag']);
});

/**
 * API Route for refferal
 * 
 * @return $route
 */
$router->group(['prefix' => 'api/'], function($router)
{
	$router->get('refferal', ['middleware' => 'cors', 'uses' => 'RefferalController@loadAllRefferal']);
	$router->post('refferal/register', ['middleware' => 'cors', 'uses' => 'RefferalController@insertRefferal']);
});

