<?php
class ModelAccountFormApproval extends Model {
	public function addFormApproval($form_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "applicant WHERE staff_id = '" . (int)$this->staff->getId() . "' AND form_id = '" . (int)$form_id . "'");

		$this->db->query("INSERT INTO " . DB_PREFIX . "applicant SET staff_id = '" . (int)$this->staff->getId() . "', form_id = '" . (int)$form_id . "', date_added = NOW()");
    }
    
    public function editFormApproval($applicant_id) {
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

	public function deleteFormSubmitted($form_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "applicant WHERE staff_id = '" . (int)$this->staff->getId() . "' AND form_id = '" . (int)$form_id . "'");
	}

	public function getFormApproval() {
        $query = $this->db->query("SELECT *, (SELECT status_name FROM " . DB_PREFIX . " status ss WHERE ss.status_id = ap.status) AS status FROM " . DB_PREFIX . "applicant ap JOIN " .DB_PREFIX . 
        "applicant_info api ON (ap.applicant_id = api.applicant_id) JOIN " . DB_PREFIX . "staff st ON (st.staff_id = api.staff_id) JOIN ". DB_PREFIX . " status ss ON (ap.status = ss.status_id ) WHERE ap.staff_id = '" . (int)$this->staff->getId() . "'");

		return $query->rows;
	}

	public function getTotalFormApproval() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "applicant WHERE staff_id = '" . (int)$this->staff->getId() . "'");

		return $query->row['total'];
    }
    
    public function getApplicant($applicant_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "applicant` WHERE applicant_id = '" . (int)$applicant_id . "'");

		return $query->row;
	}

	// public function getApplicant($applicant_id){
	// 	$applicant_data  = array();

	// 	$query = $this->db->query("SELECT st.staff_id, st.staff_name AS staff_name FROM " . DB_PREFIX . "staff st LEFT JOIN " . DB_PREFIX . "applicant_info api ON (st.staff_id = api.staff_id ) LEFT JOIN " . DB_PREFIX . " applicant ap ON ( ap.applicant_id = api.staff_id) WHERE api.applicant_id ='" .(int)$applicant_id ."'");

	// 	if ($query->num_rows){
	// 		return array(
	// 			'staff_id' => $query->row['staff_id'],
	// 			'staff_name' =>$query->row['staff_name']
	// 		);
	// 	}

	// 	return false;
    // }

    public function getApplicants() {
        $query = $this->db->query("SELECT * FROM applicant");

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

    public function getStaffOrder($eform_id){
		$query=$this->db->query('SELECT approval FROM eform WHERE eform_id='.$eform_id);
		if(!$query->num_rows){
			return false;
        }
        
		$approve=$query->rows;
		$approve=explode(',',$approve[0]['approval']);//exit(print_r($approve));
        $grades=array();
		for($i=0;$i<sizeof($approve);$i++){
			$query=$this->db->query('SELECT position FROM staff WHERE staff_name="'.$approve[$i].'"');
			if($query->num_rows){
                $position_id=$query->rows;
                $query=$this->db->query('SELECT grade FROM position WHERE position_id='.$position_id[0]['position']);
                if(!$query->num_rows){
                    return false;
                }
                $grades[]=$query->rows;
            }
			else{
                $query=$this->db->query('SELECT position FROM user WHERE firstname="'.$approve[$i].'"');
                $position_id=$query->rows;
                $query=$this->db->query('SELECT grade FROM position WHERE position_id='.$position_id[0]['position']);
                if(!$query->num_rows){
                    return false;
                }
                $grades[]=$query->rows;
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

	public function getStaffApplicantsPending($staff_name){
		$query=$this->db->query('SELECT eform_id FROM eform WHERE approval LIKE "%'.$staff_name.'%"');
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
		// exit(print_r($applicants));
		$applicantList=array();
		for($x=0;$x<sizeof($applicants);$x++){
			for($i=0;$i<sizeof($applicants[$x]);$i++){
            $approval_order=$this->getStaffOrder($applicants[$x][$i]['eform_id']);
			if(isset($approval_order[$applicants[$x][$i]['status']]['name'])){
				if($approval_order[$applicants[$x][$i]['status']]['name']==$staff_name){
					$applicantList[]=$applicants[$x][$i];
				}	
			}
		}
        }
        // exit(print_r($applicantList));
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
    
    public function getEfromById($eform_id){
		$query = $this->db->query("SELECT(SELECT eform_name FROM eform WHERE eform_id='".(int)$eform_id."') AS eform_name");

		return $query->row['eform_name'];  
    }
    
    public function getDepartmentById($department_id){
		$query = $this->db->query("SELECT(SELECT department_name FROM department WHERE department_id='".(int)$department_id."') AS department_name");

		return $query->row['department_name'];
    }
    
    public function getStaffById($staff_id){
		$query = $this->db->query("SELECT(SELECT staff_name FROM staff WHERE staff_id='".(int)$staff_id."') AS staff_name");

		return $query->row['staff_name'];
    }
    
    public function getReplyById($reply_id){
		$query = $this->db->query("SELECT(SELECT reply_format FROM reply WHERE reply_id='".(int)$reply_id."') AS reply_format");

		return $query->row['reply_format'];	
	}

	public function addEmail($mail_to, $mail_from, $subject, $body){
		$query = $this->db->query('INSERT INTO email_cron(mail_from, mail_to, subject, body, system) VALUES ("'.$mail_from. '", "'.$mail_to. '", "'.$subject. '", "'.$body. '", "Eform")');
		if($query){
			return true;
		}
		else
		return false;
	}

	public function getUserById($user_id){
		$query = $this->db->query("SELECT(SELECT firstname FROM user WHERE user_id='".(int)$user_id."') AS firstname");

		return $query->row['firstname'];
	}


}