<?php

require_once "config.php";
require_once "utils.php";

$session = $_POST["session"];

session_id($session);
session_start();

$auth = $_SESSION["auth"];

if ($auth && validateInput())
{
    $dotId = $_POST['dotID'];
    $type = $_POST['type'];
    $make = $_POST['make'];
    $model = $_POST['model'];
    $miles = $_POST['miles'];
    $status = $_POST['status'];
    $maint = $_POST['maintenance'];

    mysqli_report(MYSQLI_REPORT_ALL);
    $connection = new mysqli($hn, $un, $pw, $db);

    if ($connection->connect_error)
    {
        $response['outcome'] = "error";
        $response['err-msg'] = "Could not connect to database";
    }
    else
    {
        $stmt = $connection->prepare("UPDATE vehicles SET DOTID=?, Type=?, Make=?, Model=?, CurrentMileage=?, Status=?, LastMaintMileage=? WHERE DOTID=?");
        $stmt->bind_param("sssssssss", $dotId, $type, $make, $model, $miles, $status, $maint, $dotId);

        if ($stmt->execute())
        {
            $response['outcome'] = "success";
            $response['err-msg'] = "vehicle id $dotId updated";
        }
        else
        {
            $response['outcome'] = "error";
            $response['err-msg'] = "Could not update vehicle";
        }

        $stmt->close();
    }

    $connection->close();
}
else
{
    $response['outcome'] = "error";
    $response['err-msg'] = "Not authorized or incomplete data";
}

echo json_encode($response);

?>