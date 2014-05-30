<?php
    // Write to a log file
    function logF($fileprefix, $msg)
    { 
        // Log to a filename by date
        $filename = $fileprefix . "/" . TK_LOGPREFIX . "_" .  date("Ymd", mktime()) . ".log";
		
        // open file
        $fd = fopen($filename, "a");
        
        // append date/time to message
        $str = "[" . date("Y/m/d h:i:s", mktime()) . "] " . $msg; 
        
        // write string
        fwrite($fd, $str . "\n");
        
        // close file
        fclose($fd);
    }
    
	// Format the XML data
	function formatXmlString($xml, $padlength = 1) {  
	  
	  // add marker linefeeds to aid the pretty-tokeniser (adds a linefeed between all tag-end boundaries)
	  $xml = preg_replace('/(>)(<)(\/*)/', "$1\n$2$3", $xml);
	  
		$xml   = str_replace("<br>", " \n", $xml);

	  // now indent the tags
	  $token      = strtok($xml, "\n");
	  $result     = ''; // holds formatted version as it is built
	  $pad        = 0; // initial indent
	  $matches    = array(); // returns from preg_matches()
	  
	  // scan each line and adjust indent based on opening/closing tags
	  while ($token !== false) : 
			// test for the various tag states
			
			// 1. open and closing tags on same line - no change
			if (preg_match('/.+<\/\w[^>]*>$/', $token, $matches)) : 
				$indent=0;
			// 2. closing tag - outdent now
			elseif (preg_match('/^<\/\w/', $token, $matches)) :
				$pad--;
				$indent = 0;
			// 3. opening tag - don't pad this one, only subsequent tags
			elseif (preg_match('/^<\w[^>]*[^\/]>.*$/', $token, $matches)) :
				$indent=1;
			// 4. no indentation needed
			else :
				$indent = 0; 
			endif;
			
			// pad the line with the required number of leading spaces
			$line    = str_pad($token, strlen($token) + ($pad * $padlength), str_repeat(' ',$padlength), STR_PAD_LEFT);
			$result .= $line . "\n"; // add to the cumulative result, with linefeed
			$token   = strtok("\n"); // get the next token
			$pad    += $indent; // update the pad size for subsequent lines    
	  endwhile; 
	  
	  return $result;
	}
	
	// Handle tags cleanup
	function formatcleantags($xml) {  
		$xml   = str_replace("<", "&lt;", $xml);
		$xml   = str_replace(">", "&gt;", $xml);
	  return $xml;
	}

	// Redirect handler
	function redirectURL($url) {
		if ($url != "") {
			header('Location: ' . $url);
		};
	}

	// Dump out objects in a readable format
	function print_r2($val, $header, $bkcolor='') {
		if ($header != "") {
			echo '<h3 class="short_headline"><span>'. $header . '</span></h3>';
		}
		if ($bkcolor != "") {
			echo '<pre style="background-color: ' . $bkcolor . ';">';
		}
		else {
			echo '<pre>';
		}
		echo '<code>';
		$text = print_r($val, true);
		$text = cleanup_auth_values($text);
		echo $text;
		echo '</code>';
		echo '</pre>';
	}
	
	// Dump out xml in a readable format
	function print_xml($val, $header, $bkcolor='') {
		// Setup
		$xml = formatXmlString($val);
		if ($header != "") {
			echo '<h3 class="short_headline"><span>'. $header . '</span></h3>';
		}
		if ($bkcolor != "") {
			echo '<pre style="background-color: ' . $bkcolor . ';">';
		}
		else {
			echo '<pre>';
		}
		echo '<td><textarea rows="' . (countLInes($xml) + 2) .'" name="XML_soap_textarea" style="width:100%;">';
		$xml = cleanup_auth_values($xml);
		echo $xml;
		echo '</textarea></td>';
		echo '</pre>';
	}

	// Dump out xml in a readable format for docs
	function print_xmldoc($val, $rowcount = 14, $padcnt = 4, $bkcolor = '#eee') {
		echo '<pre style="background-color: ' . $bkcolor . '">';
		echo '<td><textarea rows="' . $rowcount . '" name="XML_textarea" style="width:100%;">';
		echo formatXmlString($val, $padcnt);
		echo '</textarea></td>';
		echo '</pre>';
	}

	// Dump out code/xml in a readable format for docs
	function print_xmlcode($val, $padcnt = 4, $bkcolor = '#eee') {
		echo '<pre style="background-color: ' . $bkcolor . '"><code>';
		echo formatcleantags(formatXmlString($val, $padcnt));
		echo '</code></pre>';
	}

	// Dump out mysql query in a readable format
	function showsql($q) {
		// Build the string
		$querystr = $q->getSqlQuery();
		foreach ($q->getFlattenedParams() as $index => $param)
			$querystr = str_replace("?", $param, $querystr);
			
		// Handle formatting
		$querystr = str_replace("FROM","<BR>FROM",$querystr);
		$querystr = str_replace("WHERE","<BR>WHERE",$querystr);
		$querystr = str_replace("AND","<BR>AND",$querystr);
		$querystr = str_replace("OR","<BR>OR",$querystr);
		$querystr = str_replace("ORDER BY","<BR>ORDER BY",$querystr);
		
		print_r2($querystr);
	}

	// Map demo values to display values
	function cleanup_auth_values($text) {
		$newtext = str_replace(TK_API, TK_DISPLAY_API, $text);
		$newtext = str_replace(TK_PWD, TK_DISPLAY_PWD, $newtext);
		$newtext = str_replace(TK_UID, TK_DISPLAY_UID, $newtext);
		return $newtext;
	}

	// Count the lines in the text
	function countLInes($text) { 
		return substr_count($text, "\n"); 
	}
	
	/**
	 * Formats a JSON string for pretty printing
	 *
	 * @param string $json The JSON to make pretty
	 * @param bool $html Insert nonbreaking spaces and <br />s for tabs and linebreaks
	 * @return string The prettified output
	 * @author Jay Roberts
	 */
	 function format_json($json, $html = false) {
		$tabcount = 0; 
		$result = ''; 
		$inquote = false; 
		$ignorenext = false; 
	
		if ($html) { 
			$tab = "&nbsp;&nbsp;&nbsp;"; 
			$newline = "<br/>"; 
		} else { 
			$tab = "    "; 
			$newline = "\n"; 
		} 
	
		for($i = 0; $i < strlen($json); $i++) { 
			$char = $json[$i]; 
	
			if ($ignorenext) { 
				$result .= $char; 
				$ignorenext = false; 
			} else { 
				switch($char) { 
					case '{': 
						$tabcount++; 
						$result .= $char . $newline . str_repeat($tab, $tabcount); 
						break; 
					case '}': 
						$tabcount--; 
						$result = trim($result) . $newline . str_repeat($tab, $tabcount) . $char; 
						break; 
					case ',': 
						$result .= $char . $newline . str_repeat($tab, $tabcount); 
						break; 
					case '"': 
						$inquote = !$inquote; 
						$result .= $char; 
						break; 
					case '\\': 
						if ($inquote) $ignorenext = true; 
						$result .= $char; 
						break; 
					default: 
						$result .= $char; 
				} 
			} 
		} 
	
		return $result; 
	}

	// Remove any un-safe values to prevent email injection
	function smcf_filter($value) {
		$pattern = array("/\n/","/\r/","/content-type:/i","/to:/i", "/from:/i", "/cc:/i");
		$value = preg_replace($pattern, "", $value);
		return $value;
	}
?>