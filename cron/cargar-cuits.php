<?php
ini_set("memory_limit","1024M");
include_once '../wp-config.php';

$servername = DB_HOST;
$username = DB_USER;
$password = DB_PASSWORD;
$dbname = DB_NAME;

try {
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 


	$file = file_get_contents('./files/cuits.txt', true);



	$arr_cuits = explode(PHP_EOL, $file);

	$sql = "TRUNCATE table cuits;";

	if (!$conn->query($sql) === TRUE) {
	    echo "Error: " . $sql . "<br>" . $conn->error;
	}

	$sql = "INSERT INTO cuits (nro, fecha) VALUES ";

	$values = [];
	$today = date("Y-m-d");
	foreach($arr_cuits as $cuit) {
		$values[] = "('$cuit','$today')";
	}

	$sql.= implode(',',$values);

	if (!$conn->query($sql) === TRUE) {
	    echo "Error: " . $sql . "<br>" . $conn->error;
	}

	$conn->close();

	echo "<script>document.location = 'https://www.finba.com.ar/wp-admin/';</script>";

} catch( \Exception $e ) {
	echo $e->getMessage();
	exit;
}