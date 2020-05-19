<?php
session_start();
?>



<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>Security System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
  
  
  
  <script type="text/javascript" src="jquery-1.3.2.js"> </script>

 <script type="text/javascript">

 function loadData(criteria) 
   {
	   var val = criteria;
	   var rows = document.getElementById('rows').value;
       
      $.ajax({    //create an ajax request to display.php
        type: "POST",
        url: "display.php",  
		data: {searchCriteria: val, numberRows: rows},	
        dataType: "html",   //expect html to be returned                
        success: function(response){                    
            $("#responsecontainer").html(response); 
            //alert(response);
        }

    });
   }
  
 $(document).ready(function() {
	 var val = "displayAll"
	 loadData(val);

    $("#display").click(function() {                
		var val = "displayAll"
		loadData(val);
});

    $("#latest").click(function() {                
		var val = "displayLatest"
		loadData(val);
});


    $("#oldest").click(function() {                
		var val = "displayOldest"
		loadData(val);
});

});
  
</script>
  
  
  </head>
  <body>
  <div class="jumbotron" style="background-color:#0F4C81">
  <center><h1 style="color:white">Security System</h1></center>     
</div>
  
  <div class="container">
  <div class="row">
    <div class="col">
    </div>
<?php
if (isset($_SESSION['loggedIn'])) {
    if ($_SESSION['loggedIn']) {

        echo "<center>". $_SESSION['email']. " | <a href='logout.php'>Log Out</a></center>";
    }

} else { // if user is not logged in display Register and Log in page link
	header("Location: securitysystem.php");
}
?>
	
    <div class="col-6">
	<h3>Operations</h3>
	<form method="POST">
	<td> <button type="submit" name="lock" class="btn btn-warning" id="lock">Lock</button></td>
	<td> <button type="submit" name="unlock" class="btn btn-warning" id="unlock">Unlock</button></td>
	</form>
	
<?php
if(isset($_POST['lock'])){
$command = escapeshellcmd('python3 /home/ubuntu/python/publisher.py Lock');
$output = shell_exec($command);
echo $output;
}

if(isset($_POST['unlock'])){
$command = escapeshellcmd('python3 /home/ubuntu/python/publisher.py Unlock');
$output = shell_exec($command);
echo $output;
}

?>
	<hr>
	<center><h4> The table displays the date and time that the system detected an intrusion.</h4></center>
	<p>Number of rows : <select id="rows" name="rows">
		<option value="10">10</option>
		<option value="30">30</option>
		<option value="50">50</option>
		<option value="All">All</option>
	</select>
	</p>
	
	<td> <button type="button" class="btn btn-primary" id="display">Refresh Data</button></td>
	<td> <button type="button" class="btn btn-primary" id="oldest">Oldest</button></td>
	<td> <button type="button" class="btn btn-primary" id="latest">Latest</button></td>
	<br>
	<div id="responsecontainer" align="center">

</div>
    <div class="col">
    </div>
  </div>
</div>
  </body>
</html>
