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

echo "<h2>LES " .count($result) . " films Disney, jusqu'&agrave; " . $last_annee[0]['last'] . ".</h2>";
echo "<table class='boxoffice_table'>
		<tbody>
			<tr><th>Film</th><th>Une image</th><th>Ann&eacute;e</th><th>Oscars</th></tr>
";

// Parcours des films
foreach($result as $key => $tb) {
	$url_img = "";
	if ($tb['dm_url_image'] != "") {
		if (@fclose(@fopen($tb['dm_url_image'], "r"))) { 
			$url_img = $tb['dm_url_image'];
		} else { 
			$url_img = str_replace("commons/thumb","fr",$tb['dm_url_image']);
			$url_img = substr($url_img,0,strrpos($url_img,"/"));
			
			if (@fclose(@fopen($url_img, "r"))) { 
				$url_img = $url_img;
			} else { 
				$url_img = "";
			}
		}
		
	}
    echo "
			<tr>
				<td><a href=\"" . $__url_wiki . utf8_decode($tb['dm_resource']) . "\">" . utf8_decode($tb['dm_titre']) . "</a></td>
				<td><img src=\"". $url_img . "\" width='150px' height='150px'/></td>
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