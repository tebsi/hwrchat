<?php
echo '<pre>'; //print vars
print_r($_REQUEST);
echo '</pre>';
include_once("functions.php");
// 3 vars
if (isset($_REQUEST["chatIsGoodOrBar"]))
{
	$chatIsGoodOrBar = $_REQUEST["chatIsGoodOrBar"];
}
else
{
	$chatIsGoodOrBar = 0;
}
if (isset($_REQUEST["foundEverything"]))
{
	$foundEverything = $_REQUEST["foundEverything"];
}
else
{
	$foundEverything = 0;
}
if (isset($_REQUEST["comment"]))
{
	$comment = $_REQUEST["comment"];
}
else
{
	$comment = '';
}
$dbhandler->getConnection()->query("INSERT INTO `feedback` (chatIsGoodOrBar, foundEverything, comment) VALUES ('$chatIsGoodOrBar', '$foundEverything', '$comment');"); // save 3 vars
// for missing
$i = 0;
while ($_REQUEST['missing'][$i] != '')
{
	$tempMissing = $_REQUEST['missing'][$i];
	$dbhandler->getConnection()->query("INSERT INTO `feedbackMissing` (missing) VALUES ('$tempMissing');"); // missing
	//echo $_REQUEST['missing'][$i]; // save in DB
	$i++;
}
?>