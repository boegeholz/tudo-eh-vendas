<?php
// PHP Data Objects(PDO) Sample Code:
try {
    $conn = new PDO("sqlsrv:server = tcp:gsinfo.database.windows.net,1433; Database = tudoehvendasdb", "boegeholz", "Lucas3006*");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (PDOException $e) {
    print("Error connecting to SQL Server.");
    die(print_r($e));
}

// SQL Server Extension Sample Code:
$connectionInfo = array("UID" => "boegeholz", "pwd" => "Lucas3006*", "Database" => "tudoehvendasdb", "LoginTimeout" => 30, "Encrypt" => 1, "TrustServerCertificate" => 0);
$serverName = "tcp:gsinfo.database.windows.net,1433";
$conn = sqlsrv_connect($serverName, $connectionInfo);
?>