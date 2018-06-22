<?php

	require_once 'GoogleLogin.php';
	include_once '../config/DatabaseMysql.php';
	include_once '../models/Users.php';
	include_once '../class/PasswordEncrypt.php';

class GoogleCallback {

	private $googleLogin;


	public function __construct() {

		//run authentication
		$this->getAuth();
	}

	public function getAuth() {

		$this->googleLogin  = new GoogleLogin();

		//instantiate database and User Object
		$databaseMysql = new DatabaseMysql();
		$dbmysql = $databaseMysql->connect();
		$user = new Users($dbmysql);
		$hash = new PasswordEncrypt();

		//check access token
		if (isset($_SESSION['access_token']))
			$this->googleLogin->gClient->setAccessToken($_SESSION['access_token']);
		else if (isset($_GET['code'])) {
			$token = $this->googleLogin->gClient->fetchAccessTokenWithAuthCode($_GET['code']);
			$_SESSION['access_token'] = $token;
		} else {
			header('Location: ../index.php');
			exit();
		}

		//instantiate Google_Service_Oauth2
		$oAuth = new Google_Service_Oauth2($this->googleLogin->gClient);
		$userData = $oAuth->userinfo_v2_me->get();

		//check if the user already registered via SNS
		$stmt = $user->validateDuplicateEmail($userData['email']);

		//if email is not in the DB then register else redirect
		if($stmt['count'] == 0 ){

			//get raw user password
			//$data = json_decode(file_get_contents("php://input"));

			//delete this.
			$password = 'sample';

			//hash the password data using bcrypt
			//$data->password = $hash->hash($data->password);

			//delete this
			$password = $hash->hash($password);

			// bind data
			$user->first_name = $userData['givenName'];
			$user->last_name = $userData['familyName'];
			$user->email = $userData['email'];
			$user->password = $password;
			$user->snsProviderName = 'Google';
			$user->snsProviderId = $userData['id'];

			//create new User and load data
			if($user->createSNSUser()) {
				$_SESSION['id'] = $userData['id'];
				$_SESSION['email'] = $userData['email'];
				$_SESSION['gender'] = $userData['gender'];
				$_SESSION['picture'] = $userData['picture'];
				$_SESSION['familyName'] = $userData['familyName'];
				$_SESSION['givenName'] = $userData['givenName'];
			} else {
				echo json_encode(
					array('message' => 'User Not Created')
				);
			}    
		} else {
			//load user data
			$_SESSION['id'] = $userData['id'];
			$_SESSION['email'] = $userData['email'];
			$_SESSION['gender'] = $userData['gender'];
			$_SESSION['picture'] = $userData['picture'];
			$_SESSION['familyName'] = $userData['familyName'];
			$_SESSION['givenName'] = $userData['givenName'];
		}

		header('Location: ../users.php');
		exit();
	}

}

$new = new GoogleCallback();
var_dump($new);



	// require_once "config.php";

	// if (isset($_SESSION['access_token']))
	// 	$gClient->setAccessToken($_SESSION['access_token']);
	// else if (isset($_GET['code'])) {
	// 	$token = $gClient->fetchAccessTokenWithAuthCode($_GET['code']);
	// 	$_SESSION['access_token'] = $token;
	// } else {
	// 	header('Location: login.php');
	// 	exit();
	// }

	// $oAuth = new Google_Service_Oauth2($gClient);
	// $userData = $oAuth->userinfo_v2_me->get();

	// $_SESSION['id'] = $userData['id'];
	// $_SESSION['email'] = $userData['email'];
	// $_SESSION['gender'] = $userData['gender'];
	// $_SESSION['picture'] = $userData['picture'];
	// $_SESSION['familyName'] = $userData['familyName'];
	// $_SESSION['givenName'] = $userData['givenName'];

	// header('Location: index.php');
	// exit();