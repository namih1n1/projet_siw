<?php
include("../includes/header.php");
$schema = "http://schema.org/Movie";

// Récuération de deux films aléatoirement, parmi le box-office
$sparql = "
	select distinct ?ressource ?titre ?year 
	where {
		?ressource 	rdf:type 						?lien ; 
					rdfs:label 						?titre ;
					prop-fr:annéeDeSortie   		?year.
		?films 		dbpedia-owl:wikiPageWikiLink 	?ressource . 
		FILTER (?lien like \"".$schema."\") .
		FILTER langmatches(lang(?titre),\"fr\") .
		FILTER (?films like \"*Liste_des_plus_gros_succ*s_du_box-office_mondial\")
}
ORDER BY RAND()
LIMIT 2
";

$two_success_movies = sparql_query( $sparql );
if( !$two_success_movies ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }

unset($sparql);
$result = array(sparql_fetch_array( $two_success_movies ), sparql_fetch_array( $two_success_movies ));
echo "
	<div class=\"question_quiz\">Quiz film </div>
	<p>Est-ce que \"" . utf8_decode($result[0]['titre']) . "\" est sorti avant \"" . utf8_decode($result[1]['titre']) . "\" ?</p>
    
	<input type=\"button\" name=\"no\" value=\"NON\" onclick=\"answer_no(". $result[0]['year'] .",". $result[1]['year'] .")\" />
    <input type=\"button\" name=\"yes\" value=\"OUI\" onclick=\"answer_yes(". $result[0]['year'] .",". $result[1]['year'] .")\" />
	
	<div class=\"reponse_quiz\"></div>
	
	<div id=\"choix\" style=\"display:none\">
		<input type=\"button\" name=\"again\" value=\"SUIVANT\" onclick=\"again()\"  />
		<input type=\"button\" name=\"other\" value=\"Autre quiz\" onclick=\"changerQuiz()\" />
		
		<div id=\"other_quiz\" style=\"display:none\">
			<a href=\"./quiz_actor_success.php\" >Quiz sur les acteurs des films &agrave; succ&egrave;s</a>
			
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
	document.getElementById('other_quiz').style.display = \"block\";
}
</script>";

include("../includes/footer.php");
?>
