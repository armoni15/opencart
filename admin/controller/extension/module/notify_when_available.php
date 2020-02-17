<?php
class ControllerExtensionModuleNotifyWhenAvailable extends Controller {
	private $error = array();

	public function index() {

		$this->load->language('extension/module/notify_when_available');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('setting/setting');
		$this->load->model('extension/module/notify_when_available');

		//languages
		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();
		$subject 	= [];
		$body 		= [];
		foreach ($data['languages'] as $language) {
			$subject[$language['language_id']]['subject'] 	= $this->language->get('email_subject');
			$body[$language['language_id']]['body'] 		= $this->language->get('email_body');
		}

		
		$this->model_extension_module_notify_when_available->add_tables();

		$data['user_token'] = $this->session->data['user_token'];

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			
			$this->model_setting_setting->editSetting('module_notify_when_available', $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));

		}

		$data['entry_out_of_stock'] 	= $this->language->get('entry_out_of_stock');
		$data['entry_notify_button'] 	= $this->language->get('entry_notify_button');
		$data['tab_notify_product'] 	= $this->language->get('tab_notify_product');
		$data['tab_email'] 				= $this->language->get('tab_email');
		$data['button_product'] 		= $this->language->get('button_product');
		$data['entry_email'] 			= $this->language->get('entry_email');
		$data['entry_email_subject'] 	= $this->language->get('entry_email_subject');
		$data['entry_email_subject'] 	= $this->language->get('entry_email_subject');
		$data['entry_email_body'] 		= $this->language->get('entry_email_body');
		$data['help_email_subject'] 	= $this->language->get('help_email_subject');
		$data['help_email_body'] 		= $this->language->get('help_email_body');
		$data['heading_email_to_customer'] 	= $this->language->get('heading_email_to_customer');
		$data['heading_email_to_admin'] 	= $this->language->get('heading_email_to_admin');
		
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/notify_when_available', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/module/notify_when_available', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->post['module_notify_when_available_status'])) {
			$data['module_notify_when_available_status'] = $this->request->post['module_notify_when_available_status'];
		} else {
			$data['module_notify_when_available_status'] = $this->config->get('module_notify_when_available_status');
		}

		if (isset($this->request->post['module_notify_when_available_stock'])) {
			$data['module_notify_when_available_stock'] = $this->request->post['module_notify_when_available_stock'];
		} else {
			$data['module_notify_when_available_stock'] = $this->config->get('module_notify_when_available_stock');
		}

		if (isset($this->request->post['module_notify_when_available_notify'])) {
			$data['module_notify_when_available_notify'] = $this->request->post['module_notify_when_available_notify'];
		} else {
			$data['module_notify_when_available_notify'] = $this->config->get('module_notify_when_available_notify');
		}

		if (isset($this->request->post['module_notify_when_available_email_subject'])) {
			$data['module_notify_when_available_email_subject'] = $this->request->post['module_notify_when_available_email_subject'];
		} else if($this->config->get('module_notify_when_available_email_subject')){
			$data['module_notify_when_available_email_subject'] = $this->config->get('module_notify_when_available_email_subject');
		}else {
			$data['module_notify_when_available_email_subject'] = $subject;
		}

		if (isset($this->request->post['module_notify_when_available_email_body'])) {
			$data['module_notify_when_available_email_body'] = $this->request->post['module_notify_when_available_email_body'];
		} else if($this->config->get('module_notify_when_available_email_body')){
			$data['module_notify_when_available_email_body'] = $this->config->get('module_notify_when_available_email_body');
		}else {
			$data['module_notify_when_available_email_body'] = $body;
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/notify/notify_when_available', $data));
	}

	public function delete() {
		
		$this->load->language('extension/module/notify_when_available');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('extension/module/notify_when_available');

		if (isset($this->request->get['product_id']) && $this->validate()) {
			$this->model_extension_module_notify_when_available->deleteNotify($this->request->get['product_id']);

			$this->session->data['success'] = $this->language->get('text_success');
		}
		$this->response->redirect($this->url->link('extension/module/notify_when_available', 'user_token=' . $this->session->data['user_token'] . $url, true));
	}

	public function products() {

		$this->load->language('extension/module/notify_when_available');
		$this->load->model('extension/module/notify_when_available');
		$this->load->model('customer/customer');
		$this->load->model('catalog/product');
		$this->load->model('tool/image');
		$this->load->model('setting/setting');
		$this->load->model('localisation/language');
		
		$data['text_register'] 	 	= $this->language->get('text_register');
		$data['text_unregister'] 	= $this->language->get('text_unregister');
		$data['text_show_customer'] = $this->language->get('text_show_customer');
		$data['column_product'] 	= $this->language->get('column_product');
		$data['column_customer'] 	= $this->language->get('column_customer');
		$data['column_message'] 	= $this->language->get('column_message');
		$data['column_date_added'] 	= $this->language->get('column_date_added');

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['products'] = array();
		$results = $this->model_extension_module_notify_when_available->get_notify_products(($page - 1) * 10, 10);

		foreach ($results as $result) {
			$product_info = $this->model_catalog_product->getProduct($result['product_id']);
			$product_edit = $this->url->link('catalog/product/edit', 'product_id='.$result['product_id'].'&user_token=' . $this->session->data['user_token'], true);
			$customer_edit = $this->url->link('customer/customer/edit', 'customer_id='.$result['customer_id'].'&user_token=' . $this->session->data['user_token'], true);
			
			if (is_file(DIR_IMAGE . $product_info['image'])) {
				$thumb = $this->model_tool_image->resize($product_info['image'], 40, 40);
			} else {
				$thumb = $this->model_tool_image->resize('no_image.png', 40, 40);
			}

			if($result['store_id']>0){
				$store_info = $this->model_setting_setting->getSetting('config', $result['store_id']);
				$store_name = $store_info['config_name'];
			}else{
				$store_name = $this->language->get('text_default');
			}	

			$language 	= $this->model_localisation_language->getLanguage($result['language_id']);

			$data['products'][] = array(
				'product_id'		=> $result['product_id'],
				'product_name'		=> ($product_info)?$product_info['name']:'',
				'product_thumb'		=> $thumb,
				'product_edit'		=> $product_edit,
				'customer_id'		=> $result['customer_id'],
				'customer_name'		=> $result['name'],
				'customer_email'	=> $result['email'],
				'message'			=> $result['message'],
				'status'			=> $result['status'],
				'date_added'  		=> $result['date_added'],
				'customer_edit'		=> $customer_edit,
				'store_name' 		=> sprintf($this->language->get('text_store_name'),$store_name),
				'language' 			=> "<img src='language/".$language['code']."/".$language['code'].".png' title='". $language['name'] ."' /> ".$language['name'],
				'delete'            => $this->url->link('extension/module/notify_when_available/delete', 'user_token=' . $this->session->data['user_token'] . '&product_id=' . $result['product_id'], true)
			);
		}

		$notify_product_total = $this->model_extension_module_notify_when_available->get_notify_total();

		$pagination = new Pagination();
		$pagination->total = $notify_product_total;
		$pagination->page = $page;
		$pagination->limit = 10;
		$pagination->url = $this->url->link('extension/module/notify_when_available/get_notify_products', 'user_token=' . $this->session->data['user_token'] . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($notify_product_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($notify_product_total - 10)) ? $notify_product_total : ((($page - 1) * 10) + 10), $notify_product_total, ceil($notify_product_total / 10));

		$this->response->setOutput($this->load->view('extension/module/notify/products', $data));
	}

	

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/notify_when_available')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}