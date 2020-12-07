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
  function datosHeader ($contenido,$detalle) {
        $this->datos_contenido = $contenido;
        $this->datos_detalle = $detalle;
    }
    function imprimeCabecera() {
        $this->docexcel->createSheet();
        $this->docexcel->getActiveSheet()->setTitle('REPORTE VENTAS PROPIAS');
        $this->docexcel->setActiveSheetIndex(0);

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


        // $this->docexcel->getActiveSheet()->mergeCells('A6:C6');
        // $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,6,'Ingresos' );

        $fechaGeneracion =  date("d/m/Y");
        $horaGeneracion=  date("H:i:s");

        if ($this->objParam->getParametro('formato_reporte') == 'REPORTE DE FACTURAS') {
          $this->docexcel->getActiveSheet()->mergeCells('C3:G3');
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,3,'REPORTE DE FACTURAS COMPUTARIZADAS VENTAS PROPIAS');
          $this->docexcel->getActiveSheet()->mergeCells('D4:F4');
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3,4,'Del: '.$this->objParam->getParametro('desde').' Al: '.$this->objParam->getParametro('hasta'));

          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8,3,'Fecha: '.$fechaGeneracion);
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8,4,'Hora: '.$horaGeneracion);

          $this->docexcel->getActiveSheet()->getStyle('A1:M1')->applyFromArray($styleTitulos1);
          $this->docexcel->getActiveSheet()->getStyle('A2:M2')->applyFromArray($styleTitulos1);
          $this->docexcel->getActiveSheet()->getStyle('A3:M3')->applyFromArray($styleTitulosPrincipal);
          $this->docexcel->getActiveSheet()->getStyle('A4:M4')->applyFromArray($styleTitulosPrincipal);
          $this->docexcel->getActiveSheet()->getStyle('A5:M5')->applyFromArray($styleTitulos1);
          $this->docexcel->getActiveSheet()->getStyle('A6:M6')->applyFromArray($styleTitulos1);


          $this->docexcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
          $this->docexcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
          $this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
          $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
          $this->docexcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
          $this->docexcel->getActiveSheet()->getColumnDimension('F')->setWidth(35);
          $this->docexcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
          $this->docexcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
          $this->docexcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
          $this->docexcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
          $this->docexcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
          $this->docexcel->getActiveSheet()->getColumnDimension('L')->setWidth(35);
          $this->docexcel->getActiveSheet()->freezePaneByColumnAndRow(0,6);


        }

    }

    function generarDatos(){
        $this->imprimeCabecera();

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

        $posicionAg = 7;
        $fila = 9;
        $inicio = 9;

        $punto_venta=array();
        $codigoPV=array();
        $estacion=array();
        $pais=array();


        foreach($this->datos_contenido as $value){
            $valor=$value['nombre'];
             // if(!in_array($valor, $punto_venta)){
                 $punto_venta[]=$valor;
             // }
        }

        foreach($this->datos_contenido as $value2){
            $valor2=$value2['codigo'];
             // if(!in_array($valor2, $codigoPV)){
                 $codigoPV[]=$valor2;
             // }
        }

        foreach($this->datos_contenido as $value3){
            $valor3=$value3['lugar'];
             // if(!in_array($valor3, $estacion)){
                 $estacion[]=$valor3;
             // }
        }

        foreach($this->datos_contenido as $value4){
            $valor4=$value4['pais'];
             // if(!in_array($valor4, $pais)){
                 $pais[]=$valor4;
             // }
        }

        $iteracion = 0;
        foreach($punto_venta as $value1 ){

          $estilosMenu = $posicionAg+1;
          $estiloPV = $posicionAg;

          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $posicionAg,'AGENCIA: ('.$codigoPV[$iteracion].') '.$value1);
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $posicionAg,'FACTURAS COMPUTARIZADAS SUELTAS');
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $posicionAg,'ESTACION: '.$estacion[$iteracion]);
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $posicionAg,$pais[$iteracion]);
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, ($posicionAg+1), 'Nro. Factura');
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, ($posicionAg+1), 'Total Factura');
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, ($posicionAg+1), 'Exentos');
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, ($posicionAg+1), 'Comision');
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, ($posicionAg+1), 'Cantidad');
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, ($posicionAg+1), 'CONCEPTOS');
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, ($posicionAg+1), 'Precio/Unit');
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, ($posicionAg+1), 'Total');
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, ($posicionAg+1), 'F-PAGO');
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, ($posicionAg+1), 'EFECTIVO');
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, ($posicionAg+1), 'CTA-CTE');
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, ($posicionAg+1), 'OBSERVACIONES/DOCUMENTOS');

          unset($totalesFacturas);
          unset($totalesComision);
          unset($totalesExcentos);
          unset($totalPrecioUnitario);
          unset($totalPrecioTotal);
          unset($totalEfectivo);
          unset($totalOtros);



          $this->docexcel->getActiveSheet()->getStyle("A$posicionAg:L$posicionAg")->applyFromArray($styleTitulosPV);
          $this->docexcel->getActiveSheet()->getStyle("A$estilosMenu:L$estilosMenu")->applyFromArray($styleTitulosDetalle);

          foreach ($this->datos_detalle as $value) {
              if ($value['nombre'] == $value1) {

                    $totalesFacturas []= $value ['total_venta'];
                    $totalesComision []= $value ['comision'];
                    $totalesExcentos []= $value ['exento'];

                    $totalesFacturasGeneral[]=$value ['total_venta'];
                    $totalesComisionGeneral[]=$value ['comision'];
                    $totalesExcentosGeneral[]=$value ['exento'];

                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $value['nro_factura']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $value['total_venta']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $value['exento']);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $value['comision']);
                    $this->docexcel->getActiveSheet()->getStyle("B$fila:D$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

                    $conceptos = explode(",", $value['conceptos']);
                    $pagos = explode(",", $value['forma_pago']);
                    $cantidad = explode(",", $value['cantidad']);
                    $precioUnitario = explode(",", $value['precio']);
                    $montos = explode(",", $value['total_monto']);
                    $precioTotal = explode(",", $value['total_precio']);

                    $inicioCantidad = $fila;
                    for ($i=0; $i < count($conceptos) ; $i++) {
                      $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $inicioCantidad, $cantidad[$i]);
                        $this->docexcel->getActiveSheet()->getStyle("A$inicioCantidad:L$inicioCantidad")->applyFromArray($styleDetalleFondo);
                        $this->docexcel->getActiveSheet()->getStyle("A$inicioCantidad:L$inicioCantidad")->applyFromArray($style_todos_bordes);
                      $inicioCantidad ++;
                    }

                    //var_dump("aqui contador",$conceptos);
                    $inicio = $fila;
                    for ($i=0; $i < count($conceptos) ; $i++) {
                      $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $inicio, $conceptos[$i]);
                      $this->docexcel->getActiveSheet()->getStyle("A$inicio:L$inicio")->applyFromArray($styleDetalleFondo);
                      $inicio ++;
                    }

                    $inicioPrecio = $fila;
                    for ($i=0; $i < count($conceptos) ; $i++) {
                      $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $inicioPrecio, $precioUnitario[$i]);
                      $this->docexcel->getActiveSheet()->getStyle("A$inicioPrecio:L$inicioPrecio")->applyFromArray($styleDetalleFondo);
                      $this->docexcel->getActiveSheet()->getStyle("G$inicioPrecio:H$inicioPrecio")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

                      $totalPrecioUnitario[] = $precioUnitario[$i];
                      $totalPrecioUnitarioGeneral[]=$precioUnitario[$i];
                      $inicioPrecio ++;
                    }

                    $inicioPrecioTotal = $fila;
                    for ($i=0; $i < count($conceptos) ; $i++) {
                      $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $inicioPrecioTotal, $precioTotal[$i]);
                      $this->docexcel->getActiveSheet()->getStyle("A$inicioPrecioTotal:L$inicioPrecioTotal")->applyFromArray($styleDetalleFondo);
                      $this->docexcel->getActiveSheet()->getStyle("H$inicioPrecio:H$inicioPrecio")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
                      $totalPrecioTotal[] = $precioTotal[$i];
                      $totalPrecioTotalGeneral[]=$precioTotal[$i];
                      $inicioPrecioTotal ++;
                    }

                    $inicio2 = $fila;
                    for ($i=0; $i < count($pagos) ; $i++) {
                      $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $inicio2, $pagos[$i]);
                      if ($pagos[$i] == 'CA') {
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $inicio2, $montos[$i]);
                        $totalEfectivo[]=$montos[$i];
                        $totalEfectivoGeneral[]=$montos[$i];
                      } else {
                        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $inicio2, $montos[$i]);
                        $totalOtros[]=$montos[$i];
                        $totalOtrosGeneral[]=$montos[$i];
                      }
                      $this->docexcel->getActiveSheet()->getStyle("A$inicio2:L$inicio2")->applyFromArray($styleDetalleFondo);
                      $this->docexcel->getActiveSheet()->getStyle("J$inicio2:K$inicio2")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
                      $inicio2 ++;
                    }
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $fila, $value['observaciones']);
                    /*Aqui Hacemos los MERGE si corresponde*/
                      $minimo = min($inicio,$fila,$inicio2,$inicioCantidad,$inicioPrecio,$inicioPrecioTotal);
                    /***************************************/
                    // if ($maximo > $fila) {
                    //   $this->docexcel->getActiveSheet()->mergeCells("A$fila:A$maximo");
                    // }

                    $fila = max($inicio,$fila,$inicio2,$inicioCantidad,$inicioPrecio,$inicioPrecioTotal);
                    $fila = $fila-1;


                    $this->docexcel->getActiveSheet()->getStyle("A$fila:L$fila")->applyFromArray($styleDetalleFondo);
                    $fila++;



                    /*Obtenemos la ultima fila*/
                    $ultimaFila=$fila-1;

                    if ($minimo < $fila) {
                      $this->docexcel->getActiveSheet()->mergeCells("A$minimo:A$ultimaFila");
                      $this->docexcel->getActiveSheet()->mergeCells("B$minimo:B$ultimaFila");
                      $this->docexcel->getActiveSheet()->mergeCells("C$minimo:C$ultimaFila");
                      $this->docexcel->getActiveSheet()->mergeCells("D$minimo:D$ultimaFila");
                      $this->docexcel->getActiveSheet()->mergeCells("L$minimo:L$ultimaFila");
                      $this->docexcel->getActiveSheet()->getStyle("L$minimo:L$ultimaFila")->getAlignment()->setWrapText(true);
                      $this->docexcel->getActiveSheet()->getStyle("A$minimo:D$ultimaFila")->applyFromArray($styleMerge);
                      $this->docexcel->getActiveSheet()->getStyle("L$minimo:L$ultimaFila")->applyFromArray($styleMerge);
                    }

                    /*************************/
                    //var_dump("aqui llega datos",max($inicio,$fila,$inicio2,$inicioCantidad,$inicioPrecio,$inicioPrecioTotal),$fila);
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, 'Total AGT');
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila,  array_sum($totalesFacturas));
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila,  array_sum($totalesExcentos));
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila,  array_sum($totalesComision));
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila,  array_sum($totalPrecioUnitario));
                    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila,  array_sum($totalPrecioTotal));
                    if ($totalEfectivo != NULL) {
                      $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $fila,  array_sum($totalEfectivo));
                    }
                    if ($totalOtros != NULL) {
                      $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $fila,  array_sum($totalOtros));
                    }
                    //$this->docexcel->getActiveSheet()->getStyle("A$fila:L$fila")->applyFromArray($style_totales_pv);



              }


          }
          $this->docexcel->getActiveSheet()->getStyle("A$fila:L$fila")->applyFromArray($style_totales_pv);
          $this->docexcel->getActiveSheet()->getStyle("A$fila:K$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);


          //
          $posicionAg = $fila+2;
          $fila = $posicionAg+2;
          $iteracion ++;
        }
          $totalGeneral = $fila-3;
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $totalGeneral, 'TOTALES');
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $totalGeneral,  array_sum($totalesFacturasGeneral));
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $totalGeneral,  array_sum($totalesExcentosGeneral));
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $totalGeneral,  array_sum($totalesComisionGeneral));
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $totalGeneral,  array_sum($totalPrecioUnitarioGeneral));
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $totalGeneral,  array_sum($totalPrecioTotalGeneral));
          if ($totalEfectivo != NULL) {
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $totalGeneral,  array_sum($totalEfectivoGeneral));
          }
          if ($totalOtros != NULL) {
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $totalGeneral,  array_sum($totalOtrosGeneral));
          }
          $this->docexcel->getActiveSheet()->getStyle("A$totalGeneral:L$totalGeneral")->applyFromArray($style_totales_general);
          $this->docexcel->getActiveSheet()->getStyle("A$totalGeneral:K$totalGeneral")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);


    }

    function generarReporte(){
        $this->generarDatos();
        $this->docexcel->setActiveSheetIndex(0);
        $this->objWriter = PHPExcel_IOFactory::createWriter($this->docexcel, 'Excel5');
        $this->objWriter->save($this->url_archivo);
    }

}
?>
