<?php
	declare(strict_types=1);

	namespace FPDF\Scripts\PDFCode128;
	//http://www.fpdf.org/en/script/script88.php

	/*******************************************************************************
	* Script :  PDF_Code128
	* Version : 1.2
	* Date :	2016-01-31
	* Auteur :  Roland Gautier
	*
	* Version   Date		Detail
	* 1.2	   2016-01-31  Compatibility with FPDF 1.8
	* 1.1	   2015-04-10  128 control characters FNC1 to FNC4 accepted
	* 1.0	   2008-05-20  First release
	*
	* Code128($x, $y, $code, $w, $h)
	*	 $x,$y :	 angle supérieur gauche du code à barre
	*				 upper left corner of the barcode
	*	 $code :	 le code à créer
	*				 ascii text to convert to barcode
	*	 $w :		largeur hors tout du code dans l'unité courante
	*				 (prévoir 5 à 15 mm de blanc à droite et à gauche)
	*				 barcode total width (current unit)
	*				 (keep 5 to 15 mm white on left and right sides)
	*	 $h :		hauteur hors tout du code dans l'unité courante
	*				 barcode total height (current unit)
	*
	* Commutation des jeux ABC automatique et optimisée
	* Automatic and optimized A/B/C sets selection and switching
	*
	*
	*   128 barcode control characters
	*   ASCII   Aset			Bset		[ne pas utiliser][do not use]
	*   ---------------------------
	*   200	 FNC3			FNC3
	*   201	 FNC2			FNC2
	*   202	 ShiftA		  ShiftB
	*   203	 [SwitchToCset]  [SwitchToCset]
	*   204	 [SwitchToBset]  FNC4
	*   205	 FNC4			[SwitchToAset]
	*   206	 FNC1			FNC1
	*******************************************************************************/

	trait PDFCode128Trait {
		private static $T128;      // Tableau des codes 128
		private static $ABCset;    // jeu des caractères éligibles au C128
		private static $Aset;      // Set A du jeu des caractères éligibles
		private static $Bset;      // Set B du jeu des caractères éligibles
		private static $Cset;      // Set C du jeu des caractères éligibles
		private static $SetFrom;   // Convertisseur source des jeux vers le tableau
		private static $SetTo;     // Convertisseur destination des jeux vers le tableau
		private static $JStart;    // Caractères de sélection de jeu au début du C128
		private static $JSwap;     // Caractères de changement de jeu

		private function code128_data_contructor () : void {
			if (!empty(self::$T128)) {
				return;
			}

			self::$JStart = ['A' => 103, 'B' => 104, 'C' => 105];
			self::$JSwap =  ['A' => 101, 'B' => 100, 'C' => 99];

			self::$T128 = [								// Tableau des codes 128
				[2, 1, 2, 2, 2, 2],		   //0 : [ ]	// composition des caractères
				[2, 2, 2, 1, 2, 2],		   //1 : [!]
				[2, 2, 2, 2, 2, 1],		   //2 : ["]
				[1, 2, 1, 2, 2, 3],		   //3 : [#]
				[1, 2, 1, 3, 2, 2],		   //4 : [$]
				[1, 3, 1, 2, 2, 2],		   //5 : [%]
				[1, 2, 2, 2, 1, 3],		   //6 : [&]
				[1, 2, 2, 3, 1, 2],		   //7 : [']
				[1, 3, 2, 2, 1, 2],		   //8 : [(]
				[2, 2, 1, 2, 1, 3],		   //9 : [)]
				[2, 2, 1, 3, 1, 2],		   //10 : [*]
				[2, 3, 1, 2, 1, 2],		   //11 : [+]
				[1, 1, 2, 2, 3, 2],		   //12 : [,]
				[1, 2, 2, 1, 3, 2],		   //13 : [-]
				[1, 2, 2, 2, 3, 1],		   //14 : [.]
				[1, 1, 3, 2, 2, 2],		   //15 : [/]
				[1, 2, 3, 1, 2, 2],		   //16 : [0]
				[1, 2, 3, 2, 2, 1],		   //17 : [1]
				[2, 2, 3, 2, 1, 1],		   //18 : [2]
				[2, 2, 1, 1, 3, 2],		   //19 : [3]
				[2, 2, 1, 2, 3, 1],		   //20 : [4]
				[2, 1, 3, 2, 1, 2],		   //21 : [5]
				[2, 2, 3, 1, 1, 2],		   //22 : [6]
				[3, 1, 2, 1, 3, 1],		   //23 : [7]
				[3, 1, 1, 2, 2, 2],		   //24 : [8]
				[3, 2, 1, 1, 2, 2],		   //25 : [9]
				[3, 2, 1, 2, 2, 1],		   //26 : [:]
				[3, 1, 2, 2, 1, 2],		   //27 : [;]
				[3, 2, 2, 1, 1, 2],		   //28 : [<]
				[3, 2, 2, 2, 1, 1],		   //29 : [=]
				[2, 1, 2, 1, 2, 3],		   //30 : [>]
				[2, 1, 2, 3, 2, 1],		   //31 : [?]
				[2, 3, 2, 1, 2, 1],		   //32 : [@]
				[1, 1, 1, 3, 2, 3],		   //33 : [A]
				[1, 3, 1, 1, 2, 3],		   //34 : [B]
				[1, 3, 1, 3, 2, 1],		   //35 : [C]
				[1, 1, 2, 3, 1, 3],		   //36 : [D]
				[1, 3, 2, 1, 1, 3],		   //37 : [E]
				[1, 3, 2, 3, 1, 1],		   //38 : [F]
				[2, 1, 1, 3, 1, 3],		   //39 : [G]
				[2, 3, 1, 1, 1, 3],		   //40 : [H]
				[2, 3, 1, 3, 1, 1],		   //41 : [I]
				[1, 1, 2, 1, 3, 3],		   //42 : [J]
				[1, 1, 2, 3, 3, 1],		   //43 : [K]
				[1, 3, 2, 1, 3, 1],		   //44 : [L]
				[1, 1, 3, 1, 2, 3],		   //45 : [M]
				[1, 1, 3, 3, 2, 1],		   //46 : [N]
				[1, 3, 3, 1, 2, 1],		   //47 : [O]
				[3, 1, 3, 1, 2, 1],		   //48 : [P]
				[2, 1, 1, 3, 3, 1],		   //49 : [Q]
				[2, 3, 1, 1, 3, 1],		   //50 : [R]
				[2, 1, 3, 1, 1, 3],		   //51 : [S]
				[2, 1, 3, 3, 1, 1],		   //52 : [T]
				[2, 1, 3, 1, 3, 1],		   //53 : [U]
				[3, 1, 1, 1, 2, 3],		   //54 : [V]
				[3, 1, 1, 3, 2, 1],		   //55 : [W]
				[3, 3, 1, 1, 2, 1],		   //56 : [X]
				[3, 1, 2, 1, 1, 3],		   //57 : [Y]
				[3, 1, 2, 3, 1, 1],		   //58 : [Z]
				[3, 3, 2, 1, 1, 1],		   //59 : [[]
				[3, 1, 4, 1, 1, 1],		   //60 : [\]
				[2, 2, 1, 4, 1, 1],		   //61 : []]
				[4, 3, 1, 1, 1, 1],		   //62 : [^]
				[1, 1, 1, 2, 2, 4],		   //63 : [_]
				[1, 1, 1, 4, 2, 2],		   //64 : [`]
				[1, 2, 1, 1, 2, 4],		   //65 : [a]
				[1, 2, 1, 4, 2, 1],		   //66 : [b]
				[1, 4, 1, 1, 2, 2],		   //67 : [c]
				[1, 4, 1, 2, 2, 1],		   //68 : [d]
				[1, 1, 2, 2, 1, 4],		   //69 : [e]
				[1, 1, 2, 4, 1, 2],		   //70 : [f]
				[1, 2, 2, 1, 1, 4],		   //71 : [g]
				[1, 2, 2, 4, 1, 1],		   //72 : [h]
				[1, 4, 2, 1, 1, 2],		   //73 : [i]
				[1, 4, 2, 2, 1, 1],		   //74 : [j]
				[2, 4, 1, 2, 1, 1],		   //75 : [k]
				[2, 2, 1, 1, 1, 4],		   //76 : [l]
				[4, 1, 3, 1, 1, 1],		   //77 : [m]
				[2, 4, 1, 1, 1, 2],		   //78 : [n]
				[1, 3, 4, 1, 1, 1],		   //79 : [o]
				[1, 1, 1, 2, 4, 2],		   //80 : [p]
				[1, 2, 1, 1, 4, 2],		   //81 : [q]
				[1, 2, 1, 2, 4, 1],		   //82 : [r]
				[1, 1, 4, 2, 1, 2],		   //83 : [s]
				[1, 2, 4, 1, 1, 2],		   //84 : [t]
				[1, 2, 4, 2, 1, 1],		   //85 : [u]
				[4, 1, 1, 2, 1, 2],		   //86 : [v]
				[4, 2, 1, 1, 1, 2],		   //87 : [w]
				[4, 2, 1, 2, 1, 1],		   //88 : [x]
				[2, 1, 2, 1, 4, 1],		   //89 : [y]
				[2, 1, 4, 1, 2, 1],		   //90 : [z]
				[4, 1, 2, 1, 2, 1],		   //91 : [{]
				[1, 1, 1, 1, 4, 3],		   //92 : [|]
				[1, 1, 1, 3, 4, 1],		   //93 : [}]
				[1, 3, 1, 1, 4, 1],		   //94 : [~]
				[1, 1, 4, 1, 1, 3],		   //95 : [DEL]
				[1, 1, 4, 3, 1, 1],		   //96 : [FNC3]
				[4, 1, 1, 1, 1, 3],		   //97 : [FNC2]
				[4, 1, 1, 3, 1, 1],		   //98 : [SHIFT]
				[1, 1, 3, 1, 4, 1],		   //99 : [Cswap]
				[1, 1, 4, 1, 3, 1],		   //100 : [Bswap]
				[3, 1, 1, 1, 4, 1],		   //101 : [Aswap]
				[4, 1, 1, 1, 3, 1],		   //102 : [FNC1]
				[2, 1, 1, 4, 1, 2],		   //103 : [Astart]
				[2, 1, 1, 2, 1, 4],		   //104 : [Bstart]
				[2, 1, 1, 2, 3, 2],		   //105 : [Cstart]
				[2, 3, 3, 1, 1, 1],		   //106 : [STOP]
				[2, 1],					   //107 : [END BAR]
			];

			self::$Aset = "\x20\x21\x22\x23\x24\x25\x26\x27\x28\x29\x2A\x2B\x2C\x2D\x2E\x2F\x30\x31\x32\x33\x34\x35\x36\x37\x38\x39\x3A\x3B\x3C\x3D\x3E\x3F\x40\x41\x42\x43\x44\x45\x46\x47\x48\x49\x4A\x4B\x4C\x4D\x4E\x4F\x50\x51\x52\x53\x54\x55\x56\x57\x58\x59\x5A\x5B\x5C\x5D\x5E\x5F\x00\x01\x02\x03\x04\x05\x06\x07\x08\x09\x0A\x0B\x0C\x0D\x0E\x0F\x10\x11\x12\x13\x14\x15\x16\x17\x18\x19\x1A\x1B\x1C\x1D\x1E\x1F\xC8\xC9\xCA\xCB\xCC\xCD\xCE\xCF\xD0\xD1\xD2";
			self::$Bset = "\x20\x21\x22\x23\x24\x25\x26\x27\x28\x29\x2A\x2B\x2C\x2D\x2E\x2F\x30\x31\x32\x33\x34\x35\x36\x37\x38\x39\x3A\x3B\x3C\x3D\x3E\x3F\x40\x41\x42\x43\x44\x45\x46\x47\x48\x49\x4A\x4B\x4C\x4D\x4E\x4F\x50\x51\x52\x53\x54\x55\x56\x57\x58\x59\x5A\x5B\x5C\x5D\x5E\x5F\x60\x61\x62\x63\x64\x65\x66\x67\x68\x69\x6A\x6B\x6C\x6D\x6E\x6F\x70\x71\x72\x73\x74\x75\x76\x77\x78\x79\x7A\x7B\x7C\x7D\x7E\x7F\xC8\xC9\xCA\xCB\xCC\xCD\xCE\xCF\xD0\xD1\xD2";
			self::$Cset = "0123456789\xCE";

			self::$SetFrom = [
				'A' => "\x00\x01\x02\x03\x04\x05\x06\x07\x08\x09\x0A\x0B\x0C\x0D\x0E\x0F\x10\x11\x12\x13\x14\x15\x16\x17\x18\x19\x1A\x1B\x1C\x1D\x1E\x1F\x20\x21\x22\x23\x24\x25\x26\x27\x28\x29\x2A\x2B\x2C\x2D\x2E\x2F\x30\x31\x32\x33\x34\x35\x36\x37\x38\x39\x3A\x3B\x3C\x3D\x3E\x3F\x40\x41\x42\x43\x44\x45\x46\x47\x48\x49\x4A\x4B\x4C\x4D\x4E\x4F\x50\x51\x52\x53\x54\x55\x56\x57\x58\x59\x5A\x5B\x5C\x5D\x5E\x5F\xC8\xC9\xCA\xCB\xCC\xCD\xCE\xCF\xD0\xD1\xD2",
				'B' => "\x20\x21\x22\x23\x24\x25\x26\x27\x28\x29\x2A\x2B\x2C\x2D\x2E\x2F\x30\x31\x32\x33\x34\x35\x36\x37\x38\x39\x3A\x3B\x3C\x3D\x3E\x3F\x40\x41\x42\x43\x44\x45\x46\x47\x48\x49\x4A\x4B\x4C\x4D\x4E\x4F\x50\x51\x52\x53\x54\x55\x56\x57\x58\x59\x5A\x5B\x5C\x5D\x5E\x5F\x60\x61\x62\x63\x64\x65\x66\x67\x68\x69\x6A\x6B\x6C\x6D\x6E\x6F\x70\x71\x72\x73\x74\x75\x76\x77\x78\x79\x7A\x7B\x7C\x7D\x7E\x7F\xC8\xC9\xCA\xCB\xCC\xCD\xCE\xCF\xD0\xD1\xD2",
            ];
			self::$SetTo = [
				'A' => "\x40\x41\x42\x43\x44\x45\x46\x47\x48\x49\x4A\x4B\x4C\x4D\x4E\x4F\x50\x51\x52\x53\x54\x55\x56\x57\x58\x59\x5A\x5B\x5C\x5D\x5E\x5F\x00\x01\x02\x03\x04\x05\x06\x07\x08\x09\x0A\x0B\x0C\x0D\x0E\x0F\x10\x11\x12\x13\x14\x15\x16\x17\x18\x19\x1A\x1B\x1C\x1D\x1E\x1F\x20\x21\x22\x23\x24\x25\x26\x27\x28\x29\x2A\x2B\x2C\x2D\x2E\x2F\x30\x31\x32\x33\x34\x35\x36\x37\x38\x39\x3A\x3B\x3C\x3D\x3E\x3F\x60\x61\x62\x63\x64\x65\x66\x67\x68\x69\x6A",
				'B' => "\x00\x01\x02\x03\x04\x05\x06\x07\x08\x09\x0A\x0B\x0C\x0D\x0E\x0F\x10\x11\x12\x13\x14\x15\x16\x17\x18\x19\x1A\x1B\x1C\x1D\x1E\x1F\x20\x21\x22\x23\x24\x25\x26\x27\x28\x29\x2A\x2B\x2C\x2D\x2E\x2F\x30\x31\x32\x33\x34\x35\x36\x37\x38\x39\x3A\x3B\x3C\x3D\x3E\x3F\x40\x41\x42\x43\x44\x45\x46\x47\x48\x49\x4A\x4B\x4C\x4D\x4E\x4F\x50\x51\x52\x53\x54\x55\x56\x57\x58\x59\x5A\x5B\x5C\x5D\x5E\x5F\x60\x61\x62\x63\x64\x65\x66\x67\x68\x69\x6A",
			];
		}

		/**
		 * Encodage et dessin du code 128
		 *
		 * @param float $x Abscissa of upper-left corner
		 * @param float $y Ordinate of upper-left corner
		 * @param string $code Barcode value
		 * @param float $w Width
		 * @param float $h Height
		 * @return void
		 */
		public function Code128 (float $x, float $y, string $code, float $w, float $h) : void {
			$this->code128_data_contructor();

			$Aguid = "";																	  // Création des guides de choix ABC
			$Bguid = "";
			$Cguid = "";
			for ($i=0; $i < strlen($code); $i++) {
				$needle = substr($code,$i,1);
				$Aguid .= ((strpos(self::$Aset,$needle)===false) ? "N" : "O");
				$Bguid .= ((strpos(self::$Bset,$needle)===false) ? "N" : "O");
				$Cguid .= ((strpos(self::$Cset,$needle)===false) ? "N" : "O");
			}

			$SminiC = "OOOO";
			$IminiC = 4;

			$crypt = "";
			while ($code > "") {
																							// BOUCLE PRINCIPALE DE CODAGE
				$i = strpos($Cguid,$SminiC);												// forçage du jeu C, si possible
				if ($i!==false) {
					$Aguid [$i] = "N";
					$Bguid [$i] = "N";
				}

				if (substr($Cguid,0,$IminiC) == $SminiC) {								  // jeu C
					$crypt .= chr(($crypt > "") ? self::$JSwap["C"] : self::$JStart["C"]);  // début Cstart, sinon Cswap
					$made = strpos($Cguid,"N");											 // étendu du set C
					if ($made === false) {
						$made = strlen($Cguid);
					}
					if (fmod($made,2)==1) {
						$made--;															// seulement un nombre pair
					}
					for ($i=0; $i < $made; $i += 2) {
						$crypt .= chr((int) strval(substr($code,$i,2)));						  // conversion 2 par 2
					}
					$jeu = "C";
				} else {
					$madeA = strpos($Aguid,"N");											// étendu du set A
					if ($madeA === false) {
						$madeA = strlen($Aguid);
					}
					$madeB = strpos($Bguid,"N");											// étendu du set B
					if ($madeB === false) {
						$madeB = strlen($Bguid);
					}
					$made = (($madeA < $madeB) ? $madeB : $madeA );						 // étendu traitée
					$jeu = (($madeA < $madeB) ? "B" : "A" );								// Jeu en cours

					$crypt .= chr(($crypt > "") ? self::$JSwap[$jeu] : self::$JStart[$jeu]); // début start, sinon swap

					$crypt .= strtr(substr($code, 0,$made), self::$SetFrom[$jeu], self::$SetTo[$jeu]); // conversion selon jeu

				}
				$code = substr($code,$made);										   // raccourcir légende et guides de la zone traitée
				$Aguid = substr($Aguid,$made);
				$Bguid = substr($Bguid,$made);
				$Cguid = substr($Cguid,$made);
			}																		  // FIN BOUCLE PRINCIPALE

			$check = ord($crypt[0]);												   // calcul de la somme de contrôle
			for ($i=0; $i<strlen($crypt); $i++) {
				$check += (ord($crypt[$i]) * $i);
			}
			$check %= 103;

			$crypt .= chr($check) . chr(106) . chr(107);							   // Chaine cryptée complète

			$i = (strlen($crypt) * 11) - 8;											// calcul de la largeur du module
			$modul = $w/$i;

			for ($i=0; $i<strlen($crypt); $i++) {									  // BOUCLE D'IMPRESSION
				$c = self::$T128[ord($crypt[$i])];
				for ($j=0; $j<count($c); $j++) {
					$this->Rect($x,$y,$c[$j]*$modul,$h,"F");
					$x += ($c[$j++]+$c[$j])*$modul;
				}
			}
		}
	}
