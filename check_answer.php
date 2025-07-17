<?php
require_once 'init.php';

header('Content-Type: application/json');

try {
    // POST 데이터 받기
    $input = json_decode(file_get_contents('php://input'), true);
    $userAnswer = trim($input['answer'] ?? '');
    $playerNumber = $input['player_number'] ?? 0;
    
    if (empty($userAnswer) || $playerNumber <= 0) {
        throw new Exception('잘못된 데이터입니다.');
    }
    
    // 현재 게임의 제시어 가져오기
    $stmt = $conn->prepare("SELECT topics FROM current_game ORDER BY id DESC");
    $stmt->execute();
    $correctAnswer = $stmt->fetchColumn();
    
    if (!$correctAnswer) {
        throw new Exception('제시어를 찾을 수 없습니다.');
    }
    
    // 정답 체크 (대소문자 구분 없이, 공백 제거)
    $isCorrect = (strtolower(trim($correctAnswer)) === strtolower(trim($userAnswer)));
    
    // 결과 저장 (선택적으로 game_results 테이블에 저장 가능)
    // 현재는 단순히 결과만 반환
    
    echo json_encode([
        'success' => true, 
        'is_correct' => $isCorrect,
        'correct_answer' => $correctAnswer,
        'user_answer' => $userAnswer,
        'player_number' => $playerNumber
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>