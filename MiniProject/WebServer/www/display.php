<?php

include_once 'connection.php';

$rows=$_POST['numberRows'];
if ($rows == 'All'){
	$sqlLimit="";
}else{
	$sqlLimit = " LIMIT ".$rows."";
}

if ($_POST['searchCriteria'] == 'displayAll') {
    $sql = "SELECT intrusionID, intrusionDate, intrusionTime FROM securitySystem".$sqlLimit."";
}

if ($_POST['searchCriteria'] == 'displayLatest') {
    $sql = "SELECT intrusionID, intrusionDate, intrusionTime FROM securitySystem ORDER BY intrusionDate DESC, intrusionTime DESC".$sqlLimit."";
}

if ($_POST['searchCriteria'] == 'displayOldest') {
    $sql = "SELECT intrusionID, intrusionDate, intrusionTime FROM securitySystem ORDER BY intrusionDate ASC, intrusionTime ASC".$sqlLimit."";
}

$result = $conn->query($sql);

$count = $result->rowCount();

		if ($count > 0) {
			// output data of each row
			echo "<table class='table'>";
			echo "<thead class='thead-light'>";
			echo "<tr>";
			echo "<th scope='col'>ID</th>";
			echo "<th scope='l'>Date</th>";
			echo "<th scope='col'>Time</th>";
			echo "</tr>";
			echo "</thead>";
			echo "<tbody>";
			while ($row = $result->fetch()) {
		echo  "<tr>";
		echo "<th scope='row'>". $row["intrusionID"]."</th>";
		echo "<td>". $row["intrusionDate"]."</td>";
		echo "<td>". $row["intrusionTime"]. "</td>";
		echo "</tr>";
			}
		echo "</tbody>";
		echo "</table>";
		}else {
			echo"Could not find any intrusion records.";	
		}
?>