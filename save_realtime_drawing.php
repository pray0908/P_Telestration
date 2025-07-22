<?php
require_once 'init.php';

header('Content-Type: application/json');

// 로그 파일 경로
$logFile = 'save_realtime_drawing.log';

// 로그 함수
function writeLog($message) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[{$timestamp}] {$message}" . PHP_EOL;
    file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
}

try {
    //writeLog("=== 실시간 그림 저장 요청 시작 ===");
    
    // POST 데이터 받기
    $rawInput = file_get_contents('php://input');
    //writeLog("Raw input length: " . strlen($rawInput));
    
    $input = json_decode($rawInput, true);
    
    if (!$input) {
        throw new Exception('JSON 데이터 파싱 실패 - 원본 데이터: ' . substr($rawInput, 0, 100));
    }
    
    $drawingData = $input['drawing_data'] ?? '';
    $playerNumber = $input['player_number'] ?? 0;
    $gameId = $input['game_id'] ?? 0;
    
    //writeLog("입력 데이터: player_number={$playerNumber}, game_id={$gameId}, drawing_data_length=" . strlen($drawingData));
    
    // 입력 검증
    if (empty($drawingData)) {
        throw new Exception('그림 데이터가 비어있습니다.');
    }
    
    if ($playerNumber <= 0) {
        throw new Exception('잘못된 플레이어 번호입니다: ' . $playerNumber);
    }
    
    if ($gameId <= 0) {
        throw new Exception('잘못된 게임 ID입니다: ' . $gameId);
    }
    
    // Base64 데이터 검증 (간단히)
    if (!preg_match('/^data:image\/(png|jpeg|jpg);base64,/', $drawingData)) {
        throw new Exception('올바르지 않은 이미지 데이터 형식입니다.');
    }
    
    //writeLog("입력 검증 완료");
    
    // 현재 게임이 진행 중인지 확인
    $stmt = $conn->prepare("SELECT COUNT(*) FROM current_game WHERE id = ? AND game_status = 'playing'");
    $stmt->execute([$gameId]);
    $gameExists = $stmt->fetchColumn();
    
    //writeLog("게임 존재 확인 쿼리 실행: game_id={$gameId}, result={$gameExists}");
    
    if ($gameExists == 0) {
        throw new Exception('진행 중인 게임을 찾을 수 없습니다: game_id=' . $gameId);
    }
    
    //writeLog("게임 상태 확인 완료: game_id={$gameId}");
    
    // 실시간 그림 데이터 저장
    $stmt = $conn->prepare("INSERT INTO real_time_drawings (game_id, player_number, drawing_data, created_at) VALUES (?, ?, ?, GETDATE())");
    $result = $stmt->execute([$gameId, $playerNumber, $drawingData]);
    
    //writeLog("INSERT 쿼리 실행: result=" . ($result ? 'true' : 'false'));
    
    if (!$result) {
        throw new Exception('데이터베이스 저장 실패');
    }
    
    // SQL Server에서 마지막 삽입 ID 가져오기
    $stmt = $conn->query("SELECT @@IDENTITY AS last_id");
    $lastIdResult = $stmt->fetch(PDO::FETCH_ASSOC);
    $insertedId = $lastIdResult['last_id'] ?? 'unknown';
    
    //writeLog("데이터베이스 저장 성공: inserted_id={$insertedId}");
    
    // 성공 응답
    $response = [
        'success' => true,
        'message' => '실시간 그림 저장 완료',
        'data' => [
            'id' => $insertedId,
            'game_id' => $gameId,
            'player_number' => $playerNumber,
            'timestamp' => date('Y-m-d H:i:s')
        ]
    ];
    
    //writeLog("응답 준비 완료: " . json_encode($response));
    echo json_encode($response);
    
} catch (Exception $e) {
    $errorMessage = $e->getMessage();
    //writeLog("ERROR: " . $errorMessage);
    
    $response = [
        'success' => false, 
        'error' => $errorMessage,
        'debug' => [
            'timestamp' => date('Y-m-d H:i:s'),
            'input_received' => isset($input) ? true : false,
            'player_number' => $playerNumber ?? 'not_set',
            'game_id' => $gameId ?? 'not_set'
        ]
    ];
    
    echo json_encode($response);
} finally {
    //writeLog("=== 실시간 그림 저장 요청 종료 ===\n");
}
?>