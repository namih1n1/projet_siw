<?php
include("../../includes/header.php");

$lettre = ( isset($_GET['alpha']) ) ? $_GET['alpha'] : null;

if ( !is_null($lettre) ) {
	// Récupération des acteurs commençant par la lettre renseignée en paramètre
	$sth_act_alpha = $dbh->prepare("
		SELECT		act_resource, act_nom, act_naissance
		FROM		actors
		WHERE 		act_nom LIKE \"" . $lettre . "%\"
		ORDER BY	act_nom;
	");
	$sth_act_alpha->execute();
	$actors_alpha = $sth_act_alpha->fetchAll();
	echo "<h3>" . count($actors_alpha). " acteurs commençant par " . $lettre ."</h3>";
	
	echo "	<table class='alpha'>
				<tbody>
					<tr><th>Acteur</th><th>Date de naissance</th></tr>
	";
	foreach( $actors_alpha as $key => $tb ) {
		echo "
					<tr>
						<td><a href=\"" . $__url_wiki . utf8_decode($tb['act_resource']) . "\">" . utf8_decode($tb['act_nom']) . "</a></td>
						<td>" . $tb['act_naissance'] . "</td>
					</tr>
		";
	}
	echo "
				</tbody>
			</table>
	";

}
else {
		echo "Rien à afficher";
}


include("../../includes/footer.php");
?>