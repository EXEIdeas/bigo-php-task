<?php
$dbHost = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "bigo-php-task";
/****************************************************************************/
$connection = mysqli_connect($dbHost, $dbUsername, $dbPassword, $dbName);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$loginStatus = false;
if ( isset($_SESSION['adminEmail']) && isset($_SESSION['adminPassword']) ) {
	$loginStatus = true;
	$globalEmail = $_SESSION['adminEmail'];
	$globalPassword = $_SESSION['adminPassword'];
}

if(isset($_POST['login'])){
	$incomingEmail = $_POST['email'];
	$incomingPassword = $_POST['password'];
	
	if($incomingEmail == "admin@admin.com" && $incomingPassword == "0000" ){
		echo "SUSSESS: You Are Now Logged In.<br/>";
		$_SESSION = array(); // Clears the $_SESSION Variables
		session_destroy(); // Destroy The Session
		session_start();
		$_SESSION['adminEmail'] = $incomingEmail;
		$_SESSION['adminPassword'] = $incomingPassword;
		$loginStatus = true;
	} else {
		
	}
}


if(isset($_POST['logout'])){
	$_SESSION = array(); // Clears the $_SESSION Variables
	session_destroy(); // Destroy The Session
	header("Location: admin.php"); // Redirect To Home Page 
}

if(isset($_POST['change'])){	
	$incomingID = $_POST['id'];
	$incomingStatus = $_POST['status'];
	$garb = mysqli_query($connection, "UPDATE applicants SET status = '$incomingStatus' WHERE id=$incomingID");
	if(!$garb){
		echo "ERROR: '".mysqli_error($connection)."<br/>";
	} else {	
		echo "SUCCESS: You Status is now Changed.<br/>";
	}		
}
?>
<!DOCTYPE html>
<html>
<head>
<title>bigo-php-task</title>
<style type="text/css">
body {text-align:center;}
fieldset {width:300px;margin:50px auto;}
table {width:100%;margin:50px auto;}
th,td {padding:10px;;}
</style>
<script type='text/javascript'>

</script>

</head>
<body>
	<?php if($loginStatus == false){ ?>
	<fieldset>
		<legend>Search and Update</legend><br>
		<form method="POST" action=""  enctype="multipart/form-data">
			<label for="email">Email: (admin@admin.com)</label><br>
			<input type="email" name="email" required><br><br>
			<label for="name">Password: (0000)</label><br>
			<input type="password" name="password" required><br><br>
			<input type="submit" name="login" value="Login">
		</form>
	</fieldset>
	<?php } ?>
	
	<?php if($loginStatus == true){ ?>	
	<form method="POST" action=""  enctype="multipart/form-data">
		<input type="submit" name="logout" value="Logout">
	</form>
	
	<h1>Applicants List</h1>
	<table border="1">	
	<?php
	$garb = mysqli_query($connection, "SELECT * FROM applicants ORDER by id DESC");
	if(!$garb) {
		echo "ERROR: ".mysqli_error($connection)."";
	} else {
		echo '<tr><th>ID</th><th>Status</th><th>Name</th><th>Email</th><th>URL</th><th>Letter</th><th>File</th><th>Action</th></tr>';
		while($row = mysqli_fetch_array($garb)) {
			$incomingID = $row['id'];
			$incomingStatus = $row['status'];
			$incomingName = $row['name'];
			$incomingEmail = $row['email'];
			$incomingURL = $row['url'];
			$incomingLetter = $row['letter'];
			$incomingFile = $row['file'];
			echo '<tr><td>'.$incomingID.'</td><td>'.$incomingStatus.'</td><td>'.$incomingName.'</td><td>'.$incomingEmail.'</td><td>'.$incomingURL.'</td><td>'.$incomingLetter.'</td><td><a href="'.$incomingFile.'"  target="_blank">'.$incomingFile.'</a></td><td><form method="POST" action=""  enctype="multipart/form-data">
				<select name="status" required>
					<option value="Pending Review">Pending Review</option>
					<option value="Ready to Interview" selected>Ready to Interview</option>
					<option value="Archived">Archived</option>
				</select>
				<input type="hidden" name="id" value="'.$incomingID.'">
				<input type="submit" name="change" value="Change">
			</form></td></tr>';
		}
	}
	?>
	</table>	
	<?php } ?>
</body>
</html>
