<?php
include("../includes/header.php");

// Récupération des acteurs à succès de la base
$sth = $dbh->prepare("SELECT * FROM success_actors ORDER BY sa_nom");
$sth->execute();

$result = $sth->fetchAll();

if( !$result ) { print_r($dbh->errorInfo()); echo "\n"; exit; }

// Récupération des films sans acteurs trouvés
$sth_no = $dbh-> prepare("SELECT sm_resource, sm_titre FROM success_movies WHERE id_success_m NOT IN (SELECT DISTINCT(id_sm) FROM link_sm_sa) ORDER BY sm_titre");
$sth_no->execute();
$result_no = $sth_no->fetchAll();

// Récupération de l'année la plus récente des films répertoriés
$sth_last = $dbh->prepare("SELECT MAX(sm_annee) as last FROM success_movies");
$sth_last->execute();
$last_annee = $sth_last->fetchAll();
if( !$last_annee ) { print_r($dbh->errorInfo()); echo "\n"; exit; }

echo "<div class = 'tabletitle' id='actor_success'><h2>Les " .count($result) . " acteurs du box-office mondial, jusqu'&agrave; " . $last_annee[0]['last'] . ".</h2></div>";
echo "<table class='boxoffice_table'>
		<thead>
			<tr><th>Acteur</th><th>Date de naissance</th><th>Photo</th><th>Nationalit&eacute;</th><th>Films au box-office</th></tr>
		</thead>
		<tbody>
";
echo "<tr>
				<td> - </td>
				<td> - </td>
				<td> - </td>
				<td> - </td>
				<td>
					<ul>";
foreach($result_no as $key => $movie) {
		echo "			<li><a href=\"" . $__url_wiki . utf8_decode($movie['sm_resource']) . "\">" . utf8_decode($movie['sm_titre']) . "</a></li>";
	}
	echo "
					</ul>
				</td>
			</tr>";   

// Parcours des acteurs
foreach($result as $key => $tb) {

	// Test d'existence des liens images
	/*$url_img = "";
	if ($tb['sa_url_image'] != "") {
		if (@fclose(@fopen($tb['sa_url_image'], "r"))) { 
			$url_img = $tb['sa_url_image'];
		} else { // Erreur 404 = réécriture des liens
			$url_img = str_replace("commons/thumb","fr",$tb['sa_url_image']);
			$url_img = substr($url_img,0,strrpos($url_img,"/"));
			
			if (@fclose(@fopen($url_img, "r"))) { 
				$url_img = $url_img;
			} else { 
				$url_img = "";
			}
		}
		
	}
	*/
    echo "
			<tr>
				<td><a href=\"" . $__url_wiki . utf8_decode($tb['sa_resource']) . "\">" . utf8_decode($tb['sa_nom']) . "</a><br />
					<a href=\"./liste_movies_by_actor_v2.php?id_actor=" . $tb['id_success_a'] . "\">Voir ses autres films</a>
				</td>
				<td>" . $tb['sa_naissance'] . "</td>
				<td><img src=\"". utf8_decode($tb['sa_url_image']) . "\" width='150px' height='150px'/></td>
				<td>" . utf8_decode($tb['sa_nationalite']) . "</td>
				<td>
					<ul>";
	
	// Pour chaque acteur, parcours des films
	$sth_idmovies = $dbh->prepare("SELECT id_sm FROM link_sm_sa WHERE id_sa = ". $tb['id_success_a']);
	$sth_idmovies->execute();

	$movies = $sth_idmovies->fetchAll();
	foreach($movies as $key => $id_mov) {
		$sth_movie = $dbh->prepare("SELECT sm_resource, sm_titre FROM success_movies WHERE id_success_m = ". $id_mov['id_sm']);
		$sth_movie->execute();
		$movie = $sth_movie->fetchAll();
		echo "			<li><a href=\"" . $__url_wiki . utf8_decode($movie[0]['sm_resource']) . "\">" . utf8_decode($movie[0]['sm_titre']) . "</a></li>";
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
