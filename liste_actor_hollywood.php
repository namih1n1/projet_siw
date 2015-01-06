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
               prop-fr:profession ?profession;
               rdf:type ?type;
               dbpedia-owl:birthDate ?birth;
               foaf:depiction ?image .
FILTER regex(?type,\"Actor\",\"i\") .
FILTER regex(?profession,\"Acteur\",\"i\") .
FILTER (!regex(?profession, \"porn\",\"i\")) . 
FILTER (?birth > \"1920-01-01\"^^xsd:date) .
}
ORDER BY ?Ressource
";

$list_actor = sparql_query( $sparql );
if( !$list_actor ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }
$fields = sparql_field_array( $list_actor );
$array_result = array();
$cpt = 0;
while( $row = sparql_fetch_array( $list_actor ) )
{
    $array_result[] =  array(   'id'    => $cpt,
                                'nom'   => utf8_decode(substr($row['Ressource'],strrpos($row['Ressource'],"/")+1)),
                                'img'   => $row['image'],
                                'birth' => $row['birth']
                        );
    $cpt++;
}
echo "<p>" .count($array_result) . " acteurs d'Hollywood.</p>";
echo "<table class='actor_table'>
        <tr><th>Acteurs</th><th>Image</th><th>Date de naissance</th></tr>
";

foreach($array_result as $tb) {
    echo "
    <tr>
        <td><a href=\"" . $__url_wiki . $tb['nom'] . "\">" . $tb['nom'] . " -- <a href=\"./listeFilms.php?actor=" . $tb['nom'] ."\" >Voir ses films</a></td>
        <td><div class=\"td_img_actor\"><span id=\"show_img_actor_". $tb['id'] ."\" onclick=\"showImgActor(". $tb['id'] .")\">Voir photo</span><span id=\"img_actor_".$tb['id']."\" style=\"display:none;\"><img src=\"" .$tb['img'] .  "\" alt=\"" . $tb['nom'] . "\" height='100px' width='100px'/></span></div></td>
        <td>" .$tb['birth']. "</td>
    </tr>";   
}
echo "</table>";
echo "
    <script language=\"javascript\">
    function showImgActor(id) {
        document.getElementById('img_actor_'+id).style.display = \"block\";
        document.getElementById('show_img_actor_'+id).style.display = \"none\";
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
