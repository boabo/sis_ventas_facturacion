<?php
class RFacturacionComputarizada
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
  function datosHeader ($contenido) {
        $this->datos_contenido = $contenido;
    }
    function imprimeCabecera() {
        $this->docexcel->createSheet();


        $styleTituloPrincipal = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 11,
                'name'  => 'Calibri',
            ),
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
            // 'borders' => array(
            //     'allborders' => array(
            //         'style' => PHPExcel_Style_Border::BORDER_THIN
            //     )
            // )
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

        $styleFondoBlanco = array(
          'fill' => array(
              'type' => PHPExcel_Style_Fill::FILL_SOLID,
              'color' => array(
                  'rgb' => 'FFFFFF'
              )
          ),
        );



        $gdImage = imagecreatefromjpeg('../../../lib/imagenes/Logo_libro_mayor.jpg');
        // Add a drawing to the worksheetecho date('H:i:s') . " Add a drawing to the worksheet\n";
        $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
        $objDrawing->setName('Sample image');
        $objDrawing->setDescription('Sample image');
        $objDrawing->setImageResource($gdImage);
        $objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
        $objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
        $objDrawing->setHeight(100);
        $objDrawing->setCoordinates('A1');
        $objDrawing->setWorksheet($this->docexcel->getActiveSheet());
        // $this->docexcel->getActiveSheet()->mergeCells('A1:C1');

        $fechaGeneracion =  date("d/m/Y");
        $horaGeneracion=  date("H:i:s");

        if ($this->objParam->getParametro('tipo_documento') == 'factura') {
          $this->docexcel->getActiveSheet()->setTitle('FACTURAS');
          $this->docexcel->setActiveSheetIndex(0);
        } elseif ($this->objParam->getParametro('tipo_documento') == 'recibo') {
          $this->docexcel->getActiveSheet()->setTitle('RECIBOS');
          $this->docexcel->setActiveSheetIndex(0);
        }



        //var_dump("aqui los parametros recibidos",$this->objParam->getParametro('tipo_documento'));

        if ($this->objParam->getParametro('tipo_documento') == 'factura') {
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,2,'REPORTE DE FACTURAS (COMPUTARIZADAS/MANUALES) VENTAS PROPIAS');
        } elseif ($this->objParam->getParametro('tipo_documento') == 'recibo') {
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,2,'REPORTE DE RECIBOS (COMPUTARIZADOS/MANUALES) VENTAS PROPIAS');
        }

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,3,'DEL: '.$this->objParam->getParametro('desde').' AL: '.$this->objParam->getParametro('hasta'));

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8,3,'FECHA: '.$fechaGeneracion);
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8,4,'HORA: '.$horaGeneracion);


        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,4,'PUNTO VENTA: '.$this->objParam->getParametro('nombre_pv'));
        //$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,5,'PERSONA NATURALES');
        $this->docexcel->getActiveSheet()->mergeCells('C2:G2');
        $this->docexcel->getActiveSheet()->mergeCells('C3:G3');
        $this->docexcel->getActiveSheet()->mergeCells('C4:G4');
        $this->docexcel->getActiveSheet()->mergeCells('C5:G5');
        $this->docexcel->getActiveSheet()->getStyle('A1:G5')->applyFromArray($styleTituloPrincipal);
        $this->docexcel->getActiveSheet()->getStyle('A1:Q5')->applyFromArray($styleFondoBlanco);





        //*************************************Cabecera*****************************************
         $this->docexcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
         $this->docexcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
         $this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
         $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
         $this->docexcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
         $this->docexcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
         $this->docexcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
         $this->docexcel->getActiveSheet()->getColumnDimension('H')->setWidth(35);
         $this->docexcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
         $this->docexcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
         $this->docexcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
         $this->docexcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
         $this->docexcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
         $this->docexcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
         $this->docexcel->getActiveSheet()->getColumnDimension('O')->setWidth(25);
         $this->docexcel->getActiveSheet()->getColumnDimension('P')->setWidth(40);
         $this->docexcel->getActiveSheet()->getColumnDimension('Q')->setWidth(40);

         if ($this->objParam->getParametro('tipo_documento') == 'factura') {
           $this->docexcel->getActiveSheet()->setCellValue('A6','Fecha');
           $this->docexcel->getActiveSheet()->setCellValue('B6','Nro. Factura');
           $this->docexcel->getActiveSheet()->setCellValue('C6','Cod. Control');
           $this->docexcel->getActiveSheet()->setCellValue('D6','Total Factura');
           $this->docexcel->getActiveSheet()->setCellValue('E6','Exentos');
           $this->docexcel->getActiveSheet()->setCellValue('F6','Comisión');

           $this->docexcel->getActiveSheet()->setCellValue('G6','Cantidad');
           $this->docexcel->getActiveSheet()->setCellValue('H6','Conceptos');
           $this->docexcel->getActiveSheet()->setCellValue('I6','Precio/Unit');
           $this->docexcel->getActiveSheet()->setCellValue('J6','Total');
           $this->docexcel->getActiveSheet()->setCellValue('K6','F-Pago');
           $this->docexcel->getActiveSheet()->setCellValue('L6','Efectivo');
           $this->docexcel->getActiveSheet()->setCellValue('M6','Cta-Cte');
           $this->docexcel->getActiveSheet()->setCellValue('N6','Observaciones');
           $this->docexcel->getActiveSheet()->setCellValue('O6','Estado Factura');
           $this->docexcel->getActiveSheet()->setCellValue('P6','Tipo Factura');
           $this->docexcel->getActiveSheet()->setCellValue('Q6','Cajero');

         } elseif ($this->objParam->getParametro('tipo_documento') == 'recibo') {
           $this->docexcel->getActiveSheet()->setCellValue('A6','Fecha');
           $this->docexcel->getActiveSheet()->setCellValue('B6','Nro. Recibo');
           $this->docexcel->getActiveSheet()->setCellValue('C6','Total Recibo');
           $this->docexcel->getActiveSheet()->setCellValue('D6','Exentos');
           $this->docexcel->getActiveSheet()->setCellValue('E6','Comisión');

           $this->docexcel->getActiveSheet()->setCellValue('F6','Cantidad');
           $this->docexcel->getActiveSheet()->setCellValue('G6','Conceptos');
           $this->docexcel->getActiveSheet()->setCellValue('H6','Precio/Unit');
           $this->docexcel->getActiveSheet()->setCellValue('I6','Total');
           $this->docexcel->getActiveSheet()->setCellValue('J6','F-Pago');
           $this->docexcel->getActiveSheet()->setCellValue('K6','Efectivo');
           $this->docexcel->getActiveSheet()->setCellValue('L6','Cta-Cte');
           $this->docexcel->getActiveSheet()->setCellValue('M6','Observaciones');
           $this->docexcel->getActiveSheet()->setCellValue('N6','Estado Recibo');
           $this->docexcel->getActiveSheet()->setCellValue('O6','Tipo Recibo');
           $this->docexcel->getActiveSheet()->setCellValue('P6','Cajero');
         }






        $this->docexcel->getActiveSheet()->getStyle('A6:Q6')->applyFromArray($styleSubtitulos);
        $this->docexcel->getActiveSheet()->freezePaneByColumnAndRow(0,8);




    }

    function generarDatos(){
        $this->imprimeCabecera();
        $fila = 7;
        $conceptos_cant = 0;
        $formas_pago_cant = 0;

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

          $styleAnulada = array(
              'fill' => array(
                  'type' => PHPExcel_Style_Fill::FILL_SOLID,
                  'color' => array(
                      'rgb' => 'FA7967'
                  )
              )
            );


        $datos = $this->datos_contenido;

        if ($this->objParam->getParametro('tipo_documento') == 'factura') {
          //var_dump("aqui el value 1111",$this->datos_contenido->datos);
          foreach ($datos as $value) {
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $value['fecha']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $value['nro_factura']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $value['cod_control']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $value['total_venta']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $value['exento']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $value['comision']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(13, $fila, $value['observaciones']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(14, $fila, $value['estado']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(15, $fila, $value['tipo_factura']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(16, $fila, $value['cajero']);

            if ($value['tipo_factura'] == 'cabecera') {
              $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila,'AGENCIA: ('.$value['codigo'].') '.$value['nombre']);
              $this->docexcel->getActiveSheet()->getStyle("A$fila:Q$fila")->applyFromArray($styleCabeza);
              $this->docexcel->getActiveSheet()->mergeCells("A$fila:Q$fila");
            }

            $conceptos = explode(",", $value['conceptos']);
            $cantidad = explode(",", $value['cantidad']);
            $precioUnitario = explode(",", $value['precio']);
            $precioTotal = explode(",", $value['total_precio']);

            $montos = explode(",", $value['total_monto']);
            $pagos = explode(",", $value['forma_pago']);

            if ($value['tipo_factura'] != 'cabecera') {
              $conceptos_cant = $fila;
              for ($i=0; $i < count($conceptos) ; $i++) {
                   $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $conceptos_cant, $cantidad[$i]);
                   $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $conceptos_cant, $conceptos[$i]);
                   $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $conceptos_cant, $precioUnitario[$i]);
                   $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $conceptos_cant, $precioTotal[$i]);
                   $conceptos_cant ++;
               }
            }

            if ($value['tipo_factura'] != 'cabecera') {
            $formas_pago_cant = $fila;
            for ($i=0; $i < count($pagos) ; $i++) {
              $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $formas_pago_cant, $pagos[$i]);
              if ($pagos[$i] == 'CA') {
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $formas_pago_cant, $montos[$i]);
              } else {
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(12, $formas_pago_cant, $montos[$i]);
              }
              $formas_pago_cant ++;
            }
          }

          if ($value['estado'] == 'ANULADA') {
            $this->docexcel->getActiveSheet()->getStyle("A$fila:Q$fila")->applyFromArray($styleAnulada);
          }



          if ($conceptos_cant > $formas_pago_cant) {
            $restado = ($conceptos_cant - 1);
            $this->docexcel->getActiveSheet()->mergeCells("A$fila:A$restado");
            $this->docexcel->getActiveSheet()->mergeCells("B$fila:B$restado");
            $this->docexcel->getActiveSheet()->mergeCells("C$fila:C$restado");
            $this->docexcel->getActiveSheet()->mergeCells("D$fila:D$restado");
            $this->docexcel->getActiveSheet()->mergeCells("E$fila:E$restado");
            $this->docexcel->getActiveSheet()->mergeCells("F$fila:F$restado");

            $this->docexcel->getActiveSheet()->mergeCells("N$fila:N$restado");
            $this->docexcel->getActiveSheet()->mergeCells("O$fila:O$restado");
            $this->docexcel->getActiveSheet()->mergeCells("P$fila:P$restado");
            $this->docexcel->getActiveSheet()->mergeCells("Q$fila:Q$restado");
            $this->docexcel->getActiveSheet()->getStyle("A$fila:Q$restado")->applyFromArray($style_datos);

            if ($value['estado'] == 'ANULADA') {
              $this->docexcel->getActiveSheet()->getStyle("A$fila:Q$restado")->applyFromArray($styleAnulada);
            }


            $fila = $restado;
          } elseif ($formas_pago_cant > $conceptos_cant) {
            $restado = ($formas_pago_cant - 1);
            $this->docexcel->getActiveSheet()->mergeCells("A$fila:A$restado");
            $this->docexcel->getActiveSheet()->mergeCells("B$fila:B$restado");
            $this->docexcel->getActiveSheet()->mergeCells("C$fila:C$restado");
            $this->docexcel->getActiveSheet()->mergeCells("D$fila:D$restado");
            $this->docexcel->getActiveSheet()->mergeCells("E$fila:E$restado");
            $this->docexcel->getActiveSheet()->mergeCells("F$fila:F$restado");

            $this->docexcel->getActiveSheet()->mergeCells("N$fila:N$restado");
            $this->docexcel->getActiveSheet()->mergeCells("O$fila:O$restado");
            $this->docexcel->getActiveSheet()->mergeCells("P$fila:P$restado");
            $this->docexcel->getActiveSheet()->mergeCells("Q$fila:Q$restado");
            $this->docexcel->getActiveSheet()->getStyle("A$fila:Q$restado")->applyFromArray($style_datos);

            if ($value['estado'] == 'ANULADA') {
              $this->docexcel->getActiveSheet()->getStyle("A$fila:Q$restado")->applyFromArray($styleAnulada);
            }
            $fila = $restado;
          }

          $this->docexcel->getActiveSheet()->getStyle("A$fila:Q$fila")->applyFromArray($style_datos);

          $this->docexcel->getActiveSheet()->getStyle("D$fila:G$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
          $this->docexcel->getActiveSheet()->getStyle("I$fila:J$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
          $this->docexcel->getActiveSheet()->getStyle("L$fila:M$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);


          $fila++;
          }
          $inicial = 7;
          $final = ($fila - 1);
          $totales_gen = ($fila + 1);

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

          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, 'Total AGT');
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, "=SUM(D$inicial:D$final)");
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, "=SUM(E$inicial:E$final)");
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, "=SUM(F$inicial:F$final)");
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, "=SUM(I$inicial:I$final)");
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $fila, "=SUM(J$inicial:J$final)");
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $fila, "=SUM(L$inicial:L$final)");
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(12, $fila, "=SUM(M$inicial:M$final)");
          $this->docexcel->getActiveSheet()->getStyle("A$fila:Q$fila")->applyFromArray($style_totales_pv);
            $this->docexcel->getActiveSheet()->getStyle("A$fila:M$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);



          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $totales_gen, 'TOTALES');
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $totales_gen, "=SUM(D$inicial:D$final)");
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $totales_gen, "=SUM(E$inicial:E$final)");
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $totales_gen, "=SUM(F$inicial:F$final)");
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $totales_gen, "=SUM(I$inicial:I$final)");
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $totales_gen, "=SUM(J$inicial:J$final)");
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $totales_gen, "=SUM(L$inicial:L$final)");
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(12, $totales_gen, "=SUM(M$inicial:M$final)");

          $this->docexcel->getActiveSheet()->getStyle("A$totales_gen:Q$totales_gen")->applyFromArray($style_totales_general);
          $this->docexcel->getActiveSheet()->getStyle("A$totales_gen:M$totales_gen")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

        } elseif ($this->objParam->getParametro('tipo_documento') == 'recibo') {
          //var_dump("aqui el value 1111",$this->datos_contenido->datos);
          foreach ($datos as $value) {
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $value['fecha']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $value['nro_factura']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $value['total_venta']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $value['exento']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $value['comision']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(12, $fila, $value['observaciones']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(13, $fila, $value['estado']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(14, $fila, $value['tipo_factura']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(15, $fila, $value['cajero']);

            if ($value['tipo_factura'] == 'cabecera') {
              $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila,'AGENCIA: ('.$value['codigo'].') '.$value['nombre']);
              $this->docexcel->getActiveSheet()->getStyle("A$fila:P$fila")->applyFromArray($styleCabeza);
              $this->docexcel->getActiveSheet()->mergeCells("A$fila:P$fila");
            }

            $conceptos = explode(",", $value['conceptos']);
            $cantidad = explode(",", $value['cantidad']);
            $precioUnitario = explode(",", $value['precio']);
            $precioTotal = explode(",", $value['total_precio']);

            $montos = explode(",", $value['total_monto']);
            $pagos = explode(",", $value['forma_pago']);

            if ($value['tipo_factura'] != 'cabecera') {
              $conceptos_cant = $fila;
              for ($i=0; $i < count($conceptos) ; $i++) {
                   $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $conceptos_cant, $cantidad[$i]);
                   $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $conceptos_cant, $conceptos[$i]);
                   $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $conceptos_cant, $precioUnitario[$i]);
                   $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $conceptos_cant, $precioTotal[$i]);
                   $conceptos_cant ++;
               }
            }

            if ($value['tipo_factura'] != 'cabecera') {
            $formas_pago_cant = $fila;
            for ($i=0; $i < count($pagos) ; $i++) {
              $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $formas_pago_cant, $pagos[$i]);
              if ($pagos[$i] == 'CA') {
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $formas_pago_cant, $montos[$i]);
              } else {
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $formas_pago_cant, $montos[$i]);
              }
              $formas_pago_cant ++;
            }
          }

          if ($value['estado'] == 'ANULADA') {
            $this->docexcel->getActiveSheet()->getStyle("A$fila:P$fila")->applyFromArray($styleAnulada);
          }



          if ($conceptos_cant > $formas_pago_cant) {
            $restado = ($conceptos_cant - 1);
            $this->docexcel->getActiveSheet()->mergeCells("A$fila:A$restado");
            $this->docexcel->getActiveSheet()->mergeCells("B$fila:B$restado");
            $this->docexcel->getActiveSheet()->mergeCells("C$fila:C$restado");
            $this->docexcel->getActiveSheet()->mergeCells("D$fila:D$restado");
            $this->docexcel->getActiveSheet()->mergeCells("E$fila:E$restado");
            $this->docexcel->getActiveSheet()->mergeCells("M$fila:M$restado");
            $this->docexcel->getActiveSheet()->mergeCells("N$fila:N$restado");
            $this->docexcel->getActiveSheet()->mergeCells("O$fila:O$restado");
            $this->docexcel->getActiveSheet()->mergeCells("P$fila:P$restado");
            $this->docexcel->getActiveSheet()->getStyle("A$fila:P$restado")->applyFromArray($style_datos);

            if ($value['estado'] == 'ANULADA') {
              $this->docexcel->getActiveSheet()->getStyle("A$fila:P$restado")->applyFromArray($styleAnulada);
            }


            $fila = $restado;
          } elseif ($formas_pago_cant > $conceptos_cant) {
            $restado = ($formas_pago_cant - 1);
            $this->docexcel->getActiveSheet()->mergeCells("A$fila:A$restado");
            $this->docexcel->getActiveSheet()->mergeCells("B$fila:B$restado");
            $this->docexcel->getActiveSheet()->mergeCells("C$fila:C$restado");
            $this->docexcel->getActiveSheet()->mergeCells("D$fila:D$restado");
            $this->docexcel->getActiveSheet()->mergeCells("E$fila:E$restado");
            $this->docexcel->getActiveSheet()->mergeCells("M$fila:M$restado");
            $this->docexcel->getActiveSheet()->mergeCells("N$fila:N$restado");
            $this->docexcel->getActiveSheet()->mergeCells("O$fila:O$restado");
            $this->docexcel->getActiveSheet()->mergeCells("P$fila:P$restado");
            $this->docexcel->getActiveSheet()->getStyle("A$fila:P$restado")->applyFromArray($style_datos);

            if ($value['estado'] == 'ANULADA') {
              $this->docexcel->getActiveSheet()->getStyle("A$fila:P$restado")->applyFromArray($styleAnulada);
            }
            $fila = $restado;
          }

          $this->docexcel->getActiveSheet()->getStyle("A$fila:P$fila")->applyFromArray($style_datos);

          $this->docexcel->getActiveSheet()->getStyle("C$fila:F$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
          $this->docexcel->getActiveSheet()->getStyle("H$fila:I$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
          $this->docexcel->getActiveSheet()->getStyle("K$fila:K$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);


          $fila++;
          }
          $inicial = 7;
          $final = ($fila - 1);
          $totales_gen = ($fila + 1);

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

          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, 'Total AGT');
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, "=SUM(C$inicial:C$final)");
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, "=SUM(D$inicial:D$final)");
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, "=SUM(E$inicial:E$final)");
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, "=SUM(H$inicial:H$final)");
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, "=SUM(I$inicial:I$final)");
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $fila, "=SUM(K$inicial:K$final)");
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $fila, "=SUM(L$inicial:L$final)");
          $this->docexcel->getActiveSheet()->getStyle("A$fila:P$fila")->applyFromArray($style_totales_pv);
            $this->docexcel->getActiveSheet()->getStyle("A$fila:L$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);



          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $totales_gen, 'TOTALES');
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $totales_gen, "=SUM(C$inicial:C$final)");
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $totales_gen, "=SUM(D$inicial:D$final)");
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $totales_gen, "=SUM(E$inicial:E$final)");
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $totales_gen, "=SUM(H$inicial:H$final)");
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $totales_gen, "=SUM(I$inicial:I$final)");
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $totales_gen, "=SUM(K$inicial:K$final)");
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $totales_gen, "=SUM(L$inicial:L$final)");

          $this->docexcel->getActiveSheet()->getStyle("A$totales_gen:P$totales_gen")->applyFromArray($style_totales_general);
          $this->docexcel->getActiveSheet()->getStyle("A$totales_gen:L$totales_gen")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

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
