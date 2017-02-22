<!DOCTYPE html>
<?php
    require_once 'functions.php';
    $name = $sessionhandler->getLoginUserName();
    $settingsArray = $userhandler->getSettingsArray();
?>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="UTF-8">
        <title>HWR-Chat</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"> <!-- Bootstrap - CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"> <!-- Bootstrap - CSS -->
        <link rel="stylesheet" href="style/chat.css" type="text/css">
        <link rel="stylesheet" href="style/popup.css" type="text/css">
        <script src="https://code.jquery.com/jquery-3.1.0.min.js" type="text/javascript"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" type="text/javascript"></script>
		<script src="script/modalFeedback.js" type="text/javascript"></script>
        <script src="script/popup.js" type="text/javascript"></script>   
        <?php if($name){?>
        <style>
            .message { background-color: <?php echo $settingsArray['message-background-color'];?> }
            <?php
            echo "\n";
            switch ($settingsArray['message-style']){
                case "edge":
                    echo ".message {border: solid 1px black; }";
                    break;
                case "light-round":
                    echo ".message {border: solid 1px black; border-radius: 5px;}";
                    break;
                case "round":
                    echo ".message {border: solid 1px black; border-radius: 20px; padding-left: 20px; padding-right: 20px}";
                    break;
                case "dotted":
                    echo ".message {border: dotted 1px black; border-radius: 2px;}";
                    break;
                default: 
                    break;
            }
            ?>
        </style>
        <script src="script/chat.js" type="text/javascript"></script>
        <?php } ?>
    </head>
    <body onfocus="setActive();" onblur="setPassive();" style="background-color:<?php echo $settingsArray['background-color'];?>; font-family: <?php echo $settingsArray['font-family'];?>" onload="init();" onbeforeunload="leave();">
        <?php
        if ($name){
            $sessionhandler->enter(1);
            ?>
            <div id="headline">
                <span class="title">HWR-Chat</span>
				<span class="menuopener">
					<a href='#' onclick="toggleMenu();">
						<li><img src="img/user-silhouette.svg" height="20px"><span class="name"><?php echo $name;?></span></li></a>
					<span id="menu">
						<li><a href="#" data-toggle="modal" data-target="#modalFeedback">Feedback</a></li>
						<li><a href="#" onclick="showPersonalSettings();">persönliche Einstellungen</a></li>
						<li><a href="handler.php?action=logout">Logout</a></li>
					</span>
				</span>
            </div>
            <div id="messages" onscroll="fromOnScroll();" style="color:<?php echo $settingsArray['text-color'];?>">
                <p class="block" id="loadMoreMessages">
                    <span id="button_up">
                        <img src="img/up.png" height="50%" width="50%" onclick="loadMoreMessages(1);">
                    </span>
                </p>
                <p class="block">
                    <span id="button_down">
                        <img src="img/down.png" onclick="fromButtonScroll();" height="50%" width="50%">
                    </span>
                </p>
            </div>
            <div id="userlist">
                 <?php    
                 $userlist = $sessionhandler->readUserList(1);
                 foreach ($userlist AS $user){
                     echo "<li>".$user[0]."</li>";
                 }
                 ?>
            </div>
            <div id="message_input">
                <input name="message" id="message" placeholder="Insert Message here..." onkeypress="sendMessage(event);" class="message_input">
            </div>
            <!-- personal settings -->
            <form id="personal-settings-form">
                <div class="popup" id="personal_settings">
                    <div id="dropDowns">
                        <div  class="auswahlTitel" id="background-color-div">
                            <label>Hintergrundfarbe:
                                <select class="auswahl" id="background-color" name="background-color" onchange="settingLivePreview('background-color');">
                                    <option value="#FFC3E1">Pink</option>
                                    <option value="#B2B2FF">Blau</option>
                                    <option value="#FF0000">Rot</option>
                                    <option value="#99FF99">Grün</option>
                                    <option value="lightgrey">Grau</option>
                                    <option value="black">Schwarz</option>
                                    <option value="white">Weiß</option>
                                </select>
                            </label>
                        </div> 
                        <br><br>       
                        <div class="auswahlTitel" id="font-family-div">
                            <label>Schriftart:
                                <select class="auswahl" id="font-family" name="font-family" onchange="settingLivePreview('font-family');">
                                    <option value="arial">Arial</option>
                                    <option value="times">Times New Roman</option>
                                    <option value="Comic Sans MS">Comic</option>
                                </select>
                            </label>
                        </div> 
                        <br><br>
                        <div  class="auswahlTitel" id="text-color-div">
                            <label>Schriftfarbe:
                                <select class="auswahl" id="text-color" name="text-color" onchange="settingLivePreview('text-color');">
                                    <option value="#FFC3E1">Pink</option>
                                    <option value="#B2B2FF">Blau</option>
                                    <option value="#FF0000">Rot</option>
                                    <option value="#99FF99">Grün</option>
                                    <option value="lightgrey">Grau</option>
                                    <option value="black">Schwarz</option>
                                    <option value="white">Weiß</option>
                                </select>
                            </label>
                        </div>
                        <br><br>
                        <div  class="auswahlTitel" id="message-background-color-div">
                            <label>Message Hintergrundfarbe:
                                <select class="auswahl" id="message-background-color" name="message-background-color" onchange="settingLivePreview('message-background-color');">
                                    <option value="#FFC3E1">Pink</option>
                                    <option value="#B2B2FF">Blau</option>
                                    <option value="#FF0000">Rot</option>
                                    <option value="#99FF99">Grün</option>
                                    <option value="lightgrey">Grau</option>
                                    <option value="black">Schwarz</option>
                                    <option value="white">Weiß</option>
                                </select>
                            </label>
                        </div> 
                        <br><br>
                        <div  class="auswahlTitel" id="message-count-div">
                            <label>Anzahl der geladenen Messages:
                                <select class="auswahl" id="message-count" name="message-count">
                                    <option value="0">keine</option>
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                </select>
                            </label>
                        </div> 
                        <br><br>
                        <div class="auswahlTitel" id="message-style-div">
                            <label>Messages Style:
                                <select class="auswahl" id="message-style" name="message-style" onchange="settingLivePreview('message-style');">
                                    <option value="edge">eckig</option>
                                    <option value="light-round">leicht abgerundet</option>
                                    <option value="round">rund</option>
                                    <option value="dotted">gepunktet</option>
                                </select>
                            </label>
                        </div> 
                    </div>
                    <div class="buttons">
                        <input id="popUpHideAccept" type="button" value="Bestätigen" onclick="savePersonalSettings();"></input>
                        <input id="pupUpHideCancel" type="button" value="Abbrechen" onclick="window.location='./';"></input>
                    </div>
                </div>
            </form>
<!-- Feedback-Modal - begin -->
			<div class="modal fade" id="modalFeedback" tabindex="-1" role="dialog" aria-labelledby="feedbackModalFeedback">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<form method="POST" id="formFeedback" onsubmit="return saveFeedback()"> <!-- noch kein Backend vorhanden -->
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h4 class="modal-title" id="feedbackModalFeedback">Bitte gib uns dein Feedback</h4>
							</div>
							<div class="modal-body">
								<label>Wie gefällt dir der Chat? (5 - gut, 1 - schlecht)</label>
								<div class="form-group">
									<label class="radio-inline">
										<input type="radio" name="chatIsGoodOrBar" value="5"> 5
									</label>
									<label class="radio-inline">
										<input type="radio" name="chatIsGoodOrBar" value="4"> 4
									</label>
									<label class="radio-inline">
										<input type="radio" name="chatIsGoodOrBar" value="3"> 3
									</label>
									<label class="radio-inline">
										<input type="radio" name="chatIsGoodOrBar" value="2"> 2
									</label>
									<label class="radio-inline">
										<input type="radio" name="chatIsGoodOrBar" value="1"> 1
									</label>
								</div>
								<label>Hast du alles gefunden?</label>
								<div class="form-group">
									<label class="radio-inline">
										<input type="radio" name="foundEverything" value="1"> Ja
									</label>
									<label class="radio-inline">
										<input type="radio" name="foundEverything" value="0"> Nein
									</label>
								</div>
								<label>Welche Funktionen vermisst du?</label> <!-- http://formvalidation.io/examples/adding-dynamic-field/ -->
								<div class="form-group">
									<div class="input-group">
										<span class="input-group-addon"><i class="glyphicon glyphicon-pencil"></i></span>
										<input type="text" class="form-control" name="missing[]" />
										<span class="input-group-btn">
											<button type="button" class="btn btn-primary addButton"><i class="glyphicon glyphicon-plus"></i></button>
										</span>
									</div>
								</div>
								<div class="form-group hide" id="missingTemplate">
									<div class="input-group">
										<span class="input-group-addon"><i class="glyphicon glyphicon-pencil"></i></span>
										<input type="text" class="form-control" name="missing[]" />
										<span class="input-group-btn">
											<button type="button" class="btn btn-danger removeButton"><i class="glyphicon glyphicon-minus"></i></button>
										</span>
									</div>
								</div>
								<label>Platz für mehr</label>
								<div class="form-group">
									<textarea class="form-control" rows="5" name="comment" placeholder="Hier kannst du deiner Kreativität freien Lauf lassen und uns noch mehr Feedback geben. :)"></textarea>
								</div>
							</div>
							<div class="modal-footer">
								<div class="form-group">
									<button type="button" class="btn btn-danger" data-dismiss="modal">
										<span class="glyphicon glyphicon-remove"></span> Abbrechen
									</button>
									<button type="submit" class="btn btn-success">
										<span class="glyphicon glyphicon-envelope"></span> Abschicken
									</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
			<div class="modal fade" id="modalFeedbackSuccess" tabindex="-1" role="dialog" aria-labelledby="feedbackModalFeedbackSuccess">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<form method="POST" id="formFeedbackSuccess" onsubmit="return closeModalFeedback('modalFeedbackSuccess')"> <!-- noch kein Backend vorhanden -->
							<div class="modal-header">
								<h4 class="modal-title" id="feedbackModalFeedbackSuccess">Erfolgreich übermittelt</h4>
							</div>
							<div class="modal-footer">
								<div class="form-group">
									<button type="submit" class="btn btn-success">
										<span class="glyphicon glyphicon-thumbs-up"></span> Ok
									</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
			<div class="modal fade" id="modalFeedbackFail" tabindex="-1" role="dialog" aria-labelledby="feedbackModalFeedbackFail">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<form method="POST" id="formFeedbackFail" onsubmit="return closeModalFeedback('modalFeedbackFail')"> <!-- noch kein Backend vorhanden -->
							<div class="modal-header">
								<h4 class="modal-title" id="feedbackModalFeedbackFail">Fehler bei der Übermittlung</h4>
							</div>
							<div class="modal-footer">
								<div class="form-group">
									<button type="submit" class="btn btn-danger">
										<span class="glyphicon glyphicon-thumbs-down"></span> Ok
									</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
<!-- Feedback-Modal - ende -->
            <?php
        }else{
            $msg = filter_input(INPUT_GET, 'msg');
            $message = false;
        if ($msg != ''){
            switch ($msg){
                    case 'err_login':
                        $message = 'Die Logindaten waren leider falsch.';
                        break;
                    case 'rec_logout':
                        $message = 'Der Server hat die Verbindung getrennt.';
                        break;
                }
            }
            ?>
        <div class="jumbotron center-block" style="border:0px solid #888;box-shadow:0px 2px 5px #ccc;max-width:750px;padding:20px">
                <h2><center>HWR-Chat</center></h2>
        </div>
        <div class="center-block" style="border:1px solid #888;box-shadow:0px 2px 5px #ccc;max-width:750px;padding:20px;background-color:#F8F8FF">
            <form action="handler.php?action=login" method="post">
                <h3>Bitte gb deine Anmeldedaten ein</h3>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                        <input class="form-control" name="user_name" placeholder="Benutzername" required autofocus /> <!-- autofocus ? -->
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                        <input class="form-control" type="password" name="user_password" placeholder="Passwort" required />
                    </div>
                </div>
                <div class="form-group text-right">
					<?php
						if ($dbhandler->getStatus() == 0) // Datenbank erreichbar // Achtung! Test-Wert
						{
							echo '<button type="submit" class="btn btn-success">
											<span class="glyphicon glyphicon-log-in"></span> Anmelden
									</button>';
						}
						else // nicht erreichbar -> Button deaktiviert
						{
							echo '<button type="submit" class="btn btn-success" disabled>
											<span class="glyphicon glyphicon-log-in"></span> Anmelden
									</button>';
						}
					?>
            </div>
            <?php
                if ($message)
                {
                    echo '<div class="alert alert-danger">' . $message . '</div>';  
                }
                if ($dbhandler->getStatus() != 0) // Datenbank erreichbar // Achtung! Test-Wert
                { // Meldung, Datenbank nicht erreichbar
                    echo '<div class="alert alert-danger"><b>Datenbank</b> nicht erreichbar!</div>';
                }
            }
        ?> 
    </body>
</html>