<?php
//incluimos la libreria
//echo dirname(__FILE__);
//include_once(dirname(__FILE__).'/../PHPExcel/Classes/PHPExcel.php');
class RResumenVentasBoaXLS
{
	private $docexcel;
	private $objWriter;
	private $nombre_archivo;
	private $hoja;
	private $columnas=array();
	private $fila;
	private $equivalencias=array();

	private $indice, $m_fila, $titulo;
	private $objParam;
	public  $url_archivo;
	public $styleTitulos0;
	public $styleTitulos1;
	public $styleTitulos2;
	public $styleDetalle;
	public $styleTotal;


	function __construct(CTParametro $objParam){
		$this->objParam = $objParam;
		$this->url_archivo = "../../../reportes_generados/".$this->objParam->getParametro('nombre_archivo');
		//ini_set('memory_limit','512M');
		set_time_limit(400);
		$cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
		$cacheSettings = array('memoryCacheSize'  => '10MB');
		PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

		$this->docexcel = new PHPExcel();
		$this->docexcel->getProperties()->setCreator("PXP")
				->setLastModifiedBy("PXP")
				->setTitle($this->objParam->getParametro('titulo_archivo'))
				->setSubject($this->objParam->getParametro('titulo_archivo'))
				->setDescription('Reporte "'.$this->objParam->getParametro('titulo_archivo').'", generado por el framework PXP')
				->setKeywords("office 2007 openxml php")
				->setCategory("Report File");
		$this->docexcel->setActiveSheetIndex(0);
		$this->docexcel->getActiveSheet()->setTitle('Resumen');
		$this->equivalencias=array(0=>'A',1=>'B',2=>'C',3=>'D',4=>'E',5=>'F',6=>'G',7=>'H',8=>'I',
				9=>'J',10=>'K',11=>'L',12=>'M',13=>'N',14=>'O',15=>'P',16=>'Q',17=>'R',
				18=>'S',19=>'T',20=>'U',21=>'V',22=>'W',23=>'X',24=>'Y',25=>'Z',
				26=>'AA',27=>'AB',28=>'AC',29=>'AD',30=>'AE',31=>'AF',32=>'AG',33=>'AH',
				34=>'AI',35=>'AJ',36=>'AK',37=>'AL',38=>'AM',39=>'AN',40=>'AO',41=>'AP',
				42=>'AQ',43=>'AR',44=>'AS',45=>'AT',46=>'AU',47=>'AV',48=>'AW',49=>'AX',
				50=>'AY',51=>'AZ',
				52=>'BA',53=>'BB',54=>'BC',55=>'BD',56=>'BE',57=>'BF',58=>'BG',59=>'BH',
				60=>'BI',61=>'BJ',62=>'BK',63=>'BL',64=>'BM',65=>'BN',66=>'BO',67=>'BP',
				68=>'BQ',69=>'BR',70=>'BS',71=>'BT',72=>'BU',73=>'BV',74=>'BW',75=>'BX',
				76=>'BY',77=>'BZ');
		$this->styleTitulos0 = array(
				'font'  => array(
						'bold'  => true,
						'size'  => 12,
						'name'  => 'Calibri'
				),
				'alignment' => array(
						'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
						'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				)
		);
		$this->styleTitulos1 = 	$this->styleTitulos0;
		$this->styleTitulos2 = 	$this->styleTitulos0;
		$this->styleDetalle = 	$this->styleTitulos0;

		$this->styleTitulos1['font']['size'] = 20;
		$this->styleTitulos1['alignment']['horizontal'] = PHPExcel_Style_Alignment::HORIZONTAL_CENTER;
		$this->styleTitulos2['alignment']['horizontal'] = PHPExcel_Style_Alignment::HORIZONTAL_CENTER;


		$this->styleTitulos2['fill'] = array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array(
						'rgb' => 'c6d9f1'
				)
		);
		$this->styleTitulos2['borders'] = array(
				'allborders' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
				)
		);

		$this->styleDetalle['borders'] = array(
				'allborders' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
				)
		);

		$this->styleDetalle['font']['bold'] = false;

		$this->styleDetalle['font']['size'] = 11;
		$this->styleDetalleRojo = $this->styleDetalle;
		$this->styleDetalleRojo['font']['color'] = array('rgb' => 'CE0000');
		$this->styleTotal = 	$this->styleDetalle;
		$this->styleTotal['font']['bold'] = true;
	}


	function imprimeCabecera ($sheet, $resumen = 'no',$objFecha = '',$mone_base,$datos) {
		$styleTitulos = array(
				'font'  => array(
						'bold'  => true,
						'size'  => 12,
						'name'  => 'Arial'
				),
				'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array(
								'rgb' => 'FFFFFF'
						)
				),
				'alignment' => array(
						'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
						'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				),
		);

		$styleTitulosTabla = array(
				'font'  => array(
						'bold'  => true,
						'size'  => 11,
						'name'  => 'Arial',
				),
				'alignment' => array(
						'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
						'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				),
				'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array(
								'rgb' => '008CE1'
						)
				),
				'borders' => array(
						'allborders' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN
						)
				)
		);



		$gdImage = imagecreatefromjpeg('../../../lib/imagenes/Logo_libro_mayor.jpg');
		// Add a drawing to the worksheetecho date('H:i:s') . " Add a drawing to the worksheet\n";
		$objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
		$objDrawing->setName('Sample image');
		$objDrawing->setDescription('Sample image');
		$objDrawing->setImageResource($gdImage);
		$objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
		$objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
		$objDrawing->setHeight(80);
		$objDrawing->setCoordinates('A1');
		$objDrawing->setWorksheet($this->docexcel->getActiveSheet());

		$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,1,'SUCURSAL: '.$mone_base[0]['nombre_pv']);
		$this->docexcel->getActiveSheet()->getStyle('A1:AA1')->applyFromArray($styleTitulos);
		$this->docexcel->getActiveSheet()->mergeCells('A1:L1');

		$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,2,'DESDE: '.$this->objParam->getParametro('fecha_desde').'  '.'HASTA: '.$this->objParam->getParametro('fecha_hasta'));
		$this->docexcel->getActiveSheet()->getStyle('A2:AA2')->applyFromArray($styleTitulos);
		$this->docexcel->getActiveSheet()->mergeCells('A2:L2');

		$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,3,'RESUMEN DE VENTAS');
		$this->docexcel->getActiveSheet()->getStyle('A3:AA3')->applyFromArray($styleTitulos);
		$this->docexcel->getActiveSheet()->mergeCells('A3:L3');

		$this->docexcel->getActiveSheet()->getStyle('A4:AA4')->applyFromArray($styleTitulos);
		$this->docexcel->getActiveSheet()->mergeCells('A4:L4');


		$this->docexcel->getActiveSheet()->setCellValue('A5','NRO.');
		$this->docexcel->getActiveSheet()->setCellValue('B5','FECHA');
		$this->docexcel->getActiveSheet()->setCellValue('C5','NOMBRE PAX');
		$this->docexcel->getActiveSheet()->setCellValue('D5','No TKT');
		$this->docexcel->getActiveSheet()->setCellValue('E5','PNR');
		$this->docexcel->getActiveSheet()->setCellValue('F5','No FACTURA');
		$this->docexcel->getActiveSheet()->setCellValue('G5','No RECIBO');
		$this->docexcel->getActiveSheet()->setCellValue('H5','ESTADO EMISIÓN');
		$this->docexcel->getActiveSheet()->setCellValue('I5','OBSERVACIONES');
		$this->docexcel->getActiveSheet()->setCellValue('J5','RUTA');
		$this->docexcel->getActiveSheet()->setCellValue('K5','MONEDA');
		$this->docexcel->getActiveSheet()->setCellValue('L5','COMISION');
		$this->docexcel->getActiveSheet()->setCellValue('M5','TOTAL');
		$this->docexcel->getActiveSheet()->setCellValue('N5','CASH USD');
		$this->docexcel->getActiveSheet()->setCellValue('O5','CC USD');
		$this->docexcel->getActiveSheet()->setCellValue('P5','CTE USD');
		$this->docexcel->getActiveSheet()->setCellValue('Q5','MCO USD');
		$this->docexcel->getActiveSheet()->setCellValue('R5','DEPOSITO USD');
		$this->docexcel->getActiveSheet()->setCellValue('S5','OTRO USD');
		$this->docexcel->getActiveSheet()->setCellValue('T5','CASH '.$mone_base[0]['moneda']);
		$this->docexcel->getActiveSheet()->setCellValue('U5','CC '.$mone_base[0]['moneda']);
		$this->docexcel->getActiveSheet()->setCellValue('V5','CTE '.$mone_base[0]['moneda']);
		$this->docexcel->getActiveSheet()->setCellValue('W5','MCO '.$mone_base[0]['moneda']);
		$this->docexcel->getActiveSheet()->setCellValue('X5','DEPOSITO '.$mone_base[0]['moneda']);
		$this->docexcel->getActiveSheet()->setCellValue('Y5','OTRO '.$mone_base[0]['moneda']);
		$this->docexcel->getActiveSheet()->setCellValue('Z5','FORMA PAGO');
		$this->docexcel->getActiveSheet()->setCellValue('AA5','DESC AUX');

		$this->docexcel->getActiveSheet()->getColumnDimension('A')->setWidth(7);
		$this->docexcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
		$this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
		$this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
		$this->docexcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
		$this->docexcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
		$this->docexcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
		$this->docexcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
		$this->docexcel->getActiveSheet()->getColumnDimension('I')->setWidth(40);
		$this->docexcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
		$this->docexcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
		$this->docexcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
		$this->docexcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
		$this->docexcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
		$this->docexcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
		$this->docexcel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
		$this->docexcel->getActiveSheet()->getColumnDimension('Q')->setWidth(20);
		$this->docexcel->getActiveSheet()->getColumnDimension('R')->setWidth(20);
		$this->docexcel->getActiveSheet()->getColumnDimension('S')->setWidth(20);
		$this->docexcel->getActiveSheet()->getColumnDimension('T')->setWidth(20);
		$this->docexcel->getActiveSheet()->getColumnDimension('U')->setWidth(20);
		$this->docexcel->getActiveSheet()->getColumnDimension('V')->setWidth(20);
		$this->docexcel->getActiveSheet()->getColumnDimension('W')->setWidth(30);
		$this->docexcel->getActiveSheet()->getColumnDimension('X')->setWidth(30);
		$this->docexcel->getActiveSheet()->getColumnDimension('Y')->setWidth(30);
		$this->docexcel->getActiveSheet()->getColumnDimension('Z')->setWidth(30);
		$this->docexcel->getActiveSheet()->getColumnDimension('AA')->setWidth(40);


		$this->docexcel->getActiveSheet()->getStyle('A5:AA5')->applyFromArray($styleTitulosTabla);
		$this->docexcel->getActiveSheet()->getStyle('A5:AA5')->getAlignment()->setWrapText(true);

		$this->docexcel->getActiveSheet()->freezePaneByColumnAndRow(0,6);

	}


	function imprimeDatos(){
		$fondoContenido = array(
				'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array(
								'rgb' => 'FFFFFF'
						)
				)
		);

		$estado_valida = array(
			'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
					'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
			),
		);

		$estado_anulada = array(
				'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array(
								'rgb' => 'FF8F77'
						)
				),
		);


		$styleTotal = array(
				'font'  => array(
						'bold'  => true,
						'size'  => 11,
						'name'  => 'Arial',
				),
				'alignment' => array(
						'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
						'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				),
				'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array(
								'rgb' => 'FD8F1B'
						)
				),
				'borders' => array(
						'allborders' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN
						)
				)
		);

		$datos = $this->objParam->getParametro('datos');
		$config = $this->objParam->getParametro('conceptos');
		$base = $this->objParam->getParametro('base');


		$conceptos = array();
		for ($i = 0;$i < count($config);$i++) {
			$conceptos[$config [$i]['nombre']] = $this->equivalencias[$i + 7];
		}

		//Imprime cabecera de resumen
		$this->imprimeCabecera(0,'si','',$base,$datos);

		$fila = 5;
		$fila_general = 5;
		$sheetId = 0;
		$fecha = '';
		$boleto = '';
		$totales = array();
		$correlativo_hoja = 1;
		$correlativo_general = 1;


		foreach ($datos as $key => $value) {

			//si es distinta creamos una nueva hoja
			$objFecha = DateTime::createFromFormat('Y-m-d', $value['fecha']);


			$fila++;
			$fila_general++;
			if ($value['tipo'] == 'boleto') {
				$boleto = $value['boleto'];
				$pnr = $value['pnr'];
				$recibo = '';
				$observacion = '';
			} else {
				$observacion = $value['boleto'];
				$pnr = $value['pnr'];
				$boleto = '';
				$recibo = $value['correlativo'];
			}

			//var_dump("data",$observacion);
			$this->docexcel->getActiveSheet()->setCellValue('A'.$fila,$correlativo_hoja);
			$this->docexcel->getActiveSheet()->setCellValue('B'.$fila,$objFecha->format('d-M'));
			$this->docexcel->getActiveSheet()->setCellValue('C'.$fila,$value['pasajero']);
			$this->docexcel->getActiveSheet()->setCellValue('D'.$fila,$boleto);
			$this->docexcel->getActiveSheet()->setCellValue('E'.$fila,$pnr);


			if($value['tipo_factura']=='recibo'){
				$this->docexcel->getActiveSheet()->setCellValue('G'.$fila,$recibo);
			} else {
				$this->docexcel->getActiveSheet()->setCellValue('F'.$fila,$recibo);
			}

			$this->docexcel->getActiveSheet()->setCellValue('H'.$fila,$value['estado_emision']);
			$this->docexcel->getActiveSheet()->setCellValue('I'.$fila,$observacion);
			$this->docexcel->getActiveSheet()->setCellValue('J'.$fila,$value['ruta']);
			$this->docexcel->getActiveSheet()->setCellValue('K'.$fila,$value['moneda_emision']);
			$this->docexcel->getActiveSheet()->setCellValue('L'.$fila,$value['comision']);
			$this->docexcel->getActiveSheet()->setCellValue('M'.$fila,$value['neto']);

			$this->docexcel->getActiveSheet()->setCellValue('N'.$fila,$value['monto_cash_usd']);
			$this->docexcel->getActiveSheet()->setCellValue('O'.$fila,$value['monto_cc_usd']);
			$this->docexcel->getActiveSheet()->setCellValue('P'.$fila,$value['monto_cte_usd']);
			$this->docexcel->getActiveSheet()->setCellValue('Q'.$fila,$value['monto_mco_usd']);

			$this->docexcel->getActiveSheet()->setCellValue('R'.$fila,$value['monto_deposito_usd']);

			$this->docexcel->getActiveSheet()->setCellValue('S'.$fila,$value['monto_otro_usd']);
			$this->docexcel->getActiveSheet()->setCellValue('T'.$fila,$value['monto_cash_mb']);
			$this->docexcel->getActiveSheet()->setCellValue('U'.$fila,$value['monto_cc_mb']);
			$this->docexcel->getActiveSheet()->setCellValue('V'.$fila,$value['monto_cte_mb']);
			$this->docexcel->getActiveSheet()->setCellValue('W'.$fila,$value['monto_mco_mb']);

			$this->docexcel->getActiveSheet()->setCellValue('X'.$fila,$value['monto_deposito_mb']);

			$this->docexcel->getActiveSheet()->setCellValue('Y'.$fila,$value['monto_otro_mb']);
			$this->docexcel->getActiveSheet()->setCellValue('Z'.$fila,$value['forma_pago']);
			$this->docexcel->getActiveSheet()->setCellValue('AA'.$fila,$value['codigo_auxiliar']);

			$this->docexcel->getActiveSheet()->getStyle("A$fila_general:AA$fila_general")->applyFromArray($fondoContenido);
			$this->docexcel->getActiveSheet()->getStyle("H$fila_general:H$fila_general")->applyFromArray($estado_valida);

			if ($value['estado_emision'] == 'ANULADO' || $value['estado_emision'] == 'ANULADA') {
				$this->docexcel->getActiveSheet()->getStyle("A$fila_general:AA$fila_general")->applyFromArray($estado_anulada);
			}


			$correlativo_hoja++;
			// $correlativo_general++;

		}


		//TOTALES RESUMEN
		$total = $fila_general + 1;
		$this->docexcel->setActiveSheetIndex(0)->setCellValue('A'.($fila_general + 1),'TOTALES: ');
		$this->docexcel->getActiveSheet()->getStyle("A$total:AA$total")->applyFromArray($styleTotal);
		$this->docexcel->getActiveSheet()->mergeCells("A$total:K$total");

		for ($i=6; $i <= $fila_general; $i++) {
			$inicio = 6;
			$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $total, "=SUM((L$inicio:L$i))");
			$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $total, "=SUM((M$inicio:M$i))");
			$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(12, $total, "=SUM((N$inicio:N$i))");
			$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(13, $total, "=SUM((O$inicio:O$i))");
			$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(14, $total, "=SUM((P$inicio:P$i))");
			$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(15, $total, "=SUM((Q$inicio:Q$i))");
			$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(16, $total, "=SUM((R$inicio:R$i))");
			$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(17, $total, "=SUM((S$inicio:S$i))");
			$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(18, $total, "=SUM((T$inicio:T$i))");
			$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(19, $total, "=SUM((U$inicio:U$i))");
			$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(20, $total, "=SUM((V$inicio:V$i))");
			$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(21, $total, "=SUM((W$inicio:W$i))");
			$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(22, $total, "=SUM((X$inicio:X$i))");
			$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(23, $total, "=SUM((Y$inicio:Y$i))");
			$this->docexcel->getActiveSheet()->getStyle("L$i:Y$i")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
			// $this->docexcel->getActiveSheet()->getStyle("L$i")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
			// $this->docexcel->getActiveSheet()->getStyle("M$i")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
			// $this->docexcel->getActiveSheet()->getStyle("N$i")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
			// $this->docexcel->getActiveSheet()->getStyle("O$i")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
			// $this->docexcel->getActiveSheet()->getStyle("P$i")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
			// $this->docexcel->getActiveSheet()->getStyle("Q$i")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
			// $this->docexcel->getActiveSheet()->getStyle("R$i")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
			// $this->docexcel->getActiveSheet()->getStyle("S$i")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
			// $this->docexcel->getActiveSheet()->getStyle("T$i")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
			// $this->docexcel->getActiveSheet()->getStyle("U$i")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
			// $this->docexcel->getActiveSheet()->getStyle("V$i")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
			// $this->docexcel->getActiveSheet()->getStyle("W$i")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
			// $this->docexcel->getActiveSheet()->getStyle("X$i")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

			$this->docexcel->getActiveSheet()->getStyle("K$total:Y$total")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
			// $this->docexcel->getActiveSheet()->getStyle("K$total")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
			// $this->docexcel->getActiveSheet()->getStyle("L$total")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
			// $this->docexcel->getActiveSheet()->getStyle("M$total")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
			// $this->docexcel->getActiveSheet()->getStyle("N$total")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
			// $this->docexcel->getActiveSheet()->getStyle("O$total")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
			// $this->docexcel->getActiveSheet()->getStyle("P$total")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
			// $this->docexcel->getActiveSheet()->getStyle("Q$total")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
			// $this->docexcel->getActiveSheet()->getStyle("R$total")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
			// $this->docexcel->getActiveSheet()->getStyle("S$total")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
			// $this->docexcel->getActiveSheet()->getStyle("T$total")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
			// $this->docexcel->getActiveSheet()->getStyle("U$total")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
			// $this->docexcel->getActiveSheet()->getStyle("V$total")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
			// $this->docexcel->getActiveSheet()->getStyle("W$total")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

		}



	}



	function generarReporte(){
		//echo $this->nombre_archivo; exit;
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$this->docexcel->setActiveSheetIndex(0);
		$this->objWriter = PHPExcel_IOFactory::createWriter($this->docexcel, 'Excel5');
		$this->objWriter->save($this->url_archivo);

	}


}

?>
