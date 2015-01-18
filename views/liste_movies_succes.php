<?php
include("../includes/header.php");

// Récupération des films à succès de la base
$sth = $dbh->prepare("SELECT * FROM success_movies");
$sth->execute();

$result = $sth->fetchAll();

if( !$result ) { print_r($dbh->errorInfo()); echo "\n"; exit; }

// Récupération de l'année la plus récente des films répertoriés
$sth_last = $dbh->prepare("SELECT MAX(sm_annee) as last FROM success_movies");
$sth_last->execute();
$last_annee = $sth_last->fetchAll();
if( !$last_annee ) { print_r($dbh->errorInfo()); echo "\n"; exit; }

echo "<div class = 'tabletitle'><h2>Les " .count($result) . " films du box-office mondial, jusqu'&agrave; " . $last_annee[0]['last'] . ".</h2></div>";
echo "<table class='boxoffice_table'>
		<thead>
			<tr><th>Film</th><th>Image</th><th>Ann&eacute;e</th><th>Acteurs</th></tr>
		</thead>
		<tbody>
";

// Parcours des films
foreach($result as $key => $tb) {
    echo "
			<tr>
				<td><a href=\"" . $__url_wiki . utf8_decode($tb['sm_resource']) . "\">" . utf8_decode($tb['sm_titre']) . "</a></td>
				<td><img src=\"". utf8_decode($tb['sm_url_image']) . "\" width='150px' height='150px'/></td>
				<td>" . $tb['sm_annee'] . "</td>
				<td>
					<ul>";
	
	// Pour chaque film, parcours des acteurs
	$sth_idactors = $dbh->prepare("SELECT id_sa FROM link_sm_sa WHERE id_sm = ". $tb['id_success_m']);
	$sth_idactors->execute();

	$actors = $sth_idactors->fetchAll();
	foreach($actors as $key => $id_act) {
		$sth_actor = $dbh->prepare("SELECT sa_resource, sa_nom FROM success_actors WHERE id_success_a = ". $id_act['id_sa']);
		$sth_actor->execute();
		$actor = $sth_actor->fetchAll();
		echo "			<li><a href=\"" . $__url_wiki . utf8_decode($actor[0]['sa_resource']) . "\">" . utf8_decode($actor[0]['sa_nom']) . "</a></li>";
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
