<?php
include("../includes/header.php");

// Récuération de deux films aléatoirement, parmi le box-office, pour les âges
$sparql = "
	SELECT distinct ?resactor ?nom ?naissance
	WHERE {
		?resfilm 	dbpedia-owl:starring 	?resactor ;
                                rdf:type 		?lien.
                ?films 		dbpedia-owl:wikiPageWikiLink 	?resfilm  .  
        
		?resactor	dbpedia-owl:birthDate  	?naissance ;
		rdfs:label				?nom .
		FILTER langmatches(lang(?nom),\"fr\").
                FILTER (?lien like \"http://schema.org/Movie\") .
                FILTER (?films like \"*Liste_des_plus_gros_succ*s_du_box-office_mondial*\") .

	}
ORDER BY RAND()
LIMIT 2

";

$two_success_movies = sparql_query( $sparql );
if( !$two_success_movies ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }

unset($sparql);
$result = array(sparql_fetch_array( $two_success_movies ), sparql_fetch_array( $two_success_movies ));
echo "
	<div class=\"question_quiz\">Quiz acteur </div>
	<p>Est-ce que \"" . utf8_decode($result[0]['nom']) . "\" est n&eacute; avant \"" . utf8_decode($result[1]['nom']) . "\" ?</p>
    
	<input id=\"answer_no\" type=\"button\" name=\"no\" value=\"NON\" onclick=\"answer_no(". $result[0]['naissance'] .",". $result[1]['naissance'] .")\" />
    <input id=\"answer_yes\" type=\"button\" name=\"yes\" value=\"OUI\" onclick=\"answer_yes(". $result[0]['naissance'] .",". $result[1]['naissance'] .")\" />
	
	<div class=\"reponse_quiz\"></div>
	
	<div id=\"choix\" style=\"display:none\">
		<input type=\"button\" name=\"again\" value=\"SUIVANT\" onclick=\"again()\"  />
		<input type=\"button\" name=\"other\" value=\"Autre quiz\" onclick=\"changerQuiz()\" />
		
		<div id=\"other_quiz\" style=\"display:none\">
			<a href=\"./quiz_films_old.php\" >Quiz sur les films &agrave; succ&egrave;s</a>
			
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
	document.getElementById('answer_no').enabled = \"disabled\";
	
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
	document.getElementById('other_quiz').style.display = \"block\";
}
</script>";

include("../includes/footer.php");
?>
