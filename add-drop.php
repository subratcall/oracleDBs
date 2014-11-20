<?php

// 3.	Develop a mini-system that will accept a Student_ID  and Semester from the Web/Form or Console, 
// and then allow the student to add or drop classes to their Transcript.  You should add classes for 
// the SPRING2015 semester to your database. As ch course is added or dropped the Web Page/Form/Console 
// will be updated with the new information and displayed.  For the Console Program, you may want to have 
// a menu system so that the student can see the classes available and the classes in their schedule.  
// This assignment is worth six points.  Three bonus points will be awarded if a new field is added to 
// the Schedule table with the total number of students allowed to register for the class, and that 
// this field is updated as students drop and add classes.  Basically this mini-system should function
//  in a similar fashion as Drop/Add in Banner (but I’m hoping your version will be much nicer!!)

//Include Database Settings
require_once("dbconfig.php");

//Include Functions
include("functions.php");

//Include Header
require_once("header.php");

//Retrieve Student ID
if(isset($_POST["studentid"])){
	$studentid = $_POST["studentid"];
}

//Retrieve Drop Value If Set
if(isset($_POST["dropvalue"])){
	$dropvalue = $_POST["dropvalue"];
	$drop_query = "
		delete from 
		TRANSCRIPT 
		where 
		SCHEDULE_ID = '" . $dropvalue . "' 
		and 
		STUDENT_ID = '". $studentid . "'
	";
	$drop_array = oci_parse($conn, $drop_query);
	oci_execute($drop_array);
	
}

//Retrieve Add Value If Set
if(isset($_POST["addvalue"])){
	$addvalue = $_POST["addvalue"];
	$add_query = "
		insert into
		TRANSCRIPT
		(SCHEDULE_ID, STUDENT_ID)
		values
		('". $addvalue . "', '". $studentid ."')
	";
	$add_array = oci_parse($conn, $add_query);
	oci_execute($add_array);
}

//Create Transcript Query
$tr_query = "
	select 
	S.COURSE_ID,  C.COURSE_NAME, S.SEMESTER, S.CITY, S.ROOM, 
	S.CLASS_TIME, S.INSTRUCTOR_ID, T.GRADE, C.CREDIT_HOURS
	from
	SCHEDULE S, COURSE C, TRANSCRIPT T
	where
	S.SCHEDULE_ID in (select T.SCHEDULE_ID from TRANSCRIPT where T.STUDENT_ID = '". $studentid . "')
	and
	C.COURSE_ID = S.COURSE_ID
	and
	T.SCHEDULE_ID = S.SCHEDULE_ID
	order by S.SCHEDULE_ID
";

//Student Spring 2015 Schedule
$dr_query = "
	select 
	S.SCHEDULE_ID, S.COURSE_ID,  C.COURSE_NAME, S.SEMESTER, S.CITY, S.ROOM, 
	S.CLASS_TIME, S.INSTRUCTOR_ID, C.CREDIT_HOURS
	from
	SCHEDULE S, COURSE C, TRANSCRIPT T
	where
	S.SCHEDULE_ID in (select T.SCHEDULE_ID from TRANSCRIPT where T.STUDENT_ID = '". $studentid . "')
	and
	C.COURSE_ID = S.COURSE_ID
	and
	T.SCHEDULE_ID = S.SCHEDULE_ID
	and
	S.SEMESTER = 'SPRING2015'
	order by S.SCHEDULE_ID
";

//Complete Spring 2015 Schedule

$ad_query = "
	select distinct
	S.SCHEDULE_ID, S.COURSE_ID,  C.COURSE_NAME, S.SEMESTER, S.CITY, S.ROOM, 
	S.CLASS_TIME, S.INSTRUCTOR_ID, C.CREDIT_HOURS
	from
	SCHEDULE S, COURSE C, TRANSCRIPT T
	where
	C.COURSE_ID = S.COURSE_ID
	and
	S.SEMESTER = 'SPRING2015'
	and
	S.SCHEDULE_ID not in (select T.SCHEDULE_ID from TRANSCRIPT T where T.STUDENT_ID = '". $studentid . "')
	order by S.SCHEDULE_ID
";

//Parse Associative Array from Query
$tr_array = oci_parse($conn, $tr_query);
$dr_array = oci_parse($conn, $dr_query);
$ad_array = oci_parse($conn, $ad_query);
oci_execute($tr_array);
oci_execute($dr_array);
oci_execute($ad_array);

?>

	<body>
		<div class="container">
			<?php echo "<h1> Transcript for " . $studentid . "</h1>"; ?>
			<table class="ui table segment">
				<thead>
					<tr>
						<th>Course ID</th>
						<th>Course</th>
						<th>Semester</th>
						<th>City</th>
						<th>Room</th>
						<th>Class Time</th>
						<th>Instructor</th>
						<th>Grade</th>
						<th>Credit Hours</th>
					</tr>
				</thead>
				<tbody>
					<?php
						//Calculates Total Credit Hours/Grade Points
						$total_hours = 0;
						$total_gpa = 0;
						$classes_taken = 0;

						//Loop through Query result and Create Table Rows
						while($row = oci_fetch_assoc($tr_array)){
							$total_hours += $row['CREDIT_HOURS'];

							if($row['GRADE']){
								$classes_taken++;
								$total_gpa += GPACalc($row['GRADE']);
							}

							echo "<tr>";
							foreach($row as $col){
								echo "<td>". $col . "</td>";
							}
							echo "</tr>";
						}
					?>
				</tbody>
			</table>

			<table class="ui table segment">
				<thead>
					<tr>
						<th>Total Credit Hours</th>
						<th>Overall GPA</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<?php 
							$overall_gpa = number_format((float)$total_gpa/(float)$classes_taken, 2, '.', '');
							echo "<td>" . $total_hours . "</td>";
							echo "<td>" . $overall_gpa . "</td>"; 
						?>
					</tr>
				</tbody>
			</table>

			<!-- Student's Registered Classes -->

			<?php echo "<h1> Registered Classes for " . $studentid . " for Spring 2015</h1>"; ?>
			<table class="ui table segment">
				<thead>
					<tr>
						<th>Course ID</th>
						<th>Course</th>
						<th>Semester</th>
						<th>City</th>
						<th>Room</th>
						<th>Class Time</th>
						<th>Instructor</th>
						<th>Credit Hours</th>
						<th>Drop</th>
					</tr>
				</thead>
				<tbody>
					<form action="add-drop.php" class="ui form" id="dropform" method="POST">
					<?php
						//Loop through Query result and Create Table Rows
						while($row = oci_fetch_assoc($dr_array)){
							echo "<tr>";
							foreach(array_slice($row, 1) as $col){
								echo "<td>". $col . "</td>";
							}
							echo "<td><button type=\"submit\" value = '" . $row['SCHEDULE_ID'] . "' name=\"dropvalue\" class=\"ui red small submit button\">Drop</button></td>";
							echo "</tr>";
						}
					?>
					<input type='hidden' name='studentid' value="<?php echo $studentid; ?>">
					</form>
				</tbody>
			</table>

			<!-- Add Drop for Next Semester -->

			<h1>Spring 2015 Schedule</h1>
			<table class="ui table segment">
				<thead>
					<tr>
						<th>Course ID</th>
						<th>Course</th>
						<th>Semester</th>
						<th>City</th>
						<th>Room</th>
						<th>Class Time</th>
						<th>Instructor</th>
						<th>Credit Hours</th>
						<th>Add</th>
					</tr>
				</thead>
				<tbody>
					<form action="add-drop.php" class="ui form" id="addform" method="POST">
					<?php
						//Loop through Query result and Create Table Rows
						while($row = oci_fetch_assoc($ad_array)){
							echo "<tr>";
							foreach(array_slice($row, 1) as $col){
								echo "<td>". $col . "</td>";
							}
							echo "<td><button type=\"submit\" value = '" . $row['SCHEDULE_ID'] . "' name=\"addvalue\" class=\"ui blue small submit button\">Add</button></td>";
							echo "</tr>";
						}
					?>
					<input type='hidden' name='studentid' value="<?php echo $studentid; ?>">
					</form>
				</tbody>
			</table>
			
		</div>
	</body>

</html>