<?php
class RReporteDosificacionesXLS
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
        $this->docexcel->getActiveSheet()->setTitle('Dosificaciones');
        $this->docexcel->setActiveSheetIndex(0);

        $styleTituloPrincipal = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 14,
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


        if ($this->objParam->getParametro('estado_dosificacion') == 'Todas') {
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,2,'REPORTE TODAS LAS DOSIFICACIONES');
        } elseif ($this->objParam->getParametro('estado_dosificacion') == 'Vigentes') {
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,2,'REPORTE DOSIFICACIONES VIGENTES');
        } elseif ($this->objParam->getParametro('estado_dosificacion') == 'Vencidas') {
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,2,'REPORTE DOSIFICACIONES VENCIDAS');
        }

        if ($this->objParam->getParametro('tipo_generacion') == 'manual') {
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,3,'DOSIFICACIONES FACTURAS MANUALES');
        } elseif ($this->objParam->getParametro('tipo_generacion') == 'computarizada') {
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,3,'DOSIFICACIONES FACTURAS COMPUTARIZADAS');
        }



        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,4,'SUCURSAL: '.$this->objParam->getParametro('nombre_sucursal'));

        if ($this->objParam->getParametro('nombre_sistema') == 'SISTEMAFACTURACIONBOA') {
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,5,'SISTEMA DE FACTURACIÓN BOA');

        } else if ($this->objParam->getParametro('nombre_sistema') == 'SISTEMA FACTURACION NCD') {
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,5,'SISTEMA DE FACTURACIÓN NOTAS DE DÉBITO/CRÉDITO');

        } else if ($this->objParam->getParametro('nombre_sistema') == 'BOA CARGO') {
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,5,'SISTEMA DE FACTURACIÓN CARGA');

        }
        //$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,5,'PERSONA NATURALES');
        $this->docexcel->getActiveSheet()->mergeCells('C2:G2');
        $this->docexcel->getActiveSheet()->mergeCells('C3:G3');
        $this->docexcel->getActiveSheet()->mergeCells('C4:G4');
        $this->docexcel->getActiveSheet()->mergeCells('C5:G5');
        $this->docexcel->getActiveSheet()->getStyle('A1:G5')->applyFromArray($styleTituloPrincipal);
        $this->docexcel->getActiveSheet()->getStyle('A1:J5')->applyFromArray($styleFondoBlanco);





        //*************************************Cabecera*****************************************
          $this->docexcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
          $this->docexcel->getActiveSheet()->getColumnDimension('B')->setWidth(70);
          $this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
          $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(40);
          $this->docexcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
          $this->docexcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
          $this->docexcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
          $this->docexcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
          $this->docexcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
          $this->docexcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
          $this->docexcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
          $this->docexcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
          $this->docexcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);

          $this->docexcel->getActiveSheet()->setCellValue('A6','ESTACIÓN');
          $this->docexcel->getActiveSheet()->setCellValue('B6','ACTIVIDAD ECONÓMICA');
          $this->docexcel->getActiveSheet()->setCellValue('C6','NRO. AUTORIZACIÓN');
          $this->docexcel->getActiveSheet()->setCellValue('D6','NRO. TRÁMITE');

          $this->docexcel->getActiveSheet()->setCellValue('E6','SISTEMA FACTURACIÓN');
          $this->docexcel->getActiveSheet()->setCellValue('F6','NRO. INICIAL');
          $this->docexcel->getActiveSheet()->setCellValue('G6','NRO. FINAL');
          $this->docexcel->getActiveSheet()->setCellValue('H6','FECHA DOSIFICACIÓN');
          $this->docexcel->getActiveSheet()->setCellValue('I6','FECHA LIMITE EMISIÓN');
          $this->docexcel->getActiveSheet()->setCellValue('J6','DIAS RESTANTES');
          $this->docexcel->getActiveSheet()->getStyle('A6:J6')->applyFromArray($styleSubtitulos);

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
                  'rgb' => 'F7FF54'
              )
          ),
          'alignment' => array(
              'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
              'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
          ),
          'font'  => array(
              'bold'  => true,
              'size'  => 11,
              'name'  => 'Calibri',
          )
        );

        $style_vencidos = array(
          'fill' => array(
              'type' => PHPExcel_Style_Fill::FILL_SOLID,
              'color' => array(
                  'rgb' => 'FF8080'
              )
          ),
          'borders' => array(
              'allborders' => array(
                  'style' => PHPExcel_Style_Border::BORDER_THIN
              )
          )
          // 'alignment' => array(
          //     'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
          //     'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
          // ),
          // 'font'  => array(
          //     'bold'  => true,
          //     'size'  => 11,
          //     'name'  => 'Calibri',
          // )
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
        foreach ($datos as $value) {

                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $value['estacion']);
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $value['desc_actividad_economica']);
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $value['nro_autorizacion']);
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $value['nro_tramite']);
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $value['nombre_sistema']);
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $value['nro_inicial']);
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $value['nro_final']);
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, date("d/m/Y", strtotime($value['fecha_dosificacion'])));
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, date("d/m/Y", strtotime($value['fecha_limite'])));
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $fila, $value['dias_restante']);
                $this->docexcel->getActiveSheet()->getStyle("A$fila:C$fila")->applyFromArray($style_datos);
                if ($value['estacion'] == 'cabecera') {
                  $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila,'SUCURSAL: '.$value['desc_sucursal']);
                  $this->docexcel->getActiveSheet()->getStyle("A$fila:J$fila")->applyFromArray($style_haber);
                  $this->docexcel->getActiveSheet()->mergeCells("A$fila:J$fila");
                } else if ($value['dias_restante'] == 0) {
                  $this->docexcel->getActiveSheet()->getStyle("A$fila:J$fila")->applyFromArray($style_vencidos);
                  } else {
                  $this->docexcel->getActiveSheet()->getStyle("A$fila:J$fila")->applyFromArray($styleFondoBlanco);
                  $this->docexcel->getActiveSheet()->getStyle("A$fila:C$fila")->applyFromArray($style_datos);
                }
                $this->docexcel->getActiveSheet()->getStyle("D$fila:H$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
                $this->docexcel->getActiveSheet()->getStyle("L$fila:L$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
                $fila++;
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
