/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var accept = false;

function showPop(popId){
    document.getElementById(popId).style.display= 'inline'; 
}

function hidePopAccept(popId){
    document.getElementById(popId).style.display='none';
    /*popup wegmachen */
}

function hidePopCancel(popId){
    document.getElementById(popId).style.display='none';
    /* einstellungen ignorieren und zurückgehen*/
}

/*
 * Ende der Popup-Funktionen. Es folgen spezifische Funktionen die von den Popups ausgeführt werden.
 * 
 */


function savePersonalSettings(){
    /* meinen bgColor submit abfragen, im switch Wert ermitteln und entsprechend handler füttern*/
    var data = $('#personal-settings-form').serialize();
    $.post("handler.php?action=savePersonalSettings", data);     
    hidePopAccept('personal_settings');
    window.location='./';
}

function getPersonalSettings(){
    $.post("handler.php?action=getPersonalSettings", "", function(returndata){
        var parsed = JSON.parse(returndata);
        for (var obj in parsed){
            if (document.getElementById(obj) != null){
                document.getElementById(obj).value = parsed[obj]; 
            }
        }
    });
}

function showPersonalSettings(){
    getPersonalSettings();
    showPop('personal_settings');
}

function settingLivePreview(divid){
    switch(divid){
        case 'background-color':
            document.getElementsByTagName("body")[0].style["background-color"] = document.getElementById(divid).value;
            break;
        case 'font-family':
            document.getElementsByTagName("body")[0].style["font-family"] = document.getElementById(divid).value;
            break;
        case 'text-color':
            document.getElementById("messages").style = "color: "+document.getElementById(divid).value;
            break;
        case 'message-background-color':
            var objs = document.getElementsByClassName("message");
            for (var msg in objs){
                objs[msg].style["background-color"] = document.getElementById(divid).value;
            }
        case 'message-style':
            var style;
            switch (document.getElementById(divid).value){
                case "edge":
                    style = "border: solid 1px black; border-radius: 0px;";
                    break;
                case "light-round":
                    style = "border: solid 1px black; border-radius: 5px;";
                    break;
                case "round":
                    style = "border: solid 1px black; border-radius: 20px; padding-left: 20px; padding-right: 20px";
                    break;
                case "dotted":
                    style = "border: dotted 1px black; border-radius: 2px;";
                    break;
                default: 
                    break;
            }
            var objs = document.getElementsByClassName("message");
            for (var msg in objs){
                objs[msg].style = style;
                objs[msg].style["background-color"] = document.getElementById("message-background-color").value;
            }
            break;
        default:
            break;
    }
}

getPersonalSettings();