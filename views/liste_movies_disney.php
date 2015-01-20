<?php
include("../includes/header.php");

// Récupération des films disney de la base
$sth = $dbh->prepare("SELECT * FROM disney_movies");
$sth->execute();

$result = $sth->fetchAll();

if( !$result ) { print_r($dbh->errorInfo()); echo "\n"; exit; }

// Récupération de l'année la plus récente des films répertoriés
$sth_last = $dbh->prepare("SELECT MAX(dm_annee) as last FROM disney_movies");
$sth_last->execute();
$last_annee = $sth_last->fetchAll();
if( !$last_annee ) { print_r($dbh->errorInfo()); echo "\n"; exit; }

echo "<div class = 'tabletitle' id='movie_disney'><h2>Les " .count($result) . " films Disney, jusqu'en " . $last_annee[0]['last'] . ".</h2></div>";
echo "<table class='boxoffice_table'>
		<thead>
			<tr><th>Film</th><th>Image</th><th>Ann&eacute;e</th><th>Oscars</th></tr>
		</thead>
		<tbody>
";

// Parcours des films
foreach($result as $key => $tb) {
    echo "
			<tr>
				<td><a href=\"" . $__url_wiki . utf8_decode($tb['dm_resource']) . "\">" . utf8_decode($tb['dm_titre']) . "</a></td>
				<td><img src=\"". utf8_decode($tb['dm_url_image']) . "\" width='150px' height='150px'/></td>
				<td>" . $tb['dm_annee'] . "</td>
				<td>
					<ul>";
	
	// Pour chaque film Disney, parcours des oscars
	$sth_oscars = $dbh->prepare("SELECT osc_dm_resource, osc_dm_titre FROM oscar_disney_movie WHERE osc_dm_id = ". $tb['dm_id']);
	$sth_oscars->execute();

	$oscars = $sth_oscars->fetchAll();
	foreach($oscars as $key => $osc) {
		echo "			<li><a href=\"" . $__url_wiki . utf8_decode($osc['osc_dm_resource']) . "\">" . utf8_decode($osc['osc_dm_titre']) . "</a></li>";
	}
	echo "
					</ul>
				</td>
			</tr>";   
}

echo "	</tbody>
	</table>";
unset($result);

include("../includes/footer.php");
?>
