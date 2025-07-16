<?php
require_once 'init.php';

header('Content-Type: application/json');

try {
    $playerNumber = $_GET['player_number'] ?? 0;
    
    if ($playerNumber <= 0) {
        throw new Exception('잘못된 플레이어 번호입니다.');
    }
    
    // 게임이 시작되었는지 확인 (logined=0인 플레이어가 있는지)
    $stmt = $conn->prepare("SELECT COUNT(*) FROM players WHERE logined = 0");
    $stmt->execute();
    $waitingPlayers = $stmt->fetchColumn();
    $gameStarted = $waitingPlayers == 0; // 0인 레코드가 없으면 게임 시작됨
    
    if (!$gameStarted) {
        // 게임이 시작되지 않았음
        echo json_encode([
            'success' => true, 
            'game_started' => false,
            'is_my_turn' => false,
            'current_turn' => 1
        ]);
        exit;
    }
    
    // 현재 턴 확인
    $stmt = $conn->prepare("SELECT current_turn FROM current_game ORDER BY id DESC");
    $stmt->execute();
    $currentTurn = $stmt->fetchColumn();
    
    if ($currentTurn === false) {
        throw new Exception('게임 정보를 찾을 수 없습니다.');
    }
    
    $isMyTurn = ($currentTurn == $playerNumber);
    
    echo json_encode([
        'success' => true, 
        'game_started' => true,
        'is_my_turn' => $isMyTurn,
        'current_turn' => $currentTurn
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>