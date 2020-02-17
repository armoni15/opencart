<?php
class ModelSaleDiscount extends Model {
	public function addDiscount($code, $data) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "module` SET `name` = '" . $this->db->escape($data['name']) . "', `code` = '" . $this->db->escape($code) . "', `setting` = '" . $this->db->escape(json_encode($data)) . "'");
	
		$jumlah = count($data['product']);
		for($i = 0; $i < $jumlah; $i++){
			$pr=$this->db->query("SELECT * FROM `" . DB_PREFIX . "product` WHERE `product_id` = '" . $this->db->escape($data['product'][$i]) . "'")->row;
			$pr['price']=$pr['price']-($pr['price']*$data['potongan']/100);
			$query = $this->db->query("INSERT INTO `" . DB_PREFIX . "product_special` SET `product_id` = '" . $this->db->escape($data['product'][$i]) . "', `customer_group_id` = '" . $this->db->escape($data['customer_group_id']) . "', `priority` = '" . $this->db->escape($data['priority']) . "', `price` = '".$pr['price']."', `date_start` = '" . $this->db->escape($data['date_start']) . "', `date_end` = '" . $this->db->escape($data['date_end']) . "', `information` = '" . $this->db->escape($data['name']) . "'");
		}
	}
	
	public function editDiscount($module_id, $data) {
		$this->db->query("UPDATE `" . DB_PREFIX . "module` SET `name` = '" . $this->db->escape($data['name']) . "', `setting` = '" . $this->db->escape(json_encode($data)) . "' WHERE `module_id` = '" . (int)$module_id . "'");
	}

	public function deleteDiscount($module_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "module` WHERE `module_id` = '" . (int)$module_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "layout_module` WHERE `code` LIKE '%." . (int)$module_id . "'");
	}
		
	public function getDiscount($module_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "module` WHERE `module_id` = '" . (int)$module_id . "'");

		if ($query->row) {
			return json_decode($query->row['setting'], true);
		} else {
			return array();
		}
	}
	
	public function getDiscounts() {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "module` ORDER BY `code`");

		return $query->rows;
	}	
		
	public function getDiscountsByCode($code) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "module` WHERE `code` = '" . $this->db->escape($code) . "' ORDER BY `name`");

		return $query->rows;
	}	
	
	public function deleteDiscountsByCode($code) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "module` WHERE `code` = '" . $this->db->escape($code) . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "layout_module` WHERE `code` LIKE '" . $this->db->escape($code) . "' OR `code` LIKE '" . $this->db->escape($code . '.%') . "'");
	}	
}