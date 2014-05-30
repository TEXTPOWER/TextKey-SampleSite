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
require_once("textkeysite.php");

// Setup
date_default_timezone_set('America/Los_Angeles');

// Create the session handling object
$textkeysite = new textkeysite();

// Handle clearing out the session info
$textkeysite->endSession();

exit;

?>