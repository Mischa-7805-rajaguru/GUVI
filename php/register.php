<?php

require_once "dbconfig.php";

if(isset($_POST["newusername"]) && isset($_POST["newemail"]) && isset($_POST["newpassword"]))
{	
	$username = $_POST["newusername"];

	$email = $_POST["newemail"];

	$password = $_POST["newpassword"];
	
	$stmt=$db->prepare("INSERT INTO tbl_user(username,
											 email, 
											 password)
										VALUES
										    (:uname,
											 :uemail,
											 :upassword)"); 
	
		
	$stmt->bindParam(":uname",$username);
	$stmt->bindParam(":uemail",$email);
	$stmt->bindParam(":upassword",$password);
		 		
	if($stmt->execute())
	{
		echo '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button> 
					Register Successfully  
			 </div>';		
	}	
	else
	{
		echo  '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button> 
					Fail to Register  
			   </div>';		
	}
}

?>
