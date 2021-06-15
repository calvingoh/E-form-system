<?php
class ModelEformEform extends Model {
	public function addEform($data) {
        $this->db->query("INSERT INTO `" . DB_PREFIX . "eform` SET eform_name = '" . $this->db->escape($data['eform_name']) . "', status = '" . (int)$data['status'] . "', department = '" . (int)$data['department_id'] . "', eform_format = '" . $this->db->escape($data['eform_format']) . "', approval = '" . $data['approve'] . "', date_added=NOW()");
        $eform_id = $this->db->getLastId();
		return $eform_id;
	}

	public function editEform($eform_id, $data) {
		$this->db->query("UPDATE `" . DB_PREFIX . "eform` SET eform_name = '" . $this->db->escape($data['eform_name']) . "', status = '" . (int)$data['status'] . "', department = '" . (int)$data['department_id'] . "', eform_format = '" . $this->db->escape($data['eform_format']) . "', approval = '" . $data['approve'] . "', date_modified=NOW() WHERE eform_id = '" . (int)$eform_id . "'");
	}

	public function deleteEform($eform_id) {
		// exit("DELETE FROM `" . DB_PREFIX . "eform` WHERE eform_id = '" . (int)$eform_id ."'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "eform` WHERE eform_id = '" . (int)$eform_id ."'");    }
    
    public function getEform($eform_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "eform` WHERE eform_id = '" . (int)$eform_id . "'");

		return $query->row;
	}

	public function getEforms($eform_id) {
        $query = $this->db->query("SELECT * FROM eform ");

        return $query->rows;
	}

	public function getDepartmentById($department_id){
		$query = $this->db->query("SELECT * FROM department WHERE department_id='".(int)$department_id."'");

		return $query->row['department_name'];
	}

	public function getTotalEforms() {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "eform";
	
		$query = $this->db->query($sql);
	
		return $query->row['total'];
	}

	public function getEformsByStatus($eform_id){
        $query = $this->db->query("SELECT * FROM eform WHERE status = '1'");

		return $query->rows;
	}

	public function getStaff(){
		$query=$this->db->query('SELECT * FROM staff');
		if($query->num_rows){
			return $query->rows;
		}
		else{
			return false;
		}
	}
	public function getAdmin(){
		$query=$this->db->query('SELECT * FROM user');
		if($query->num_rows){
			return $query->rows;
		}
		else{
			return false;
		}
	}
}
