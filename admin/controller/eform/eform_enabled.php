<?php
class ControllerEformEformEnabled extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('eform/eform');

		$this->document->setTitle($this->language->get('heading'));

		$this->load->model('eform/eform');

		$this->getList();

	}
	
	public function add() {
		$this->load->language('eform/eform');

		$this->document->setTitle($this->language->get('heading'));

		$this->load->model('eform/eform_enabled');

		if (($this->request->server['REQUEST_METHOD'] == 'POST' ) ) {
			// echo'<pre>';print_r($this->request->post);echo'</pre>';exit;
			$reply_id=$this->model_eform_eform_enabled->addReply($this->request->post['eform_id'], $this->request->post);
			$this->model_eform_eform_enabled->addApplicant( $this->request->post,$reply_id);
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

			$this->response->redirect($this->url->link('eform/eform_enabled', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
		
	}

    public function edit() {
		$this->load->language('eform/eform');

		$this->document->setTitle($this->language->get('heading'));

		$this->load->model('eform/eform_enabled');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') ) {
			$this->model_eform_eform_enabled->editReply($this->request->post['eform_id'], $this->request->post);

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

			$this->response->redirect($this->url->link('eform/eform_enabled', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
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
			'text' => $this->language->get('heading'),
			'href' => $this->url->link('eform/eform_enabled', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['add'] = $this->url->link('eform/eform_enabled/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('eform/eform_enabled/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);
		//$data['button'] = $this->url->link('eform/eform/button', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['eforms'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$results = $this->model_eform_eform->getEformsByStatus($filter_data);

		foreach ($results as $result) {
			$data['eforms'][] = array(
				'eform_id'        => $result['eform_id'],
				'eform_name'      => $result['eform_name'],
			    'department' 	  => $this->model_eform_eform->getDepartmentById($result['department']),
                'status' 		  => ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
				'eform_format'	  => $result['eform_format'],
				'date_added' 	  => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'date_modified'   => date($this->language->get('date_format_short'), strtotime($result['date_modified'])),
				'edit'            => $this->url->link('eform/eform_enabled/edit', 'user_token=' . $this->session->data['user_token'] . '&eform_id=' . $result['eform_id'], true),
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

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		
		$pagination->page = $page;
		$pagination->limit = 10;
		$pagination->url = $this->url->link('eform/eform_enabled/history', 'user_token=' . $this->session->data['user_token'] . '&page={page}', true);

		$data['pagination'] = $pagination->render();


		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        
        $this->response->setOutput($this->load->view('eform/eform_enabled_list', $data));
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
			'text' => $this->language->get('heading'),
			'href' => $this->url->link('eform/eform_enabled', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['eform_id'])) {
			$data['action'] = $this->url->link('eform/eform_enabled/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('eform/eform_enabled/edit', 'user_token=' . $this->session->data['user_token'] . '&eform_id=' . $this->request->get['eform_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('eform/eform_enabled', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['eform_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$eforms_info = $this->model_eform_eform_enabled->getEform($this->request->get['eform_id']);
			// $info['reply'] = $this->model_eform_eform_enabled->getReplyById($eforms_info['reply_id']);
			//  echo'<pre>';print_r($eforms_info);echo'</pre>';exit;
			$data['temp'] = htmlspecialchars_decode($eforms_info['eform_format']);
			//   echo'<pre>';print_r($eforms_info);echo'</pre>';exit;
			$data['eform_id']=$this->request->get['eform_id'];
		}

		if (isset($this->request->post['eform_name'])) {
			$data['eform_name'] = $this->request->post['eform_name'];
		} elseif (!empty($eforms_info)) {
			$data['eform_name'] = $eforms_info['eform_name'];
		} else {
			$data['eform_name'] = '';
        }
        
		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($eforms_info)) {
			$data['status'] = $eforms_info['status'];
		} else {
			$data['status'] = '';
        }
        
		if (isset($this->request->post['eform_format'])) {
		$data['eform_format'] = $this->request->post['eform_format'];
		} elseif (!empty($eforms_info)) {
			$data['eform_format'] = htmlspecialchars_decode($eforms_info['eform_format']);
		} else {
			$data['eform_format'] = '';
		}

		if(isset($this->request->post['approve'])){
			$data['approve']=implode(',',$data['approve']);
		} else{
			$data['approve']="";
		}

		if (isset($this->request->post['remarks'])) {
			$data['remarks'] = $this->request->post['remarks'];
		} elseif (!empty($eforms_info)) {
			$data['remarks'] = $eforms_info['remarks'];
		} else {
			$data['remarks'] = '';
        }


		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$data['user_token']=$this->session->data['user_token'];
		
		$this->response->setOutput($this->load->view('eform/eform_enabled_form', $data));
	}
}