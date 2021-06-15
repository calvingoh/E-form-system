<?php
class ModelEformEformEnabled extends Model {

    public function addReply($reply_id, $data){
		$this->db->query("INSERT INTO `" . DB_PREFIX . "reply` SET reply_format = '" . $this->db->escape($data['form_input']) . "', eform_id = '" . (int)$data['eform_id'] . "'");
        $reply_id = $this->db->query("SELECT reply_id FROM reply WHERE reply_id=(SELECT MAX(reply_id) FROM reply)");
        
        return $reply_id->row['reply_id'];
    }
    
    public function addApplicant($data,$reply_id) {
        // exit("INSERT INTO `" . DB_PREFIX . "applicant` SET user_id = '" . (int)$this->user->getId() . "', eform_id = '" . (int)$data['eform_id'] . "', reply_id = '" .(int)$reply_id . "', department = '" . $this->db->escape($data['department']) . "', status = '" . $this->db->escape($data['status']) . "', date_submitted=NOW()");
        $this->db->query("INSERT INTO `" . DB_PREFIX . "applicant` SET user_id = '" . (int)$this->user->getId() . "', eform_id = '" . (int)$data['eform_id'] . "', reply_id = '" .(int)$reply_id . "', department = '" . $this->db->escape($data['department']) . "', status = '" . $this->db->escape($data['status']) . "', remarks = '" . $this->db->escape($data['remarks']) . "', date_submitted=NOW()");
        $applicant_id = $this->db->getLastId();
        return $applicant_id;
	}
  
    public function editReply($reply_id, $data){

		  $this->db->query("UPDATE `" . DB_PREFIX . "reply` SET reply_format = '" . $this->db->escape($data['form_input'])."' WHERE eform_id = '" . (int)$reply_id . "'");
    }
    
    public function getEform($eform_id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "eform` WHERE eform_id = '" . (int)$eform_id . "'");

        return $query->row;
    }

    public function getReplyById($reply_id){
		$query = $this->db->query("SELECT(SELECT reply_format FROM reply WHERE reply_id='".(int)$reply_id."') AS reply_format");

		return $query->row['reply_format'];	
	}

    public function getEformsByStatus($eform_id){
        $query = $this->db->query("SELECT * FROM eform WHERE status = '1'");

        return $query->rows;
    }
}