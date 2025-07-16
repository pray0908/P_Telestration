<?php
require_once 'init.php';

header('Content-Type: application/json');

try {
    $playerNumber = $_GET['player_number'] ?? 0;
    $roundNumber = $_GET['round_number'] ?? 1;
    
    if ($playerNumber <= 0) {
        throw new Exception('잘못된 플레이어 번호입니다.');
    }
    
    // 이전 플레이어의 그림 데이터 가져오기
    $previousPlayer = $playerNumber - 1;
    
    if ($previousPlayer > 0) {
        $stmt = $conn->prepare("SELECT drawing_data FROM game_rounds WHERE round_number = ? AND player_number = ? order by id desc");
        $stmt->execute([$roundNumber, $previousPlayer]);
        $drawingData = $stmt->fetchColumn();
        
        if ($drawingData) {
            echo json_encode(['success' => true, 'drawing_data' => $drawingData]);
        } else {
            echo json_encode(['success' => false, 'error' => '이전 그림을 찾을 수 없습니다.']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => '첫 번째 플레이어입니다.']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>