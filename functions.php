<?php

	//Function used to calculate GPA value for Letter Grades
	function GPACalc($grade){
		switch($grade){
			case 'A':
				return 4;
			case 'B':
				return 3;
			case 'C':
				return 2;
			case 'D':
				return 1;
			case 'F':
				return 0;
			default:
				return 0;
		}
	}

?>