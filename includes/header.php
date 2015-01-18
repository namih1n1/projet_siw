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
		<link href='http://fonts.googleapis.com/css?family=Gabriela' rel='stylesheet' type='text/css'>

	</head>	
	<body>

<?php
// Importation de la librairie SPARQL et connexion à la base
require_once( "../lib/sparqllib.php" );
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
	<h1>Bienvenue sur QuizMovies</h1>
	<ul id="menu">
		<li><a href="../views/liste_movies_succes.php">Films au box-office mondial</a></li>
		<li><a href="../views/liste_movies_disney.php">Films Disney</a></li>
		<li><a href="../views/liste_actors_succes.php">Acteurs au box-office mondial</a></li>
		<li><a href="">Films r&eacute;f&eacute;renc&eacute;s</a></li>
	</ul>
</div>
