<?php
function phpErrorHandler($errnum,$errmsg,$file,$lineno){
	if($errnum==E_USER_WARNING){
		$error_msg = 'Error: '. $errmsg.' File: '. $file.' Line: '. $lineno;
		echo '({"error":' . json_encode($error_msg) . '})';
		exit;	
	}
}

// define error handling function
set_error_handler('phpErrorHandler');

// Add include
include_once("config.php");
include_once("textkey_rest.php");
require_once("textkeysite.php");

// Code to handle the login for the specific site
function user_password_login($name, $password, $tk) {
	$userid = "";

	// For this demo we are just authenticating the user against a "fixed password" and the TextPower phone registration
	//
	// NOTE: This is where you would hook into your own internal authentication handler and return back the user id to 
	// handle assigning a TextKey
	//
	if ($password == TK_PASSWORD) {
		$textkey_result = $tk->perform_DoesRegistrationUserIDExist($name, TK_ISHASHED);
		if ($textkey_result->errorDescr == "") {
			if ($textkey_result->userIDCount == 1) {
				$userid = $name;
			};
		}
	}
	
	return $userid;
}

// Validate the user login and get the TextKey dialog to display in the browser
function login_user() {
	global $textkeysite;

	// Setup
	$error_msg = '';

	// Create the textkey object
	$tk = new textKey(TK_API);
		
	// Get the passed in info.
	$name = isset($_POST["name"]) ? $_POST["name"] : "";
	$password = isset($_POST["password"]) ? $_POST["password"] : "";

	// HANDLE THE USER/LOGIN AUTHENTICATION HERE
	$textkey_userid = user_password_login($name, $password, $tk);
	if ($textkey_userid != "") {
	
		// Handle setting the sesssion info.
		$textkeysite->setPassedLoginCheck($name, $textkey_userid, $tk->tk_textkey);

		// Handle getting a valid TextKey using the user id
		$textkey_result = $tk->perform_IssueTextKeyFromUserId($textkey_userid, TK_ISHASHED);
		if ($textkey_result->errorDescr == "") {
			// No error so setup the return payload
			$error_msg = '({"error":"", "textkey":' . json_encode($textkey_result->textKey) . ', "textkeyVC":' . json_encode($textkey_result->validationCode) . ', "shortcode":' . json_encode('81888') . '})';
	
			// Handle setting the textkey sesssion info.
			$textkeysite->setTextKeyInfo($textkey_userid, $textkey_result->textKey,  $textkey_result->validationCode, '81888');
	
			// Return the valid info. 
			return $error_msg;
		}
		else {
			$error_msg = $textkey_result->errorDescr;
		}
	}
	else {
		$error_msg = "The name or password did not match. Please try again...";
	};
	
	// Handle clearing the sesssion info.
	$textkeysite->endSession();

	// Return the error
	$error_msg = '({"error":' . json_encode($error_msg) . '})';
	return $error_msg;
}

// Setup
date_default_timezone_set('America/Los_Angeles');

// Process the incoming values
$action = isset($_POST["action"]) ? $_POST["action"] : "";

// Create the session handling object
$textkeysite = new textkeysite();

// Handle the user/password authentication
if ($action == "login") {

	// Init the session values
	$textkeysite->initSessionInfo();
	
	// Get the check token
	$token = isset($_POST["name"]) ? $_POST["token"] : "";

	// Make sure the token from the login dialog matches
	//
	// NOTE: This is done to ensure the request is coming from the sites login form
	//
	if ($token === smcf_token($to)) {
	
		// Login the user
		$login_payload = login_user();

		// Return the resulting payload
		echo $login_payload;
	}
	else {
		echo '({"error":"Unfortunately, your login request could not be verified (' . $token . ' vs ' . smcf_token($to) . ")" . '"})';
	}
}

// Handle the TextKey authentication
else if ($action == "tklogin") {

	// Check to make sure pass 1 worked (i.e. username/pasword authentication)
	$loggedIn = $textkeysite->getPassedLoginCheck();
	if ($loggedIn) {
		// Get the session values from the textkey validation and check to make sure they are good
		$textkeyvc = $textkeysite->get_textkeyvc();
		$tkuserId = $textkeysite->get_tkuserId();
		$textkey = $textkeysite->get_textkey();
		
		// Create the textkey object
		$tk = new textKey(TK_API);
		
		// Validate the TextKey to ensure it was the original one with the TextKey validation code
		$textkey_result = $tk->perform_ValidateTextKeyFromUserId($tkuserId, $textkey, $textkeyvc, TK_ISHASHED);
		if ($textkey_result->errorDescr === "") {
			// Check for an error
			$validationErrors = $textkey_result->validationErrors;
			foreach($validationErrors as $key => $value) { 
				switch ($value) {
					case "textKeyNoError":
						// No error so setup the return payload
						$error_msg = '({"error":"", "validated":' . json_encode($textkey_result->validated) . '})';

						// Handle setting the sesssion info.
						$textkeysite->setPassedTKCheck();
					break;
					case "textKeyNotFound":
						$error_msg = '({"error": "The TextKey sent was not valid."})';
					break;
					case "textKeyNotReceived":
						$error_msg = '({"error": "The TextKey was never received."})';
					break;
					case "textKeyFraudDetect":
						$error_msg = '({"error": "Fraud Detected - The TextKey was not sent by the authorized device."})';
					break;
					case "noRegistrationFound":
						$error_msg = '({"error": "The TextKey was received but it was not assigned to a registered user."})';
					break;
					case "validationCodeInvalid":
						$error_msg = '({"error": "The TextKey was received but the validation code was invalid."})';
					break;
					case "textKeyTooOld":
						$error_msg = '({"error": "The TextKey was received but had already expired."})';
					break;
					case "textKeyError":
						$error_msg = '({"error": "An innternal TextKey error occured."})';
					break;
					case "textKeyNotValidated":
						$error_msg = '({"error": "The TextKey was not validated."})';
					break;
					case "pinCodeError":
						$error_msg = '({"error": "A Pin Code error occured."})';
					break;
					default:
						$error_msg = '({"error": "An error occured while trying to verify the TextKey."})';
					break;
				}
			} 			
		}
		else {
			$error_msg = $textkey_result->errorDescr;
			$error_msg = '({"error":' . json_encode($error_msg) . '})';
		}
	}
	else {
		$error_msg = "Error logging in user: User/Password validation was not finalized.";		
		$error_msg = '({"error":' . json_encode($error_msg) . '})';
	}

	error_log('error_msg: ' . $error_msg);

	echo $error_msg;
}

exit;

?>