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
			$this->model_admin_admin->editAdmin($this->request->get['adminID'], $this->request->post);

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
			foreach ($this->request->post['selected'] as $adminID) {
				$this->model_admin_admin->deleteAdmin($adminID);
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

		$this->load->model('localisation/language');

		//buttons
		$data['add'] = $this->url->link('admin/admin/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('admin/admin/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['admins'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		

		$results = $this->model_admin_admin->getAdmin($filter_data);

		foreach ($results as $result) {
			$data['admins'][] = array(
				'adminID'        => $result['adminID'],
				'Name'           => $result['Name'],
				'ID'          	 => $result['ID'],
				'Position'       => $result['Position'],
				'Department'     => $result['Department'],
				'edit'           => $this->url->link('admin/admin/edit', 'user_token=' . $this->session->data['user_token'] . '&adminID=' . $result['adminID'], true),
			);
		}

		$data['user_token'] = $this->session->data['user_token'];

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

		if (isset($this->request->post['entry_id'])) {
			$data['entry_id'] = $this->request->post['entry_id'];
		} else {
			$data['entry_id'] = $this->config->get('entry_id');
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
		$data['sort_admin_name'] = $this->url->link('admin/admin', 'user_token=' . $this->session->data['user_token'] . '&sort=admin_name' . $url, true);
		$data['sort_username'] = $this->url->link('admin/admin', 'user_token=' . $this->session->data['user_token'] . '&sort=username' . $url, true);
		$data['sort_password'] = $this->url->link('admin/admin', 'user_token=' . $this->session->data['user_token'] . '&sort=password' . $url, true);
		$data['sort_department'] = $this->url->link('admin/admin', 'user_token=' . $this->session->data['user_token'] . '&sort=department' . $url, true);
		$data['sort_position'] = $this->url->link('admin/admin', 'user_token=' . $this->session->data['user_token'] . '&sort=position' . $url, true);
		$data['sort_status'] = $this->url->link('admin/admin', 'user_token=' . $this->session->data['user_token'] . '&sort=status' . $url, true);
		$data['sort_date_added'] = $this->url->link('admin/admin', 'user_token=' . $this->session->data['user_token'] . '&sort=date_added' . $url, true);
		$data['sort_date_modified'] = $this->url->link('admin/admin', 'user_token=' . $this->session->data['user_token'] . '&sort=date_modified' . $url, true);

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

		$this->response->setOutput($this->load->view('admin/admin_list', $data));
	}

	protected function validateDelete(){
			if(!$this->user->hasPermission('modify', 'admin/admin')){
				$this->error['warning'] = $this->language->get('error_permission');
			}
			return !$this->error;
	}

	public function validateForm(){
			if (strlen($this->request->post['entry_name']) <= 3) {
				$this->error['warning'] = $this->language->get('error_key');
			}
	
			if ($this->error) {
				return false;
			} else {
				return true;
			}
		
	}

	protected function getForm() {
		$data['text_form'] = !isset($this->request->get['admin_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
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

		

		$data['cancel'] = $this->url->link('admin/admin', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->request->get['adminID']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$admin_info = $this->model_admin_admin->getAdmin($this->request->get['adminID']);
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('admin/admin_form', $data));
	}
}