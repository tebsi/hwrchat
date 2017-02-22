<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'functions.php';
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');


while (true){
    echo 'data: '. $messagehandler->readMessages(1);
    flush();
    ob_flush();
    usleep(50000);
}