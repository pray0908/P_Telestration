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
            'current_turn' => 1,
            'debug' => "Game not started, waiting players: {$waitingPlayers}"
        ]);
        exit;
    }
    
    // 게임 완료 상태 확인
    $stmt = $conn->prepare("SELECT game_status, final_answer, is_correct, topics, current_turn FROM current_game ORDER BY id DESC");
    $stmt->execute();
    $gameInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($gameInfo && $gameInfo['game_status'] === 'completed') {
        // 게임이 완료됨
        echo json_encode([
            'success' => true, 
            'game_started' => true,
            'game_completed' => true,
            'is_correct' => $gameInfo['is_correct'] == 1,
            'correct_answer' => $gameInfo['topics'],
            'final_answer' => $gameInfo['final_answer'],
            'debug' => "Game completed"
        ]);
        exit;
    }
    
    // 현재 턴 확인
    $currentTurn = $gameInfo['current_turn'] ?? 1;
    
    // 최대 플레이어 번호 확인 (마지막 순번)
    $stmt = $conn->prepare("SELECT MAX(player_number) FROM players WHERE logined = 1");
    $stmt->execute();
    $maxPlayerNumber = $stmt->fetchColumn();
    
    $isMyTurn = ($currentTurn == $playerNumber);
    $isLastPlayer = ($playerNumber == $maxPlayerNumber);
    
    echo json_encode([
        'success' => true, 
        'game_started' => true,
        'game_completed' => false,
        'is_my_turn' => $isMyTurn,
        'is_last_player' => $isLastPlayer,
        'current_turn' => $currentTurn,
        'max_player_number' => $maxPlayerNumber,
        'debug' => "Player {$playerNumber} check: current_turn={$currentTurn}, is_my_turn=" . ($isMyTurn ? 'true' : 'false') . ", max_player={$maxPlayerNumber}"
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>