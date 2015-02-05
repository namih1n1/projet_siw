<?php
include("../includes/header.php");
$real_id = ( isset($_GET['id_real']) ) ? $_GET['id_real'] : null;

// Infos real
$sth_real = $dbh->prepare("SELECT dir_nom FROM directors WHERE id_dir = ".$real_id);
$sth_real->execute();
$real = $sth_real->fetchAll();

$sth_actors = $dbh->prepare("
	SELECT 	a.*
	FROM 	directors d,
			link_directors_movies ldm,
			link_movies_actors lma,
			actors a
	WHERE 	d.id_dir = ".$real_id."
	AND		d.id_dir = ldm.id_dir
	AND		ldm.id_mov = lma.id_mov
	AND		lma.id_act = a.id_act
	ORDER BY a.act_nom");
$sth_actors->execute();
$actors = $sth_actors->fetchAll();

echo "<div class = 'tabletitle' id='actorlist'><h2>" .count($actors) . " acteurs dirig&eacute;s par " . $real[0]['dir_nom']. ".</h2></div>";
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
