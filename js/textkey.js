/*
**
** textkey.js
**
** These are the TextKey js handling functions. They handle the interaction with the backend handler code as well as the client side interaction/integration.
** 
*/

/*
**
** TextKey modal default options
**
** These settings define what shows up in the TextKey Modal dialog.
**
** NOTES:
**
** urlTextKeyAuth: This is the polling handler which checks to see if the TextKey has been received or not.
** pollFreq: This defines how often to poll the back end handler (in Milliseconds).
** pollTime: This defines the time the user is given to send the TextKey code for authentication (in Seconds - Max is 180 seconds).
** tkHTML: This is the HTML content displayed in the TextKey Modal. Overide this with the setTextKeyHTML call.
** tkcontainerCss: This is the container styling for the TextKey Modal. Overide this with the setTextKeyContainerCss call.
** tkdataCss: This is the data styling for the TextKey Modal. Overide this with the setTextKeyDataCss call.
** tkoverlayCss: This is the overlay styling for the TextKey Modal. Overide this with the setTextKeyOverlayCss call.
**
** See Simple Modal Documentation for more info: http://www.ericmmartin.com/projects/simplemodal/
**
** The remaining elemtns are used to hold inforation/state.
**
*/
error_message = '';
tkSettings = {
	urlTextKeyAuth: 'textkeycheck.php?callback=?',
	pollFreq: 5000,
	pollTime: 120,
	tkHTML: '<div id="simplemodal-container"><h3>Waiting for TextKey Authentication...</h3><div id="tkTime"></div></div>',
	tkcontainerCss: {'height':'100px', 'width':'600px', 'color':'#bbb', 'background-color':'#333', 'border':'4px solid #444', 'padding':'12px'},
	tkdataCss: {'padding':'8px'},
	tkoverlayCss: {'background-color':'#000', 'cursor':'wait'},
	tkTextKey: '',
	tkShortCode: '',
	tkTextKeyUnloackSound: 'audio/door_open.mp3',
	tkTextKeySuccess: false,
	tkTextKeyMessage: '',
	tkTextKeyStatus: false,
	tkFnSuccess: null,
	tkFnFail: null
};

/*
** Standard modal dialog handling
*/
function closeModal() {
	$.modal.close();
};

function showModal(title, msg, md_closeProc, md_height, md_width) {
	if (typeof(md_height) === "undefined") { md_height = 160; };
	if (typeof(md_width) === "undefined") { md_width = 625; };
	
	// Build the hmtl to display
	tkModalHTML = ('<div id="basic-modal-content"><h3>'+title+'</h3><p>'+msg+'</p><div class="modal-buttons"><span><a class="modal-button" onclick="javascript:$.modal.close();">Close</a></span></div></div><!-- preload the images --><div style="display:none"><img src="./images/x.png" alt="" /></div>');

	$.modal(
		tkModalHTML, 
		{
			onClose: md_closeProc,
			containerCss:{
					height:md_height, 
					width:md_width
		},
	});
	
	return true;

};

/*
 * TextKey Change Standard Settings
 *
 */
function setTextKeyAuth(urlTextKeyAuth) {
	tkSettings.urlTextKeyAuth = urlTextKeyAuth;
}

function setPollFreq(pollFreq) {
	tkSettings.pollFreq = pollFreq;
}

function setPollTime(newpollTime) {
	tkSettings.pollTime = newpollTime;
	pollTime = tkSettings.pollTime;
}

function setTextKeyHTML(tkHTML) {
	tkSettings.tkHTML = tkHTML;
}

function setTextKeyContainerCss(tkcontainerCss) {
	tkSettings.tkcontainerCss = tkcontainerCss;
}

function setTextKeyDataCss(tkdataCss) {
	tkSettings.tkdataCss = tkdataCss;
}

function setTextKeyOverlayCss(tkoverlayCss) {
	tkSettings.tkoverlayCss = tkoverlayCss;
}

/*
 * TextKey Globals
 *
 */
var pollTime = tkSettings.pollTime;
var tkTextKeyHandled = false;
var timer_is_on = 0;
var t;

/*
 *
 * Hide and show the scoll bar
 *
 */
function hideScrollBar() {
	$("body").css("overflow", "hidden");
}

function showScrollBar() {
	$("body").css("overflow", "auto");
}

/*
 *
 * Close the TextKey dialog
 *
 */
function closeTKModal() {

	// Set the handled flag
	tkTextKeyHandled = true;

	// Reset the poll time
	pollTime=tkSettings.pollTime;
	doTimer(1);

	// Clear out the messages
	$("#tkTime").text("");

	// Hide the modal dialog
	$.modal.close();
	
	// Show the scroll bar
	showScrollBar();
}

/*
 *
 * TextKey Post Handler
 *
 */
function postRedirect(redirectURL) {
	var tkredirectform = $('<form id="tkredirectform" action="' + redirectURL + '" method="post">' +
	  '<input type="text" name="textkeymessage" value="' + tkSettings.tkTextKeyMessage + '" />' +
	  '<input type="text" name="textkeystatus" value="' + tkSettings.tkTextKeyStatus + '" />' +
	  '</form>');
	$('body').append(tkredirectform);
	$('#tkredirectform').submit();
}

/*
 *
 * TextKey Login was succesful
 *
 */
function completeLogin() {
	
	// Set flag to success
	tkSettings.tkTextKeySuccess = true;

	// Hide the modal dialog
	closeTKModal();
	
	// Handle the Client Call back or URL redirect
	if ($.isFunction(tkSettings.tkFnSuccess)) {
		tkSettings.tkFnSuccess(tkSettings.tkTextKeyMessage, tkSettings.tkTextKeyStatus);
	}
	else {
		postRedirect(tkSettings.tkFnSuccess);
	}
}

/*
 *
 * TextKey Login failed
 *
 */
function errorLogin() {
	// Handle the Session
	login.handlelogout(false);

	// Hide the modal dialog
	closeTKModal();
	
	// Handle the Client Call back or URL redirect
	if ($.isFunction(tkSettings.tkFnFail)) {
		tkSettings.tkFnFail(tkSettings.tkTextKeyMessage, tkSettings.tkTextKeyStatus);
	}
	else {
		postRedirect(tkSettings.tkFnFail);
	}
}

/*
 *
 * Show an error modal
 *
 */
function errorShow() {
	showModal('Login Error...', 'The TextKey authentication did not complete.  You can try again if you think this was an error.', closeModal);
	error_message = "";
}

/*
 *
 * Show the TextKey modal dialog
 *
 */
function showTKModal(TextKey, ShortCode, fnCallSuccess, fnCallFailed) {
	
	// Check for valid call backs
	if (typeof(fnCallSuccess) === "undefined") {
		alert("Please make sure you pass in your success and failure handler parameters...");
		return;
	};
	if (typeof(fnCallFailed) === "undefined") {
		alert("Please make sure you pass in your success and failure handler parameters...");
		return;
	};

	// Set the tkSettings values
	tkSettings.tkTextKey = TextKey;
	tkSettings.tkShortCode = ShortCode;
	tkSettings.tkFnSuccess = fnCallSuccess;
	tkSettings.tkFnFail = fnCallFailed;
	
	// Set the status & handls variables
	tkSettings.tkTextKeySuccess = false;
	tkSettings.tkTextKeyMessage = '';
	tkSettings.tkTextKeyStatus = false;
	tkTextKeyHandled = false;

	// Hide the scroll bar
	hideScrollBar();
	
	// Show the dialog
	$.modal(
		tkSettings.tkHTML, 
		{
			onClose: function (dialog) {
				dialog.data.fadeOut('slow', function () {
					dialog.container.slideUp('slow', function () {
						dialog.overlay.fadeOut('slow', function () {
							// Handle the user initiated close					 
							if (!(tkTextKeyHandled)) {	
								// Set the status values
								tkSettings.tkTextKeyMessage = 'User cancelled the TextKey check...';
								tkSettings.tkTextKeyStatus = false;
								
								// Handle the failed login
								errorLogin();
								
								// Show the dialog
								error_message = tkSettings.tkTextKeyMessage;
								setTimeout("errorShow()", 1000);
							}
							else {
								$.modal.close();
								if (error_message != "") {
									setTimeout("errorShow()", 1000);
								};
							};
						});
					});
				});
			},
			position: ["15%",],
			containerCss: tkSettings.tkcontainerCss,
			dataCss: tkSettings.tkdataCss,
			overlayCss: tkSettings.tkoverlayCss
		}
	);
	
	// Hide the close button
	$('#simplemodal-container a.modalCloseImg').hide();

	// Start the TextKey handler
	sendTextKeyCheck();
}

/*
 *
 * Handle the JS timer
 *
 */
function doTimer(clr) {
	// Handle cancelling the timer
	if(clr == 1 && timer_is_on == 1) {
		clearTimeout(t);
		timer_is_on = 0;
	};
	
	// Handle starting the timer
	if (clr == 0) {
		timer_is_on = 1;
		t = setTimeout("sendTextKeyCheck()",tkSettings.pollFreq);
	} 
}

// Validate the TextKey and finalize the login
function validateTextKey()  {
	$.ajax({
		url: 'login.php',
		data: 'action=tklogin',
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
				handleUnlock();
				$("#tkTime").html('<p><span class="bigmsg colorsuccess">SUCCESS!</span></br><p>TextKey accepted. Completing login now...</p>');
				tkSettings.tkTextKeyMessage = 'SUCCESS! TextKey accepted and verified.';
				setTimeout("completeLogin()",3000);
			}
			else {
				$("#tkTime").html('<p><span class="bigmsg colorfailed">FAILED!</span></br><p>'+data.error+'</p>');
				tkSettings.tkTextKeyMessage = 'FAILED! TextKey was not verified.';
				error_message = 'ERROR: '+data.error;
				setTimeout("errorLogin()",3000);
		   };
		}
	});
};

function changeLock() {
	$(".poweredby img").attr("src", "images/poweredbyunlocked.gif");
}

// Handle the unlock image/sound
function handleUnlock() {
	// Switch to unlocked logo if there
	if (tkSettings.tkTextKeyUnloackSound != "") {
		$("#tkSound").html('<audio src="'+tkSettings.tkTextKeyUnloackSound+'" autoplay></audio>');
	};
	setTimeout("changeLock()",1500);
}

/*
 *
 * The TextKey Hander
 *
 */
function sendTextKeyCheck() {

	// Setup the data payload
	var DTO = { 'textKey': tkSettings.tkTextKey };

	// Show the results in the console
	if (typeof(console) !== 'undefined' && console != null) {
		console.log(DTO);
		console.log(JSON.stringify(DTO));
	};

	try {
		// Made a request to check for response
		$.getJSON(tkSettings.urlTextKeyAuth, 
		DTO,
		function(responseS) {
			
			if (typeof(console) !== 'undefined' && console != null) {
				console.log(responseS);
			};
			
			if (responseS.errorDescr) {
				error_message = 'ERROR: '+responseS.errorDescr;
				tkSettings.tkTextKeyMessage = error_message;
				$("#tkTime").html('<p>'+error_message+'</p>');
				doTimer(1);
				setTimeout("errorLogin()",3000);
			}
			else {
				
				// Look at the response
				tkSettings.tkTextKeyStatus = responseS.ActivityDetected;
		
				// Show the results in the console
				if (typeof(console) !== 'undefined' && console != null) {
					console.log('tkSettings.tkTextKeyStatus: ' + tkSettings.tkTextKeyStatus);
				};
				
				// Check for an expired textKey
				if (responseS.TimeExpired) {
						error_message = '<span class="bigmsg colorfailed">FAILED!</span></br><p>Time for response has expired.<p>';
						tkSettings.tkTextKeyMessage = error_message;
						$("#tkTime").html('<p>'+error_message+'</p>');
						doTimer(1);
						setTimeout("errorLogin()",10000);
				}
				else {
					// Check for activity detected
					if (responseS.ActivityDetected) {
							$("#tkTime").html('<p><span class="bigmsg colorverify">TEXTKEY RECEIVED</span></br><p>Completing verification now...</p>');
							tkSettings.tkTextKeyMessage = 'Verifying TextKey...';
							
							// Validate the TextKey to make sure it was legal
							doTimer(1);
							setTimeout("validateTextKey()",3000);
					}
					else {
						$("#tkTime").html('<p>To complete this login, text the following code to '+tkSettings.tkShortCode+':</p><p id="tkCode">' + tkSettings.tkTextKey + '</p><p>within '+pollTime+' seconds...</p>');
						pollTime -= 5;
					}
				}
			}
		});
	} catch(err) {
		error_message = 'ERROR: '+err;
		tkSettings.tkTextKeyMessage = error_message;
		$("#tkTime").html('<p>'+error_message+'</p>');
		doTimer(1);
		setTimeout("errorLogin()",3000);
	};

	// Start the timer
	doTimer(0);
}
