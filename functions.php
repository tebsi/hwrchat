<?php
/** Diese Datei enthält alle für das Backend unseres Chats relevanten Funktionen!
 * 
 * 
 * Author: MTebs
 * 
 * 
 */


session_start();

require_once __DIR__."/config.php";

if ($CONFIG['debug']){
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}else{
    ini_set('display_errors', 0);
    error_reporting(0);
}

$dbhandler = new DBHandler();
class DBHandler{
    private $connection;
    
    function __construct(){
        global $CONFIG;
        $this->connection = new mysqli($CONFIG['DB']['server'], $CONFIG['DB']['user'], $CONFIG['DB']['password'], $CONFIG['DB']['db']);
    }
    
    public function getConnection() {
       return $this->connection;
    }
    
    public function getStatus(){
        if (isset($this->connection)){
            return $this->connection->connect_errno;
        }else{
            return false;
        }
    }
}

$sessionhandler = new Session_Handler($dbhandler);
/** Behandelt alle Nutzerbezogenen Login- und Logout Daten, bla, keks
 *
 */
class Session_Handler {
    static $LOGIN_URL = "https://webmail.stud.hwr-berlin.de/ajax/login?action=login";
    static $CONTACTS_URL = "https://webmail.stud.hwr-berlin.de/ajax/contacts?action=getuser";
    private static $MAX_NA = 8;
    
    private $dbhandler, $loginID;
    
    public function __construct(&$dbhandler) {
        $this->dbhandler = $dbhandler;
        $this->readLoginId();
    }
    /** Fragt beim OX Server eine Session ID ab, speichert Sie in der Datenbank
     * 
     * @param String $name
     * @param String $password
     * @return boolean
     */
    public function login($name, $password) {
        $url = self::$LOGIN_URL;
        $post = "name=" . $name . "&password=" . $password;
        $response = json_decode(self::fireCURL($url, $post)); //Gibt ein Objekt mit session-ID und User-ID zurück
        if (isset($response->session)) {
            $_SESSION['sessionID'] = $response->session;
            $display_name = $this->readUserName($response->session,  $response->user_id, $name);
            $this->insertNewSession($response->session, $response->user_id, $display_name);
            return $response->session;
        } else {
            return false;
        }  
    }
    
    /** Liest anzeigename von OX-Server
     * 
     * @param type $sid
     * @param type $uid
     * @param type $name_alt
     * @return type
     */
    public function readUserName($sid, $uid, $name_alt='Unbekannt') {
        if (isset($uid) && isset($sid)) {
            $url = self::$CONTACTS_URL;
            $post = "session=" . $sid . "&id=" . $uid;
            $response = json_decode(self::fireCURL($url, $post)); //gibt ein Objekt mit Display_name zurück
            unlink("cookies/".session_id());
            if (isset($response->data)) {
                return $response->data->display_name;
            } else {
                return $name_alt;
            }
        }else{
            return false;
        }
    }
    
    /** Löscht SessionID aus Session
     * 
     */
    public function logout() {
        $this->leave();
        unset($_SESSION['sessionID']);
    }
    
    /** Liest fk_user_id aus Datenbank auf Grundlage der gesetzten SessionID
     * 
     * @return userID oder False
     */
    private function readLoginId() {
        if (isset($_SESSION['sessionID'])) {
            $result = $this->dbhandler->getConnection()->query("SELECT fk_user_id FROM login WHERE session_id = '" . $_SESSION['sessionID'] . "';");
            if ($result->num_rows){
                $field = $result->fetch_object();
                if (isset($field->fk_user_id)) {
                    $this->loginID = $field->fk_user_id;
                } else {
                    self::logout();
                    return false;
                }
            }else{
                self::logout();
                return false;
            }
        } else {
            return false;
        }
    }
    
    public function getLoginId(){
        return $this->loginID;
    }
    
    /** Liest Anzeigenamen des angemeldeten Benutzers aus Datenbank
     * 
     * @return string
     */
    public  function getLoginUserName() {
        $uid = $this->getLoginId();
        if ($uid) {
            $result = $this->dbhandler->getConnection()->query("SELECT display_name FROM login WHERE fk_user_id = '" . $uid . "';");
            $field = $result->fetch_object();
            if ( $field->display_name == ''){
                return 'unbekannt';
            }else{
                return $field->display_name;
            }
        }
    }
    
    /** Schreibt einen neuen Eintrag in die Sessiontabelle, wenn noch keiner Vorhanden ist
     * 
     * @param int $sesID
     * @param int $uid
     * @param string $display_name
     */
    public function insertNewSession($sesID, $uid, $display_name) {
        $result = $this->dbhandler->getConnection()->query("SELECT session_id FROM login WHERE fk_user_id = '" . $uid . "';");
        if ($result->num_rows > 0) {
            $this->dbhandler->getConnection()->query("UPDATE login SET session_id='" . $sesID . "', display_name='" . $display_name . "', time=" . time() . " WHERE fk_user_id='" . $uid . "';");
        } else {
            $this->dbhandler->getConnection()->query("INSERT INTO login (session_id, fk_user_id, display_name, time) VALUES('" . $sesID . "', '" . $uid . "', '" . $display_name . "', " . time() . ");");
        }   
    }
    
    public function activity($room){
        $this->leave();
        $uid = $this->getLoginId();
        $this->dbhandler->getConnection()->query("INSERT INTO user_activity (fk_user_id, last_activity, fk_room_id) VALUES('" . $uid . "', " . time() . ", '".$room."') ON DUPLICATE KEY UPDATE last_activity=VALUES(last_activity);");
        echo $this->dbhandler->getConnection()->error;
    }
    
    public function leave(){
        $uid = $this->getLoginId();
        $this->dbhandler->getConnection()->query("DELETE FROM user_activity WHERE fk_user_id = '".$uid."';");
    }
    
    public function cleanupActivity(){
        $this->dbhandler->getConnection()->query('DELETE FROM user_activity WHERE last_activity < '.(time()-self::$MAX_NA).';'); 
    }
    
    public function readUserList($room){
        $this->activity($room);
        $this->cleanupActivity();
        $result = $this->dbhandler->getConnection()->query("SELECT display_name FROM user_activity LEFT JOIN login ON login.fk_user_id = user_activity.fk_user_id WHERE fk_room_id = '".$room."';");
        if ($result->num_rows){
            $list = $result->fetch_all();
            $_SESSION['userlist'] = $list;
            return $list;
        }else{
            return null;
        }
    }
    
    public function readUserListUpdate($room){
        $oldList = $_SESSION['userlist'];
        $list = $this->readUserList($room);
        if ($list != $oldList){
            $send =  '[{"control": "clearuserlist"},';
            foreach ($list AS $item){
                $send .= '{"user": "'.$item[0].'", "control": "useradd"},';
            }
            $send = substr($send, 0, -1);
            $send .= ']';
            return $send;
        }else{
            return '';
        }
    }
    
    /** Selbstbeschreibend, oder?
     * 
     * @param String $url
     * @param String $post
     * @return String
     */
    private static function fireCURL($url, $post){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
        curl_setopt($curl, CURLOPT_COOKIEJAR, "cookies/".session_id());
        curl_setopt($curl, CURLOPT_COOKIEFILE, "cookies/".session_id()); 
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }
}

$userhandler = new User_Handler($sessionhandler->getLoginId(), $dbhandler);
class User_Handler{
    private $validSettings = array(
            "background-color", "font-family", "text-color", "message-background-color", "message-count", "message-style"
        );
    private $dbconnection;
    private $userID;
    
    function __construct($userID, &$dbhandler){
        $this->dbconnection = $dbhandler->getConnection();
        $this->userID = $userID;
    }
    
    private function getPersonalSetting($title){
        $query = 'SELECT value FROM personal_settings WHERE title = "'.$title.'" AND fk_user_id = "'. $this->userID.'";';
        $result = $this->dbconnection->query($query);
        echo $this->dbconnection->error;
        if ($result->num_rows){
            $field = $result->fetch_object();
            return $field->value;
        }else{
            return false;
        }
    }
    
    private function getGeneralSetting($title){
        $query = 'SELECT value FROM general_settings WHERE title = "'.$title.'";';
        $result = $this->dbconnection->query($query);
        if ($result->num_rows){
            $field = $result->fetch_object();
            return $field->value;     
        }else{
            return false;
        }
    }
    
    public function getSetting($title){
        $personal = $this->getPersonalSetting($title);
        if ($personal != ""){
            return $personal;
        }else{
            return $this->getGeneralSetting($title);
        }
    }
    
    public function getSettingsArray(){
        $returnarray = array();
        foreach ($this->validSettings AS $title){
              $returnarray[$title] = $this->getSetting($title);
        }
        return $returnarray;
    }
    
    function parseSettingsArray(){
        $returnage = "{";
        foreach($this->validSettings AS $title){
            $returnage .= '"'.$title.'": "'.$this->getSetting($title).'", ';
          
        }
        $returnage = substr($returnage, 0, -2);
        
        $returnage .= "}";
        return $returnage;
    }
    
    public function setSetting($title, $value){
        $personal = $this->getPersonalSetting($title);
        
        if ($personal != ""){
            $query = 'UPDATE personal_settings SET value = "'.$value.'" WHERE title = "'.$title.'" AND fk_user_id = "'.$this->userID.'";';
        }else{
            $query = 'INSERT INTO personal_settings (fk_user_id, title, value) VALUES ("'.$this->userID.'", "'.$title.'", "'.$value.'");';
        }
        $this->dbconnection->query($query);
    }
    
    public function saveSettingsArray($settingsArray){
        foreach ($settingsArray AS $title=>$value){
            if (in_array($title, $this->validSettings)){
                $this->setSetting($title, $value);
            }
        }
    }
}

$messagehandler = new MessageHandler($dbhandler, $sessionhandler, $userhandler);
/**
 *  Beschreibt alle Methoden im Umgang mit Nachrichten
 */
class MessageHandler{
    private $dbconnection, $uid, $shandler, $userhandler;
    
    public function __construct(&$dbhandler, &$sessionhandler, &$userhandler) {
        $this->dbconnection = $dbhandler->getConnection();
        $this->shandler = $sessionhandler;
        $this->userhandler = $userhandler;
        $this->uid = $this->shandler->getLoginId();
    }
    
    /** Speichert Nachricht in Datenbank (von eingeloggtem Benutzer)
     * 
     * @param string $message
     * @param int $room
     */
    public function writeMessage($message, $room) {
        if (RoomHandler::isInRoom($this->uid, $room) && $this->uid) {
            $test_string = str_replace("&#10;", "", trim(filter_var($message, FILTER_SANITIZE_SPECIAL_CHARS)));
            $message = str_replace("&#10;", "<br>", trim(filter_var($message, FILTER_SANITIZE_SPECIAL_CHARS)));
            $message = str_replace(" ", "&nbsp;", $message);
            $message = str_replace("\\", "&#92;", $message);
            if ($test_string != ""){
                $this->dbconnection->query("INSERT INTO messages (fk_room_id, fk_user_id, message, time) VALUES ('" . $room . "','" . $this->uid . "','" . $message . "','" . time() . "');");
            }
        }
    }
    
    /** Liest Nachrichten und wandelt sie in JSON Format zum senden
     * 
     */
    public function readMessages($room) {
        if (RoomHandler::isInRoom($this->uid, $room) && $this->uid) { 
            $lastId = $this->getLastMessageId($room);
            $currentID = $this->getCurrentMessageId($room);
            if ($lastId != $currentID) {
                $msg_query = $this->dbconnection->query("
                    SELECT messages_id, message, display_name, messages.fk_user_id AS uid, messages.time 
                    FROM messages 
                    LEFT JOIN login ON login.fk_user_id = messages.fk_user_id 
                    WHERE fk_room_id = '" . $room . "' AND messages_id > " . $lastId . ";");
                $messages = mysqli_fetch_all($msg_query, MYSQLI_ASSOC);
                $this->refreshLastMessageId($room);
                return $this->parseJSON($messages);
                
            } else {
                return "[]";
            }
        } else { //Wenn der Benutzer nicht im Raum ist, wird er knallhart rausgeschmissen
            return '[{"control":"logout"}]';
        }
    }
    
    /** Gibt die letzte vom Nutzer gelesene Nachricht zurück. 
     *  Wird benutzt, um zu prüfen, ob der Nutzer neue Nachrichten hat.
     * 
     * @param int $room
     * @return int
     */
    public function getLastMessageId($room) {
        $query = $this->dbconnection->query("SELECT last_message FROM room_user_relation WHERE fk_user_id = '" . $this->uid . "' AND fk_room_id = '" . $room . "';");
        $result = $query->fetch_object();
        if (!isset($result->last_message)) {
            $this->insertNewLastMessageId($room);
            return 0;
        } else {
            return $result->last_message;
        }
    }
    
    /** Gibt die aktuelle Nachrichtennummer im Raum.
     *  Wird benutzt, um zu prüfen, ob der Nutzer neue Nachrichten hat.
     * 
     * @param int $room
     * @return int
     */
    public function getCurrentMessageId($room) {
        $query = $this->dbconnection->query("SELECT MAX(messages_id) AS last_id FROM messages WHERE fk_room_id = '" . $room . "';");
        $result = $query->fetch_object();
        if (!isset($result->last_id)) {
            return 0;
        } else {
            return $result->last_id;
        }
    }
    
    /** Wenn alle neuen Nachrichten übertragen wurden, wird in die Nutzertabelle die neue Nachrichtennummer geschrieben
     * 
     * @param int $room
     */
    private function refreshLastMessageId($room) {
        $nidq = $this->dbconnection->query("SELECT messages_id FROM messages WHERE fk_room_id = '" . $room . "' ORDER BY messages_id DESC LIMIT 1;");
        $newID = mysqli_fetch_object($nidq); 
        $this->dbconnection->query("UPDATE room_user_relation SET last_message='" . $newID->messages_id . "' WHERE fk_user_id = '" . $this->uid . "' AND fk_room_id = '" . $room . "';");
        $this->dbconnection->commit();
    }
    
    /** Neue user_room_relation anlegen, falls der Nutzer das erste Mal Nachrichten liest.
     * 
     * @param type $room
     */
    public function insertNewLastMessageId($room) {
        $this->dbconnection->query("INSERT INTO room_user_relation (fk_user_id, fk_room_id, last_message) VALUES ('" . $this->uid . "', '" . $room . "', '0');");
        $this->dbconnection->commit();
    }
    
    /** Lädt die ersten 10 Nachrichten beim Login
     * 
     * @param int $room
     */
    public function getFirstMessages($room){
        if (RoomHandler::isInRoom($this->uid, $room) && $this->uid) {
            $message_count = $this->userhandler->getSetting("message-count");
            $msg_query = $this->dbconnection->query("
                SELECT messages_id, message, display_name, messages.fk_user_id AS uid, messages.time 
                FROM messages 
                LEFT JOIN login ON login.fk_user_id = messages.fk_user_id 
                WHERE fk_room_id = '" . $room . "' 
                ORDER BY messages_id DESC 
                LIMIT ".$message_count.";");
            $messages = array_reverse(mysqli_fetch_all($msg_query, MYSQLI_ASSOC));
            echo $this->parseJSON($messages);
            $this->refreshLastMessageId($room);
        }
    }
    
    /** Liest ältere Nachrichten, wenn der Nutzer ältere Nachrichten lesen möchte.
     * 
     * @param int $room
     * @param int $start
     */
    public function getOlderMessages($room, $start){
        if (RoomHandler::isInRoom($this->uid, $room) && $this->uid) {
            $msg_query = $this->dbconnection->query("SELECT messages_id, message, display_name, messages.fk_user_id AS uid, messages.time FROM messages LEFT JOIN login ON login.fk_user_id = messages.fk_user_id WHERE fk_room_id = '" . $room . "' AND messages_id < ".$start." ORDER BY messages_id DESC LIMIT 20;");
            $messages = array_reverse(mysqli_fetch_all($msg_query, MYSQLI_ASSOC));
            echo $this->parseJSON($messages);
        }
    }
    
    /** Selbsterklärend....
     * 
     * @param String $messages
     * @return string
     */
    private function parseJSON($messages){
        $send = '[';
        foreach ($messages AS $message) {
            if ($message['uid'] == $this->uid){
               $control = 'own_message';
            }else{
               $control = 'new_message';
            }   
            $send .= '{"message": "' . $message['message'] . '", "name": "' . $message['display_name'] . '", "time":"' . $message['time'] . '", "control":"'.$control.'", "id":"'.$message['messages_id'].'"},';
        }
        if (count($messages)>0){
            $send = substr($send, 0, -1);
        }
        $send .= "]"; 
        return $send;
    }
}

class RoomHandler{
    
    /** Prüft, ob ein Benutzer in einem Raum ist
     *  NOT YET IMPLEMENTED!!!
     * @param int $uid
     * @param int $room
     * @return boolean
     */
    public static function isInRoom($uid, $room) {
        return true;
        /* $result = $this->dbHandler->query("SELECT id FROM room_user_relation WHERE fk_user_id = '".$uid."' AND fk_room_id = '".$room."';");
          $row = $result->fetch_object();
          if (isset($row->id)){
          return true
          }else{
          return false;
          } */
    }
}