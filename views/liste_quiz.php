<?php include("../includes/header.php");

$sth_hard = $dbh->prepare("SELECT COUNT(id_act) as 'nb' FROM actors");
$sth_hard->execute();
$hard = $sth_hard->fetchAll();
$nb_hard = $hard[0]['nb'];

$sth_soft = $dbh->prepare("SELECT COUNT(id_act) as 'nb' FROM actors WHERE act_is_success = 1");
$sth_soft->execute();
$soft = $sth_soft->fetchAll();
$nb_soft = $soft[0]['nb'];
echo "
<div class=\"listequiz\" id=\"liste_quiz\">
	<ul >
		<li><a href=\"../views/quiz_actor.php?easy=no\">Quiz sur les ".$nb_hard." acteurs r&eacute;f&eacute;renc&eacute;s</a></li>
		<li><a href=\"../views/quiz_actor.php?easy=yes\">Quiz sur les ".$nb_soft." acteurs au box_office</a></li>
		<li><a href=\"../views/quiz_movie.php\">Quiz sur les films</a></li>
		<li><a href=\"../views/quiz_disney.php\">Quiz sur les films des studios Disney</a></li>
	</ul>
</div>";


include("../includes/footer.php"); ?>
