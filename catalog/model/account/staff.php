<?php
class ModelAccountStaff extends Model {

	public function getStaffs($data=array()) {
		$query= $this->db->query("SELECT * FROM staff"); 
		$sort_data = array(
			'staff_id',
			'date_added'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY staff_id";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql); 
		return $query->rows;
	}

	public function getStaff($staff_id){
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX ."staff` WHERE staff_id ='" .(int)$staff_id . "'");
		return $query->row;
	}

	public function getStaffByToken($token) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "staff WHERE token = '" . $this->db->escape($token) . "' AND token != ''");

		$this->db->query("UPDATE " . DB_PREFIX . "staff SET token = ''");

		return $query->row;
	}

	public function getStaffsByEmail($email) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "staff WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");

		return $query->rows;
	}
	public function getDepartmentById($department_id){
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "department WHERE department_id ='" .(int)$department_id . "'");
		return $query->row['department_name'];
	}
	public function getPositionById($position_id){
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "position WHERE position_id ='" .(int)$position_id . "'");
		return $query->row['position_name'];
	}

	public function getTotalStaffs($staff_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "staff`");

		return $query->row['total'];
	}

	public function addLoginAttempt($email) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "staff_login WHERE email = '" . $this->db->escape(utf8_strtolower((string)$email)) . "' ");

		if (!$query->num_rows) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "staff_login SET email = '" . $this->db->escape(utf8_strtolower((string)$email)) . "', total = 1, date_added = '" . $this->db->escape(date('Y-m-d H:i:s')) . "', date_modified = '" . $this->db->escape(date('Y-m-d H:i:s')) . "'");
		} else {
			$this->db->query("UPDATE " . DB_PREFIX . "staff_login SET total = (total + 1), date_modified = '" . $this->db->escape(date('Y-m-d H:i:s')) . "' WHERE staff_login_id = '" . (int)$query->row['staff_login_id'] . "'");
		}
	}

	public function getLoginAttempts($email) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "staff_login` WHERE email = '" . $this->db->escape(utf8_strtolower($email)) . "'");

		return $query->row;
	}

	public function deleteLoginAttempts($email) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "staff_login` WHERE email = '" . $this->db->escape(utf8_strtolower($email)) . "'");
	}

	
}