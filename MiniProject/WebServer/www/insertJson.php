<?php

include_once 'connection.php';

if (isset($_POST['Data'])) {
#$intrusionDate=$_POST['intrusionDate'];
#$intrusionTime=$_POST['intrusionTime'];
$data=$_POST['Data'];
$data = json_decode($data);
$resultsjson = json_decode($data);
$intrusionDate=$data->date;
$intrusionTime=$data->time;


		$query = $conn->prepare("
			INSERT INTO securitySystem(intrusionDate, intrusionTime)
			VALUES (:intrusionDate, :intrusionTime)
			
			");

		$success = $query->execute([
			'intrusionDate' => $intrusionDate,
			'intrusionTime' => $intrusionTime
		]);

		$count = $query->rowCount();

		if ($count > 0) {
			echo"Insert Successful";
			echo "Date"+intrusionDate;
			echo "Date"+intrusionTime;
		} else {
			echo"Submission Failedd.";
			
		}

}else{
	echo "Failed";
}
?>