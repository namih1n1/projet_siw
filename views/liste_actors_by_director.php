<?php
include("../includes/header.php");
$l_real = ( isset($_GET['lettre']) ) ? ucwords($_GET['lettre']) : null;

$sth_reals = $dbh->prepare("SELECT * FROM directors WHERE dir_nom LIKE \"".$l_real."%\" ORDER BY dir_nom");
$sth_reals->execute();
$reals = $sth_reals->fetchAll();

echo "<div class = 'tabletitle' id='movielist'><h2>" .count($reals) . " r&eacute;alisateurs commencant par " . $l_real . ".</h2></div>";
echo "<table>
	<thead>
        <tr>
			<th>R&eacute;alisateur</th><th>Informations</th>
		</tr>
	</thead>
	<tbody>
";

// Parcours des films

foreach ($reals as $key => $tb) {

	$traitement_nom = trim(utf8_decode($tb['dir_nom']));
	
	$image = ( utf8_decode($tb['dir_url_image']) == "") ? "Pas de photo" : "<img src=\"".utf8_decode($tb['dir_url_image'])."\" width='200px' height='auto' />";
	if ($tb['dir_naissance'] != "0000-00-00") {
			$tb_naiss  = date_parse($tb['dir_naissance']);
			$naissance = $tb_naiss['day'] . "/" .$tb_naiss['month'] . "/" .$tb_naiss['year'];
		}
		else {
			$naissance = "-";
		}
	echo "
    <tr>
        <td><a class=\"url_wiki\" href=\"" . $__url_wiki . utf8_decode($tb['dir_resource']) . "\">" . $traitement_nom ."</td>
		<td><div id=\"show_infos_".$tb['id_dir']."\" onclick=\"show_infos(".$tb['id_dir'].")\">Voir les informations relatives</div>
			<div><a href=\"./liste_actors_for_one_director.php?id_real=".$tb['id_dir']."\">Cliquer ici pour voir les acteurs dirig&eacute;s</a></div>
			<table id=\"montrer_infos_".$tb['id_dir']."\" style=\"display:none;\" >
				<thead><th>Image</th><th>Date de naissance</th></thead>
				<tr>
					<td>".$image."</td>
					<td>".$naissance."</td>
				</tr>
			</table>
		</td>
    </tr>";   
}

echo "</tbody></table>";

include("../includes/footer.php");
?>
