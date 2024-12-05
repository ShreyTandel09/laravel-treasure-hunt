<?php

namespace App\Http\Controllers;

use App\Models\GameState;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GameController extends Controller
{
    public function index()
    {
        return view('game.index');
    }

    public function initializeGrid(Request $request)
    {
        $request->validate([
            'userName' => 'required|string|max:255',
            'gridSize' => 'required|integer|min:3|max:10'
        ]);

        $gridSize = $request->gridSize;
        $grid = array_fill(0, $gridSize, array_fill(0, $gridSize, null));

        // Place treasures randomly
        $treasuresPlaced = 0;
        while ($treasuresPlaced < $gridSize) {
            $x = rand(0, $gridSize - 1);
            $y = rand(0, $gridSize - 1);

            if ($grid[$x][$y] !== 'treasure') {
                $grid[$x][$y] = 'treasure';
                $treasuresPlaced++;
            }
        }

        // Store in session
        $gameState = [
            'userName' => $request->userName,
            'gridSize' => $gridSize,
            'grid' => $grid,
            'treasuresFound' => 0,
            'misses' => 0,
            'timeRemaining' => 180 // 3 minutes in seconds
        ];

        session(['gameState' => $gameState]);

        return response()->json([
            'grid' => array_fill(0, $gridSize, array_fill(0, $gridSize, null)),
            'timeRemaining' => 180
        ]);
    }

    public function processClick(Request $request)
    {
        $gameState = session('gameState');
        $x = $request->x;
        $y = $request->y;

        $result = [
            'hit' => false,
            'treasuresFound' => $gameState['treasuresFound'],
            'misses' => $gameState['misses']
        ];

        if ($gameState['grid'][$x][$y] === 'treasure') {
            $result['hit'] = true;
            $gameState['treasuresFound']++;
        } else {
            $gameState['misses']++;
        }

        session(['gameState' => $gameState]);

        if ($gameState['treasuresFound'] === $gameState['gridSize']) {
            $randomNumber = Str::random(10);

            GameState::create([
                'user_name' => $gameState['userName'],
                'grid_size' => $gameState['gridSize'],
                'grid_state' => $gameState['grid'],
                'random_number' => $randomNumber,
                'treasures_found' => $gameState['treasuresFound'],
                'misses' => $gameState['misses'],
                'completed' => true
            ]);

            $result['completed'] = true;
            $result['redirectUrl'] = route('game.show', $randomNumber);
        }

        return response()->json($result);
    }

    public function show($randomNumber)
    {
        $gameState = GameState::where('random_number', $randomNumber)->firstOrFail();
        return view('game.show', compact('gameState'));
    }
}
