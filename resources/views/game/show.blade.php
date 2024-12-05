<!DOCTYPE html>
<html>

<head>
    <title>Treasure Hunt Result</title>
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
        }

        .treasure {
            background-color: green;
        }
    </style>
</head>

<body>
    <h1>Game Results for {{ $gameState->user_name }}</h1>

    <div id="stats">
        <p>Treasures Found: {{ $gameState->treasures_found }}</p>
        <p>Misses: {{ $gameState->misses }}</p>
    </div>

    <div class="grid">
        @foreach($gameState->grid_state as $row)
        <div class="row">
            @foreach($row as $cell)
            <div class="cell {{ $cell === 'treasure' ? 'treasure' : '' }}">
                @if($cell === 'treasure')
                💎
                @endif
            </div>
            @endforeach
        </div>
        @endforeach
    </div>

    <p><a href="{{ route('game.index') }}">Play Again</a></p>
</body>

</html>