<?php
class Event {
    private $conn;
    private $table = 'events';

    public $id;
    public $event_type;
    public $user_status;
    public $user_address;
    public $event_date;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function save($event_type, $user_status) {

        $user_address = $_SERVER['REMOTE_ADDR'];
        $event_date = date('Y-m-d', $_SERVER['REQUEST_TIME']);

        $query = "INSERT INTO " . $this->table . " (event_type, user_status, user_address, event_date) VALUES (?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([$event_type, $user_status, $user_address, $event_date]);

        return $stmt;
    }

    function filter_events($event_type, $event_date, $count_by) {
        $query = "SELECT event_type, event_date, ";

        switch ($count_by) {
            case "by_event":
                $query .= "COUNT(*) as event_count ";
                break;
            case "by_user":
                $query .= "user_address, COUNT(*) as event_count ";
                break;
            case "by_status":
                $query .= "user_status, COUNT(*) as event_count ";
                break;
            default:
                throw new Exception("Invalid aggregation type specified");
        }

        $query .= "FROM events WHERE event_type = :event_type AND event_date = :event_date ";

        switch ($count_by) {
            case "by_event":
                $query .= "GROUP BY event_type, event_date";
                break;
            case "by_user":
                $query .= "GROUP BY event_type, event_date, user_address";
                break;
            case "by_status":
                $query .= "GROUP BY event_type, event_date, user_status";
                break;
        }

        $stmt = $this->conn->prepare($query);
        $stmt->execute(array(
            ':event_type' => $event_type,
            ':event_date' => $event_date
        ));

        $result = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $event_type = $row['event_type'];
            $event_date = date('Y-m-d', strtotime($row['event_date']));
            $event_count = $row['event_count'];

            switch ($count_by) {
                case "by_event":
                    $json_result[] = array(
                        'event_type' => $event_type,
                        'event_date' => $event_date,
                        'values_count' => $event_count
                    );
                    break;
                case "by_user":
                    $user_address = $row['user_address'];
                    if (!isset($result)) {
                        $result = array();
                    }
                    $result[$user_address] = $event_count;
                    $json_result = array(
                        'event_type' => $event_type,
                        'event_date' => $event_date,
                        'users' => $result
                    );
                    break;
                case "by_status":
                    $user_status = $row['user_status'];
                    if (!isset($result)) {
                        $result = array(
                            'authorized' => 0,
                            'unauthorized' => 0
                        );
                    }
                    if ($user_status == 'authorized') {
                        $result['authorized'] = $event_count;
                    } else {
                        $result['unauthorized'] = $event_count;
                    }
                    $json_result = array(
                        'event_type' => $event_type,
                        'event_date' => $event_date,
                        'status' => $result
                    );
                    break;
            }
        }
        return $json_result;
    }

}

?>