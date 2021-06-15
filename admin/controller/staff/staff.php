<?php
class ControllerStaffStaff extends Controller {
	private $error = array();

	public function index() {

		$this->load->language('staff/staff');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('staff/staff');

		$this->getList();
	}

	public function add() {
		$this->load->language('staff/staff');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('staff/staff');

		if (($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateForm()) ) {
			$this->model_staff_staff->addStaff($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
		

			$this->response->redirect($this->url->link('staff/staff', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('staff/staff');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('staff/staff');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_staff_staff->editStaff($this->request->get['staff_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('staff/staff', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('staff/staff');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('staff/staff');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $staff_id) {
				$this->model_staff_staff->deleteStaff($staff_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
			

			$this->response->redirect($this->url->link('staff/staff', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'store';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		//breadcrumbs
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('staff/staff', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['add'] = $this->url->link('staff/staff/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('staff/staff/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$this->load->model('setting/store');
		$stores = $this->model_setting_store->getStores();

		$data['staffs'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_staff'),
			'limit' => $this->config->get('config_limit_staff')
		);

		$staff_total = $this->model_staff_staff->getTotalStaffs($filter_data);


		$results = $this->model_staff_staff->getStaffs($filter_data);

		foreach ($results as $result) {
			$data['staffs'][] = array(
				'staff_id'        => $result['staff_id'],
				'staff_name'      => $result['staff_name'],
				'email'           => $result['email'],
				'password'        => $result['password'],
				'position'        => $this->model_staff_staff->getPositionById($result['position']),
				'department'      => $this->model_staff_staff->getDepartmentById($result['department']),
                'status' 		  => ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
				'date_added' 	  => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'date_modified'   => date($this->language->get('date_format_short'), strtotime($result['date_modified'])),
				'edit'            => $this->url->link('staff/staff/edit', 'user_token=' . $this->session->data['user_token'] . '&staff_id=' . $result['staff_id'], true),
			);
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		
		$data['sort_staff_id'] = $this->url->link('staff/staff', 'user_token=' . $this->session->data['user_token'] . '&sort=staff_id' . $url, true);
		$data['sort_name'] = $this->url->link('staff/staff', 'user_token=' . $this->session->data['user_token'] . '&sort=staff_name' . $url, true);
		$data['sort_username'] = $this->url->link('staff/staff', 'user_token=' . $this->session->data['user_token'] . '&sort=email' . $url, true);
		$data['sort_password'] = $this->url->link('staff/staff', 'user_token=' . $this->session->data['user_token'] . '&sort=password' . $url, true);
		$data['sort_department'] = $this->url->link('staff/staff', 'user_token=' . $this->session->data['user_token'] . '&sort=department' . $url, true);
		$data['sort_position'] = $this->url->link('staff/staff', 'user_token=' . $this->session->data['user_token'] . '&sort=position' . $url, true);
		$data['sort_status'] = $this->url->link('staff/staff', 'user_token=' . $this->session->data['user_token'] . '&sort=status' . $url, true);
		$data['sort_date_added'] = $this->url->link('staff/staff', 'user_token=' . $this->session->data['user_token'] . '&sort=date_added' . $url, true);
		$data['sort_date_modified'] = $this->url->link('staff/staff', 'user_token=' . $this->session->data['user_token'] . '&sort=date_modified' . $url, true);

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		
		$pagination->page = $page;
		$pagination->limit = 10;
		$pagination->url = $this->url->link('staff/staff/history', 'user_token=' . $this->session->data['user_token'] . '&page={page}', true);

		$data['pagination'] = $pagination->render();


		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		// echo '<pre>';
		// print_r($data);
		// echo '</pre>';exit;
		$this->response->setOutput($this->load->view('staff/staff_list', $data));
	}

	protected function getForm() {
		$data['text_form'] = !isset($this->request->get['staff_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}
		
		if (isset($this->error['username'])) {
			$data['error_username'] = $this->error['username'];
		} else {
			$data['error_username'] = '';
		}
		
		if (isset($this->error['password'])) {
			$data['error_password'] = $this->error['password'];
		} else {
			$data['error_password'] = '';
		}
		
	
		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		//breadcrumbs
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('staff/staff', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['staff_id'])) {
			$data['action'] = $this->url->link('staff/staff/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('staff/staff/edit', 'user_token=' . $this->session->data['user_token'] . '&staff_id=' . $this->request->get['staff_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('staff/staff', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['staff_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$staff_info = $this->model_staff_staff->getStaff($this->request->get['staff_id']);
		}
		
		if (isset($this->request->post['staff_name'])) {
			$data['staff_name'] = $this->request->post['staff_name'];
		} elseif (!empty($staff_info)) {
			$data['staff_name'] = $staff_info['staff_name'];
		} else {
			$data['staff_name'] = '';
		}

		if (isset($this->request->post['email'])) {
			$data['email'] = $this->request->post['email'];
		} elseif (!empty($staff_info)) {
			$data['email'] = $staff_info['email'];
		} else {
			$data['email'] = '';
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($staff_info)) {
			$data['status'] = $staff_info['status'];
		} else {
			$data['status'] = '';
		}

		if (isset($this->request->post['department'])) {
			$data['department'] = $this->request->post['department'];
		} elseif (!empty($staff_info)) {
			$data['department'] = $staff_info['department'];
		} else {
			$data['department'] = '';
		}

		if (isset($this->request->post['position'])) {
			$data['position'] = $this->request->post['position'];
		} elseif (!empty($staff_info)) {
			$data['position'] = $staff_info['position'];
		} else {
			$data['position'] = '';
		}

		if (isset($this->request->post['level'])) {
			$data['level'] = $this->request->post['level'];
		} elseif (!empty($staff_info)) {
			$data['level'] = $staff_info['level'];
		} else {
			$data['level'] = '';
		}
		

		$this->load->model('department/department');
		$this->load->model('position/position');
	
		$data['departments'] = $this->model_department_department->getDepartments();
		$data['positions'] = $this->model_position_position->getPositions();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		// echo '<pre>';
        // print_r($data);
        // echo '</pre>';exit;

		$this->response->setOutput($this->load->view('staff/staff_form', $data));
	}

	public function validateForm(){
		if (!$this->user->hasPermission('modify', 'staff/staff')) {
			$this->error['warning'] = $this->language->get('error_permission');
        }
        // echo '<pre>';
        // print_r($this->request->post);
        // echo '</pre>';exit;
        // echo strlen(trim($this->request->post['position_name']));exit;


		if ((strlen(trim($this->request->post['staff_name'])) < 1) || (strlen(trim($this->request->post['staff_name'])) > 32))  {
		$this->error['staff_name'] = $this->language->get('error_staff');
		}
		
		if ((strlen(trim($this->request->post['email'])) < 1) || (strlen(trim($this->request->post['email'])) > 32))  {
			$this->error['email'] = $this->language->get('error_staff');
			}

			if ((strlen(trim($this->request->post['password'])) < 1) || (strlen(trim($this->request->post['password'])) > 32))  {
				$this->error['password'] = $this->language->get('error_staff');
				}

        // echo '<pre>';
        // print_r($this->error['position_name']);
        // echo '</pre>';exit;

        return !$this->error;
		
	}

	protected function validateDelete(){
		if(!$this->user->hasPermission('modify', 'staff/staff')){
			$this->error['warning'] = $this->language->get('error_permission');
		}
		return !$this->error;
}
}