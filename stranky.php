<?php

	//vytvoreni pripojeni na databazi
	$db = new PDO(
		"mysql:host=localhost;dbname=primakavarna;charset=utf8",
		"root",
		"", // heslo
		array(
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		)	
	);

	class Stranka
	{
		public $id;
		public $titulek;
		public $menu;

		function __construct($id, $titulek, $menu)
		{
			$this -> id = $id;
			$this -> titulek = $titulek;
			$this -> menu = $menu;
		}

		function getObsah()
		{
      		//return file_get_contents("$this->id.html");
			//nacteni obsahu stranky z databaze
			global $db;

			$dotaz = $db->prepare("SELECT obsah FROM stranka WHERE id = ?");
        		$dotaz->execute([$this->id]);

			$vysledek = $dotaz -> fetch ();

			// pokud by databaze nic nevratila, tak vratime prazdny obsah
			if ($vysledek == false)
			{
				return "";
			}
			else 
			{
				return $vysledek["obsah"];
			}
		}

		function setObsah($obsah)
		{
			//file_put_contents("$this->id.html",$obsah);
			// ukladani stranky do databaze
			global $db;

			$dotaz=$db->prepare("UPDATE stranka set obsah = ? where id = ?");
			$dotaz ->execute ([$obsah,$this->id]);
		}

		function ulozit($puvodniId)
		{
			global $db;

			if ($puvodniId != "")
			{
				//jde o aktualizaci existujice stranky
				$dotaz=$db ->prepare("update stranka set id =?, titulek = ?, menu = ? WHERE id = ?");
				$dotaz->execute([$this->id,$this->titulek,$this->menu,$puvodniId]);
			}
			else
			{
				//jde o pridavani nove stranky
				//zjisteni max poradi
				$dotaz =$db->prepare("select MAX(poradi) as poradi from stranka");
				$dotaz->execute();
				$vysledek = $dotaz->fetch();
				$poradi = $vysledek["poradi"] + 1;

				$dotaz = $db->prepare("INSERT INTO stranka SET id = ?, titulek = ?, menu = ?, poradi = ?");
            		$dotaz->execute([$this->id, $this->titulek, $this->menu, $poradi]);
			}
		}

		function smazat()
		{
			global $db;

			$dotaz = $db->prepare ("DELETE FROM stranka WHERE id = ?");
			$dotaz -> execute ([$this->id]);
		}

		// funkce pro nastaveni pořadí
		static function nastavitPoradi($poradi)
		{
			global $db;

			//projdeme pole s poradim (cislovane pole)
			foreach ($poradi as $cislo => $idStranky)
			{
				$dotaz = $db->prepare ("UPDATE stranka SET poradi = ? WHERE $cislo = ?");
				$dotaz->execute([$cislo, $idStranky]);
			};
		}
	}


	/*
	$seznamStranek = 
	[
		"uvod" => new Stranka("uvod", "PrimaPenzion", "Domů"),
    		"nabidka" => new Stranka("nabidka", "PrimaPenzion - Nabídka", "Nabídka"),
    		"galerie" => new Stranka("galerie", "PrimaPenzion - Galerie", "Galerie"),
    		"rezervace" => new Stranka("rezervace", "PrimaPenzion - Rezervace", "Rezervace"),
    		"kontakt" => new Stranka("kontakt", "PrimaPenzion - Kontakt", "Kontakt"),
		"404" => new Stranka("404", "Stránka neexistuje", "")
	];
	*/

	$seznamStranek = [];
	// pole se seznamem stranek naplnime dynamicky z databaze
	$dotaz = $db->prepare("select id,titulek,menu from stranka order by poradi");
	$dotaz->execute();

	$stranky = $dotaz -> fetchall();

	// vezmeme pole radek, ktere nam vratila databaze a postupne
	// nakrmime pole seznamStranek jednotlivymi instancemi tridy Stranka

	foreach ($stranky as $stranka)
	{
		$idStranky = $stranka ["id"];
		// pridame do pole novou instanci tridy Stranka
		$seznamStranek[$idStranky] = new Stranka($idStranky, $stranka ["titulek"], $stranka["menu"]);
	};
?>
