<?php
class ControllerDesignTranslation extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('design/translation');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('design/translation');

		$this->getList();
	}

	public function add() {
		$this->load->language('design/translation');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('design/translation');

		if (($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateForm()) ) {
			$this->model_design_translation->addAdmin($this->request->post);

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

			$this->response->redirect($this->url->link('design/translation', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('design/translation');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('design/translation');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_design_translation->editAdmin($this->request->get['adminID'], $this->request->post);

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

			$this->response->redirect($this->url->link('design/translation', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('design/translation');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('design/translation');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $adminID) {
				$this->model_design_translation->deleteAdmin($adminID);
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

			$this->response->redirect($this->url->link('design/translation', 'user_token=' . $this->session->data['user_token'] . $url, true));
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
			'href' => $this->url->link('design/translation', 'user_token=' . $this->session->data['user_token'], true)
		);

		$this->load->model('localisation/language');

		//buttons
		$data['add'] = $this->url->link('design/translation/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('design/translation/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['translations'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		

		$results = $this->model_design_translation->getAdmin($filter_data);

		foreach ($results as $result) {
			$data['translations'][] = array(
				'adminID'        => $result['adminID'],
				'Name'           => $result['Name'],
				'ID'          	 => $result['ID'],
				'Position'       => $result['Position'],
				'Department'     => $result['Department'],
				'edit'           => $this->url->link('design/translation/edit', 'user_token=' . $this->session->data['user_token'] . '&adminID=' . $result['adminID'], true),
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
		
		$data['sort_admin_id'] = $this->url->link('design/translation', 'user_token=' . $this->session->data['user_token'] . '&sort=admin_id' . $url, true);
		$data['sort_admin_name'] = $this->url->link('design/translation', 'user_token=' . $this->session->data['user_token'] . '&sort=admin_name' . $url, true);
		$data['sort_username'] = $this->url->link('design/translation', 'user_token=' . $this->session->data['user_token'] . '&sort=username' . $url, true);
		$data['sort_password'] = $this->url->link('design/translation', 'user_token=' . $this->session->data['user_token'] . '&sort=password' . $url, true);
		$data['sort_department'] = $this->url->link('design/translation', 'user_token=' . $this->session->data['user_token'] . '&sort=department' . $url, true);
		$data['sort_position'] = $this->url->link('design/translation', 'user_token=' . $this->session->data['user_token'] . '&sort=position' . $url, true);
		$data['sort_status'] = $this->url->link('design/translation', 'user_token=' . $this->session->data['user_token'] . '&sort=status' . $url, true);
		$data['sort_date_added'] = $this->url->link('design/translation', 'user_token=' . $this->session->data['user_token'] . '&sort=date_added' . $url, true);
		$data['sort_date_modified'] = $this->url->link('design/translation', 'user_token=' . $this->session->data['user_token'] . '&sort=date_modified' . $url, true);

		$pagination = new Pagination();
		
		$pagination->page = $page;
		$pagination->limit = 10;
		$pagination->url = $this->url->link('design/translation/history', 'user_token=' . $this->session->data['user_token'] . '&page={page}', true);

		$data['pagination'] = $pagination->render();


		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('design/translation_list', $data));
	}

	protected function validateDelete(){
			if(!$this->user->hasPermission('modify', 'design/translation')){
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
			'href' => $this->url->link('design/translation', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		

		$data['cancel'] = $this->url->link('design/translation', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->request->get['adminID']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$translation_info = $this->model_design_translation->getAdmin($this->request->get['adminID']);
		}

		

		
		


		

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('design/translation_form', $data));
	}


/*
	public function path() {
		$this->load->language('design/translation');

		$json = array();

		if (isset($this->request->get['language_id'])) {
			$language_id = $this->request->get['language_id'];
		} else {
			$language_id = 0;
		}

		$this->load->model('localisation/language');

		$language_info = $this->model_localisation_language->getLanguage($language_id);

		if (!empty($language_info)) {
			$path = glob(DIR_CATALOG . 'language/'.$language_info['code'].'/*');

			while (count($path) != 0) {
				$next = array_shift($path);

				foreach ((array)glob($next) as $file) {
					if (is_dir($file)) {
						$path[] = $file . '/*';
					}

					if (substr($file, -4) == '.php') {
						$json[] = substr(substr($file, strlen(DIR_CATALOG . 'language/'.$language_info['code'].'/')), 0, -4);
					}
				}
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function translation() {
		$this->load->language('design/translation');

		$json = array();

		if (isset($this->request->get['store_id'])) {
			$store_id = $this->request->get['store_id'];
		} else {
			$store_id = 0;
		}

		if (isset($this->request->get['language_id'])) {
			$language_id = $this->request->get['language_id'];
		} else {
			$language_id = 0;
		}

		if (isset($this->request->get['path'])) {
			$route = $this->request->get['path'];
		} else {
			$route = '';
		}

		$this->load->model('localisation/language');

		$language_info = $this->model_localisation_language->getLanguage($language_id);

		$directory = DIR_CATALOG . 'language/';

		if ($language_info && is_file($directory . $language_info['code'] . '/' . $route . '.php') && substr(str_replace('\\', '/', realpath($directory . $language_info['code'] . '/' . $route . '.php')), 0, strlen($directory)) == str_replace('\\', '/', $directory)) {
			$_ = array();

			include($directory . $language_info['code'] . '/' . $route . '.php');

			foreach ($_ as $key => $value) {
				$json[] = array(
					'key'   => $key,
					'value' => $value
				);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	*/
}
