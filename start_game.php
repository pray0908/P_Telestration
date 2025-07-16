<?php
require_once 'init.php';

header('Content-Type: application/json');

try {
    // 현재 접속 중인 사람들(logined=0)을 모두 logined=1로 update
    $stmt = $conn->prepare("UPDATE players SET logined = 1 WHERE logined = 0");
    $stmt->execute();
    
    // topics 테이블에서 랜덤하게 1개의 단어를 가져온다
    $stmt = $conn->prepare("SELECT TOP 1 text FROM topics ORDER BY NEWID()");
    $stmt->execute();
    $topic = $stmt->fetchColumn();
    
    if (!$topic) {
        throw new Exception('제시어를 가져올 수 없습니다.');
    }
    
    // 가져온 단어를 current_game 테이블에 insert
    $stmt = $conn->prepare("INSERT INTO current_game (topics) VALUES (?)");
    $stmt->execute([$topic]);
    
    echo json_encode(['success' => true, 'topic' => $topic]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>