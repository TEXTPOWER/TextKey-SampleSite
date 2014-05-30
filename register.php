<?php
function phpErrorHandler($errnum,$errmsg,$file,$lineno){
	if($errnum==E_USER_WARNING){
		$alertmsg = 'Error: '. $errmsg.' File: '. $file.' Line: '. $lineno;
		$result = 'BAD';
		echo $alertmsg;
		exit;	
	}
}

// define error handling function
set_error_handler('phpErrorHandler');

// Add include
include_once('config.php');
include_once("textkey_rest_debug.php");
require_once("textkeysite.php");
require_once("textkeyshared.php");

function register_user() {

	// Setup
	$error_msg = '';

	// Get the passed in info.
	$name = isset($_POST["name"]) ? $_POST["name"] : "";
	$mobile = isset($_POST["mobile"]) ? $_POST["mobile"] : "";

	// Filter and validate fields
	$name = smcf_filter($name);
	$mobile = smcf_filter($mobile);
	
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

	return $error_msg;
}

// Setup
$return_msg = "";
$error_msg = "";

// Process
$action = isset($_POST["action"]) ? $_POST["action"] : "";
if ($action == "register") {
	// Register the user via the API
	$error_msg = register_user();
	if ($error_msg != "") {
		$return_msg = "Error creating user: " . $error_msg;
	}
	else {
		$return_msg = "";
	}
	echo $return_msg;
}

exit;
?>
