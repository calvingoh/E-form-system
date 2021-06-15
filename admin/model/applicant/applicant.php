<?php
class ModelApplicantApplicant extends Model {
	public function addApplicant($data) {
        // exit("INSERT INTO `" . DB_PREFIX . "applicant` SET staff_id = '" . (int)$data['staff_id'] . "', eform_id = '" . (int)$data['eform_id'] . "', reply_id = '" .(int)$data['reply_id'] . "', department = '" . $this->db->escape($data['department']) . "', status = '" . $this->db->escape($data['status']) . "'");
        $this->db->query("INSERT INTO `" . DB_PREFIX . "applicant` SET staff_id = '" . (int)$data['staff_id'] . "', eform_id = '" . (int)$data['eform_id'] . "', reply_id = '" .(int)$data['reply_id'] . "', department = '" . $this->db->escape($data['department']) . "', status = '" . $this->db->escape($data['status']) . "', remarks = '" . $this->db->escape($data['remarks']) . "', date_submitted=NOW()");
        $applicant_id = $this->db->getLastId();
        return $applicant_id;
	}
		
	public function editApplicant($applicant_id, $data) {
		$query=$this->db->query('SELECT status FROM applicant WHERE applicant_id='.$applicant_id);
		$status=$query->rows;
		$status=$status[0]['status']+1;

		//exit("UPDATE `" . DB_PREFIX . "applicant` SET  date_approved = CURDATE(), status=".$status." WHERE applicant_id = '" . (int)$applicant_id . "'");
		$this->db->query("UPDATE `" . DB_PREFIX . "applicant` SET  date_approved = CURDATE(), status=".$status." WHERE applicant_id = '" . (int)$applicant_id . "'");
	}

	public function editApplicantReject($applicant_id){
		$query=$this->db->query('UPDATE applicant SET date_approved=NULL, status=-1 WHERE applicant_id='.$applicant_id);
		if($query){
			return true;
		}
		else return false;
	}

	public function getUserOrder($eform_id){
		$query=$this->db->query('SELECT approval FROM eform WHERE eform_id='.$eform_id);
		if(!$query->num_rows){
			return false;
		}
		$approve=$query->rows;
		$approve=explode(',',$approve[0]['approval']);
		$grades=array();
		for($i=0;$i<sizeof($approve);$i++){
			$query=$this->db->query('SELECT position FROM user WHERE firstname="'.$approve[$i].'"');
			if($query->num_rows){
				$position_id=$query->rows;
				$query=$this->db->query('SELECT grade FROM position WHERE position_id='.$position_id[0]['position']);
				if($query->num_rows){
					$grades[]=$query->rows;
				}
			}
			else{
				$query=$this->db->query('SELECT position FROM staff WHERE staff_name="'.$approve[$i].'"');
				if($query->num_rows){
					$position_id=$query->rows;
					$query=$this->db->query('SELECT grade FROM position WHERE position_id='.$position_id[0]['position']);
					if($query->num_rows){
						$grades[]=$query->rows;
					}
				}
			}	
		}
		$users=array();
		for($i=0;$i<sizeof($approve);$i++){
			$users[$i]['grade']=$grades[$i][0]['grade'];
			$users[$i]['name']=$approve[$i];
		}
		array_multisort(array_column($users,'grade'), SORT_ASC, $users);
		return $users;
	}

	public function getApplicantsPending($firstname){
		$query=$this->db->query('SELECT eform_id FROM eform WHERE approval LIKE "%'.$firstname.'%"');
		if(!$query->num_rows){
			return false;
		}
		$eform_ids=$query->rows;
		
		$applicants=array();
		for($i=0;$i<sizeof($eform_ids);$i++){
			$query=$this->db->query('SELECT * FROM applicant WHERE eform_id='.$eform_ids[$i]['eform_id']);	
			if($query->num_rows){
				$applicants[]=$query->rows;
			}
		}
		//exit(print_r($applicants));
		$applicantList=array();
		for($x=0;$x<sizeof($applicants);$x++){
			for($i=0;$i<sizeof($applicants[$x]);$i++){
			$approval_order=$this->getUserOrder($applicants[$x][$i]['eform_id']);
			
			if(isset($approval_order[$applicants[$x][$i]['status']]['name'])){
				if($approval_order[$applicants[$x][$i]['status']]['name']==$firstname && $approval_order[$applicants[$x][$i]['status']]['name']==$firstname ){
					$applicantList[]=$applicants[$x][$i];
				}	
			}
		}
	}
		return $applicantList;
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
	
	public function deleteApplicant($applicant_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "applicant` WHERE applicant_id = '" . (int)$applicant_id ."'");
    }
    
    public function getApplicant($applicant_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "applicant` WHERE applicant_id = '" . (int)$applicant_id . "'");

		return $query->row;
	}

	public function getApplicants($applicant_id) {
        $query = $this->db->query("SELECT * FROM applicant");

        return $query->rows;
	}

	public function getApplicantsByUser() {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "applicant WHERE user_id = '" . (int)$this->user->getId() . "'");

		return $query->rows;
	}

	public function getTotalApplicants() {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "applicant";

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getEform($eform_id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "eform` WHERE eform_id = '" . (int)$eform_id . "'");

        return $query->row;
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

	public function getUserById($user_id){
		$query = $this->db->query("SELECT(SELECT firstname FROM user WHERE user_id='".(int)$user_id."') AS firstname");

		return $query->row['firstname'];
	}

	public function getDepartmentById($department_id){
		$query = $this->db->query("SELECT(SELECT department_name FROM department WHERE department_id='".(int)$department_id."') AS department_name");

		return $query->row['department_name'];
	}

	public function getStatusById($status_id){
		$query = $this->db->query("SELECT * FROM status");

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

	public function getApplicantbyId($eform_id){
		$query=$this->db->query("SELECT * FROM `" . DB_PREFIX . "applicant` WHERE applicant_id = '" . (int)$eform_id . "'");
		if($query->num_rows){
			return $query->rows;
		}
		else{
			return false;
	}
	}

	public function addEmail($mail_to, $mail_from, $subject, $body){
		$query = $this->db->query('INSERT INTO email_cron(mail_from, mail_to, subject, body, system) VALUES ("'.$mail_from. '", "'.$mail_to. '", "'.$subject. '", "'.$body. '", "Eform")');
		if($query){
			return true;
		}
		else
		return false;
	}
}