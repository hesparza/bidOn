<?php
error_reporting ( E_ERROR | E_PARSE );
session_start ();

$fn = (isset($_SERVER['HTTP_X_FILENAME']) ? $_SERVER['HTTP_X_FILENAME'] : false);

if ($fn) {
	// AJAX call
	if(isset($_SESSION["nomUsuario"])) {
		
		$path = 'imagenes_subastas/' . $_SESSION["nomUsuario"] . '/tmp/';
		if (!file_exists($path)) {
			mkdir($path, 0777, true);
		}
		file_put_contents(
		$path . $fn,
		file_get_contents('php://input')
		);
		echo "$fn uploaded";
		exit();		
	}
}
// else {

// 	// form submit
// 	$files = $_FILES['fileselect'];

// 	foreach ($files['error'] as $id => $err) {
// 		if ($err == UPLOAD_ERR_OK) {
// 			$fn = $files['name'][$id];
// 			move_uploaded_file(
// 				$files['tmp_name'][$id],
// 				'uploads/' . $fn
// 			);
// 			echo "<p>File $fn uploaded.</p>";
// 		}
// 	}

// }

?>