<?php
class ControllerAccountEformForm extends Controller {

	private $error = array();
	public function index() {
		if (!$this->staff->isLogged()) {
            $this->session->data['redirect'] = $this->url->link('account/eform_form', '', true);
			$this->response->redirect($this->url->link('account/login', '', true));
		}

		$this->load->language('account/eform');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('account/eform');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') ) {
			// $this->model_account_forms->fillForms($this->request->get['eform_id'], $this->request->post);
            $reply_id = $this->model_account_eform->addReply($this->request->post['eform_id'], $this->request->post);
			$this->model_account_eform->addApplicant( $this->request->post,$reply_id);
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

			$this->response->redirect($this->url->link('account/eform_form', '' , true));
		}

		$data['eforms'] = array();

		$filter_data = array(
			
		);

		$results = $this->model_account_eform->getEforms($filter_data);

		foreach ($results as $result) {
			$data['eforms'][] = array(
				'eform_id'        => $result['eform_id'],
				'eform_name'      => $result['eform_name'],
			    // 'department' 	  => $this->model_eform_eform->getDepartmentById($result['department']),
                'status' 		  => ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
				'eform_format'	  => $result['eform_format'],
				'date_added' 	  => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'date_modified'   => date($this->language->get('date_format_short'), strtotime($result['date_modified'])),
				'edit'            => $this->url->link('eform/eform_enabled/edit', '&eform_id=' . $result['eform_id'], true),
			);
		}


		$data['text_form'] = !isset($this->request->get['eform_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['eform_name'])) {
			$data['error_form_name'] = $this->error['eform_name'];
		} else {
			$data['error_form_name'] = array();
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
			'href' => $this->url->link('account/dashboard', '' , true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_department'),
			'href' => $this->url->link('account/department', '' , true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_forms'),
			'href' => $this->url->link('account/eform', '' , true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_formsinfo'),
			'href' => $this->url->link('account/eform_form', '' , true)
		);


		if (!isset($this->request->get['eform_id'])) {
			$data['action'] = $this->url->link('account/eform_form', '' , true);
		} 

		$data['cancel'] = $this->url->link('account/eform', '' , true);
        
        if (isset($this->request->get['eform_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$eform_info = $this->model_account_eform->getEformFormat($this->request->get['eform_id']);
			// $info['reply'] = $this->model_eform_eform_enabled->getReplyById($eforms_info['reply_id']);
			//  echo'<pre>';print_r($eforms_info);echo'</pre>';exit;
			$data['temp'] = htmlspecialchars_decode($eform_info['eform_format']);
			//   echo'<pre>';print_r($eforms_info);echo'</pre>';exit;
			// $data['eform'] = $this->model_account_eform->getEform($this->request->get['eform_id']);
			$data['eform_id']=$this->request->get['eform_id'];
		}

		if (isset($this->request->post['eform_name'])) {
			$data['eform_name'] = $this->request->post['eform_name'];
		} elseif (!empty($eform_info)) {
			$data['eform_name'] = $eform_info['eform_name'];
		} else {
			$data['eform_name'] = '';
		}

		if(isset($this->request->post['approve'])){
			$data['approve']=implode(',',$data['approve']);
		} else{
			$data['approve']="";
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_right');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('account/eform_form', $data));
	}

}