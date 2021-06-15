<?php
class ModelDepartmentDepartment extends Model {
	public function addDepartment($data) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "department` SET department_name = '" . $this->db->escape($data['department_name']) . "'");

		$department_id = $this->db->getLastId();

		return $department_id;
	}

	public function editDepartment($department_id, $data) {
		// echo $department_id;exit;
		$this->db->query("UPDATE `" . DB_PREFIX . "department` SET department_name = '" . $this->db->escape($data['department_name']) . "' WHERE department_id = '" . (int)$department_id . "'");
	}

	public function deleteDepartment($department_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "department` WHERE department_id = '" . (int)$department_id . "'");
	}

	public function getDepartment($department_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "department` WHERE department_id = '" . (int)$department_id . "'");

		return $query->row;
	}


	public function getDepartments($data = array()) {
		$query = $this->db->query ("SELECT * FROM department");
		return $query -> rows;
	}
	

	public function getTotalDepartment() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "department");

		return $query->row['total'];
	}

	
}