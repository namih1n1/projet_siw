<?php
/****************************************************/
/*         Mise à jour de la table actors          	*/
/*       Lien entre movies et actors        		*/
/****************************************************/
$dbh->exec("TRUNCATE TABLE link_movies_actors");

// Récupération des movies
$sth_films = $dbh->prepare("SELECT id_mov, mov_resource FROM movies");
$sth_films->execute();
$movies = $sth_films->fetchAll();
if( !$movies ) { print_r($dbh->errorInfo()); echo "\n"; exit; }

// Parcours des acteurs
foreach($movies as $key => $tb) {
	$resource_film = "<".$__ressource.$tb['mov_resource'].">";
	// Requête SPARQL des acteurs du film courant
	$sparql = "
		select ?resactors
		where {
		   ".$resource_film."   	dbpedia-owl:starring  ?resactors .
		   ?resactors  				rdfs:label            ?nom .
			OPTIONAL {?resactors 	dbpedia-owl:birthDate ?naissance }
			OPTIONAL {?resactors	dbpedia-owl:thumbnail ?image }
		   FILTER langmatches(lang(?nom),\"fr\") .
		}
	";

	
	$list_actors = sparql_query( $sparql );
	if( !$list_actors ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }
	unset($sparql);

	// Parcours des acteurs du film courant
	while( $row = sparql_fetch_array( $list_actors ) )
	{
		$traited_resactor = substr($row['resactors'],strrpos($row['resactors'],"/")+1);
		// Récupération de l'id de l'acteur
		$sth_act = $dbh->prepare("SELECT id_act FROM actors WHERE act_resource LIKE \"%".$traited_resactor."%\"");
		$sth_act->execute();
		$acteur = $sth_act->fetchAll();
		
		// Mise à jour de la table link_movies_actors
		$sth_upd = $dbh->prepare("INSERT INTO link_movies_actors VALUES(".$tb['id_mov'].",".$acteur[0]['id_act'].")");
		$sth_upd->execute();
	}
	unset($list_actors);
}
unset($movies);
echo "Mise à jour de link_movies_actors : FINI \n"
?>