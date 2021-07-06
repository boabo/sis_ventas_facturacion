<?php
/**
*@package pXP
*@file gen-ACTInsertarNuevaBD.php
*@author  (Ismael Valdivia)
*@date 11-05-2021 16:30:56
*@description Action para la creacion de la nueva base de datos por gestion
*/
include_once(dirname(__FILE__).'/../../lib/lib_general/funciones.inc.php');
require_once(dirname(__FILE__).'/../../pxp/pxpReport/DataSource.php');
include_once(dirname(__FILE__).'/../../lib/PHPMailer/class.phpmailer.php');
include_once(dirname(__FILE__).'/../../lib/PHPMailer/class.smtp.php');
include_once(dirname(__FILE__).'/../../lib/lib_general/cls_correo_externo.php');

class ACTInsertarNuevaBD extends ACTbase{
    function insertarDb(){

        $this->objFunc=$this->create('MODInsertarNuevaBD');
        $this->res=$this->objFunc->insertarDb($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }


    function enviarNotificacion(){

      $this->objFunSeguridad=$this->create('MODInsertarNuevaBD');
        $this->res=$this->objFunSeguridad->obtenerDatosVariablesGlobales($this->objParam);

      $array = $this->res->getDatos();

      //echo "{\"ROOT\":{\"error\":true,\"detalle\":{\"mensaje\":\" ".$array[0]['replicacion_data']."\"}}}";

      //$this->objParam->getParametro('Salida');
 		 ////////////////////////////////////////
 		 //arma el texto del correo electronico
 		 ///////////////////////////////////////
     $data_mail = '';

     if ($this->objParam->getParametro('Salida') != 'Correcto') {
       $data_mail.= 'Ocurrio un incidente con la creacion de la base de datos <br>'.
                    'El mensaje de respuesta generado es el siguiente: <br>'.
                    '<b>'.$this->objParam->getParametro('Salida').'</b><br>'.
                     '-------------------------------------<br>'.
                     'Favor Verificar la nueva base de datos (tablas,funciones,trigger,server,etc.)<br>'.
                   '***Sistema ERP BOA***';
     } else {
       $data_mail.= $this->objParam->getParametro('Mensaje');
      }




 		 ///////////////////////////////////////////////////
 		 //manda el correo electronicos al solicitante
 		 ///////////////////////////////////////////////////

 		    $correo=new CorreoExterno();
 		    $correo->addDestinatario('ismael.valdivia@boa.bo'); //  este mail esta destinado al area de tesoreria
 	      //$correo->addDestinatario('grover.velasquez@boa.bo');
 					// breydi.vasquez con copia para funcionario que dispara la notificacion
 					//if($array[0]['func_cc'] !='' && $array[0]['func_cc']!=null){
 							//$correo->addCC('ismael.valdivia@boa.bo');
 					//}
 		    //asunto
        if ($this->objParam->getParametro('Salida') != 'Correcto') {
           $correo->setAsunto('Error de Creación Nueva DB');
           //cuerpo mensaje
           $correo->setMensaje($data_mail);
           $correo->setTitulo('Error de Creación Nueva DB');
        } else {
           $correo->setAsunto('Creación Nueva DB '.$array[0]['replicacion_data'].'.');
           //cuerpo mensaje
           $correo->setMensaje($data_mail);
           $correo->setTitulo('Creación Nueva DB '.$array[0]['replicacion_data'].'.');
        }


 			$correo->setDefaultPlantilla();
             $resp=$correo->enviarCorreo();
             if($resp=='OK'){
                 $mensajeExito = new Mensaje();
                 $mensajeExito->setMensaje('EXITO','Solicitud.php','Correo enviado',
                 'Se mando el correo con exito: OK','control' );
                 $this->res = $mensajeExito;
                 $this->res->imprimirRespuesta($this->res->generarJson());

            }
             else{
               //echo $resp;
               echo "{\"ROOT\":{\"error\":true,\"detalle\":{\"mensaje\":\" Error al enviar correo\"}}}";

            }

 		   exit;






    }

}

?>
