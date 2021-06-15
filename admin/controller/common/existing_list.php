<?php
class ControllerInformationStatic extends Controller {
  private $error = array();
    
  public function index() {
    $this->language->load('information/static'); //Optional. This calls for your language file

    $this->document->setTitle($this->language->get('heading_title')); //Optional. Set the title of your web page.

    $data['breadcrumbs'] = array();

    $data['breadcrumbs'][] = array(
      'text'      => $this->language->get('text_home'),
      'href'      => $this->url->link('common/home')
    );

    $data['breadcrumbs'][] = array(
      'text'      => $this->language->get('heading_title'),
      'href'      => $this->url->link('information/static')
    );   
       
    $data['heading_title'] = $this->language->get('heading_title'); //Get "heading title" from language file.

    $data['column_left'] = $this->load->controller('common/column_left');
    $data['column_right'] = $this->load->controller('common/column_right');
    $data['content_top'] = $this->load->controller('common/content_top');
    $data['content_bottom'] = $this->load->controller('common/content_bottom');
    $data['footer'] = $this->load->controller('common/footer');
    $data['header'] = $this->load->controller('common/header');

    if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/information/static.tpl')) { //if file exists in your current template folder
      $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/information/static.tpl', $data)); //get it
    } else {
      $this->response->setOutput($this->load->view('default/template/information/static.tpl', $data)); //or get the file from the default folder
    }     
  }
}