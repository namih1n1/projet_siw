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
	if ($what == "movie") {
		if ($by == "name") {
			$url_href = "../views/liste_movies_by_title.php";	
			$result .= "<div class ='recherche' id='liste_referer'><p>Recherche de films par titre</p>";
		}
		
		if ($by == "actor") {
			$url_href = "../views/liste_movies_by_actor.php";
			$result .= "<div class ='recherche' id='liste_referer'><p>Recherche de films par acteur</p>";
		}
		
		if ($by == "director") {
			$url_href = "../views/liste_movies_by_director.php";
			$result .= "<div class ='recherche' id='liste_referer'><p>Recherche de films par r&eacute;alisateur</p>";
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