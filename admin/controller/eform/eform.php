<?php
class ControllerEformEform extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('eform/eform');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('eform/eform');

		$this->getList();

	}

	public function add() {
		$this->load->language('eform/eform');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('eform/eform');


		if (($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateForm()) ) {
			// echo'<pre>';print_r($this->request->post);echo'</pre>';exit;
			$this->model_eform_eform->addEform($this->request->post);
		
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

			$this->response->redirect($this->url->link('eform/eform', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
		
	}

	public function edit() {
		$this->load->language('eform/eform');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('eform/eform');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_eform_eform->editEform($this->request->get['eform_id'], $this->request->post);

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

			$this->response->redirect($this->url->link('eform/eform', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('eform/eform');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('eform/eform');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $eform_id) {
				$this->model_eform_eform->deleteEform($eform_id);
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

			$this->response->redirect($this->url->link('eform/eform', 'user_token=' . $this->session->data['user_token'] . $url, true));
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
			'href' => $this->url->link('eform/eform', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['add'] = $this->url->link('eform/eform/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('eform/eform/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);
		//$data['button'] = $this->url->link('eform/eform/button', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['eforms'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$results = $this->model_eform_eform->getEforms($filter_data);


		foreach ($results as $result) {
			$data['eforms'][] = array(
				'eform_id'        => $result['eform_id'],
				'eform_name'      => $result['eform_name'],
				'status' 		  => ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
				'department' 	  => $this->model_eform_eform->getDepartmentById($result['department']),
				'eform_format'	  => $result['eform_format'],
				'date_added' 	  => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'date_modified'   => date($this->language->get('date_format_short'), strtotime($result['date_modified'])),
				'edit'            =>$this->url->link('eform/eform/edit', 'user_token=' . $this->session->data['user_token'] . '&eform_id=' . $result['eform_id'], true),
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
		
		$data['sort_eform_id'] = $this->url->link('eform/eform', 'user_token=' . $this->session->data['user_token'] . '&sort=eform_id' . $url, true);
		$data['sort_eform_name'] = $this->url->link('eform/eform', 'user_token=' . $this->session->data['user_token'] . '&sort=eform_name' . $url, true);
		$data['sort_department'] = $this->url->link('eform/eform', 'user_token=' . $this->session->data['user_token'] . '&sort=department' . $url, true);
		$data['sort_status'] = $this->url->link('eform/eform', 'user_token=' . $this->session->data['user_token'] . '&sort=status' . $url, true);
		$data['sort_date_added'] = $this->url->link('eform/eform', 'user_token=' . $this->session->data['user_token'] . '&sort=date_added' . $url, true);
		$data['sort_date_modified'] = $this->url->link('eform/eform', 'user_token=' . $this->session->data['user_token'] . '&sort=date_modified' . $url, true);

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		
		$pagination->page = $page;
		$pagination->limit = 10;
		$pagination->url = $this->url->link('eform/eform/history', 'user_token=' . $this->session->data['user_token'] . '&page={page}', true);

		$data['pagination'] = $pagination->render();


		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		// echo '<pre>';
        // print_r($data);
        // echo '</pre>';exit;
		$this->response->setOutput($this->load->view('eform/eform_list', $data));
	}

	protected function getForm() {
		$data['text_form'] = !isset($this->request->get['eform_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['eform_name'])) {
			$data['error_eform_name'] = $this->error['eform_name'];
		} else {
			$data['error_eform_name'] = '';
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
			'href' => $this->url->link('eform/eform', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['eform_id'])) {
			$data['action'] = $this->url->link('eform/eform/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('eform/eform/edit', 'user_token=' . $this->session->data['user_token'] . '&eform_id=' . $this->request->get['eform_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('eform/eform', 'user_token=' . $this->session->data['user_token'] . $url, true);
		
		$data['staff']=$this->model_eform_eform->getStaff();
		$data['admin']=$this->model_eform_eform->getAdmin();

		if (isset($this->request->get['eform_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$eform_info = $this->model_eform_eform->getEform($this->request->get['eform_id']);
			$data['format'] = htmlspecialchars_decode($eform_info['eform_format']);
			$data['staff']=$this->model_eform_eform->getStaff();
			$data['admin']=$this->model_eform_eform->getAdmin();
			
			//echo'<pre>';print_r($data['format']);echo'</pre>';exit;
			$data['eform_id']=$this->request->get['eform_id'];
		}

		if (isset($this->request->post['eform_name'])) {
			$data['eform_name'] = $this->request->post['eform_name'];
		} elseif (!empty($eform_info)) {
			$data['eform_name'] = $eform_info['eform_name'];
		} else {
			$data['eform_name'] = '';
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($eform_info)) {
			$data['status'] = $eform_info['status'];
		} else {
			$data['status'] = true;
		}

		if (isset($this->request->post['department'])) {
			$data['department'] = $this->request->post['department'];
		} elseif (!empty($eform_info)) {
			$data['department'] = $eform_info['department'];
		} else {
			$data['department'] = '';
		}

		if (isset($this->request->post['eform_format'])) {
			$data['eform_format'] = $this->request->post['eform_format'];
		} elseif (!empty($eform_info)) {
			$data['eform_format'] = htmlspecialchars_decode($eform_info['eform_format']);
		} else {
			$data['eform_format'] = '';
		}

		if (isset($this->request->post['approval'])) {
			$data['approval'] = $this->request->post['approval'];
		} elseif (!empty($eform_info)) {
			$data['approval'] = $eform_info['approval'];
		} else {
			$data['approval'] = '';
		}

		$this->load->model('department/department');

		$data['departments'] = $this->model_department_department->getDepartments();
		//    echo '<pre>';
        // print_r($data);
        // echo '</pre>';exit;
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('eform/eform_add', $data));
	}

	public function validateForm(){
		if (!$this->user->hasPermission('modify', 'eform/eform')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if ((strlen(trim($this->request->post['eform_name'])) < 3) || (strlen(trim($this->request->post['eform_name'])) > 32))  {
		$this->error['eform_name'] = $this->language->get('error_eform');
		}
        return !$this->error;
	}

	protected function validateDelete(){
		if(!$this->user->hasPermission('modify', 'eform/eform')){
			$this->error['warning'] = $this->language->get('error_permission');
		}
		return !$this->error;
	}
}
	

	