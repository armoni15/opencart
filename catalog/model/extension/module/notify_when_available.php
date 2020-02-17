<?php
class ModelExtensionModuleNotifyWhenAvailable extends Model {

	public function add_notify_request($data = array()) {

		$this->db->query("INSERT INTO " . DB_PREFIX . "notify_when_available SET name = '".$this->db->escape($data['name'])."', email = '".$this->db->escape($data['email'])."', product_id = '".$this->db->escape($data['product_id'])."', message = '', customer_id = '".$this->db->escape($data['customer_id'])."', language_id = '" . (int)$this->config->get('config_language_id')."', store_id = '" . (int)$this->config->get('config_store_id'). "', date_added = NOW()");

	}

}