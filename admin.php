<?php
require "stranky.php";

session_start();

$chyba = "";

if (array_key_exists("prihlasit", $_POST)) {
	$jmeno = $_POST["jmeno"];
	$heslo = $_POST["heslo"];

	if ($jmeno == "admin" && $heslo == "1234") {
		// uzivatel. zadal platne prihlasovaci udaje
		$_SESSION["prihlasenyUzivatel"] = $jmeno;
	} else {
		// spatne prihlasovaci udaje
		$chyba = "Nesprávné přihlašovací údaje";
	}
}

//zpracovani odhlaseni
if (array_key_exists("odhlasit", $_POST)) {
	unset($_SESSION["prihlasenyUzivatel"]);
	header("Location:?");
}

// zpracovani akci v administraci je pouze pro prihlasene uzivatele
if (array_key_exists("prihlasenyUzivatel", $_SESSION)) {
	//promena predstavujice editovanou stranku
	$instanceAktualniStranky = null;


	//zpracovani vyberu aktualni stranky
	if (array_key_exists("stranka", $_GET)) {
		$idStranky = $_GET["stranka"];
		$instanceAktualniStranky = $seznamStranek[$idStranky];
	}

	// zpracovani tlacitka pridat
	if(array_key_exists ("pridat",$_GET))
	{
		$instanceAktualniStranky = new Stranka("","","");
	}

	// zpracovani mazani
	if (array_key_exists("smazat", $_GET))
	{
	    $instanceAktualniStranky->smazat();
	    //po smazani stranky se musime presmerovat domu
	    header("Location:?");
	}

	// zpracovani formulare pro ulozeni
	if (array_key_exists("ulozit", $_POST)) {

		// poznamename si puvodni id nez si ho prepiseme
		$puvodniId = $instanceAktualniStranky->id;

		$instanceAktualniStranky->id = $_POST["id"];
        	$instanceAktualniStranky->titulek = $_POST["titulek"];
        	$instanceAktualniStranky->menu = $_POST["menu"];
		// zavolame funkci pro ulozeni zmenenych hodnod do databaze
		$instanceAktualniStranky->ulozit($puvodniId);

		//ukladani obsahu stranky
		$obsah = $_POST["obsah"];
		$instanceAktualniStranky->setObsah($obsah);

		// presmerujeme se na url s editaci stranky s novym id
        	// (kdyby se id zmenilo tak nesmime zustat na puvodni url)
		header("Location: ?stranka=".urlencode($instanceAktualniStranky->id));
	}

	//zpracovani pozadavku změny pořadí stránek z javascriptu ajaxem
	if (array_key_exists("poradi", $_GET))
	{
		$poradi = $_GET("poradi");

		// zavolani funkce pro nastaveni poradi a ulozeni do databaze
		Stranka::nastavitPoradi($poradi);
	
		//odpovime v JS ze je to ok
		echo "OK";
		// script ukoncime aby se do javasriptu negeneroval zbytek stranky
		exit;
	}
}


	

?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Administrace</title>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
	<link rel="stylesheet" href="css/admin.css">

	<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
  	<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
</head>


<body>
	<div class="admin-body">
	<?php
	if (array_key_exists("prihlasenyUzivatel", $_SESSION) == false) {
		// sekce pro neprihlasene uzivatele
	?>
		<main class="form-signin w-100 m-auto">
			<form method="post">
			<h1 class="h3 mb-3 fw-normal">Přihlašte se</h1>

			<?php if ($chyba != "") { ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo $chyba; ?>
                        </div>
                    <?php } ?>

			<div class="form-floating">
				<input type="text" name="jmeno" class="form-control" id="floatingInput" placeholder="Login">
				<label for="floatingInput">Přihlasovací jmeno</label>
			</div>
			<div class="form-floating">
				<input type="password" name="heslo" class="form-control" id="floatingPassword" placeholder="Heslo">
				<label for="floatingPassword">Heslo</label>
			</div>

			<button name="prihlasit" class="w-100 btn btn-lg btn-primary" type="submit">Prihlásit</button>
			</form>
		</main>

		<?php
		
	}

	// sekce pro prihlasene uzivatele
	else {
		echo "<main class='admin'>";
		?>
		<div class="container">
			<header class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-between py-3 mb-4 border-bottom">
     				<div>Prihlasený uživatel: <?php echo $_SESSION['prihlasenyUzivatel'] ?> </div>
    			<div class="col-md-3 text-end">
				<form method='post'>
					<button name='odhlasit' class="btn btn-outline-primary me-2">Odhlásit</button>
				</form>
			</div>
			</header>
  		</div>
		<?php

		// vypiseme seznam stranek pro editaci
		echo "<ul id='stranky' class='list-group'>";
		foreach ($seznamStranek as $idStranky => $instance) {
			$active = "";
			$buttonClass ="btn-primary";
			if ($instance == $instanceAktualniStranky)
			{
				$active= "active";
				$buttonClass ="btn-danger" ;
			}
			echo "<li class='list-group-item $active' id='$instance->id'>
				<a class='btn $buttonClass' href='?stranka=$instance->id'><i class='fa-solid fa-pen-to-square'></i></a>
				
				<a class='smazat btn $buttonClass' href='?stranka=$instance->id&smazat'><i class='fa-solid fa-trash-can'></i></a>

				<a class='btn $buttonClass' href='$instance->id' target='_blank'><i class='fa-solid fa-eye'></i></a>

				<span> $instance->id </span>
				</li>";
		}
		echo "</ul>";

		//formular pro vypsani stranky
		?>
		<div class="container">
			<header class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-between py-3 mb-4 border-bottom">
    			<div class="col-md-3 text-start">
			    	<form>
                            <button name='pridat' class="btn btn-outline-primary me-2">Přidat</button>
                  	</form>
			</div>
			</header>
  		</div>
		<?php
		// editacni formular
		// zobbrazit pokud je nejaka stranka vybrana k editaci
		if ($instanceAktualniStranky != null) {
			

			echo "<div class='alert alert-secondary' role='alert'><h1>";
                		if ($instanceAktualniStranky->id == "")
               		{
                  		echo "Přidávání stránky";
                		}
                		else
                		{
                    		echo "Editace stránky: $instanceAktualniStranky->id";
               		}
                		echo "</h1></div>";

		?>

			<form method="post">
				<div class="form-floating">
					<input placeholder="id" class="form-control" type="text" name="id" id="id" value="<?php echo htmlspecialchars($instanceAktualniStranky->id) ?>">
					<label for="id">id:</label>
				</div>

				<div class="form-floating">
					<input placeholder="titulek" class="form-control" type="text" name="titulek" id="titulek" value="<?php echo htmlspecialchars($instanceAktualniStranky->titulek) ?>">
					<label for="titulek">Titulek</label>
				</div>

				<div class="form-floating">
					<input placeholder="menu" class="form-control" type="text" name="menu" id="menu" value="<?php echo htmlspecialchars($instanceAktualniStranky->menu) ?>">
					<label for="menu">Menu</label>
				</div>

				<textarea id="obsah" name="obsah" cols="80" rows="15">
				<?php
				echo htmlspecialchars($instanceAktualniStranky->getObsah());
				?>
				</textarea>
				<br>
				<button name="ulozit" class="btn btn-primary">Uložit</button>
			</form>
			<script src="vendor/tinymce/tinymce/tinymce.min.js"></script>
            	<script type="text/javascript">
                	tinymce.init({
                  	selector: '#obsah', 
				language: 'cs',
    				language_url:'/<?php echo dirname ($_SERVER["PHP_SELF"]); ?>/vendor\tweeb\tinymce-i18n\langs\cs.js',
				height : '80vh',
				entity_encoding: "raw",
				verify_html: false,
				content_css: [
					'css/reset.css',
					'css/section.css',
					'css/style.css',
					'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css',
					'https://fonts.googleapis.com/css2?family=Kaushan+Script&display=swap',
				],
				body_id:"content",
				plugins: 'advlist anchor autolink charmap code colorpicker contextmenu directionality emoticons fullscreen hr image imagetools insertdatetime link lists nonbreaking noneditable pagebreak paste preview print save searchreplace tabfocus table textcolor textpattern visualchars',
				toolbar1:"insertfile undo redo | styleselect | fontselect | fontsizeselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | forecolor backcolor",
				toolbar2:"link unlink anchor | fontawesome | image media | responsivefilemanager | preview code",
				external_plugins: {
					'responsivefilemanager': '<?php echo dirname($_SERVER['PHP_SELF']); ?>/vendor/primakurzy/responsivefilemanager/tinymce/plugins/responsivefilemanager/plugin.min.js',
				},
				external_filemanager_path: "<?php echo dirname($_SERVER['PHP_SELF']); ?>/vendor/primakurzy/responsivefilemanager/filemanager/",
				filemanager_title: "správce souborů",
			});
            	</script>
	<?php
		}
		echo "</main>";
	}
	?>
	</div>

	<script src="js/admin.js"></script>
</body>

</html>