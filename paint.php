<?php
// 간단한 그림판 페이지
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>텔레스트레이션 - 그림판</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.32/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.32/sweetalert2.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Fredoka+One:wght@400&family=Noto+Sans+KR:wght@400;700&display=swap');
        
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
            overflow-x: hidden;
        }
        
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            background: rgba(255, 255, 255, 0.9);
            padding: 30px 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            position: relative;
            overflow: hidden;
        }
        
        .player-name {
            font-family: 'Fredoka One', cursive;
            font-size: 2.5rem;
            background: linear-gradient(45deg, #ff6b6b, #4ecdc4, #45b7d1, #96ceb4, #feca57);
            background-size: 300% 300%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: gradientShift 3s ease-in-out infinite;
            margin-bottom: 5px;
            letter-spacing: 1px;
        }
        
        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        
        .subtitle {
            color: #666;
            font-size: 1.1rem;
            font-weight: 400;
        }
        
        .timer-container {
            margin-top: 20px;
            text-align: center;
        }
        
        .timer {
            font-family: 'Fredoka One', cursive;
            font-size: 3rem;
            background: linear-gradient(45deg, #ff6b6b, #feca57);
            background-size: 300% 300%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: gradientShift 1s ease-in-out infinite, pulse 1s ease-in-out infinite;
            margin-bottom: 5px;
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
            font-size: 1rem;
            font-weight: 600;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        
        .canvas-disabled {
            pointer-events: none;
            opacity: 0.6;
            filter: grayscale(50%);
        }
        
        .canvas-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            margin-bottom: 20px;
        }
        
        #canvas {
            border: 3px solid #e0e0e0;
            border-radius: 15px;
            background-color: white;
            cursor: crosshair;
            touch-action: none;
            display: block;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        #canvas:hover {
            border-color: #4ecdc4;
            box-shadow: 0 8px 25px rgba(78, 205, 196, 0.3);
        }
        
        .controls {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
            background: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            backdrop-filter: blur(10px);
            margin-bottom: 20px;
        }
        
        .control-btn {
            background: linear-gradient(45deg, #ff6b6b, #4ecdc4);
            color: white;
            font-size: 1rem;
            font-weight: 600;
            padding: 12px 25px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            font-family: 'Noto Sans KR', sans-serif;
        }
        
        .control-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }
        
        .control-btn:active {
            transform: translateY(0);
        }
        
        .color-picker {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .color-btn {
            width: 40px;
            height: 40px;
            border: 3px solid #fff;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
        }
        
        .color-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }
        
        .color-btn.active {
            border-color: #333;
            transform: scale(1.2);
        }
        
        .size-control {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #333;
            font-weight: 600;
        }
        
        .size-slider {
            width: 100px;
            height: 8px;
            border-radius: 4px;
            background: linear-gradient(45deg, #ff6b6b, #4ecdc4);
            outline: none;
            cursor: pointer;
        }
        
        .info {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1rem;
            text-align: center;
            background: rgba(0, 0, 0, 0.2);
            padding: 15px 25px;
            border-radius: 10px;
            backdrop-filter: blur(5px);
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
            opacity: 0.1;
            animation: float 8s ease-in-out infinite;
            font-size: 2rem;
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
            50% { transform: translateY(-30px) rotate(180deg); }
        }
        
        @media (max-width: 900px) {
            .player-name {
                font-size: 2rem;
            }
            
            #canvas {
                width: 100%;
                max-width: 600px;
                height: 400px;
            }
            
            .controls {
                flex-direction: column;
                align-items: center;
            }
            
            .container {
                padding: 10px;
            }
        }
        
        @media (max-width: 600px) {
            .player-name {
                font-size: 1.8rem;
            }
            
            .header {
                padding: 20px 25px;
            }
            
            .canvas-container {
                padding: 15px;
            }
            
            #canvas {
                height: 300px;
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
            <p class="subtitle">마음껏 그림을 그려보세요!</p>
            <div class="timer-container" id="timerContainer" style="display: none;">
                <div class="timer" id="timer">10</div>
                <div class="timer-label">남은 시간</div>
            </div>
        </div>
        
        <div class="canvas-container">
            <canvas id="canvas" width="800" height="600"></canvas>
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
            
            <div class="size-control">
                <span>굵기:</span>
                <input type="range" class="size-slider" id="brushSize" min="1" max="20" value="2">
                <span id="sizeDisplay">2px</span>
            </div>
            
            <button class="control-btn" onclick="clearCanvas()">지우기</button>
            <?php 
            $playerNumber = $_GET['player_number'] ?? 0;
            if ($playerNumber == 1): 
            ?>
            <button class="control-btn start-btn" id="startBtn">게임 시작</button>
            <?php endif; ?>
        </div>
        
        <div class="info">
            마우스나 터치를 이용해서 그림을 그려보세요!
        </div>
    </div>

    <script>
        const canvas = document.getElementById('canvas');
        const ctx = canvas.getContext('2d');
        const playerNameElement = document.getElementById('playerName');
        const brushSizeSlider = document.getElementById('brushSize');
        const sizeDisplay = document.getElementById('sizeDisplay');
        const colorButtons = document.querySelectorAll('.color-btn');
        const timerContainer = document.getElementById('timerContainer');
        const timerElement = document.getElementById('timer');
        
        let isDrawing = false;
        let lastX = 0;
        let lastY = 0;
        let currentColor = '#000000';
        let currentSize = 2;
        let drawingEnabled = true;
        let countdownTimer = null;
        let currentRound = 1;
        let turnCheckInterval = null;
        let isGameActive = false;
        let isLastPlayer = false;
        let maxPlayerNumber = 0;
        
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
                // 모든 버튼의 active 클래스 제거
                colorButtons.forEach(btn => btn.classList.remove('active'));
                // 클릭된 버튼에 active 클래스 추가
                this.classList.add('active');
                
                currentColor = this.dataset.color;
                ctx.strokeStyle = currentColor;
            });
        });
        
        // 브러시 크기 조절
        brushSizeSlider.addEventListener('input', function() {
            currentSize = this.value;
            ctx.lineWidth = currentSize;
            sizeDisplay.textContent = currentSize + 'px';
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
        
        function startCountdown(seconds = 10) {
            timerContainer.style.display = 'block';
            timerElement.textContent = seconds;
            
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
                    
                    // 그림 저장
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
        }
        
        function enableDrawing() {
            drawingEnabled = true;
            canvas.classList.remove('canvas-disabled');
            canvas.style.cursor = 'crosshair';
        }
        
        // 그림 저장
        function saveDrawing() {
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
                    console.log('그림이 저장되었습니다. 다음 턴:', data.next_turn);
                    
                    // 게임 완료 후 대기 상태로 전환
                    isGameActive = false;
                    
                    // 저장 완료 후 대기 화면으로 전환
                    setTimeout(() => {
                        showWaitingScreen();
                        
                        // 폴링 재시작 (1번이 아닌 경우)
                        if (playerNumber !== 1) {
                            startTurnPolling();
                        }
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
            return [
                e.clientX - rect.left,
                e.clientY - rect.top
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
        }
        
        // 반응형 캔버스 크기 조정
        function resizeCanvas() {
            const container = document.querySelector('.canvas-container');
            const containerWidth = container.clientWidth - 50; // 패딩 고려
            
            if (window.innerWidth <= 900) {
                canvas.width = Math.min(600, containerWidth);
                canvas.height = window.innerWidth <= 600 ? 300 : 400;
            } else {
                canvas.width = 800;
                canvas.height = 600;
            }
            
            // 캔버스 크기 변경 후 설정 재적용
            ctx.strokeStyle = currentColor;
            ctx.lineWidth = currentSize;
            ctx.lineCap = 'round';
            ctx.lineJoin = 'round';
        }
        
        // 페이지 로드 및 리사이즈 시 캔버스 크기 조정
        window.addEventListener('load', function() {
            resizeCanvas();
            checkMyTurn();
            
            // 1번이 아니면 주기적으로 턴 체크 시작
            if (playerNumber !== 1) {
                startTurnPolling();
            }
        });
        window.addEventListener('resize', resizeCanvas);
        
        // 페이지 벗어날 때 폴링 정리
        window.addEventListener('beforeunload', function() {
            stopTurnPolling();
        });
        
        // 주기적으로 턴 상태 체크 (1번 제외)
        function startTurnPolling() {
            turnCheckInterval = setInterval(() => {
                if (!isGameActive) { // 게임 중이 아닐 때만 체크
                    checkMyTurn();
                }
            }, 2000); // 2초마다 체크
        }
        
        // 턴 체크 중단
        function stopTurnPolling() {
            if (turnCheckInterval) {
                clearInterval(turnCheckInterval);
                turnCheckInterval = null;
            }
        }
        
        // 내 턴인지 확인
        function checkMyTurn() {
            fetch('check_turn.php?player_number=' + playerNumber)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (!data.game_started) {
                        // 게임이 시작되지 않았음
                        if (playerNumber === 1) {
                            // 1번 플레이어는 게임 시작 버튼 대기
                            console.log('게임 시작 대기 중...');
                        } else {
                            // 2번 이상은 게임 시작 대기 (조용히)
                            console.log('게임 시작을 기다리는 중...');
                        }
                        return;
                    }
                    
                    // 게임이 시작된 상태
                    isLastPlayer = data.is_last_player;
                    maxPlayerNumber = data.max_player_number;
                    
                    console.log(`플레이어 ${playerNumber}번 - 마지막 순번: ${isLastPlayer} (총 ${maxPlayerNumber}명)`);
                    
                    if (data.is_my_turn) {
                        // 내 턴이면
                        isGameActive = true;
                        stopTurnPolling(); // 폴링 중단
                        
                        if (isLastPlayer) {
                            // 마지막 플레이어는 제시어 입력
                            showAnswerInput();
                        } else if (playerNumber > 1) {
                            // 2번 이상이면 이전 그림 보여주기
                            showPreviousDrawingAndStart();
                        }
                        // 1번은 방장이 시작 버튼을 눌러야 시작
                    } else {
                        // 내 턴이 아니면 대기 화면 (게임 시작된 후에만)
                        if (!isGameActive) { // 아직 게임이 활성화되지 않았을 때만
                            showWaitingScreen();
                        }
                    }
                } else {
                    console.error('턴 확인 오류:', data.error);
                }
            })
            .catch(error => {
                console.error('턴 확인 실패:', error);
            });
        }
        
        // 이전 그림 보여주고 게임 시작
        function showPreviousDrawingAndStart() {
            Swal.close(); // 기존 대기 화면 닫기
            
            fetch('get_previous_drawing.php?player_number=' + playerNumber + '&round_number=' + currentRound)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // SweetAlert 안에 이미지 직접 표시
                    Swal.fire({
                        title: '이전 그림',
                        html: `
                            <div style="text-align: center;">
                                <p style="margin-bottom: 15px; font-size: 16px; color: #333;">이 그림을 보고 무엇인지 추측하여 다시 그려주세요!</p>
                                <div style="border: 3px solid #e0e0e0; border-radius: 10px; overflow: hidden; display: inline-block; background: white;">
                                    <img src="${data.drawing_data}" style="max-width: 400px; max-height: 300px; display: block;" />
                                </div>
                            </div>
                        `,
                        icon: null,
                        timer: 5000,
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        width: '500px',
                        background: '#fff',
                        backdrop: 'rgba(0,0,0,0.8)'
                    }).then(() => {
                        // 캔버스 지우고 그리기 시작
                        ctx.clearRect(0, 0, canvas.width, canvas.height);
                        Swal.fire({
                            title: '그리기 시작!',
                            text: '추측한 내용을 그려주세요!',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            startCountdown(10);
                            enableDrawing(); // 그리기 활성화!
                        });
                    });
                } else {
                    console.error('이전 그림 가져오기 실패:', data.error);
                }
            })
            .catch(error => {
                console.error('이전 그림 가져오기 오류:', error);
            });
        }
        
        // 제시어 입력 화면 (마지막 플레이어용)
        function showAnswerInput() {
            Swal.close(); // 기존 대기 화면 닫기
            
            fetch('get_previous_drawing.php?player_number=' + playerNumber + '&round_number=' + currentRound)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // SweetAlert 안에 이미지와 입력 폼 표시
                    Swal.fire({
                        title: '제시어를 맞춰주세요!',
                        html: `
                            <div style="text-align: center;">
                                <p style="margin-bottom: 15px; font-size: 16px; color: #333;">이 그림이 나타내는 제시어를 입력해주세요!</p>
                                <div style="border: 3px solid #e0e0e0; border-radius: 10px; overflow: hidden; display: inline-block; background: white; margin-bottom: 20px;">
                                    <img src="${data.drawing_data}" style="max-width: 400px; max-height: 300px; display: block;" />
                                </div>
                                <input type="text" id="answerInput" placeholder="제시어를 입력하세요..." style="width: 300px; padding: 12px; font-size: 16px; border: 2px solid #e0e0e0; border-radius: 8px; outline: none;" />
                            </div>
                        `,
                        icon: null,
                        showCancelButton: false,
                        confirmButtonText: '제출',
                        confirmButtonColor: '#4ecdc4',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        width: '550px',
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
                    
                    // 입력 필드에 포커스
                    setTimeout(() => {
                        const input = document.getElementById('answerInput');
                        if (input) {
                            input.focus();
                            // Enter 키로 제출 가능하도록
                            input.addEventListener('keypress', function(e) {
                                if (e.key === 'Enter') {
                                    Swal.clickConfirm();
                                }
                            });
                        }
                    }, 100);
                } else {
                    console.error('이전 그림 가져오기 실패:', data.error);
                }
            })
            .catch(error => {
                console.error('이전 그림 가져오기 오류:', error);
            });
        }
        
        // 제시어 정답 체크
        function checkAnswer(userAnswer) {
            // 서버로 정답 체크 요청
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
            })
            .catch(error => {
                Swal.fire({
                    title: '오류',
                    text: '정답 체크 중 오류가 발생했습니다.',
                    icon: 'error'
                });
            });
        }
        
        // 정답 성공 이펙트
        function showSuccessEffect(correctAnswer, userAnswer) {
            Swal.close();
            
            // 성공 오버레이 생성
            const overlay = document.createElement('div');
            overlay.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: linear-gradient(45deg, #00c851, #007e33);
                z-index: 99999;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                color: white;
                font-family: 'Fredoka One', cursive;
                animation: celebrationSlideIn 0.5s ease-out;
            `;
            
            overlay.innerHTML = `
                <div id="fireworksContainer" style="position: absolute; width: 100%; height: 100%; pointer-events: none;"></div>
                <div style="font-size: 4rem; margin-bottom: 20px; animation: bounceIn 1s ease-out;">🎉 정답! 🎉</div>
                <div style="font-size: 1.5rem; margin-bottom: 10px; animation: fadeInUp 1s ease-out 0.3s both;">축하합니다!</div>
                <div style="font-size: 1.2rem; opacity: 0.9; animation: fadeInUp 1s ease-out 0.6s both;">정답: "${correctAnswer}"</div>
                <div style="font-size: 1.2rem; opacity: 0.9; animation: fadeInUp 1s ease-out 0.9s both;">답안: "${userAnswer}"</div>
            `;
            
            document.body.appendChild(overlay);
            console.log('성공 오버레이 생성됨:', overlay);
            
            // 불꽃놀이 이펙트
            createFireworks();
            
            // 3초 후 제거
            setTimeout(() => {
                if (overlay && overlay.parentNode) {
                    overlay.remove();
                    console.log('성공 오버레이 제거됨');
                }
                gameComplete();
            }, 3000);
        }
        
        // 오답 실패 이펙트
        function showFailureEffect(correctAnswer, userAnswer) {
            Swal.close();
            
            // 실패 오버레이 생성
            const overlay = document.createElement('div');
            overlay.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: linear-gradient(45deg, #ff4757, #c44569);
                z-index: 99999;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                color: white;
                font-family: 'Fredoka One', cursive;
                animation: failureSlideIn 0.5s ease-out;
            `;
            
            overlay.innerHTML = `
                <div style="font-size: 4rem; margin-bottom: 20px; animation: bounceIn 1s ease-out;">😅 아쉬워요!</div>
                <div style="font-size: 1.5rem; margin-bottom: 10px; animation: fadeInUp 1s ease-out 0.3s both;">하지만 잘 했어요!</div>
                <div style="font-size: 1.2rem; opacity: 0.9; animation: fadeInUp 1s ease-out 0.6s both;">정답: "${correctAnswer}"</div>
                <div style="font-size: 1.2rem; opacity: 0.9; animation: fadeInUp 1s ease-out 0.9s both;">답안: "${userAnswer}"</div>
            `;
            
            document.body.appendChild(overlay);
            console.log('실패 오버레이 생성됨:', overlay);
            
            // 3초 후 제거
            setTimeout(() => {
                if (overlay && overlay.parentNode) {
                    overlay.remove();
                    console.log('실패 오버레이 제거됨');
                }
                gameComplete();
            }, 3000);
        }
        
        // 불꽃놀이 생성
        function createFireworks() {
            const container = document.getElementById('fireworksContainer');
            if (!container) {
                console.log('불꽃놀이 컨테이너를 찾을 수 없음');
                return;
            }
            
            console.log('불꽃놀이 시작');
            
            // 여러 개의 불꽃 생성
            for (let i = 0; i < 15; i++) {
                setTimeout(() => {
                    const firework = document.createElement('div');
                    firework.style.cssText = `
                        position: absolute;
                        width: 20px;
                        height: 20px;
                        background: radial-gradient(circle, ${getRandomColor()}, transparent);
                        border-radius: 50%;
                        left: ${Math.random() * 100}%;
                        top: ${Math.random() * 100}%;
                        animation: fireworkExplode 2s ease-out infinite;
                    `;
                    
                    container.appendChild(firework);
                    
                    // 애니메이션 완료 후 제거
                    setTimeout(() => {
                        if (firework && firework.parentNode) {
                            firework.remove();
                        }
                    }, 2000);
                }, i * 200);
            }
        }
        
        // 랜덤 컬러 생성
        function getRandomColor() {
            const colors = ['#ff6b6b', '#4ecdc4', '#45b7d1', '#96ceb4', '#feca57', '#ff9ff3', '#54a0ff', '#5f27cd'];
            return colors[Math.floor(Math.random() * colors.length)];
        }
        
        // 게임 완료 처리
        function gameComplete() {
            isGameActive = false;
            
            Swal.fire({
                title: '게임 완료!',
                text: '텔레스트레이션 게임이 완료되었습니다!',
                icon: 'success',
                confirmButtonText: '확인',
                confirmButtonColor: '#4ecdc4'
            }).then(() => {
                // 게임 완료 후 처리 (예: 메인 화면으로 이동)
                console.log('게임 완료');
            });
        }
        
        // 디버깅용 테스트 함수 (개발자 콘솔에서 호출 가능)
        window.testSuccessEffect = function() {
            showSuccessEffect("사과", "사과");
        };
        
        window.testFailureEffect = function() {
            showFailureEffect("사과", "바나나");
        };
        function showWaitingScreen() {
            disableDrawing();
            
            // 이미 대기 화면이 표시 중이면 중복 호출 방지
            if (Swal.isVisible()) {
                return;
            }
            
            Swal.fire({
                title: '대기 중...',
                text: '다른 플레이어의 턴입니다. 잠시만 기다려주세요.',
                icon: 'info',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false
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
                    cancelButtonText: '취소'
                }).then((result) => {
                    if (result.isConfirmed) {
                        startGame();
                    }
                });
            });
        }
        
        function startGame() {
            // 게임 시작 시 폴링 중단 (1번은 이제 게임 진행)
            isGameActive = true;
            stopTurnPolling();
            
            fetch('start_game.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // 1번 플레이어에게만 단어 보여주기
                    if (playerNumber === 1) {
                        Swal.fire({
                            title: '제시어',
                            text: data.topic,
                            icon: 'info',
                            timer: 3000,
                            showConfirmButton: false,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            background: '#fff',
                            color: '#333',
                            customClass: {
                                title: 'swal-title',
                                content: 'swal-content'
                            }
                        }).then(() => {
                            Swal.fire({
                                title: '그림 그리기 시작!',
                                text: '제시어를 그려주세요!',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                // 10초 카운트다운 시작
                                startCountdown(10);
                                enableDrawing(); // 그리기 활성화!
                            });
                        });
                    } else {
                        Swal.fire({
                            title: '게임 시작!',
                            text: '게임이 시작되었습니다!',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                } else {
                    Swal.fire({
                        title: '오류',
                        text: data.error,
                        icon: 'error'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    title: '오류',
                    text: '게임 시작 중 오류가 발생했습니다.',
                    icon: 'error'
                });
            });
        }
    </script>
</body>
</html>