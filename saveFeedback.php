<?php
include_once("functions.php");
// 3 vars
$request = filter_input_array(INPUT_POST);
if (isset($request["chatIsGoodOrBad"]))
{
	$chatIsGoodOrBad = $request["chatIsGoodOrBad"];
}
else
{
	$chatIsGoodOrBad = 0;
}
if (isset($request["foundEverything"]))
{
	$foundEverything = $request["foundEverything"];
}
else
{
	$foundEverything = 0;
}
if (isset($request["comment"]))
{
	$comment = $request["comment"];
}
else
{
	$comment = '';
}
$dbhandler->getConnection()->query("INSERT INTO `feedback` (chatIsGoodOrBad, foundEverything, comment) VALUES ('$chatIsGoodOrBad', '$foundEverything', '$comment');"); // save 3 vars
// for missing
$i = 0;
while ($request['missing'][$i] != '')
{
	$tempMissing = $request['missing'][$i];
	$dbhandler->getConnection()->query("INSERT INTO `feedbackMissing` (missing) VALUES ('$tempMissing');"); // missing
	//echo $_REQUEST['missing'][$i]; // save in DB
	$i++;
}
?>