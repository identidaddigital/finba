<?php
header('Content-type: application/json');
include_once '../wp-config.php';

$servername = DB_HOST;
$username = DB_USER;
$password = DB_PASSWORD;
$dbname = DB_NAME;

$result = [
	'success' => false,
	'message' => null,
	'exists' => false
];

try {
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
		$result = [
			'success' => false,
			'message' => "Connection failed: " . $conn->connect_error,
			'exists' => false
		];
	    
	    return json_encode($result);
	} 

	$nro = str_replace('-', '', $_REQUEST['nro']);


	$consulta = "SELECT 1 from cuits where nro = '$nro' LIMIT 0,1";

	if ($resultado = $conn->query($consulta)) {

		if ($data = $resultado->fetch_object()) {
			$result = [
				'success' => true,
				'message' => null,
				'exists' => true 
			];		
		} else {
			$result = [
				'success' => true,
				'message' => null,
				'exists' => false 
			];				
		}


		$resultado->close();
	}

	$conn->close();

	echo json_encode($result);

} catch( \Exception $e ) {
	echo $e->getMessage();
	exit;
}