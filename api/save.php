<?php
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');

    include_once '../config/DB.php';
    include_once '../models/Event.php';

    $database = new Database();
    $db = $database->connect();

    $event = new Event($db);

    $event_type = isset($_POST['event_type']) ? $_POST['event_type'] : null;
    $user_status = isset($_POST['user_status']) ? $_POST['user_status'] : null;

    $result = $event->save($event_type, $user_status);

?>