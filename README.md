TextKey Sample PHP Site
=======================

This TextKey Sample PHP Site provides an example of how to implement registration and login using the TextKey REST API.

What is included
----------------

Here are the key elements in this repository.

* User Registration - registeruser.php
* User Login - index.php
* tests folder - sample code using the TextKey API

Configuration Settings
----------------------

The Sample Site configuration file is called `config.php`. The only item you will need to setup for the Sample SIte is your API Key. Just replace `YOUR_API_KEY` with your actual TextKey API Key:

```php
/*
** TextKey Settings
*/
define('TK_API', 'YOUR_API_KEY');
define('TK_DISPLAY_API', TK_API);
```
**NOTE:** You can get a developer API Key by going to the TextKey developer site at [TextKey Devloper Site Registration](http://developer.textkey.com/register.php) and registering for an account. Once you have created an account and are logged in, you can get a Developer API Key by going to the user settings page (in the user menu on the upper left) and following the instructions on the `API Information` tab.

The `Output Settings` options in the `config.php` only appy to the items in the tests folder which is using the `textkey_rest_debug.php` library. The code in that folder can be used to test certain TextKey API calls relevant to this site.

To display test output, turn on the debugging flag by setting it to true.

```php
define('DEBUGGING', true);	// Turn this on to show output
```

To define the level of details, set OUTPUT_STATE. 

You have 3 options: 

```php
define('OUTPUT_PLAYLOAD', 0);	// Just show the output payload
define('OUTPUT_LIMITED', 1);	// Show more details about the API call
define('OUTPUT_FULL', 2);	// Show all detials about the API call
```

Each of the tests will use one of the 3 options to set the OUTPUT_STATE constant.

For example:

```php
// Shared code
include_once("../config.php");
include_once("../textkey_rest_debug.php");
	
// Setup
define('OUTPUT_STATE', OUTPUT_FULL);
```

Registering a user
------------------

The first step in testing out the Sample Site is to register a user. In order for TextKey to be able to verify an incoming TextKey and the associated phone, it must be registered under the account tied to the API Key. 

You can do that via the `registeruser.php` page:

![Sample Site Registration](http://developer.textkey.com/images/ss_register_user.jpg)

This registration page utilizes the code in `register.php` to make a request to the `registerTextKeyUser` API call using the `AddChange` Command value to add the user using the `User Name` and `Mobile Number` that was entered. 

You can look at more details on the call at [registerTextKeyUser API Call](http://developer.textkey.com/apidocpg_registertextkeyuser.php) or [registerTextKeyUserCSA API Call](http://developer.textkey.com/apidocpg_registertextkeyusercsa.php) on the TextKey developer site.

Here is the code snippet that handles the registration:

```php
// Handle the TextKey Registration
$tk = new textKey(TK_API);

// Setup
$Command = "AddChange";
$CellNumber = $mobile;
$OwnerFName = $name;
$OwnerLName = $name;
$RegUserID = $name;
$Suppl1 = "";
$Suppl2 = "";
$isHashed = TK_ISHASHED;
$PinCode = "";
$DistressPinCode = "";
$TextKeyMode = "TextKeyOnly";
$ReceiveMode = "AnyCode";

// Handle the operation
$textkey_result = $tk->perform_registerTextKeyUser($Command, $CellNumber, $OwnerFName, $OwnerLName, $Suppl1, $Suppl2, $RegUserID, $isHashed, $PinCode, $DistressPinCode, $TextKeyMode, $ReceiveMode);
if ($textkey_result->errorDescr != "") {
	$error_msg = $textkey_result->errorDescr . TK_NEWLINE;
}
```

Logging in via TextKey 
----------------------

One you have a registered user, you can try logging them in via the login page at `index.php`. Most of the server-side login handling is done in the `login.php` code.

![Sample Site Registration](http://developer.textkey.com/images/ss_login_user.jpg)

The flow consists of the following:

* 1st pass authenticaion - Validating the User Name/Password combination
* 2nd pass authentication - Once the 1st pass is verified, the TextKey authentication comes into play
  * Get a valid TextKey for the specific user
  * Display the TextKey and short code for the user to text to
  * Monitor client side via polling to see if the TextKey was received
  * Once received verify that it is valid and came from the correct phone

## Basic Code Flow

* The login form submit calls the JS login handler `handlelogin` (i.e. see `login.js`) which makes an AJAX request to `login.php`
* The server side code (i.e. in `login.php`) handles a 1st pass user/password check and if that passes will request a TextKey for the 2nd pass authentication
* The AJAX response from `handlelogin` will return back a payload with either an error or the valid TextKey information to display
* A good response will trigger a call to the `textKeyHandler` JS function (i.e. see `textkey_custom.js`) which handles displaying the TextKey information to the user
* Once the dialog is displayed to the user, a polling mechanism is initiated (i.e. see `showTKModal` in `textkey.js`)
* The polling handler will either find that a TextKey is received or will timeout
* Upon finding a reciept of a TextKey, it will attempt to  validate it (i.e. see `validateTextKey` in `textkey.js`) via a server side AJAX request
* A good validation will call the success handler - set to the `loginSuccess` JS function via the `showTKModal` call
* A bad validation will call the error hander - set to the `loginFailed` JS function via the `showTKModal` call

## Login Form submit handling

The Login form submit calls a handler in `login.js` which handles initiating the server side login authentication.

```javascript
// Submit the form													 
$.ajax({
	url: 'login.php',
	data: $('form').serialize() + '&action=login',
	type: 'post',
	cache: false,
	dataType: 'html',
	success: function (jsondata) {
		// Convert to an object
		data = eval(jsondata);
		
		if (typeof(console) !== 'undefined' && console != null) {
			console.log("data: " + jsondata);
			console.log(data);
		};

		// Check for a valid login
		if (data.error == "") {
			// Set the flag
			login.loggedin = true;
			
			// Set the textkey values
			login.textkey = data.textkey;
			login.textkeyVC = data.textkeyVC;
			login.shortcode = data.shortcode;

			// Handle the textkey login
			if (login.loggedin) {
				textKeyHandler(login.textkey, login.shortcode);
			};
		}
		else {
			// Set the error message
			login.message = data.error;
			
			// Show the error message
			login.showError();
		};
	},
	error: login.error
});
```
## 1st pass authenticaion

This is just an example of how to implement TextKey so we don't have it linked to a 1st pass authentication (i.e. a username/password check for example). Instead we have a function called `user_password_login($name, $password, $tk)` that will simulate the action. In a production environment site, this would actually handle the username and password authentication and return back the unique id that the account is tied to. In our case, we tied the account to the `User Name` when we registered the user however this can be any key you would like to use.

```php
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

		HANDLE GETTING THE TextKey HERE

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
```

We are also using server side session values to keep track of the flow. This is being handled via the `textkeysite` class in `textkeysite.php`. They consist of the following:

```
// User login Info.
$_SESSION['username']
$_SESSION['userid']

// TextKey info.
$_SESSION['textkeycheckuserid']
$_SESSION['textkey']
$_SESSION['textkeyvc']
$_SESSION['shortcode']

// 2 pass flags
$_SESSION['passedcheck1']
$_SESSION['passedcheck2']
```

The 1st pass will set the `username` and `userid` values and set `passedcheck1` to true.

```php
// Handle setting the sesssion info.
$textkeysite->setPassedLoginCheck($name, $textkey_userid, $tk->tk_textkey);
```

## 2nd pass authenticaion

### Get a valid TextKey for the specific user

Once the 1st pass has been handled, the `login_user()` function will take care of assigning a TextKey based on the `user id` linked to the TextKey registered user and then passing it back to the client-side handler to display the message to the user.

```php
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
```

At this point, we also set the server side session variables to hold the TextKey information for later verification. We set the `textkeycheckuserid`, `textkey`, `textkeyvc` and `shortcode` session variables however `passedcheck2` would still be set to false.

```php
// Handle setting the textkey sesssion info.
$textkeysite->setTextKeyInfo($textkey_userid, $textkey_result->textKey,  $textkey_result->validationCode, '81888');
```

### Display the TextKey and short code for the user to text to

At this point, we return back either an error payload or a valid payload with the TextKey information. The login handler gets the TextKey information and passes it onto the `textKeyHandler` function.

```javascript
// Check for a valid login
if (data.error == "") {
	// Set the flag
	login.loggedin = true;
	
	// Set the textkey values
	login.textkey = data.textkey;
	login.textkeyVC = data.textkeyVC;
	login.shortcode = data.shortcode;
	
	// Handle the textkey login
	if (login.loggedin) {
		textKeyHandler(login.textkey, login.shortcode);
	};
}
```
The `textKeyHandler` (i.e. in `textkey_custom.js`) handles setting up the styling and content of the display dialog, the time to poll for a TextKey response, and the success and failure callback handlers. 

This code sets the styling and content of the dialog:

```javascript
// Customize the look and feel
setTextKeyHTML('<div id="tkmessage-container"><h1>Mobile Authentication...</h1><div class="poweredby"><img src="images/poweredbylocked.gif" alt="Powered by TextPower" border="0" align="absmiddle"></div><div id="tkSound"></div><div id="tkTime"></div></div>');
setTextKeyContainerCss({'height':'260px', 
						'width':'625px', 
						'font': '16px/22px \'Raleway\', \'Lato\', Arial, sans-serif',
						'color':'#000000', 
						'background-color':'#000', 
						'padding':'10px', 
						'background-color':'#F1F1F1', 
						'margin':'0', 
						'padding':'0',
						'border':'4px solid #444'});
setTextKeyDataCss({'padding':'8px'});
setTextKeyOverlayCss({'background-color':'#AAA', 'cursor':'wait'});
```

This code setups up the polling timeout in seconds:

```javascript
// Set the total time to wait for TextKey to 120 seconds
setPollTime(120);
```

This code displays the dialog and initiates polling:

```javascript
// Show the TextKey Modal and handle the checking
showTKModal(textKey, shortcode, loginSuccess, loginFailed);
```

### Monitor client side via polling to see if the TextKey was received and verify a valid TextKey

The `showTKModal` (i.e. in `textkey.js`) handles all of the elements of both polling and verification. Upon completion it will call either the `loginSuccess` JS function or the `loginFailed` JS function.

The verification handler is triggered via the `validateTextKey` JS function. It makes a server side request to make sure that the TextKey received was from the correct phone and that the Verification Code is also correct.

The code in `login.php` that handles this verification is as follows:

```php
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
```

The code ensures that pass 1 was already successful. Then it verifies the TextKey values via the session values. Finally, it handles the verification response.

The final step if everything is good is to set the `passedcheck2` session flag to be true to let the site know that this user is logged in.

```php
// Handle setting the sesssion info.
$textkeysite->setPassedTKCheck();
```


### After success or failure

Both callback handlers are in `textkey_custom.js` and should be modified as necessary to act appropriately. In this case, a successful login just reloads the page and a failure does nothing. The messaging to the user in the dialog will update them on the status and will display an error message if login was not successful.

```javascript
// Call to handle the successful authentication
function loginSuccess(tkTextKeyMessage, tkTextKeyStatus) {
	location.reload(true);
};

// Call to handle the failed authentication
function loginFailed(tkTextKeyMessage, tkTextKeyStatus) {
};
```
**NOTE:** The server side session variables are checked before any page on the site is rendered and if both `passedcheck1` and `passedcheck2` are true, then the user is treated as being logged in. This ensures that any client side manipulation cannot force an authentication.

For example, in `index.php`, this is the first set of code use to check login status and act appropriately:

```php
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
```
At those point, the `$loggedIn` variable can be used to determine what to show or not show. For example:

```php
<?php if (!($loggedIn)): ?>

SHOW THE LOGIN FORM

<?php else: ?>

SHOW THE LOGGED IN INFORMATION

<?php endif; ?>
```

Testing Code
------------

We have included a folder called `tests` in this repository with sample code for some API calls. In each case, just customize the call parameters for testing.

* `tk_issuetktest.php` - issues a TextKey to a registered user
* `tk_registertest.php` - registers a user
* `tk_unregistertest.php` - unregisters/deletes a user
* `tk_useridexiststest.php` - checks to see if a user exists via their user id
* `tk_validatetktest.php` - validate a TextKey using the TextKey and the Validation Code

Contributing to this Sample Site
--------------------------------

**Issues**

Please discuss issues and features on Github Issues. We'll be happy to answer to your questions and improve the Sample Site based on your feedback.

**Pull requests**

You are welcome to fork this Sample Site and to make pull requests on Github. We'll review each of them, and integrate in a future release if they are relevant.
