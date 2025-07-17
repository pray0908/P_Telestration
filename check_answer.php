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
    $stmt = $conn->prepare("SELECT TOP 1 topics, id FROM current_game ORDER BY id DESC");
    $stmt->execute();
    $gameInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$gameInfo || !$gameInfo['topics']) {
        throw new Exception('제시어를 찾을 수 없습니다.');
    }
    
    $correctAnswer = $gameInfo['topics'];
    $currentGameId = $gameInfo['id'];
    
    // 정답 체크 (대소문자 구분 없이, 공백 제거)
    $isCorrect = (strtolower(trim($correctAnswer)) === strtolower(trim($userAnswer)));
    
    // 게임 결과를 current_game 테이블에 저장 (WHERE 절 사용)
    $stmt = $conn->prepare("UPDATE current_game SET game_status = 'completed', final_answer = ?, is_correct = ? WHERE id = ?");
    $stmt->execute([$userAnswer, $isCorrect ? 1 : 0, $currentGameId]);
    
    // 업데이트 확인
    $stmt = $conn->prepare("SELECT game_status, is_correct FROM current_game WHERE id = ?");
    $stmt->execute([$currentGameId]);
    $updatedInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true, 
        'is_correct' => $isCorrect,
        'correct_answer' => $correctAnswer,
        'user_answer' => $userAnswer,
        'player_number' => $playerNumber,
        'current_game_id' => $currentGameId,
        'updated_status' => $updatedInfo['game_status'] ?? 'unknown',
        'debug' => "Player {$playerNumber} answered '{$userAnswer}', correct: " . ($isCorrect ? 'true' : 'false')
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>