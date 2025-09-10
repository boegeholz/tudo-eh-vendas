<?php

setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');


$connectionInfo = [
    "Database" => "tudoehvendasdb",
    "Uid" => "boegeholz",
    "PWD" => "Lucas3006*",
    "CharacterSet" => "UTF-8",
    "Encrypt" => 1,
    "TrustServerCertificate" => 0
];
$serverName = "tcp:gsinfo.database.windows.net,1433";
$conn = sqlsrv_connect($serverName, $connectionInfo);

function displayAlert($msg)
{
    echo "<script type='text/javascript'>alert('" . $msg . "');</script>";
}

?>