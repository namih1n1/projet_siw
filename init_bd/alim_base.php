<?php
set_time_limit(1200);
include("../includes/header.php");
$repertoire = "alim_repertory";

include("./".$repertoire."/alim_success_movies.php");
include("./".$repertoire."/alim_success_actors.php");
include("./".$repertoire."/alim_link_sm_sa.php");
include("./".$repertoire."/alim_all_movies.php");
// include("./".$repertoire."/alim_all_actors.php");
include("./".$repertoire."/alim_disney_movies.php");
include("./".$repertoire."/alim_oscar_disney_movie.php");


include("../includes/footer.php");
?>
