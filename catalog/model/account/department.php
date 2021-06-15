<?php
class ModelAccountDepartment extends Model {
	public function getDepartments($data = array()) {
		$sql = "SELECT * FROM `" . DB_PREFIX . "department`";
		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getTotalDepartment() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "department` WHERE department_id = '" . (int)$this->customer->getId() . "'");

		return $query->row['total'];
	}
}