<?php
	function phpErrorHandler($errnum,$errmsg,$file,$lineno){
		if($errnum==E_USER_WARNING){
			$alertmsg = 'Error: '.$errmsg.' File: '.$file.' Line: '.$lineno;
			echo $alertmsg;
			exit;	
		}
	}
	
	// define error handling function
	set_error_handler('phpErrorHandler');
	
	// Shared code
	include_once("../config.php");
	include_once("../textkey_rest_debug.php");
	
	// Setup
	define('OUTPUT_STATE', OUTPUT_FULL);
	
	// Create a TK object
	$tk = new textKey(TK_API);
		
	// Test Values
	$UserID = "BobSmith";
	$isHashed = TK_ISHASHED;

	// Handle the operation
	$textkey_result = $tk->perform_DoesRegistrationUserIDExist($UserID, $isHashed);
	if ($textkey_result->errorDescr == "") {
		$tkResultsArr = get_object_vars($textkey_result);
		foreach($tkResultsArr as $key => $value) { 
			$results .= $key . ': ' . $value . TK_NEWLINE;
		} 			
		print_r2($results, "API Returned Values", "#D6FCFF");
	}
	else {
		$results = 'Error: ' . $textkey_result->errorDescr . TK_NEWLINE;
		print_r2($results, "API Error", "#D6FCFF");
	}
?>