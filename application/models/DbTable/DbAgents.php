<?php

class Application_Model_DbTable_DbAgents extends Zend_Db_Table_Abstract
{

    protected $_name = 'cs_agents';
	
	function getAgentList($start, $limit){
		$db = $this->getAdapter();
		$sql = $this->_buildQuery()." LIMIT ".$start.", ".$limit;
		if ($limit == 'All') {
			$sql = $this->_buildQuery();
		}	
		return $db->fetchAll($sql);
	}
	
	function getAgentListBy($search){        
		$db = $this->getAdapter();		
		$sql = "SELECT a.`id`,  a.`name`, a.`tel`, p.`name` AS proname,a.`khan`, a.`sangkat`, a.`block`
				FROM `cs_agents` as a
				     INNER JOIN `cs_provinces` AS p ON (a.`province` = p.`id`)
				WHERE 1 ";
		
		$orderby = " ORDER BY a.`id`";
		$where ='';
		
		if ($search['active'] >= 0){
			$where = ' AND a.`active` = '.$search['active'];			
		}
		
		
		if (!empty($search['txtsearch'])){
			$fields = array('a.name', 'a.tel', 'a.fax', 'a.hp', 'a.code');	
			$s_where = array();	
	        foreach($fields as $field)
	    	{
	    		$s_where[] = $field." LIKE '%{$search['txtsearch']}%'";
	    	}	    	
	        $where .= ' AND ('.implode(' OR ',$s_where).')';
		}
		
		if ($search['province'] > 0){
			$where .= ' AND a.`province` = '. $search['province'];
		}		
		return $db->fetchAll($sql.$where.$orderby);
	}
	
	function getAgentListTotal($search=''){        
		$db = $this->getAdapter();
		$sql = $this->_buildQuery();
		if(!empty($search)){
			$sql = $this->_buildQuery($search);
		}
		$_result = $db->fetchAll($sql); 
		return count($_result);
	}
	
	function getAgentListSelect(){
		$db = $this->getAdapter();
		$sql = "SELECT id, name, province
				FROM `cs_agents`
				WHERE active = 1";	
		return $db->fetchAll($sql);
	}
	
	function getSenderList(){
		$db = $this->getAdapter();
		$sql = "SELECT DISTINCT sender_name
		FROM `cs_money_transactions`
		WHERE tran_type = 0 AND sender_name!=''";
		return $db->fetchAll($sql);
	}
	function getSenderOweList(){
		$db = $this->getAdapter();
		$sql = "SELECT DISTINCT sender_name
		FROM `cs_money_transactions`
		WHERE status_loan = 1 AND sender_name!=''";
		return $db->fetchAll($sql);
	}
	
	function getAgentListSelectTrns(){
		$db = $this->getAdapter();
		$sql = "SELECT id, name, province, `house_no`, `street_no`, `block`, `tel`
				FROM `cs_agents`
				WHERE active = 1";		
		return $db->fetchAll($sql);
	}
	
	function getAgentEditedById($id){
		$db = $this->getAdapter();
		$sql = "SELECT 	a.`id`,  
						a.`name`, 
						a.`tel`,
						a.`code`, 
						a.`fax`, 
						a.`hp`, 
						a.`block`,
						a.`house_no`,
						a.`group_no`,
						a.`street_no`, 
						a.`khan`, 
						a.`sangkat`,
						a.`province`,
						a.`active`
						
				FROM `cs_agents` as a				     
				WHERE a.`id` = ".$id;	
		//print ($sql); exit();	
		return $db->fetchRow($sql);
	}
	
	function  getAgentViewById($id){
		$db = $this->getAdapter();
		$sql = "SELECT 	a.`id`,  
						a.`name`,						 
						a.`tel`,
						a.`code`, 
						a.`fax`, 
						a.`hp`, 
						a.`block`,
						a.`house_no`,
						a.`group_no`,
						a.`street_no`, 
						a.`khan`, 
						a.`sangkat`,
						p.`name` AS proname,
						a.`active`
											
				FROM `cs_agents` as a
				     INNER JOIN `cs_provinces` AS p ON (a.`province` = p.`id`)
				WHERE a.`id` = ".$id;	
		//print ($sql); exit();	
		return $db->fetchRow($sql);
	}
	
	function insertAgent($data){    	

    	$_agent_data=array(
    					'name'=>$data['agent_name'],
						'code'=>$data['code'],
						'tel'=>$data['tel'],
						'fax'=>$data['fax'],
						'hp'=>$data['hp'],
						'block'=>$data['block'],
						'house_no'=>$data['house_no'],
						'group_no'=>$data['group_no'],
						'street_no'=>$data['street_no'],
						'sangkat'=>$data['sangkat'],
						'khan'=>$data['khan'],						
						'province'=>$data['province'],						
						'active'=> 1			
    	           );    
    	 	 
			return $this->insert($_agent_data);		
	}
	
	function updateAgent($data){    	
		
		
    	$_agent_data=array(
    		'name'=>$data['agent_name'],
			'code'=>$data['code'],
			'tel'=>$data['tel'],
			'fax'=>$data['fax'],
			'hp'=>$data['hp'],
			'block'=>$data['block'],
			'house_no'=>$data['house_no'],
			'group_no'=>$data['group_no'],
			'street_no'=>$data['street_no'],
			'sangkat'=>$data['sangkat'],
			'khan'=>$data['khan'],						
			'province'=>$data['province'],						
			'active'=> $data['active']			
			);    
		$where=$this->getAdapter()->quoteInto('id=?', $data['id']);
    	 
		return  $this->update($_agent_data, $where);    	 
	}

}

