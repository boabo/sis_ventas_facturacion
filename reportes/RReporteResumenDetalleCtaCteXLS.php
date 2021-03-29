<?php
class RReporteResumenDetalleCtaCteXLS
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
        set_time_limit(6000);
        $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $cacheSettings = array('memoryCacheSize'  => '50MB');
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
  function datosHeader ($contenido,$resumen) {
        $this->datos_contenido = $contenido;
        $this->datos_resumen = $resumen;
    }
    function imprimeCabecera() {
        // $this->docexcel->createSheet();
        $this->docexcel->setActiveSheetIndex(0);
        $this->docexcel->getActiveSheet()->setTitle('Reporte Venta Propia');

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



        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,2,'RESUMEN CTA. CTE. DE VENTAS');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,3,'DEL: '.$this->objParam->getParametro('desde').' AL: '.$this->objParam->getParametro('hasta'));
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,4,'CODIGO AUXILIAR: '.$this->objParam->getParametro('codigo_auxiliar'));


        $this->docexcel->getActiveSheet()->mergeCells('C2:G2');
        $this->docexcel->getActiveSheet()->mergeCells('C3:G3');
        $this->docexcel->getActiveSheet()->mergeCells('C4:G4');
        $this->docexcel->getActiveSheet()->mergeCells('C5:G5');
        $this->docexcel->getActiveSheet()->getStyle('A1:G5')->applyFromArray($styleTituloPrincipal);
        $this->docexcel->getActiveSheet()->getStyle('A1:I5')->applyFromArray($styleFondoBlanco);





        //*************************************Cabecera*****************************************

        $this->docexcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
        $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
        $this->docexcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
        $this->docexcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
        $this->docexcel->getActiveSheet()->getColumnDimension('G')->setWidth(35);
        $this->docexcel->getActiveSheet()->getColumnDimension('H')->setWidth(100);
        $this->docexcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);


        $this->docexcel->getActiveSheet()->setCellValue('A6','Mes');
        $this->docexcel->getActiveSheet()->setCellValue('B6','Debe');
        $this->docexcel->getActiveSheet()->setCellValue('B7','Monto Facturado S/G BOA en  Bs');
        $this->docexcel->getActiveSheet()->setCellValue('C6','Haber');
        $this->docexcel->getActiveSheet()->setCellValue('C7','Depósito');
        $this->docexcel->getActiveSheet()->setCellValue('C8','Fecha');
        $this->docexcel->getActiveSheet()->setCellValue('D8','N° Comprobante');
        $this->docexcel->getActiveSheet()->setCellValue('E8','Importe');
        $this->docexcel->getActiveSheet()->setCellValue('F6','Saldo');

        $this->docexcel->getActiveSheet()->setCellValue('G6','Observaciones');

        $this->docexcel->getActiveSheet()->getStyle('A6:G8')->applyFromArray($styleSubtitulos);
        $this->docexcel->getActiveSheet()->getStyle('A6:G8')->getAlignment()->setWrapText(true);

        $this->docexcel->getActiveSheet()->mergeCells('A6:A8');
        $this->docexcel->getActiveSheet()->mergeCells('B7:B8');
        $this->docexcel->getActiveSheet()->mergeCells('F6:F8');
        $this->docexcel->getActiveSheet()->mergeCells('G6:G8');
        $this->docexcel->getActiveSheet()->mergeCells('C6:E6');
        $this->docexcel->getActiveSheet()->mergeCells('C7:E7');



        $this->docexcel->getActiveSheet()->freezePaneByColumnAndRow(0,9);




    }

    function generarDatos(){
        $this->imprimeCabecera();
        $fila = 10;

        $style_datos = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        );

        $style_anticipos = array(
          'fill' => array(
              'type' => PHPExcel_Style_Fill::FILL_SOLID,
              'color' => array(
                  'rgb' => '9DFF65'
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

        $style_depositos = array(
          'fill' => array(
              'type' => PHPExcel_Style_Fill::FILL_SOLID,
              'color' => array(
                  'rgb' => '319DFD'
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
                  'rgb' => 'FFEF2A'
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

        $resumen = $this->datos_resumen->datos;
        $datos = $this->datos_contenido->datos;

      //var_dump("aqui resumen",$resumen);
        $this->docexcel->setActiveSheetIndex(0);
        //var_dump("aqui el value 1111",$this->datos_contenido->datos);
        foreach ($resumen as $value) {


          if ($value['tipo'] == 'gastos') {
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $value['mes']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $value['total_debe']);
            //$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, date("d/m/Y", strtotime($value['fecha_factura'])));

          } else {
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, date("d/m/Y", strtotime($value['fecha_factura'])));
            //$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $value['total_debe']);
          }


          //$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, date("d/m/Y", strtotime($value['fecha_factura'])));
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $value['nro_factura']);
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $value['total_haber']);

          $this->docexcel->getActiveSheet()->getStyle("A$fila:G$fila")->applyFromArray($styleFondoBlanco);
          //$this->docexcel->getActiveSheet()->getStyle("A$fila:G$fila")->applyFromArray($style_datos);
          $this->docexcel->getActiveSheet()->getStyle("B$fila:B$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
          $this->docexcel->getActiveSheet()->getStyle("E$fila:E$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

          $saldo = ($fila-1);

          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, "=SUM((F$saldo+B$fila-E$fila))");
          $this->docexcel->getActiveSheet()->getStyle("E$fila:G$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

          $fila++;
        }

        $saldo = ($fila + 1);
        $inicial = 9;
        $final = ($fila-1);
        /*Aqui los totales y el saldo*/
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, 'TOTAL: ');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, "=SUM(B$inicial:B$final)");
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, "=SUM(E$inicial:E$final)");
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, "=SUM(F$inicial:F$final)");

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $saldo, 'SALDO: ');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $saldo, "=SUM(B$fila-E$fila)");

        $this->docexcel->getActiveSheet()->getStyle("B$fila:F$saldo")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
        $this->docexcel->getActiveSheet()->getStyle("A$fila:G$saldo")->applyFromArray($styleTotales);




          $sheet = 1;
          $fecha = '';

          $this->docexcel->createSheet();
          $this->docexcel->setActiveSheetIndex($sheet);
          $this->docexcel->getActiveSheet()->setTitle('RESUMEN');

          $inicio = 7;
          $fila = 8;


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

          $styleFondoBlanco2 = array(
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



          //$this->docexcel->getActiveSheet()->mergeCells('A1:C1');



          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,2,'DETALLE CTA. CTE. DE VENTAS');
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,3,'DEL: '.$this->objParam->getParametro('desde').' AL: '.$this->objParam->getParametro('hasta'));
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,4,'CODIGO AUXILIAR: '.$this->objParam->getParametro('codigo_auxiliar'));


          $this->docexcel->getActiveSheet()->mergeCells('C2:G2');
          $this->docexcel->getActiveSheet()->mergeCells('C3:G3');
          $this->docexcel->getActiveSheet()->mergeCells('C4:G4');
          $this->docexcel->getActiveSheet()->mergeCells('C5:G5');
          $this->docexcel->getActiveSheet()->getStyle('A1:G5')->applyFromArray($styleTituloPrincipal);
          $this->docexcel->getActiveSheet()->getStyle('A1:I5')->applyFromArray($styleFondoBlanco);





          //*************************************Cabecera*****************************************

          $this->docexcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
          $this->docexcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
          $this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
          $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(45);
          $this->docexcel->getActiveSheet()->getColumnDimension('E')->setWidth(45);
          $this->docexcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
          $this->docexcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
          $this->docexcel->getActiveSheet()->getColumnDimension('H')->setWidth(50);
          $this->docexcel->getActiveSheet()->getColumnDimension('I')->setWidth(50);
          $this->docexcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
          $this->docexcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
          $this->docexcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
          $this->docexcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);


          $this->docexcel->getActiveSheet()->setCellValue('A6','Fecha');
          $this->docexcel->getActiveSheet()->setCellValue('B6','Nro. Boleto/Factura');
          $this->docexcel->getActiveSheet()->setCellValue('C6','Nombre');
          $this->docexcel->getActiveSheet()->setCellValue('D6','Ruta');

          $this->docexcel->getActiveSheet()->setCellValue('E6','Debe');
          $this->docexcel->getActiveSheet()->setCellValue('F6','Haber');
          $this->docexcel->getActiveSheet()->setCellValue('G6','Saldo');
          $this->docexcel->getActiveSheet()->setCellValue('H6','Observaciones');
          $this->docexcel->getActiveSheet()->setCellValue('I6','Punto Venta');


          $this->docexcel->getActiveSheet()->getStyle('A6:I6')->applyFromArray($styleSubtitulos);
          $this->docexcel->getActiveSheet()->freezePaneByColumnAndRow(0,7);

          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $inicio, 0);
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $inicio, 0);
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $inicio, 0);

          $this->docexcel->getActiveSheet()->getStyle("E$inicio:G$inicio")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);


          foreach($datos as $value2) {

            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, date("d/m/Y", strtotime($value2['fecha_factura'])));
            //$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $value2['nro_factura']);
            //$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, ' ');

            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $value2['ruta']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $value2['debe']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $value2['haber']);
            //$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, ' ');
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, $value2['punto_venta']);
            $this->docexcel->getActiveSheet()->getStyle("A$fila:I$fila")->applyFromArray($style_datos);


            if ($value2['tipo_factura'] == 'deposito') {
              $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila,'N° DE DEPÓSITO: '.$value2['nro_factura']);
              $this->docexcel->getActiveSheet()->getStyle("A$fila:I$fila")->applyFromArray($style_depositos);
              $this->docexcel->getActiveSheet()->mergeCells("B$fila:D$fila");
            } elseif ($value2['tipo_factura'] == 'anticipo') {
              $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila,$value2['nro_factura']);
              $this->docexcel->getActiveSheet()->getStyle("A$fila:I$fila")->applyFromArray($style_anticipos);
            } else {
              $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $value2['nro_factura']);
              $this->docexcel->getActiveSheet()->getStyle("A$fila:I$fila")->applyFromArray($styleFondoBlanco2);
              $this->docexcel->getActiveSheet()->getStyle("A$fila:I$fila")->applyFromArray($style_datos);
            }

            $saldo = ($fila-1);

            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, "=SUM((G$saldo+E$fila-F$fila))");
            $this->docexcel->getActiveSheet()->getStyle("E$fila:G$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

            $fila++;


          }


          $this->docexcel->setActiveSheetIndex(0);


    }

    function generarReporte(){
        $this->generarDatos();
        //$this->docexcel->setActiveSheetIndex(0);
        $this->objWriter = PHPExcel_IOFactory::createWriter($this->docexcel, 'Excel5');
        $this->objWriter->save($this->url_archivo);
    }

}
?>
