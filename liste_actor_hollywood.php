<?php
include("./includes/header.php");

echo "
<style>
.show_img_actor:hover {
    text_decoration:underline;    
    color:blue;
    cursor:pointer;
}
</style>
";

$sparql = "
select distinct ?Ressource ?birth ?image
where {
    ?Ressource foaf:name ?o ;
               rdf:type ?profession;
               dbpedia-owl:birthDate ?birth;
               foaf:depiction ?image;
               dbpedia-owl:wikiPageWikiLink dbpedia-fr:Hollywood ;
               dbpedia-owl:wikiPageWikiLink ?autrecritere .
FILTER (?profession like \"*Actor*\") .
FILTER (?autrecritere like \"*Acteur*\") .
FILTER (?birth > \"1920-01-01\"^^xsd:date) .
}
ORDER BY ?Ressource
";

$list_actor = sparql_query( $sparql );
if( !$list_actor ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }
$fields = sparql_field_array( $list_actor );
$array_result = array();
while( $row = sparql_fetch_array( $list_actor ) )
{
    $cpt = 0;
    $array_result[] =  array(   'id'    => $cpt,
                                'nom'   => utf8_decode(substr($row['Ressource'],strrpos($row['Ressource'],"/")+1)),
                                'img'   => $row['image'],
                                'birth' => $row['birth']
                        );
    $cpt = $cpt+1;
}
echo "<p>" .count($array_result) . " acteurs d'Hollywood.</p>";
echo "<table class='actor_table'>
        <tr><th>Acteurs</th><th>Image</th><th>Date de naissance</th></tr>
";

foreach($array_result as $tb) {
    echo "
    <tr>
        <td><a href=\"" . $__url_wiki . $tb['nom'] . "\">" . $tb['nom'] . " -- <a href=\"./listeFilms.php?actor=" . $tb['nom'] ."\" >Voir ses films</a></td>
        <td><div class=\"td_img_actor\"><span class=\"show_img_actor\" onclick=\"showImgActor(". $tb['id'] .")\">Voir photo</span><span id=\"img_actor_".$tb['id']."\" style=\"display:none;\"><img src=\"" .$tb['img'] .  "\" alt=\"" . $tb['nom'] . "\" height='100px' width='100px'/></span></div></td>
        <td>" .$tb['birth']. "</td>
    </tr>";   
}
echo "</table>";
echo "
    <script language=\"javascript\">
    function showImgActor(id) {
        document.getElementById('img_actor_'+id).style.display = \"block\";
    }  
    </script>";


/*
print sparql_num_rows( $list_actor )." acteurs d'Hollywood.</p>";
print "<table class='example_table'>";
print "<tr>";
// Entête
// foreach( $fields as $field ) { print "<th>$field</th>"; }
print "</tr>";
while( $row = sparql_fetch_array( $list_actor ) )
{
print "<tr>";
foreach( $fields as $field )
{
$nom_actor = utf8_decode(substr("$row[$field]",strrpos("$row[$field]","/")+1)); 
echo "<td><a href=\"" . $__url_wiki . $nom_actor ."\"> ".$nom_actor. "</a> -- <a href=\"./listeFilms.php?actor=$nom_actor\" >Voir ses films</a></td>";
}
print "</tr>";
}
print "</table>";
*/
?>
