<?php
class RComisionistasSimplificadoXLS
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
        $this->docexcel->getActiveSheet()->setTitle('Simplificado');
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
                    'rgb' => 'EDEDED'
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

        //titulos

        // $this->docexcel->getActiveSheet()->getStyle('A2:H2')->applyFromArray($styleTitulos);
        // $this->docexcel->getActiveSheet()->getStyle('A1:A3')->applyFromArray($bordes);

        $NIT = 	$this->datos_contenido[0]['nit_empresa'];

        if( $this->objParam->getParametro('filtro_sql') == 'periodo' ){
            $sufijo = ($this->datos_contenido[0]['periodo_num']<10?'0'.$this->datos_contenido[0]['periodo_num']:$this->datos_contenido[0]['periodo_num']).$this->datos_contenido[0]['gestion'];
        }else{
            $sufijo=$this->objParam->getParametro('fecha_ini').'_'.$this->objParam->getParametro('fecha_fin');
        }

        if ($this->datos_contenido[0]['periodo_literal_inicio'] == $this->datos_contenido[0]['periodo_literal_fin']) {
          $gestion = 'GESTIÓN: '.$this->datos_contenido[0]['gestion'].' MES: '.$this->datos_contenido[0]['periodo_literal_inicio'];
          //$mes_envio = 'MES DE ENVIO AL SIN: '.$this->datos_contenido[0]['periodo_literal'];
        } else {
          $gestion = 'GESTIÓN: '.$this->datos_contenido[0]['gestion'].' DESDE: '.$this->datos_contenido[0]['periodo_literal_inicio'].' HASTA: '.$this->datos_contenido[0]['periodo_literal_fin'];
          //$mes_envio = 'ENVIO AL SIN DESDE: '.$this->objParam->getParametro('fecha_ini').' HASTA: '.$this->objParam->getParametro('fecha_fin');

        }


        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,2,'REGIMEN SIMPLIFICADO');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,3,'BOLIVIANA DE AVIACIÓN (BoA) NIT '.$NIT);
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,4,$gestion);
        //$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,5,'PERSONA NATURALES');
        $this->docexcel->getActiveSheet()->mergeCells('C2:F2');
        $this->docexcel->getActiveSheet()->mergeCells('C3:F3');
        $this->docexcel->getActiveSheet()->mergeCells('C4:F4');
        $this->docexcel->getActiveSheet()->mergeCells('C5:F5');
        $this->docexcel->getActiveSheet()->getStyle('A1:F5')->applyFromArray($styleTituloPrincipal);
        $this->docexcel->getActiveSheet()->getStyle('A1:I5')->applyFromArray($styleFondoBlanco);
        // $this->docexcel->getActiveSheet()->getStyle('A2:H2')->applyFromArray($styleTitulos);
        // $this->docexcel->getActiveSheet()->getStyle('B1:L1')->applyFromArray($bordes_titulo_supe);
        // $this->docexcel->getActiveSheet()->getStyle('B3:L3')->applyFromArray($bordes_titulo_infe);
        // $this->docexcel->getActiveSheet()->mergeCells('B2:L2');
        // $this->docexcel->getActiveSheet()->getStyle('B2:L2')->applyFromArray($styleTituloPrincipal);
        // $this->docexcel->getActiveSheet()->mergeCells('B3:L3');
        // $this->docexcel->getActiveSheet()->mergeCells('B1:L1');
        //
        //
        // $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(12,1,'Desde: '.$this->objParam->getParametro('desde'));
        // $this->docexcel->getActiveSheet()->getStyle('A1:M1')->applyFromArray($styleTitulosSubCabezera);
        // $this->docexcel->getActiveSheet()->getStyle('M1')->applyFromArray($bordes);
        //
        // $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(12,2,'Hasta: '.$this->objParam->getParametro('hasta'));
        // $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(12,3,'Gestión: '.$this->objParam->getParametro('gestion'));
        // $this->docexcel->getActiveSheet()->getStyle('A3:M3')->applyFromArray($styleTitulosSubCabezera);
        // $this->docexcel->getActiveSheet()->getStyle('M2')->applyFromArray($styleTitulosSubCabezera);
        // $this->docexcel->getActiveSheet()->getStyle('M2')->applyFromArray($bordes);
        // $this->docexcel->getActiveSheet()->getStyle('M3')->applyFromArray($bordes);
        // $this->docexcel->getActiveSheet()->getStyle('A4:M4')->applyFromArray($styleTitulosSubCabezera);
        //




        //*************************************Cabecera*****************************************

        $this->docexcel->getActiveSheet()->getColumnDimension('A')->setWidth(17);
        $this->docexcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(40);
        $this->docexcel->getActiveSheet()->getColumnDimension('E')->setWidth(40);
        $this->docexcel->getActiveSheet()->getColumnDimension('F')->setWidth(40);
        $this->docexcel->getActiveSheet()->getColumnDimension('G')->setWidth(22);
        $this->docexcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('J')->setWidth(50);
        $this->docexcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);


        $this->docexcel->getActiveSheet()->setCellValue('A6','Cod. Cliente');
        $this->docexcel->getActiveSheet()->setCellValue('B6','NIT Cliente');
        $this->docexcel->getActiveSheet()->setCellValue('C6','Cod. Producto');
        $this->docexcel->getActiveSheet()->setCellValue('D6','Desc. Producto');

        $this->docexcel->getActiveSheet()->setCellValue('E6','Cant. Producto');
        $this->docexcel->getActiveSheet()->setCellValue('F6','P/U (Bs)');
        $this->docexcel->getActiveSheet()->setCellValue('G6','Total (Bs)');


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

        $styleFondoBlanco = array(
          'fill' => array(
              'type' => PHPExcel_Style_Fill::FILL_SOLID,
              'color' => array(
                  'rgb' => 'FFFFFF'
              )
          ),
        );

        $datos = $this->datos_contenido;

        foreach ($datos as $value) {
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $value["nit"]);
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $value['nit']);
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $value['sistema_origen']);
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $value['desc_ruta']);
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $value['cantidad']);
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $value['precio_unitario']);
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $value['precio_total']);

          $this->docexcel->getActiveSheet()->getStyle("A$fila:H$fila")->applyFromArray($styleFondoBlanco);
          $this->docexcel->getActiveSheet()->getStyle("A$fila:B$fila")->applyFromArray($style_datos);

          $this->docexcel->getActiveSheet()->getStyle("F$fila:G$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);


          $fila++;
        }

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
