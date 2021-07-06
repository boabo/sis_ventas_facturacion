<?php
/***
 Nombre: Intermediario.php
 Proposito: Creara la nueva base de datos en el cual se replican las factuas para el libro de ventas
 Autor:	Ismael Valdivia
 Fecha:	11/05/2021
 */

include_once(dirname(__FILE__)."/../../lib/lib_control/CTSesion.php");
session_start();
$_SESSION["_SESION"]= new CTSesion();

include(dirname(__FILE__).'/../../lib/DatosGenerales.php');
include_once(dirname(__FILE__).'/../../lib/lib_general/Errores.php');
include_once(dirname(__FILE__).'/../../lib/rest/PxpRestClient.php');


ob_start();


//estable aprametros ce la cookie de sesion
$_SESSION["_CANTIDAD_ERRORES"]=0;//inicia control


//echo dirname(__FILE__).'LLEGA';
register_shutdown_function('fatalErrorShutdownHandler');
set_exception_handler('exception_handler');
set_error_handler('error_handler');;
include_once(dirname(__FILE__).'/../../lib/lib_control/CTincludes.php');
/*Descomentar para produccion*/
//$pxpRestClient = PxpRestClient::connect('127.0.0.1',substr($_SESSION["_FOLDER"], 1) .'pxp/lib/rest/')->setCredentialsPxp($_GET['user'],$_GET['pw']);

/*Para pruebas en desarrollo*/
$pxpRestClient = PxpRestClient::connect('127.0.0.1',substr($_SESSION["_FOLDER"], 1) .'kerp_ismael/pxp/lib/rest/')->setCredentialsPxp($_GET['user'],$_GET['pw']);

$fecha = new DateTime();
$res = $pxpRestClient->doPost('ventas_facturacion/InsertarNuevaBD/insertarDb',
				    array(	"fecha"=>date_format($fecha, 'd/m/Y')));
$res_json = json_decode($res);

//if ($res_json['ROOT']['error'] == true) {
if ($res_json->ROOT->error == true) {
	//var_dump("respuesta insert notifica",$res_json->ROOT->detalle->mensaje_tec);
	$res1 = $pxpRestClient->doPost('ventas_facturacion/InsertarNuevaBD/enviarNotificacion',
					    						array(	"Salida"=>$res_json->ROOT->detalle->mensaje_tec,
																	"Mensaje"=>"error"));
	$res_json_error = json_decode($res1);
	var_dump("Respuesta Error",$res_json_error);
}
else {
	$res2 = $pxpRestClient->doPost('ventas_facturacion/InsertarNuevaBD/enviarNotificacion',
					    					 array(	"Salida"=>"Correcto",
											 					"Mensaje"=>$res_json->ROOT->datos->MensajeRespuesta));
	$res_json_exito = json_decode($res2);
	var_dump("Respuesta Éxito",$res_json_exito);
}
//var_dump("Respuesta Json Inicial",$res_json);
exit;
?>
