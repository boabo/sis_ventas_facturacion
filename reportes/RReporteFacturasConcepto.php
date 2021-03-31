<?php
class RReporteFacturasConcepto
{
    private $docexcel;
    private $objWriter;
    private $equivalencias=array();
    private $aux=0;
    private $aux2=0;
    private $objParam;
    public  $url_archivo;
    public  $fila = 0;
    public  $filaAux = 0;
    public  $fnum =array();
    public  $fnumA =0;
    public  $garantia =0;
    public  $array =array();
    public  $array2 =array();
    public  $sinboleta =array();
    public  $sb2 =array();
    public  $saldoanterior =array();
    public  $boletaGarantia =array();
    public  $depositosTotal =array();
    public  $comision =array();
    public  $boletos =array();


    function __construct(CTParametro $objParam){
        $this->objParam = $objParam;
        $this->url_archivo = "../../../reportes_generados/".$this->objParam->getParametro('nombre_archivo');
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

        $this->equivalencias=array( 0=>'A',1=>'B',2=>'C',3=>'D',4=>'E',5=>'F',6=>'G',7=>'H',8=>'I',
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

    }
  function datosHeader ($contenido/*$detalle*/) {
        $this->datos_contenido = $contenido;
        //$this->datos_detalle = $detalle;
    }
    function imprimeCabecera() {

        if ($this->objParam->getParametro('tipo_documento') == 'factura') {
          $this->docexcel->getActiveSheet()->setTitle('FACTURAS POR CONCEPTO');
          $this->docexcel->setActiveSheetIndex(0);
        } elseif ($this->objParam->getParametro('tipo_documento') == 'recibo') {
          $this->docexcel->getActiveSheet()->setTitle('RECIBOS POR CONCEPTO');
          $this->docexcel->setActiveSheetIndex(0);
        }


        $styleTitulos1 = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 11,
                'name'  => 'Calibri',
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => 'FFFFFF'
                )
            )
        );

        $styleTitulosPrincipal = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 14,
                'name'  => 'Calibri',
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => 'FFFFFF'
                )
            )
        );

        $styleSubtitulos = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 11,
                'name'  => 'Calibri',
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => '3F94B4'
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
        $objDrawing->setHeight(95);
        $objDrawing->setCoordinates('A1');
        $objDrawing->setWorksheet($this->docexcel->getActiveSheet());


          $fechaGeneracion =  date("d/m/Y");
          $horaGeneracion=  date("H:i:s");

          $this->docexcel->getActiveSheet()->mergeCells('B3:E3');

          if ($this->objParam->getParametro('tipo_documento') == 'factura') {
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1,3,'REPORTE DE FACTURAS (COMPUTARIZADAS/MANUALES) POR CONCEPTO');
          } elseif ($this->objParam->getParametro('tipo_documento') == 'recibo') {
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1,3,'REPORTE DE RECIBOS (COMPUTARIZADOS/MANUALES) POR CONCEPTO');
          }
          $this->docexcel->getActiveSheet()->mergeCells('C4:D4');


          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,4,'Del: '.$this->objParam->getParametro('desde').' Al: '.$this->objParam->getParametro('hasta'));

          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5,3,'Fecha: '.$fechaGeneracion);
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5,4,'Hora: '.$horaGeneracion);

          $this->docexcel->getActiveSheet()->getStyle('A1:H2')->applyFromArray($styleTitulos1);
          $this->docexcel->getActiveSheet()->getStyle('A3:H4')->applyFromArray($styleTitulosPrincipal);
          $this->docexcel->getActiveSheet()->getStyle('A5:H6')->applyFromArray($styleTitulos1);


          $this->docexcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
          $this->docexcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
          $this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
          $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
          $this->docexcel->getActiveSheet()->getColumnDimension('E')->setWidth(35);
          $this->docexcel->getActiveSheet()->getColumnDimension('F')->setWidth(40);
          $this->docexcel->getActiveSheet()->getColumnDimension('G')->setWidth(35);
          $this->docexcel->getActiveSheet()->getColumnDimension('H')->setWidth(35);
          $this->docexcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
          $this->docexcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
          $this->docexcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
          $this->docexcel->getActiveSheet()->getColumnDimension('L')->setWidth(35);

          if ($this->objParam->getParametro('tipo_documento') == 'factura') {
            $this->docexcel->getActiveSheet()->setCellValue('A6','Fecha');
            $this->docexcel->getActiveSheet()->setCellValue('B6','Nro. Autorización');
            $this->docexcel->getActiveSheet()->setCellValue('C6','Nro. Factura');
            $this->docexcel->getActiveSheet()->setCellValue('D6','Importe M/L');
            $this->docexcel->getActiveSheet()->setCellValue('E6','Concepto');
            $this->docexcel->getActiveSheet()->setCellValue('F6','Estado Factura');
            $this->docexcel->getActiveSheet()->setCellValue('G6','Tipo Factura');
            $this->docexcel->getActiveSheet()->setCellValue('H6','Cajero');
          } elseif ($this->objParam->getParametro('tipo_documento') == 'recibo') {
            $this->docexcel->getActiveSheet()->setCellValue('A6','Fecha');
            $this->docexcel->getActiveSheet()->setCellValue('B6','Nro. Recibo');
            $this->docexcel->getActiveSheet()->setCellValue('C6','Importe M/L');
            $this->docexcel->getActiveSheet()->setCellValue('D6','Concepto');
            $this->docexcel->getActiveSheet()->setCellValue('E6','Estado Recibo');
            $this->docexcel->getActiveSheet()->setCellValue('F6','Tipo Recibo');
            $this->docexcel->getActiveSheet()->setCellValue('G6','Cajero');
          }

          $this->docexcel->getActiveSheet()->getStyle('A6:H6')->applyFromArray($styleSubtitulos);



          $this->docexcel->getActiveSheet()->freezePaneByColumnAndRow(0,6);



    }

    function generarDatos(){
        $this->imprimeCabecera();

        $styleCabeza = array(
          'font'  => array(
                'bold'  => true,
                'size'  => 14,
                'name'  => 'Calibri',
            ),
            // 'alignment' => array(
            //     'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            //     'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            // ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => '8ECEE6'
                )
            )
          );

          $style_datos = array(
              'alignment' => array(
                  //'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                  'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
              ),
              'borders' => array(
                  'allborders' => array(
                      'style' => PHPExcel_Style_Border::BORDER_THIN
                  )
              ),
          );

        $styleTitulosDetalle = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 11,
                'name'  => 'Calibri',
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => '8ECEE6'
                )
            )
        );

        $styleTitulosPV = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 14,
                'name'  => 'Calibri',
            ),
            // 'alignment' => array(
            //     'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            //     'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            // ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => '3F94B4'
                )
            )
        );

        $styleDetalleFondo = array(
            // 'font'  => array(
            //     'bold'  => true,
            //     'size'  => 14,
            //     'name'  => 'Calibri',
            // ),
            // 'alignment' => array(
            //     'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            //     'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            // ),


            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => 'FFFFFF'
                )
            )
        );

        $styleMerge = array(
            // 'font'  => array(
            //     'bold'  => true,
            //     'size'  => 11,
            //     'name'  => 'Calibri',
            // ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            // 'fill' => array(
            //     'type' => PHPExcel_Style_Fill::FILL_SOLID,
            //     'color' => array(
            //         'rgb' => 'EDEDED'
            //     )
            // ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );

        $style_todos_bordes = array(
            'borders' => array(
                 'allborders' => array(
                     'style' => PHPExcel_Style_Border::BORDER_THIN
                 )
             ),
        );

        $style_totales_pv = array(
          'font'  => array(
              'bold'  => true,
              'size'  => 12,
              'name'  => 'Calibri',
          ),
          'fill' => array(
              'type' => PHPExcel_Style_Fill::FILL_SOLID,
              'color' => array(
                  'rgb' => 'FFC052'
              )
          )
        );

        $style_totales_general = array(
          'font'  => array(
              'bold'  => true,
              'size'  => 12,
              'name'  => 'Calibri',
          ),
          'fill' => array(
              'type' => PHPExcel_Style_Fill::FILL_SOLID,
              'color' => array(
                  'rgb' => '73ED00'
              )
          )
        );

        $styleAnulada = array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => 'FA7967'
                )
            )
          );


        $posicionAg = 7;
        $fila = 7;
        $inicio = 9;

        $punto_venta=array();
        $codigoPV=array();
        $estacion=array();
        $concepto=array();


        // foreach($this->datos_contenido as $value){
        //     $valor=$value['nombre'];
        //      // if(!in_array($valor, $punto_venta)){
        //          $punto_venta[]=$valor;
        //      // }
        // }
        //
        // foreach($this->datos_contenido as $value2){
        //     $valor2=$value2['codigo'];
        //      // if(!in_array($valor2, $codigoPV)){
        //          $codigoPV[]=$valor2;
        //      // }
        // }
        //
        //
        //
        // foreach($this->datos_detalle as $value4){
        //     $valor4=$value4['concepto'];
        //       if(!in_array($valor4, $concepto)){
        //          $concepto[]=$valor4;
        //       }
        // }

        $datos = $this->datos_contenido;


        if ($this->objParam->getParametro('tipo_documento') == 'factura') {
          foreach ($datos as $value) {

            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $value['fecha']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $value['nroaut']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $value['nro_factura']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $value['total_precio']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $value['concepto']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $value['tipo_factura']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $value['estado']);
            $this->docexcel->getActiveSheet()->getStyle("A$fila:F$fila")->applyFromArray($style_datos);

            if ($value['desc_persona'] == 'cabecera') {
              $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila,'AGENCIA: ('.$value['codigo'].') '.$value['nombre']);
              //$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila,$value['concepto']);
              $this->docexcel->getActiveSheet()->getStyle("A$fila:H$fila")->applyFromArray($styleCabeza);
              $this->docexcel->getActiveSheet()->mergeCells("A$fila:D$fila");
            } else {
              $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, $value['desc_persona']);
              $this->docexcel->getActiveSheet()->getStyle("A$fila:H$fila")->applyFromArray($style_datos);
            }

            if ($value['estado'] == 'ANULADA') {
              $this->docexcel->getActiveSheet()->getStyle("A$fila:H$fila")->applyFromArray($styleAnulada);
            }


          $this->docexcel->getActiveSheet()->getStyle("B$fila:D$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);


          $fila++;
          }
        } elseif ($this->objParam->getParametro('tipo_documento') == 'recibo') {
          foreach ($datos as $value) {

            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $value['fecha']);
            //$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $value['nroaut']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $value['nro_factura']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $value['total_precio']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $value['concepto']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $value['tipo_factura']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $value['estado']);
            $this->docexcel->getActiveSheet()->getStyle("A$fila:F$fila")->applyFromArray($style_datos);

            if ($value['desc_persona'] == 'cabecera') {
              $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila,'AGENCIA: ('.$value['codigo'].') '.$value['nombre']);
              //$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila,$value['concepto']);
              $this->docexcel->getActiveSheet()->getStyle("A$fila:H$fila")->applyFromArray($styleCabeza);
              $this->docexcel->getActiveSheet()->mergeCells("A$fila:D$fila");
            } else {
              $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $value['desc_persona']);
              $this->docexcel->getActiveSheet()->getStyle("A$fila:H$fila")->applyFromArray($style_datos);
            }


            if ($value['estado'] == 'ANULADA') {
              $this->docexcel->getActiveSheet()->getStyle("A$fila:H$fila")->applyFromArray($styleAnulada);
            }


          $this->docexcel->getActiveSheet()->getStyle("B$fila:C$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);


          $fila++;
          }
        }




        $style_totales_pv = array(
          'font'  => array(
              'bold'  => true,
              'size'  => 12,
              'name'  => 'Calibri',
          ),
          'fill' => array(
              'type' => PHPExcel_Style_Fill::FILL_SOLID,
              'color' => array(
                  'rgb' => 'FFC052'
              )
          )
        );

        $style_totales_general = array(
          'font'  => array(
              'bold'  => true,
              'size'  => 12,
              'name'  => 'Calibri',
          ),
          'fill' => array(
              'type' => PHPExcel_Style_Fill::FILL_SOLID,
              'color' => array(
                  'rgb' => '73ED00'
              )
          )
        );
        $inicial = 7;
        $final = ($fila - 1);
        $totales_gen = ($fila + 1);

        if ($this->objParam->getParametro('tipo_documento') == 'factura') {
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, 'Total AGT');
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, "=SUM(D$inicial:D$final)");
          $this->docexcel->getActiveSheet()->getStyle("A$fila:H$fila")->applyFromArray($style_totales_pv);
          $this->docexcel->getActiveSheet()->getStyle("D$fila:D$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);



          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $totales_gen, 'TOTALES');
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $totales_gen, "=SUM(D$inicial:D$final)");

          $this->docexcel->getActiveSheet()->getStyle("A$totales_gen:H$totales_gen")->applyFromArray($style_totales_general);
          $this->docexcel->getActiveSheet()->getStyle("D$totales_gen:D$totales_gen")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

        } elseif ($this->objParam->getParametro('tipo_documento') == 'recibo') {
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, 'Total AGT');
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, "=SUM(C$inicial:C$final)");
          $this->docexcel->getActiveSheet()->getStyle("A$fila:H$fila")->applyFromArray($style_totales_pv);
          $this->docexcel->getActiveSheet()->getStyle("C$fila:C$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);



          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $totales_gen, 'TOTALES');
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $totales_gen, "=SUM(C$inicial:C$final)");

          $this->docexcel->getActiveSheet()->getStyle("A$totales_gen:H$totales_gen")->applyFromArray($style_totales_general);
          $this->docexcel->getActiveSheet()->getStyle("C$totales_gen:C$totales_gen")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

        }

    }

    function generarReporte(){
        $this->generarDatos();
        $this->docexcel->setActiveSheetIndex(0);
        $this->objWriter = PHPExcel_IOFactory::createWriter($this->docexcel, 'Excel5');
        $this->objWriter->save($this->url_archivo);
    }

}
?>
