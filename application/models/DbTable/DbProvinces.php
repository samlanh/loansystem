<?php

class Application_Model_DbTable_DbProvinces extends Zend_Db_Table_Abstract
{

    protected $_name = 'ln_province';
	
	function getProvinceList(){
		$db = $this->getAdapter();
		$sql = "SELECT p.province_id AS id, p.province_kh_name AS name, COUNT(a.id) AS num
				FROM `ln_province` AS p
					INNER JOIN `cs_agents` AS a ON (p.province_id = a.province)
				GROUP BY a.province, p.province_id, p.province_kh_name
				ORDER BY p.province_id ";		
		return $db->fetchAll($sql);
	}
	
	function getProvinceListAll(){
		$db = $this->getAdapter();
		$sql = "SELECT p.id, p.name
				FROM `ln_province` AS p";		
		return $db->fetchAll($sql);
	}

}

