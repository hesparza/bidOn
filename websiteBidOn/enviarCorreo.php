<?php
$correo = $HTTP_POST_VARS['correo'];
$asunto = $HTTP_POST_VARS['asunto'];
$mensaje = $HTTP_POST_VARS['mensaje'];

if (!preg_match("/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/", $correo)) {
  $respuesta['respuesta'] = false;
} elseif ($asunto == "") {
  $respuesta['respuesta'] = false;
}

elseif (mail(administracion@bidonequipo5.com,$asunto,'de: ' . $correo. '\nmensaje:' . $mensaje)) {
  $respuesta['respuesta'] = true;
} else {
  $respuesta['respuesta'] = false;
}
echo json_encode($respuesta);
?>
