<?php
/** 
 * Hier werden alle Nutzeranfragen ausgewertet.
 */
require_once __DIR__."/functions.php";
error_reporting(E_ALL);
$action = filter_input(INPUT_GET, 'action');
switch ($action){
    case 'login':
        $name = filter_input(INPUT_POST, "user_name", FILTER_SANITIZE_STRING);
        $password = rawurlencode(utf8_encode($_POST["user_password"]));
        if ($sessionhandler->login($name, $password)){
            header("Location: index.php");
        }else{
            header("Location: index.php?msg=err_login");
        }
        break;
    case 'logout':
        $sessionhandler->logout();
        header("Location: index.php");
        break;
    case 'sendMessage':
        $message = $_POST['message'];
        $room = filter_input(INPUT_POST, 'room');
        $messagehandler->writeMessage($message, "1");
        break;
    case 'recieveMessage':
        $room = filter_input(INPUT_GET, 'room');
        $useraction = $sessionhandler->readUserListUpdate($room);
        if (!$useraction){
            echo $messagehandler->readMessages($room);
        }else{
            echo $useraction;
        }
        break;
    case 'getFirstMessages':
        $room = filter_input(INPUT_POST, 'room');
        $messagehandler->getFirstMessages($room);
        break;
    case 'getOlderMessages':
        $room = filter_input(INPUT_GET, 'room');
        $start = filter_input(INPUT_GET, 'start');
        $messagehandler->getOlderMessages($room, $start);
        break;
    case 'savePersonalSettings':
        $values = filter_input_array(INPUT_POST);
        $userhandler->saveSettingsArray($values);
        break;
    case 'getPersonalSettings':
        echo $userhandler->parseSettingsArray();
        break;
    case 'leave':
        $sessionhandler->leave();
        break;
    default: 
        echo $action."- Nicht definiert. ";
        exit;
}