<?php
include_once("config.php");
require_once("textkeysite.php");
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<head>
<title>TextKey Registration</title>
<meta charset="UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link type='text/css' rel="stylesheet" href="css/textkey.css?v=1">
<!--[if lte IE 7]><style>.main{display:none;} .support-note .note-ie{display:block;}</style><![endif]-->
<script type='text/javascript' src="js/jquery-1.7.1.min.js"></script>
<script type='text/javascript' src='js/jquery.simplemodal-1.4.4.js'></script>
<script type='text/javascript' src='js/textkey.js'></script>
<script type='text/javascript' src='js/textkeyreg_custom.js'></script>
</head>
<body>
<div class="container">
  <section class="main">
    <header>
      <h1><a href="index.php">TextPower - TextKey™ Sample Registraton</a></h1>
    </header>
    <div class="tagdesc"> <br />
      <p align="center">Enter your <strong>Temporary User Name</strong> and a <strong>Mobile Number</strong> and then Click the <strong>"Register" button</strong> below.</p>
      </br>
      <p align="center">Once registered, you will be presented with a dialog containing basic instructions.</p>
      </br>
      <p align="center">Click the <strong>"Click and Go To Sample Site" button</strong> from the dialog to take you to the Sample site.</p>
      </br>
      <p align="center">From the <strong>Sample Site</strong> use your <strong>User Name</strong> and the password <strong>12345</strong> to <strong>login and see TextKey&#153; in action</strong>.</p>
    </div>
    <form class="form-textkey" id="form-textkey" action='javascript:registerTKUser();'>
      <h1>Register User</h1>
      <p>
        <label for="register">Username</label>
        <input type="text" name="name" id='register-name' placeholder="Create a Temporary User Name">
      </p>
      <p>
        <label for="password">Mobile Number</label>
        <input type="text" name='mobile' id='register-mobile' placeholder="Enter Your Mobile Number">
      </p>
      <p>
        <input type="submit" name="submit" value="Register" class='register-send'>
      </p>
    </form>
    ​ </section>
</div>
</body>
</html>
