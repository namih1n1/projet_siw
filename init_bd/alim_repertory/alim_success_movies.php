<?php
/******************************************************/
/*      ALIMENTATION DE LA TABLE success_movies       */
/*       Tous les films au box-office mondial         */
/******************************************************/

$dbh->exec("TRUNCATE TABLE success_movies" );

$sparql = "
	select distinct ?ressource ?titre ?year ?image
	where {
		?ressource 	rdf:type 				<http://schema.org/Movie> ; 
					rdfs:label 				?titre ;
					prop-fr:annéeDeSortie   ?year.
		OPTIONAL {?ressource dbpedia-owl:thumbnail ?image }
		<http://fr.dbpedia.org/resource/Liste_des_plus_gros_succès_du_box-office_mondial> 	dbpedia-owl:wikiPageWikiLink 	?ressource . 
		FILTER langmatches(lang(?titre),\"fr\") .
}
ORDER BY ?ressource
";
$list_success_films = sparql_query( $sparql );
if( !$list_success_films ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }

unset($sparql);
unset($array_result);
$array_result = array();
$cpt = 1;

while( $row = sparql_fetch_array( $list_success_films ) )
{
	$url_img = isset($row['image']) ? $row['image'] : NULL;
	$dbh->prepare("INSERT INTO success_movies 
			VALUES ( ". $cpt .", 
			\"" . substr($row['ressource'],strrpos($row['ressource'],"/")+1) . "\",
			\"" . $row['titre'] . "\",
			\"" . $url_img . "\",
			". $row['year'] ."
			
			)")->execute();
	$cpt++;
}
unset($list_succes_films);
echo "alim_success_movies : FINI <br />";

?>