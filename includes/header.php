<html>
	<head>
		<title>Projet WEB - Quiz sur les films du box-office mondial</title>
		<meta http-equiv="Content-type" content="text/html; charset=iso-8859-1"/>
		<meta name="description" content="Projet WEB personnel de Alexandre LE et Elsa SAMIMI. Utilisation de données issues du Web de données 
		FR.DBPEDIA.ORG et requêtage en SPARQL. Site répertoriant les films du box-office mondial jusqu'en 2013, et leurs principaux acteurs."/>
		<meta name="keywords" content="sparql,virtuoso,dbpedia,wikipedia,siw,système,information,web,projet,données"/>
		<meta name="Content-Language" content="fr"/>
		<meta name="Author" content="Alexandre LE - Elsa SAMIMI"/>
		
		<link href="../css/boxoff.css" rel="stylesheet" type="text/css"/>
		<script src="../script/quizscript.js"></script>
		<link href='http://fonts.googleapis.com/css?family=Gabriela' rel='stylesheet' type='text/css'>

	</head>	
	<body>

<?php
// Importation de la librairie SPARQL et connexion à la base
require_once( "../lib/sparqllib.php" );

// Importation des fonctions utiles
require_once( "../lib/functions.php" );

$db = sparql_connect( "http://fr.dbpedia.org/sparql" );

if( !$db ) { print sparql_errno() . " : " . sparql_error(). "\n"; exit; }


$dsn = 'mysql:dbname=siw_projetweb;host=localhost';
$user = 'root';
$password = '';

try {
    $dbh = new PDO($dsn, $user, $password);
} catch (PDOException $e) {
    echo 'Connexion échouée : ' . $e->getMessage();
}

// Initialisation du namespace pour l'ontology
sparql_ns("dbpedia-owl","http://dbpedia.org/ontology/");

// Déclaration des variables 
// $__today = date("m-d");
$__url_wiki = "http://fr.wikipedia.org/wiki/";
$pattern = array("é","è","ë","ê","à","â","ï","î","ô","ö","ç","ù","û",":","'");
$__ressource = "http://fr.dbpedia.org/resource/";
?>
<div class = sitetitle>
	<h1>Bienvenue sur Cin&eacute;Quiz</h1>
	<h2>Etes-vous un vrai fan de Disney ? Un f&eacute;ru des films au box-office ?</h2>
	<h2>C'est le moment de vous tester !</h2>
	<h4>Petit conseil ... Consultez le site avant de passer au Quiz ... A part si vous &ecirc;tes incollable ;) </h4>
	
	<ul id="menu">
		<li class="onglet"><a>Liste des films</a>
			<ul class="ss_menu">
				<li><a href="../views/liste_movies_succes.php#movie_success">Au box-office</a></li>
				<li><a href="../views/liste_movies_disney.php#movie_disney">Films Disney</a></li>
				<li><a href="../views/liste_alpha.php?what=movie&by=name">Par titre</a></li>
				<li><a href="../views/liste_alpha.php?what=movie&by=actor">Par acteur</a></li>
				<li><a href="../views/liste_alpha.php?what=movie&by=director">Par r&eacute;alisateur</a></li>
			</ul>
		</li>
		<li class="onglet"><a>Liste des acteurs</a>
			<ul class="ss_menu">
				<li><a href="../views/liste_actors_succes.php#actor_success">Au box-office</a></li>
				<li><a href="../views/liste_alpha.php?what=actor&by=name">Par nom</a></li>
				<li><a href="../views/liste_alpha.php?what=actor&by=movie">Par film</a></li>
				<li><a href="../views/liste_alpha.php?what=actor&by=director">Par r&eacute;alisateur</a></li>
			</ul>
		</li>
		<li class="onglet"><a>Liste des r&eacute;alisateurs</a>
			<ul class="ss_menu">
				<li><a href="../views/liste_alpha.php?what=director&by=name">Par nom</a></li>
				<li><a href="../views/liste_alpha.php?what=director&by=movie">Par film**</a></li>
				<li><a href="../views/liste_alpha.php?what=director&by=actor">Par acteur dirig&eacute;**</a></li>
			</ul>
		</li>
		<li class="onglet"><a>Acc&eacute;der aux diff&eacute;nts quiz</a>
			<ul class="ss_menu">
				<li><a href="../views/quiz_actor.php?easy=yes">Acteurs au box-office</a></li>
				<li><a href="../views/quiz_actor.php?easy=no">Tous les acteurs</a></li>
				<li><a href="../views/quiz_movie.php">Sur les films</a></li>
				<li><a href="../views/quiz_disney.php">Quiz Disney</a></li>
			</ul>
		</li>
	</ul>
</div>


