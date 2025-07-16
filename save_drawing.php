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
    
    // 현재 턴을 다음 플레이어로 업데이트
    $nextTurn = $playerNumber + 1;
    $stmt = $conn->prepare("UPDATE current_game SET current_turn = ?");
    $stmt->execute([$nextTurn]);
    
    echo json_encode(['success' => true, 'next_turn' => $nextTurn]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>