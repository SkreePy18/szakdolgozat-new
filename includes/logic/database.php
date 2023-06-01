<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// connect to database
$host = "XXXX";
$user_name = "XXXX";
$password = "XXXX";
$database = "XXXX";
$conn = new mysqli($host, $user_name, $password, $database);

// Check connection
if ($conn->connect_error)
{
    die("Connection failed: " . $conn->connect_error);
}

// switch on transactions
mysqli_autocommit($conn, true);

define('RANDOM_SECURITY', 'XXXXXXXXXXXX'); // random seed for CSRF token

function getMultipleRecords($sql, $types = null, $params = [])
{
    global $conn;

    $stmt = $conn->prepare($sql);

    if (!empty($types) && !empty($params))
    { // parameters must exist before you call bind_param() method
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();

    $result = $stmt->get_result();
    $user = $result->fetch_all(MYSQLI_ASSOC);

    $stmt->close();

    return $user;
}
function getSingleRecord($sql, $types = null, $params = [])
{
    global $conn;

    $stmt = $conn->prepare($sql);

    if (!empty($types) && !empty($params))
    { // parameters must exist before you call bind_param() method
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();

    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    $stmt->close();

    return $user;
}
function modifyRecord($sql, $types, $params)
{
    global $conn;

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);

    $result = $stmt->execute();

    $stmt->close();

    return $result;
}

?>
