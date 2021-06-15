<?php
class ModelAccountFormSubmitted extends Model {
	public function addFormSubmitted($eform_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "applicant WHERE staff_id = '" . (int)$this->staff->getId() . "' AND eform_id = '" . (int)$eform_id . "'");

		$this->db->query("INSERT INTO " . DB_PREFIX . "applicant SET staff_id = '" . (int)$this->staff->getId() . "', eform_id = '" . (int)$eform_id . "', date_submitted = NOW()");
	}

	public function editApplicant($applicant_id, $data) {
		$query=$this->db->query('SELECT status FROM applicant WHERE applicant_id='.$applicant_id);
		$status=$query->rows;
		$status=$status[0]['status']+1;
		// exit("UPDATE `" . DB_PREFIX . "applicant` SET  date_approved = CURDATE(), status=".$status." WHERE applicant_id = '" . (int)$applicant_id . "'");
		$this->db->query("UPDATE `" . DB_PREFIX . "applicant` SET  date_approved = CURDATE(), status=".$status." WHERE applicant_id = '" . (int)$applicant_id . "'");
	}

	public function deleteFormSubmitted($eform_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "applicant WHERE staff_id = '" . (int)$this->staff->getId() . "' AND eform_id = '" . (int)$eform_id . "'");
	}

	public function getFormSubmitted() {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "applicant WHERE staff_id = '" . (int)$this->staff->getId() . "'");

		return $query->rows;
	}

	public function getTotalFormSubmitted() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "forms_submitted WHERE staff_id = '" . (int)$this->staff->getId() . "'");

		return $query->row['total'];
    }
    
	public function getApplicant($applicant_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "applicant` WHERE applicant_id = '" . (int)$applicant_id . "'");

		return $query->row;
	}

	
	public function getApplicants($applicant_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "applicant WHERE staff_id = '" . (int)$this->staff->getId() . "'");

		return $query->rows;
	}
	
	public function getDepartmentById($department_id){
		$query = $this->db->query("SELECT(SELECT department_name FROM department WHERE department_id='".(int)$department_id."') AS department_name");

		return $query->row['department_name'];
	}

	public function getEfromById($eform_id){
		$query = $this->db->query("SELECT(SELECT eform_name FROM eform WHERE eform_id='".(int)$eform_id."') AS eform_name");

		return $query->row['eform_name'];  
	}

	public function getReplyById($reply_id){
		$query = $this->db->query("SELECT(SELECT reply_format FROM reply WHERE reply_id='".(int)$reply_id."') AS reply_format");

		return $query->row['reply_format'];	
	}

	public function getStaffById($staff_id){
		$query = $this->db->query("SELECT(SELECT staff_name FROM staff WHERE staff_id='".(int)$staff_id."') AS staff_name");

		return $query->row['staff_name'];
	}

	public function getApproval($applicant_id){
		$query=$this->db->query('SELECT eform_id FROM applicant WHERE applicant_id='.$applicant_id);
		if(!$query->num_rows){
			return false;
		}
		$eform_id=$query->rows;
		$query=$this->db->query('SELECT approval FROM eform WHERE eform_id='.$eform_id[0]['eform_id']);
		if(!$query->num_rows){
			return false;
		}
		else{
			return $query->rows;
		}
	}

	public function getApprovedForms($applicant_id){
		$query=$this->db->query('SELECT * FROM applicant WHERE status = "2"');

		return $query->rows;
	}
}