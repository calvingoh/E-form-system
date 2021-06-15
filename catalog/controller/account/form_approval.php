<?php
class ControllerAccountFormApproval extends Controller {
	public function index() {
		if (!$this->staff->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/form_approval', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}

		$this->load->language('account/form_approval');

		$this->load->model('account/form_approval');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/dashboard', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('account/form_approval')
		);

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		// $results = $this->model_account_form_approval->getApplicants($filter_data);
        $this->load->model('account/staff');
		$staff=$this->model_account_staff->getStaff($this->staff->getId());
		$results=$this->model_account_form_approval->getStaffApplicantsPending($staff['staff_name']);
        
        if(!empty($results)){
        foreach ($results as $result) {
            $status=$this->model_account_form_approval->getApproval($result['applicant_id']);
			$status=explode(',',$status[0]['approval']);
			if($result['status']==(sizeof($status)))
			{$message='Approved';}
			else if($result['status']==-1)
			{$message='Denied';}
            else{$message='Pending';}
            
				$data['approves'][]  = array(
					'applicant_id' => $result['applicant_id'],
					'user_id'      => $this->model_account_form_approval->getUserById($result['user_id']),
					'staff_id'     => $this->model_account_form_approval->getStaffById($result['staff_id']),
					'eform_id'     => $this->model_account_form_approval->getEfromById($result['eform_id']),
					// 'department'   => $this->model_account_form_approval->getDepartmentById($result['department']),
					'status'       => $message,
                    'date_submitted' =>date($this->language->get('date_format_short'), strtotime($result['date_submitted'])),
                    'date_approved'=> date($this->language->get('date_format_short'), strtotime($result['date_approved'])),
					'edit'         => $this->url->link('account/form_approval/display', 'applicant_id=' . $result['applicant_id']),
					
				);
        }
	}
	
		$data['continue'] = $this->url->link('account/dashboard', '', true);

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('account/form_approval_list', $data));
	}

    public function edit() {
		$this->load->language('account/form_approval');
		
		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('account/form_approval');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$applicant_info = $this->model_form_approval->getApplicant($this->request->post['applicant_id']);
			$info['reply'] = $this->model_form_approval->getReplyById($applicant_info['reply_id']);
			
			if(isset($this->request->post['approve'])){
				$this->model_form_approval->editFormApproval($this->request->get['applicant_id'], $this->request->post);
			}
			else if(isset($this->request->post['reject'])){
				$this->model_form_approval->editApplicantReject($this->request->get['applicant_id']);
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

			$this->model_account_form_approval->addEmail();
			$this->response->redirect($this->url->link('account/form_approval', '', true));

			$this->response->redirect($this->url->link('account/form_approval', $url, true));
		}

		$this->display();
    }
    
    public function display(){
		$this->load->model('account/form_approval');

		$this->load->language('account/form_approval');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('account/dashboard', 'language=' . $this->config->get('config_language'))
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('form_approval'),
			'href' => $this->url->link('account/form_approval', 'language=' . $this->config->get('config_language'))
		);

		if (!isset($this->request->get['applicant_id'])) {
			$data['action'] = $this->url->link('account/form_approval', '' , true);
		}  else {
			$data['action'] = $this->url->link('account/form_approval/edit', '' . '&applicant_id=' . $this->request->get['applicant_id'] . true);
		}

		$data['cancel'] = $this->url->link('account/form_approval', '' , true);

		if (isset($this->request->get['applicant_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$data['applicant_id']=$this->request->get['applicant_id'];
			$applicant_info = $this->model_account_form_approval->getApplicant($this->request->get['applicant_id']);
			$info['reply'] = $this->model_account_form_approval->getReplyById($applicant_info['reply_id']);
			$name['name'] = $this->model_account_form_approval->getEfromById($applicant_info['eform_id']);
			$data['test'] = htmlspecialchars_decode($info['reply']);

			$arr= json_decode($data['test'],true);
			foreach($arr as $key => $value){
				$data['temp'][] = array(
					'label' => $key,
					'content' => $value
				);
			}
			//    echo'<pre>';print_r($name['name'] );echo'</pre>';exit;
			$data['applicant_id']=$this->request->get['applicant_id'];

		// 	$mail_from = $this->model_account_form_approval->getFormApproval($applicant_id)
		// 	$mail_to = $this->model_account_form_approval->getApplicant($applicant_id);
		// if($mail_from){
		// 	$data['email'] = $mail_from['email'];
		// }
		// if($mail_to){
		// 	$data['email'] = $mail_from['email'];
		// }
		// 	$data['text_subject'] = $subject;
		// 	$data['text_body'] = $body;

		// 	$this->model_account_from_approval->addEmail($mail_from, $mail_to, $subject, $body);

		// 	$this->response->redirect($this->url->link('account/form_approval', '', true));
			
		$data['continue'] = $this->url->link('account/dashboard', 'language=' . $this->config->get('config_language'));

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('account/form_approval_info', $data));
		}
	}
	
	public function approve(){
		$this->load->model('account/form_approval');
	
		if(isset($this->request->get['applicant_id'])){
			$this->model_account_form_approval->editFormApproval($this->request->get['applicant_id']);
			$this->model_account_form_approval->addEmail($this->request->get['applicant_id']);
		}
	}

	public function reject(){
		$this->load->model('account/form_approval');
		if(isset($this->request->get['applicant_id'])){
			$this->model_account_form_approval->editApplicantReject($this->request->get['applicant_id']);
		}
	}
}
