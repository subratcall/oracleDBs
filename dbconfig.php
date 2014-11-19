<?php

	//Establish Database Connection
	$conn = oci_connect("kfiol", "orakle", "dboracle.eng.fau.edu/r11g");

	if (!$conn) {
	   $m = oci_error();
	   echo $m['message'], "\n";
	   exit;
	}

?>