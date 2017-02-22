// Initialisierung des Chats
var autoscroll = true;                                          // Autoscroll (Standart an, wird deaktiviert wenn manuell gescrollt wird, reaktiviert wenn der nach-unten-button gedrückt wird
var fromreciever = true;                                        // Wer Scrollt (Reciever / Manuell)
var firstMessage = 0;                                           // Variable für die Nachricht die abgeholt werdenn soll beim ältere Nachrichten laden
//var reciever = new Worker("script/reciever.js");                // Reciever starten
var notify = false;                                             // Benachrichtigung anzeigen (Hört auf Focus/blur)
Notification.requestPermission();                               // Notifications erfragen
readFirstMessages(1);                                           // Und los gehts, bitte die ersten Nachrichten, lieber Herr Server!
window.setTimeout(terminateFromReciever, 100);                  // Scrollen Auf Manuell setzen
//reciever.onmessage = function(event){postMessage(event);};      // Nachrichten abfangen und eintragen
window.onload = function(){scrollToBottom();};                  // Nach unten scrollen

//Initialisierung abgeschlossen

/*var source = new EventSource("servlet.php");
source.onmessage = function(event) {
    postMessage(event);
};
*/
/** body.onFocus / Benachrichtigungen ausschalten
 * 
 */
function setActive(){
    notify = false;
}
/** body.onBlur / Benachrichtigungen einschalten
 * 
 */
function setPassive(){
    notify = true;
}

/** Benachrichtigung anzeigen
 * 
 * @return void
 */
function notifyMe(){
    if (Notification.permission === "granted"){
        new Notification('Neue Nachrichten', { 
               body: 'Sie haben neue Nachrichten im HWR-Chatclient!'
        });    
    }
}

/** Nachricht einlesen und anzeigen
 * 
 * @param event event
 */
function postMessage(event){
    fromreciever = true;
    var msgs = parseHTML(JSON.parse(event.data));
    
    if (msgs != ''){
        document.getElementById("messages").innerHTML += msgs; 
        if (notify){
            notifyMe();
        }
        scrollToBottom();
    }
    window.setTimeout(terminateFromReciever, 100);
}

/** Setzt scrollen wieder auf Manuell
 * 
 */
function terminateFromReciever(){
    fromreciever = false;
}

/** Nachricht absetzen... Auf Enter
 * 
 * @param {event} event
 */
function sendMessage(event){
    if (event.keyCode == 13){
        var data = {'message': $('#message').val()};
        $('#message').val("");
        $.ajax("handler.php?action=sendMessage", data, function(){});        
    }
}

/** Beim Klicken auf den Nach-Oben-Knopf
 * 
 * @param {int} room
 */
function loadMoreMessages(room){
    fromOnScroll();
    $.ajax("handler.php?action=getOlderMessages&room="+room+"&start="+firstMessage, {} , function(returndata){
        var content = JSON.parse(returndata);
        firstMessage = content[0].id;
        $('#loadMoreMessages').after(parseHTML(content));
    });
}

/** Die ersten 10 Nachrichten vom Server laden (Beim betreten des Chats)
 * 
 * @param {int} room
 */
function readFirstMessages(room){
    var data = {'room':room};
    $.ajax ({ url: "handler.php?action=getFirstMessages",
        data: data})
        .done(function(returndata){
        var Jmsgs = JSON.parse(returndata);
        firstMessage = Jmsgs[0].id;
        var msgs = parseHTML(Jmsgs);
        document.getElementById("messages").innerHTML += msgs;
        scrollToBottom();
    });
}

/** Beim Scrollen: Zeigt beim Manuellen (!) Scrollen den Runterscrollen-Button an.
 * 
 */
function fromOnScroll(){
    if (!fromreciever){
        autoscroll = false;
        document.getElementById("button_down").style.display  = "inline";
        setPassive();
    }
}

/** Beim Anklicken des Nach-Unten-scrollen-Buttons
 * 
 */
function fromButtonScroll(){
    setActive();
    autoscroll = true;
    fromreciever = true;
    scrollToBottom();
    document.getElementById("button_down").style.display  = "none"; 
}

/** Die eigentliche automatische Nach-Unten_Scroll_Funktion, die nur scrollt, wenn autoscroll true ist 
 * 
 */
function scrollToBottom(){
    if (autoscroll){
        sH = document.getElementById('messages').scrollHeight;
        oH = document.getElementById('messages').offsetHeight;
        if (sH > oH){
            document.getElementById('messages').scrollTop += (sH-oH);
        }
        document.getElementById("button_down").style.display  = "none"; 
    }
}

/** Parst eine eingegangene Nachricht. Bei bestimmten Nachrichten aktionen ausführen.
 * 
 * @param {JSON-Objekt} msgs
 * @return {String|returner} 
 */
function parseHTML(msgs){
    returner = '';
    for (i=0;i<msgs.length;i++){
        if (msgs[i].control == 'logout'){
            document.location.href='index.php?msg=rec_logout';
        }
        if (msgs[i].control == 'refresh'){
            document.location.href='index.php';
        }
        if (msgs[i].control == "own_message" || msgs[i].control == "new_message"){
            returner += '<p class="block"><span class="message '+msgs[i].control+'"><span class="originator">'+msgs[i].name+' ('+formatTime(msgs[i].time)+')</span><br><span class="message_content">'+msgs[i].message+'</span></span></p>';  
        }
        if (msgs[i].control == 'clearuserlist'){
            document.getElementById('userlist').innerHTML = "";
        }
        if (msgs[i].control == 'useradd'){
            document.getElementById('userlist').innerHTML += "<li>"+msgs[i].user+"</li>";
        }
    }
    return returner;
}

/** Wandelt einen Unix-Code Timestamp in ein schön formatiertes Datum um
 * 
 * @param {int} time
 * @return {String}
 */
function formatTime(time){
    var d = new Date(time*1000);
    var today = new Date();
    var returnMe = '';
    minutes = (d.getMinutes()<10)?'0'+d.getMinutes():d.getMinutes();
    if (d.getDate() == today.getDate() && d.getMonth() == today.getMonth() && d.getFullYear() == today.getFullYear()){
        returnMe = d.getHours()+":"+minutes;
    }else{
        month = ((d.getMonth()+1)<10)?'0'+(d.getMonth()+1):(d.getMonth()+1);
        returnMe = d.getDate()+"."+month+"."+d.getFullYear()+" "+d.getHours()+":"+minutes;
    }
    return returnMe;
}

function toggleMenu(){
    if (document.getElementById('menu').style.display == "none"){
        document.getElementById('menu').style.display = "inline";
    }else{
        document.getElementById('menu').style.display = "none";
    }
}
function init(){
    document.getElementById('menu').style.display = "none";
}

function leave(){
    $.post("handler.php?action=leave", null);
}