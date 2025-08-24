<?php
    require_once(__DIR__ . "/../includes/db_utils.php");
    require_once("GameMatch.php");

    class Round {
        private $db;

        private $id;
        private $league_id;
        private $broj_kola;
        private $datum;

        public function __construct($id = null) {
            $this->db = new DBUtils();
            if ($id) {
                $data = $this->db->getRoundById($id);
                if ($data) {
                    $this->id = $data[COL_ROUND_ID];
                    $this->league_id = $data[COL_ROUND_LEAGUE_ID];
                    $this->broj_kola = $data[COL_ROUND_BROJ_KOLA];
                    $this->datum = $data[COL_ROUND_DATUM];
                }
            }
        }

        function getId() { return $this->id; }
        function getLeagueId() { return $this->league_id; }
        function getBrojKola() { return $this->broj_kola; }
        function getDatum() { return $this->datum; }

        public function setLeagueId($league_id) { $this->league_id = $league_id; }
        public function setBrojKola($broj_kola) { $this->broj_kola = $broj_kola; }
        public function setDatum($datum) { $this->datum = $datum; }

        public function create() { return $this->db->insertRound($this->league_id, $this->broj_kola, $this->datum); }
        public function update() { if (!$this->id) return false; return $this->db->updateRound($this->id, $this->broj_kola, $this->datum); }
        public function delete() { if (!$this->id) return false; return $this->db->deleteRound($this->id); }

        public function getHtml() {
            return "
            <div class='round-box'>
                <h3>Round {$this->broj_kola}</h3>
                <p>League ID: {$this->league_id}</p>
                <p>Date: {$this->datum}</p>
            </div>";
        }

        public function getMatches() {
            $matchesData = $this->db->getMatchesByRound($this->id);
            $matches = [];
            foreach ($matchesData as $match) {
                $matches[] = new GameMatch($match['id']);
            }
            return $matches;
        }
    }
?>
