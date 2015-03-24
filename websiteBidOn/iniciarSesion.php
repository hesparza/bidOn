<?php
session_start();
$_SESSION["id"] = htmlspecialchars($_GET["id"]);
$_SESSION["estadoUsuarioId"] = htmlspecialchars($_GET["estadoUsuarioId"]);
$_SESSION["rol"] = htmlspecialchars($_GET["rol"]);
$_SESSION["nombre"] = htmlspecialchars($_GET["nombre"]);
$_SESSION["apellidoP"] = htmlspecialchars($_GET["apellidoP"]);
$_SESSION["apellidoM"] = htmlspecialchars($_GET["apellidoM"]);
$_SESSION["correo"] = htmlspecialchars($_GET["correo"]);
$_SESSION["nomUsuario"] = htmlspecialchars($_GET["nomUsuario"]);
$_SESSION["reputacion"] = htmlspecialchars($_GET["reputacion"]);
header("Location: index.php");
die();
?>