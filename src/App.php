<?php

namespace App;

use Exception;

class App
{
    private array $leaderboard;

    private int $last_id;

    public function __construct()
    {
        $this->leaderboard = include 'data.php';
        $this->last_id = 5;
    }

    public function startGame(string $home_team, string $away_team): ?int
    {
        try {
            $this->last_id++;
            $this->leaderboard[$this->last_id] = [
                'id' => $this->last_id,
                'home_team' => $home_team,
                'away_team' => $away_team,
                'home_score' => 0,
                'away_score' => 0,
            ];
            return $this->last_id;
        } catch (Exception $e) {
            return null;
        }
    }

    public function finishGame(int $id): bool
    {
        try {
            unset($this->leaderboard[$id]);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function updateScore(int $id, int $home_score = null, int $away_score = null): bool
    {
        try {
            if ($home_score) {
                $this->leaderboard[$id]['home_score'] = $home_score;
            }
            if ($away_score) {
                $this->leaderboard[$id]['away_score'] = $away_score;
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getScores(): ?array
    {
        return $this->_sortScores();
    }

    private function _sortScores(): ?array
    {
        try {
            $leaderboard = $this->leaderboard;
            usort($leaderboard, function (array $a, array $b) {
                $a_score = $a['home_score'] + $a['away_score'];
                $b_score = $b['home_score'] + $b['away_score'];
                if ($a_score === $b_score) {
                    return $b['id'] <=> $a['id'];
                }
                return $b_score <=> $a_score;
            });
            return $leaderboard;
        } catch (Exception $e) {
            return null;
        }
    }
}
