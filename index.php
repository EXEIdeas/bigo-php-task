<?php
$dbHost = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "bigo-php-task";
/****************************************************************************/
$connection = mysqli_connect($dbHost, $dbUsername, $dbPassword, $dbName);
?>
<!DOCTYPE html>
<html>
<head>
<title>bigo-php-task</title>
<style type="text/css">
body {text-align:center;}
fieldset {width:300px;margin:50px auto;}
</style>
<?php
$incomingName = "";
$incomingEmail = "";
$incomingURL = "";
$incomingLetter = "";
$incomingFileURL = "";
			
if(isset($_POST['search'])){	
	$incomingEmail = $_POST['email'];
	
	$garb = mysqli_query($connection, "SELECT * FROM applicants WHERE email = '$incomingEmail'");
	if(!$garb){
		echo "ERROR: '".mysqli_error($connection)."<br/>";
	} else {
		if(mysqli_num_rows($garb)) {
			$row = mysqli_fetch_array($garb);
			$incomingName = $row['name'];
			$incomingEmail = $row['email'];
			$incomingURL = $row['url'];
			$incomingLetter = $row['letter'];
			$incomingFileURL = $row['file'];
		}
	}
}

if(isset($_POST['submit'])){	
	$defaultStatus = "Pending Review";
	$incomingName = $_POST['name'];
	$incomingEmail = $_POST['email'];
	$incomingURL = $_POST['url'];
	$incomingLetter = $_POST['letter'];
	$incomingFile = $_FILES['file'];
	
	$garb = mysqli_query($connection, "SELECT * FROM applicants WHERE email = '$incomingEmail'");
	if(!$garb){
		echo "ERROR: '".mysqli_error($connection)."<br/>";
	} else {
		if(mysqli_num_rows($garb)) {
			echo "NOTICE: Your Application is already Exists.<br/>";	
			$row = mysqli_fetch_array($garb);
			$incomingID = $row['id'];
			$incomingStatus = $row['status'];
			if($incomingStatus == "Pending Review") {
				if($incomingFile["name"]) {
					$folderPath = "uploads";
					$temporary = explode(".", $incomingFile["name"]);
					$file_name = reset($temporary);
					$file_extension = end($temporary);
					if (file_exists($folderPath."/".$file_name."-".date('Y-m-d-H-i-s').$file_extension)) {
						echo "ERROR: File Already Exists.<br/>";
					} else {
						$sourcePath = $incomingFile['tmp_name'];
						$targetPath = $folderPath."/".$file_name."-".date('Y-m-d-H-i-s').".".$file_extension;
						move_uploaded_file($sourcePath,$targetPath);
						$incomingFileURL = $targetPath;
						
						$garb = mysqli_query($connection, "UPDATE applicants SET name = '$incomingName', url = '$incomingURL', letter = '$incomingLetter', file = '$incomingFileURL' WHERE id='$incomingID'");
						if(!$garb){
							echo "ERROR: '".mysqli_error($connection)."<br/>";
						} else {	
							echo "SUCCESS: You Data is now Updated.<br/>";
						}		
					}
				}
				
			} else {
				echo "ERROR: You Can't Update Your Application Now.<br/>";
			}
		} else {			
			$row = mysqli_fetch_array($garb);
			$incomingStatus = $row['status'];
			if($incomingFile["name"]) {
				$folderPath = "uploads";
				$temporary = explode(".", $incomingFile["name"]);
				$file_name = reset($temporary);
				$file_extension = end($temporary);
				if (file_exists($folderPath."/".$file_name."-".date('Y-m-d-H-i-s').$file_extension)) {
					echo "ERROR: File Already Exists.<br/>";
				} else {
					$sourcePath = $incomingFile['tmp_name'];
					$targetPath = $folderPath."/".$file_name."-".date('Y-m-d-H-i-s').".".$file_extension;
					move_uploaded_file($sourcePath,$targetPath);
					$incomingFileURL = $targetPath;
					
					$garb = mysqli_query($connection, "INSERT INTO applicants (status, name, email, url, letter, file) VALUES ('$defaultStatus', '$incomingName', '$incomingEmail', '$incomingURL', '$incomingLetter', '$incomingFileURL')");
					if(!$garb){
						echo "ERROR: '".mysqli_error($connection)."<br/>";
					} else {
						echo "SUCCESS: Your Application is Submitted.<br/>";		
					}			
				}
			}
		}
	}
}
?>
</head>
<body>
	<fieldset>
		<legend>Search and Update</legend><br>
		<form method="POST" action=""  enctype="multipart/form-data">
			<label for="email">Email:</label><br>
			<input type="email" name="email" required><br><br>
			<input type="submit" name="search" value="Search">
		</form>
	</fieldset>
	<fieldset>
		<legend>Apply Now</legend><br>
		<form method="POST" action=""  enctype="multipart/form-data">
			<label for="name">Name:</label><br>
			<input type="text" name="name" value="<?php echo $incomingName ?>" required><br><br>
			<label for="email">Email:</label><br>
			<input type="email" name="email" value="<?php echo $incomingEmail ?>" required><br><br>
			<label for="url">URL:</label><br>
			<input type="url" name="url" value="<?php echo $incomingURL ?>" required><br><br>
			<label for="letter">Cover Letter:</label><br>
			<textarea name="letter" rows="4" cols="35" required><?php echo $incomingLetter ?></textarea><br><br>
			<label for="file">CV:</label><br>
			<a href="<?php echo $incomingFileURL ?>"  target="_blank"><?php echo $incomingFileURL ?></a>
			<input type="file" name="file" required><br><br><br>
			<input type="submit" name="submit" value="Submit">
		</form>
	</fieldset>	
</body>
</html>
