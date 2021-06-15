
<?php
class ModelAccountEform extends Model {
	public function getEforms($data = array()) {
		// exit("SELECT * FROM forms");
		 $query = $this->db->query("SELECT * FROM eform"); 

		return $query->rows;
	}

	public function getEformFormat($eform_id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "eform` WHERE eform_id = '" . (int)$eform_id . "'");

        return $query->row;
    }

    public function getEform($eform_id) {
		$query = $this->db->query("SELECT DISTINCT *, ef.eform_id, ef.eform_name AS eform_name, (SELECT department_name FROM " . DB_PREFIX . " department dp WHERE dp.department_id = ef.department) AS department, (SELECT status_name FROM " . DB_PREFIX . "status st WHERE st.status_id  = ef.status ) AS status FROM " . DB_PREFIX . "eform ef LEFT JOIN " . DB_PREFIX . "department dp ON (ef.department  = dp.department_id ) LEFT JOIN " . DB_PREFIX . "status st ON (ef.status = st.status_id) WHERE ef.eform_id ='" .(int)$eform_id . "'" );

		if ($query->num_rows){
			return array(
				'eform_id' => $query->row['eform_id'],
				'eform_name' => $query->row['eform_name'],
				'department' => $query->row['department'],
				'status' => $query->row['status'],
				'date_submitted' => $query->row['date_added']
			);
		}
		else{
			return false;
		}
    }

	public function addApplication($eform_id) {
		//  exit("INSERT INTO " . DB_PREFIX . "applicant SET staff_id = '" . (int)$this->staff->getId() . "', eform_id = '" . (int)$this->staff->getId() . "', eform_id = '" . (int)$eform_id . "', date_added = NOW()");
		$query = $this->db->query("INSERT INTO " . DB_PREFIX . "applicant SET staff_id = '" . (int)$this->staff->getId() . "', eform_id = '" . (int)$this->staff->getId() . "', eform_id = '" . (int)$eform_id . "', date_added = NOW()");
        
        return $query->rows;
	}

	public function getEformsByDepartment($department_id) {
			//   exit("SELECT * FROM forms WHERE department = '" . (int)$department_id . "'");
	    $query = $this->db->query("SELECT * FROM eform WHERE department = '" . (int)$department_id . "' AND status ='1'");

		return $query->rows;
	}

	public function getTotalEforms() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "eform");

		return $query->row['total'];
    }
    
    public function addReply($reply_id, $data){
		$this->db->query("INSERT INTO `" . DB_PREFIX . "reply` SET reply_format = '" . $this->db->escape($data['form_input']) . "', eform_id = '" . (int)$data['eform_id'] . "'");
        $reply_id = $this->db->query("SELECT reply_id FROM reply WHERE reply_id=(SELECT MAX(reply_id) FROM reply)");
        
        return $reply_id->row['reply_id'];
    }
    
    public function addApplicant($data,$reply_id) {
		// exit("INSERT INTO `" . DB_PREFIX . "applicant` SET staff_id = '" . (int)$this->staff->getId() . "', eform_id = '" . (int)$data['eform_id'] . "', reply_id = '" .(int)$reply_id . "', status = '" . $this->db->escape($data['status']) . "', date_submitted=NOW()");
        $this->db->query("INSERT INTO `" . DB_PREFIX . "applicant` SET staff_id = '" . (int)$this->staff->getId() . "', eform_id = '" . (int)$data['eform_id'] . "', reply_id = '" .(int)$reply_id . "', status = '" . $this->db->escape($data['status']) . "', date_submitted=NOW()");
        $applicant_id = $this->db->getLastId();
        return $applicant_id;
	}

	public function getDepartmentById($department_id){
		$query = $this->db->query("SELECT * FROM department WHERE department_id='".(int)$department_id."'");
			// echo '<pre>';
			// print_r($query->rows);
			// echo '</pre>';exit;
		return $query->row['department_name'];
	}
  
	}