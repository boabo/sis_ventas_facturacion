<?php
class RReporteEmisionBoletosXLS
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
        $this->docexcel->getActiveSheet()->setTitle('Reporte Venta Propia');
        $this->docexcel->setActiveSheetIndex(0);

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
                    'rgb' => 'FDAC14'
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

        //$this->docexcel->getActiveSheet()->mergeCells('A1:C1');


        if ($this->objParam->getParametro('formato_reporte') == 'REPORTE FORMAS DE PAGO CTA/CTE (DEBE)') {
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,2,'ESTADO DE CUENTA DE: PASAJES, FACTURAS, RECIBOS OFICIALES');
        } elseif ($this->objParam->getParametro('formato_reporte') == 'REPORTE ANTICIPO (HABER)') {
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,2,'REPORTE DE ANTICIPOS');
        } elseif ($this->objParam->getParametro('formato_reporte') == 'REPORTE CONSOLIDADO (DEBE-HABER)') {
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,2,'REPORTE CONSOLIDADO (DEBE-HABER)');
        } elseif ($this->objParam->getParametro('formato_reporte') == 'RESUMEN CTA/CTE TOTALIZADO') {
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,2,'RESUMEN CTA/CTE TOTALIZADO');
        }


        //$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,5,'PERSONA NATURALES');

        if ($this->objParam->getParametro('formato_reporte') == 'RESUMEN CTA/CTE TOTALIZADO') {
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,3,'DEL: '.$this->objParam->getParametro('desde').' AL: '.$this->objParam->getParametro('hasta'));
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,4,'CODIGO AUXILIAR: '.$this->objParam->getParametro('codigo_auxiliar'));
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,5,'PUNTO DE VENTA: '.$this->objParam->getParametro('nombre_pv'));

          $this->docexcel->getActiveSheet()->mergeCells('A2:C2');
          $this->docexcel->getActiveSheet()->mergeCells('A3:C3');
          $this->docexcel->getActiveSheet()->mergeCells('A4:C4');
          $this->docexcel->getActiveSheet()->mergeCells('A5:C5');
          $this->docexcel->getActiveSheet()->getStyle('A1:C5')->applyFromArray($styleTituloPrincipal);
          $this->docexcel->getActiveSheet()->getStyle('A1:C5')->applyFromArray($styleFondoBlanco);
        } else {
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,3,'DEL: '.$this->objParam->getParametro('desde').' AL: '.$this->objParam->getParametro('hasta'));
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,4,'CODIGO AUXILIAR: '.$this->objParam->getParametro('codigo_auxiliar'));
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,5,'PUNTO DE VENTA: '.$this->objParam->getParametro('nombre_pv'));

          $this->docexcel->getActiveSheet()->mergeCells('C2:G2');
          $this->docexcel->getActiveSheet()->mergeCells('C3:G3');
          $this->docexcel->getActiveSheet()->mergeCells('C4:G4');
          $this->docexcel->getActiveSheet()->mergeCells('C5:G5');
          $this->docexcel->getActiveSheet()->getStyle('A1:G5')->applyFromArray($styleTituloPrincipal);
          $this->docexcel->getActiveSheet()->getStyle('A1:I5')->applyFromArray($styleFondoBlanco);
        }







        //*************************************Cabecera*****************************************
          if ($this->objParam->getParametro('formato_reporte') == 'RESUMEN CTA/CTE TOTALIZADO') {
            $this->docexcel->getActiveSheet()->getColumnDimension('A')->setWidth(80);
            $this->docexcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(45);
            $this->docexcel->getActiveSheet()->getColumnDimension('E')->setWidth(45);
            $this->docexcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $this->docexcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
            $this->docexcel->getActiveSheet()->getColumnDimension('H')->setWidth(100);
            $this->docexcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
            $this->docexcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
            $this->docexcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
            $this->docexcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
            $this->docexcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
          } else {
            $this->docexcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
            $this->docexcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(70);
            $this->docexcel->getActiveSheet()->getColumnDimension('E')->setWidth(45);
            $this->docexcel->getActiveSheet()->getColumnDimension('F')->setWidth(45);
            $this->docexcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
            $this->docexcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
            $this->docexcel->getActiveSheet()->getColumnDimension('I')->setWidth(100);
            $this->docexcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
            $this->docexcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
            $this->docexcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
            $this->docexcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
          }

        if ($this->objParam->getParametro('formato_reporte') == 'RESUMEN CTA/CTE TOTALIZADO') {
          $this->docexcel->getActiveSheet()->setCellValue('A6','Cuenta Corriente');
          $this->docexcel->getActiveSheet()->setCellValue('B6','Importe Debe');
          $this->docexcel->getActiveSheet()->setCellValue('C6','Importe Debe');

          if ($this->objParam->getParametro('nombre_pv') == 'Todos') {
            $this->docexcel->getActiveSheet()->getStyle('A6:C6')->applyFromArray($styleSubtitulos);
          } else {
            $this->docexcel->getActiveSheet()->setCellValue('D6','Punto de Venta');
            $this->docexcel->getActiveSheet()->getStyle('A6:D6')->applyFromArray($styleSubtitulos);
          }




          $this->docexcel->getActiveSheet()->freezePaneByColumnAndRow(0,7);

        } else {
          $this->docexcel->getActiveSheet()->setCellValue('A6','Fecha');
          $this->docexcel->getActiveSheet()->setCellValue('B6','Nro. Factura');
          $this->docexcel->getActiveSheet()->setCellValue('C6','Nro. Documento.');
          $this->docexcel->getActiveSheet()->setCellValue('D6','Observaciones');
          $this->docexcel->getActiveSheet()->setCellValue('E6','Rutas');

          $this->docexcel->getActiveSheet()->setCellValue('F6','Pasajero');
          $this->docexcel->getActiveSheet()->setCellValue('G6','Importe Debe');
          $this->docexcel->getActiveSheet()->setCellValue('H6','Importe Haber');
          $this->docexcel->getActiveSheet()->setCellValue('I6','Cuenta Corriente');
          $this->docexcel->getActiveSheet()->getStyle('A6:I6')->applyFromArray($styleSubtitulos);
          $this->docexcel->getActiveSheet()->freezePaneByColumnAndRow(0,7);
        }









    }

    function generarDatos(){
        $this->imprimeCabecera();
        $fila = 7;

        $style_datos = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        );

        $style_haber = array(
          'fill' => array(
              'type' => PHPExcel_Style_Fill::FILL_SOLID,
              'color' => array(
                  'rgb' => '319DFD'
              )
          ),
          'alignment' => array(
              'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
              'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
          ),
          'font'  => array(
              'bold'  => true,
              'size'  => 11,
              'name'  => 'Calibri',
          )
        );

        $style_depositos = array(
          'fill' => array(
              'type' => PHPExcel_Style_Fill::FILL_SOLID,
              'color' => array(
                  'rgb' => 'FF6060'
              )
          ),
          'alignment' => array(
              'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
              'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
          ),
          'font'  => array(
              'bold'  => true,
              'size'  => 11,
              'name'  => 'Calibri',
          )
        );

        $style_subtotal = array(
          'fill' => array(
              'type' => PHPExcel_Style_Fill::FILL_SOLID,
              'color' => array(
                  'rgb' => 'F4FD31'
              )
          ),
          'alignment' => array(
              'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
              'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
          ),
          'font'  => array(
              'bold'  => true,
              'size'  => 11,
              'name'  => 'Calibri',
          )
        );

        $styleFondoBlanco = array(
          'fill' => array(
              'type' => PHPExcel_Style_Fill::FILL_SOLID,
              'color' => array(
                  'rgb' => 'FFFFFF'
              )
          ),
          'borders' => array(
              'allborders' => array(
                  'style' => PHPExcel_Style_Border::BORDER_THIN
              )
          )
        );

        $styleTotales = array(
          'fill' => array(
              'type' => PHPExcel_Style_Fill::FILL_SOLID,
              'color' => array(
                  'rgb' => '92E176'
              )
          ),
          'borders' => array(
              'allborders' => array(
                  'style' => PHPExcel_Style_Border::BORDER_THIN
              )
          ),
          'alignment' => array(
              'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
              'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
          ),
          'font'  => array(
              'bold'  => true,
              'size'  => 13,
              'name'  => 'Calibri',
          )
        );

        $styleDiferencia = array(
          'fill' => array(
              'type' => PHPExcel_Style_Fill::FILL_SOLID,
              'color' => array(
                  'rgb' => '63EEAD'
              )
          ),
          'borders' => array(
              'allborders' => array(
                  'style' => PHPExcel_Style_Border::BORDER_THIN
              )
          ),
          'alignment' => array(
              'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
              'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
          ),
          'font'  => array(
              'bold'  => true,
              'size'  => 13,
              'name'  => 'Calibri',
          )
        );

        $styleDeudor = array(
          'fill' => array(
              'type' => PHPExcel_Style_Fill::FILL_SOLID,
              'color' => array(
                  'rgb' => 'FFCFCF'
              )
          ),
          'borders' => array(
              'allborders' => array(
                  'style' => PHPExcel_Style_Border::BORDER_THIN
              )
          ),
          'alignment' => array(
              'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
              'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
          ),
          'font'  => array(
              'bold'  => true,
              'size'  => 15,
              'name'  => 'Calibri',
              'color' => array(
                  'rgb' => 'FF0000'
              )
          )
        );

        $styleAcreedor = array(
          'fill' => array(
              'type' => PHPExcel_Style_Fill::FILL_SOLID,
              'color' => array(
                  'rgb' => 'D1FEA6'
              )
          ),
          'borders' => array(
              'allborders' => array(
                  'style' => PHPExcel_Style_Border::BORDER_THIN
              )
          ),
          'alignment' => array(
              'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
              'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
          ),
          'font'  => array(
              'bold'  => true,
              'size'  => 15,
              'name'  => 'Calibri',
              'color' => array(
                  'rgb' => '58B600'
              )
          )
        );

        $datos = $this->datos_contenido->datos;
        //var_dump("aqui el value 1111",$this->datos_contenido->datos);

        if ($this->objParam->getParametro('formato_reporte') == 'RESUMEN CTA/CTE TOTALIZADO') {

          if ($this->objParam->getParametro('nombre_pv') == 'Todos') {

            foreach ($datos as $value) {
              // var_dump("aqui el value",$value);
              // var_dump("aqui el value",$value[0]);
              $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $value['cuenta_auxiliar']);
              $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $value['debe']);
              $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $value['haber']);

              $this->docexcel->getActiveSheet()->getStyle("B$fila:C$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);


              $fila++;
            }
            $total_sum = $this->datos_contenido->extraData;

            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, 'TOTAL GENERAL: ');
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $total_sum['total_debe']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $total_sum['total_haber']);

            $this->docexcel->getActiveSheet()->getStyle("B$fila:C$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
            $this->docexcel->getActiveSheet()->getStyle("A$fila:C$fila")->applyFromArray($styleTotales);

          } else {

            foreach ($datos as $value) {
              // var_dump("aqui el value",$value);
              // var_dump("aqui el value",$value[0]);
              $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $value['cuenta_auxiliar']);
              $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $value['debe']);
              $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $value['haber']);
              $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $value['punto_venta']);

              $this->docexcel->getActiveSheet()->getStyle("B$fila:C$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);


              $fila++;
            }
            $total_sum = $this->datos_contenido->extraData;

            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, 'TOTAL GENERAL: ');
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $total_sum['total_debe']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $total_sum['total_haber']);

            $this->docexcel->getActiveSheet()->getStyle("B$fila:C$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
            $this->docexcel->getActiveSheet()->getStyle("A$fila:D$fila")->applyFromArray($styleTotales);

          }



        } else {

          foreach ($datos as $value) {
            // var_dump("aqui el value",$value);
            // var_dump("aqui el value",$value[0]);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, date("d/m/Y", strtotime($value['fecha_factura'])));
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $value['nro_factura']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $value['nro_documento']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, rtrim(ltrim($value['observaciones'])));
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $value['ruta']);

            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $value['debe']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, $value['haber']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, $value['cuenta_auxiliar']);
            $this->docexcel->getActiveSheet()->getStyle("A$fila:C$fila")->applyFromArray($style_datos);


            if ($value['tipo_factura'] == null && $value['pasajero'] != 'DEPOSITO') {
              $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila,'PUNTO DE VENTA: '.$value['pasajero']);
              $this->docexcel->getActiveSheet()->getStyle("A$fila:I$fila")->applyFromArray($style_haber);
              $this->docexcel->getActiveSheet()->mergeCells("A$fila:I$fila");
            } else if ($value['tipo_factura'] == 'total_pv') {
              $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila,'TOTAL PUNTO DE VENTA:');
              $this->docexcel->getActiveSheet()->getStyle("A$fila:I$fila")->applyFromArray($style_subtotal);
              $this->docexcel->getActiveSheet()->mergeCells("A$fila:F$fila");
            } else if ($value['tipo_factura'] == null && $value['pasajero'] == 'DEPOSITO') {
              $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila,'DEPOSITOS:');
              $this->docexcel->getActiveSheet()->getStyle("A$fila:I$fila")->applyFromArray($style_depositos);
              $this->docexcel->getActiveSheet()->mergeCells("A$fila:F$fila");
              $this->docexcel->getActiveSheet()->mergeCells("A$fila:I$fila");
            }
            else {
              $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $value['pasajero']);
              $this->docexcel->getActiveSheet()->getStyle("A$fila:I$fila")->applyFromArray($styleFondoBlanco);
              $this->docexcel->getActiveSheet()->getStyle("A$fila:C$fila")->applyFromArray($style_datos);
            }








            $this->docexcel->getActiveSheet()->getStyle("G$fila:H$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);


            $fila++;
          }
          $total_sum = $this->datos_contenido->extraData;

          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, 'TOTAL GENERAL: ');
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $total_sum['total_debe']);
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, $total_sum['total_haber']);

          $this->docexcel->getActiveSheet()->getStyle("G$fila:H$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
          $this->docexcel->getActiveSheet()->getStyle("A$fila:I$fila")->applyFromArray($styleTotales);
          $this->docexcel->getActiveSheet()->mergeCells("A$fila:F$fila");

          $fila=$fila+1;
          $diferencia = ($total_sum['total_debe']-$total_sum['total_haber']);

          if ($total_sum['total_debe'] > $total_sum['total_haber']) {
            $diferencia = $diferencia;
          }else{
            $diferencia = ($diferencia*(-1));
          }



          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, '(TOTAL DEBE - TOTAL HABER): ');
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $diferencia);


          $this->docexcel->getActiveSheet()->getStyle("G$fila:H$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
          $this->docexcel->getActiveSheet()->getStyle("A$fila:I$fila")->applyFromArray($styleDiferencia);
          $this->docexcel->getActiveSheet()->mergeCells("A$fila:F$fila");
          $this->docexcel->getActiveSheet()->mergeCells("G$fila:H$fila");

          $fila=$fila+1;

          if ($total_sum['total_debe'] > $total_sum['total_haber']) {
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, 'SALDO DEUDOR');
            $this->docexcel->getActiveSheet()->getStyle("G$fila:H$fila")->applyFromArray($styleDeudor);
          } else {
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, 'SALDO ACREEDOR');
            $this->docexcel->getActiveSheet()->getStyle("G$fila:H$fila")->applyFromArray($styleAcreedor);
          }

            $this->docexcel->getActiveSheet()->mergeCells("G$fila:H$fila");

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
