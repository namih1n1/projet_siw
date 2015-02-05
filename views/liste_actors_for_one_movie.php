<?php
include("../includes/header.php");
$movie_id = ( isset($_GET['id_movie']) ) ? $_GET['id_movie'] : null;

// Infos movie
$sth_movie = $dbh->prepare("SELECT mov_titre FROM movies WHERE id_mov = ".$movie_id);
$sth_movie->execute();
$movie = $sth_movie->fetchAll();

$sth_actors = $dbh->prepare("
	SELECT 	a.*
	FROM 	actors a,
			link_movies_actors l
	WHERE 	a.id_act = l.id_act
	AND		l.id_mov = ".$movie_id."
	ORDER BY a.act_nom");
$sth_actors->execute();
$actors = $sth_actors->fetchAll();

echo "<div class = 'tabletitle' id='actorlist'><h2>" .count($actors) . " acteurs du film " . $movie[0]['mov_titre']. ".</h2></div>";
echo "<table>
	<thead>
        <tr>
			<th>Acteurs</th><th>Photo</th><th>Date de naissance</th><th>Au box-office</th>
		</tr>
	</thead>
	<tbody>
";

// Parcours des acteurs

foreach ($actors as $key => $tb) {
	$traitement_nom = trim(utf8_decode($tb['act_nom']));
	$image = (utf8_decode($tb['act_url_image']) == "") ? "Pas de photo" : "<img src=\"".utf8_decode($tb['act_url_image'])."\" width='200px' height='auto' />";
	if ($tb['act_naissance'] != "0000-00-00") {
		$tb_naiss  = date_parse($tb['act_naissance']);
		$naissance = $tb_naiss['day'] . "/" .$tb_naiss['month'] . "/" .$tb_naiss['year'];
	}
	else {
		$naissance = "-";
	}
	
	$succes = ($tb['act_is_success'] == 1) ? 'Oui' : 'Non';
	echo "
    <tr>
        <td><a class=\"url_wiki\" href=\"" . $__url_wiki . utf8_decode($tb['act_resource']) . "\">" . $traitement_nom ."</td>
		<td>".$image."</td>
        <td>" .$naissance. "</td>
		<td>" . $succes . "</td>
    </tr>";   
}

echo "</tbody></table>";

include("../includes/footer.php");
?>
