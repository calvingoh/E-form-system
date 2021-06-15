<?php

class ModelCatalogUser extends Model {
    public function addUser($data){
        $this->db->query("INSERT INTO `" . DB_PREFIX . "position` SET position_name = '" . $this->db->escape($data['position_name']) . "', date_added = NOW(), date_modified = NOW()");
		exit("INSERT INTO `" . DB_PREFIX . "position` SET position_name = '" . $this->db->escape($data['position_name']) . "', date_added = NOW(), date_modified = NOW()");
		return $this->db->getLastId();
    }
}

?>