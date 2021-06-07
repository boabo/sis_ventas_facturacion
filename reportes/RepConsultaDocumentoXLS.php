<?php
class RepConsultaDocumentoXLS
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

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,2,'REPORTE CONSULTA FACTURA RECIBO');
        // $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,3,'DEL: '.$this->objParam->getParametro('desde').' AL: '.$this->objParam->getParametro('hasta'));
        // $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,4,'CODIGO AUXILIAR: '.$this->objParam->getParametro('codigo_auxiliar'));
        // $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,5,'PUNTO DE VENTA: '.$this->objParam->getParametro('nombre_pv'));

        $this->docexcel->getActiveSheet()->mergeCells('C2:G2');
        $this->docexcel->getActiveSheet()->mergeCells('C3:G3');
        $this->docexcel->getActiveSheet()->mergeCells('C4:G4');
        $this->docexcel->getActiveSheet()->mergeCells('C5:G5');
        $this->docexcel->getActiveSheet()->getStyle('A1:G5')->applyFromArray($styleTituloPrincipal);
        $this->docexcel->getActiveSheet()->getStyle('A1:R5')->applyFromArray($styleFondoBlanco);


        //*************************************Cabecera*****************************************
          $this->docexcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
          $this->docexcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
          $this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
          $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(40);
          $this->docexcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
          $this->docexcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
          $this->docexcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
          $this->docexcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
          $this->docexcel->getActiveSheet()->getColumnDimension('I')->setWidth(40);
          $this->docexcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
          $this->docexcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
          $this->docexcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
          $this->docexcel->getActiveSheet()->getColumnDimension('M')->setWidth(50);
          $this->docexcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
          $this->docexcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
          $this->docexcel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
          $this->docexcel->getActiveSheet()->getColumnDimension('Q')->setWidth(20);
          $this->docexcel->getActiveSheet()->getColumnDimension('R')->setWidth(35);

          $this->docexcel->getActiveSheet()->setCellValue('A6','N°');
          $this->docexcel->getActiveSheet()->setCellValue('B6','N°. Documento');
          $this->docexcel->getActiveSheet()->setCellValue('C6','Nit');
          $this->docexcel->getActiveSheet()->setCellValue('D6','Razon Social');
          $this->docexcel->getActiveSheet()->setCellValue('E6','Codigo Control');

          $this->docexcel->getActiveSheet()->setCellValue('F6','Fecha Emision');
          $this->docexcel->getActiveSheet()->setCellValue('G6','Tipo Documento');
          $this->docexcel->getActiveSheet()->setCellValue('H6','Estado');
          $this->docexcel->getActiveSheet()->setCellValue('I6','Observaciones');
          $this->docexcel->getActiveSheet()->setCellValue('J6','N° Autorizacion');
          $this->docexcel->getActiveSheet()->setCellValue('K6','Total Venta');
          $this->docexcel->getActiveSheet()->setCellValue('L6','Excento');
          $this->docexcel->getActiveSheet()->setCellValue('M6','Punto de Venta');
          $this->docexcel->getActiveSheet()->setCellValue('N6','N° Boleto Asociado');
          $this->docexcel->getActiveSheet()->setCellValue('O6','N° Deposito');
          $this->docexcel->getActiveSheet()->setCellValue('P6','Monto Total Deposito');
          $this->docexcel->getActiveSheet()->setCellValue('Q6','Fecha Deposito');
          $this->docexcel->getActiveSheet()->setCellValue('R6','Usuario Emision');
          $this->docexcel->getActiveSheet()->getStyle('A6:R6')->applyFromArray($styleSubtitulos);
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

        $style_center = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        );

        $style_left = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
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


        $datos = $this->datos_contenido->datos;
        $count = 1;
          foreach ($datos as $value) {

            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $count);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $value['nro_factura']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $value['nit']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $value['nombre_factura']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $value['cod_control']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, date("d/m/Y", strtotime($value['fecha_factura'])));
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $value['tipo_factura']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, ($value['estado']=='finalizado')?'VALIDA':'ANULADO');
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, rtrim(ltrim($value['observaciones'])));
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $fila, $value['nroaut']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $fila, $value['total_venta']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $fila, $value['excento']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(12, $fila, $value['punto_venta']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(13, $fila, $value['nro_boleto']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(14, $fila, $value['nro_deposito']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(15, $fila, $value['monto_total']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(16, $fila, ($value['fecha_dep']=='' || $value['fecha_dep'] == null)?'':date("d/m/Y", strtotime($value['fecha_dep'])));
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(17, $fila, $value['desc_persona']);

            $this->docexcel->getActiveSheet()->getStyle("A$fila:C$fila")->applyFromArray($style_center);
            $this->docexcel->getActiveSheet()->getStyle("E$fila:H$fila")->applyFromArray($style_center);
            $this->docexcel->getActiveSheet()->getStyle("D$fila")->applyFromArray($style_left);
            $this->docexcel->getActiveSheet()->getStyle("I$fila")->applyFromArray($style_left);
            $this->docexcel->getActiveSheet()->getStyle("J$fila")->applyFromArray($style_center);
            $this->docexcel->getActiveSheet()->getStyle("k$fila:L$fila")->applyFromArray($style_datos);
            $this->docexcel->getActiveSheet()->getStyle("M$fila")->applyFromArray($style_left);
            $this->docexcel->getActiveSheet()->getStyle("N$fila:O$fila")->applyFromArray($style_center);
            $this->docexcel->getActiveSheet()->getStyle("P$fila")->applyFromArray($style_datos);
            $this->docexcel->getActiveSheet()->getStyle("R$fila")->applyFromArray($style_left);
            $this->docexcel->getActiveSheet()->getStyle("Q$fila")->applyFromArray($style_center);

            $this->docexcel->getActiveSheet()->getStyle("k$fila:L$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
            $this->docexcel->getActiveSheet()->getStyle("P$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);


            $fila++;
            $count++;
          }
          $inicio=7;
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, 'TOTALES: ');
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $fila, "=SUM((K$inicio:K$fila))");
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $fila, "=SUM((L$inicio:L$fila))");
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(15, $fila, "=SUM((P$inicio:P$fila))");

          $this->docexcel->getActiveSheet()->getStyle("K$fila:L$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
          $this->docexcel->getActiveSheet()->getStyle("P$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
          $this->docexcel->getActiveSheet()->getStyle("A$fila:R$fila")->applyFromArray($styleTotales);
          $this->docexcel->getActiveSheet()->mergeCells("A$fila:F$fila");
          $this->docexcel->getActiveSheet()->mergeCells("G$fila:J$fila");
          $this->docexcel->getActiveSheet()->mergeCells("M$fila:O$fila");
          $this->docexcel->getActiveSheet()->mergeCells("Q$fila:R$fila");
          $fila=$fila+1;

    }

    function generarReporte(){
        $this->generarDatos();
        $this->docexcel->setActiveSheetIndex(0);
        $this->objWriter = PHPExcel_IOFactory::createWriter($this->docexcel, 'Excel5');
        $this->objWriter->save($this->url_archivo);
    }

}
?>
