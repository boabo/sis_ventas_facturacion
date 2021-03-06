<?php
/***
 Nombre: Intermediario.php
 Proposito: Invocar al disparador saltandose los pasos de
 *          autentificacion
 * 		    este archivo se  invoca desde un cron tab en servidor linux
 *          solo deberia llamarse desde ahí, otras llamadas no seran autorizadas
 Autor:	Kplian (Ismael Valdivia)
 Fecha:	05/02/2020
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

$pxpRestClient = PxpRestClient::connect('127.0.0.1',substr($_SESSION["_FOLDER"], 1) .'pxp/lib/rest/')
                                                                                                        ->setCredentialsPxp($_GET['user'],$_GET['pw']);
$fecha = new DateTime();
$dato = 'traer_data';
$res = $pxpRestClient->doPost('ventas_facturacion/Comisionistas/TraerAcumulados',
				    array(	"comisionistas_traer"=>"traer_data"));        
$res_json = json_decode($res);

var_dump($res_json);
exit;
?>
