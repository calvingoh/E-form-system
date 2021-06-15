<?php
class ControllerApplicantApplicant extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('applicant/applicant');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('applicant/applicant');

		$this->getList();
	}

	public function add() {
		$this->load->language('applicant/applicant');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('applicant/applicant');

		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$applicant_info = $this->model_applicant_applicant->getApplicant($this->request->post['applicant_id']);
			$this->model_applicant_applicant->addApplicant($this->request->post);
			// $info['reply'] = $this->model_applicant_applicant->getReplyById($applicant_info['reply_id']);
			// $email['email']= $this->model_appliacant_applicant->addEmail($applicant_info['email_cron_id']);
			// $this->model_eform_eform_enabled->addReply($this->request->post['eform_id'], $this->request->post);
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

			$this->response->redirect($this->url->link('applicant/applicant', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('applicant/applicant');
		
		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('applicant/applicant');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$applicant_info = $this->model_applicant_applicant->getApplicant($this->request->post['applicant_id']);
			$info['reply'] = $this->model_applicant_applicant->getReplyById($applicant_info['reply_id']);
			if(isset($this->request->post['approve'])){
				$this->model_applicant_applicant->editApplicant($this->request->get['applicant_id'], $this->request->post);
			}
			else if(isset($this->request->post['reject'])){
				$this->model_applicant_applicant->editApplicantReject($this->request->get['applicant_id']);
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

			$this->response->redirect($this->url->link('applicant/applicant', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('applicant/applicant');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('applicant/applicant');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $applicant_id) {
				$this->model_applicant_applicant->deleteApplicant($applicant_id);
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

			$this->response->redirect($this->url->link('applicant/applicant', 'user_token=' . $this->session->data['user_token'] . $url, true));
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
			'href' => $this->url->link('applicant/applicant', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['add'] = $this->url->link('applicant/applicant/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('applicant/applicant/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['applicants'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$results = $this->model_applicant_applicant->getApplicants($filter_data);
		$this->load->model('user/user');
		$user=$this->model_user_user->getUser($this->user->getId());
		$list=$this->model_applicant_applicant->getApplicantsPending($user['firstname']);
		// $list=$this->model_applicant_applicant->getRejected($user['firstname']);
		if(!empty($list)){
			foreach ($list as $list) {
			$status=$this->model_applicant_applicant->getApproval($list['applicant_id']);
			$status=explode(',',$status[0]['approval']);
			if($list['status']==(sizeof($status)))
			{$message='Approved';}
			else if($list['status']==-1)
			{$message='Denied';}
			else{$message='Pending';}
				
				$data['list'][] = array(
					'applicant_id'    => $list['applicant_id'],
					'staff_id'        => $this->model_applicant_applicant->getStaffById($list['staff_id']),
					'user_id'         => $this->model_applicant_applicant->getUserById($list['user_id']),
					'eform_id'        => $this->model_applicant_applicant->getEfromById($list['eform_id']),
					'reply_id'    	  => $this->model_applicant_applicant->getReplyById($list['reply_id']),
					'department'      => $this->model_applicant_applicant->getDepartmentById($list['department']),
					'status'		  => $message,
					'date_submitted'  => date($this->language->get('date_format_short'), strtotime($list['date_submitted'])),
					'date_approved'	  => date($this->language->get('date_format_short'), strtotime($list['date_approved'])),
					'edit'            => $this->url->link('applicant/applicant/edit', 'user_token=' . $this->session->data['user_token'] . '&applicant_id=' . $list['applicant_id'], true),
				);
			}
		}
		

		// echo'<pre>';print_r($data['applicants']);echo'</pre>';exit;
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
		
		$data['sort_applicant_id'] = $this->url->link('applicant/applicant', 'user_token=' . $this->session->data['user_token'] . '&sort=applicant_id' . $url, true);
		$data['sort_applicant_name'] = $this->url->link('applicant/applicant', 'user_token=' . $this->session->data['user_token'] . '&sort=applicant_name' . $url, true);
		$data['sort_staff_id'] = $this->url->link('applicant/applicant', 'user_token=' . $this->session->data['user_token'] . '&sort=staff_id' . $url, true);
		$data['sort_user_id'] = $this->url->link('applicant/applicant', 'user_token=' . $this->session->data['user_token'] . '&sort=user_id' . $url, true);
		$data['sort_eform_id'] = $this->url->link('applicant/applicant', 'user_token=' . $this->session->data['user_token'] . '&sort=eform_id' . $url, true);
		$data['sort_department'] = $this->url->link('applicant/applicant', 'user_token=' . $this->session->data['user_token'] . '&sort=department' . $url, true);
		$data['sort_reply_id'] = $this->url->link('applicant/applicant', 'user_token=' . $this->session->data['user_token'] . '&sort=reply_id' . $url, true);
		$data['sort_status'] = $this->url->link('applicant/applicant', 'user_token=' . $this->session->data['user_token'] . '&sort=status' . $url, true);
		$data['sort_date_submitted'] = $this->url->link('applicant/applicant', 'user_token=' . $this->session->data['user_token'] . '&sort=date_submitted' . $url, true);
		$data['sort_date_approvrd'] = $this->url->link('applicant/applicant', 'user_token=' . $this->session->data['user_token'] . '&sort=date_approved' . $url, true);

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		
		$pagination->page = $page;
		$pagination->limit = 10;
		$pagination->url = $this->url->link('applicant/applicant/history', 'user_token=' . $this->session->data['user_token'] . '&page={page}', true);

		$data['pagination'] = $pagination->render();


		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('applicant/applicant_list', $data));
	}

	protected function getForm() {
		$data['text_form'] = !isset($this->request->get['applicant_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

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
			'href' => $this->url->link('applicant/applicant', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['applicant_id'])) {
			$data['action'] = $this->url->link('applicant/applicant/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('applicant/applicant/edit', 'user_token=' . $this->session->data['user_token'] . '&applicant_id=' . $this->request->get['applicant_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('applicant/applicant', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['applicant_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			// $data['staff']=$this->model_applicant_applicant->getStaff();
			// $data['admin']=$this->model_applicant_applicant->getAdmin();
			$data['applicant_id']=$this->request->get['applicant_id'];
			$applicant_info = $this->model_applicant_applicant->getApplicant($this->request->get['applicant_id']);
			$info['reply'] = $this->model_applicant_applicant->getReplyById($applicant_info['reply_id']);
			$name['name'] = $this->model_applicant_applicant->getEfromById($applicant_info['eform_id']);
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
		}

		if (isset($this->request->post['remarks'])) {
			$data['remarks'] = $this->request->post['remarks'];
		} elseif (!empty($applicant_info)) {
			$data['remarks'] = $applicant_info['remarks'];
		} else {
			$data['remarks'] = '';
		}
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('applicant/applicant_info', $data));
	
	}

	protected function validateDelete(){
		if(!$this->user->hasPermission('modify', 'applicant/applicant')){
			$this->error['warning'] = $this->language->get('error_permission');
		}
		return !$this->error;
	}

	// public function generateEmail(){
	// 	$applicant_id = $this->model_applicant_applicant->getApplicant();
	// 	$user_id = $this->model_applicant_applicant->getUser();

	// 	$mail = new Mail($this->config->get('config_mail_engine'));
	// 	$mail->parameter = $this->config->get('config_mail_parameter');
	// 	$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
	// 	$mail->smtp_port = $this->config->get('config_mail_smtp_port');
	// 	$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

	// 	$mail->setTo($applicant_id['email']);
	// 	$mail->setFrom($this->config->get('config_email'));
	// 	$mail->setSender($user_id);
	// 	$mail->setText($this->load->view('applicant/applicant_info'));
	// 	$mail->send();

	// }
}