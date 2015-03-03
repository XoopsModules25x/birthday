<?php
function listejour($name,$select=1) {
    $select--; // pour avoir l'incdiee ds le tableau
    $j = array ("01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","31");
    $stop = count ($j);
    $liste="<select name='$name'>";
    for ($i=0; $i<$stop; $i++) {
        if ($select==$i)
            $liste.="<option value='$j[$i]' selected>$j[$i]</option>";
        else
            $liste.="<option value='$j[$i]'>$j[$i]</option>";
    }
    $liste.="</select>";

    return $liste;
}

function listemois($name,$select=1) {
    $select--; // pour avoir l'indice ds le tableau
    $nom = array ("Janvier","Février","Mars","Avril","Mai","Juin","Juillet","Août","Septembre","Octobre","Novembre","Décembre");
    $m = array ("01","02","03","04","05","06","07","08","09","10","11","12");
    $liste="<select name=\"$name\">";
    for ($i=0; $i<count($m); $i++) {
        if ($select==$i)
            $liste.="<option value=\"$m[$i]\" selected>$nom[$i]</option>";
        else
            $liste.="<option value=\"$m[$i]\">$nom[$i]</option>";
    }
    $liste.="</select>";

    return $liste;
}

function listeannee ($name, $fin, $select="1930") {
    $liste = "<select name=\"$name\">";
    for ($i=1930; $i<=$fin; $i++) {
        if ($select==$i) {
            $liste.="<option value=\"$i\" selected>$i</option>";
        } else {
            $liste.="<option value=\"$i\">$i</option>";
        }
    }
    $liste.="</select>";

    return $liste;
}
