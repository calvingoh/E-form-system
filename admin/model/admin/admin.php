<?php
class ModelAdminAdmin extends Model {
	public function addAdmin($data) {
        // echo '<pre>';
        // print_r($data);
        // echo '</pre>';exit;
        $this->db->query("INSERT INTO `" . DB_PREFIX . "admin` SET name = '" . $this->db->escape($data['name']) . "', username = '" . $this->db->escape($data['username']) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt .sha1($data['password'])))) . "', department = '" . $this->db->escape($data['department_id']) . "', position = '" . $this->db->escape($data['position_id']) . "', status = '" . $this->db->escape($data['status']) . "', date_added=NOW()");
        $admin_id = $this->db->getLastId();
        return $admin_id;
	}
		
	public function editAdmin($admin_id, $data) {
		$this->db->query("UPDATE `" . DB_PREFIX . "admin` SET name = '" . $this->db->escape($data['name']) . "', username = '" . $this->db->escape($data['username']) . "', password = '" . $this->db->escape($data['password']) . "', 
		department = '" . (int)$data['department_id'] . "', position = '" . (int)$data['position_id'] . "', status = '" . (int)$data['status'] . "', date_modified = NOW() WHERE admin_id = '" . (int)$admin_id . "'");
	}

	public function deleteAdmin($admin_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "admin` WHERE admin_id = '" . (int)$admin_id ."'");
    }
    
    public function getAdmin($admin_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "admin` WHERE admin_id = '" . (int)$admin_id . "'");

		return $query->row;
	}

	public function getAdmins($admin_id) {
        $query = $this->db->query("SELECT * FROM admin");

        // echo '<pre>';
        // print_r($query->rows);
        // echo '</pre>';exit;

        return $query->rows;
}

public function editPassword($admin_id, $password){
        $this->db->query("UPDATE `" . DB_PREFIX . "admin` SET salt = '" . $this->db->escape($salt = token(9)) . "', 
        password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($password)))) . "' WHERE admin_id = '" . (int)$admin_id . "'");

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

public function getTotalAdmins() {
    $sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "admin";

    $query = $this->db->query($sql);

    return $query->row['total'];
}

}