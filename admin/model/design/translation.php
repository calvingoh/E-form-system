<?php
class ModelDesignTranslation extends Model {
	public function addAdmin($data) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "admins` SET `name` = '" . $this->db->escape($data['name']) . "', `username` = '" . $this->db->escape($data['username']) . "', `password` = '" . $this->db->escape($data['password']) . "', 
		`department` = '" . (int)$data['department'] . "', `position` = '" . (int)$data['position'] . "', `status` = '" . (int)$data['status'] . "', date_added = '" . $this->db->escape($data['date_added']) . "', date_modified = '" . $this->db->escape($data['date_modified']) . "'");
		$admin_id = $this->db->getLastId();

		//$this->cache->delete('product');

		return $admin_id;
	}
		
	public function editAdmin($admin_id, $data) {
		$this->db->query("UPDATE `" . DB_PREFIX . "admins` SET `name` = '" . $this->db->escape($data['name']) . ", username = '" . $this->db->escape($data['username']) . "'', `password` = '" . $this->db->escape($data['password']) . "', 
		`department` = '" . (int)$data['department'] . "', `position` = '" . (int)$data['position'] . "', `status` = '" . (int)$data['status'] . "', date_added = '" . $this->db->escape($data['date_added']) . "', date_modified = NOW() WHERE admin_id = '" . (int)$admin_id . "'");
	}

	public function deleteAdmin($admin_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "admins` WHERE `admin_id = '" . (int)$admin_id ."'");
	}

	public function getAdmin($admin_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM `" . DB_PREFIX . "admins` WHERE `admin_id` = '" . (int)$admin_id . "'");

		return $query->row;
	}
}
