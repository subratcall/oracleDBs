<?php

//Include Header
require_once("header.php");

//Index.php only contains forms which are submitted to either
//transcript.php, add-drop.php, or studentinfo.php
//A Student ID is submitted and used for each of these pages to retrieve and update information.

?>

	<body>
		<div class="container">
			<div class="form_container">
				<h1>Transcript</h1>
				<form id="id_form" class="ui form segment" action="transcript.php" method="POST">
				 	
				 	<div class="field">
				 		<label>Enter Student ID</label>
				 		<div class="ui left labeled icon input">
					    	<input type="text" class="form-control" placeholder="(ex. Z123)" name="studentid">
				  			<i class="user icon"></i>
				  		</div>
				  		<button type="submit" class="ui blue submit button">Enter</button>
				  	</div>

				</form>

			</div>

			
			<div class="form_container">
				<h1>Student Information</h1>
				<form id="id_form" class="ui form segment" action="studentinfo.php" method="POST">
				 	
				 	<div class="field">
				 		<label>Enter Student ID</label>
				 		<div class="ui left labeled icon input">
					    	<input type="text" class="form-control" placeholder="(ex. Z123)" name="studentid">
				  			<i class="user icon"></i>
				  		</div>
				  		<button type="submit" class="ui blue submit button">Enter</button>
				  	</div>

				</form>

			</div>

			<div class="form_container">
				<h1>Add/Drop</h1>
				<form id="id_form" class="ui form segment" action="add-drop.php" method="POST">
				 	
				 	<div class="field">
				 		<label>Enter Student ID</label>
				 		<div class="ui left labeled icon input">
					    	<input type="text" class="form-control" placeholder="(ex. Z123)" name="studentid">
				  			<i class="user icon"></i>
				  		</div>
				  		<button type="submit" class="ui blue submit button">Enter</button>
				  	</div>

				</form>

			</div>

		</div>
	</body>

</html>