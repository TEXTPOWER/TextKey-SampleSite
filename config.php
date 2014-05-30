<?php

/*
** config.php
**
** This page sets debugging/display settings as well as global values.
**
*/

/*
** TextKey Settings
*/
define('TK_API', 'YOUR_API_KEY');
define('TK_DISPLAY_API', TK_API);

// TextKey SOAP paths
define('TK_WSDL', 'https://secure.textkey.com/ws/textkey.asmx?wsdl');
define('TK_NS', 'https://secure.textkey.com/services/');

// TextKey REST path
define('TK_REST', 'https://secure.textkey.com/REST/TKRest.asmx/');

// Verify token
$to = "123231";

// Dummy Password 
define('TK_PASSWORD', '12345');

// Whether or not the passwords are being hashed
define('TK_ISHASHED', '0');


/*
** Output Settings
** 
** NOTE: These are only used when the textkey_rest_debug.php or textkey_soap_debug.php libraries are being included. 
** The tests folder does use these in setting the OUTPUT_STATE constant
** The Sample Site does not
*/
// Setup how to display messages - web output or file output
define('DEBUGGING', false);		// Enable when diplaying 

// Setup the output details
define('OUTPUT_PLAYLOAD', 0);	// Just show the output payload
define('OUTPUT_LIMITED', 1);	// Show more details about the API call
define('OUTPUT_FULL', 2);		// Show all details about the API call

define('TK_NEWLINE', "<BR>");	// Use this as the newline option when displaying output

function smcf_token($s) {
	return md5("smcf-" . $s . date("WY"));
}
?>
