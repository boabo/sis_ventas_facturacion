<?php
class RResumenVentasNaturalesXLS
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


        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,1,'RESUMEN - VENTA DE BIENES Y/O SERVICIOS A SUJETOS PASIVOS' );
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,2,'DEL REGIMEN TRIBUTARIO SIMPLIFICADO A PERSONAS NO INSCRITAS' );
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,3,'EN EL PADRON NACIONAL DE CONTRIBUYENTES' );
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,4,'PERIODO: '.$this->datos_contenido[0]['gestion'].' DESDE MES: '.$this->datos_contenido[0]['mes_inicio'].' HASTA MES: '.$this->datos_contenido[0]['mes_final'] );
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2,5,'PERSONAS NATURALES');
        $this->docexcel->getActiveSheet()->mergeCells('C1:D1');
        $this->docexcel->getActiveSheet()->mergeCells('C2:D2');
        $this->docexcel->getActiveSheet()->mergeCells('C3:D3');
        $this->docexcel->getActiveSheet()->mergeCells('C4:D4');
        $this->docexcel->getActiveSheet()->mergeCells('C5:D5');

        $this->docexcel->getActiveSheet()->getStyle('C1:D5')->applyFromArray($styleTituloPrincipal);
        $this->docexcel->getActiveSheet()->getStyle('A1:D5')->applyFromArray($styleFondoBlanco);

        //*************************************Cabecera*****************************************

        $this->docexcel->getActiveSheet()->getColumnDimension('A')->setWidth(17);
        $this->docexcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
        $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(40);



        $this->docexcel->getActiveSheet()->setCellValue('A6','NRO.');
        $this->docexcel->getActiveSheet()->setCellValue('B6','NRO. DE DOC. DE IDENTIFICACIÓN');
        $this->docexcel->getActiveSheet()->setCellValue('C6','IMPORTE ACUMULADO EN BS.');
        $this->docexcel->getActiveSheet()->setCellValue('D6','MES DE ENVIO AL SIN');


        $this->docexcel->getActiveSheet()->getStyle('A6:D6')->applyFromArray($styleSubtitulos);
        $this->docexcel->getActiveSheet()->getStyle('A6:D6')->getAlignment()->setWrapText(true);

        $this->docexcel->getActiveSheet()->freezePaneByColumnAndRow(0,7);




    }

    function generarDatos(){
        $this->imprimeCabecera();
        $fila = 7;
        $numero = 1;

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
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $numero);
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $value['nit']);
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $value['total_acumulado']);
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $value['mes_envio']);

          $this->docexcel->getActiveSheet()->getStyle("A$fila:D$fila")->applyFromArray($styleFondoBlanco);
          $this->docexcel->getActiveSheet()->getStyle("A$fila:B$fila")->applyFromArray($style_datos);
          $this->docexcel->getActiveSheet()->getStyle("C$fila:C$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
          $this->docexcel->getActiveSheet()->getStyle("D$fila:D$fila")->applyFromArray($style_datos);



          $fila++;
          $numero ++;
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
