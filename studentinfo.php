<?php

// 2.	Develop a mini-system that will accept a Student_ID from the Web or Console.  
// If this is a web page, the system will look up the student’s information and display 
// it in a new web page that can be updated with new information.  If this is from a 
// console program, then the student’s information will be displayed, and each field will
// be queried.  The program will then update the information for the student.  
// Remember that dates have to have special processing.  This should work in a similar 
// fashion as the Instructor Update.  This assignment is worth six points with no bonus points available.

//Include Database Settings
require_once("dbconfig.php");

//Include Functions
include("functions.php");

//Include Header
require_once("header.php");

if(empty($_POST["upd_id"])
	&& empty($_POST["upd_last"])
	&& empty($_POST["upd_first"])
	&& empty($_POST["upd_gender"])
	&& empty($_POST["upd_birth"])
	&& empty($_POST["upd_city"])
	&& isset($_POST["studentid"])
) {
	//Retrieve Student ID
	$studentid = $_POST["studentid"];
}


else {
	//Retrieve Student ID
	if(!empty($_POST["upd_id"])){
		$studentid = $_POST["upd_id"];
	}
	else {
		$studentid = $_POST["studentid"];
	}

	$original = $_POST["studentid"];

	//Update Changes
	$subq = array();

	if(!empty($_POST["upd_id"]))
		$subq[] = "STUDENT_ID = '" . $_POST["upd_id"] . "'";
	if(!empty($_POST["upd_last"]))
		$subq[] = "LAST_NAME = '" . $_POST["upd_last"] . "'";
	if(!empty($_POST["upd_first"]))
		$subq[] = "FIRST_NAME = '" . $_POST["upd_first"] . "'";
	if(!empty($_POST["upd_gender"]))
		$subq[] = "GENDER = '" . $_POST["upd_gender"] . "'";
	if(!empty($_POST["upd_birth"]))
		$subq[] = "BIRTH_DATE = '" . $_POST["upd_birth"] . "'";
	if(!empty($_POST["upd_city"]))
		$subq[] = "CITY = '" . $_POST["upd_city"] . "'";

	$subq = implode(', ', $subq);
	$updateQuery = "update STUDENT set " . $subq . " where STUDENT_ID = '" . $original . "'";
	
	$updateArray = oci_parse($conn, $updateQuery);
	oci_execute($updateArray);
	oci_commit($conn);
}


//Create Transcript Query
$query = "
	select
	*
	from
	STUDENT
	where
	STUDENT_ID = '" . $studentid . "'
";

//Parse Associative Array from Query
$array = oci_parse($conn, $query);
oci_execute($array);



?>

	<body>
		<div class="container">
			<?php echo "<h1> Student Information for " . $studentid . "</h1>"; ?>
			<table class="ui table segment">
				<thead>
					<tr>
						<th>Student ID</th>
						<th>Last Name</th>
						<th>First Name</th>
						<th>Gender</th>
						<th>Birth Date</th>
						<th>City</th>
					</tr>
				</thead>
				<tbody>
					<?php
						//Loop through Query result and Create Table Rows
						while($row = oci_fetch_assoc($array)){
							echo "<tr>";
							foreach($row as $col){
								echo "<td>". $col . "</td>";
							}
							echo "</tr>";
						}
					?>
				</tbody>
			</table>

			<h1>Update Info</h1>
			<table class="ui table segment">
				<thead>
					<tr>
						<th>Student ID</th>
						<th>Last Name</th>
						<th>First Name</th>
						<th>Gender</th>
						<th>Birth Date</th>
						<th>City</th>
					</tr>
				</thead>

			<form action="studentinfo.php" class="ui form" method="POST">
				<tbody>
					<tr>
						<td><div class="ui left input"><input disabled="disabled" type="text" placeholder="<?php echo $studentid; ?>" class="form-control" name="upd_id"></div></td>
						<td><div class="ui left input"><input type="text" class="form-control" name="upd_last"></div></td>
						<td><div class="ui left input"><input type="text" class="form-control" name="upd_first"></div></td>
						<td><div class="ui left input"><input type="text" class="form-control" name="upd_gender"></div></td>
						<td><div class="ui left input"><input type="text" class="form-control" name="upd_birth"></div></td>
						<td><div class="ui left input"><input type="text" class="form-control" name="upd_city"></div></td>
					</tr>
				</tbody>
			</table>

			<!-- Hidden Input Field To Pass Back Unchanged Student ID -->
			<input type='hidden' name='studentid' value="<?php echo $studentid; ?>">
			<button type="submit" class="ui blue submit button">Enter</button>
			</form>
			
		</div>
	</body>

</html>