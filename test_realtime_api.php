<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>실시간 API 테스트</title>
    <style>
        body {
            font-family: 'Noto Sans KR', sans-serif;
            margin: 20px;
            background: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .section {
            margin-bottom: 30px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .section h3 {
            margin-top: 0;
            color: #333;
        }
        button {
            background: #4ecdc4;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin: 5px;
        }
        button:hover {
            background: #45b7d1;
        }
        .result {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 5px;
            padding: 10px;
            margin-top: 10px;
            white-space: pre-wrap;
            font-family: monospace;
            font-size: 12px;
            max-height: 300px;
            overflow-y: auto;
        }
        .success { border-left: 4px solid #28a745; }
        .error { border-left: 4px solid #dc3545; }
        input, select {
            padding: 8px;
            margin: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .test-canvas {
            border: 2px solid #ddd;
            border-radius: 5px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 실시간 API 테스트 페이지</h1>
        <p>1단계, 2단계 API가 정상 작동하는지 테스트합니다.</p>
        
        <!-- 1. 현재 상태 조회 테스트 -->
        <div class="section">
            <h3>📊 1. 현재 게임 상태 조회 테스트</h3>
            <p>get_realtime_status.php API를 테스트합니다.</p>
            <button onclick="testGetStatus()">상태 조회 실행</button>
            <button onclick="startAutoRefresh()">자동 갱신 시작 (1초)</button>
            <button onclick="stopAutoRefresh()">자동 갱신 중지</button>
            <div id="statusResult" class="result"></div>
        </div>
        
        <!-- 2. 실시간 저장 테스트 -->
        <div class="section">
            <h3>💾 2. 실시간 그림 저장 테스트</h3>
            <p>save_realtime_drawing.php API를 테스트합니다.</p>
            
            <div>
                <label>게임 ID: </label>
                <input type="number" id="testGameId" value="1" min="1">
                
                <label>플레이어 번호: </label>
                <input type="number" id="testPlayerNumber" value="1" min="1">
            </div>
            
            <div>
                <canvas id="testCanvas" class="test-canvas" width="200" height="150"></canvas>
                <br>
                <button onclick="drawTestImage()">테스트 그림 그리기</button>
                <button onclick="clearTestCanvas()">캔버스 지우기</button>
                <button onclick="saveTestDrawing()">실시간 저장 실행</button>
            </div>
            
            <div id="saveResult" class="result"></div>
        </div>
        
        <!-- 3. 통합 테스트 -->
        <div class="section">
            <h3>🔄 3. 통합 테스트 (저장 → 조회)</h3>
            <p>그림을 저장한 후 바로 상태를 조회하여 저장된 데이터를 확인합니다.</p>
            <button onclick="runIntegratedTest()">통합 테스트 실행</button>
            <div id="integratedResult" class="result"></div>
        </div>
        
        <!-- 4. 로그 확인 -->
        <div class="section">
            <h3>📝 4. 서버 로그 정보</h3>
            <p>API 호출 시 서버에서 생성되는 로그 파일을 확인하세요:</p>
            <ul>
                <li><strong>save_realtime_drawing.log</strong> - 실시간 저장 API 로그</li>
                <li><strong>get_realtime_status.log</strong> - 상태 조회 API 로그</li>
            </ul>
            <p>이 파일들은 PHP 파일과 같은 디렉토리에 생성됩니다.</p>
        </div>
    </div>

    <script>
        let autoRefreshInterval = null;
        const canvas = document.getElementById('testCanvas');
        const ctx = canvas.getContext('2d');
        
        // 현재 상태 조회 테스트
        function testGetStatus() {
            showResult('statusResult', '상태 조회 중...', 'info');
            
            fetch('get_realtime_status.php')
            .then(response => response.json())
            .then(data => {
                showResult('statusResult', JSON.stringify(data, null, 2), data.success ? 'success' : 'error');
            })
            .catch(error => {
                showResult('statusResult', 'ERROR: ' + error.message, 'error');
            });
        }
        
        // 자동 갱신 시작/중지
        function startAutoRefresh() {
            if (autoRefreshInterval) {
                clearInterval(autoRefreshInterval);
            }
            autoRefreshInterval = setInterval(testGetStatus, 1000);
            showResult('statusResult', '자동 갱신 시작됨 (1초마다)', 'info');
        }
        
        function stopAutoRefresh() {
            if (autoRefreshInterval) {
                clearInterval(autoRefreshInterval);
                autoRefreshInterval = null;
                showResult('statusResult', '자동 갱신 중지됨', 'info');
            }
        }
        
        // 테스트 그림 그리기
        function drawTestImage() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            
            // 간단한 테스트 그림 그리기
            ctx.strokeStyle = '#ff6b6b';
            ctx.lineWidth = 3;
            ctx.beginPath();
            ctx.arc(100, 75, 30, 0, 2 * Math.PI);
            ctx.stroke();
            
            ctx.strokeStyle = '#4ecdc4';
            ctx.beginPath();
            ctx.moveTo(70, 60);
            ctx.lineTo(90, 80);
            ctx.moveTo(110, 80);
            ctx.lineTo(130, 60);
            ctx.stroke();
            
            ctx.beginPath();
            ctx.arc(100, 90, 15, 0, Math.PI);
            ctx.stroke();
            
            showResult('saveResult', '테스트 그림 생성 완료', 'info');
        }
        
        // 캔버스 지우기
        function clearTestCanvas() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            showResult('saveResult', '캔버스 지워짐', 'info');
        }
        
        // 실시간 저장 테스트
        function saveTestDrawing() {
            const gameId = document.getElementById('testGameId').value;
            const playerNumber = document.getElementById('testPlayerNumber').value;
            const drawingData = canvas.toDataURL('image/png');
            
            if (!gameId || !playerNumber) {
                showResult('saveResult', 'ERROR: 게임 ID와 플레이어 번호를 입력하세요', 'error');
                return;
            }
            
            showResult('saveResult', '실시간 저장 중...', 'info');
            
            const data = {
                game_id: parseInt(gameId),
                player_number: parseInt(playerNumber),
                drawing_data: drawingData
            };
            
            fetch('save_realtime_drawing.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                showResult('saveResult', JSON.stringify(result, null, 2), result.success ? 'success' : 'error');
            })
            .catch(error => {
                showResult('saveResult', 'ERROR: ' + error.message, 'error');
            });
        }
        
        // 통합 테스트
        function runIntegratedTest() {
            showResult('integratedResult', '통합 테스트 시작...', 'info');
            
            // 1단계: 테스트 그림 그리기
            drawTestImage();
            
            setTimeout(() => {
                // 2단계: 저장
                const gameId = document.getElementById('testGameId').value || 1;
                const playerNumber = document.getElementById('testPlayerNumber').value || 1;
                const drawingData = canvas.toDataURL('image/png');
                
                const data = {
                    game_id: parseInt(gameId),
                    player_number: parseInt(playerNumber),
                    drawing_data: drawingData
                };
                
                fetch('save_realtime_drawing.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(saveResult => {
                    showResult('integratedResult', '저장 결과:\n' + JSON.stringify(saveResult, null, 2), saveResult.success ? 'success' : 'error');
                    
                    if (saveResult.success) {
                        // 3단계: 1초 후 상태 조회
                        setTimeout(() => {
                            fetch('get_realtime_status.php')
                            .then(response => response.json())
                            .then(statusResult => {
                                showResult('integratedResult', 
                                    '저장 결과:\n' + JSON.stringify(saveResult, null, 2) + 
                                    '\n\n상태 조회 결과:\n' + JSON.stringify(statusResult, null, 2), 
                                    statusResult.success ? 'success' : 'error'
                                );
                            });
                        }, 1000);
                    }
                })
                .catch(error => {
                    showResult('integratedResult', 'ERROR: ' + error.message, 'error');
                });
            }, 500);
        }
        
        // 결과 표시 함수
        function showResult(elementId, message, type) {
            const element = document.getElementById(elementId);
            element.textContent = message;
            element.className = 'result';
            if (type) {
                element.classList.add(type);
            }
        }
        
        // 페이지 로드 시 초기 상태 확인
        window.addEventListener('load', function() {
            testGetStatus();
        });
    </script>
</body>
</html>