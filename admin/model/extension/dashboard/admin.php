<?php
class ModelExtensionDashboardAdmin extends Model {
	public function getTotalCustomers($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "admin";

		$implode = array();

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}
}