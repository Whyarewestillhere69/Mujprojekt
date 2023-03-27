<?php
require "vendor/autoload.php";
require "stranky.php";

if (array_key_exists("stranka", $_GET)) 
{
	$stranka = $_GET["stranka"];

	//kontrola zda li stranka existuje (404)
	if (array_key_exists($stranka,$seznamStranek ) == false)
	{
		$stranka = "404";
	

	// odeslat informaci i vyhledavaci ze url neexistuje
	http_response_code(404);
	}
} 

else 
{	
	//zjistime prvni stranku z pole seznamStranek
	$stranka = array_key_first($seznamStranek);
}

?>
<!DOCTYPE html>
<html lang="cs">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo $seznamStranek[$stranka]->titulek?></title>
	<link rel="stylesheet" href="css/reset.css">
	<link rel="stylesheet" href="css/header.css">
	<link rel="stylesheet" href="css/section.css">
	<link rel="stylesheet" href="css/footer.css">
	<link rel="stylesheet" href="css/style.css">
	<link rel="stylesheet" href="../fontawesome/css/all.min.css">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Kaushan+Script&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
	<link rel="shortcut icon" href="./img kavarna/favicon.png" type="image/x-icon">
	<link rel="stylesheet" href="vendor/photoswipe/dist/photoswipe.css">
	<script
  		src="https://code.jquery.com/jquery-3.6.4.min.js"
  		integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8="
  		crossorigin="anonymous">
	</script>
</head>

<body>
	<header>
		<menu>
			<div class="container">
				<a href="./">
					<img src="img kavarna/logo.png" alt="Logo prima kavarna" width="142" height="80">
				</a>
				<nav>
					<ul>
						<?php
							foreach($seznamStranek as $idStranky => $instance)
							{
								if ($instance -> menu != "")
								{
								echo "<li><a href='$instance->id'>$instance->menu</a></li>";
								}
							};
						?>
					</ul>
				</nav>
			</div>
		</menu>
		<div class="nadpis">
			<h1>PrimaKavárna</h1>
			<h2>Jsme tu pro vás již od roku 2002</h3>
				<div class="social">
					<a href="https://www.facebook.com/" target="_blank"><i class="fa-brands fa-facebook"></i></a>
					<a href="https://www.instagram.com/" target="_blank"><i class="fa-brands fa-instagram"></i></a>
					<a href="https://www.youtube.com/" target="_blank"><i class="fa-brands fa-youtube"></i></a>
				</div>
		</div>
	</header>

	<section id="content">
		<?php
		$obsah = $seznamStranek[$stranka]->getObsah();
		echo primakurzy\Shortcode\Processor::process('shortcodes', $obsah);
		?>
	</section>

	<footer>
		<div class="container">
			<nav>
				<h3>Menu</h3>
				<ul>
					<?php
						foreach($seznamStranek as $idStranky => $instance)
						{
							if ($instance -> menu != "")
							{
							echo "<li><a href='?stranka=$instance->id'>$instance->menu</a></li>";
							}
						};
					?>
				</ul>
			</nav>
			<div class="kontakt">
				<h3>Kontakt</h3>
				<address>
					<a href="https://mapy.cz/s/pobarojoka" target="_blank">
						PrimaKavárna<br>
						Jablonského 2<br>
						Praha, Holešovice<br>
					</a>
				</address>
			</div>
			<div class="otevreno">
				<h3>Otevřeno</h3>
				<table>
					<tr>
						<th>Po - Pá:</th>
						<td>8h - 20h</td>
					</tr>
					<tr>
						<th>So</th>
						<td>10h - 22h</td>
					</tr>
					<tr>
						<th>Ne</th>
						<td>12h - 20h</td>
					</tr>
				</table>
			</div>
		</div>
	</footer>

	<!-- tlacitko pro návrat nahoru -->
	<div id="nahoru"><i class="fa-solid fa-angle-up"></i></div>
	<script src="js/index.js"></script>
</body>

</html>