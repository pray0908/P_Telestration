<?php
require_once 'init.php';

header('Content-Type: application/json');

try {
    // POST 데이터 받기
    $input = json_decode(file_get_contents('php://input'), true);
    $playerName = $input['name'] ?? '';
    
    if (empty($playerName)) {
        throw new Exception('플레이어 이름이 필요합니다.');
    }
    
    // logined가 0인 레코드가 있는지 확인
    $stmt = $conn->prepare("SELECT COUNT(*) FROM players WHERE logined = 0");
    $stmt->execute();
    $hasWaitingPlayers = $stmt->fetchColumn() > 0;
    
    if (!$hasWaitingPlayers) {
        // logined가 0인 레코드가 없으면 player_number = 1로 insert
        $playerNumber = 1;
    } else {
        // 가장 마지막 player_number를 가져온 뒤 +1
        $stmt = $conn->prepare("SELECT MAX(player_number) FROM players WHERE logined = 0");
        $stmt->execute();
        $maxPlayerNumber = $stmt->fetchColumn();
        $playerNumber = $maxPlayerNumber + 1;
    }
    
    // 데이터 insert
    $stmt = $conn->prepare("INSERT INTO players (player_number, name, logined) VALUES (?, ?, 0)");
    $stmt->execute([$playerNumber, $playerName]);
    
    echo json_encode(['success' => true, 'player_number' => $playerNumber]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>