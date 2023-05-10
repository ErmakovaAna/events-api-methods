<?php
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');

    include_once '../config/DB.php';
    include_once '../models/Event.php';

    $database = new Database();
    $db = $database->connect();

    $events = new Event($db);

    $event_type = isset($_GET['event_type']) ? $_GET['event_type'] : '';
    $event_date = isset($_GET['event_date']) ? $_GET['event_date'] : '';
    $count_by = isset($_GET['count_by']) ? $_GET['count_by'] : '';

    $result = $events->filter_events($event_type, $event_date, $count_by);

    print_r(json_encode($result));

?>