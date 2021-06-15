<?php
class ModelStaffStaff extends Model {
	public function addStaff($data) {
        // echo '<pre>';
        // print_r($data);
        // echo '</pre>';exit;
        $this->db->query("INSERT INTO `" . DB_PREFIX . "staff` SET staff_name = '" . $this->db->escape($data['staff_name']) . "', email = '" . $this->db->escape($data['email']) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt .sha1($data['password'])))) . "', department = '" . $this->db->escape($data['department_id']) . "', position = '" . $this->db->escape($data['position_id']) . "', status = '" . $this->db->escape($data['status']) . "', level = '" . $this->db->escape($data['level']) . "', date_added=NOW()");
        $staff_id = $this->db->getLastId();
        return $staff_id;
	}
		
	public function editStaff($staff_id, $data) {
		$this->db->query("UPDATE `" . DB_PREFIX . "staff` SET staff_name = '" . $this->db->escape($data['staff_name']) . "', email = '" . $this->db->escape($data['email']) . "', password = '" . $this->db->escape($data['password']) . "', 
        department = '" . (int)$data['department_id'] . "', position = '" . (int)$data['position_id'] . "', status = '" . (int)$data['status'] . "', level = '" . $this->db->escape($data['level']) . "', date_modified = NOW() WHERE staff_id = '" . (int)$staff_id . "'");
        
        
		if ($data['password']) {
			$this->db->query("UPDATE `" . DB_PREFIX . "staff` SET salt = '" . $this->db->escape($salt = token(9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "' WHERE staff_id = '" . (int)$staff_id . "'");
		}
    }
    
    public function editPassword($staff_id, $password) {
		$this->db->query("UPDATE `" . DB_PREFIX . "staff` SET salt = '" . $this->db->escape($salt = token(9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($password)))) . "', code = '' WHERE staff_id = '" . (int)$staff_id . "'");
	}

	public function deleteStaff($staff_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "staff` WHERE staff_id = '" . (int)$staff_id ."'");
    }
    
    public function getStaff($staff_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "staff` WHERE staff_id = '" . (int)$staff_id . "'");

		return $query->row;
    }

	public function getStaffs($staff_id) {
        $query = $this->db->query("SELECT * FROM staff");

        // echo '<pre>';
        // print_r($query->rows);
        // echo '</pre>';exit;

        return $query->rows;
}

public function getDepartmentById($department_id){
    $query = $this->db->query("SELECT * FROM department WHERE department_id='".(int)$department_id."'");
        // echo '<pre>';
        // print_r($query->rows);
        // echo '</pre>';exit;
    return $query->row['department_name'];
}

public function getPositionById($position_id){
    $query = $this->db->query("SELECT * FROM position WHERE position_id='".(int)$position_id."'");

    return $query->row['position_name'];
}

public function getTotalStaffs() {
    $sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "staff";

    $query = $this->db->query($sql);

    return $query->row['total'];
}

}