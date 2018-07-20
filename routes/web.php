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
	//return $router->app->version();
	return view('sample');
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
	$router->post('User/create/', ['middleware' => 'cors', 'uses' => 'UserController@createUser']);
	$router->get('User/{id}', ['middleware' => 'cors', 'uses' => 'UserController@getUser']);	
	$router->post('User/update/{id}', ['middleware' => 'cors', 'uses' => 'UserController@updateUser']);
	$router->post('User/updateUserInfo/', ['middleware' => 'cors', 'uses' => 'UserinfoController@updateUserInfo']);
	$router->post('User/updatePassword/{id}', ['middleware' => 'cors', 'uses' => 'UserController@updatePassword']);
	$router->post('User/Search/', ['middleware' => 'cors', 'uses' => 'UserController@searchUser']);
	$router->post('User/delete/{id}', ['middleware' => 'cors', 'uses' => 'UserController@deleteUser']);	
	$router->post('User/setPassword/{id}', ['middleware' => 'cors', 'uses' => 'UserController@savePassword']);	
});

/**
 * API Route for Banner
 * 
 * @return $route
 */
$router->group(['prefix' => 'api/'], function($router)
{
	$router->get('Banner/', ['middleware' => 'cors', 'uses' => 'BannerController@readAllBanners']);
	$router->get('Banner/read/{id}', ['middleware' => 'cors', 'uses' => 'BannerController@readBanner']);
	$router->get('Banner/active/', ['middleware' => 'cors', 'uses' => 'BannerController@activeBanner']);
	$router->post('Banner/Search/', ['middleware' => 'cors', 'uses' => 'BannerController@searchBanner']);	
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
	$router->get('Content/Best/', ['middleware' => 'cors', 'uses' => 'ContentController@bestContent']);
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
	$router->post('like/', ['middleware' => 'cors', 'uses' => 'LikeController@like']);
	$router->post('dislike/', ['middleware' => 'cors', 'uses' => 'LikeController@dislike']);
	$router->get('contentLike/{id}', ['middleware' => 'cors', 'uses' => 'LikeController@contentLike']);
	$router->get('contentDislike/{id}', ['middleware' => 'cors', 'uses' => 'LikeController@contentDislike']);
});

/**
 * API Route for Advertise
 * 
 * @return $route
 */
$router->group(['prefix' => 'api/'], function($router)
{
	$router->get('Ads/', ['middleware' => 'cors', 'uses' => 'AdvertiseController@adsList']);
	$router->get('Ads/Best/', ['middleware' => 'cors', 'uses' => 'AdvertiseController@bestAds']);
	$router->get('Ads/{id}', ['middleware' => 'cors', 'uses' => 'AdvertiseController@readAds']);
	$router->post('Ads/create/', ['middleware' => 'cors', 'uses' => 'AdvertiseController@createAds']);
	$router->post('Ads/update/{id}', ['middleware' => 'cors', 'uses' => 'AdvertiseController@updateAds']);
});

/**
 * API Route for Advertiser
 * 
 * @return $route
 */
$router->group(['prefix' => 'api/'], function($router)
{
	$router->get('Advertiser/', ['middleware' => 'cors', 'uses' => 'AdvertiserController@advertiserList']);
	$router->get('Advertiser/{id}', ['middleware' => 'cors', 'uses' => 'AdvertiserController@readAdvertiser']);
	$router->post('Advertiser/create/', ['middleware' => 'cors', 'uses' => 'AdvertiserController@createAdvertiser']);
	$router->post('Advertiser/insertPassword/{id}', ['middleware' => 'cors', 'uses' => 'AdvertiserController@insertPassword']);
	$router->post('Advertiser/update/{id}', ['middleware' => 'cors', 'uses' => 'AdvertiserController@updateAdvertiser']);
	//Ads list of Advertiser
	$router->get('Advertiser/{id}/Adslist/', ['middleware' => 'cors', 'uses' => 'AdvertiseController@adsListbyAdvertiser']);
	//Banner list of Advertiser
	$router->get('Advertiser/{id}/Banner/', ['middleware' => 'cors', 'uses' => 'AdvertiserBannerController@bannerListbyAdvertiser']);
	
});

/**
 * API Route for Subcriptions
 * 
 * @return $route
 */
$router->group(['prefix' => 'api/'], function($router)
{
	$router->post('Subscribe/', ['middleware' => 'cors', 'uses' => 'SubscriptionController@subscribe']);
	$router->post('Unsubscribe/', ['middleware' => 'cors', 'uses' => 'SubscriptionController@unsubscribe']);
	$router->get('Subcriptionllist/{iduser}', ['middleware' => 'cors', 'uses' => 'SubscriptionController@subscriptionList']);
	$router->get('Recent/{iduser}', ['middleware' => 'cors', 'uses' => 'SubscriptionController@recentSubscriptionList']);
	$router->get('Subscription/History/{iduser}', ['middleware' => 'cors', 'uses' => 'SubscriptionController@subscriptionHistory']);
});

/**
 * API Route for comments
 * 
 * @return $route
 */
$router->group(['prefix' => 'api/'], function($router)
{
	$router->post('Comment/post/', ['middleware' => 'cors', 'uses' => 'CommentController@postComment']);
	$router->post('Comment/delete/', ['middleware' => 'cors', 'uses' => 'CommentController@deleteComment']);
	$router->get('Comment/count/{id}', ['middleware' => 'cors', 'uses' => 'CommentController@countComment']);
	$router->get('Comment/{id}', ['middleware' => 'cors', 'uses' => 'CommentController@loadComment']);
});

/**
 * API Route for reply
 * 
 * @return $route
 */
$router->group(['prefix' => 'api/'], function($router)
{
	$router->post('Reply/post/', ['middleware' => 'cors', 'uses' => 'ReplyController@postReply']);
	$router->post('Reply/delete/', ['middleware' => 'cors', 'uses' => 'ReplyController@deleteReply']);
	$router->get('Reply/count/{id}', ['middleware' => 'cors', 'uses' => 'ReplyController@countReply']);
	$router->get('Reply/{id}', ['middleware' => 'cors', 'uses' => 'ReplyController@loadReply']);
});

/**
 * API Route for userinfo mongodb
 * 
 * @return $route
 */
$router->group(['prefix' => 'api/'], function($router)
{
	$router->get('Userinfo/', ['middleware' => 'cors', 'uses' => 'UserinfoController@all']);
	$router->get('Userinfo/try/{id}', ['middleware' => 'cors', 'uses' => 'UserinfoController@try']);
});

$router->group(['prefix' => '/'], function($router)
{
	$router->get('google', ['middleware' => 'cors', 'uses' => 'SnsController@redirectToGoogle']);		
	$router->get('google/callback', ['middleware' => 'cors', 'uses' => 'SnsController@googleCallback']);
	$router->get('/facebook/login', function(SammyK\LaravelFacebookSdk\LaravelFacebookSdk $fb) {
		$login_link = $fb
            ->getRedirectLoginHelper()
            ->getLoginUrl('https://uth702bpo.com/callback/facebook', ['email']);
    
    		echo '<a href="' . $login_link . '">Log in with Facebook</a>';
	});
	$router->get('/facebook/callback', function(SammyK\LaravelFacebookSdk\LaravelFacebookSdk $fb)
	{
		// Obtain an access token.
		try {
			$token = $fb->getAccessTokenFromRedirect();
		} catch (Facebook\Exceptions\FacebookSDKException $e) {
			dd($e->getMessage());
		}

		// Access token will be null if the user denied the request
		// or if someone just hit this URL outside of the OAuth flow.
		if (! $token) {
			// Get the redirect helper
			$helper = $fb->getRedirectLoginHelper();

			if (! $helper->getError()) {
				abort(403, 'Unauthorized action.');
			}

			// User denied the request
			dd(
				$helper->getError(),
				$helper->getErrorCode(),
				$helper->getErrorReason(),
				$helper->getErrorDescription()
			);
		}

		if (! $token->isLongLived()) {
			// OAuth 2.0 client handler
			$oauth_client = $fb->getOAuth2Client();

			// Extend the access token.
			try {
				$token = $oauth_client->getLongLivedAccessToken($token);
			} catch (Facebook\Exceptions\FacebookSDKException $e) {
				dd($e->getMessage());
			}
		}

		$fb->setDefaultAccessToken($token);

		// Save for later
		Session::put('fb_user_access_token', (string) $token);

		// Get basic info on the user from Facebook.
		try {
			$response = $fb->get('/me?fields=id,name,email');
		} catch (Facebook\Exceptions\FacebookSDKException $e) {
			dd($e->getMessage());
		}

		// Convert the response to a `Facebook/GraphNodes/GraphUser` collection
		$facebook_user = $response->getGraphUser();

		// Create the user if it does not exist or update the existing entry.
		// This will only work if you've added the SyncableGraphNodeTrait to your User model.
		$user = App\User::createOrUpdateGraphNode($facebook_user);

		// Log the user into Laravel
		Auth::login($user);

		return redirect('/')->with('message', 'Successfully logged in with Facebook');
	});
});


