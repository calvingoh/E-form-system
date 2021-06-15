<?php
class ControllerDepartmentDepartment extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('department/department');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('department/department');

		$this->getList();
	}

	public function add() {
		$this->load->language('department/department');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('department/department');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_department_department->addDepartment($this->request->post);

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

			$this->response->redirect($this->url->link('department/department', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	

	public function edit() {
		$this->load->language('department/department');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('department/department');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_department_department->editDepartment($this->request->get['department_id'], $this->request->post);

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

			$this->response->redirect($this->url->link('department/department', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	
	public function delete() {
		$this->load->language('department/department');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('department/department');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $department_id) {
				$this->model_department_department->deleteDepartment($department_id);
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

			$this->response->redirect($this->url->link('department/department', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'department_name';
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

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('department/department', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('department/department/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('department/department/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['departments'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$department_total = $this->model_department_department->getTotalDepartment();

		$results = $this->model_department_department->getDepartments($filter_data);

		foreach ($results as $result) {
			$data['departments'][] = array(
				'department_id' 		 => $result['department_id'],
				'department_name'        => $result['department_name'],
				'edit'       			 => $this->url->link('department/department/edit', 'user_token=' . $this->session->data['user_token'] . '&department_id=' . $result['department_id'] . $url, true)
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

		$data['sort_department_name'] = $this->url->link('department/department', 'user_token=' . $this->session->data['user_token'] . '&sort=dd.department_name' . $url, true);
		$data['sort_date_added'] = $this->url->link('department/department', 'user_token=' . $this->session->data['user_token'] . '&sort=d.date_added' . $url, true);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $department_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('department/department', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($department_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($department_total - $this->config->get('config_limit_admin'))) ? $department_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $department_total, ceil($department_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('department/department_list', $data));
	}

	protected function getForm() {
		$data['text_form'] = !isset($this->request->get['department_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['department_name'])) {
			$data['error_department_name'] = $this->error['department_name'];
		} else {
			$data['error_department_name'] = array();
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

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('department/department', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['department_id'])) {
			$data['action'] = $this->url->link('department/department/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('department/department/edit', 'user_token=' . $this->session->data['user_token'] . '&department_id=' . $this->request->get['department_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('department/department', 'user_token=' . $this->session->data['user_token'] . $url, true);


		if (isset($this->request->get['department_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$department_info = $this->model_department_department->getDepartment($this->request->get['department_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->request->post['department_id'])) {
			$data['department_id'] = $this->request->post['department_id'];
		} elseif (!empty($department_info)) {
			$data['department_id'] = $department_info['department_id'];
		} else {
			$data['department_id'] = '';
		}

		if (isset($this->request->post['department_name'])) {
			$data['department_name'] = $this->request->post['department_name'];
		} elseif (!empty($department_info)) {
			$data['department_name'] = $department_info['department_name'];
		} else {
			$data['department_name'] = '';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('department/department_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'department/department')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['department_name']) < 1) || (utf8_strlen($this->request->post['department_name']) > 64)) {
			$this->error['department_name'] = $this->language->get('error_department_name');
		}
	
		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'department/department')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		foreach ($this->request->post['selected'] as $department_id) {
			if ($this->user->getId() == $department_id) {
				$this->error['warning'] = $this->language->get('error_department');
			}
		}

		return !$this->error;}
	
}