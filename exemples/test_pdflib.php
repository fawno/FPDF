<?php
//Script de test PDFlib
$file=tempnam("","pdf");
$fp=fopen($file,"w");
$pdf=pdf_open($fp);
for($p=1;$p<=10;$p++)
{
	pdf_begin_page($pdf, 595.3, 841.9);
	pdf_set_font($pdf,"Times-Roman",12,"host");
	for($i=1;$i<=25;$i++)
		pdf_show_xy($pdf,"This is line number $i on page number $p",30.1,824.1-28.35*$i);
	pdf_end_page($pdf);
}
pdf_close($pdf);
fclose($fp);
Header("Content-Type: application/pdf");
Header("Content-Length: ".filesize($file));
Header("Expires: 0");
readfile($file);
unlink($file);
?>
