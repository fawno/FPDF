<?php
//Script de test FPDF
require("fpdf.php");
$pdf=new FPDF();
$pdf->Open();
for($p=1;$p<=10;$p++)
{
	$pdf->AddPage();
	$pdf->SetFont("Times","",12);
	for($i=1;$i<=25;$i++)
		$pdf->Cell(10,10,"This is line number $i on page number $p",0,1);
}
$pdf->Output();
?>
