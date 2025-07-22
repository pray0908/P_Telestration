<?php
require_once 'init.php';

header('Content-Type: application/json');

// 로그 파일 경로
$logFile = 'get_realtime_status.log';

// 로그 함수
function writeLog($message) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[{$timestamp}] {$message}" . PHP_EOL;
    file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
}

try {
    writeLog("=== 실시간 상태 조회 요청 시작 ===");
    
    // 현재 진행 중인 게임 정보 가져오기
    $stmt = $conn->prepare("SELECT TOP 1 id, topics, current_turn, game_status FROM current_game ORDER BY id DESC");
    $stmt->execute();
    $gameInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$gameInfo) {
        throw new Exception('진행 중인 게임이 없습니다.');
    }
    
    $gameId = $gameInfo['id'];
    $currentTurn = $gameInfo['current_turn'];
    $gameStatus = $gameInfo['game_status'];
    $topics = $gameInfo['topics'];
    
    writeLog("게임 정보: game_id={$gameId}, current_turn={$currentTurn}, status={$gameStatus}");
    
    // 게임이 시작되지 않았거나 완료된 경우
    if ($gameStatus !== 'playing') {
        $response = [
            'success' => true,
            'game_started' => false,
            'game_status' => $gameStatus,
            'message' => '게임이 진행 중이 아닙니다.',
            'debug' => [
                'game_id' => $gameId,
                'status' => $gameStatus,
                'timestamp' => date('Y-m-d H:i:s')
            ]
        ];
        
        writeLog("게임 미진행 상태로 응답");
        echo json_encode($response);
        exit;
    }
    
    // 🔧 수정된 부분: 현재 게임 ID로 참여 중인 플레이어만 조회
    $stmt = $conn->prepare("SELECT player_number, name FROM players WHERE logined = 1 AND game_id = ? ORDER BY player_number");
    $stmt->execute([$gameId]);
    $players = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    writeLog("현재 게임 ({$gameId})의 참여 플레이어 수: " . count($players));
    
    if (empty($players)) {
        throw new Exception('현재 게임에 참여 중인 플레이어가 없습니다.');
    }
    
    // 🔧 개선된 부분: 하나의 쿼리로 모든 플레이어의 최신 그림을 한 번에 가져오기
    $playersWithDrawings = [];
    
    // CTE를 사용한 최적화된 쿼리
    $stmt = $conn->prepare("
        WITH LatestDrawings AS (
            SELECT 
                game_id, 
                player_number, 
                drawing_data, 
                created_at,
                ROW_NUMBER() OVER (PARTITION BY player_number ORDER BY created_at DESC) as rn
            FROM real_time_drawings 
            WHERE game_id = ?
        )
        SELECT player_number, drawing_data, created_at
        FROM LatestDrawings 
        WHERE rn = 1
    ");
    $stmt->execute([$gameId]);
    $latestDrawings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    writeLog("실시간 그림 조회 완료: " . count($latestDrawings) . "개 그림 발견");
    
    // 플레이어 데이터와 그림 데이터 결합
    $drawingsByPlayer = [];
    foreach ($latestDrawings as $drawing) {
        $drawingsByPlayer[$drawing['player_number']] = $drawing;
    }
    
    foreach ($players as $player) {
        $playerNumber = $player['player_number'];
        $playerName = $player['name'];
        
        $playerData = [
            'player_number' => $playerNumber,
            'name' => $playerName,
            'is_current_turn' => ($currentTurn == $playerNumber),
            'has_drawing' => false,
            'drawing_data' => null,
            'last_updated' => null
        ];
        
        if (isset($drawingsByPlayer[$playerNumber])) {
            $drawing = $drawingsByPlayer[$playerNumber];
            $playerData['has_drawing'] = true;
            $playerData['drawing_data'] = $drawing['drawing_data'];
            $playerData['last_updated'] = $drawing['created_at'];
        }
        
        $playersWithDrawings[] = $playerData;
        
        writeLog("플레이어 {$playerNumber} ({$playerName}): drawing=" . ($playerData['has_drawing'] ? 'yes' : 'no'));
    }
    
    // 성공 응답
    $response = [
        'success' => true,
        'game_started' => true,
        'game_status' => $gameStatus,
        'game_info' => [
            'game_id' => $gameId,
            'topics' => $topics,
            'current_turn' => $currentTurn,
            'total_players' => count($players)  // 🔧 정확한 참여자 수
        ],
        'players' => $playersWithDrawings,
        'timestamp' => date('Y-m-d H:i:s'),
        'debug' => [
            'query_time' => date('Y-m-d H:i:s'),
            'players_count' => count($players),
            'drawings_found' => count(array_filter($playersWithDrawings, function($p) { return $p['has_drawing']; })),
            'filtered_by_game_id' => $gameId
        ]
    ];
    
    writeLog("성공 응답 준비 완료: players=" . count($playersWithDrawings) . " (game_id={$gameId}로 필터링됨)");
    echo json_encode($response);
    
} catch (Exception $e) {
    $errorMessage = $e->getMessage();
    writeLog("ERROR: " . $errorMessage);
    
    $response = [
        'success' => false, 
        'error' => $errorMessage,
        'game_started' => false,
        'debug' => [
            'timestamp' => date('Y-m-d H:i:s'),
            'error_location' => 'get_realtime_status.php'
        ]
    ];
    
    echo json_encode($response);
} finally {
    writeLog("=== 실시간 상태 조회 요청 종료 ===\n");
}
?>