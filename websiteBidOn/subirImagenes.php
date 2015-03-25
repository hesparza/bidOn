<?php
error_reporting ( E_ERROR | E_PARSE );
session_start ();

$fn = (isset(apache_request_headers()['X-Filename']) ? apache_request_headers()['X-Filename'] : false);

if ($fn) {
	// AJAX call
	if(isset($_SESSION["nomUsuario"])) {
		$path = $_SERVER['DOCUMENT_ROOT'] . '/websiteBidOn/imagenes_subastas/' . $_SESSION["nomUsuario"] . '/tmp/';
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
// echo 'Path: ' . $path . '';
// echo '<br/> $_SESSION["nomUsuario"]= '. $_SESSION["nomUsuario"];
// echo '<br /> fn= ' .$fn;
// $asd = apache_request_headers();
// echo '<br /> apache_request_headers()[X-FILENAME] = ' . $asd['X-Filename'];
// print_r(apache_request_headers());
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