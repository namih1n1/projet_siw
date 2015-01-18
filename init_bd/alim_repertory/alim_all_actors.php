<?php
/******************************************************/
/*         ALIMENTATION DE LA TABLE actors            */
/*     Tous les acteurs des films listés en bd        */
/******************************************************/

$dbh->exec("TRUNCATE TABLE actors" );

// Récupération des films de la base
$sth_mov = $dbh->prepare("SELECT id_mov, mov_resource FROM movies ORDER BY mov_resource");
$sth_mov->execute();

$movies = $sth_mov->fetchAll();
if( !$movies ) { print_r($dbh->errorInfo()); echo "\n"; exit; }

$cpt = 1;
// Parcours des acteurs
foreach($movies as $key => $tb) {
	$resource_film = "<".$__ressource.$tb['mov_resource'].">";
	// Requête SPARQL des acteurs du film courant
	$sparql = "
		select ?resactors ?nom ?naissance
		where {
		   ".$resource_film."   dbpedia-owl:starring  ?resactors .
		   ?resactors  			rdfs:label            ?nom ;
								dbpedia-owl:birthDate ?naissance .
		   FILTER langmatches(lang(?nom),\"fr\") .
		}
	";

	
	$list_actors = sparql_query( $sparql );
	if( !$list_actors ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }
	unset($sparql);
	if (sparql_num_rows( $list_actors ) == 0) echo $resource_film. "\n";

	// Parcours des acteurs du film courant
	while( $row = sparql_fetch_array( $list_actors ) )
	{
		$traited_resactor = substr($row['resactors'],strrpos($row['resactors'],"/")+1);
		$sth_actor = $dbh->prepare("SELECT * FROM actors WHERE act_resource LIKE \"%". $traited_resactor ."%\"");
		$sth_actor->execute();
		
		// Non-existence du film = ajout à la base
		if($sth_actor->fetchAll() == null) {
			$dbh->prepare("INSERT INTO actors
					VALUES ( " . $cpt . ", 
					\"" . $traited_resactor . "\",
					\"" . $row['nom'] . "\",
					\"" . $row['naissance'] . "\",
					0,
					\"\"
					)")->execute();
			$cpt++;
		}
		
		$sth_id_films = $dbh->prepare("SELECT act_id_movie FROM actors WHERE act_resource LIKE \"%". $traited_resactor ."%\"");
		$sth_id_films->execute();
		$list_id_films = $sth_id_films->fetchAll();
		$new_list_id_film = $list_id_films[0]['act_id_movie']. ",". $tb['id_mov'];

		// Insertion id du film
		$dbh->prepare("UPDATE actors SET act_id_movie = \"". $new_list_id_film. "\" WHERE act_resource LIKE \"%". $traited_resactor ."%\"")->execute();
	}
	unset($list_actors);
}
unset($movies);
echo "alim_all_actors FINI\n";

// Mise à jour des films qui sont des succès au box-office
$upd = $dbh->prepare("
		UPDATE 	actors 
		SET 	act_is_success = 1 
		WHERE 	act_resource IN (SELECT DISTINCT sa_resource FROM success_actors)");
$upd->execute();
echo "UPDATE actors FINI \n";

?>