<?php
class ControllerExtensionModuleNotifyWhenAvailable extends Controller {
	
	public function index() {
	
	}

	public function add_notify_request(){

		$this->load->language('extension/module/notify_when_available');
		$this->load->model('extension/module/notify_when_available');

		$json = array();

		if ((utf8_strlen(trim($this->request->post['name'])) < 1) || (utf8_strlen(trim($this->request->post['name'])) > 32)) {
			$json['error'] = $this->language->get('error_name');
		}

		if (utf8_strlen(trim($this->request->post['product_id'])) < 1) {
			$json['error'] = $this->language->get('error_warning');
		}

		if ((utf8_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
			$json['error'] = $this->language->get('error_email');
		}

		if(!$json){
			$data = [
				'name' 			=> $this->request->post['name'],
				'email' 		=> $this->request->post['email'],
				'product_id' 	=> $this->request->post['product_id'],
			];
			$customer_id = 0;
			if ($this->customer->isLogged()) {
				$customer_id = $this->customer->getId();
			}
			$data = array_merge($data,['customer_id' => $customer_id]);

			$this->model_extension_module_notify_when_available->add_notify_request($data);
			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));

	}

}