<?php
/****************************************************************************
* Logiciel : FPDF                                                           *
* Version :  1.2                                                            *
* Date :     11/11/2001                                                     *
* Licence :  Freeware                                                       *
* Auteur :   Olivier PLATHEY                                                *
*                                                                           *
* Vous pouvez utiliser et modifier ce logiciel comme vous le souhaitez.     *
****************************************************************************/
define('FPDF_VERSION','1.2');

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
var $cMargin;           //cell margin in user unit
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
var $DisplayMode;       //display mode
var $title;             //title
var $subject;           //subject
var $author;            //author
var $keywords;          //keywords
var $creator;           //creator

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
	$this->fontnames['zapfdingbats']='ZapfDingbats';
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
	//Page margins (1 cm)
	$margin=(double)sprintf('%.2f',28.35/$this->k);
	$this->SetMargins($margin,$margin);
	//Interior cell margin (1 mm)
	$this->cMargin=$margin/10;
	//Automatic page break
	$this->SetAutoPageBreak(true,2*$margin);
	//Full width display mode
	$this->SetDisplayMode('fullwidth');
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

function SetDisplayMode($mode,$z=100)
{
	//Set display mode in viewer
	if($mode=='fullpage' or $mode=='fullwidth' or $mode=='real' or $mode=='default')
		$this->DisplayMode=$mode;
	elseif($mode=='zoom')
		$this->DisplayMode=$z;
	else
		$this->Error('Incorrect display mode : '.$mode);
}

function SetTitle($title)
{
	//Title of document
	$this->title=$title;
}

function SetSubject($subject)
{
	//Subject of document
	$this->subject=$subject;
}

function SetAuthor($author)
{
	//Author of document
	$this->author=$author;
}

function SetKeywords($keywords)
{
	//Keywords of document
	$this->keywords=$keywords;
}

function SetCreator($creator)
{
	//Creator of document
	$this->creator=$creator;
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

function GetStringWidth($s)
{
	//Get width of a string in the current font
	global $fpdf_charwidths;

	return $this->_getstringwidth($s,&$fpdf_charwidths[$this->FontFamily.$this->FontStyle]);
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

function Cell($w,$h=0,$txt='',$border=0,$ln=0,$align='')
{
	//Output a cell
	if($this->y+$h>$this->PageBreakTrigger and $this->AutoPageBreak and !$this->InFooter)
	{
		$old=$this->x;
		$this->AddPage();
		$this->x=$old;
	}
	if($border==1)
		$s=$this->x.' -'.$this->y.' '.$w.' -'.$h.' re S ';
	else
		$s='';
	if($txt!='')
	{
		if($align=='R')
			$dx=$w-$this->cMargin-$this->GetStringWidth($txt);
		elseif($align=='C')
			$dx=($w-$this->GetStringWidth($txt))/2;
		else
			$dx=$this->cMargin;
		$txt=str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$txt)));
		$s.='BT '.($this->x+$dx).' -'.($this->y+.5*$h+.3*$this->FontSize).' Td ('.$txt.') Tj ET';
	}
	if($s)
		$this->_out($s);
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
		$mqr=get_magic_quotes_runtime();
		set_magic_quotes_runtime(0);
		if($type=='jpg' or $type=='jpeg')
			$info=$this->_parsejpg($file);
		elseif($type=='png')
			$info=$this->_parsepng($file);
		else
			$this->Error('Unsupported image file type : '.$type);
		set_magic_quotes_runtime($mqr);
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

function GetX()
{
	//Get x position
	return $this->x;
}

function SetX($x)
{
	//Set x position
	if($x>=0)
		$this->x=$x;
	else
		$this->x=(double)sprintf('%.2f',$this->w/$this->k)+$x;
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

function Output($file='',$download=false)
{
	//Output PDF to file or browser
	global $HTTP_ENV_VARS;

	if($this->DocOpen)
		$this->Close();
	if($file=='')
	{
		//Send to browser
		Header('Content-Type: application/pdf');
		Header('Content-Length: '.strlen($this->buffer));
		Header('Expires: 0');
		echo $this->buffer;
	}
	else
	{
		if($download)
		{
			//Download file
			if(isset($HTTP_ENV_VARS['HTTP_USER_AGENT']) and strpos($HTTP_ENV_VARS['HTTP_USER_AGENT'],'MSIE 5.5'))
				Header('Content-Type: application/dummy');
			else
				Header('Content-Type: application/octet-stream');
			Header('Content-Length: '.strlen($this->buffer));
			Header('Content-Disposition: attachment; filename='.$file);
			Header('Expires: 0');
			echo $this->buffer;
		}
		else
		{
			//Save file locally
			$f=fopen($file,'wb');
			if(!$f)
				$this->Error('Unable to create output file : '.$file);
			fwrite($f,$this->buffer,strlen($this->buffer));
			fclose($f);
		}
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
		if($name!='Symbol' and $name!='ZapfDingbats')
			$this->_out('/Encoding /WinAnsiEncoding');
		$this->_out(' >>');
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
		if(isset($info['parms']))
			$this->_out($info['parms']);
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
	if(!empty($this->title))
		$this->_out('/Title ('.$this->_escape($this->title).')');
	if(!empty($this->subject))
		$this->_out('/Subject ('.$this->_escape($this->subject).')');
	if(!empty($this->author))
		$this->_out('/Author ('.$this->_escape($this->author).')');
	if(!empty($this->keywords))
		$this->_out('/Keywords ('.$this->_escape($this->keywords).')');
	if(!empty($this->creator))
		$this->_out('/Creator ('.$this->_escape($this->creator).')');
	$this->_out('/CreationDate (D:'.date('YmdHis').') >>');
	$this->_out('endobj');
	//Catalog
	$this->_newobj();
	$this->_out('<< /Type /Catalog');
	if($this->DisplayMode=='fullpage')
		$this->_out('/OpenAction [2 0 R /Fit]');
	elseif($this->DisplayMode=='fullwidth')
		$this->_out('/OpenAction [2 0 R /FitH null]');
	elseif($this->DisplayMode=='real')
		$this->_out('/OpenAction [2 0 R /XYZ null null 1]');
	else
		$this->_out('/OpenAction [2 0 R /XYZ null null '.($this->DisplayMode/100).']');
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
	global $fpdf_charwidths;

	$family=strtolower($family);
	if($family=='')
		$family=$this->FontFamily;
	if($family=='arial')
		$family='helvetica';
	if($family=='symbol' or $family=='zapfdingbats')
		$style='';
	$style=strtoupper($style);
	if($style=='IB')
		$style='BI';
	if($size==0)
		$size=$this->FontSizePt;
	//Test if font is already selected
	if($this->FontFamily==$family and $this->FontStyle==$style and $this->FontSizePt==$size)
		return true;
	//Retrieve Type1 font name
	if(!isset($this->fontnames[$family.$style]))
		return false;
	$fontname=$this->fontnames[$family.$style];
	//Test if used for the first time
	if(!isset($this->fonts[$fontname]))
	{
		$n=count($this->fonts);
		$this->fonts[$fontname]=$n+1;
		if(!isset($fpdf_charwidths[$family.$style]))
		{
			//include metric file
			$file=$family;
			if($family=='times' or $family=='helvetica')
				$file.=strtolower($style);
			$file.='.php';
			if(defined('FPDF_FONTPATH'))
				$file=FPDF_FONTPATH.$file;
			include($file);
			if(!isset($fpdf_charwidths[$family.$style]))
				$this->Error('Could not include font metric file');
		}
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

function _getstringwidth($s,$cw)
{
	//Compute width of a string
	$w=0;
	$l=strlen($s);
	for($i=0;$i<$l;$i++)
		$w+=$cw[$s[$i]];
	return $w*$this->FontSize/1000;
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
		$colspace='DeviceGray';
	elseif($ct==2)
		$colspace='DeviceRGB';
	elseif($ct==3)
		$colspace='Indexed';
	else
		$this->Error('Alpha channel not supported : '.$file);
	if(ord(fread($f,1))!=0)
		$this->Error('Unknown compression method : '.$file);
	if(ord(fread($f,1))!=0)
		$this->Error('Unknown filter method : '.$file);
	if(ord(fread($f,1))!=0)
		$this->Error('Interlacing not supported : '.$file);
	fread($f,4);
	$parms='/DecodeParms << /Predictor 15 /Colors '.($ct==2 ? 3 : 1).' /BitsPerComponent '.$bpc.' /Columns '.$w.' >>';
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
				if(is_int($pos))
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
		elseif($type=='IEND')
			break;
		else
			fread($f,$n+4);
	}
	while($n);
	if($colspace=='Indexed' and empty($pal))
		$this->Error('Missing palette in '.$file);
	fclose($f);
	return array('w'=>$w,'h'=>$h,'cs'=>$colspace,'bpc'=>$bpc,'f'=>'FlateDecode','parms'=>$parms,'pal'=>$pal,'trns'=>$trns,'data'=>$data);
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

function _escape($s)
{
	//Add \ before \, ( and )
	return str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$s)));
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
