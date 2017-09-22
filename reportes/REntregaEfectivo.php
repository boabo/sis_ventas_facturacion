<?php

class REntregaEfectivo extends  ReportePDF
{
    function Header()
    {
        $this->Image(dirname(__FILE__) . '/../../pxp/lib/images/Logo-BoA.png', 17, 15, 25);
    }
    function cabezera ($tipo){
        $height = 8;
        $this->Cell(40, $height, '', 0, 0, 'L', false, '', 0, false, 'T', 'C');
        $this->SetFontSize(13);
        $this->SetFont('', 'B');
        $this->Cell(105, $height, 'FORMULARIO DE ENTREGA DE EFECTIVO', 0, 0, 'C', 0, '', 0);
        $this->Ln();
        $this->SetFontSize(10);
        $this->SetFont('', 'B');
        $this->Cell(0, 5, 'PAPELETA DE DEPÓSITO EN '.$tipo, 0, 0, 'C', 0, '', 0);
        $this->MultiCell(0, $height, "\n" . '' . "\n" . '' . "\n" . '', 0, 'C', 0, '', '');
        $this->Ln();
    }

    function reporteEntrega(){
        $this->cabezera('BOLIVIANOS');
        $height = 5;
        $width2 = 20;
        $width3 = 35;
        $this->SetFillColor(192,192,192, true);
        $this->SetFont('','B',8);
        $this->Cell(0, 5, 'DATOS GENERALES',1,1,'C',1);
        $this->SetFont('','',7);
        $this->Ln();
        $this->SetFontSize(6);
        $this->SetFont('', 'B');
        $this->Cell($width3, $height, 'NOMBRE CAJERO:', 0, 0, 'L', false, '', 0, false, 'T', 'C');
        $this->SetFont('', '');
        $this->Cell($width3+$width2, $height, $this->datos[0]["nombre_cajero"], 1, 0, 'L', 0, '', 0);
        $this->SetFont('', 'B');
        $this->Cell(5, $height, '', 0, 0, 'L', false, '', 0, false, 'T', 'C');
        $this->Cell($width3, $height, 'FECHA DE RECOJO:', 0, 0, 'L', false, '', 0, false, 'T', 'C');
        $this->SetFont('', '');
        $this->Cell($width3+$width2, $height, $this->datos[0]["fecha_recojo"], 1, 0, 'L', 0, '', 0);
        $this->Ln();
        $this->SetFontSize(6);
        $this->SetFont('', 'B');
        $this->Cell($width3, $height, 'ESTACION:', 0, 0, 'L', false, '', 0, false, 'T', 'C');
        $this->SetFont('', '');
        $this->Cell($width3+$width2, $height, $this->datos[0]["estacion"], 1, 0, 'L', 0, '', 0);
        $this->SetFont('', 'B');
        $this->Cell(5, $height, '', 0, 0, 'L', false, '', 0, false, 'T', 'C');
        $this->Cell($width3, $height, 'BANCO:', 0, 0, 'L', false, '', 0, false, 'T', 'C');
        $this->SetFont('', '');
        $this->Cell($width3+$width2, $height, $this->datos[0]["denominacion"], 1, 0, 'L', 0, '', 0);
        $this->Ln();
        $this->SetFontSize(6);
        $this->SetFont('', 'B');
        $this->Cell($width3, $height, 'PUNTO DE VENTA:', 0, 0, 'L', false, '', 0, false, 'T', 'C');
        $this->SetFont('', '');
        $this->Cell($width3+$width2, $height, $this->datos[0]["punto_venta"], 1, 0, 'L', 0, '', 0);
        $this->SetFont('', 'B');
        $this->Cell(5, $height, '', 0, 0, 'L', false, '', 0, false, 'T', 'C');
        $this->Cell($width3, $height, 'NRO. CUENTA:', 0, 0, 'L', false, '', 0, false, 'T', 'C');
        $this->SetFont('', '');
        $this->Cell($width3+$width2, $height, $this->datos[0]["nro_cuenta"], 1, 0, 'L', 0, '', 0);
        $this->Ln(12);
        $this->SetFillColor(192,192,192, true);
        $this->SetFont('','B',8);
        $this->Cell(0, 5, 'DESGLOSE DE DEPÓSITOS EN EFECTIVO',1,1,'C',1);
        $this->SetFont('','',7);
        $this->Ln();
        $this->SetFontSize(6);
        $this->SetFont('', 'B');
        $this->Cell(50, 5, '', 0, 0, 'C', 0, '', 0);
        $this->Cell(50, 5, 'FECHA DE VENTA', 1, 0, 'C', 0, '', 0);
        $this->Cell(50, 5, 'IMPORTE (Bs)', 1, 0, 'C', 0, '', 0);
        $this->Cell(0, 5, '', 0, 0, 'C', 0, '', 0);
        $this->Ln();
        foreach ($this->datos  as $value){
            if ($value['arqueo_moneda_local'] != 0) {
                $this->SetFontSize(7);
                $this->SetFont('', '');
                $this->Cell(50, 5, '', 0, 0, 'C', 0, '', 0);
                $this->Cell(50, 5, $value['fecha_apertura_cierre'], 1, 0, 'C', 0, '', 0);
                $this->Cell(50, 5, $value['arqueo_moneda_local'], 1, 0, 'R', 0, '', 0);
                $this->Cell(0, 5, '', 0, 0, 'C', 0, '', 0);
                $this->Ln();
            }
        }
        $this->SetFontSize(6);
        $this->SetFont('', '');
        $this->Cell(50, 5, '', 0, 0, 'C', 0, '', 0);
        $this->Cell(50, 5, 'TOTAL', 1, 0, 'R', 0, '', 0);
        $this->Cell(50, 5, $this->datos[0]['total'], 1, 0, 'R', 0, '', 0);
        $this->Cell(0, 5, '', 0, 0, 'C', 0, '', 0);
        $this->Ln(10);
        $this->Cell(25, 5, 'SON:', 0, 0, 'R', 0, '', 0);
        $this->Cell(150, 5, $this->datos[0]['literial'].'BOLIVIANOS', 1, 0, 'L', 0, '', 0);
        $this->Cell(0, 5, '', 0, 0, 'C', 0, '', 0);
        $this->Ln(30);
        $this->SetFillColor(192,192,192, true);
        $this->SetFont('','B',8);
        $this->Cell(55, 5, 'DEPOSITANTE', 1, 0, 'C', 1);
        $this->SetFont('','',7);
        $this->SetFillColor(192,192,192, true);
        $this->SetFont('','B',8);
        $this->Cell(75, 5, '', 1, 0, 'C', 1);
        $this->SetFillColor(192,192,192, true);
        $this->SetFont('','B',8);
        $this->Cell(0, 5, 'DEPOSITARIO', 1, 0, 'C', 1);
        $this->SetFont('','',7);
        $this->Ln();
        $this->Cell(55, 25, '', 1, 0, 'C', 0, '', 0);
        $this->Cell(75, 25, '', 1, 0, 'C', 0, '', 0);
        $this->Cell(0, 25, '', 1, 0, 'C', 0, '', 0);
        $this->Ln();
        $this->SetFontSize(6);
        $this->SetFont('', '');
        $this->Cell(55, 5, $this->datos[0]["nombre_cajero"], 1, 0, 'C', 0, '', 0);
        $this->Cell(75, 5, '', 1, 0, 'C', 0, '', 0);
        $this->Cell(0, 5, '', 1, 0, 'C', 0, '', 0);
        $this->Ln();
        $this->SetFontSize(6);
        $this->SetFont('', 'B');
        $this->Cell(55, 5, 'BOA Entrega Conforme', 1, 0, 'C', 0, '', 0);
        $this->Cell(75, 5, '', 1, 0, 'C', 0, '', 0);
        $this->Cell(0, 5, 'Recibí Conforne', 1, 0, 'C', 0, '', 0);
    }

    function reporteEntregaDolares(){
        $this->cabezera('DOLARES AMERICANOS');
        $height = 5;
        $width2 = 20;
        $width3 = 35;
        $this->SetFillColor(192,192,192, true);
        $this->SetFont('','B',8);
        $this->Cell(0, 5, 'DATOS GENERALES',1,1,'C',1);
        $this->SetFont('','',7);
        $this->Ln();
        $this->SetFontSize(6);
        $this->SetFont('', 'B');
        $this->Cell($width3, $height, 'NOMBRE CAJERO:', 0, 0, 'L', false, '', 0, false, 'T', 'C');
        $this->SetFont('', '');
        $this->Cell($width3+$width2, $height, $this->datos2[0]["nombre_cajero"], 1, 0, 'L', 0, '', 0);
        $this->SetFont('', 'B');
        $this->Cell(5, $height, '', 0, 0, 'L', false, '', 0, false, 'T', 'C');
        $this->Cell($width3, $height, 'FECHA DE RECOJO:', 0, 0, 'L', false, '', 0, false, 'T', 'C');
        $this->SetFont('', '');
        $this->Cell($width3+$width2, $height, $this->datos2[0]["fecha_recojo"], 1, 0, 'L', 0, '', 0);
        $this->Ln();

        $this->SetFontSize(6);
        $this->SetFont('', 'B');
        $this->Cell($width3, $height, 'ESTACION:', 0, 0, 'L', false, '', 0, false, 'T', 'C');
        $this->SetFont('', '');
        $this->Cell($width3+$width2, $height, $this->datos2[0]["estacion"], 1, 0, 'L', 0, '', 0);
        $this->SetFont('', 'B');
        $this->Cell(5, $height, '', 0, 0, 'L', false, '', 0, false, 'T', 'C');
        $this->Cell($width3, $height, 'BANCO:', 0, 0, 'L', false, '', 0, false, 'T', 'C');
        $this->SetFont('', '');
        $this->Cell($width3+$width2, $height, $this->datos2[0]["denominacion"], 1, 0, 'L', 0, '', 0);
        $this->Ln();

        $this->SetFontSize(6);
        $this->SetFont('', 'B');
        $this->Cell($width3, $height, 'PUNTO DE VENTA:', 0, 0, 'L', false, '', 0, false, 'T', 'C');
        $this->SetFont('', '');
        $this->Cell($width3+$width2, $height, $this->datos2[0]["punto_venta"], 1, 0, 'L', 0, '', 0);
        $this->SetFont('', 'B');
        $this->Cell(5, $height, '', 0, 0, 'L', false, '', 0, false, 'T', 'C');
        $this->Cell($width3, $height, 'NRO. CUENTA:', 0, 0, 'L', false, '', 0, false, 'T', 'C');
        $this->SetFont('', '');
        $this->Cell($width3+$width2, $height, $this->datos2[0]["nro_cuenta"], 1, 0, 'L', 0, '', 0);
        $this->Ln(12);
        $this->SetFillColor(192,192,192, true);
        $this->SetFont('','B',8);
        $this->Cell(0, 5, 'DESGLOSE DE DEPÓSITOS EN EFECTIVO',1,1,'C',1);
        $this->SetFont('','',7);
        $this->Ln();
        $this->SetFontSize(6);
        $this->SetFont('', 'B');
        $this->Cell(50, 5, '', 0, 0, 'C', 0, '', 0);
        $this->Cell(50, 5, 'FECHA DE VENTA', 1, 0, 'C', 0, '', 0);
        $this->Cell(50, 5, 'IMPORTE ($us)', 1, 0, 'C', 0, '', 0);
        $this->Cell(0, 5, '', 0, 0, 'C', 0, '', 0);
        $this->Ln();
        foreach ($this->datos2  as $value){
            if ($value['arqueo_moneda_extranjera'] != 0) {
                $this->SetFontSize(6);
                $this->SetFont('', '');
                $this->Cell(50, 5, '', 0, 0, 'C', 0, '', 0);
                $this->Cell(50, 5, $value['fecha_apertura_cierre'], 1, 0, 'C', 0, '', 0);
                $this->Cell(50, 5, $value['arqueo_moneda_extranjera'], 1, 0, 'R', 0, '', 0);
                $this->Cell(0, 5, '', 0, 0, 'C', 0, '', 0);
                $this->Ln();
            }
        }
        $this->SetFontSize(6);
        $this->SetFont('', '');
        $this->Cell(50, 5, '', 0, 0, 'C', 0, '', 0);
        $this->Cell(50, 5, 'TOTAL', 1, 0, 'R', 0, '', 0);
        $this->Cell(50, 5, $this->datos2[0]['total'], 1, 0, 'R', 0, '', 0);
        $this->Cell(0, 5, '', 0, 0, 'C', 0, '', 0);
        $this->Ln(10);
        $this->Cell(25, 5, 'SON:', 0, 0, 'R', 0, '', 0);
        $this->Cell(150, 5, $this->datos2[0]['literial'].'DOLARES AMERICANOS', 1, 0, 'L', 0, '', 0);
        $this->Cell(0, 5, '', 0, 0, 'C', 0, '', 0);
        $this->Ln(30);
        $this->SetFillColor(192,192,192, true);
        $this->SetFont('','B',8);
        $this->Cell(55, 5, 'DEPOSITANTE', 1, 0, 'C', 1);
        $this->SetFont('','',7);
        $this->SetFillColor(192,192,192, true);
        $this->SetFont('','B',8);
        $this->Cell(75, 5, '', 1, 0, 'C', 1);
        $this->SetFillColor(192,192,192, true);
        $this->SetFont('','B',8);
        $this->Cell(0, 5, 'DEPOSITARIO', 1, 0, 'C', 1);
        $this->SetFont('','',7);
        $this->Ln();
        $this->Cell(55, 25, '', 1, 0, 'C', 0, '', 0);
        $this->Cell(75, 25, '', 1, 0, 'C', 0, '', 0);
        $this->Cell(0, 25, '', 1, 0, 'C', 0, '', 0);
        $this->Ln();
        $this->SetFontSize(6);
        $this->SetFont('', '');
        $this->Cell(55, 5, $this->datos2[0]["nombre_cajero"], 1, 0, 'C', 0, '', 0);
        $this->Cell(75, 5, '', 1, 0, 'C', 0, '', 0);
        $this->Cell(0, 5, '', 1, 0, 'C', 0, '', 0);
        $this->Ln();
        $this->SetFontSize(6);
        $this->SetFont('', 'B');
        $this->Cell(55, 5, 'BOA Entrega Conforme', 1, 0, 'C', 0, '', 0);
        $this->Cell(75, 5, '', 1, 0, 'C', 0, '', 0);
        $this->Cell(0, 5, 'Recibí Conforne', 1, 0, 'C', 0, '', 0);
    }

    function setDatos($datos,$datos2) {
        $this->datos = $datos;
        $this->datos2 = $datos2;
    }
    function generarReporte() {
        $this->SetMargins(15,20,15);
        $this->setFontSubsetting(false);
        $this->AddPage();
        $this->SetMargins(15,20,15);
        if($this->datos[0]['total']!= 0) {
            $this->reporteEntrega();
            }else{
            $this->reporteEntregaDolares();
        }

        if($this->datos2[0]['total'] != 0 and $this->datos[0]['total']!= 0) {
            $this->AddPage();
            $this->reporteEntregaDolares();
        }

    }

}

?>