<?php
class ControllerAccountEform extends Controller {
    private $error = array();
public function index(){
    
		
		if (!$this->staff->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/department/eform', '', true);
			$this->response->redirect($this->url->link('account/login', '', true));
		}

		$this->load->language('account/eform');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('account/account')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_department'),
			'href' => $this->url->link('account/department', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_forms'),
			'href' => $this->url->link('account/eform', '', true)
		);

		$this->load->model('account/eform');
	
		
		$data['column_amount'] = sprintf($this->language->get('column_amount'), $this->config->get('config_currency'));

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['eforms'] = array();

		$filter_data = array(
			'sort'  => 'date_added',
			'order' => 'DESC',
			'start' => ($page - 1) * 10,
			'limit' => 10
		);

        // $eform_total = $this->model_account_eform->getTotalEform();
		$results = $this->model_account_eform->getEformsByDepartment($this->request->get['department_id']);
		foreach ($results as $result) {
			$data['eforms'][] = array(
				'eform_id'    =>$result['eform_id'], 
				'eform_name'  => $result['eform_name'],
				'department'  => $result['department'], 
				'edit'  => $this->url->link('account/eform_form','eform_id='.$result['eform_id'],true)
			);
		}
		$pagination = new Pagination();
		// $pagination->total = $eform_total;
		$pagination->page = $page;
		$pagination->limit = 10;
		$pagination->url = $this->url->link('account/department/eform', 'page={page}', true);

		$data['pagination'] = $pagination->render();

		// $data['results'] = sprintf($this->language->get('text_pagination'), ($eform_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($eform_total - 10)) ? $eform_total : ((($page - 1) * 10) + 10), $eform_total, ceil($eform_total / 10));

		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('account/eform_list', $data));
    }
}