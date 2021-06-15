<?php
namespace Cart;
class Staff {
	private $staff_id;
	private $staff_name;
	private $email;
	private $department_id;
	private $position_id;
	private $date_added;
	private $date_modified;
	

	public function __construct($registry) {
		$this->config = $registry->get('config');
		$this->db = $registry->get('db');
		$this->request = $registry->get('request');
		$this->session = $registry->get('session');

		if (isset($this->session->data['staff_id'])) {
			$staff_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "staff WHERE staff_id = '" . (int)$this->session->data['staff_id'] . "'");
			// AND status-'1'

			if ($staff_query->num_rows) {
				$this->staff_id = $staff_query->row['staff_id'];
				$this->staff_name = $staff_query->row['staff_name'];
				$this->email = $staff_query->row['email'];
				$this->department = $staff_query->row['department'];
				$this->position = $staff_query->row['position'];
				//$this->db->query("UPDATE " . DB_PREFIX . "staff  WHERE staff_id = '" . (int)$this->staff_id . "'");

				// exit("SELECT * FROM " . DB_PREFIX . "staff WHERE staff_id = '" . (int)$this->session->data['staff_id'] . "'");

				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "staff WHERE staff_id = '" . (int)$this->session->data['staff_id'] . "'");

				if (!$query->num_rows) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "staff'" . (int)$this->session->data['staff_id'] . "', date_added = NOW()");
				}
			} else {
				$this->logout();
			}
		}
	}

  public function login($email, $password, $override = false) {
		if ($override){
			//exit("SELECT * FROM " . DB_PREFIX . "staff WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");
			$staff_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "staff WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");
			// AND status = '1'
		}
		 else {
		 	 // exit("SELECT * FROM " . DB_PREFIX . "staff WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "' AND (password = SHA1(CONCAT(salt, SHA1(CONCAT(salt, SHA1('" . $this->db->escape($password) . "')))))) OR password = '" . $this->db->escape(md5($password)) . "'");
			$staff_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "staff WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "' AND (password = SHA1(CONCAT(salt, SHA1(CONCAT(salt, SHA1('" . $this->db->escape($password) . "')))))) OR password = '" . $this->db->escape(md5($password)) . "'");
		}


		if ($staff_query->num_rows) {
			$this->session->data['staff_id'] = $staff_query->row['staff_id'];
			$this->staff_id = $staff_query->row['staff_id'];
			$this->staff_name = $staff_query->row['staff_name'];
			$this->email = $staff_query->row['email'];
			$this->department_id = $staff_query->row['department_id'];
			$this->position_id = $staff_query->row['position_id'];
			$this->date_added = $staff_query->row['date_added'];
			$this->date_modified = $staff_query->row['date_modified'];
			// $this->status = $customer_query->row['status'];
			// $this->db->query("UPDATE " . DB_PREFIX . "staff  WHERE staff_id = '" . (int)$this->staff_id . "'");

			return true;
		} else {
			return false;
		}
	}

	public function logout() {
		unset($this->session->data['staff_id']);

		$this->staff_id = '';
		$this->staff_name = '';
		$this->email = '';
		$this->department_id = '';
		$this->position_id = '';
		$this->date_added = '';
		$this->date_modified = '';
		// $this->status = '';
		// $this->address_id = '';
	}

	public function isLogged() {
			
		return $this->staff_id;
	}

	public function getId() {
		return $this->staff_id;
	}

	public function getStaffName() {
		return $this->staff_name;
	}

	public function getEmail() {
		return $this->email;
	}

	public function getPosition() {
		return $this->position;
	}

	public function getDepartment() {
		return $this->department;
	}

	public function getDateAdded() {
		return $this->date_added;
	}

	public function getDateModified() {
		return $this->date_modified;
	}

	// public function getStatus() {
	// 	return $this->status;
	// }

	// // public function getAddressId() {
	// 	return $this->address_id;
	// }

	// public function getBalance() {
	// 	$query = $this->db->query("SELECT SUM(amount) AS total FROM " . DB_PREFIX . "customer_transaction WHERE customer_id = '" . (int)$this->customer_id . "'");

	// 	return $query->row['total'];
	// }

	// public function getRewardPoints() {
	// 	$query = $this->db->query("SELECT SUM(points) AS total FROM " . DB_PREFIX . "customer_reward WHERE customer_id = '" . (int)$this->customer_id . "'");

	// 	return $query->row['total'];
	// }
}
