<?php
// 간단한 그림판 페이지
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>텔레스트레이션 - 그림판</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.32/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.32/sweetalert2.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Fredoka+One:wght@400&family=Noto+Sans+KR:wght@400;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            -khtml-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
        
        html, body {
            height: 100%;
            overflow: hidden;
        }
        
        body {
            font-family: 'Noto Sans KR', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: fixed;
            width: 100%;
            height: 100%;
            padding: 0;
        }
        
        .container {
            display: flex;
            flex-direction: column;
            width: 100%;
            height: 100vh;
            height: 100dvh; /* Dynamic viewport height for mobile */
        }
        
        .header {
            flex-shrink: 0;
            text-align: center;
            background: rgba(255, 255, 255, 0.9);
            padding: 8px 12px;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .player-name {
            font-family: 'Fredoka One', cursive;
            font-size: 1.3rem;
            background: linear-gradient(45deg, #ff6b6b, #4ecdc4, #45b7d1, #96ceb4, #feca57);
            background-size: 300% 300%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: gradientShift 3s ease-in-out infinite;
            letter-spacing: 0.5px;
            margin: 0;
        }
        
        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        
        .timer-container {
            margin-top: 5px;
            text-align: center;
        }
        
        .timer {
            font-family: 'Fredoka One', cursive;
            font-size: 1.8rem;
            background: linear-gradient(45deg, #ff6b6b, #feca57);
            background-size: 300% 300%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: gradientShift 1s ease-in-out infinite, pulse 1s ease-in-out infinite;
            margin: 0;
        }
        
        .timer.warning {
            background: none !important;
            color: #ff3838 !important;
            -webkit-text-fill-color: #ff3838 !important;
            background-clip: unset !important;
            -webkit-background-clip: unset !important;
            animation: shake 0.5s ease-in-out infinite !important;
            text-shadow: 0 0 10px rgba(255, 56, 56, 0.5) !important;
        }
        
        .timer-label {
            color: #666;
            font-size: 0.7rem;
            font-weight: 600;
            margin: 0;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-3px); }
            75% { transform: translateX(3px); }
        }
        
        .canvas-disabled {
            pointer-events: none;
            opacity: 0.6;
            filter: grayscale(50%);
        }
        
        .canvas-container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            background: rgba(255, 255, 255, 0.95);
            margin: 8px;
            border-radius: 12px;
            backdrop-filter: blur(10px);
            padding: 8px;
            overflow: hidden;
        }
        
        #canvas {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            background-color: white;
            cursor: crosshair;
            touch-action: none;
            display: block;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            max-width: 100%;
            max-height: 100%;
        }
        
        #canvas:active {
            border-color: #4ecdc4;
        }
        
        .controls {
            flex-shrink: 0;
            display: flex;
            gap: 8px;
            justify-content: center;
            align-items: center;
            background: rgba(255, 255, 255, 0.9);
            padding: 8px 12px;
            backdrop-filter: blur(10px);
            border-top: 1px solid rgba(255, 255, 255, 0.3);
            flex-wrap: wrap;
        }
        
        .control-btn {
            background: linear-gradient(45deg, #ff6b6b, #4ecdc4);
            color: white;
            font-size: 0.8rem;
            font-weight: 600;
            padding: 6px 12px;
            border: none;
            border-radius: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            font-family: 'Noto Sans KR', sans-serif;
            white-space: nowrap;
        }
        
        .control-btn:active {
            transform: scale(0.95);
        }
        
        .color-picker {
            display: flex;
            gap: 6px;
            align-items: center;
        }
        
        .color-btn {
            width: 28px;
            height: 28px;
            border: 2px solid #fff;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        }
        
        .color-btn:active {
            transform: scale(0.9);
        }
        
        .color-btn.active {
            border-color: #333;
            transform: scale(1.1);
        }
        
        .floating-elements {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }
        
        .floating-element {
            position: absolute;
            opacity: 0.05;
            animation: float 8s ease-in-out infinite;
            font-size: 1.5rem;
        }
        
        .floating-element:nth-child(1) {
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }
        
        .floating-element:nth-child(2) {
            top: 20%;
            right: 10%;
            animation-delay: 2s;
        }
        
        .floating-element:nth-child(3) {
            bottom: 30%;
            left: 15%;
            animation-delay: 4s;
        }
        
        .floating-element:nth-child(4) {
            bottom: 10%;
            right: 20%;
            animation-delay: 1s;
        }
        
        .floating-element:nth-child(5) {
            top: 50%;
            left: 5%;
            animation-delay: 3s;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }
        
        /* 가로 모드 (landscape) */
        @media screen and (orientation: landscape) {
            .header {
                padding: 6px 10px;
            }
            
            .player-name {
                font-size: 1.1rem;
            }
            
            .timer {
                font-size: 1.5rem;
            }
            
            .timer-label {
                font-size: 0.6rem;
            }
            
            .controls {
                padding: 6px 10px;
                gap: 6px;
            }
            
            .control-btn {
                font-size: 0.7rem;
                padding: 5px 10px;
            }
            
            .color-btn {
                width: 24px;
                height: 24px;
            }
        }
        
        /* 세로 모드 (portrait) */
        @media screen and (orientation: portrait) {
            .header {
                padding: 10px 12px;
            }
            
            .player-name {
                font-size: 1.4rem;
            }
            
            .timer {
                font-size: 2rem;
            }
            
            .timer-label {
                font-size: 0.8rem;
            }
            
            .controls {
                padding: 10px 12px;
                gap: 8px;
            }
            
            .control-btn {
                font-size: 0.85rem;
                padding: 7px 14px;
            }
            
            .color-btn {
                width: 30px;
                height: 30px;
            }
        }
        
        /* 매우 작은 화면 */
        @media screen and (max-height: 500px) {
            .header {
                padding: 4px 8px;
            }
            
            .player-name {
                font-size: 1rem;
            }
            
            .timer {
                font-size: 1.3rem;
            }
            
            .timer-label {
                font-size: 0.5rem;
            }
            
            .controls {
                padding: 4px 8px;
                gap: 4px;
            }
            
            .control-btn {
                font-size: 0.65rem;
                padding: 4px 8px;
            }
            
            .color-btn {
                width: 22px;
                height: 22px;
            }
        }
        
        /* 매우 큰 태블릿 */
        @media screen and (min-width: 768px) {
            .header {
                padding: 12px 15px;
            }
            
            .player-name {
                font-size: 1.6rem;
            }
            
            .timer {
                font-size: 2.2rem;
            }
            
            .timer-label {
                font-size: 0.9rem;
            }
            
            .controls {
                padding: 12px 15px;
                gap: 10px;
            }
            
            .control-btn {
                font-size: 0.9rem;
                padding: 8px 16px;
            }
            
            .color-btn {
                width: 32px;
                height: 32px;
            }
        }
    </style>
</head>
<body>
    <div class="floating-elements">
        <div class="floating-element">🎨</div>
        <div class="floating-element">✏️</div>
        <div class="floating-element">🖍️</div>
        <div class="floating-element">📝</div>
        <div class="floating-element">🖌️</div>
    </div>

    <div class="container">
        <div class="header">
            <h1 class="player-name" id="playerName">그림판</h1>
            <div class="timer-container" id="timerContainer" style="display: none;">
                <div class="timer" id="timer">10</div>
                <div class="timer-label">남은 시간</div>
            </div>
        </div>
        
        <div class="canvas-container">
            <canvas id="canvas"></canvas>
        </div>
        
        <div class="controls">
            <div class="color-picker">
                <div class="color-btn active" data-color="#000000" style="background-color: #000000;"></div>
                <div class="color-btn" data-color="#ff6b6b" style="background-color: #ff6b6b;"></div>
                <div class="color-btn" data-color="#4ecdc4" style="background-color: #4ecdc4;"></div>
                <div class="color-btn" data-color="#45b7d1" style="background-color: #45b7d1;"></div>
                <div class="color-btn" data-color="#96ceb4" style="background-color: #96ceb4;"></div>
                <div class="color-btn" data-color="#feca57" style="background-color: #feca57;"></div>
                <div class="color-btn" data-color="#ff9ff3" style="background-color: #ff9ff3;"></div>
                <div class="color-btn" data-color="#54a0ff" style="background-color: #54a0ff;"></div>
            </div>
            
            <button class="control-btn" onclick="clearCanvas()">지우기</button>
            <?php 
            $playerNumber = $_GET['player_number'] ?? 0;
            if ($playerNumber == 1): 
            ?>
            <button class="control-btn start-btn" id="startBtn">게임 시작</button>
            <?php endif; ?>
        </div>
    </div>

    <script>
        const canvas = document.getElementById('canvas');
        const ctx = canvas.getContext('2d');
        const playerNameElement = document.getElementById('playerName');
        const colorButtons = document.querySelectorAll('.color-btn');
        const timerContainer = document.getElementById('timerContainer');
        const timerElement = document.getElementById('timer');
        
        let isDrawing = false;
        let lastX = 0;
        let lastY = 0;
        let currentColor = '#000000';
        let currentSize = 3;
        let drawingEnabled = true;
        let countdownTimer = null;
        let currentRound = 1;
        let turnCheckInterval = null;
        let isGameActive = false;
        let isLastPlayer = false;
        let maxPlayerNumber = 0;
        let gameCompletedProcessed = false;
        let lastGameId = null;
        let gameCompleteExecuted = false;
        let isAnswerSubmitter = false;
        
        // 실시간 저장 관련 변수
        let realtimeSaveInterval = null;
        let currentGameId = null;
        let isRealtimeSaving = false;
        let lastCanvasData = null;
        let drawingStartTime = null;
        
        // URL에서 플레이어 번호 가져오기
        const urlParams = new URLSearchParams(window.location.search);
        const playerNumber = parseInt(urlParams.get('player_number')) || 0;
        
        // 사용자 이름 표시
        const playerName = sessionStorage.getItem('playerName');
        if (playerName) {
            playerNameElement.textContent = playerName + '님의 그림판';
        }
        
        // 그리기 설정
        ctx.strokeStyle = currentColor;
        ctx.lineWidth = currentSize;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';
        
        // 색상 선택 이벤트
        colorButtons.forEach(button => {
            button.addEventListener('click', function() {
                colorButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                
                currentColor = this.dataset.color;
                ctx.strokeStyle = currentColor;
            });
        });
        
        // 마우스 이벤트
        canvas.addEventListener('mousedown', startDrawing);
        canvas.addEventListener('mousemove', draw);
        canvas.addEventListener('mouseup', stopDrawing);
        canvas.addEventListener('mouseout', stopDrawing);
        
        // 터치 이벤트 (모바일)
        canvas.addEventListener('touchstart', handleTouch);
        canvas.addEventListener('touchmove', handleTouch);
        canvas.addEventListener('touchend', stopDrawing);
        
        function startDrawing(e) {
            if (!drawingEnabled) return;
            isDrawing = true;
            [lastX, lastY] = getMousePos(e);
            
            // 그리기 시작 시 실시간 저장 시작
            if (!drawingStartTime) {
                drawingStartTime = Date.now();
                startRealtimeSaving();
                console.log(`플레이어 ${playerNumber}: 그리기 시작 - 실시간 저장 활성화`);
            }
        }
        
        function draw(e) {
            if (!isDrawing || !drawingEnabled) return;
            
            const [currentX, currentY] = getMousePos(e);
            
            ctx.beginPath();
            ctx.moveTo(lastX, lastY);
            ctx.lineTo(currentX, currentY);
            ctx.stroke();
            
            [lastX, lastY] = [currentX, currentY];
        }
        
        function stopDrawing() {
            isDrawing = false;
        }
        
        // 실시간 저장 시작
        function startRealtimeSaving() {
            console.log(`플레이어 ${playerNumber}: startRealtimeSaving 호출됨 - currentGameId: ${currentGameId}, realtimeSaveInterval: ${realtimeSaveInterval}`);
            
            if (realtimeSaveInterval) {
                console.log(`플레이어 ${playerNumber}: 이미 실시간 저장이 실행 중입니다 - 종료`);
                return;
            }
            
            if (!currentGameId) {
                console.log(`플레이어 ${playerNumber}: currentGameId가 null입니다 - 실시간 저장 시작할 수 없음`);
                return;
            }
            
            console.log(`플레이어 ${playerNumber}: 실시간 저장 시작 (0.5초 간격) - gameId: ${currentGameId}`);
            
            realtimeSaveInterval = setInterval(() => {
                if (drawingEnabled && currentGameId) {
                    console.log(`플레이어 ${playerNumber}: saveRealtimeDrawing 호출 시도`);
                    saveRealtimeDrawing();
                } else {
                    console.log(`플레이어 ${playerNumber}: saveRealtimeDrawing 건너뛰기 - drawingEnabled: ${drawingEnabled}, currentGameId: ${currentGameId}`);
                }
            }, 100); // 0.5초마다 실행
        }
        
        // 실시간 저장 중지
        function stopRealtimeSaving() {
            if (realtimeSaveInterval) {
                clearInterval(realtimeSaveInterval);
                realtimeSaveInterval = null;
                drawingStartTime = null;
                console.log(`플레이어 ${playerNumber}: 실시간 저장 중지`);
            }
        }
        
        // 실시간 그림 저장 함수
        function saveRealtimeDrawing() {
            console.log(`플레이어 ${playerNumber}: saveRealtimeDrawing 시작 - isRealtimeSaving: ${isRealtimeSaving}`);
            
            // 이미 저장 중이면 건너뛰기 (중복 방지)
            if (isRealtimeSaving) {
                console.log(`플레이어 ${playerNumber}: 실시간 저장 이미 진행 중 - 건너뛰기`);
                return;
            }
            
            // 현재 캔버스 데이터 가져오기
            const currentCanvasData = canvas.toDataURL('image/png');
            console.log(`플레이어 ${playerNumber}: 캔버스 데이터 길이: ${currentCanvasData.length}`);
            
            // 빈 캔버스 체크 (빈 캔버스는 저장하지 않음)
            if (isCanvasEmpty()) {
                console.log(`플레이어 ${playerNumber}: 빈 캔버스 - 실시간 저장 건너뛰기`);
                return;
            }
            
            // 이전 데이터와 동일하면 저장하지 않음 (변화 없음)
            if (lastCanvasData === currentCanvasData) {
                console.log(`플레이어 ${playerNumber}: 캔버스 변화 없음 - 실시간 저장 건너뛰기`);
                return;
            }
            
            isRealtimeSaving = true;
            lastCanvasData = currentCanvasData;
            
            const data = {
                game_id: currentGameId,
                player_number: playerNumber,
                drawing_data: currentCanvasData
            };
            
            console.log(`플레이어 ${playerNumber}: 실시간 저장 시도 (game_id: ${currentGameId})`);
            
            fetch('save_realtime_drawing.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    console.log(`플레이어 ${playerNumber}: 실시간 저장 성공`);
                } else {
                    console.error(`플레이어 ${playerNumber}: 실시간 저장 실패:`, result.error);
                }
            })
            .catch(error => {
                console.error(`플레이어 ${playerNumber}: 실시간 저장 오류:`, error);
            })
            .finally(() => {
                isRealtimeSaving = false;
                console.log(`플레이어 ${playerNumber}: 실시간 저장 완료 - isRealtimeSaving: false`);
            });
        }
        
        // 캔버스가 비어있는지 확인
        function isCanvasEmpty() {
            const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
            const data = imageData.data;
            
            // 모든 픽셀이 투명(알파값 0)이거나 흰색인지 확인
            for (let i = 0; i < data.length; i += 4) {
                const r = data[i];     // Red
                const g = data[i + 1]; // Green
                const b = data[i + 2]; // Blue
                const a = data[i + 3]; // Alpha
                
                // 투명하지 않고 흰색이 아닌 픽셀이 있으면 false
                if (a > 0 && (r !== 255 || g !== 255 || b !== 255)) {
                    console.log(`플레이어 ${playerNumber}: 캔버스에 그림이 있음 (픽셀 발견: r=${r}, g=${g}, b=${b}, a=${a})`);
                    return false;
                }
            }
            console.log(`플레이어 ${playerNumber}: 캔버스가 비어있음`);
            return true;
        }
        
        function startCountdown(seconds = 10) {
            timerContainer.style.display = 'block';
            timerElement.textContent = seconds;
            
            // 게임 ID 설정 (실시간 저장을 위해) - 강화
            if (lastGameId && !currentGameId) {
                currentGameId = lastGameId;
                console.log(`플레이어 ${playerNumber}: startCountdown에서 게임 ID 설정됨 - ${currentGameId}`);
            }
            
            console.log(`플레이어 ${playerNumber}: 카운트다운 시작 - currentGameId: ${currentGameId}`);
            
            countdownTimer = setInterval(() => {
                seconds--;
                timerElement.textContent = seconds;
                
                if (seconds <= 3) {
                    timerElement.classList.add('warning');
                }
                
                if (seconds <= 0) {
                    clearInterval(countdownTimer);
                    disableDrawing();
                    timerContainer.style.display = 'none';
                    
                    saveDrawing();
                    
                    Swal.fire({
                        title: '시간 종료!',
                        text: '그림 그리기가 완료되었습니다.',
                        icon: 'info',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            }, 1000);
        }
        
        function disableDrawing() {
            drawingEnabled = false;
            canvas.classList.add('canvas-disabled');
            canvas.style.cursor = 'not-allowed';
            
            // 그리기 종료 시 실시간 저장 중지
            stopRealtimeSaving();
            console.log(`플레이어 ${playerNumber}: 그리기 비활성화 - 실시간 저장 중지`);
        }
        
        function enableDrawing() {
            drawingEnabled = true;
            canvas.classList.remove('canvas-disabled');
            canvas.style.cursor = 'crosshair';
            
            // 그리기 활성화 시 캔버스 상태 초기화
            lastCanvasData = null;
            drawingStartTime = null;
            console.log(`플레이어 ${playerNumber}: 그리기 활성화`);
        }
        
        // 동적 캔버스 크기 조정 함수 (개선된 버전)
        function resizeCanvas() {
            const container = document.querySelector('.canvas-container');
            const containerRect = container.getBoundingClientRect();
            
            // 컨테이너 크기에서 패딩 제외 (실제로 사용할 수 있는 공간)
            const availableWidth = containerRect.width - 16; // padding 8px * 2
            const availableHeight = containerRect.height - 16; // padding 8px * 2
            
            // 사용 가능한 공간의 95%를 활용 (여백 최소화)
            let canvasWidth = Math.floor(availableWidth * 0.95);
            let canvasHeight = Math.floor(availableHeight * 0.95);
            
            // 최소 크기 보장
            canvasWidth = Math.max(280, canvasWidth);
            canvasHeight = Math.max(180, canvasHeight);
            
            // 최대 크기 제한 (너무 커지지 않도록)
            canvasWidth = Math.min(1200, canvasWidth);
            canvasHeight = Math.min(800, canvasHeight);
            
            // 캔버스 크기 설정
            canvas.width = canvasWidth;
            canvas.height = canvasHeight;
            
            // CSS로도 크기 명시적 설정 (중요!)
            canvas.style.width = canvasWidth + 'px';
            canvas.style.height = canvasHeight + 'px';
            
            // 캔버스 크기 변경 후 설정 재적용
            ctx.strokeStyle = currentColor;
            ctx.lineWidth = currentSize;
            ctx.lineCap = 'round';
            ctx.lineJoin = 'round';
            
            console.log(`Canvas resized: ${canvasWidth}x${canvasHeight} (container: ${availableWidth}x${availableHeight})`);
        }
        
        // 화면 크기 변경 감지
        function setupResizeObserver() {
            // ResizeObserver가 지원되는 경우
            if (window.ResizeObserver) {
                const resizeObserver = new ResizeObserver(entries => {
                    resizeCanvas();
                });
                resizeObserver.observe(document.querySelector('.canvas-container'));
            } else {
                // 폴백: resize 이벤트 사용
                let resizeTimeout;
                window.addEventListener('resize', () => {
                    clearTimeout(resizeTimeout);
                    resizeTimeout = setTimeout(resizeCanvas, 100);
                });
            }
        }
        
        // 화면 회전 감지
        function setupOrientationChange() {
            window.addEventListener('orientationchange', () => {
                setTimeout(() => {
                    resizeCanvas();
                }, 300); // 회전 애니메이션 완료 대기
            });
        }
        
        // 그림 저장
        function saveDrawing() {
            console.log(`플레이어 ${playerNumber}: 그림 저장 시작`);
            const drawingData = canvas.toDataURL('image/png');
            
            fetch('save_drawing.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    drawing_data: drawingData,
                    player_number: playerNumber,
                    round_number: currentRound
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('그림 저장 성공:', data);
                    
                    isGameActive = false;
                    
                    setTimeout(() => {
                        showWaitingScreen();
                        console.log(`플레이어 ${playerNumber}: 폴링 재시작`);
                        startTurnPolling();
                    }, 3000);
                } else {
                    console.error('그림 저장 실패:', data.error);
                }
            })
            .catch(error => {
                console.error('그림 저장 오류:', error);
            });
        }
        
        function getMousePos(e) {
            const rect = canvas.getBoundingClientRect();
            const scaleX = canvas.width / rect.width;
            const scaleY = canvas.height / rect.height;
            
            return [
                (e.clientX - rect.left) * scaleX,
                (e.clientY - rect.top) * scaleY
            ];
        }
        
        function handleTouch(e) {
            e.preventDefault();
            const touch = e.touches[0];
            const mouseEvent = new MouseEvent(getTouchEventType(e.type), {
                clientX: touch.clientX,
                clientY: touch.clientY
            });
            canvas.dispatchEvent(mouseEvent);
        }
        
        function getTouchEventType(touchType) {
            switch(touchType) {
                case 'touchstart': return 'mousedown';
                case 'touchmove': return 'mousemove';
                case 'touchend': return 'mouseup';
                default: return touchType;
            }
        }
        
        function clearCanvas() {
            if (!drawingEnabled) {
                Swal.fire({
                    title: '불가능',
                    text: '그리기 시간이 종료되었습니다.',
                    icon: 'warning',
                    timer: 1500,
                    showConfirmButton: false
                });
                return;
            }
            
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            
            // 캔버스 지우기 시 실시간 저장 데이터 초기화
            lastCanvasData = null;
            console.log(`플레이어 ${playerNumber}: 캔버스 지우기 - 실시간 저장 데이터 초기화`);
        }
        
        // 페이지 로드 시 설정
        window.addEventListener('load', function() {
            setupResizeObserver();
            setupOrientationChange();
            resizeCanvas();
            
            // 실시간 저장 상태 초기화
            stopRealtimeSaving();
            currentGameId = null;
            lastCanvasData = null;
            drawingStartTime = null;
            isRealtimeSaving = false;
            
            console.log(`플레이어 ${playerNumber}: 페이지 로드 완료 - 실시간 저장 초기화`);
            checkMyTurn();
            
            if (playerNumber !== 1) {
                console.log(`플레이어 ${playerNumber}: 폴링 시작 (초기)`);
                startTurnPolling();
            } else {
                console.log(`플레이어 ${playerNumber}: 방장이므로 폴링 안함 (게임 시작 대기)`);
            }
        });
        
        window.addEventListener('beforeunload', function() {
            stopTurnPolling();
            stopRealtimeSaving();
            console.log(`플레이어 ${playerNumber}: 페이지 종료 - 모든 인터벌 정리`);
        });
        
        // 주기적으로 턴 상태 체크
        function startTurnPolling() {
            if (turnCheckInterval) {
                clearInterval(turnCheckInterval);
            }
            
            console.log(`플레이어 ${playerNumber}: 폴링 시작`);
            turnCheckInterval = setInterval(() => {
                if (!isGameActive) {
                    console.log(`플레이어 ${playerNumber}: 턴 체크 중...`);
                    checkMyTurn();
                }
            }, 2000);
        }
        
        function stopTurnPolling() {
            if (turnCheckInterval) {
                clearInterval(turnCheckInterval);
                turnCheckInterval = null;
            }
        }
        
        // 내 턴인지 확인 (수정된 로직 + 게임 ID 설정 강화)
        function checkMyTurn() {
            console.log(`플레이어 ${playerNumber}: 턴 확인 중...`);
            
            fetch('check_turn.php?player_number=' + playerNumber)
            .then(response => response.json())
            .then(data => {
                console.log(`플레이어 ${playerNumber}: 턴 확인 결과:`, data);
                
                if (data.success) {
                    if (!data.game_started) {
                        console.log(`플레이어 ${playerNumber}: 게임 시작 대기 중`);
                        // 게임 시작 전에는 실시간 저장 중지
                        stopRealtimeSaving();
                        currentGameId = null;
                        return;
                    }
                    
                    // 게임 ID 설정 (실시간 저장을 위해) - 강화된 버전
                    if (data.game_id) {
                        currentGameId = data.game_id;
                        console.log(`플레이어 ${playerNumber}: 현재 게임 ID 설정 - ${currentGameId}`);
                        
                        // 추가: 게임 ID 설정이 잘 되었는지 확인
                        if (!currentGameId) {
                            console.error(`플레이어 ${playerNumber}: 게임 ID 설정 실패!`);
                        }
                    } else {
                        console.warn(`플레이어 ${playerNumber}: check_turn.php에서 game_id가 없음`);
                    }
                    
                    // 게임 완료 체크 (수정된 로직)
                    if (data.game_completed) {
                        console.log('게임 완료 감지!');
                        
                        // 게임 완료 시 실시간 저장 중지
                        stopRealtimeSaving();
                        currentGameId = null;
                        
                        const currentGameId_str = String(data.game_id);
                        const savedGameId = String(lastGameId || '');
                        
                        // 같은 게임이지만 아직 처리하지 않은 경우 허용
                        if (lastGameId && savedGameId === currentGameId_str && gameCompletedProcessed) {
                            console.log(`게임 완료 이미 처리됨 - 무시`);
                            return;
                        }
                        
                        gameCompletedProcessed = true;
                        lastGameId = currentGameId_str;
                        stopTurnPolling();
                        isGameActive = false;
                        
                        // 정답 제출자가 아닌 경우에만 이펙트 표시
                        if (!isAnswerSubmitter) {
                            if (data.is_correct) {
                                showSuccessEffect(data.correct_answer, data.final_answer);
                            } else {
                                showFailureEffect(data.correct_answer, data.final_answer);
                            }
                        } else {
                            setTimeout(() => {
                                gameComplete();
                            }, 4000);
                        }
                        return;
                    }
                    
                    // 새 게임 시작 감지
                    if (gameCompletedProcessed && data.game_id && String(data.game_id) !== String(lastGameId)) {
                        console.log(`플레이어 ${playerNumber}: 새 게임 감지!`);
                        gameCompletedProcessed = false;
                        gameCompleteExecuted = false;
                        isAnswerSubmitter = false;
                        lastGameId = data.game_id;
                        currentGameId = data.game_id;
                        isGameActive = false;
                        
                        // 새 게임 시작 시 실시간 저장 상태 초기화
                        stopRealtimeSaving();
                        lastCanvasData = null;
                        drawingStartTime = null;
                    }
                    
                    if (gameCompletedProcessed) {
                        console.log(`플레이어 ${playerNumber}: 새 게임 진행 중 - 플래그 초기화`);
                        gameCompletedProcessed = false;
                        gameCompleteExecuted = false;
                    }
                    
                    isLastPlayer = data.is_last_player;
                    maxPlayerNumber = data.max_player_number;
                    
                    if (data.is_my_turn) {
                        console.log(`플레이어 ${playerNumber}: 내 턴 시작!`);
                        isGameActive = true;
                        stopTurnPolling();
                        
                        if (Swal.isVisible()) {
                            Swal.close();
                        }
                        
                        if (isLastPlayer) {
                            showAnswerInput();
                        } else if (playerNumber > 1) {
                            showPreviousDrawingAndStart();
                        }
                    } else {
                        if (!isGameActive && !Swal.isVisible()) {
                            showWaitingScreen();
                        }
                    }
                }
            })
            .catch(error => {
                console.error('턴 확인 실패:', error);
            });
        }
        
        // 이전 그림 보여주고 게임 시작
        function showPreviousDrawingAndStart() {
            Swal.close();
            
            fetch('get_previous_drawing.php?player_number=' + playerNumber + '&round_number=' + currentRound)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: '이전 그림',
                        html: `
                            <div style="text-align: center;">
                                <p style="margin-bottom: 15px; font-size: 14px; color: #333;">이 그림을 보고 무엇인지 추측하여 다시 그려주세요!</p>
                                <div style="border: 2px solid #e0e0e0; border-radius: 8px; overflow: hidden; display: inline-block; background: white;">
                                    <img src="${data.drawing_data}" style="max-width: 90vw; max-height: 40vh; display: block;" />
                                </div>
                            </div>
                        `,
                        icon: null,
                        timer: 5000,
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        width: '95%',
                        background: '#fff',
                        backdrop: 'rgba(0,0,0,0.8)'
                    }).then(() => {
                        ctx.clearRect(0, 0, canvas.width, canvas.height);
                        Swal.fire({
                            title: '그리기 시작!',
                            text: '추측한 내용을 그려주세요!',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            startCountdown(10);
                            enableDrawing();
                        });
                    });
                }
            });
        }
        
        // 제시어 입력 화면
        function showAnswerInput() {
            Swal.close();
            
            fetch('get_previous_drawing.php?player_number=' + playerNumber + '&round_number=' + currentRound)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: '제시어를 맞춰주세요!',
                        html: `
                            <div style="text-align: center;">
                                <p style="margin-bottom: 15px; font-size: 14px; color: #333;">이 그림이 나타내는 제시어를 입력해주세요!</p>
                                <div style="border: 2px solid #e0e0e0; border-radius: 8px; overflow: hidden; display: inline-block; background: white; margin-bottom: 15px;">
                                    <img src="${data.drawing_data}" style="max-width: 90vw; max-height: 35vh; display: block;" />
                                </div>
                                <input type="text" id="answerInput" placeholder="제시어를 입력하세요..." style="width: 80%; max-width: 250px; padding: 10px; font-size: 14px; border: 2px solid #e0e0e0; border-radius: 6px; outline: none;" />
                            </div>
                        `,
                        icon: null,
                        showCancelButton: false,
                        confirmButtonText: '제출',
                        confirmButtonColor: '#4ecdc4',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        width: '95%',
                        background: '#fff',
                        backdrop: 'rgba(0,0,0,0.8)',
                        preConfirm: () => {
                            const answer = document.getElementById('answerInput').value.trim();
                            if (!answer) {
                                Swal.showValidationMessage('제시어를 입력해주세요!');
                                return false;
                            }
                            return answer;
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            checkAnswer(result.value);
                        }
                    });
                    
                    setTimeout(() => {
                        const input = document.getElementById('answerInput');
                        if (input) {
                            input.focus();
                            input.addEventListener('keypress', function(e) {
                                if (e.key === 'Enter') {
                                    Swal.clickConfirm();
                                }
                            });
                        }
                    }, 100);
                }
            });
        }
        
        // 제시어 정답 체크
        function checkAnswer(userAnswer) {
            isAnswerSubmitter = true;
            
            fetch('check_answer.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    answer: userAnswer,
                    player_number: playerNumber
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    gameCompletedProcessed = true;
                    lastGameId = data.current_game_id;
                    
                    if (data.is_correct) {
                        showSuccessEffect(data.correct_answer, data.user_answer);
                    } else {
                        showFailureEffect(data.correct_answer, data.user_answer);
                    }
                } else {
                    Swal.fire({
                        title: '오류',
                        text: data.error,
                        icon: 'error'
                    });
                }
            });
        }
        
        // 성공/실패 이펙트 (기존과 동일하지만 모바일 최적화)
        function showSuccessEffect(correctAnswer, userAnswer) {
            Swal.close();
            
            const overlay = document.createElement('div');
            overlay.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                z-index: 99999;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                color: white;
                font-family: 'Noto Sans KR', sans-serif;
                animation: fadeIn 0.5s ease-out;
                backdrop-filter: blur(20px);
            `;
            
            overlay.innerHTML = `
                <div style="text-align: center; z-index: 1; background: rgba(255,255,255,0.1); padding: 30px 20px; border-radius: 15px; backdrop-filter: blur(10px); box-shadow: 0 20px 60px rgba(0,0,0,0.3); max-width: 90%;">
                    <div style="font-size: 3rem; margin-bottom: 15px;">🎉</div>
                    <div style="font-size: 1.8rem; margin-bottom: 10px; font-weight: 700; color: #fff;">SUCCESS!</div>
                    <div style="font-size: 1rem; margin-bottom: 20px; opacity: 0.9;">완벽한 정답입니다!</div>
                    <div style="font-size: 0.9rem; margin-bottom: 8px; padding: 8px 16px; background: rgba(255,255,255,0.2); border-radius: 20px; border: 1px solid rgba(255,255,255,0.3);">
                        <strong>정답:</strong> ${correctAnswer}
                    </div>
                    <div style="font-size: 0.9rem; padding: 8px 16px; background: rgba(255,255,255,0.2); border-radius: 20px; border: 1px solid rgba(255,255,255,0.3);">
                        <strong>답안:</strong> ${userAnswer}
                    </div>
                </div>
            `;
            
            document.body.appendChild(overlay);
            
            setTimeout(() => {
                if (overlay && overlay.parentNode) {
                    overlay.remove();
                    gameComplete();
                }
            }, 4000);
        }
        
        function showFailureEffect(correctAnswer, userAnswer) {
            Swal.close();
            
            const overlay = document.createElement('div');
            overlay.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
                z-index: 99999;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                color: white;
                font-family: 'Noto Sans KR', sans-serif;
                animation: fadeIn 0.5s ease-out;
                backdrop-filter: blur(20px);
            `;
            
            overlay.innerHTML = `
                <div style="text-align: center; z-index: 1; background: rgba(255,255,255,0.1); padding: 30px 20px; border-radius: 15px; backdrop-filter: blur(10px); box-shadow: 0 20px 60px rgba(0,0,0,0.3); max-width: 90%;">
                    <div style="font-size: 3rem; margin-bottom: 15px;">💝</div>
                    <div style="font-size: 1.8rem; margin-bottom: 10px; font-weight: 700; color: #fff;">GOOD TRY!</div>
                    <div style="font-size: 1rem; margin-bottom: 20px; opacity: 0.9;">정말 잘했어요!</div>
                    <div style="font-size: 0.9rem; margin-bottom: 8px; padding: 8px 16px; background: rgba(255,255,255,0.2); border-radius: 20px; border: 1px solid rgba(255,255,255,0.3);">
                        <strong>정답:</strong> ${correctAnswer}
                    </div>
                    <div style="font-size: 0.9rem; padding: 8px 16px; background: rgba(255,255,255,0.2); border-radius: 20px; border: 1px solid rgba(255,255,255,0.3);">
                        <strong>답안:</strong> ${userAnswer}
                    </div>
                </div>
            `;
            
            document.body.appendChild(overlay);
            
            setTimeout(() => {
                if (overlay && overlay.parentNode) {
                    overlay.remove();
                    gameComplete();
                }
            }, 4000);
        }
        
        function gameComplete() {
            if (gameCompleteExecuted) return;
            
            gameCompleteExecuted = true;
            isGameActive = false;
            
            // 게임 완료 시 실시간 저장 완전 중지
            stopRealtimeSaving();
            currentGameId = null;
            
            Swal.close();
            enableDrawing();
            
            setTimeout(() => {
                Swal.fire({
                    title: '🎨 게임 완료!',
                    text: '텔레스트레이션 게임이 완료되었습니다!\n모든 플레이어가 수고하셨습니다!',
                    icon: 'success',
                    confirmButtonText: '확인',
                    confirmButtonColor: '#4ecdc4',
                    backdrop: 'rgba(0,0,0,0.8)',
                    allowOutsideClick: false,
                    width: '90%'
                }).then(() => {
                    // 게임 완료 후 캔버스 지우기
                    console.log(`플레이어 ${playerNumber}: 게임 완료 - 캔버스 지우기 및 실시간 저장 상태 초기화`);
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    
                    // 실시간 저장 상태 완전 초기화
                    lastCanvasData = null;
                    drawingStartTime = null;
                    isRealtimeSaving = false;
                    
                    // 상태 초기화
                    isGameActive = false;
                    isLastPlayer = false;
                    maxPlayerNumber = 0;
                    currentRound = 1;
                    gameCompleteExecuted = false;
                    
                    setTimeout(() => {
                        restartPollingForNextGame();
                    }, 3000);
                });
            }, 1000);
        }
        
        function restartPollingForNextGame() {
            isAnswerSubmitter = false;
            
            // 다음 게임을 위한 실시간 저장 상태 초기화
            stopRealtimeSaving();
            currentGameId = null;
            lastCanvasData = null;
            drawingStartTime = null;
            isRealtimeSaving = false;
            
            console.log(`플레이어 ${playerNumber}: 다음 게임을 위한 상태 초기화 완료`);
            
            if (playerNumber !== 1) {
                startTurnPolling();
            }
        }
        
        function showWaitingScreen() {
            disableDrawing();
            
            if (Swal.isVisible()) return;
            
            Swal.fire({
                title: '대기 중...',
                text: '다른 플레이어의 턴입니다.',
                icon: 'info',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                width: '90%'
            });
        }
        
        // 게임 시작 버튼 이벤트
        const startBtn = document.getElementById('startBtn');
        if (startBtn) {
            startBtn.addEventListener('click', function() {
                Swal.fire({
                    title: '게임 시작',
                    text: '게임을 시작하시겠습니까?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#4ecdc4',
                    cancelButtonColor: '#ff6b6b',
                    confirmButtonText: '시작',
                    cancelButtonText: '취소',
                    width: '90%'
                }).then((result) => {
                    if (result.isConfirmed) {
                        startGame();
                    }
                });
            });
        }
        
        function startGame() {
            gameCompletedProcessed = false;
            isGameActive = true;
            stopTurnPolling();
            
            // 새 게임 시작 시 실시간 저장 상태 초기화
            stopRealtimeSaving();
            lastCanvasData = null;
            drawingStartTime = null;
            currentGameId = null;
            
            console.log(`플레이어 ${playerNumber}: 새 게임 시작 요청 - currentGameId 초기화됨`);
            
            fetch('start_game.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    gameCompletedProcessed = false;
                    lastGameId = null;
                    
                    // 게임 ID 설정 (중요!)
                    if (data.game_id) {
                        currentGameId = data.game_id;
                        console.log(`플레이어 ${playerNumber}: 게임 ID 설정됨 - ${currentGameId}`);
                    } else {
                        console.error(`플레이어 ${playerNumber}: start_game.php에서 game_id가 없음!`);
                    }
                    
                    console.log(`플레이어 ${playerNumber}: 게임 시작 성공 응답:`, data);
                    console.log(`플레이어 ${playerNumber}: 새 게임 시작 - 실시간 저장 준비 완료 (gameId: ${currentGameId})`);
                    
                    if (playerNumber === 1) {
                        Swal.fire({
                            title: '제시어',
                            text: data.topic,
                            icon: 'info',
                            timer: 3000,
                            showConfirmButton: false,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            width: '90%'
                        }).then(() => {
                            Swal.fire({
                                title: '그림 그리기 시작!',
                                text: '제시어를 그려주세요!',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false,
                                width: '90%'
                            }).then(() => {
                                startCountdown(10);
                                enableDrawing();
                            });
                        });
                    }
                }
            });
        }
        
        // CSS 애니메이션 추가
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
            @keyframes pulse {
                0%, 100% { transform: scale(1); }
                50% { transform: scale(1.05); }
            }
        `;
        document.head.appendChild(style);
        
        // 실시간 저장 기능 테스트용 함수들 (개발자 콘솔에서 사용 가능)
        window.testRealtimeSaving = function() {
            console.log('=== 실시간 저장 테스트 ===');
            console.log('currentGameId:', currentGameId);
            console.log('drawingEnabled:', drawingEnabled);
            console.log('isRealtimeSaving:', isRealtimeSaving);
            console.log('realtimeSaveInterval:', realtimeSaveInterval);
            console.log('lastCanvasData length:', lastCanvasData ? lastCanvasData.length : 'null');
            console.log('drawingStartTime:', drawingStartTime);
            console.log('isCanvasEmpty():', isCanvasEmpty());
        };
        
        window.forceRealtimeSave = function() {
            console.log('=== 강제 실시간 저장 실행 ===');
            if (currentGameId) {
                saveRealtimeDrawing();
            } else {
                console.log('게임 ID가 없어서 저장할 수 없습니다.');
            }
        };
        
        console.log(`플레이어 ${playerNumber}: 실시간 저장 기능이 활성화되었습니다.`);
        console.log('테스트 함수: window.testRealtimeSaving() 또는 window.forceRealtimeSave()');
    </script>
</body>
</html>