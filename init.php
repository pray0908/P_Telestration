<?php
// 에러 표시 설정
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 데이터베이스 연결 정보
$serverName = "127.0.0.1";  
$uid = "sa";  
$pwd = "skrxk0908@";
$database = "TelestrationGame";

// 데이터베이스 연결
try {
    $conn = new PDO(
        "sqlsrv:Server=$serverName;Database=$database;TrustServerCertificate=1", 
        $uid,
        $pwd
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die(json_encode(["error" => "Error connecting to SQL Server: " . $e->getMessage()]));
}
?>