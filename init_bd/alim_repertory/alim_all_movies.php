<?php
/******************************************************/
/*         ALIMENTATION DE LA TABLE movies            */
/*       Tous les films des acteurs à succès          */
/******************************************************/

$dbh->exec("TRUNCATE TABLE movies" );

// Récupération des acteurs à succès de la base
$sth = $dbh->prepare("SELECT id_success_a, sa_resource FROM success_actors");
$sth->execute();

$result = $sth->fetchAll();
if( !$result ) { print_r($dbh->errorInfo()); echo "\n"; exit; }

$cpt = 1;
// Parcours des acteurs
foreach($result as $key => $res_acteur) {
	$resource_actor = "<".$__ressource . $res_acteur['sa_resource'].">";

	// Requête SPARQL des films de l'acteur courant
	$sparql = "
		select distinct ?resfilm ?titre ?year ?image
		where {
			?resfilm 	rdf:type 				<http://schema.org/Movie> ; 
						rdfs:label 				?titre ;
						prop-fr:annéeDeSortie   ?year;
						dbpedia-owl:starring   	". $resource_actor .".
			OPTIONAL{?resfilm dbpedia-owl:thumbnail ?image}
			FILTER langmatches(lang(?titre),\"fr\") .
	}
	ORDER BY ?resfilm
	";
	
	$list_movies = sparql_query( $sparql );
	if( !$list_movies ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }
	unset($sparql);

	// Parcours des films de l'acteur courant
	while( $row = sparql_fetch_array( $list_movies ) )
	{
		$traited_resfilm = substr($row['resfilm'],strrpos($row['resfilm'],"/")+1);
		$sth_movie = $dbh->prepare("SELECT * FROM movies WHERE mov_resource LIKE \"%". $traited_resfilm ."%\"");
		$sth_movie->execute();
		// Traitement de l'url image
		$url_img 	= isset($row['image']) 			? $row['image'] : NULL;
		if ($url_img != "") {
			if (@fclose(@fopen($url_img, "r"))) { 
				$url_img = $url_img;
			} else { // Erreur 404 = réécriture des liens
				$url_img = str_replace("commons/thumb","fr",$url_img);
				$url_img = substr($url_img,0,strrpos($url_img,"/"));
				
				if (@fclose(@fopen($url_img, "r"))) { 
					$url_img = $url_img;
				} else { 
					$url_img = "";
				}
			}
			
		}
		
		// Non-existence du film = ajout à la base
		if($sth_movie->fetchAll() == null) {
			$dbh->prepare("INSERT INTO movies
					VALUES ( " . $cpt . ", 
					\"" . $traited_resfilm . "\",
					\"" . $row['titre'] . "\",
					" . $row['year'] . ",
					\"" . $url_img . "\",
					0,
					\"\"
					)")->execute();
			$cpt++;
		}
	}
	unset($list_movies);
}
unset($result);

// Mise à jour des films qui sont des succès au box-office
$upd = $dbh->prepare("
		UPDATE 	movies 
		SET 	mov_is_success = 1 
		WHERE 	mov_resource IN (SELECT DISTINCT sm_resource FROM success_movies)");
$upd->execute();
echo "alim_all_movies : FINI <br />";

?>