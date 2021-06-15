<?php
class ControllerAccountStaff extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('account/staff');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('account/staff');

		if (!$this->staff->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/staff', '', true);
			$this->response->redirect($this->url->link('account/login', '', true));
		}

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$staff_data = array_merge($this->request->post, array(
				'department' => $this->staff->getDepartmentById(),
				'position' => $this->staff->getPositionById(),
			));
			
			$this->model_account_staff->getStaff($this->staff->getId(), $staff_data);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('account/staff', '', true));
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['staff_name'])) {
			$data['error_staff_name'] = $this->error['staff_name'];
		} else {
			$data['error_staff_name'] = '';
		}

		if (isset($this->error['email'])) {
			$data['error_email'] = $this->error['email'];
		} else {
			$data['error_email'] = '';
		}


		if (isset($this->error['department'])) {
			$data['error_department'] = $this->error['department_name'];
		} else {
			$data['error_department'] = '';
		}

		if (isset($this->error['position'])) {
			$data['error_position'] = $this->error['position'];
		} else {
			$data['error_position'] = '';
		}

		// if (isset($this->error['email'])) {
		// 	$data['error_email'] = $this->error['email'];
		// } else {
		// 	$data['error_email'] = '';
		// }

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('account/dashboard', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('account/staff', '', true)
		);

		$data['action'] = $this->url->link('account/staff', '', true);

		$data['cancel'] = $this->url->link('account/dashboard', '', true);

		if ($this->request->server['REQUEST_METHOD'] != 'POST') {
			$staff_info = $this->model_account_staff->getStaff($this->staff->getId());
		}

		if (isset($this->request->post['staff_name'])) {
			$data['staff_name'] = $this->request->post['staff_name'];
		} elseif (!empty($staff_info)) {
			$data['staff_name'] = $staff_info['staff_name'];
		} else {
			$data['staff_name'] = '';
		}

		if (isset($this->request->post['email'])) {
			$data['email'] = $this->request->post['email'];
		} elseif (!empty($staff_info)) {
			$data['email'] = $staff_info['email'];
		} else {
			$data['email'] = '';
		}


		if (isset($this->request->post['department'])) {
			$data['department'] =$this->request->post['department'];
		} elseif (!empty($staff_info)) {
			$data['department'] = $this->model_account_staff->getDepartmentById( $staff_info['department']);
		} else {
			$data['department'] = '';
		}

		if (isset($this->request->post['position'])) {
			$data['position'] = $this->request->post['position'];
		} elseif (!empty($staff_info)) {
			$data['position'] =$this->model_account_staff->getPositionById( $staff_info['position']);
		} else {
			$data['position'] = '';
		}

		

		// if (isset($this->request->post['lastname'])) {
		// 	$data['lastname'] = $this->request->post['lastname'];
		// } elseif (!empty($user_info)) {
		// 	$data['lastname'] = $user_info['lastname'];
		// } else {
		// 	$data['lastname'] = '';
		// }

		// if (isset($this->request->post['email'])) {
		// 	$data['email'] = $this->request->post['email'];
		// } elseif (!empty($user_info)) {
		// 	$data['email'] = $user_info['email'];
		// } else {
		// 	$data['email'] = '';
		// }

		// if (isset($this->request->post['image'])) {
		// 	$data['image'] = $this->request->post['image'];
		// } elseif (!empty($user_info)) {
		// 	$data['image'] = $user_info['image'];
		// } else {
		// 	$data['image'] = '';
		// }

		// $this->load->model('tool/image');

		// if (isset($this->request->post['image']) && is_file(DIR_IMAGE . $this->request->post['image'])) {
		// 	$data['thumb'] = $this->model_tool_image->resize($this->request->post['image'], 100, 100);
		// } elseif (!empty($user_info) && $user_info['image'] && is_file(DIR_IMAGE . $user_info['image'])) {
		// 	$data['thumb'] = $this->model_tool_image->resize($user_info['image'], 100, 100);
		// } else {
		// 	$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		// }
		
		// $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('account/staff', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'account/staff')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['email']) < 3) || (utf8_strlen($this->request->post['email']) > 20)) {
			$this->error['email'] = $this->language->get('error_email');
		}

		$staff_info = $this->model_account_staff->getStaffByEmail($this->request->post['email']);

		if ($staff_info && ($this->staff->getId() != $staff_info['staff_id'])) {
			$this->error['warning'] = $this->language->get('error_exists_email');
		}

		if ((utf8_strlen(trim($this->request->post['staff_name'])) < 1) || (utf8_strlen(trim($this->request->post['staff_name'])) > 32)) {
			$this->error['staff_name'] = $this->language->get('error_staff_name');
		}

		if ((utf8_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
			$this->error['email'] = $this->language->get('error_email');
		}

		$staff_info = $this->model_account_staff->getStaffByEmail($this->request->post['email']);

		if ($staff_info && ($this->staff->getId() != $staff_info['staff_id'])) {
			$this->error['warning'] = $this->language->get('error_exists_email');
		}

		if ($this->request->post['password']) {
			if ((utf8_strlen(html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')) < 4) || (utf8_strlen(html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')) > 40)) {
				$this->error['password'] = $this->language->get('error_password');
			}

			if ($this->request->post['password'] != $this->request->post['confirm']) {
				$this->error['confirm'] = $this->language->get('error_confirm');
			}
		}

		return !$this->error;
	}
}