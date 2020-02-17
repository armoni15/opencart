<?php
class ModelExtensionModuleNotifyWhenAvailable extends Model {
	
	public function add_tables() {

		$this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "notify_when_available ( notify_id INT AUTO_INCREMENT PRIMARY KEY, customer_id int(11) DEFAULT 0 NOT NULL, product_id int(11) DEFAULT 0 NOT NULL, name VARCHAR (255)  DEFAULT '' NOT NULL, email VARCHAR (255)  DEFAULT '' NOT NULL, message TEXT DEFAULT '' NOT NULL, status INT(11) DEFAULT 0 NOT NULL, language_id int(11) DEFAULT 0 NOT NULL, store_id int(11) DEFAULT 0 NOT NULL, date_added DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00') ENGINE MyISAM DEFAULT CHARSET utf8 COLLATE utf8_general_ci");
		//notify email sent
		$this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "notify_when_available_email ( email_sent_id INT(11) AUTO_INCREMENT PRIMARY KEY, product_id int(11) DEFAULT 0 NOT NULL, product_name VARCHAR (255)  DEFAULT '' NOT NULL, name VARCHAR (255)  DEFAULT '' NOT NULL, email VARCHAR (255)  DEFAULT '' NOT NULL, email_content TEXT DEFAULT '' NOT NULL, date_added DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00') ENGINE MyISAM DEFAULT CHARSET utf8 COLLATE utf8_general_ci");
		
	}

	public function get_notify_products($start = 0, $limit = 10) {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 10;
		}

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "notify_when_available ORDER BY date_added DESC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}

	public function get_notify_total() {
		$query = $this->db->query("SELECT COUNT(*) AS total  FROM " . DB_PREFIX . "notify_when_available");

		return $query->row['total'];
	}

	public function send_in_stock_email($product_id = 0, $data = array()){

		$active_language_id = [];
		foreach ($data['product_description'] as $key => $value) {
			$active_language_id[] = $key;
		}

		$this->load->model('setting/setting');
		$this->load->model('extension/module/notify_when_available');
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "notify_when_available WHERE product_id = '".(int)$product_id."'");
		foreach ($query->rows as $result) {

			//get the store url
			$store_info = $this->model_setting_setting->getSetting('config', $result['store_id']);
			if($result['store_id'] == 0){
				$store_url = HTTP_CATALOG;
			}else{
				$store_url = $store_info['config_url'];
			}

			if(in_array($result['language_id'], $active_language_id)){
				$send_language_id = $result['language_id'];
			}else{
				$send_language_id = $active_language_id[0];
			}

			$data = [
				'product_id'   	=> $result['product_id'],
				'product_name' 	=> $data['product_description'][$send_language_id]['name'],
				'product_url' 	=> $store_url."index.php?route=product/product&product_id=". $result['product_id'],
				'name'			=> $result['name'],
				'email'			=> $result['email'], 
				'language_id' 	=> $result['language_id'],
			];

			$this->model_extension_module_notify_when_available->instock_email_to_customer($data);
			

		}

	}

	public function insert_data_to_customer($data = array()){
		$this->db->query("INSERT INTO " . DB_PREFIX . "notify_when_available_email SET name = '".$this->db->escape($data['name'])."', email = '".$this->db->escape($data['email'])."', product_id = '".$this->db->escape($data['product_id'])."', product_name = '".$this->db->escape($data['product_name'])."', email_content = '".$this->db->escape($data['email_content'])."', date_added = NOW()");
		$this->db->query("UPDATE " . DB_PREFIX . "notify_when_available SET status = '1' WHERE product_id = '".$this->db->escape($data['product_id'])."'");
	}

	//send email to customer
	public function instock_email_to_customer($data = array()){
	
		$customer_info 		= $data;
		$subject_array   	= $this->config->get('module_notify_when_available_email_subject');
		$email_body_array 	= $this->config->get('module_notify_when_available_email_body');
		
		$store_language_id = $data['language_id'];
		$subject = '';
		if(count($subject_array)>0){
			$subject 		= $subject_array[$store_language_id]['subject'];
		}

		$email_body = '';
		if(count($email_body_array)>0){
			$email_body 	= $email_body_array[$store_language_id]['body'];
		}

		$pre_html  = '<html dir="ltr" lang="en">' . "\n";
		$pre_html .= '  <head>' . "\n";
		$pre_html .= '    <title>' . $subject . '</title>' . "\n";
		$pre_html .= '    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">' . "\n";
		$pre_html .= '  </head>' . "\n";
		$pre_html .= '  <body>' ;
		$post_html = '</body>' . "\n";
		$post_html .= '</html>' . "\n";
		$codes = ["{name}", "{product_url}", "{product_name}"];	
		$email = $customer_info;
		$subjects = ''. $email['product_name'] .' - '. $subject .'';
		if (preg_match('/^[^\@]+@.*.[a-z]{2,15}$/i', $email['email'])) {
			$values   = [$email['name'], $email['product_url'],$email['product_name']];
			$message = str_replace($codes, $values, $email_body);
			$html = html_entity_decode($pre_html . $message . $post_html, ENT_QUOTES, 'UTF-8');

			$mail = new Mail($this->config->get('config_mail_engine'));
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
	
			$mail->setTo($email['email']);
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
			$mail->setSubject(html_entity_decode($subjects, ENT_QUOTES, 'UTF-8'));
			$mail->setHtml($html);
			$mail->send();

			$insert_email_data = array_merge($data,['email_content' => serialize($html)]);
			$this->model_extension_module_notify_when_available->insert_data_to_customer($insert_email_data);
		}	
	}

	public function deleteNotify($product_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "notify_when_available` WHERE `product_id` = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "notify_when_available_email` WHERE `product_id` = '" . (int)$product_id . "'");
	}
}
