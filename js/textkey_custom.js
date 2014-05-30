// Call to handle the successful authentication
function loginSuccess(tkTextKeyMessage, tkTextKeyStatus) {
	location.reload(true);
};

// Call to handle the failed authentication
function loginFailed(tkTextKeyMessage, tkTextKeyStatus) {
};

// Simple Call to handle the login
function textKeyHandler(textKey, shortcode) {

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
	// Set the total time to wait for TextKey to 120 seconds
	setPollTime(120);
	
	// Show the TextKey Modal and handle the checking
	showTKModal(textKey, shortcode, loginSuccess, loginFailed);
};
