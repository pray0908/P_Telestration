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
            'game_id' => null,  // 게임 시작 전에는 null
            'debug' => "Game not started, waiting players: {$waitingPlayers}"
        ]);
        exit;
    }
    
    // 게임 완료 상태 확인 (현재 게임 정보 가져오기)
    $stmt = $conn->prepare("SELECT id, game_status, final_answer, is_correct, topics, current_turn FROM current_game ORDER BY id DESC");
    $stmt->execute();
    $gameInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$gameInfo) {
        throw new Exception('게임 정보를 찾을 수 없습니다.');
    }
    
    $currentGameId = $gameInfo['id'];
    $currentTurn = $gameInfo['current_turn'] ?? 1;
    $gameStatus = $gameInfo['game_status'];
    
    if ($gameStatus === 'completed') {
        // 게임이 완료됨
        echo json_encode([
            'success' => true, 
            'game_started' => true,
            'game_completed' => true,
            'game_id' => $currentGameId,  // 중요: 게임 ID 반환
            'is_correct' => $gameInfo['is_correct'] == 1,
            'correct_answer' => $gameInfo['topics'],
            'final_answer' => $gameInfo['final_answer'],
            'debug' => "Game completed, ID: {$currentGameId}"
        ]);
        exit;
    }
    
    // 최대 플레이어 번호 확인 (마지막 순번)
    $stmt = $conn->prepare("SELECT MAX(player_number) FROM players WHERE logined = 1");
    $stmt->execute();
    $maxPlayerNumber = $stmt->fetchColumn();
    
    // 모든 참여 플레이어 목록 확인 (디버깅용)
    $stmt = $conn->prepare("SELECT player_number, name FROM players WHERE logined = 1 ORDER BY player_number");
    $stmt->execute();
    $allPlayers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $isMyTurn = ($currentTurn == $playerNumber);
    $isLastPlayer = ($playerNumber == $maxPlayerNumber);
    
    echo json_encode([
        'success' => true, 
        'game_started' => true,
        'game_completed' => false,
        'game_id' => $currentGameId,  // 중요: 항상 게임 ID 반환
        'is_my_turn' => $isMyTurn,
        'is_last_player' => $isLastPlayer,
        'current_turn' => $currentTurn,
        'max_player_number' => $maxPlayerNumber,
        'all_players' => $allPlayers,
        'debug' => "Player {$playerNumber} check: current_turn={$currentTurn}, is_my_turn=" . ($isMyTurn ? 'true' : 'false') . ", max_player={$maxPlayerNumber}, is_last=" . ($isLastPlayer ? 'true' : 'false') . ", game_id={$currentGameId}"
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>