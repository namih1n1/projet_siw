<?php
include("../includes/header.php");

// Récupération de 2 films à succès aléatoires
	$sth_quizmovie = $dbh->prepare("SELECT * FROM movies WHERE mov_is_success = 1 ORDER BY RAND() LIMIT 2");
	$sth_quizmovie->execute();
	$quizmovie = $sth_quizmovie->fetchAll();

	if( !$quizmovie ) { print_r($dbh->errorInfo()); echo "\n"; exit; }

	?>
	<div class="question_quiz">Quiz sur les films au box-office</div>

	<?php 
	if (strpos($quizmovie[0]['mov_titre'],"(film") != 0)
		$traitement_titre0 = trim(utf8_decode(substr($quizmovie[0]['mov_titre'],0,strpos($quizmovie[0]['mov_titre'],"(film"))));
	else $traitement_titre0 = trim(utf8_decode($quizmovie[0]['mov_titre']));
		
	if (strpos($quizmovie[1]['mov_titre'],"(film") != 0)
		$traitement_titre1 = trim(utf8_decode(substr($quizmovie[1]['mov_titre'],0,strpos($quizmovie[1]['mov_titre'],"(film"))));
	else $traitement_titre1 = trim(utf8_decode($quizmovie[1]['mov_titre']));
	
	echo "
		<div class = \"quizz\">
		<div class=\"bloc_question\"><p>Est-ce que</p><p style='font-weight:bold;text-decoration:underline;'>" . $traitement_titre0 . "</p><p>est sorti avant</p><p style='font-weight:bold;text-decoration:underline;'>" . $traitement_titre1 . " ?</p>
		</div>
		<br />
		<input id=\"answer_no\" type=\"button\" name=\"no\" value=\"NON\" onclick=\"answer_no(". $quizmovie[0]['mov_annee'] .",". $quizmovie[1]['mov_annee'] .")\" />
		<input id=\"answer_yes\" type=\"button\" name=\"yes\" value=\"OUI\" onclick=\"answer_yes(". $quizmovie[0]['mov_annee'] .",". $quizmovie[1]['mov_annee'] .")\" />
		</div>
		<div class=\"propositions\">
		<div class=\"reponse_quiz\"></div>
		
		<div id=\"choix\" style=\"display:none\">
			<input type=\"button\" name=\"again\" value=\"SUIVANT\" onclick=\"again()\"  />
			<input type=\"button\" name=\"other\" value=\"Autre quiz\" onclick=\"changerQuiz()\" />
		</div>
		</div>
		
		";

include("../includes/footer.php");
?>
