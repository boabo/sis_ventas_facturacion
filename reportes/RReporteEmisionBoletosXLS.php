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
        $this->docexcel->getActiveSheet()->setTitle('Naturales');
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
                    'rgb' => 'A3CFEF'
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




        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,2,'ESTADO DE CUENTA DE: PASAJES, FACTURAS, RECIBOS OFICIALES');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,3,'DEL: '.$this->objParam->getParametro('desde').' AL: '.$this->objParam->getParametro('desde'));
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,4,'CODIGO AUXILIAR: '.$this->objParam->getParametro('codigo_auxiliar'));
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,5,'PUNTO DE VENTA: '.$this->objParam->getParametro('nombre_pv'));
        //$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,5,'PERSONA NATURALES');
        $this->docexcel->getActiveSheet()->mergeCells('C2:F2');
        $this->docexcel->getActiveSheet()->mergeCells('C3:F3');
        $this->docexcel->getActiveSheet()->mergeCells('C4:F4');
        $this->docexcel->getActiveSheet()->mergeCells('C5:F5');
        $this->docexcel->getActiveSheet()->getStyle('A1:F5')->applyFromArray($styleTituloPrincipal);
        $this->docexcel->getActiveSheet()->getStyle('A1:I5')->applyFromArray($styleFondoBlanco);





        //*************************************Cabecera*****************************************

        $this->docexcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(45);
        $this->docexcel->getActiveSheet()->getColumnDimension('E')->setWidth(45);
        $this->docexcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);


        $this->docexcel->getActiveSheet()->setCellValue('A6','Fecha');
        $this->docexcel->getActiveSheet()->setCellValue('B6','Nro. Factura');
        $this->docexcel->getActiveSheet()->setCellValue('C6','Nro. Documento.');
        $this->docexcel->getActiveSheet()->setCellValue('D6','Rutas');

        $this->docexcel->getActiveSheet()->setCellValue('E6','Pasajero');
        $this->docexcel->getActiveSheet()->setCellValue('F6','Importe Debe');
        $this->docexcel->getActiveSheet()->setCellValue('G6','Importe Haber');


        $this->docexcel->getActiveSheet()->getStyle('A6:G6')->applyFromArray($styleSubtitulos);
        $this->docexcel->getActiveSheet()->freezePaneByColumnAndRow(0,7);




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
                  'rgb' => 'FFC318'
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
                  'rgb' => 'EEE563'
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
              'size'  => 11,
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
        foreach ($datos as $value) {
          // var_dump("aqui el value",$value);
          // var_dump("aqui el value",$value[0]);
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $value['fecha_factura']);
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $value['nro_factura']);
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $value['nro_documento']);
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $value['ruta']);

          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $value['debe']);
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $value['haber']);
          $this->docexcel->getActiveSheet()->getStyle("A$fila:C$fila")->applyFromArray($style_datos);


          if ($value['tipo_factura'] == null) {
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila,'PUNTO DE VENTA: '.$value['pasajero']);
            $this->docexcel->getActiveSheet()->getStyle("A$fila:G$fila")->applyFromArray($style_haber);
            $this->docexcel->getActiveSheet()->mergeCells("A$fila:G$fila");
          } else if ($value['tipo_factura'] == 'total_pv') {
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila,'TOTAL PUNTO DE VENTA:');
            $this->docexcel->getActiveSheet()->getStyle("A$fila:G$fila")->applyFromArray($style_subtotal);
            $this->docexcel->getActiveSheet()->mergeCells("A$fila:E$fila");
          }
          else {
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $value['pasajero']);
            $this->docexcel->getActiveSheet()->getStyle("A$fila:G$fila")->applyFromArray($styleFondoBlanco);
            $this->docexcel->getActiveSheet()->getStyle("A$fila:C$fila")->applyFromArray($style_datos);
          }








          $this->docexcel->getActiveSheet()->getStyle("F$fila:G$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);


          $fila++;
        }
        $total_sum = $this->datos_contenido->extraData;

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, 'TOTAL GENERAL: ');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $total_sum['total_debe']);
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $total_sum['total_haber']);

        $this->docexcel->getActiveSheet()->getStyle("F$fila:G$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
        $this->docexcel->getActiveSheet()->getStyle("A$fila:G$fila")->applyFromArray($styleTotales);
        $this->docexcel->getActiveSheet()->mergeCells("A$fila:E$fila");

        $fila=$fila+1;
        $diferencia = ($total_sum['total_debe']-$total_sum['total_haber']);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, '(TOTAL DEBE - TOTAL HABER): ');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $diferencia);


        $this->docexcel->getActiveSheet()->getStyle("F$fila:G$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
        $this->docexcel->getActiveSheet()->getStyle("A$fila:G$fila")->applyFromArray($styleDiferencia);
        $this->docexcel->getActiveSheet()->mergeCells("A$fila:E$fila");
        $this->docexcel->getActiveSheet()->mergeCells("F$fila:G$fila");

        $fila=$fila+1;

        if ($total_sum['total_debe'] > $total_sum['total_haber']) {
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, 'SALDO DEUDOR');
          $this->docexcel->getActiveSheet()->getStyle("F$fila:G$fila")->applyFromArray($styleDeudor);
        } else {
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, 'SALDO ACREEDOR');
          $this->docexcel->getActiveSheet()->getStyle("F$fila:G$fila")->applyFromArray($styleAcreedor);
        }

          $this->docexcel->getActiveSheet()->mergeCells("F$fila:G$fila");


    }

    function generarReporte(){
        $this->generarDatos();
        $this->docexcel->setActiveSheetIndex(0);
        $this->objWriter = PHPExcel_IOFactory::createWriter($this->docexcel, 'Excel5');
        $this->objWriter->save($this->url_archivo);
    }

}
?>
