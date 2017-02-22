function getmessages(room){
    xmlhttp=new XMLHttpRequest();
    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            postMessage(xmlhttp.responseText);
        }
    };
    xmlhttp.open("GET","../handler.php?action=recieveMessage&room="+room,true);
    xmlhttp.send();
    setTimeout("getmessages("+room+")", 500);
}
getmessages(1);