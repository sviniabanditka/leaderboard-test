<?php

namespace Tests;

use App\App;
use PHPUnit\Framework\TestCase;

final class AppTest extends TestCase
{
    public function testStartGame()
    {
        $app = new App();

        // check init leaderboard
        $scores = $app->getScores();
        $this->assertIsArray($scores);
        $score_home_teams = array_column($scores, 'home_team');
        $score_away_teams = array_column($scores, 'away_team');
        $this->assertNotContains('Ukraine', $score_home_teams);
        $this->assertNotContains('Germany', $score_away_teams);

        // add new game
        $id = $app->startGame('Ukraine', 'Germany');
        $this->assertNotNull($id);
        $this->assertIsInt($id);

        // check added game
        $scores = $app->getScores();
        $this->assertIsArray($scores);
        $score_home_teams = array_column($scores, 'home_team');
        $score_away_teams = array_column($scores, 'away_team');
        $this->assertContains('Ukraine', $score_home_teams);
        $this->assertContains('Germany', $score_away_teams);
        foreach ($scores as $score) {
            if ($score['home_team'] === 'Ukraine' && $score['away_team'] === 'Germany') {
                $this->assertSame($score['home_score'], 0);
                $this->assertSame($score['away_score'], 0);
            }
        }
    }

    public function testGetScores()
    {
        $app = new App();

        // get init leaderboard
        $scores = $app->getScores();
        $this->assertIsArray($scores);

        // check sorting
        $first_game = array_shift($scores);
        $prev_score = $first_game['home_score'] + $first_game['away_score'];
        $prev_id = $first_game['id'];
        foreach ($scores as $score) {
            $curr_score = $score['home_score'] + $score['away_score'];
            $this->assertGreaterThanOrEqual($curr_score, $prev_score);
            if ($prev_score === $curr_score) {
                $this->assertGreaterThan($score['id'], $prev_id);
            }
            $prev_score = $curr_score;
            $prev_id = $score['id'];
        }
    }

    public function testUpdateScore()
    {
        $app = new App();

        // check init leaderboard
        $scores = $app->getScores();
        $this->assertIsArray($scores);
        $score_home_teams = array_column($scores, 'home_team');
        $score_away_teams = array_column($scores, 'away_team');
        $this->assertNotContains('Ukraine', $score_home_teams);
        $this->assertNotContains('Germany', $score_away_teams);

        // add new game
        $id = $app->startGame('Ukraine', 'Germany');
        $this->assertNotNull($id);
        $this->assertIsInt($id);

        // check new game
        $scores = $app->getScores();
        $this->assertIsArray($scores);
        $score_home_teams = array_column($scores, 'home_team');
        $score_away_teams = array_column($scores, 'away_team');
        $this->assertContains('Ukraine', $score_home_teams);
        $this->assertContains('Germany', $score_away_teams);
        foreach ($scores as $score) {
            if ($score['home_team'] === 'Ukraine' && $score['away_team'] === 'Germany') {
                $this->assertSame($score['home_score'], 0);
                $this->assertSame($score['away_score'], 0);
            }
        }

        // update game score
        $update = $app->updateScore($id, 5, 3);
        $this->assertSame($update, true);

        // check updated game
        $scores = $app->getScores();
        $this->assertIsArray($scores);
        $score_home_teams = array_column($scores, 'home_team');
        $score_away_teams = array_column($scores, 'away_team');
        $this->assertContains('Ukraine', $score_home_teams);
        $this->assertContains('Germany', $score_away_teams);
        foreach ($scores as $score) {
            if ($score['home_team'] === 'Ukraine' && $score['away_team'] === 'Germany') {
                $this->assertSame($score['home_score'], 5);
                $this->assertSame($score['away_score'], 3);
            }
        }
    }

    public function testFinishGame()
    {
        $app = new App();

        // check init leaderboard
        $scores = $app->getScores();
        $this->assertIsArray($scores);
        $score_home_teams = array_column($scores, 'home_team');
        $score_away_teams = array_column($scores, 'away_team');
        $this->assertNotContains('Ukraine', $score_home_teams);
        $this->assertNotContains('Germany', $score_away_teams);

        // add new game
        $id = $app->startGame('Ukraine', 'Germany');
        $this->assertNotNull($id);
        $this->assertIsInt($id);

        // check added game
        $scores = $app->getScores();
        $this->assertIsArray($scores);
        $score_home_teams = array_column($scores, 'home_team');
        $score_away_teams = array_column($scores, 'away_team');
        $this->assertContains('Ukraine', $score_home_teams);
        $this->assertContains('Germany', $score_away_teams);

        // finish added game
        $finish = $app->finishGame($id);
        $this->assertSame($finish, true);

        // check that added game removed from leaderboard
        $scores = $app->getScores();
        $this->assertIsArray($scores);
        $score_home_teams = array_column($scores, 'home_team');
        $score_away_teams = array_column($scores, 'away_team');
        $this->assertNotContains('Ukraine', $score_home_teams);
        $this->assertNotContains('Germany', $score_away_teams);
    }
}
