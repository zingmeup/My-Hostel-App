<?php

function logger($colorcode, $tag, $message){
	switch ($colorcode) {
		case 1: echo "<br><p style=\"color: grey;\">LOG: Verbose- ".$tag.": ".$message." </p>";
			# code...
			break;
		case 2:  echo "<br><p style=\"color: green;\">LOG: Debug- ".$tag.": ".$message." </p>";
			# code...
			break;
		case 3:  echo "<br><p style=\"color: lightred;\">LOG: Warning- ".$tag.": ".$message." </p>";
			# code...
			break;
		case 4:  echo "<br><p style=\"color: red;\">LOG: Error- ".$tag.": ".$message." </p>";
			# code...
			break;

		default: echo "<br><p style=\"color: grey;\">LOG: Verbose- ".$tag.": ".$message." </p>";
			# code...
			break;
	}

}
