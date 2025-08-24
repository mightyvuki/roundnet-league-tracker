<?php
    require_once(__DIR__ . "/../includes/db_utils.php");

    class League {
        private $db; // za dbutils jer mi je tamo sve

        private $id;
        private $naziv;
        private $godina;
        private $opis;
        private $admin_id;

        public function __construct($id = null) {
            $this->db = new DBUtils();
            if ($id) {
                $data = $this->db->getLeagueById($id);
                if ($data) {
                    $this->id = $data['id'];
                    $this->naziv = $data['naziv'];
                    $this->godina = $data['godina'];
                    $this->opis = $data['opis'];
                    $this->admin_id = $data['admin_id'];
                }
            }
        }

        public function getId() { return $this->id; }
        public function getNaziv() { return $this->naziv; }
        public function getGodina() { return $this->godina; }
        public function getOpis() { return $this->opis; }
        public function getAdminId() { return $this->admin_id; }

        public function setNaziv($naziv) { $this->naziv = $naziv; }
        public function setGodina($godina) { $this->godina = $godina; }
        public function setOpis($opis) { $this->opis = $opis; }
        public function setAdminId($admin_id) { $this->admin_id = $admin_id; }

        // CRUD - ovo sam sve vec uradio u dbutils pa sam ovako ostavio
        public function create() {
            return $this->db->insertLeague($this->naziv, $this->godina, $this->opis, $this->admin_id);
        }

        public function update() {
            if (!$this->id) return false;
            return $this->db->updateLeague($this->id, $this->naziv, $this->godina, $this->opis, $this->admin_id);
        }

        public function delete() {
            if (!$this->id) return false;
            return $this->db->deleteLeague($this->id);
        }

        public function getRounds() {
        if (!$this->id) return [];

        $roundsData = $this->db->getRoundsByLeague($this->id);
        $rounds = [];
        foreach ($roundsData as $r) {
            $rounds[] = new Round($r['id']);
        }
        return $rounds;
    }

        public function getHtml() {
            return "
            <div class='league-box'>
                <h2>{$this->naziv} ({$this->godina})</h2>
                <p>{$this->opis}</p>
                <p>Admin ID: {$this->admin_id}</p>
            </div>";
        }
    }
?>