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
    
    // 🔧 핵심 추가: Ready 버튼을 누른다는 것은 새로운 게임 세션이 시작된다는 의미
    // 따라서 이전 게임의 모든 플레이어 레코드를 정리
    $stmt = $conn->prepare("DELETE FROM players WHERE game_id IS NOT NULL");
    $stmt->execute();
    
    // logined가 0이고 game_id가 NULL 또는 0인 대기 중인 플레이어가 있는지 확인
    $stmt = $conn->prepare("SELECT COUNT(*) FROM players WHERE logined = 0 AND (game_id IS NULL OR game_id = 0)");
    $stmt->execute();
    $hasWaitingPlayers = $stmt->fetchColumn() > 0;
    
    if (!$hasWaitingPlayers) {
        // 대기 중인 플레이어가 없으면 player_number = 1로 시작
        $playerNumber = 1;
    } else {
        // 가장 마지막 player_number를 가져온 뒤 +1
        $stmt = $conn->prepare("SELECT MAX(player_number) FROM players WHERE logined = 0 AND (game_id IS NULL OR game_id = 0)");
        $stmt->execute();
        $maxPlayerNumber = $stmt->fetchColumn();
        $playerNumber = $maxPlayerNumber + 1;
    }
    
    // 데이터 insert (game_id는 아직 NULL로 설정)
    $stmt = $conn->prepare("INSERT INTO players (player_number, name, logined, game_id) VALUES (?, ?, 0, NULL)");
    $stmt->execute([$playerNumber, $playerName]);
    
    echo json_encode([
        'success' => true, 
        'player_number' => $playerNumber,
        'debug' => "Previous game players cleared, new player {$playerName} joined as #{$playerNumber}"
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>