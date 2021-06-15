<?php

class ModelPositionPosition extends Model {
    public function addPosition($data){
        // echo '<pre>';
        // print_r($data);
        // echo '</pre>';exit;
        $this->db->query("INSERT INTO `" . DB_PREFIX . "position` SET position_name = '" . $this->db->escape($data['position_name']) . "', grade='" .(int)($data['grade']). "', date_added=NOW()");
        $position_id = $this->db->getLastId();
        return $position_id;
    }

    public function editPosition($position_id, $data) {
		$this->db->query("UPDATE `" . DB_PREFIX . "position` SET position_name = '" . $this->db->escape($data['position_name']) . "', grade='" .(int)($data['grade']). "', date_modified = NOW() WHERE position_id = '" . (int)$position_id . "'");
    
        // if ($data['position']) {
		// 	$this->db->query("UPDATE `" . DB_PREFIX . "position` SET position_name = '" . $this->db->escape($data['position_name']) . "', status = '" . (int)$data['status'] . "'date_added = NOW(), date_modified = NOW() WHERE position_id = '" . (int)$position_id . "'");
		// }
    }

    public function deletePosition($position_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "position` WHERE position_id = '" . (int)$position_id . "'");
    }
    
    public function getPosition($position_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "position` WHERE position_id = '" . (int)$position_id . "'");

		return $query->row;
	}

    public function getPositions(){
        $query = $this->db->query("SELECT * FROM position");

        // echo '<pre>';
        // print_r($query->rows);
        // echo '</pre>';exit;

        return $query->rows;
    }

    public function getTotalPositions() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "position`");

		return $query->row['total'];
	}
}

?>