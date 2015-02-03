<?php
include("../includes/header.php");

	// Récupération des années référencées dans la table disney
	$sth_quizdisney = $dbh->prepare("SELECT DISTINCT dm_annee FROM disney_movies ORDER BY dm_annee");
	$sth_quizdisney->execute();
	$yeardisney = $sth_quizdisney->fetchAll();

	if( !$yeardisney ) { print_r($dbh->errorInfo()); echo "\n"; exit; }
	
	$tb_annee = array();
	foreach($yeardisney as $key => $year) {
		array_push($tb_annee,$year['dm_annee']);
	}

	// Récupération d'un film Disney aléatoirement
	$sth_onedisney = $dbh->prepare("SELECT dm_titre, dm_annee, dm_url_image FROM disney_movies ORDER BY RAND() LIMIT 1");
	$sth_onedisney->execute();
	$onedisney = $sth_onedisney->fetchAll();
	
	?>
	<div class="question_quiz">Quiz sur les films des studios Disney</div>

	<?php 
	if (substr($onedisney[0]['dm_titre'],0,strpos($onedisney[0]['dm_titre'],"(film")) != false)
		$traitement_titre = trim(utf8_decode(substr($onedisney[0]['dm_titre'],0,strpos($onedisney[0]['dm_titre'],"(film"))));
	else $traitement_titre = trim(utf8_decode($onedisney[0]['dm_titre']));
	
	echo "
		<div class=\"propositions\">
			<div class=\"reponse_quiz\"></div>
			
			<div id=\"choix\" style=\"display:none\">
				<input type=\"button\" name=\"again\" value=\"SUIVANT\" onclick=\"again()\"  />
				<input type=\"button\" name=\"other\" value=\"Autre quiz\" onclick=\"changerQuiz()\" />
			</div>
		</div>
		<div class = \"quizz\">
			<table>
				<thead><tr><th>Question</th><th>Propositions</th></tr></thead>
				<tr>
					<td style=\"font-size:20px; text-align:center;\">Quand est sorti le film Disney suivant ? <br />" .$traitement_titre. "<br />";
					if (($onedisney[0]['dm_url_image']) != "") {
						echo "<img src=\"".utf8_decode($onedisney[0]['dm_url_image'])."\" width='200px' height='200px'/>";
					}
					echo "</td>
					<td>
						<table>";
						foreach($yeardisney as $cle => $tb) {
							if( ($cle % 8) == 0 ) echo "<tr>";

							echo "<td><input id=\"".$tb['dm_annee']."\" class=\"button_annee\" type=\"button\" name=\"".$tb['dm_annee']."\" value=\"".$tb['dm_annee']."\" onclick=\"verif_year_disney(".$cle.",".$onedisney[0]['dm_annee'].",".$tb['dm_annee'].")\" /></td>";
							
							if (($cle % 8) == 7 ) echo "</tr>";
						}
						
						echo "</table>
					</td>
				</tr>
			</table>
		</div>
		
		";

	include("../includes/footer.php");
?>
