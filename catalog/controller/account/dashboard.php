<?php
class ControllerAccountDashboard extends Controller {
	public function index() {
		if (!$this->staff->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/dashboard', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}

		$this->load->language('account/dashboard');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('account/dashboard')
		);

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}


		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		} 
		
		$data['approves'] = array();

		$filter_data = array(
			'sort'  => 'date_added',
			'order' => 'DESC',
			'start' => ($page - 1) * 10,
			'limit' => 10
		);

		$this->load->model('account/form_submitted');

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
			$data['approves'][] = array(
				'applicant_id'    => $result['applicant_id'],
				// 'staff_id'        => $this->model_account_form_submitted->getStaffById($result['staff_id']),
				// 'user_id'         => $this->model_account_form_submitted->getUserById($result['user_id']),
				'eform_id'        => $this->model_account_form_submitted->getEfromById($result['eform_id']),
				// 'reply_id'    	  => $this->model_account_form_submitted->getReplyById($result['reply_id']),
				'department'      => $applicants_info['department'],
				'status'		  => $message,
				'date_submitted'  => date($this->language->get('date_format_short'), strtotime($result['date_submitted'])),
				'date_approved'	  => date($this->language->get('date_format_short'), strtotime($result['date_approved'])),
				'edit'            => $this->url->link('account/form_submitted', '&applicant_id=' . $result['applicant_id'], true),
			);
			// exit(print_r($data['approves']));
		}

		$data['edit'] = $this->url->link('account/edit', '', true);
		$data['profile'] = $this->url->link('account/profile', '', true);
		$data['password'] = $this->url->link('account/password', '', true);

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		
		$this->response->setOutput($this->load->view('account/dashboard', $data));
	}


}
