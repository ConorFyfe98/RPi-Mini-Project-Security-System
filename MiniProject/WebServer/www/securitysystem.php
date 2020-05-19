<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src='https://www.google.com/recaptcha/api.js'></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script>
	function passwordVisibility() {
    var password = document.getElementById("password");
    if (password.type === "password") {
        password.type = "text";
    } else {
        password.type = "password";
    }
} 
	
	</script>
  <style type="text/css" media="all">
  
  @import "style.css"
  
</style>
<title>Medication system login</title>
</head>
<body>
 <!-- Header-->
  <div class="jumbotron" style="background-color:#0F4C81">
  <center><h1 style="color:white">Security System</h1></center>     
</div>


  <!--Columns-->
  <div class="container">
    <div class="row">

      <div class="col">
      </div>
      <div class="col-8">
       <div class="form-group">
        <form id="attempt_login" method="POST" name="login" action="login.php">
         <p class="text-center h3 mb-4">Welcome</p>
         <label for="email">Email: </label>
         <input type="text" class="form-control" name="email" id="email" maxlength="255" placeholder="Email" ><br>
        
		 <label for="password">Password: </label>
         <input type="password" class="form-control" name="password" id="password" maxlength="500" placeholder="Password" ><br>
		
		 <label for="checkbox">Show password: </label>
		 <input type="checkbox" onclick="passwordVisibility()" name ="checkbox" id ="checkbox">
		 
         <center><button name = "button" class="btn btn-primary" id="submit" type="submit" value="Log in">Sign in</button></center>
       </form>
      	   
		   <?php if(!empty($_SESSION['loginErrorMessage'])){
		   echo "<center>". $_SESSION['loginErrorMessage']. "</center>";
	   }		   
	   unset($_SESSION['loginErrorMessage']);?>
</div>
	   </div>

<div class="col">
</div>

</div>	
</div>
</div>

</body>
</html>