<?php
include("../includes/header.php");

// Récupération de 2 films à succès aléatoires
	$sth_quizmovie = $dbh->prepare("SELECT * FROM success_movies ORDER BY RAND() LIMIT 2");
	$sth_quizmovie->execute();
	$quizmovie = $sth_quizmovie->fetchAll();

	if( !$quizmovie ) { print_r($dbh->errorInfo()); echo "\n"; exit; }

	?>
	<div class="question_quiz">Quiz sur les films</div>

	<?php 
	echo "
		<div class = \"quizz\">
		<p>Est-ce que \"" . utf8_decode($quizmovie[0]['sm_titre']) . "\" est sorti avant \"" . utf8_decode($quizmovie[1]['sm_titre']) . "\" ?</p>
		<input id=\"answer_no\" type=\"button\" name=\"no\" value=\"NON\" onclick=\"answer_no(". $quizmovie[0]['sm_annee'] .",". $quizmovie[1]['sm_annee'] .")\" />
		<input id=\"answer_yes\" type=\"button\" name=\"yes\" value=\"OUI\" onclick=\"answer_yes(". $quizmovie[0]['sm_annee'] .",". $quizmovie[1]['sm_annee'] .")\" />
		</div>
		<div class=\"propositions\">
		<div class=\"reponse_quiz\"></div>
		
		<div id=\"choix\" style=\"display:none\">
			<input type=\"button\" name=\"again\" value=\"SUIVANT\" onclick=\"again()\"  />
			<input type=\"button\" name=\"other\" value=\"Autre quiz\" onclick=\"changerQuiz()\" />
		</div>
		</div>
		
		";

echo "<script language=\"javascript\">
var juste 	= \"Vous avez raison !\";
var faux 	= \"Vous avez tort.\";
function answer_no(param1,param2){
	(param1 > param2) 	? document.getElementsByClassName('reponse_quiz').item(0).innerHTML = juste
						: document.getElementsByClassName('reponse_quiz').item(0).innerHTML = faux;
	document.getElementById('choix').style.display = \"block\";
}
function answer_yes(param1,param2){
	(param1 < param2) 	? document.getElementsByClassName('reponse_quiz').item(0).innerHTML = juste
						: document.getElementsByClassName('reponse_quiz').item(0).innerHTML = faux;
	document.getElementById('choix').style.display = \"block\";
}

function again() {
	window.location.reload(5);
}

function changerQuiz() {
	window.location = \"./liste_quiz.php\";
}
</script>";

include("../includes/footer.php");
?>
