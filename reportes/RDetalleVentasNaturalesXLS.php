<?php
class RDetalleVentasNaturalesXLS
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
        ini_set('memory_limit','2G');
        set_time_limit(1800);
        $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $cacheSettings = array('memoryCacheSize'  => '64MB');
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
                'size'  => 12,
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
                    'rgb' => '5B9BD5'
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


        $NIT = 	$this->datos_contenido[0]['nit_empresa'];

        if( $this->objParam->getParametro('filtro_sql') == 'periodo' ){
            $sufijo = ($this->datos_contenido[0]['periodo_num']<10?'0'.$this->datos_contenido[0]['periodo_num']:$this->datos_contenido[0]['periodo_num']).$this->datos_contenido[0]['gestion'];
        }else{
            $sufijo=$this->objParam->getParametro('fecha_ini').'_'.$this->objParam->getParametro('fecha_fin');
        }


        if ($this->datos_contenido[0]['periodo_literal_inicio'] == $this->datos_contenido[0]['periodo_literal_fin']) {
          $gestion = 'GESTIÓN: '.$this->datos_contenido[0]['gestion'].' MES: '.$this->datos_contenido[0]['periodo_literal_inicio'];
          $mes_envio = 'MES DE ENVIO AL SIN: '.$this->datos_contenido[0]['periodo_literal_inicio'];
        } else {
          $gestion = 'GESTIÓN: '.$this->datos_contenido[0]['gestion'].' DESDE MES: '.$this->datos_contenido[0]['periodo_literal_inicio'].' HASTA MES: '.$this->datos_contenido[0]['periodo_literal_fin'];
          $mes_envio = 'ENVIO AL SIN DESDE: '.$this->datos_contenido[0]['periodo_literal_inicio'].' HASTA: '.$this->datos_contenido[0]['periodo_literal_fin'];

        }
        $fecha_actual = date("d/m/y");
        $hora_actual = date('H:i:s');


        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,1,'VENTA DE BIENES Y/O SERVICIOS A SUJETOS PASIVOS DEL REGIMEN TRIBUTARIO SIMPLIFICADO A PERSONAS NO INSCRITAS EN EL PADRON NACIONAL DE CONTRIBUYENTES' );
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5,2,$gestion);
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5,3,$mes_envio);
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,4,'DEPTO. DE FINANZAS');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5,4,'REGIMEN TRIBUTARIO PERSONAS NATURALES');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8,4,'FECHA: '.$fecha_actual.'   HORA: '.$hora_actual);




        $this->docexcel->getActiveSheet()->mergeCells('C1:J1');
        $this->docexcel->getActiveSheet()->mergeCells('C4:D4');
        $this->docexcel->getActiveSheet()->mergeCells('I4:J4');
        $this->docexcel->getActiveSheet()->mergeCells('F4:G4');
        $this->docexcel->getActiveSheet()->mergeCells('F2:G2');
        $this->docexcel->getActiveSheet()->mergeCells('F3:G3');
        $this->docexcel->getActiveSheet()->getStyle('C1:I5')->applyFromArray($styleTituloPrincipal);
        $this->docexcel->getActiveSheet()->getStyle('A1:L5')->applyFromArray($styleFondoBlanco);


        //*************************************Cabecera*****************************************

        $this->docexcel->getActiveSheet()->getColumnDimension('A')->setWidth(9);
        $this->docexcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(16);
        $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(16);
        $this->docexcel->getActiveSheet()->getColumnDimension('E')->setWidth(16);
        $this->docexcel->getActiveSheet()->getColumnDimension('F')->setWidth(16);
        $this->docexcel->getActiveSheet()->getColumnDimension('G')->setWidth(50);
        $this->docexcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('I')->setWidth(32);
        $this->docexcel->getActiveSheet()->getColumnDimension('J')->setWidth(12);
        $this->docexcel->getActiveSheet()->getColumnDimension('K')->setWidth(17);
        $this->docexcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('M')->setWidth(17);


        $this->docexcel->getActiveSheet()->setCellValue('A6','NRO');
        $this->docexcel->getActiveSheet()->setCellValue('B6','FECHA DE EMISIÓN BILLETE O FACTURA');
        $this->docexcel->getActiveSheet()->setCellValue('C6','NRO DE BILLETE O FACTURA');
        $this->docexcel->getActiveSheet()->setCellValue('D6','CODIGO CLIENTE');

        $this->docexcel->getActiveSheet()->setCellValue('E6','TIPO DE DOCUMENTO DE IDENTIFICACIÓN');
        $this->docexcel->getActiveSheet()->setCellValue('F6','NRO DE DOCUMENTO DE IDENTIFICACIÓN');
        $this->docexcel->getActiveSheet()->setCellValue('G6','NOMBRES Y APELLIDOS DEL CLIENTE');
        $this->docexcel->getActiveSheet()->setCellValue('H6','CÓDIGO DEL PRODUCTO O SERVICIO');
        $this->docexcel->getActiveSheet()->setCellValue('I6','DESCRIPCIÓN DEL PRODUCTO O SERVICIO');
        $this->docexcel->getActiveSheet()->setCellValue('J6','CANTIDAD DEL PRODUCTO O SERVICIO VENDIDO');
        $this->docexcel->getActiveSheet()->setCellValue('K6','PRECIO UNITARIO EN (BS)');
        $this->docexcel->getActiveSheet()->setCellValue('L6','PRECIO TOTAL EN (BS)');


        $this->docexcel->getActiveSheet()->getStyle('A6:L6')->applyFromArray($styleSubtitulos);
        $this->docexcel->getActiveSheet()->getStyle('A6:L6')->getAlignment()->setWrapText(true);

        $this->docexcel->getActiveSheet()->freezePaneByColumnAndRow(0,7);




    }

    function generarDatos(){
        $this->imprimeCabecera();

        $styleCabeceraNit = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 12,
                'name'  => 'Calibri',
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => 'F5C332'
                )
            ),
            // 'borders' => array(
            //     'allborders' => array(
            //         'style' => PHPExcel_Style_Border::BORDER_THIN
            //     )
            // )
        );

        $style_numeros = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );

        $style_datos = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
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

        $styleTotales = array(
          'fill' => array(
              'type' => PHPExcel_Style_Fill::FILL_SOLID,
              'color' => array(
                  'rgb' => '2395DA'
              )
          ),
          'font'  => array(
              'bold'  => true,
              'size'  => 13,
              'name'  => 'Calibri',
          ),
          'alignment' => array(
              'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
              'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
          ),
          'borders' => array(
              'allborders' => array(
                  'style' => PHPExcel_Style_Border::BORDER_THIN
              )
          )
        );

        $styleTotalesGenerales = array(
          'fill' => array(
              'type' => PHPExcel_Style_Fill::FILL_SOLID,
              'color' => array(
                  'rgb' => 'F06200'
              )
          ),
          'font'  => array(
              'bold'  => true,
              'size'  => 13,
              'name'  => 'Calibri',
          ),
          'alignment' => array(
              'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
              'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
          ),
          'borders' => array(
              'allborders' => array(
                  'style' => PHPExcel_Style_Border::BORDER_THIN
              )
          )
        );

        $styleTotales1 = array(
          'fill' => array(
              'type' => PHPExcel_Style_Fill::FILL_SOLID,
              'color' => array(
                  'rgb' => '35DEEF'
              )
          ),
        );

        $styleTotales2 = array(
          'fill' => array(
              'type' => PHPExcel_Style_Fill::FILL_SOLID,
              'color' => array(
                  'rgb' => '35DEEF'
              )
          ),
        );

        $datos = $this->datos_contenido;

        $nit_cliente=array();
        $totales = 0;
        $posicion_cabecera = 7;
        $fila = 7;
        $numero = 1;


        foreach ($datos as $value) {

          if ($value['razon_social'] == 'cabecera') {
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, 'NIT: '.$value['nit']);
            $this->docexcel->getActiveSheet()->getStyle("A$fila:L$fila")->applyFromArray($styleCabeceraNit);
          } else if ($value['razon_social'] != 'cabecera' && $value['razon_social'] != 'total') {
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $numero);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, date("d/m/Y", strtotime($value['fecha_factura'])));
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $value['nro_factura']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $value['nit']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $value['carnet_ide']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $value['nit']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $value['razon_social']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, $value['sistema_origen']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, $value['desc_ruta']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $fila, $value['cantidad']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $fila, $value['precio_unitario']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $fila, $value['precio_total']);
            $this->docexcel->getActiveSheet()->getStyle("A$fila:I$fila")->applyFromArray($styleFondoBlanco);
            $this->docexcel->getActiveSheet()->getStyle("A$fila:I$fila")->applyFromArray($styleFondoBlanco);
            $this->docexcel->getActiveSheet()->getStyle("A$fila:I$fila")->applyFromArray($style_datos);
            $this->docexcel->getActiveSheet()->getStyle("J$fila:L$fila")->applyFromArray($style_numeros);
            $this->docexcel->getActiveSheet()->getStyle("J$fila:L$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
            $numero++;
          } elseif ($value['razon_social'] == 'total') {
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, 'TOTALES NIT: '.$value['nit']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $fila, $value['precio_total']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $fila, $value['precio_total']);

            $this->docexcel->getActiveSheet()->mergeCells("A$fila:J$fila");
            $this->docexcel->getActiveSheet()->getStyle("A$fila:L$fila")->applyFromArray($styleTotales);
            $this->docexcel->getActiveSheet()->getStyle("K$fila:L$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
          
          }

          $fila++;

        }

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, 'TOTALES GENERAL: ');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $fila, $value['total_general']);
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $fila, $value['total_general']);
        $this->docexcel->getActiveSheet()->getStyle("K$fila:L$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

        $this->docexcel->getActiveSheet()->mergeCells("A$fila:J$fila");
        $this->docexcel->getActiveSheet()->getStyle("A$fila:L$fila")->applyFromArray($styleTotalesGenerales);


        // foreach($datos as $value){
        //         $valor=$value['nit'];
        //       if(!in_array($valor, $nit_cliente)){
        //          $nit_cliente[]=$valor;
        //       }
        // }

      //   foreach($nit_cliente as $value_nit ){
      //     $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $posicion_cabecera, 'NIT: '.$value_nit);
      //     $this->docexcel->getActiveSheet()->getStyle("A$posicion_cabecera:L$posicion_cabecera")->applyFromArray($styleCabeceraNit);
      //     unset($totaleNit);
      //       foreach ($datos as $value) {
      //           if ($value['nit'] == $value_nit) {
      //             $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $numero);
      //             $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, date("d/m/Y", strtotime($value['fecha_factura'])));
      //             $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $value['nro_factura']);
      //             $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $value['nit']);
      //             $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $value['carnet_ide']);
      //             $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $value['nit']);
      //             $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $value['razon_social']);
      //             $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, $value['sistema_origen']);
      //             $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, $value['desc_ruta']);
      //             $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $fila, $value['cantidad']);
      //             $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $fila, $value['precio_unitario']);
      //             $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $fila, $value['precio_total']);
      //             $this->docexcel->getActiveSheet()->getStyle("A$fila:I$fila")->applyFromArray($styleFondoBlanco);
      //             $this->docexcel->getActiveSheet()->getStyle("A$fila:I$fila")->applyFromArray($styleFondoBlanco);
      //             $this->docexcel->getActiveSheet()->getStyle("A$fila:I$fila")->applyFromArray($style_datos);
      //             $this->docexcel->getActiveSheet()->getStyle("J$fila:L$fila")->applyFromArray($style_numeros);
      //             $this->docexcel->getActiveSheet()->getStyle("J$fila:L$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
      //             $totaleNit[]=$value ['precio_total'];
      //             $fila++;
      //             $numero++;
      //           }
      //       }
      //       $fila_menos = $fila - 1;
      //
      //       $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, 'TOTALES NIT: '.$value_nit);
      //       $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $fila, array_sum($totaleNit));
      //       $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $fila, array_sum($totaleNit));
      //       $this->docexcel->getActiveSheet()->getStyle("K$fila:L$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
      //
      //       $this->docexcel->getActiveSheet()->mergeCells("A$fila:J$fila");
      //       $this->docexcel->getActiveSheet()->getStyle("A$fila:L$fila")->applyFromArray($styleTotales);
      //       // $this->docexcel->getActiveSheet()->getStyle("K$fila:K$fila")->applyFromArray($styleTotales1);
      //       // $this->docexcel->getActiveSheet()->getStyle("L$fila:L$fila")->applyFromArray($styleTotales2);
      //
      //       $posicion_cabecera = ($fila + 1);
      //       $fila = $fila + 1;
      //       $fila++;
      //
      // }

        // $inicio = 7;
        //
        // $this->docexcel->getActiveSheet()->getStyle("A$inicio:I$fila")->applyFromArray($styleFondoBlanco);
        // $this->docexcel->getActiveSheet()->getStyle("A$inicio:C$fila")->applyFromArray($style_datos);
        // $this->docexcel->getActiveSheet()->getStyle("A$fila:A$fila")->applyFromArray($styleFondoBlanco);

    }

    function generarReporte(){
        $this->generarDatos();
        $this->docexcel->setActiveSheetIndex(0);
        $this->objWriter = PHPExcel_IOFactory::createWriter($this->docexcel, 'Excel5');
        $this->objWriter->save($this->url_archivo);
    }

}
?>
