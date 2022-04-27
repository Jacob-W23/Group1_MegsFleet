<?php

require_once "config.php";

$username = $_POST["username"];
$password = $_POST["password"];

$conn = new mysqli($hn,$un,$pw,$db);

if($conn->connect_error) {
    $res["status"] = "error";
    $res["status_message"] = "MySQL Connection Error";
} else {
    $stmt = $conn->prepare("SELECT * FROM users WHERE fName=?");

    $stmt->bind_param("s",$username);

    if($stmt->execute()) {
        $result = $stmt->get_result();

        $rows = $result->num_rows;

        if($rows >= 1) {
            foreach($result as $item) {
                if(password_verify($password,$item["uPassword"])) {
                    session_start();
                    $_SESSION["role"] = $item["Role"];
                    $_SESSION["username"] = $item["fName"];
                    $_SESSION["id"] = $item["UserID"];
                    $_SESSION["auth"] = true;

                    $res["status"] = "Success";
                    $res["role"] = $item["Role"];
                    $res["username"] = $item["fName"];
                    $res["id"] = $item["UserID"];
                    $res["session"] = session_id();
                    $res["auth"] = true;
                } else {
                    $res["status"] = "Error";
                    $res["status_message"] = "Incorrect password.";
                }
            }
        } else {
            $res["status"] = "Error";
            $res["status_message"] = "No user found.";
        }
    } else {
        $res["status"] = "Error";
        $res["status_message"] = $stmt->error;
    }
}

echo json_encode($res);

?>