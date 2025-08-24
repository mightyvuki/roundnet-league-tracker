<?php
    require_once(__DIR__ . "/../includes/db_utils.php");

    class GameMatch //iz nekog razloga ne moze da se zove Match
    {
        public $db;

        private $id;
        private $round_id;
        private $team1_player1_id;
        private $team1_player2_id;
        private $team2_player1_id;
        private $team2_player2_id;
        private $score_team1;
        private $score_team2;

        public function __construct($id = null) {
            $this->db = new DBUtils();
            if ($id) {
                $data = $this->db->getMatchById($id);
                if ($data) {
                    $this->id = $data[COL_MATCH_ID];
                    $this->round_id = $data[COL_MATCH_ROUND_ID];
                    $this->team1_player1_id = $data[COL_MATCH_TEAM1_P1];
                    $this->team1_player2_id = $data[COL_MATCH_TEAM1_P2];
                    $this->team2_player1_id = $data[COL_MATCH_TEAM2_P1];
                    $this->team2_player2_id = $data[COL_MATCH_TEAM2_P2];
                    $this->score_team1 = $data[COL_MATCH_SCORE1];
                    $this->score_team2 = $data[COL_MATCH_SCORE2];
                }
            }
        }

        public function getId() { return $this->id; }
        public function getRoundId() { return $this->round_id; }
        public function getTeam1() { return [$this->team1_player1_id, $this->team1_player2_id]; }
        public function getTeam2() { return [$this->team2_player1_id, $this->team2_player2_id]; }
        public function getScoreTeam1() { return $this->score_team1; }
        public function getScoreTeam2() { return $this->score_team2; }

        public function setRoundId($round_id) { $this->round_id = $round_id; }
        public function setTeam1($p1, $p2) { $this->team1_player1_id = $p1; $this->team1_player2_id = $p2; }
        public function setTeam2($p1, $p2) { $this->team2_player1_id = $p1; $this->team2_player2_id = $p2; }
        public function setScore($score1, $score2) { $this->score_team1 = $score1; $this->score_team2 = $score2; }

        public function create() { return $this->db->insertMatch($this->round_id, $this->team1_player1_id, $this->team1_player2_id, $this->team2_player1_id, $this->team2_player2_id, $this->score_team1, $this->score_team2); }
        public function updateScore() { if (!$this->id) return false; return $this->db->updateMatchScore($this->id, $this->score_team1, $this->score_team2); }
        public function delete() { if (!$this->id) return false; return $this->db->deleteMatch($this->id); }

        public function getHtml() {
            return "
            <div class='match-box'>
                <p>Team 1: {$this->team1_player1_id} & {$this->team1_player2_id} - Score: {$this->score_team1}</p>
                <p>Team 2: {$this->team2_player1_id} & {$this->team2_player2_id} - Score: {$this->score_team2}</p>
            </div>";
        }
    }
?>
