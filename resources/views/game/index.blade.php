<!DOCTYPE html>
<html>

<head>
    <title>Treasure Hunt</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .grid {
            display: inline-block;
        }

        .row {
            display: flex;
        }

        .cell {
            width: 50px;
            height: 50px;
            border: 1px solid black;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .cell.hit {
            background-color: green;
        }

        .cell.miss {
            background-color: red;
        }
    </style>
</head>

<body>
    <div id="gameForm">
        <input type="text" id="userName" placeholder="User Name" required>
        <input type="number" id="gridSize" placeholder="Grid Size" min="3" max="10" required>
        <button onclick="startGame()">Start</button>
    </div>

    <div id="gameBoard" style="display: none;">
        <div id="stats">
            <p>Time Remaining: <span id="timer">3:00</span></p>
            <p>Treasures Found: <span id="treasuresFound">0</span></p>
            <p>Misses: <span id="misses">0</span></p>
        </div>
        <div id="grid" class="grid"></div>
    </div>

    <script>
        let timeRemaining = 180;
        let timerInterval;

        function startGame() {
            const userName = document.getElementById('userName').value;
            const gridSize = parseInt(document.getElementById('gridSize').value);

            if (!userName || !gridSize) {
                alert('Please fill in all fields');
                return;
            }

            fetch('/initialize-grid', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        userName,
                        gridSize
                    })
                })
                .then(response => response.json())
                .then(data => {
                    createGrid(data.grid);
                    document.getElementById('gameForm').style.display = 'none';
                    document.getElementById('gameBoard').style.display = 'block';
                    startTimer();
                });
        }

        function createGrid(grid) {
            const gridElement = document.getElementById('grid');
            gridElement.innerHTML = '';

            grid.forEach((row, x) => {
                const rowDiv = document.createElement('div');
                rowDiv.className = 'row';

                row.forEach((cell, y) => {
                    const cellDiv = document.createElement('div');
                    cellDiv.className = 'cell';
                    cellDiv.onclick = () => handleClick(x, y);
                    rowDiv.appendChild(cellDiv);
                });

                gridElement.appendChild(rowDiv);
            });
        }

        function handleClick(x, y) {
            fetch('/process-click', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        x,
                        y
                    })
                })
                .then(response => response.json())
                .then(data => {
                    const cell = document.getElementById('grid').children[x].children[y];

                    if (data.hit) {
                        cell.className = 'cell hit';
                        cell.innerHTML = '💎';
                    } else {
                        cell.className = 'cell miss';
                        cell.innerHTML = '👎';
                    }

                    document.getElementById('treasuresFound').textContent = data.treasuresFound;
                    document.getElementById('misses').textContent = data.misses;

                    if (data.completed) {
                        clearInterval(timerInterval);
                        window.location.href = data.redirectUrl;
                    }
                });
        }

        function startTimer() {
            timerInterval = setInterval(() => {
                timeRemaining--;
                const minutes = Math.floor(timeRemaining / 60);
                const seconds = timeRemaining % 60;
                document.getElementById('timer').textContent =
                    `${minutes}:${seconds.toString().padStart(2, '0')}`;

                if (timeRemaining <= 0) {
                    clearInterval(timerInterval);
                    alert('Time\'s up! Game Over!');
                    window.location.reload();
                }
            }, 1000);
        }
    </script>
</body>

</html>