<?php
//Exemple de 2 pages avec en-tête et pied de page
require('fpdf.php');

class PDF extends FPDF
{

//Constructeur (obligatoire pour PHP3)
function PDF()
{
    $this->FPDF();
}

//En-tête
function Header()
{
    //Police Arial gras 15
    $this->SetFont('Arial','B',15);
    //Décalage à droite
    $this->Cell(80);
    //Titre
    $this->Cell(30,10,'Titre');
    //Saut de ligne
    $this->Ln(20);
}

//Pied de page
function Footer()
{
    //Police Arial italique 8
    $this->SetFont('Arial','I',8);
    //Positionnement à 1,5 cm du bas
    $this->SetY(-15);
    //Décalage à droite
    $this->Cell(80);
    //Numéro de page
    $this->Cell(30,10,'Page '.$this->PageNo());
}
}

$pdf=new PDF();
$pdf->Open();
$pdf->AddPage();
$pdf->SetFont('Times','',12);
for($i=1;$i<=40;$i++)
    $pdf->Cell(50,10,'Impression de la ligne numéro '.$i,0,1);
$pdf->Output();
?>
