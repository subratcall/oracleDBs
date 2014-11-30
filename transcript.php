<?php

// 1.	Develop a mini-system that will accept a Student_ID from a web–page/form or 
// from the console and the system will then generate a transcript for that student.  
// Please go look at your transcript and use something similar to that format.   
// Generating a list sorted in order by semester but with just a final credit hour 
// total and GPA for all courses taken will be assigned six points.  Including a
// heading line and a total line/GPA for each semester and then a total GPA at 
// the end of the transcript will award two bonus points.  In either case, take 
// the time to make it some-what pretty (don’t just dump out the data into a table
// or display it on the console!!)

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

//Create Transcript Query
$query = "
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

//Parse Associative Array from Query
$array = oci_parse($conn, $query);
oci_execute($array);

//Variables for Each Semester
$temp = oci_fetch_assoc($array);
$currentSemester = $temp['SEMESTER'];
$sem_points = 0;
$sem_hours = 0;
$sem_taken = 0;

$overall_points = 0;
$overall_hours = 0;
$overall_taken = 0;

?>

	<body>
		<a href="index.php"><div class="ui left labeled icon button">
		  <i class="left arrow icon"></i>
		  Back
		</div></a>
		<div class="container">
			<?php 
				echo "<h1> Student Transcript for " . $studentid . "</h1>"; 
				echo "<h1>" . $currentSemester . "</h1>";
			?>
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

						

						//Reset Array for Looping
						//Consider Placing Fetched Assoc Arrays into PHP Array
						oci_execute($array);

						//Loop through Query result and Create Table Rows
						while($row = oci_fetch_assoc($array)){

							//If New Semester
							if($row['SEMESTER'] != $currentSemester){
								//Echo New Table + GPA Points
								echo "
									<!-- End Last Table -->
										</tbody>
									</table>

									<!-- Append GPA + Credits Table -->
									<table class=\"ui table segment\">
										<thead>
											<tr>
												<th>Semester Credit Hours</th>
												<th>Semester GPA</th>
											</tr>
										</thead>
										<tbody>
											<tr>";
													//Gather Overall Data For Semester
													$overall_points += $sem_points;
													$overall_hours += $sem_hours;
													$overall_taken += $sem_taken;

													//Display GPA and Hours For Semester
													if($sem_taken != 0)
														$sem_gpa = number_format((float)$sem_points/(float)$sem_taken, 2, '.', '');
													else
														$sem_gpa = "N/A";
													echo "<td>" . $sem_hours . "</td>";
													echo "<td>" . $sem_gpa . "</td>"; 
							
								echo "		</tr>
										</tbody>
									</table>

									<!-- Begin New Table -->
									<h1>". $row['SEMESTER'] . "</h1>
									<table class=\"ui table segment\">
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
								";

								//Reset currentSemester + Semester Variables
								$currentSemester = $row['SEMESTER'];
								$sem_hours = 0;
								$sem_points = 0;
								$sem_taken = 0;
							}

							$sem_hours += $row['CREDIT_HOURS'];

							if($row['GRADE']){
								$sem_taken++;
								$sem_points += GPACalc($row['GRADE']);
							}

							//Create New Class Row
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
						<th>Semester Credit Hours</th>
						<th>Semester GPA</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<?php 
							//Gather Overall Data For Semester
							$overall_points += $sem_points;
							$overall_hours += $sem_hours;
							$overall_taken += $sem_taken;

							if($sem_taken != 0)
								$sem_gpa = number_format((float)$sem_points/(float)$sem_taken, 2, '.', '');
							else
								$sem_gpa = "N/A";
							echo "<td>" . $sem_hours . "</td>";
							echo "<td>" . $sem_gpa . "</td>"; 
						?>
					</tr>
				</tbody>
			</table>

			<!-- Overall GPA and Credit Hours -->
			<h2>Overall GPA and Credit Hours</h2>
			<table class="ui table segment">
				<thead>
					<tr>
						<th>Overall Credit Hours</th>
						<th>Overall GPA</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<?php 
							if($overall_taken != 0)
								$overall_gpa = number_format((float)$overall_points/(float)$overall_taken, 2, '.', '');
							else
								$sem_gpa = "N/A";
							echo "<td>" . $overall_hours . "</td>";
							echo "<td>" . $overall_gpa . "</td>"; 
						?>
					</tr>
				</tbody>
			</table>
			
		</div>
	</body>

</html>