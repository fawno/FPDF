<?php
/****************************************************************************
* Logiciel : FPDF                                                           *
* Version :  1.1                                                            *
* Date :     07/10/2001                                                     *
* Licence :  Freeware                                                       *
* Auteur :   Olivier PLATHEY                                                *
*                                                                           *
* Vous pouvez utiliser et modifier ce logiciel comme vous le souhaitez.     *
****************************************************************************/
define('FPDF_VERSION','1.1');

class FPDF
{
//Private properties
var $page;              //current page number
var $n;                 //current number of objects
var $offset;            //stream offset
var $offsets;           //array of object offsets
var $buffer;            //buffer holding in-memory PDF
var $w,$h;              //dimensions of page in points
var $lMargin;           //left margin in user unit
var $tMargin;           //top margin in user unit
var $x,$y;              //current position in user unit for cell positionning
var $lasth;             //height of last cell printed
var $k;                 //scale factor (number of points in user unit)
var $fontnames;         //array of Postscript (Type1) font names
var $fonts;             //array of used fonts
var $images;            //array of used images
var $FontFamily;        //current font family
var $FontStyle;         //current font style
var $FontSizePt;        //current font size in points
var $FontSize;          //current font size in user unit
var $AutoPageBreak;     //automatic page breaking
var $PageBreakTrigger;  //threshold used to trigger page breaks
var $InFooter;          //flag set when processing footer
var $DocOpen;           //flag indicating whether doc is open or closed

/****************************************************************************
*                                                                           *
*                              Public methods                               *
*                                                                           *
****************************************************************************/
function FPDF($orientation='P',$unit='mm')
{
	//Initialization of properties
	$this->page=0;
	$this->n=1;
	$this->buffer='';
	$this->fonts=array();
	$this->images=array();
	$this->InFooter=false;
	$this->DocOpen=false;
	$this->FontStyle='';
	$this->FontSizePt=12;
	//Font names
	$this->fontnames['courier']='Courier';
	$this->fontnames['courierB']='Courier-Bold';
	$this->fontnames['courierI']='Courier-Oblique';
	$this->fontnames['courierBI']='Courier-BoldOblique';
	$this->fontnames['helvetica']='Helvetica';
	$this->fontnames['helveticaB']='Helvetica-Bold';
	$this->fontnames['helveticaI']='Helvetica-Oblique';
	$this->fontnames['helveticaBI']='Helvetica-BoldOblique';
	$this->fontnames['times']='Times-Roman';
	$this->fontnames['timesB']='Times-Bold';
	$this->fontnames['timesI']='Times-Italic';
	$this->fontnames['timesBI']='Times-BoldItalic';
	$this->fontnames['symbol']='Symbol';
	$this->fontnames['symbolB']='Symbol';
	$this->fontnames['symbolI']='Symbol';
	$this->fontnames['symbolBI']='Symbol';
	$this->fontnames['zapfdingbats']='ZapfDingbats';
	$this->fontnames['zapfdingbatsB']='ZapfDingbats';
	$this->fontnames['zapfdingbatsI']='ZapfDingbats';
	$this->fontnames['zapfdingbatsBI']='ZapfDingbats';
	//Page orientation (A4 format)
	$orientation=strtolower($orientation);
	if($orientation=='p' or $orientation=='portrait')
	{
		$this->w=595.3;
		$this->h=841.9;
	}
	elseif($orientation=='l' or $orientation=='landscape')
	{
		$this->w=841.9;
		$this->h=595.3;
	}
	else
		$this->Error('Incorrect orientation : '.$orientation);
	//Scale factor
	if($unit=='pt')
		$this->k=1;
	elseif($unit=='mm')
		$this->k=72/25.4;
	elseif($unit=='cm')
		$this->k=72/2.54;
	elseif($unit=='in')
		$this->k=72;
	else
		$this->Error('Incorrect unit : '.$unit);
	//Margins (1 cm)
	$margin=(double)sprintf('%.2f',28.35/$this->k);
	$this->SetMargins($margin,$margin);
	//Automatic page breaks
	$this->SetAutoPageBreak(true,2*$margin);
}

function SetMargins($left,$top)
{
	//Set left and top margins
	$this->lMargin=$left;
	$this->tMargin=$top;
}

function SetAutoPageBreak($auto,$margin=0)
{
	//Set auto page break mode and triggering margin
	$this->AutoPageBreak=$auto;
	$this->PageBreakTrigger=$this->h/$this->k-$margin;
}

function Error($msg)
{
	//Fatal error
	die('<B>FPDF error : </B>'.$msg);
}

function Open()
{
	//Begin document
	$this->_begindoc();
	$this->DocOpen=true;
}

function Close()
{
	//Terminate document
	if($page=$this->page==0)
		$this->Error('Document contains no page');
	//Page footer
	$this->InFooter=true;
	$this->Footer();
	$this->InFooter=false;
	//Close page
	$this->_endpage();
	//Close document
	$this->_enddoc();
	$this->DocOpen=false;
}

function AddPage()
{
	//Start a new page
	$page=$this->page;
	if($page>0)
	{
		//Remember font
		$family=$this->FontFamily;
		$style=$this->FontStyle;
		$size=$this->FontSizePt;
		//Page footer
		$this->InFooter=true;
		$this->Footer();
		$this->InFooter=false;
		//Close page
		$this->_endpage();
	}
	//Start new page
	$this->_beginpage();
	//Set line width to 1 point
	$this->SetLineWidth(sprintf('%.2f',1/$this->k));
	//Set line cap style to square
	$this->_out('2 J');
	//Page header
	$this->Header();
	//Restore font
	if($page>0 and $family!='')
		$this->SetFont($family,$style,$size);
}

function Header()
{
	//To be implemented in your own inherited class
}

function Footer()
{
	//To be implemented in your own inherited class
}

function PageNo()
{
	//Get current page number
	return $this->page;
}

function SetLineWidth($width)
{
	//Set line width
	$this->_out($width.' w');
}

function Line($x1,$y1,$x2,$y2)
{
	//Draw a line
	$this->_out($x1.' -'.$y1.' m '.$x2.' -'.$y2.' l S');
}

function Rect($x,$y,$w,$h)
{
	//Draw a rectangle
	$this->_out($x.' -'.$y.' '.$w.' -'.$h.' re S');
}

function SetFont($family,$style='',$size=0)
{
	//Select a font; size given in points
	if(!$this->_setfont($family,$style,$size))
		$this->Error('Incorrect font family or style : '.$family.' '.$style);
}

function SetFontSize($size)
{
	//Set font size in points
	$this->_setfontsize($size);
}

function Text($x,$y,$txt)
{
	//Output a string
	$txt=str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$txt)));
	$this->_out('BT '.$x.' -'.$y.' Td ('.$txt.') Tj ET');
}

function Cell($w,$h=0,$txt='',$border=0,$ln=0)
{
	//Output a cell
	if($this->y+$h>$this->PageBreakTrigger && $this->AutoPageBreak && !$this->InFooter)
		$this->AddPage();
	if($border==1)
		$this->_out($this->x.' -'.$this->y.' '.$w.' -'.$h.' re S');
	if($txt!='')
	{
		$txt=str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$txt)));
		$this->_out('BT '.($this->x+.15*$this->FontSize).' -'.($this->y+.5*$h+.3*$this->FontSize).' Td ('.$txt.') Tj ET');
	}
	$this->lasth=$h;
	if($ln==1)
	{
		//Go to next line
		$this->x=$this->lMargin;
		$this->y+=$h;
	}
	else
		$this->x+=$w;
}

function Image($file,$x,$y,$w,$h=0,$type='')
{
	//Put an image on the page
	if(!isset($this->images[$file]))
	{
		//First use of image, get info
		if($type=='')
		{
			$pos=strrpos($file,'.');
			if(!$pos)
				$this->Error('Image file has no extension and no type was specified : '.$file);
			$type=substr($file,$pos+1);
		}
		$type=strtolower($type);
		if($type=='jpg' or $type=='jpeg')
			$info=$this->_parsejpg($file);
		elseif($type=='png')
			$info=$this->_parsepng($file);
		else
			$this->Error('Unsupported image file type : '.$type);
		$info['n']=count($this->images)+1;
		$this->images[$file]=$info;
	}
	else
		$info=$this->images[$file];
	//Automatic height calculus
	if($h==0)
		$h=(double)sprintf('%.2f',$w*$info['h']/$info['w']);
	$this->_out('q '.$w.' 0 0 '.$h.' '.$x.' -'.($y+$h).' cm /I'.$info['n'].' Do Q');
}

function Ln($h='')
{
	//Line feed; default value is last cell height
	$this->x=$this->lMargin;
	if(is_string($h))
		$this->y+=$this->lasth;
	else
		$this->y+=$h;
}

function GetY()
{
	//Get y position
	return $this->y;
}

function SetY($y)
{
	//Set y position
	$this->x=$this->lMargin;
	if($y>=0)
		$this->y=$y;
	else
		$this->y=(double)sprintf('%.2f',$this->h/$this->k)+$y;
}

function Output($file='')
{
	//Output PDF to file or browser
	if($this->DocOpen)
		$this->Close();
	if($file=='')
	{
		Header('Content-Type: application/pdf');
		Header('Content-Length: '.strlen($this->buffer));
		Header('Expires: 0');
		echo $this->buffer;
	}
	else
	{
		$f=fopen($file,'wb');
		if(!$f)
			$this->Error('Unable to create output file : '.$file);
		fwrite($f,$this->buffer,strlen($this->buffer));
		fclose($f);
	}
}

/****************************************************************************
*                                                                           *
*                              Private methods                              *
*                                                                           *
****************************************************************************/
function _begindoc()
{
	$this->_out('%PDF-1.3');
}

function _enddoc()
{
	//Fonts
	$nf=$this->n;
	reset($this->fonts);
	while(list($name)=each($this->fonts))
	{
		$this->_newobj();
		$this->_out('<< /Type /Font');
		$this->_out('/Subtype /Type1');
		$this->_out('/BaseFont /'.$name);
		$this->_out('/Encoding /WinAnsiEncoding >>');
		$this->_out('endobj');
	}
	//Images
	$ni=$this->n;
	reset($this->images);
	while(list($file,$info)=each($this->images))
	{
		$this->_newobj();
		$this->_out('<< /Type /XObject');
		$this->_out('/Subtype /Image');
		$this->_out('/Width '.$info['w']);
		$this->_out('/Height '.$info['h']);
		if($info['cs']=='Indexed')
			$this->_out('/ColorSpace [/Indexed /DeviceRGB '.(strlen($info['pal'])/3-1).' '.($this->n+1).' 0 R]');
		else
			$this->_out('/ColorSpace /'.$info['cs']);
		$this->_out('/BitsPerComponent '.$info['bpc']);
		$this->_out('/Filter /'.$info['f']);
		if(isset($info['trns']) and is_array($info['trns']))
		{
			$trns='';
			for($i=0;$i<count($info['trns']);$i++)
				$trns.=$info['trns'][$i].' '.$info['trns'][$i].' ';
			$this->_out('/Mask ['.$trns.']');
		}
		$this->_out('/Length '.strlen($info['data']).' >>');
		$this->_out('stream');
		$this->_out($info['data']);
		$this->_out('endstream');
		$this->_out('endobj');
		//Palette
		if($info['cs']=='Indexed')
		{
			$this->_newobj();
			$this->_out('<< /Length '.strlen($info['pal']).' >>');
			$this->_out('stream');
			$this->_out($info['pal']);
			$this->_out('endstream');
			$this->_out('endobj');
		}
	}
	//Pages root
	$this->offsets[1]=strlen($this->buffer);
	$this->_out('1 0 obj');
	$this->_out('<< /Type /Pages');
	$kids='/Kids [';
	for($i=0;$i<$this->page;$i++)
		$kids.=(2+3*$i).' 0 R ';
	$this->_out($kids.']');
	$this->_out('/Count '.$this->page);
	$this->_out('/MediaBox [0 0 '.$this->w.' '.$this->h.']');
	$this->_out('/Resources << /ProcSet [/PDF /Text /ImageC]');
	$this->_out('/Font <<');
	for($i=1;$i<=count($this->fonts);$i++)
		$this->_out('/F'.$i.' '.($nf+$i).' 0 R');
	$this->_out('>>');
	$this->_out('/XObject <<');
	$nbpal=0;
	reset($this->images);
	while(list(,$info)=each($this->images))
	{
		$this->_out('/I'.$info['n'].' '.($ni+$info['n']+$nbpal).' 0 R');
		if($info['cs']=='Indexed')
			$nbpal++;
	}
	$this->_out('>> >> >>');
	$this->_out('endobj');
	//Info
	$this->_newobj();
	$this->_out('<< /Producer (FPDF '.FPDF_VERSION.')');
	$this->_out('/CreationDate (D:'.date('YmdHis').') >>');
	$this->_out('endobj');
	//Catalog
	$this->_newobj();
	$this->_out('<< /Type /Catalog');
	$this->_out('/Pages 1 0 R >>');
	$this->_out('endobj');
	//Cross-ref
	$o=strlen($this->buffer);
	$this->_out('xref');
	$this->_out('0 '.($this->n+1));
	$this->_out('0000000000 65535 f ');
	for($i=1;$i<=$this->n;$i++)
		$this->_out(sprintf('%010d 00000 n ',$this->offsets[$i]));
	//Trailer
	$this->_out('trailer');
	$this->_out('<< /Size '.($this->n+1));
	$this->_out('/Root '.$this->n.' 0 R');
	$this->_out('/Info '.($this->n-1).' 0 R >>');
	$this->_out('startxref');
	$this->_out($o);
	$this->_out('%%EOF');
}

function _beginpage()
{
	$this->page++;
	$this->x=$this->lMargin;
	$this->y=$this->tMargin;
	$this->lasth=0;
	$this->FontFamily='';
	//Page object
	$this->_newobj();
	$this->_out('<< /Type /Page');
	$this->_out('/Parent 1 0 R');
	$this->_out('/Contents '.($this->n+1).' 0 R >>');
	$this->_out('endobj');
	//Begin of page contents
	$this->_newobj();
	$this->_out('<< /Length '.($this->n+1).' 0 R >>');
	$this->_out('stream');
	$this->offset=strlen($this->buffer);
	//Set transformation matrix
	$this->_out(sprintf('%.6f',$this->k).' 0 0 '.sprintf('%.6f',$this->k).' 0 '.$this->h.' cm');
}

function _endpage()
{
	//End of page contents
	$size=strlen($this->buffer)-$this->offset;
	$this->_out('endstream');
	$this->_out('endobj');
	//Size of page contents stream
	$this->_newobj();
	$this->_out($size);
	$this->_out('endobj');
}

function _newobj()
{
	//Begin a new object
	$this->n++;
	$this->offsets[$this->n]=strlen($this->buffer);
	$this->_out($this->n.' 0 obj');
}

function _setfont($family,$style,$size)
{
	$family=strtolower($family);
	if($family=='')
		$family=$this->FontFamily;
	if($family=='arial')
		$family='helvetica';
	$style=strtoupper($style);
	if($style=='IB')
		$style='BI';
	if($size==0)
		$size=$this->FontSizePt;
	//Test if font already selected
	if($this->FontFamily==$family and $this->FontStyle==$style and $this->FontSizePt==$size)
		return true;
	//Retrieve Type1 font name
	if(!isset($this->fontnames[$family.$style]))
		return false;
	$fontname=$this->fontnames[$family.$style];
	//If the font is used for the first time, record it
	if(!isset($this->fonts[$fontname]))
	{
		$n=count($this->fonts);
		$this->fonts[$fontname]=$n+1;
	}
	//Select it
	$this->FontFamily=$family;
	$this->FontStyle=$style;
	$this->FontSizePt=$size;
	$this->FontSize=(double)sprintf('%.2f',$size/$this->k);
	$this->_out('BT /F'.$this->fonts[$fontname].' '.$this->FontSize.' Tf ET');
	return true;
}

function _setfontsize($size)
{
	//Test if size already selected
	if($this->FontSizePt==$size)
		return;
	//Select it
	$fontname=$this->fontnames[$this->FontFamily.$this->FontStyle];
	$this->FontSizePt=$size;
	$this->FontSize=(double)sprintf('%.2f',$size/$this->k);
	$this->_out('BT /F'.$this->fonts[$fontname].' '.$this->FontSize.' Tf ET');
}

function _parsejpg($file)
{
	//Extract info from a JPEG file
	$a=GetImageSize($file);
	if(!$a)
		$this->Error('Missing or incorrect image file :'.$file);
	if($a[2]!=2)
		$this->Error('Not a JPEG file : '.$file);
	if(!isset($a['channels']) or $a['channels']==3)
		$colspace='DeviceRGB';
	elseif($a['channels']==4)
		$colspace='DeviceCMYK';
	else
		$colspace='DeviceGray';
	$bpc=isset($a['bits']) ? $a['bits'] : 8;
	//Read whole file
	$f=fopen($file,'rb');
	$data=fread($f,filesize($file));
	fclose($f);
	return array('w'=>$a[0],'h'=>$a[1],'cs'=>$colspace,'bpc'=>$bpc,'f'=>'DCTDecode','data'=>$data);
}

function _parsepng($file)
{
	//Extract info from a PNG file
	$f=fopen($file,'rb');
	if(!$f)
		$this->Error('Can\'t open image file : '.$file);
	//Check signature
	if(fread($f,8)!=chr(137).'PNG'.chr(13).chr(10).chr(26).chr(10))
		$this->Error('Not a PNG file : '.$file);
	//Read header chunk
	fread($f,4);
	if(fread($f,4)!='IHDR')
		$this->Error('Incorrect PNG file : '.$file);
	$w=$this->_freadint($f);
	$h=$this->_freadint($f);
	$bpc=ord(fread($f,1));
	if($bpc>8)
		$this->Error('16-bit depth not supported : '.$file);
	$ct=ord(fread($f,1));
	if($ct==0)
	{
		$colspace='DeviceGray';
		$r=($w*$bpc)%8;
		$wb=$r ? ($w*$bpc-$r)/8+1 : ($w*$bpc)/8;
	}
	elseif($ct==2)
	{
		$colspace='DeviceRGB';
		$wb=3*$w;
	}
	elseif($ct==3)
	{
		$colspace='Indexed';
		$r=($w*$bpc)%8;
		$wb=$r ? ($w*$bpc-$r)/8+1 : ($w*$bpc)/8;
	}
	else
		$this->Error('Alpha channel not supported : '.$file);
	if(ord(fread($f,1))!=0)
		$this->Error('Unknown compression method : '.$file);
	if(ord(fread($f,1))!=0)
		$this->Error('Unknown filter method : '.$file);
	if(ord(fread($f,1))!=0)
		$this->Error('Interlacing not supported : '.$file);
	fread($f,4);
	//Scan chunks looking for palette, transparency and image data
	$pal='';
	$trns='';
	$data='';
	do
	{
		$n=$this->_freadint($f);
		$type=fread($f,4);
		if($type=='PLTE')
		{
			//Read palette
			$pal=fread($f,$n);
			fread($f,4);
		}
		elseif($type=='tRNS')
		{
			//Read transparency info
			$t=fread($f,$n);
			if($ct==0)
				$trns=array(substr($t,1,1));
			elseif($ct==2)
				$trns=array(substr($t,1,1),substr($t,3,1),substr($t,5,1));
			else
			{
				$pos=strpos($t,chr(0));
				if(!is_string($pos))
					$trns=array($pos);
			}
			fread($f,4);
		}
		elseif($type=='IDAT')
		{
			//Read image data block
			$data.=fread($f,$n);
			fread($f,4);
		}
		else
			fread($f,$n+4);
	}
	while($n);
	if($colspace=='Indexed' and empty($pal))
		$this->Error('Missing palette in '.$file);
	fclose($f);
	//Remove predictor tags
	if(!function_exists('gzuncompress'))
		$this->Error('PHP4 and Zlib extension needed for PNG support');
	$data2=gzuncompress($data);
	unset($data);
	$data3='';
	for($i=0;$i<$h;$i++)
	{
		if(substr($data2,$i*($wb+1),1)!=chr(0))
			$this->Error('PNG predictors not supported : '.$file);
		$data3.=substr($data2,$i*($wb+1)+1,$wb);
	}
	unset($data2);
	$data=gzcompress($data3);
	unset($data3);
	return array('w'=>$w,'h'=>$h,'cs'=>$colspace,'bpc'=>$bpc,'f'=>'FlateDecode','pal'=>$pal,'trns'=>$trns,'data'=>$data);
}

function _freadint($f)
{
	//Read a 4-byte integer from file
	$i=ord(fread($f,1))<<24;
	$i+=ord(fread($f,1))<<16;
	$i+=ord(fread($f,1))<<8;
	$i+=ord(fread($f,1));
	return $i;
}

function _out($s)
{
	//Add a line to the document
	$this->buffer.=$s."\n";
}
//End of class
}

//Handle silly IE contype request
if(isset($HTTP_ENV_VARS['HTTP_USER_AGENT']) and $HTTP_ENV_VARS['HTTP_USER_AGENT']=='contype')
{
	Header('Content-Type: application/pdf');
	exit;
}

?>
