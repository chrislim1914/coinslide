<?php
    
    require_once __DIR__."/config/GoogleLogin.php";
    require_once __DIR__."/config/facebookLogin.php";
    require_once __DIR__.'/config/DatabaseMysql.php';

	if (isset($_SESSION['access_token'])) {
		header('Location: users.php');
		exit();
	}

    $g = new GoogleLogin();
    $g->googleSetAuthentication();
    $loginURL = $g->gClient->createAuthUrl();
    
    $redirectURL = "https://localhost:8900/config/facebookCallback.php";
	$permissions = ['email'];
	$fblogin = $helper->getLoginUrl($redirectURL, $permissions);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login With Google</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
</head>
<body>
    <div class="container" style="margin-top: 100px">
        <div class="row justify-content-center">
            <div class="col-md-6 col-offset-3" align="center">

                <img src="coinslide-sm.png"><br><br>

                <form >
                    <input placeholder="Email..." name="email" disable="yes" class="form-control"><br>
                    <input type="password" disable="yes" placeholder="Password..." name="password" class="form-control"><br>
                    <input type="submit" value="Log In" class="btn btn-primary">
                    <input type="button" onclick="window.location = '<?php echo $loginURL ?>';" value="Log In With Google" class="btn btn-danger">
                    <input type="button" onclick="window.location = '<?php echo $fblogin ?>';" value="Log In With Facebook" class="btn btn-primary">
                </form>

            </div>
        </div>
    </div>
    <?php
   
    ?>
</body>
</html>