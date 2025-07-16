<?php
// 텔레스트레이션 게임 시작 페이지
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>텔레스트레이션 - 시작</title>
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
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }
        
        .container {
            background: rgba(255, 255, 255, 0.95);
            padding: 50px 40px;
            border-radius: 25px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            text-align: center;
            max-width: 500px;
            width: 90%;
            position: relative;
            backdrop-filter: blur(10px);
        }
        
        .game-title {
            font-family: 'Fredoka One', cursive;
            font-size: 3.5rem;
            background: linear-gradient(45deg, #ff6b6b, #4ecdc4, #45b7d1, #96ceb4, #feca57);
            background-size: 300% 300%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: gradientShift 3s ease-in-out infinite;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
            letter-spacing: 2px;
        }
        
        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        
        .subtitle {
            color: #666;
            font-size: 1.1rem;
            margin-bottom: 40px;
            font-weight: 400;
        }
        
        .input-group {
            margin-bottom: 30px;
        }
        
        .input-label {
            display: block;
            color: #333;
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 15px;
        }
        
        .player-input {
            width: 100%;
            padding: 15px 20px;
            font-size: 1.1rem;
            border: 3px solid #e0e0e0;
            border-radius: 15px;
            outline: none;
            transition: all 0.3s ease;
            font-family: 'Noto Sans KR', sans-serif;
            background: #fff;
        }
        
        .player-input:focus {
            border-color: #4ecdc4;
            box-shadow: 0 0 20px rgba(78, 205, 196, 0.3);
            transform: translateY(-2px);
        }
        
        .ready-btn {
            background: linear-gradient(45deg, #ff6b6b, #4ecdc4);
            color: white;
            font-size: 1.3rem;
            font-weight: 700;
            padding: 18px 50px;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            text-transform: uppercase;
            letter-spacing: 1px;
            font-family: 'Noto Sans KR', sans-serif;
        }
        
        .ready-btn:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.3);
        }
        
        .ready-btn:active {
            transform: translateY(-1px) scale(1.02);
        }
        
        .ready-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        
        .floating-elements {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            overflow: hidden;
        }
        
        .floating-element {
            position: absolute;
            opacity: 0.1;
            animation: float 6s ease-in-out infinite;
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
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }
        
        @media (max-width: 600px) {
            .game-title {
                font-size: 2.8rem;
            }
            
            .container {
                padding: 30px 25px;
                margin: 20px;
            }
            
            .ready-btn {
                font-size: 1.1rem;
                padding: 15px 40px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="floating-elements">
            <div class="floating-element">🎨</div>
            <div class="floating-element">✏️</div>
            <div class="floating-element">🖍️</div>
            <div class="floating-element">📝</div>
        </div>
        
        <h1 class="game-title">텔레스트레이션</h1>
        <p class="subtitle">그림과 단어로 이어가는 재미있는 게임!</p>
        
        <div class="input-group">
            <label for="playerName" class="input-label">참여자 이름</label>
            <input type="text" id="playerName" class="player-input" placeholder="이름을 입력해주세요..." maxlength="20">
        </div>
        
        <button id="readyBtn" class="ready-btn" disabled>Ready</button>
    </div>

    <script>
        const playerInput = document.getElementById('playerName');
        const readyBtn = document.getElementById('readyBtn');
        
        // 입력 필드 감지
        playerInput.addEventListener('input', function() {
            const name = this.value.trim();
            readyBtn.disabled = name.length === 0;
        });
        
        // Enter 키로 Ready 버튼 클릭
        playerInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !readyBtn.disabled) {
                goToPaintPage();
            }
        });
        
        // Ready 버튼 클릭 이벤트
        readyBtn.addEventListener('click', goToPaintPage);
        
        function goToPaintPage() {
            const playerName = playerInput.value.trim();
            if (playerName) {
                readyBtn.disabled = true;
                readyBtn.textContent = '접속 중...';
                
                fetch('join_game.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ name: playerName })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        sessionStorage.setItem('playerName', playerName);
                        window.location.href = 'paint.php?player_number=' + data.player_number;
                    } else {
                        alert('접속 실패: ' + data.error);
                        readyBtn.disabled = false;
                        readyBtn.textContent = 'Ready';
                    }
                })
                .catch(error => {
                    alert('오류가 발생했습니다: ' + error);
                    readyBtn.disabled = false;
                    readyBtn.textContent = 'Ready';
                });
            }
        }
        
        // 페이지 로드 시 입력 필드에 포커스
        window.addEventListener('load', function() {
            playerInput.focus();
        });
    </script>
</body>
</html>