<?php
function mix_tb_keyed($tb) {
	$keys = array_keys($tb);
	shuffle($keys);
	foreach($keys as $key) {
		$rnd_tb[$key] = $tb[$key];
	}
	$tb = $rnd_tb;
	unset($rnd_tb);
}

function create_liste_alpha($tb_result,$what,$by) {
	$result = "";
	
	$nb_tuples = count($tb_result);
	$url_href = "";
	
	// Gestion de la recherche de films
	if ($what == "movie") {
		// Par nom
		if ($by == "name") {
			$url_href = "../views/liste_movies_by_title.php";	
			$result .= "<div class ='recherche' id='liste_referer'><p>Recherche de films par titre</p>";
		}
		// Par acteur
		if ($by == "actor") {
			$url_href = "../views/liste_movies_by_actor.php";
			$result .= "<div class ='recherche' id='liste_referer'><p>Recherche de films par acteur</p>";
		}
		// Par réalisateur
		if ($by == "director") {
			$url_href = "../views/liste_movies_by_director.php";
			$result .= "<div class ='recherche' id='liste_referer'><p>Recherche de films par r&eacute;alisateur</p>";
		}
	}
	
	// Gestion de la recherche d'acteurs
	if ($what == "actor") {
		// Par nom
		if ($by == "name") {
			$url_href = "../views/liste_actors_by_name.php";	
			$result .= "<div class ='recherche' id='liste_referer'><p>Recherche d'acteurs par nom</p>";
		}
		// Par acteur
		if ($by == "movie") {
			$url_href = "../views/liste_actors_by_movie.php";
			$result .= "<div class ='recherche' id='liste_referer'><p>Recherche d'acteurs par film</p>";
		}
		// Par réalisateur
		if ($by == "director") {
			$url_href = "../views/liste_actors_by_director.php";
			$result .= "<div class ='recherche' id='liste_referer'><p>Recherche d'acteurs par r&eacute;alisateur les ayant dirig&eacute;s</p>";
		}
	}
	
	// Gestion de la recherche de réalisateurs
	if ($what == "director") {
		// Par nom
		if ($by == "name") {
			$url_href = "../views/liste_directors_by_name.php";	
			$result .= "<div class ='recherche' id='liste_referer'><p>Recherche de r&eacute;alisateurs par nom</p>";
		}
		// Par film
		if ($by == "movie") {
			$url_href = "../views/liste_directors_by_movie.php";
			$result .= "<div class ='recherche' id='liste_referer'><p>Recherche de r&eacute;alisateurs par film</p>";
		}
		// Par acteur dirigés
		if ($by == "actor") {
			$url_href = "../views/liste_directors_by_actor.php";
			$result .= "<div class ='recherche' id='liste_referer'><p>Recherche de r&eacute;alisateurs par acteurs dirig&eacute;s</p>";
		}
	}
	
	$cpt = 1;

	
	foreach ($tb_result as $key => $tb) {

		$result .= "<span><a href=\"".$url_href."?lettre=".ucwords($tb['lettre'])."\">".ucwords($tb['lettre'])."</a></span> (".$tb['nombre'].")  - ";
		if ($cpt == floor($nb_tuples/2)) $result .= "<br />";
		$cpt++;
	}
	$result .= "</div>";
	
	return $result;
}

?>