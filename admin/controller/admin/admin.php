<?php
class ControllerAdminAdmin extends Controller {
	private $error = array();

	public function index() {

		$this->load->language('admin/admin');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('admin/admin');

		$this->getList();
	}

	public function add() {
		$this->load->language('admin/admin');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('admin/admin');

		if (($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateForm()) ) {
			$this->model_admin_admin->addAdmin($this->request->post);

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
		

			$this->response->redirect($this->url->link('admin/admin', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('admin/admin');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('admin/admin');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_admin_admin->editAdmin($this->request->get['admin_id'], $this->request->post);

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

			$this->response->redirect($this->url->link('admin/admin', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('admin/admin');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('admin/admin');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $admin_id) {
				$this->model_admin_admin->deleteAdmin($admin_id);
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
			

			$this->response->redirect($this->url->link('admin/admin', 'user_token=' . $this->session->data['user_token'] . $url, true));
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
			'href' => $this->url->link('admin/admin', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['add'] = $this->url->link('admin/admin/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('admin/admin/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$this->load->model('setting/store');
		$stores = $this->model_setting_store->getStores();

		$data['admins'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$admin_total = $this->model_admin_admin->getTotalAdmins($filter_data);


		$results = $this->model_admin_admin->getAdmins($filter_data);

		foreach ($results as $result) {
			$data['admins'][] = array(
				'admin_id'        => $result['admin_id'],
				'name'            => $result['name'],
				'username'        => $result['username'],
				'password'        => $result['password'],
				'position'        => $this->model_admin_admin->getPositionById($result['position']),
				'department'      => $this->model_admin_admin->getDepartmentById($result['department']),
				'status'		  => ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
				'date_added'	  => $result['date_added'],
				'date_modified'	  => $result['date_modified'],
				'edit'            => $this->url->link('admin/admin/edit', 'user_token=' . $this->session->data['user_token'] . '&admin_id=' . $result['admin_id'], true),
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
		
		$data['sort_admin_id'] = $this->url->link('admin/admin', 'user_token=' . $this->session->data['user_token'] . '&sort=admin_id' . $url, true);
		$data['sort_name'] = $this->url->link('admin/admin', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, true);
		$data['sort_username'] = $this->url->link('admin/admin', 'user_token=' . $this->session->data['user_token'] . '&sort=username' . $url, true);
		$data['sort_password'] = $this->url->link('admin/admin', 'user_token=' . $this->session->data['user_token'] . '&sort=password' . $url, true);
		$data['sort_department'] = $this->url->link('admin/admin', 'user_token=' . $this->session->data['user_token'] . '&sort=department' . $url, true);
		$data['sort_position'] = $this->url->link('admin/admin', 'user_token=' . $this->session->data['user_token'] . '&sort=position' . $url, true);
		$data['sort_status'] = $this->url->link('admin/admin', 'user_token=' . $this->session->data['user_token'] . '&sort=status' . $url, true);
		$data['sort_date_added'] = $this->url->link('admin/admin', 'user_token=' . $this->session->data['user_token'] . '&sort=date_added' . $url, true);
		$data['sort_date_modified'] = $this->url->link('admin/admin', 'user_token=' . $this->session->data['user_token'] . '&sort=date_modified' . $url, true);

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		
		$pagination->page = $page;
		$pagination->limit = 10;
		$pagination->url = $this->url->link('admin/admin/history', 'user_token=' . $this->session->data['user_token'] . '&page={page}', true);

		$data['pagination'] = $pagination->render();


		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		// echo '<pre>';
		// print_r($data);
		// echo '</pre>';exit;
		$this->response->setOutput($this->load->view('admin/admin_list', $data));
	}

	protected function getForm() {
		$data['text_form'] = !isset($this->request->get['admin_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

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
			'href' => $this->url->link('admin/admin', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['admin_id'])) {
			$data['action'] = $this->url->link('admin/admin/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('admin/admin/edit', 'user_token=' . $this->session->data['user_token'] . '&admin_id=' . $this->request->get['admin_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('admin/admin', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['admin_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$admin_info = $this->model_admin_admin->getAdmin($this->request->get['admin_id']);
		}
		
		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($admin_info)) {
			$data['name'] = $admin_info['name'];
		} else {
			$data['name'] = '';
		}

		if (isset($this->request->post['username'])) {
			$data['username'] = $this->request->post['username'];
		} elseif (!empty($admin_info)) {
			$data['username'] = $admin_info['username'];
		} else {
			$data['username'] = '';
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($admin_info)) {
			$data['status'] = $admin_info['status'];
		} else {
			$data['status'] = '';
		}

		if (isset($this->request->post['department'])) {
			$data['department'] = $this->request->post['department'];
		} elseif (!empty($admin_info)) {
			$data['department'] = $admin_info['department'];
		} else {
			$data['department'] = '';
		}

		if (isset($this->request->post['position'])) {
			$data['position'] = $this->request->post['position'];
		} elseif (!empty($admin_info)) {
			$data['position'] = $admin_info['position'];
		} else {
			$data['position'] = '';
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

		$this->response->setOutput($this->load->view('admin/admin_form', $data));
	}

	public function validateForm(){
		if (!$this->user->hasPermission('modify', 'admin/admin')) {
			$this->error['warning'] = $this->language->get('error_permission');
        }
        // echo '<pre>';
        // print_r($this->request->post);
        // echo '</pre>';exit;
        // echo strlen(trim($this->request->post['position_name']));exit;


		if ((strlen(trim($this->request->post['name'])) < 1) || (strlen(trim($this->request->post['name'])) > 32))  {
		$this->error['name'] = $this->language->get('error_admin');
		}
		
		if ((strlen(trim($this->request->post['username'])) < 1) || (strlen(trim($this->request->post['username'])) > 32))  {
			$this->error['username'] = $this->language->get('error_admin');
			}

			if ((strlen(trim($this->request->post['password'])) < 1) || (strlen(trim($this->request->post['password'])) > 32))  {
				$this->error['password'] = $this->language->get('error_admin');
				}

        // echo '<pre>';
        // print_r($this->error['position_name']);
        // echo '</pre>';exit;

        return !$this->error;
		
	}

	protected function validateDelete(){
		if(!$this->user->hasPermission('modify', 'admin/admin')){
			$this->error['warning'] = $this->language->get('error_permission');
		}
		return !$this->error;
}
}