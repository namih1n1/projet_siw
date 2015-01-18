<?php
/**************************************************/
/*      ALIMENTATION DE LA TABLE link_sm_sa       */
/*       Table de correspondance films-acteurs    */
/**************************************************/

$dbh->exec("TRUNCATE TABLE link_movies_actors;" );

$sth_act = $dbh->prepare("SELECT id_mov, mov_id_actor FROM movies;");
$sth_act->execute();
$acteurs = $sth_act->fetchAll();

foreach( $acteurs as $key => $tb ) {
	$str_id_movie = substr($tb['mov_id_actor'],1);
	$tb_list_id_actor = explode("|",$str_id_movie);
	array_pop($tb_list_id_actor);
	
	foreach($tb_list_id_actor as $id_m) {
		$sth = $dbh->prepare("INSERT INTO link_movies_actors VALUES (".$tb['id_mov'].",".$id_m.")");
		$sth->execute();
	}
}
echo "alim_link_movies_actors : FINI <br />";
?>