       
	   <?php
	   
       include 'connection.php';
       session_start();
       if(isset($_POST['button'])){
        $email = filter_input(INPUT_POST,'email', FILTER_SANITIZE_STRING);
        $password = filter_input (INPUT_POST,'password', FILTER_SANITIZE_STRING);


	//Check no fields are empty, if empty display error message.
          if(empty($_POST["email"]) || empty($_POST["password"])){ echo "<center>Please ensure all fields are filled in.</center>"; }else{
			
			//encrypt password
            $Salt = "cf01passwordSalt";
            $password = $password . $Salt;
            $password = sha1($password);
            
            $sql = "SELECT * FROM securityUsers WHERE email = :email
            AND password = :password";
            
            $stmt = $conn->prepare($sql);
            $success = $stmt->execute(['email'=> $email, 'password' => $password]);
            if($success && $stmt->rowCount() > 0){
			header("Location: dashboard.php");
			$_SESSION['loggedIn'] = true;
			$_SESSION['email'] = $email;
       }
       else
       {
        $_SESSION['loginErrorMessage'] = "Sorry, the email and password combonation is incorrect.";
        $_SESSION['loggedIn'] = false;
		header('Location: securitysystem.php');
        
      }
    }
  }

?>