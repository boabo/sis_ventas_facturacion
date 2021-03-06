<?php
/**
*@package pXP
*@file gen-ACTPuntoVenta.php
*@author  (jrivera)
*@date 07-10-2015 21:02:00
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTPuntoVenta extends ACTbase{

	function listarPuntoVenta(){
		$this->objParam->defecto('ordenacion','id_punto_venta');

		if ($this->objParam->getParametro('id_sucursal') != '') {
            $this->objParam->addFiltro(" puve.id_sucursal = " .  $this->objParam->getParametro('id_sucursal'));
        }

		/*********************************Filtro para pv ATO Y CTO********************************/
		if ($this->objParam->getParametro('tipo_pv') != '') {
			$this->objParam->addFiltro("puve.tipo in (SELECT UNNEST(REGEXP_SPLIT_TO_ARRAY(''".$this->objParam->getParametro('tipo_pv')."'', '','')))");
		}
		/*****************************************************************************************/
		if ($this->objParam->getParametro('id_punto_venta') != '') {
			$this->objParam->addFiltro(" puve.id_punto_venta = " .  $this->objParam->getParametro('id_punto_venta'));
		}

        if($this->objParam->getParametro('tipo_factura') != '') {
            $this->objParam->addFiltro(" ''".$this->objParam->getParametro('tipo_factura')."'' =ANY (suc.tipo_interfaz)");
        }

		if($this->objParam->getParametro('tipo') != '') {
			$this->objParam->addFiltro(" puve.tipo =''".$this->objParam->getParametro('tipo')."''");
		}
		//var_dump("llega aqui datos",$this->objParam->getParametro('tipo_usuario'));
		if($this->objParam->getParametro('tipo_usuario') == 'vendedor') {
                $this->objParam->addFiltro(" (1 in (select id_rol from segu.tusuario_rol ur where ur.id_usuario = " . $_SESSION["ss_id_usuario"] . " and ur.estado_reg = ''activo'' ) or (
                                                " . $_SESSION["ss_id_usuario"] .  " in (select id_usuario from
                                                vef.tsucursal_usuario sucusu where puve.id_punto_venta = sucusu.id_punto_venta and
                                                    sucusu.tipo_usuario = ''vendedor''))) ");
        }

		if($this->objParam->getParametro('lugar') != ''){
			$this->objParam->addFiltro(" puve.id_sucursal in (select suc.id_sucursal from vef.tsucursal suc
						  inner join param.tlugar lug on lug.id_lugar = suc.id_lugar where lug.codigo=''".
					$this->objParam->getParametro('lugar')."'')");
		}

        // if($this->objParam->getParametro('tipo_usuario') == 'administrador') {
        //     $this->objParam->addFiltro(" (1 in (select id_rol from segu.tusuario_rol ur where ur.id_usuario = " . $_SESSION["ss_id_usuario"] . " ) or (
        //                                         " . $_SESSION["ss_id_usuario"] .  " in (select id_usuario from
        //                                         vef.tsucursal_usuario sucusu where puve.id_punto_venta = sucusu.id_punto_venta and
        //                                             sucusu.tipo_usuario = ''administrador''))) ");
        // }

        if($this->objParam->getParametro('tipo_usuario') == 'cajero') {
            $this->objParam->addFiltro(" (1 in (select id_rol from segu.tusuario_rol ur where ur.id_usuario = " . $_SESSION["ss_id_usuario"] . " and ur.estado_reg = ''activo'') or (
                                                " . $_SESSION["ss_id_usuario"] .  " in (select id_usuario from
                                                vef.tsucursal_usuario sucusu where puve.id_punto_venta = sucusu.id_punto_venta and
                                                    sucusu.tipo_usuario in (''cajero'', ''cajero_auxiliar'')))) ");
        }

				if($this->objParam->getParametro('tipo_usuario') == 'finanzas') {
					$this->objParam->addFiltro(" (1 in (select id_rol from segu.tusuario_rol ur where ur.id_usuario = " . $_SESSION["ss_id_usuario"] . " and ur.estado_reg = ''activo'') or (
		                                                " . $_SESSION["ss_id_usuario"] .  " in (select id_usuario from
		                                                vef.tsucursal_usuario sucusu where puve.id_punto_venta = sucusu.id_punto_venta and
		                                                    sucusu.tipo_usuario in (''finanzas'')))) ");
				}

		//20-01-2021 (may) para tipo usuario cajero y cajero_auxiliar
		if($this->objParam->getParametro('tipo_usuario') == 'cajero_auxiliar') {
			$this->objParam->addFiltro(" (1 in (select id_rol from segu.tusuario_rol ur where ur.id_usuario = " . $_SESSION["ss_id_usuario"] . " and ur.estado_reg = ''activo'') or (
                                                " . $_SESSION["ss_id_usuario"] .  " in (select id_usuario from
                                                vef.tsucursal_usuario sucusu where puve.id_punto_venta = sucusu.id_punto_venta and
                                                    sucusu.tipo_usuario in (''cajero'', ''cajero_auxiliar'')))) ");
		}
		//

        if($this->objParam->getParametro('tipo_usuario') == 'todos') {
            $this->objParam->addFiltro(" (1 in (select id_rol from segu.tusuario_rol ur where ur.id_usuario = " . $_SESSION["ss_id_usuario"] . " ) or (
                                                " . $_SESSION["ss_id_usuario"] .  " in (select id_usuario from
                                                vef.tsucursal_usuario sucusu where puve.id_punto_venta = sucusu.id_punto_venta
                                                    ))) ");

        }if($this->objParam->getParametro('tipo_usuario') == 'adminEntrega') {
            $this->objParam->addFiltro("(1 in (select id_rol from segu.tusuario_rol ur where ur.id_usuario = " . $_SESSION["ss_id_usuario"] .  " ) or (
                                                    " . $_SESSION["ss_id_usuario"] .  " in (select d.id_usuario
                                                                                            from vef.tsucursal s
                                                                                            inner join vef.tpunto_venta p on p.id_sucursal = s.id_sucursal
                                                                                            inner join param.tdepto_usuario d on d.id_depto = s.id_depto
                                                                                            where puve.id_punto_venta = p.id_punto_venta
                                                                                            )))");
        }

		$this->objParam->defecto('dir_ordenacion','asc');
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODPuntoVenta','listarPuntoVenta');
		} else{
			$this->objFunc=$this->create('MODPuntoVenta');

			$this->res=$this->objFunc->listarPuntoVenta($this->objParam);
		}

		/*Aqui para poner todos los puntos de ventas*/
		if($this->objParam->getParametro('_adicionar')!=''){

			$respuesta = $this->res->getDatos();


			array_unshift ( $respuesta, array(  'id_punto_venta'=>'0',
				  'nombre'=>'Todos',
					'codigo'=>'Todos') );
			//var_dump($respuesta);
			$this->res->setDatos($respuesta);
		}
		/********************************************/




		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function insertarPuntoVenta(){
		$this->objFunc=$this->create('MODPuntoVenta');
		if($this->objParam->insertar('id_punto_venta')){
			$this->res=$this->objFunc->insertarPuntoVenta($this->objParam);
		} else{
			$this->res=$this->objFunc->modificarPuntoVenta($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function eliminarPuntoVenta(){
			$this->objFunc=$this->create('MODPuntoVenta');
		$this->res=$this->objFunc->eliminarPuntoVenta($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function obtenerOficinaID(){
		$this->objFunc=$this->create('MODPuntoVenta');
		$this->res=$this->objFunc->obtenerOficinaID($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	//28-01-2021 (may)
	function comboPuntoVentaUsuario(){
		$this->objParam->defecto('ordenacion','id_punto_venta');

		if ($this->objParam->getParametro('id_sucursal') != '') {
			$this->objParam->addFiltro(" puve.id_sucursal = " .  $this->objParam->getParametro('id_sucursal'));
		}

		/*********************************Filtro para pv ATO Y CTO********************************/
		if ($this->objParam->getParametro('tipo_pv') != '') {
			$this->objParam->addFiltro("puve.tipo in (SELECT UNNEST(REGEXP_SPLIT_TO_ARRAY(''".$this->objParam->getParametro('tipo_pv')."'', '','')))");
		}
		/*****************************************************************************************/
		if ($this->objParam->getParametro('id_punto_venta') != '') {
			$this->objParam->addFiltro(" puve.id_punto_venta = " .  $this->objParam->getParametro('id_punto_venta'));
		}

		if($this->objParam->getParametro('tipo_factura') != '') {
			$this->objParam->addFiltro(" ''".$this->objParam->getParametro('tipo_factura')."'' =ANY (suc.tipo_interfaz)");
		}

		if($this->objParam->getParametro('tipo') != '') {
			$this->objParam->addFiltro(" puve.tipo =''".$this->objParam->getParametro('tipo')."''");
		}
		//var_dump("llega aqui datos",$this->objParam->getParametro('tipo_usuario'));
		if($this->objParam->getParametro('tipo_usuario') == 'vendedor') {
			$this->objParam->addFiltro(" (1 in (select id_rol from segu.tusuario_rol ur where ur.id_usuario = " . $_SESSION["ss_id_usuario"] . " and ur.estado_reg = ''activo'' ) or (
                                                " . $_SESSION["ss_id_usuario"] .  " in (select id_usuario from
                                                vef.tsucursal_usuario sucusu where puve.id_punto_venta = sucusu.id_punto_venta and
                                                    sucusu.tipo_usuario = ''vendedor''))) ");
		}

		if($this->objParam->getParametro('lugar') != ''){
			$this->objParam->addFiltro(" puve.id_sucursal in (select suc.id_sucursal from vef.tsucursal suc
						  inner join param.tlugar lug on lug.id_lugar = suc.id_lugar where lug.codigo=''".
				$this->objParam->getParametro('lugar')."'')");
		}

		// if($this->objParam->getParametro('tipo_usuario') == 'administrador') {
		//     $this->objParam->addFiltro(" (1 in (select id_rol from segu.tusuario_rol ur where ur.id_usuario = " . $_SESSION["ss_id_usuario"] . " ) or (
		//                                         " . $_SESSION["ss_id_usuario"] .  " in (select id_usuario from
		//                                         vef.tsucursal_usuario sucusu where puve.id_punto_venta = sucusu.id_punto_venta and
		//                                             sucusu.tipo_usuario = ''administrador''))) ");
		// }

		if($this->objParam->getParametro('tipo_usuario') == 'cajero') {
			$this->objParam->addFiltro(" (1 in (select id_rol from segu.tusuario_rol ur where ur.id_usuario = " . $_SESSION["ss_id_usuario"] . " and ur.estado_reg = ''activo'') or (
                                                " . $_SESSION["ss_id_usuario"] .  " in (select id_usuario from
                                                vef.tsucursal_usuario sucusu where puve.id_punto_venta = sucusu.id_punto_venta and
                                                    sucusu.tipo_usuario = ''cajero''))) ");
		}

		//20-01-2021 (may) para tipo usuario cajero y cajero_auxiliar
		if($this->objParam->getParametro('tipo_usuario') == 'cajero_auxiliar') {
			$this->objParam->addFiltro(" (1 in (select id_rol from segu.tusuario_rol ur where ur.id_usuario = " . $_SESSION["ss_id_usuario"] . " and ur.estado_reg = ''activo'') or (
                                                " . $_SESSION["ss_id_usuario"] .  " in (select id_usuario from
                                                vef.tsucursal_usuario sucusu where puve.id_punto_venta = sucusu.id_punto_venta and
                                                    sucusu.tipo_usuario in (''cajero'', ''cajero_auxiliar'')))) ");
		}
		//

		if($this->objParam->getParametro('tipo_usuario') == 'todos') {
			$this->objParam->addFiltro(" (1 in (select id_rol from segu.tusuario_rol ur where ur.id_usuario = " . $_SESSION["ss_id_usuario"] . " ) or (
                                                " . $_SESSION["ss_id_usuario"] .  " in (select id_usuario from
                                                vef.tsucursal_usuario sucusu where puve.id_punto_venta = sucusu.id_punto_venta
                                                    ))) ");

		}if($this->objParam->getParametro('tipo_usuario') == 'adminEntrega') {
			$this->objParam->addFiltro("(1 in (select id_rol from segu.tusuario_rol ur where ur.id_usuario = " . $_SESSION["ss_id_usuario"] .  " ) or (
                                                    " . $_SESSION["ss_id_usuario"] .  " in (select d.id_usuario
                                                                                            from vef.tsucursal s
                                                                                            inner join vef.tpunto_venta p on p.id_sucursal = s.id_sucursal
                                                                                            inner join param.tdepto_usuario d on d.id_depto = s.id_depto
                                                                                            where puve.id_punto_venta = p.id_punto_venta
                                                                                            )))");
		}

		$this->objParam->defecto('dir_ordenacion','asc');
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODPuntoVenta','comboPuntoVentaUsuario');
		} else{
			$this->objFunc=$this->create('MODPuntoVenta');

			$this->res=$this->objFunc->comboPuntoVentaUsuario($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

}

?>
