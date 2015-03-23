<?php
class Configuracion {
	//Base de datos
	const URL = 'localhost';
	const USUARIO = "bidonadm";
	const PASSWD = "password";
	const BASEDEDATOS = "bidon";
	const PUERTO = 3306;
	const SOCKET = '/tmp/mysql.sock';
	//Modelos
	const ARTICULO = 'src/main/modelo/Articulo.php';
	const CALIFICACION = 'src/main/modelo/Calificacion.php';
	const CATEGORIA = 'src/main/modelo/Categoria.php';
	const DIRECCION = 'src/main/modelo/Direccion.php';
	const ENVIO = 'src/main/modelo/Envio.php';
	const ESTADOSUBASTA = 'src/main/modelo/EstadoSubasta.php';
	const ESTADOUSUARIO = 'src/main/modelo/EstadoUsuario.php';
	const IMAGEN = 'src/main/modelo/Imagen.php';
	const MENSAJE = 'src/main/modelo/Mensaje.php';
	const OFERTA = 'src/main/modelo/Oferta.php';
	const PAGO = 'src/main/modelo/Pago.php';
	const ROL = 'src/main/modelo/Rol.php';
	const SUBASTA = 'src/main/modelo/Subasta.php';
	const TARJETACREDITO = 'src/main/modelo/TarjetaCredito.php';
	const TARJETACREDITOUSUARIO = 'src/main/modelo/TarjetaCreditoUsuario.php';
	const TIPOENVIO = 'src/main/modelo/TipoEnvio.php';
	const TIPOPAGO = 'src/main/modelo/TipoPago.php';
	const TIPOSUBASTA = 'src/main/modelo/TipoSubasta.php';
	const USUARIO = 'src/main/modelo/Usuario.php';
	const USUARIODIRECCION = 'src/main/modelo/UsuarioDireccion.php';
	//Controlador
	const PROCESADORDERESPUESTAS = 'src/main/controlador/procesadorDeRespuestas.php';
	const NEGOCIOS = 'src/main/negocios/Negocios.php';
	//Recuperacion de datos
	const LECTURAMYSQL = 'src/main/lectura_escritura_datos/LecturaMySql.php';
	//Mantenimiento de datos
	const ESCRITURAMYSQL = 'src/main/lectura_escritura_datos/EscrituraMySql.php';
}


?>