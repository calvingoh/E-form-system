<?php
class ControllerCommonColumnLeft extends Controller {
	public function index() {
		if (isset($this->request->get['user_token']) && isset($this->session->data['user_token']) && ($this->request->get['user_token'] == $this->session->data['user_token'])) {
			$this->load->language('common/column_left');

			// Create a 3 level menu array
			// Level 2 can not have children
			
			// Menu
			$data['menus'][] = array(
				'id'       => 'menu-dashboard',
				'icon'	   => 'fa-dashboard',
				'name'	   => $this->language->get('text_dashboard'),
				'href'     => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true),
				'children' => array()
			);
			
			// System
			$system = array();
			
			if ($this->user->hasPermission('access', 'setting/setting')) {
				$system[] = array(
					'name'	   => $this->language->get('text_setting'),
					'href'     => $this->url->link('setting/store', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()		
				);	
			}

			//E-form
			$eform = array();

			if ($this->user->hasPermission('access', 'eform/eform')) {		
				$eform[] = array(
					'name'	   => $this->language->get('text_eform'),
					'href'     => $this->url->link('eform/eform', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()		
				);	
			}

			if ($this->user->hasPermission('access', 'eform/eform_enabled')) {	
				$eform[] = array(
					'name'	   => $this->language->get('text_eform_enabled'),
					'href'     => $this->url->link('eform/eform_enabled', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()		
				);	
			}

			$data['menus'][] = array(
				'id'       => 'menu-eform',
				'icon'	   => 'fa fa-pencil-square-o', 
				'name'	   => $this->language->get('text_eform'),
				'href'     => '',
				'children' => $eform
			);
			
			//applicant
			$applicant = array();

			if ($this->user->hasPermission('access', 'applicant/applicant')) {		
				$applicant[] = array(
					'name'	   => $this->language->get('text_approve'),
					'href'     => $this->url->link('applicant/applicant', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()		
				);	
			}

			
			if ($this->user->hasPermission('access', 'applicant/applicant')) {		
				$applicant[] = array(
					'name'	   => $this->language->get('text_approved'),
					'href'     => $this->url->link('applicant/applicant_approved', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()		
				);	
			}

			$data['menus'][] = array(
				'id'       => 'menu-applicant',
				'icon'	   => 'fa fa-envelope', 
				'name'	   => $this->language->get('text_applicant'),
				'href'     => '',
				'children' => $applicant
			);

			//position
			$position = array();
			if ($this->user->hasPermission('access', 'position/position')) {		
				$position[] = array(
					'name'	   => $this->language->get('text_position'),
					'href'     => $this->url->link('position/position', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}
				
			$data['menus'][] = array(
				'id'       => 'menu-position',
				'icon'	   => '	fa fa-book', 
				'name'	   => $this->language->get('text_position'),
				'href'     => '',
				'children' => $position
			);	

			//department
			$department = array();
			if ($this->user->hasPermission('access', 'department/department')) {		
				$department[] = array(
					'name'	   => $this->language->get('text_department'),
					'href'     => $this->url->link('department/department', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}
			$data['menus'][] = array(
				'id'       => 'menu-department',
				'icon'	   => 'fa fa-briefcase', 
				'name'	   => $this->language->get('text_department'),
				'href'     => '',
				'children' => $department
			);	

			//admin
			// $admin = array();
			// if($this->user->hasPermission('access', 'admin/admin')){
			// 	$admin[] = array(
			// 		'name'	   => $this->language->get('text_admin'),
			// 		'href'	   => $this->url->link('admin/admin', 'user_token=' . $this->session->data['user_token'], true),
			// 		'children' => array()
			// 	);
			// }
						
			// if ($admin) {
			// 	$data['menus'][] = array(
			// 		'id'       => 'menu-admin',
			// 		'icon'	   => 'fa-television', 
			// 		'name'	   => $this->language->get('text_admin'),
			// 		'href'     => '',
			// 		'children' => $admin
			// 	);	
			// }

			//staff
			$staff = array();
			if($this->user->hasPermission('access', 'staff/staff')){
				$staff[] = array(
					'name'	   => $this->language->get('text_staff'),
					'href'	   => $this->url->link('staff/staff', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()
				);
			}
						
			if ($staff) {
				$data['menus'][] = array(
					'id'       => 'menu-staff',
					'icon'	   => 'fa fa-user', 
					'name'	   => $this->language->get('text_staff'),
					'href'     => '',
					'children' => $staff
				);	
			}

			// Users
			$user = array();
			if ($this->user->hasPermission('access', 'user/user')) {
				$user[] = array(
					'name'	   => $this->language->get('text_users'),
					'href'     => $this->url->link('user/user', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()		
				);	
			}

			if ($this->user->hasPermission('access', 'user/user_permission')) {	
				$user[] = array(
					'name'	   => $this->language->get('text_user_group'),
					'href'     => $this->url->link('user/user_permission', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()		
				);	
			}
			
			// if ($user) {
			// 	$user[] = array(
			// 		'name'	   => $this->language->get('text_users'),
			// 		'href'     => '',
			// 		'children' => $user		
			// 	);
			// }

			if ($user) {
				$data['menus'][] = array(
					'id'       => 'menu-system',
					'icon'	   => 'fa fa-users', 
					'name'	   => $this->language->get('text_users'),
					'href'     => '',
					'children' => $user
				);
			}

			return $this->load->view('common/column_left', $data);
		}
	}
}