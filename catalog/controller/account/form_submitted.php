<?php
class ControllerAccountFormSubmitted extends Controller {
	public function index() {
		if (!$this->staff->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/form_submitted', 'language=' . $this->config->get('config_language'));

			$this->response->redirect($this->url->link('account/login', 'language=' . $this->config->get('config_language')));
		}

		$this->load->language('account/form_submitted');

		$this->load->model('account/form_submitted');

		$this->load->model('account/form_submitted');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('account/dashboard', 'language=' . $this->config->get('config_language'))
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_form_submitted'),
			'href' => $this->url->link('account/form_submitted', 'language=' . $this->config->get('config_language'))
		);

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['applicants'] = array();

		$filter_data = array(
			'sort'  => 'date_submitted',
			'order' => 'DESC',
			'start' => ($page - 1) * 10,
			'limit' => 10
		);

		// $results = $this->model_account_form_submitted->getFormSubmitted();
		$results= $this->model_account_form_submitted->getApplicants($filter_data);

		foreach ($results as $result) {
			$this->load->model('account/eform');
			$applicants_info = $this->model_account_eform->getEform($result['eform_id']);

			$status=$this->model_account_form_submitted->getApproval($result['applicant_id']);
			$status=explode(',',$status[0]['approval']);
			if($result['status']==(sizeof($status)))
			{$message='Approved';}
			else if($result['status']==-1)
			{$message='Denied';}
			else{$message='Pending';}

				$data['applicants'][] = array(
					'applicant_id' => $result['applicant_id'],
					'eform_id'     => $applicants_info['eform_id'],
					'eform_name'   => $applicants_info['eform_name'],
					'department'   => $applicants_info['department'],
					'status'       => $message,
                    'date_submitted' =>date($this->language->get('date_format_short'), strtotime($result['date_submitted'])),
                    'date_approved'=> date($this->language->get('date_format_short'), strtotime($result['date_approved'])),
					'edit' => $this->url->link('account/form_submitted/forms', '&applicant_id=' . $result['applicant_id'], true),
				);
		}

		$data['continue'] = $this->url->link('account/dashboard', 'language=' . $this->config->get('config_language'));

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('account/form_submitted_list', $data));

	}

	public function edit() {
		$this->load->language('account/form_submitted');
		
		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('account/form_submitted');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$applicant_info = $this->model_account_form_submitted->getApplicant($this->request->post['applicant_id']);
			// $info['reply'] = $this->model_applicant_applicant->getReplyById($applicant_info['reply_id']);
			
			$this->model_account_form_submitted->editApplicant($this->request->get['applicant_id'], $this->request->post);

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

			$this->response->redirect($this->url->link('applicant/applicant', 'user_token=', true));
		}

		// $this->getForm();
	}

	public function forms(){
		$this->load->model('account/form_submitted');

		$this->load->language('account/form_submitted');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('account/dashboard', 'language=' . $this->config->get('config_language'))
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_form_submitted'),
			'href' => $this->url->link('account/form_submitted', 'language=' . $this->config->get('config_language'))
		);

		if (!isset($this->request->get['applicant_id'])) {
			$data['action'] = $this->url->link('account/form_submitted', '' , true);
		}  else {
			$data['action'] = $this->url->link('account/form_submitted/edit', '' . '&applicant_id=' . $this->request->get['applicant_id'] . true);
		}


		$data['cancel'] = $this->url->link('account/form_submitted', '' , true);

		if (isset($this->request->get['applicant_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$applicant_info = $this->model_account_form_submitted->getApplicant($this->request->get['applicant_id']);
			$info['reply'] = $this->model_account_form_submitted->getReplyById($applicant_info['reply_id']);
			$name['name'] = $this->model_account_form_submitted->getEfromById($applicant_info['eform_id']);
			$data['test'] = htmlspecialchars_decode($info['reply']);

			$arr= json_decode($data['test'],true);
			foreach($arr as $key => $value){
				$data['temp'][] = array(
					'label' => $key,
					'content' => $value
				);
			}
			//    echo'<pre>';print_r($name['name']);echo'</pre>';exit;
			$data['applicant_id']=$this->request->get['applicant_id'];

			
			
		$data['continue'] = $this->url->link('account/dashboard', 'language=' . $this->config->get('config_language'));

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('account/form_submitted_info', $data));
		}
	}
}

	

	
