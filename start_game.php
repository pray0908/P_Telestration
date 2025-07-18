<?php
require_once 'init.php';

header('Content-Type: application/json');

try {
    // 현재 게임에 참여 중인 플레이어들 확인 (logined = 0 또는 1)
    $stmt = $conn->prepare("SELECT player_number, name FROM players WHERE logined IN (0, 1) ORDER BY player_number");
    $stmt->execute();
    $currentPlayers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($currentPlayers)) {
        throw new Exception('참여 중인 플레이어가 없습니다.');
    }
    
    // 이전 게임 데이터 정리
    // 1. 이전 current_game 레코드 삭제
    $stmt = $conn->prepare("DELETE FROM current_game");
    $stmt->execute();
    
    // 2. 이전 game_rounds 레코드 삭제
    $stmt = $conn->prepare("DELETE FROM game_rounds");
    $stmt->execute();
    
    // 3. logined > 1인 이전 게임 플레이어들만 삭제 (완료된 이전 게임들)
    $stmt = $conn->prepare("DELETE FROM players WHERE logined > 1");
    $stmt->execute();
    
    // 4. 현재 게임 참여자들을 모두 logined=1로 설정 (게임 진행 상태)
    $stmt = $conn->prepare("UPDATE players SET logined = 1 WHERE logined IN (0, 1)");
    $stmt->execute();
    
    // 현재 게임 참여자 수 확인
    $stmt = $conn->prepare("SELECT COUNT(*), MAX(player_number) FROM players WHERE logined = 1");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_NUM);
    $playerCount = $result[0];
    $maxPlayerNumber = $result[1];
    
    // topics 테이블에서 랜덤하게 1개의 단어를 가져온다
    $stmt = $conn->prepare("SELECT TOP 1 text FROM topics ORDER BY NEWID()");
    $stmt->execute();
    $topic = $stmt->fetchColumn();
    
    if (!$topic) {
        throw new Exception('제시어를 가져올 수 없습니다.');
    }
    
    // 가져온 단어를 current_game 테이블에 insert
    $stmt = $conn->prepare("INSERT INTO current_game (topics, current_turn, game_status) VALUES (?, 1, 'playing')");
    $stmt->execute([$topic]);
    
    echo json_encode([
        'success' => true, 
        'topic' => $topic,
        'player_count' => $playerCount,
        'max_player_number' => $maxPlayerNumber,
        'current_players' => $currentPlayers,
        'debug' => "Game restarted with {$playerCount} players (1 to {$maxPlayerNumber})"
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>