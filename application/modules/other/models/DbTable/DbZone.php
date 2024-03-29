<?php

class Other_Model_DbTable_DbZone extends Zend_Db_Table_Abstract
{

    protected $_name = 'ln_zone';
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace(SYSTEM_SES);
    	return $session_user->user_id;
    }
	public function addZone($_data){
		try {
			$_arr=array(
					'branch_id'	  => $_data['branch_id'],
					'zone_name'	  => $_data['zone_name'],
					'zone_num'	  => $_data['zone_number'],
					'modify_date' => date('Y-m-d'),
					'status'	  => $_data['status'],
					'user_id'	  => $this->getUserId()
			);
			if(!empty($_data['id'])){
				$where = 'zone_id = '.$_data['id'];
				return  $this->update($_arr, $where);
			}else{
				return  $this->insert($_arr);
			}
		}catch (Exception $e){
		}
	}
	public function getZoneById($id){
		$db = $this->getAdapter();
		$sql=" SELECT * FROM $this->_name WHERE
			  zone_id = ".$db->quote($id);
		
		$dbp = new Application_Model_DbTable_DbGlobal();
		$sql.=$dbp->getAccessPermission("branch_id");
		
		$sql.=" LIMIT 1 ";
		$row=$db->fetchRow($sql);
		return $row;
	}
	function getAllZoneArea($search=null){
		$db = $this->getAdapter();
		$sql = "SELECT
				zone_id,
				(SELECT branch_namekh FROM `ln_branch` WHERE ln_branch.br_id=branch_id LIMIT 1) AS branch,
				zone_name,zone_num,modify_date
				 ";
		
		$dbp = new Application_Model_DbTable_DbGlobal();
		$sql.=$dbp->caseStatusShowImage("ln_zone.status");
		$sql.=",
			(SELECT first_name FROM rms_users WHERE id=user_id LIMIT 1) As user_name
		FROM $this->_name ";
		
		$where = ' WHERE zone_name!="" ';
		if($search['search_status']>-1){
			$where.= " AND status = ".$search['search_status'];
		}
		if(!empty($search['adv_search'])){
			$s_where = array();
			$s_search = str_replace(' ', '', addslashes(trim($search['adv_search'])));
			$s_where[] ="REPLACE(zone_num,' ','')  	LIKE '%{$s_search}%'";
			$s_where[] ="REPLACE(zone_name,' ','')  LIKE '%{$s_search}%'";
			$where.=' AND ('.implode(' OR ',$s_where).')';
		}
		$dbp = new Application_Model_DbTable_DbGlobal();
		$where.=$dbp->getAccessPermission("branch_id");
		
		$order = " ORDER BY zone_id DESC";
		return $db->fetchAll($sql.$where.$order);	
	}	
}

