<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include_once("functions.php");

$result = $dbhandler->getConnection()->query("SELECT SUM(chatIsGoodOrBad) AS sum, COUNT(chatIsGoodOrBad) AS count FROM feedback WHERE chatIsGoodOrBad > 0;");

$field = $result->fetch_object();
$avg = $field->sum/$field->count;
echo "Durchschnittlich ".$avg." Punkte bei ".$field->count." Stimmen<br>";

$result2 = $dbhandler->getConnection()->query("SELECT comment FROM feedback WHERE comment!='';");
$array2 = $result2->fetch_all();
echo "<b>Allgemeine Kommentare:</b><br>" ;
echo "<ul>";
foreach ($array2 AS $content){
    echo "<li>".$content[0]."</li><br>";
}
echo "</ul>";
$result3 = $dbhandler->getConnection()->query("SELECT missing FROM feedbackMissing;");
$array3 = $result3->fetch_all();
echo "<b>Fehlende Dinge:</b><br>" ;
echo "<ul>";
foreach ($array3 AS $content){
    echo "<li>".$content[0]."</li><br>";
}
echo "</ul>";