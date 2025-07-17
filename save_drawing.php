<?php
require_once 'init.php';

header('Content-Type: application/json');

try {
    // POST 데이터 받기
    $input = json_decode(file_get_contents('php://input'), true);
    $drawingData = $input['drawing_data'] ?? '';
    $playerNumber = $input['player_number'] ?? 0;
    $roundNumber = $input['round_number'] ?? 1;
    
    if (empty($drawingData) || $playerNumber <= 0) {
        throw new Exception('잘못된 데이터입니다.');
    }
    
    // 그림 데이터 저장
    $stmt = $conn->prepare("INSERT INTO game_rounds (round_number, player_number, drawing_data) VALUES (?, ?, ?)");
    $stmt->execute([$roundNumber, $playerNumber, $drawingData]);
    
    // 현재 턴을 다음 플레이어로 업데이트 (SQL Server 호환)
    $nextTurn = $playerNumber + 1;
    
    // 최신 current_game 레코드의 ID 가져오기
    $stmt = $conn->prepare("SELECT TOP 1 id FROM current_game ORDER BY id DESC");
    $stmt->execute();
    $currentGameId = $stmt->fetchColumn();
    
    if ($currentGameId) {
        // 특정 ID의 레코드만 업데이트
        $stmt = $conn->prepare("UPDATE current_game SET current_turn = ? WHERE id = ?");
        $stmt->execute([$nextTurn, $currentGameId]);
    } else {
        throw new Exception('current_game 레코드를 찾을 수 없습니다.');
    }
    
    // 업데이트 확인
    $stmt = $conn->prepare("SELECT current_turn FROM current_game WHERE id = ?");
    $stmt->execute([$currentGameId]);
    $updatedTurn = $stmt->fetchColumn();
    
    echo json_encode([
        'success' => true, 
        'next_turn' => $nextTurn,
        'updated_turn' => $updatedTurn,
        'current_game_id' => $currentGameId,
        'debug' => "Player {$playerNumber} completed, next turn: {$nextTurn}, updated to: {$updatedTurn}"
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>