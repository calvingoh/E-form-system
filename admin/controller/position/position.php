<?php
class ControllerPositionPosition extends Controller {
    private $error = array();
	public function index() {
        $this->load->language('position/position');

		$this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('position/position');
        // unset($this->error);
        $this->getList();
        // $data = array();
        // $data['header']=$this->load->controller('common/header');
        // $data['column_left']=$this->load->controller('common/column_left');
        
		// $this->response->setOutput($this->load->view('position/position_list', $data));
    }

    public function add() {
		$this->load->language('position/position');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('position/position');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
             
            $this->model_position_position->addPosition($this->request->post);
        

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

			$this->response->redirect($this->url->link('position/position', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
    }
    
    public function edit() {
		$this->load->language('position/position');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('position/position');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_position_position->editPosition($this->request->get['position_id'], $this->request->post);

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

			$this->response->redirect($this->url->link('position/position', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
    }
    
    public function delete() {
		$this->load->language('position/position');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('position/position');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $position_id) {
				$this->model_position_position->deletePosition($position_id);
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

			$this->response->redirect($this->url->link('position/position', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

    protected function getList() {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'position';
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

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('position/position', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('position/position/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('position/position/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['positions'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

        $position_total = $this->model_position_position->getTotalPositions();


		$results = $this->model_position_position->getPositions($filter_data);

		foreach ($results as $result) {
			$data['positions'][] = array(
				'position_id'    => $result['position_id'],
				'position_name'  => $result['position_name'],
				'grade'          => $result['grade'],
                //'status' => ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
				'date_added'     => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'date_modified'  => date($this->language->get('date_format_short'), strtotime($result['date_modified'])),
				'edit'           => $this->url->link('position/position/edit', 'user_token=' . $this->session->data['user_token'] . '&position_id=' . $result['position_id'] . $url, true)
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

		$data['sort_position'] = $this->url->link('position/position', 'user_token=' . $this->session->data['user_token'] . '&sort=position' . $url, true);
		$data['sort_grade'] = $this->url->link('position/position', 'user_token=' . $this->session->data['user_token'] . '&sort=position' . $url, true);
		$data['sort_date_added'] = $this->url->link('position/position', 'user_token=' . $this->session->data['user_token'] . '&sort=date_added' . $url, true);
		$data['sort_date_modified'] = $this->url->link('position/position', 'user_token=' . $this->session->data['user_token'] . '&sort=date_modified' . $url, true);
		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $position_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('position/position', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($position_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($position_total - $this->config->get('config_limit_admin'))) ? $position_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $position_total, ceil($position_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        
        // echo '<pre>';
        // print_r($data);
        // echo '</pre>';exit;
		$this->response->setOutput($this->load->view('position/position_list', $data));
	}

	protected function getForm() {
		$data['text_form'] = !isset($this->request->get['position_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
        }
        
		if (isset($this->error['position_name'])) {
			$data['error_position'] = $this->error['position_name'];
		} else {
			$data['error_position'] = '';
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
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('position/position', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['position_id'])) {
			$data['action'] = $this->url->link('position/position/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('position/position/edit', 'user_token=' . $this->session->data['user_token'] . '&position_id=' . $this->request->get['position_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('position/position', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['position_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$position_info = $this->model_position_position->getPosition($this->request->get['position_id']);
        }

        // $this->load->model('position/position');
        // $data['position'] = $this->model_position_position->getPositions();

		if (isset($this->request->post['position_name'])) {
			$data['position_name'] = $this->request->post['position_name'];
		} elseif (!empty($position_info)) {
			$data['position_name'] = $position_info['position_name'];
		} else {
			$data['position_name'] = '';
		}
		
		if (isset($this->request->post['grade'])) {
			$data['grade'] = $this->request->post['grade'];
		} elseif (!empty($position_info)) {
			$data['grade'] = $position_info['grade'];
		} else {
			$data['grade'] = '';
        }

        
        // if (isset($this->request->post['status'])) {
		// 	$data['status'] = $this->request->post['status'];
		// } elseif (!empty($position_info)) {
		// 	$data['status'] = $position_info['status'];
		// } else {
		// 	$data['status'] = 0;
		// }

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('position/position_form', $data));
    }
    
    protected function validateForm() {
         
		if (!$this->user->hasPermission('modify', 'position/position')) {
			$this->error['warning'] = $this->language->get('error_permission');
        }
        // echo '<pre>';
        // print_r($this->request->post);
        // echo '</pre>';exit;
        // echo strlen(trim($this->request->post['position_name']));exit;


		if ((strlen(trim($this->request->post['position_name'])) < 1) || (strlen(trim($this->request->post['position_name'])) > 32))  {

		$this->error['position_name'] = $this->language->get('error_position');
        }

        // echo '<pre>';
        // print_r($this->error['position_name']);
        // echo '</pre>';exit;
        

        return !$this->error;
    }

    
	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'position/position')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['selected'] as $position_id) {
			if ($this->user->getId() == $position_id) {
				$this->error['warning'] = $this->language->get('error_position');
			}
		}

		return !$this->error;
	}
}

