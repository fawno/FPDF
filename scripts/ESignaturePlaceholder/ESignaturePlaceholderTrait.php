<?php

declare(strict_types=1);

namespace FPDF\Scripts\ESignaturePlaceholder;

trait ESignaturePlaceholderTrait {
    protected $acroformObjId = 0;
    protected $SigFields = [];

    /**
     * Add a e-signature placeholder
     *
     * @param float $x Abscissa of upper-left corner
     * @param float $y Ordinate of upper-left corner
     * @param float $w Width
     * @param string $h Height
     * @param string $name Name of pdf object
     */
    public function AddSignatureField($x, $y, $w, $h, $name = '')
    {
        $this->PageLinks[$this->page][] = compact('name', 'x', 'y', 'w', 'h');

        if (! isset($this->SigFields[$this->page])) {
            $this->SigFields[$this->page] = true;
            $this->PageLinks[$this->page][] = []; // just to reserve the oid on _putpages
        }
    }

    protected function _putlinks($n)
    {
        $this->_putsigfields($n);
        parent::_putlinks($n);
    }

    protected function _putsigfields($n)
    {
        $pageLinks = $this->PageLinks[$n];
        $this->PageLinks[$n] = [];
        $this->SigFields[$n] = [];
        foreach ($pageLinks as $pl)
        {
            if (isset($pl['name'])) // it's a sig placeholder
                $this->SigFields[$n][] = $pl;
            elseif (isset($pl[0])) // it's a page link
                $this->PageLinks[$n][] = $pl;
        }

        if (empty($this->SigFields[$n]))
            return;

        $fieldRefs = [];
        $page = $this->PageInfo[$n]['n'] . ' 0 R';

        foreach ($this->SigFields[$n] as $i => $f) {
            $this->_newobj();
            $fieldRefs[] = $this->n . ' 0 R';
            $name = $f['name'] ?: 'Sign_' . $n. '_' . $i;
            $rect = sprintf(
                '%.2F %.2F %.2F %.2F',
                $f['x'] * $this->k,
                ($this->h - $f['y'] - $f['h']) * $this->k,
                ($f['x'] + $f['w']) * $this->k,
                ($this->h - $f['y']) * $this->k
            );
            $this->_put("<</Type/Annot/Subtype/Widget/FT/Sig/V()/T({$name})/P {$page}/Rect[{$rect}]/F 132>>");
            $this->_put('endobj');
        }

        $this->_newobj();
        $this->acroformObjId = $this->n;
        $this->_put('<</SigFlags 3/Fields[' . implode(' ', $fieldRefs) . ']>>');
        $this->_put('endobj');
    }

    function _putcatalog()
    {
        parent::_putcatalog();
        $this->_putesignatureplaceholdercatalog();
    }

    protected function _putesignatureplaceholdercatalog() {
        if ($this->acroformObjId)
            $this->_put('/AcroForm ' . $this->acroformObjId . ' 0 R');
    }
}
