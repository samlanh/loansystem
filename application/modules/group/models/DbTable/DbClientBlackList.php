<?php

class Group_Model_DbTable_DbClientBlackList extends Zend_Db_Table_Abstract
{
    protected $_name = 'ln_client';
    public function getUserId(){
    	$db=new Application_Model_DbTable_DbGlobal();
    	return $db->getUserId();
    
    }
    function updatBlackList($data){
    	try{
//     		$arr = array(
//     				'branch_id'=>$data['branch'],
//     				'client_id'=>$data['client_name'],
//     				'date'=>$data['date'],
//     				'user_id'=>$this->getUserId(),
//     				'status'=>1,
//     		);
//     		$this->_name ="ln_client_blacklist";
//     		$this->insert($arr);
	    	$arr = array(
	    			'branch_id'=>$data['branch'],
	    			'is_blacklist'=>1,
	    			'date_blacklist'=>$data['date'],
	    			'reasonblack_list'=>$data['problem'],
	    			'status_blacklist'=>1,
	    			);
	    	$where=" client_id = ".$data['client_name'];
	    	$this->_name ="ln_client";
	    	$this->update($arr, $where);
    	}catch(Exception $e){
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    	}
    }
    function updatClientBlackList($data){
    	try{
	    	$arr = array(
	    			'is_blacklist'=>1,
	    			'date_blacklist'=>$data['date'],
	    			'reasonblack_list'=>$data['problem'],
	    			'status_blacklist'=>$data['status'],
	    	);
	    	$where=" client_id= ".$data['id'];
	    	$this->update($arr, $where);
    	}catch(Exception $e){
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    	}
    }
    function getAllBlackList($search=null){
    	$db=$this->getAdapter();
    	
    	$from_date =(empty($search['start_date']))? '1': " date >= '".$search['start_date']." 00:00:00'";
    	$to_date = (empty($search['end_date']))? '1': " date <= '".$search['end_date']." 23:59:59'";
    	$where = " AND ".$from_date." AND ".$to_date;
    	
    	$sql = " SELECT * FROM v_getclientblacklist WHERE 1";
    	if(!empty($search['adv_search'])){
    		$s_where = array();
    		$s_search = trim($search['adv_search']);
    		$s_where[] = "branch_name LIKE '%{$s_search}%'";
    		$s_where[] = "client_number LIKE '%{$s_search}%'";
    		$s_where[] = "client_name LIKE '%{$s_search}%'";
    		$s_where[] = "sex LIKE '%{$s_search}%'";
    		$s_where[] = " situation LIKE '%{$s_search}%'";
    		$s_where[] = " doc_name LIKE '%{$s_search}%'";
    		$s_where[] = " id_number LIKE '%{$s_search}%'";
    		$s_where[] = " street LIKE '%{$s_search}%'";
    		$s_where[] = " house LIKE '%{$s_search}%'";
    		$s_where[] = " village_name LIKE '%{$s_search}%'";
    		$s_where[] = " commune_name LIKE '%{$s_search}%'";
    		$s_where[] = " district_name LIKE '%{$s_search}%'";
    		$s_where[] = " province_name LIKE '%{$s_search}%'";
    		$where .=' AND ('.implode(' OR ',$s_where).')';
    	}
    	if(!empty($search['branch_id'])){
    		$where.= " AND branch_id = ".$search['branch_id'];
    	}
    	$order = " ORDER BY id DESC ";
    	return $db->fetchAll($sql.$where.$order);
    }
    function getAllBlackListInList($search=null){
    	$db=$this->getAdapter();
    	 
    	$from_date =(empty($search['start_date']))? '1': " c.date_blacklist >= '".$search['start_date']." 00:00:00'";
    	$to_date = (empty($search['end_date']))? '1': " c.date_blacklist <= '".$search['end_date']." 23:59:59'";
    	$where = " AND ".$from_date." AND ".$to_date;
    	 
//     	$sql = " SELECT id,branch_name, client_number ,client_name ,sex,situation,doc_name,id_number,reason,`date`,status FROM v_getclientblacklist WHERE 1";
    	$sql=" 
    		SELECT 
		    	c.client_id,
		    	(SELECT branch_namekh FROM `ln_branch` WHERE br_id =c.branch_id LIMIT 1) AS branch_name ,
		    	c.client_number,
		    	c.name_kh,
    			(SELECT `ln_view`.`name_en` FROM `ln_view` WHERE `ln_view`.`type` = 11 AND `ln_view`.`key_code` = `c`.`sex` LIMIT 1) AS `sex`,
    			(SELECT `ln_view`.`name_en` FROM `ln_view` WHERE `ln_view`.`type` = 5 AND `ln_view`.`key_code` = `c`.`sit_status` LIMIT 1) AS `situation`,
        	 	(SELECT `ln_view`.`name_en` FROM `ln_view` WHERE `ln_view`.`type` = 23 AND `ln_view`.`key_code` = `c`.`id_type` LIMIT 1) AS `doc_name`,     
         		`c`.`nation_id`,
         		c.reasonblack_list,
         		c.date_blacklist
    	";
    	
    	$dbp = new Application_Model_DbTable_DbGlobal();
    	$sql.=$dbp->caseStatusShowImage("c.status_blacklist");
    	$sql.=" FROM `ln_client` AS c WHERE 1 AND  c.status_blacklist =1 ";
    	
    	if(!empty($search['adv_search'])){
    		$s_where = array();
    		$s_search = trim($search['adv_search']);
    		$s_where[] = "c.client_number LIKE '%{$s_search}%'";
    		$s_where[] = "c.name_kh LIKE '%{$s_search}%'";
    		$s_where[] = "(SELECT `ln_view`.`name_en` FROM `ln_view` WHERE `ln_view`.`type` = 11 AND `ln_view`.`key_code` = `c`.`sex` LIMIT 1) LIKE '%{$s_search}%'";
    		$s_where[] = " (SELECT `ln_view`.`name_en` FROM `ln_view` WHERE `ln_view`.`type` = 5 AND `ln_view`.`key_code` = `c`.`sit_status` LIMIT 1) LIKE '%{$s_search}%'";
    		$s_where[] = " (SELECT `ln_view`.`name_en` FROM `ln_view` WHERE `ln_view`.`type` = 23 AND `ln_view`.`key_code` = `c`.`id_type` LIMIT 1) LIKE '%{$s_search}%'";
    		$s_where[] = " c.nation_id LIKE '%{$s_search}%'";
    		$where .=' AND ('.implode(' OR ',$s_where).')';
    	}
    	if(($search['status_search'])>-1){
    		$where.= " AND c.status_blacklist = ".$search['status_search'];
    	}
    	if(!empty($search['branch'])){
    		$where.= " AND c.branch_id = ".$search['branch'];
    	}
    	
    	$order = " ORDER BY c.client_id DESC ";
    	return $db->fetchAll($sql.$where.$order);
    }
    public function getBlackListById($id){
    	$db = $this->getAdapter();
    	$sql = "SELECT * FROM $this->_name WHERE client_id = ".$db->quote($id);
    	$sql.=" LIMIT 1 ";
    	$row=$db->fetchRow($sql);
    	return $row;
    }
   
}

