<?php
/**************************************************/
/*      ALIMENTATION DE LA TABLE link_sm_sa       */
/*       Table de correspondance films-acteurs    */
/**************************************************/

$dbh->exec("TRUNCATE TABLE link_sm_sa;" );

$schema = "http://schema.org/Movie";

$sth_act = $dbh->prepare("SELECT id_success_a, list_idfilms FROM success_actors;");
$sth_act->execute();
$acteurs = $sth_act->fetchAll();

foreach( $acteurs as $key => $tb ) {
	$str_id_movie = substr($tb['list_idfilms'],1);
	foreach(explode(",",$str_id_movie) as $id_m) {
		$sth = $dbh->prepare("INSERT INTO link_sm_sa VALUES (".$id_m.",".$tb['id_success_a'].")");
		$sth->execute();
	}
}
echo "alim_link_sm_sa : FINI <br />";
?>