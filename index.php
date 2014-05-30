<?php
include_once("config.php");
require_once("textkeysite.php");

// Setup
$loggedIn = false;
$userName = "";

// Create the session handling object
$textkeysite = new textkeysite();

// Check to see if the user has fully logged in by passing both checks and then handle the custom code
$loggedIn = $textkeysite->getPassedTKCheck();
if ($loggedIn) {
	$fullName = $textkeysite->get_userName();
	$userName = $fullName;
	if (strlen($fullName) > 7) {
		$userName = substr($fullName, 0, 7) . '...';
	};
};

?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<head>
    <title>TextKey Login</title>

    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link type='text/css' rel="stylesheet" href="css/textkey.css?v=1">

    <!--[if lte IE 7]><style>.main{display:none;} .support-note .note-ie{display:block;}</style><![endif]-->

    <script type='text/javascript' src="js/jquery-1.7.1.min.js"></script>
	<script type='text/javascript' src='js/jquery.simplemodal-1.4.4.js'></script>
    <script type='text/javascript' src='js/login.js'></script>
    <script type='text/javascript' src='js/textkey.js'></script>
    <script type='text/javascript' src='js/textkey_custom.js'></script>

</head>
<body>
<div class="container">
  <section class="main">
  
<?php if (!($loggedIn)): ?>

	<header>
        <h1><a href="index.php">TextPower - TextKeytrademark>™</trademark> Demo Registraton</a></h1>
        <div class="tagline">
            <h1 align="center">Authenticate using text messaging<span id="reg"><sup> &reg;<sup></span></h1>
        </div>
        <div class="tagdesc">
        	<br />
            <p align="center" class="text-italics">This web page simulates what happens on a TextKey<trademark>™</trademark>-protected website.</p>
            <br />
            <p align="center">Enter the Username you were given and temporary</p>
            <p align="center">password <strong>"12345"</strong>, then click <strong>"Login"</strong>.</p>
        </div>
	</header>
    <form class="form-textkey" id="form-textkey">
      <h1>Login</h1>
        <input type='hidden' name='token' value='<?php  echo smcf_token($to); ?>'/>
      <p>
        <label for="login">Username</label>
        <input type="text" name="name" id='login-name' placeholder="Username">
      </p>
      <p>
        <label for="password">Password</label>
        <input type="password" name='password' id='login-password' placeholder="Password">
      </p>
      <p>
        <input type="submit" name="submit" value="Login" class='login-send' onClick="return login.handlelogin();">
      </p>
    </form>

<?php else: ?>
	<header>
        <h1><a href="index.php">TextPower - TextKey™ Demo Registraton</a></h1>
        <div class="tagdesc">
        	<br />
            <h1 align="center"><strong>SUCCESS!</strong></h1>
            <br />
            <h3 align="center">You have logged in with the user name <strong><?php echo $fullName; ?></strong></h3>
            <br />
            <h2 align="center">If this was your company's website you would now be successfully logged in.</h2>
        </div>
	</header>
    <form class="form-textkey" id="form-textkey">
      <p>
        <input type="submit" name="submit" value="Logout" class='logout-send' onClick="return login.handlelogout(true);">
      </p>
    </form>
    <div class="tagdesc">
        <h3 align="center">Want to try this again?  Click the <strong>"Logout"</strong> button to return to the login screen.</h3>
        <br />
    </div>
    <div class="tagline">
        <h1 align="center">Authenticate using text messaging<span id="reg"><sup> &reg;<sup></span></h1>
    </div>
<?php endif; ?>

    ​ </section>
</div>
</body>
</html>
