<?php
	declare(strict_types=1);

	namespace FPDF\Scripts\PDFBookmark;
	//http://www.fpdf.org/en/script/script1.php

	trait PDFBookmarkTrait {
		protected $outlines = array();
		protected $outlineRoot;

		/**
		 * Add a bookmark
		 *
		 * @param string $txt The bookmark title.
		 * @param bool $isUTF8 Indicates if the title is encoded in ISO-8859-1 (false) or UTF-8 (true). Default value: false.
		 * @param int $level The bookmark level (0 is top level, 1 is just below, and so on). Default value: 0.
		 * @param float $y The y position of the bookmark destination in the current page. -1 means the current position. Default value: 0.
		 * @return void
		 */
		public function Bookmark (string $txt, bool $isUTF8 = false, int $level = 0, float $y = 0) : void	{
			if(!$isUTF8)
				$txt = mb_convert_encoding($txt, 'UTF-8', 'ISO-8859-1');
			if($y==-1)
				$y = $this->GetY();
			$this->outlines[] = array('t'=>$txt, 'l'=>$level, 'y'=>($this->h-$y)*$this->k, 'p'=>$this->PageNo());
		}

		protected function _putbookmarks()
		{
			$nb = count($this->outlines);
			if($nb==0)
				return;
			$lru = array();
			$level = 0;
			foreach($this->outlines as $i=>$o)
			{
				if($o['l']>0)
				{
					$parent = $lru[$o['l']-1];
					// Set parent and last pointers
					$this->outlines[$i]['parent'] = $parent;
					$this->outlines[$parent]['last'] = $i;
					if($o['l']>$level)
					{
						// Level increasing: set first pointer
						$this->outlines[$parent]['first'] = $i;
					}
				}
				else
					$this->outlines[$i]['parent'] = $nb;
				if($o['l']<=$level && $i>0)
				{
					// Set prev and next pointers
					$prev = $lru[$o['l']];
					$this->outlines[$prev]['next'] = $i;
					$this->outlines[$i]['prev'] = $prev;
				}
				$lru[$o['l']] = $i;
				$level = $o['l'];
			}
			// Outline items
			$n = $this->n+1;
			foreach($this->outlines as $i=>$o)
			{
				$this->_newobj();
				$this->_put('<</Title '.$this->_textstring($o['t']));
				$this->_put('/Parent '.($n+$o['parent']).' 0 R');
				if(isset($o['prev']))
					$this->_put('/Prev '.($n+$o['prev']).' 0 R');
				if(isset($o['next']))
					$this->_put('/Next '.($n+$o['next']).' 0 R');
				if(isset($o['first']))
					$this->_put('/First '.($n+$o['first']).' 0 R');
				if(isset($o['last']))
					$this->_put('/Last '.($n+$o['last']).' 0 R');
				$this->_put(sprintf('/Dest [%d 0 R /XYZ 0 %.2F null]',$this->PageInfo[$o['p']]['n'],$o['y']));
				$this->_put('/Count 0>>');
				$this->_put('endobj');
			}
			// Outline root
			$this->_newobj();
			$this->outlineRoot = $this->n;
			$this->_put('<</Type /Outlines /First '.$n.' 0 R');
			$this->_put('/Last '.($n+$lru[0]).' 0 R>>');
			$this->_put('endobj');
		}

		protected function _putresources()
		{
			parent::_putresources();
			$this->_putbookmarks();
		}

		protected function _putbookmarkscatalog()
		{   
			if(count($this->outlines)>0)
			{
				$this->_put('/Outlines '.$this->outlineRoot.' 0 R');
				$this->_put('/PageMode /UseOutlines');
			}
		}

		protected function _putcatalog()
		{
			parent::_putcatalog();
			$this->_putbookmarkscatalog();
		}
	}
