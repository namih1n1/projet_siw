<?php
/******************************************************/
/*         ALIMENTATION DE LA TABLE movies            */
/*       Tous les films des acteurs à succès          */
/******************************************************/

// Récupération des ressources des films
$sth = $dbh->prepare("SELECT DISTINCT id_mov, mov_resource FROM movies ORDER BY mov_resource");
$sth->execute();

$result = $sth->fetchAll();
if( !$result ) { print_r($dbh->errorInfo()); echo "\n"; exit; }

// Parcours des films
foreach($result as $key => $res_film) {
	$resource_movie = "<".$__ressource . $res_film['mov_resource'].">";

	// Requête SPARQL des films de l'acteur courant
	$sparql = "
		select ?actors
		where {	".$resource_movie." dbpedia-owl:starring ?actors
		}
		ORDER BY ?actors
	";
	
	$list_actors = sparql_query( $sparql );
	if( !$list_actors ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }
	unset($sparql);

	// Parcours des acteurs du film courant
	while( $row = sparql_fetch_array( $list_actors ) )
	{
		$traited_resactor = substr($row['actors'],strrpos($row['actors'],"/")+1);
		
		$sth_movie = $dbh->prepare("SELECT id_success_a FROM success_actors WHERE sa_resource LIKE \"%". $traited_resactor ."%\"");
		$sth_movie->execute();
		$id_actor = $sth_movie->fetchAll();
		
		// Existence de l'acteur = ajout à la liste des acteurs participants
		if($id_actor != null) {
			$sth = $dbh->prepare("SELECT mov_id_actor FROM movies WHERE id_mov = " . $res_film['id_mov']);
			$sth->execute();
			$liste_participant = $sth->fetchAll();
			
			$new_list = $liste_participant[0]['mov_id_actor'].$id_actor[0]['id_success_a']."|";
			$dbh->prepare("
				UPDATE movies SET mov_id_actor = \"".$new_list."\" WHERE id_mov = ".$res_film['id_mov']
				)->execute();
		}
	}
	unset($list_actors);
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