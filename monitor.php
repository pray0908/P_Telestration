<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>텔레스트레이션 - 실시간 모니터링</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Fredoka+One:wght@400&family=Noto+Sans+KR:wght@400;700;900&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Noto Sans KR', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
            overflow-x: auto;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
            min-height: calc(100vh - 40px);
            display: flex;
            flex-direction: column;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding: 15px 25px;
            background: linear-gradient(135deg, #ff6b6b, #4ecdc4);
            border-radius: 15px;
            color: white;
            position: relative;
            overflow: hidden;
            min-height: 80px;
        }
        
        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: rotate 10s linear infinite;
        }
        
        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .title-section {
            text-align: center;
            order: 2;
        }
        
        .game-title {
            font-family: 'Fredoka One', cursive;
            font-size: 2.5rem;
            margin-bottom: 5px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            position: relative;
            z-index: 1;
        }
        
        .game-status {
            font-size: 1.2rem;
            font-weight: 700;
            position: relative;
            z-index: 1;
        }
        

        
        .info-left {
            display: flex;
            align-items: center;
            order: 1;
            flex: 1;
            justify-content: flex-start;
        }
        
        .info-right {
            display: flex;
            gap: 15px;
            align-items: center;
            order: 3;
            flex: 1;
            justify-content: flex-end;
        }
        
        .info-item {
            background: rgba(255, 255, 255, 0.2);
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.95rem;
            backdrop-filter: blur(5px);
            white-space: nowrap;
        }
        
        .players-grid {
            display: grid;
            gap: 15px;
            margin-top: 15px;
            flex: 1;
            min-height: 0;
        }
        
        /* 그리드 레이아웃 - 참여자 수에 따라 동적 설정 */
        .grid-1x1 { grid-template-columns: 1fr; }
        .grid-1x2 { grid-template-columns: repeat(2, 1fr); }
        .grid-2x2 { grid-template-columns: repeat(2, 1fr); grid-template-rows: repeat(2, 1fr); }
        .grid-2x3 { grid-template-columns: repeat(3, 1fr); grid-template-rows: repeat(2, 1fr); }
        .grid-3x3 { grid-template-columns: repeat(3, 1fr); grid-template-rows: repeat(3, 1fr); }
        .grid-3x4 { grid-template-columns: repeat(4, 1fr); grid-template-rows: repeat(3, 1fr); }
        
        .player-card {
            background: white;
            border-radius: 15px;
            padding: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border: 3px solid transparent;
            position: relative;
            overflow: hidden;
        }
        
        .player-card.current-turn {
            border-color: #ff6b6b;
            box-shadow: 0 0 30px rgba(255, 107, 107, 0.5);
            animation: pulse-glow 2s ease-in-out infinite;
        }
        
        @keyframes pulse-glow {
            0%, 100% { 
                transform: scale(1); 
                box-shadow: 0 0 30px rgba(255, 107, 107, 0.5);
            }
            50% { 
                transform: scale(1.02); 
                box-shadow: 0 0 40px rgba(255, 107, 107, 0.8);
            }
        }
        
        .player-card.current-turn::before {
            content: '⭐ 현재 턴 ⭐';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            background: linear-gradient(45deg, #ff6b6b, #feca57);
            color: white;
            text-align: center;
            padding: 5px;
            font-weight: 900;
            font-size: 0.9rem;
            z-index: 10;
            animation: blink 1s ease-in-out infinite alternate;
        }
        
        @keyframes blink {
            0% { opacity: 1; }
            100% { opacity: 0.7; }
        }
        
        .player-header {
            text-align: center;
            margin-bottom: 15px;
            margin-top: 25px; /* 현재 턴 배너 공간 */
        }
        
        .player-number {
            display: inline-block;
            background: linear-gradient(45deg, #4ecdc4, #45b7d1);
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            line-height: 40px;
            font-weight: 900;
            font-size: 1.2rem;
            margin-bottom: 8px;
        }
        
        .player-name {
            font-size: 1.1rem;
            font-weight: 700;
            color: #333;
        }
        
        .canvas-area {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 10px;
            text-align: center;
            min-height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        
        .canvas-area.has-drawing {
            background: white;
            border-color: #4ecdc4;
        }
        
        .player-canvas {
            max-width: 95%;
            max-height: 280px;
            width: auto;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }
        
        .no-drawing {
            color: #6c757d;
            font-size: 1rem;
            font-style: italic;
        }
        
        .waiting-screen {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }
        
        .waiting-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .waiting-text {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .waiting-subtext {
            font-size: 1rem;
            color: #999;
        }
        
        .error-screen {
            text-align: center;
            padding: 60px 20px;
            color: #dc3545;
        }
        
        .error-icon {
            font-size: 4rem;
            margin-bottom: 20px;
        }
        
        .connection-status {
            position: fixed;
            top: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.9);
            padding: 10px 15px;
            border-radius: 25px;
            font-weight: 700;
            font-size: 0.9rem;
            backdrop-filter: blur(10px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }
        
        .connection-status.online {
            color: #28a745;
            border: 2px solid #28a745;
        }
        
        .connection-status.offline {
            color: #dc3545;
            border: 2px solid #dc3545;
            animation: shake 0.5s ease-in-out infinite;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-3px); }
            75% { transform: translateX(3px); }
        }
        
        /* 게임 완료 결과 화면 스타일 추가 */
        .result-screen {
            text-align: center;
            padding: 60px 20px;
            color: #333;
        }
        
        .result-screen.correct {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            margin: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        
        .result-screen.incorrect {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            border-radius: 15px;
            margin: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        
        .result-icon {
            font-size: 5rem;
            margin-bottom: 30px;
            animation: bounce 2s ease-in-out infinite;
        }
        
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-20px); }
            60% { transform: translateY(-10px); }
        }
        
        .result-title {
            font-size: 2.5rem;
            font-weight: 900;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .result-message {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 30px;
        }
        
        .answer-box {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            padding: 20px;
            margin: 15px auto;
            max-width: 400px;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.3);
        }
        
        .answer-label {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 8px;
            opacity: 0.9;
        }
        
        .answer-text {
            font-size: 1.5rem;
            font-weight: 700;
            word-break: break-all;
        }
        
        /* 반응형 디자인 */
        @media (max-width: 1200px) {
            .container {
                padding: 15px;
            }
            
            .game-title {
                font-size: 2rem;
            }
            
            .header {
                flex-direction: column;
                gap: 15px;
                padding: 15px 20px;
                min-height: auto;
            }
            
            .info-left, .info-right {
                flex: none !important;
                justify-content: center !important;
            }
            
            .title-section {
                order: 1 !important;
            }
            
            .info-left {
                order: 2 !important;
            }
            
            .info-right {
                order: 3 !important;
                gap: 10px;
            }
        }
        
        @media (max-width: 768px) {
            body {
                padding: 5px;
            }
            
            .container {
                padding: 10px;
            }
            
            .game-title {
                font-size: 1.8rem;
            }
            
            .game-status {
                font-size: 1rem;
            }
            
            .header {
                padding: 12px 15px;
            }
            
            .info-right {
                gap: 8px;
            }
            
            .info-item {
                font-size: 0.85rem;
                padding: 6px 12px;
            }
            
            .players-grid {
                gap: 10px;
                max-height: calc(100vh - 250px);
            }
            
            .player-card {
                padding: 10px;
            }
            
            .result-title {
                font-size: 2rem;
            }
            
            .result-message {
                font-size: 1.1rem;
            }
            
            .answer-text {
                font-size: 1.3rem;
            }
        }
        
        /* 게임 완료 축하 효과 */
        .celebration {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 9999;
        }
        
        .confetti {
            position: absolute;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            animation: confetti-fall 3s linear infinite;
        }
        
        @keyframes confetti-fall {
            0% {
                transform: translateY(-100vh) rotate(0deg);
                opacity: 1;
            }
            100% {
                transform: translateY(100vh) rotate(720deg);
                opacity: 0;
            }
        }
    </style>
</head>
<body>
    <div class="connection-status" id="connectionStatus">🔄 연결 확인 중...</div>
    
    <div class="container">
        <div class="header">
            <div class="info-left" id="gameInfoLeft" style="display: none;">
                <div class="info-item">
                    <span id="topicInfo">제시어: -</span>
                </div>
            </div>
            
            <div class="title-section">
                <h1 class="game-title">🎨 텔레스트레이션 LIVE</h1>
                <div class="game-status" id="gameStatus">게임 상태 확인 중...</div>
            </div>
            
            <div class="info-right" id="gameInfoRight" style="display: none;">
                <div class="info-item">
                    <span id="currentTurnInfo">턴: -</span>
                </div>
                <div class="info-item">
                    <span id="playerCountInfo">참여자: -</span>
                </div>
            </div>
        </div>
        
        <div id="mainContent" style="flex: 1; display: flex; flex-direction: column;">
            <div class="waiting-screen">
                <div class="waiting-icon">⏳</div>
                <div class="waiting-text">게임 데이터 로딩 중...</div>
                <div class="waiting-subtext">잠시만 기다려주세요</div>
            </div>
        </div>
    </div>

    <script>
        let updateInterval = null;
        let lastUpdateTime = Date.now();
        let connectionRetries = 0;
        let maxRetries = 5;
        let isUpdating = false;
        let lastGameData = null;
        let requestStartTime = 0;
        let averageResponseTime = 0;
        let responseTimeCount = 0;
        let celebrationShown = false; // 축하 효과 중복 방지
        
        // 페이지 로드 시 시작
        window.addEventListener('load', function() {
            console.log('모니터링 페이지 시작');
            startMonitoring();
        });
        
        // 모니터링 시작
        function startMonitoring() {
            if (updateInterval) {
                clearInterval(updateInterval);
            }
            
            // 즉시 첫 업데이트
            updateGameStatus();
            
            // 100ms마다 업데이트 (사용자가 변경한 주기)
            updateInterval = setInterval(updateGameStatus, 100);
            console.log('실시간 모니터링 시작 (100ms 간격)');
        }
        
        // 게임 상태 업데이트
        function updateGameStatus() {
            if (isUpdating) {
                console.log('이전 요청 진행 중 - 건너뛰기');
                return;
            }
            
            isUpdating = true;
            requestStartTime = performance.now(); // 요청 시작 시간 기록
            
            fetch('get_realtime_status.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                // 응답 시간 계산
                const responseTime = performance.now() - requestStartTime;
                responseTimeCount++;
                averageResponseTime = ((averageResponseTime * (responseTimeCount - 1)) + responseTime) / responseTimeCount;
                
                // 성능 로그 (10회마다)
                if (responseTimeCount % 10 === 0) {
                    console.log(`평균 응답 시간: ${averageResponseTime.toFixed(1)}ms`);
                }
                
                connectionRetries = 0;
                updateConnectionStatus(true, responseTime);
                lastUpdateTime = Date.now();
                
                if (data.success) {
                    if (data.game_started) {
                        displayGameInProgress(data);
                    } else {
                        displayWaitingScreen(data.game_status || 'waiting', data);
                    }
                } else {
                    displayErrorScreen(data.error || '알 수 없는 오류');
                }
                
                lastGameData = data;
            })
            .catch(error => {
                const responseTime = performance.now() - requestStartTime;
                console.error('업데이트 실패 (응답시간: ' + responseTime.toFixed(1) + 'ms):', error);
                connectionRetries++;
                updateConnectionStatus(false, responseTime);
                
                if (connectionRetries >= maxRetries) {
                    displayErrorScreen('서버 연결 실패 (재시도 ' + connectionRetries + '/' + maxRetries + ')');
                }
            })
            .finally(() => {
                isUpdating = false;
            });
        }
        
        // 게임 진행 중 화면 표시
        function displayGameInProgress(data) {
            const gameInfo = data.game_info;
            const players = data.players;
            
            // 헤더 정보 업데이트
            document.getElementById('gameStatus').textContent = '🎮 게임 진행 중';
            document.getElementById('gameInfoLeft').style.display = 'flex';
            document.getElementById('gameInfoRight').style.display = 'flex';
            
            document.getElementById('currentTurnInfo').textContent = `턴: ${gameInfo.current_turn}번`;
            document.getElementById('playerCountInfo').textContent = `참여자: ${gameInfo.total_players}명`;
            document.getElementById('topicInfo').textContent = `제시어: ${gameInfo.topics || '비밀'}`;
            
            // 플레이어 그리드 생성
            createPlayersGrid(players, gameInfo.current_turn);
            
            // 게임 진행 중이므로 축하 효과 플래그 초기화
            celebrationShown = false;
        }
        
        // 플레이어 그리드 생성
        function createPlayersGrid(players, currentTurn) {
            const mainContent = document.getElementById('mainContent');
            
            // 그리드 클래스 결정
            const gridClass = getGridClass(players.length);
            
            mainContent.innerHTML = `
                <div class="players-grid ${gridClass}" id="playersGrid" style="flex: 1; display: grid;">
                    ${players.map(player => createPlayerCard(player, currentTurn)).join('')}
                </div>
            `;
        }
        
        // 그리드 클래스 결정
        function getGridClass(playerCount) {
            if (playerCount <= 1) return 'grid-1x1';
            if (playerCount <= 2) return 'grid-1x2';
            if (playerCount <= 4) return 'grid-2x2';
            if (playerCount <= 6) return 'grid-2x3';
            if (playerCount <= 9) return 'grid-3x3';
            if (playerCount <= 12) return 'grid-3x4';
            return 'grid-3x4'; // 기본값
        }
        
        // 플레이어 카드 생성
        function createPlayerCard(player, currentTurn) {
            const isCurrentTurn = player.is_current_turn || (player.player_number === currentTurn);
            const hasDrawing = player.has_drawing && player.drawing_data;
            
            let canvasContent;
            if (hasDrawing) {
                canvasContent = `
                    <img src="${player.drawing_data}" class="player-canvas" alt="${player.name}의 그림">
                `;
            } else {
                canvasContent = `<div class="no-drawing">아직 그림이 없습니다</div>`;
            }
            
            return `
                <div class="player-card ${isCurrentTurn ? 'current-turn' : ''}" data-player="${player.player_number}">
                    <div class="player-header">
                        <div class="player-number">${player.player_number}</div>
                        <div class="player-name">${player.name}</div>
                    </div>
                    <div class="canvas-area ${hasDrawing ? 'has-drawing' : ''}">
                        ${canvasContent}
                    </div>
                </div>
            `;
        }
        
        // 대기 화면 표시
        function displayWaitingScreen(status, data) {
            document.getElementById('gameInfoLeft').style.display = 'none';
            document.getElementById('gameInfoRight').style.display = 'none';
            
            let waitingText, waitingSubtext, waitingIcon;
            let screenContent;
            
            // 게임 완료 시 정답/오답 결과 표시
            if (status === 'completed' && data && typeof data.is_correct !== 'undefined') {
                const isCorrect = data.is_correct;
                const correctAnswer = data.correct_answer || '알 수 없음';
                const finalAnswer = data.final_answer || '입력 없음';
                
                if (isCorrect) {
                    // 정답 화면
                    document.getElementById('gameStatus').textContent = '🎉 정답!';
                    
                    screenContent = `
                        <div class="result-screen correct">
                            <div class="result-icon">🎉</div>
                            <div class="result-title">CORRECT!</div>
                            <div class="result-message">완벽한 정답입니다!</div>
                            <div class="answer-box">
                                <div class="answer-label">정답</div>
                                <div class="answer-text">${correctAnswer}</div>
                            </div>
                            <div class="answer-box">
                                <div class="answer-label">플레이어 답안</div>
                                <div class="answer-text">${finalAnswer}</div>
                            </div>
                        </div>
                    `;
                    
                    // 정답 축하 효과 (한 번만)
                    if (!celebrationShown) {
                        setTimeout(() => showCelebration(), 500);
                        celebrationShown = true;
                    }
                    
                } else {
                    // 오답 화면  
                    document.getElementById('gameStatus').textContent = '💝 좋은 시도!';
                    
                    screenContent = `
                        <div class="result-screen incorrect">
                            <div class="result-icon">💝</div>
                            <div class="result-title">GOOD TRY!</div>
                            <div class="result-message">정말 잘했어요!</div>
                            <div class="answer-box">
                                <div class="answer-label">정답</div>
                                <div class="answer-text">${correctAnswer}</div>
                            </div>
                            <div class="answer-box">
                                <div class="answer-label">플레이어 답안</div>
                                <div class="answer-text">${finalAnswer}</div>
                            </div>
                        </div>
                    `;
                }
                
            } else {
                // 기존 대기 화면 로직
                document.getElementById('gameStatus').textContent = '⏰ 게임 시작 대기 중';
                
                switch(status) {
                    case 'completed':
                        waitingText = '게임 완료!';
                        waitingSubtext = '새로운 게임을 기다리고 있습니다';
                        waitingIcon = '🎉';
                        break;
                    default:
                        waitingText = '게임 시작을 기다리는 중...';
                        waitingSubtext = '참여자들이 접속하면 게임이 시작됩니다';
                        waitingIcon = '⏳';
                }
                
                screenContent = `
                    <div class="waiting-screen">
                        <div class="waiting-icon">${waitingIcon}</div>
                        <div class="waiting-text">${waitingText}</div>
                        <div class="waiting-subtext">${waitingSubtext}</div>
                    </div>
                `;
            }
            
            document.getElementById('mainContent').innerHTML = screenContent;
        }
        
        // 에러 화면 표시
        function displayErrorScreen(errorMessage) {
            document.getElementById('gameStatus').textContent = '❌ 연결 오류';
            document.getElementById('gameInfoLeft').style.display = 'none';
            document.getElementById('gameInfoRight').style.display = 'none';
            
            document.getElementById('mainContent').innerHTML = `
                <div class="error-screen">
                    <div class="error-icon">⚠️</div>
                    <div class="waiting-text">연결 오류 발생</div>
                    <div class="waiting-subtext">${errorMessage}</div>
                </div>
            `;
        }
        
        // 연결 상태 업데이트
        function updateConnectionStatus(isOnline, responseTime = 0) {
            const statusElement = document.getElementById('connectionStatus');
            
            if (isOnline) {
                statusElement.textContent = `🟢 연결됨 (${responseTime.toFixed(0)}ms)`;
                statusElement.className = 'connection-status online';
            } else {
                statusElement.textContent = `🔴 연결 끊김 (${responseTime.toFixed(0)}ms)`;
                statusElement.className = 'connection-status offline';
            }
        }
        
        // 게임 완료 시 축하 효과
        function showCelebration() {
            const celebration = document.createElement('div');
            celebration.className = 'celebration';
            
            // 색색의 종이 조각 생성
            const colors = ['#ff6b6b', '#4ecdc4', '#45b7d1', '#96ceb4', '#feca57', '#ff9ff3', '#54a0ff'];
            
            for (let i = 0; i < 50; i++) {
                const confetti = document.createElement('div');
                confetti.className = 'confetti';
                confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                confetti.style.left = Math.random() * 100 + '%';
                confetti.style.animationDelay = Math.random() * 3 + 's';
                confetti.style.animationDuration = (Math.random() * 2 + 2) + 's';
                celebration.appendChild(confetti);
            }
            
            document.body.appendChild(celebration);
            
            // 5초 후 제거
            setTimeout(() => {
                if (celebration.parentNode) {
                    celebration.remove();
                }
            }, 5000);
        }
        
        // 페이지 종료 시 정리
        window.addEventListener('beforeunload', function() {
            if (updateInterval) {
                clearInterval(updateInterval);
            }
            console.log('모니터링 페이지 종료');
        });
        
        // 키보드 단축키
        document.addEventListener('keydown', function(e) {
            switch(e.key) {
                case 'r':
                case 'R':
                    if (e.ctrlKey) return; // Ctrl+R은 브라우저 새로고침
                    console.log('수동 새로고침');
                    updateGameStatus();
                    break;
                case 'f':
                case 'F':
                    // 전체화면 토글
                    if (document.fullscreenElement) {
                        document.exitFullscreen();
                    } else {
                        document.documentElement.requestFullscreen();
                    }
                    break;
            }
        });
        
        console.log('텔레스트레이션 모니터링 페이지 로드 완료');
        console.log('키보드 단축키: R(새로고침), F(전체화면)');
    </script>
</body>
</html>