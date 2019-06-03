<?php

class Tellerandexchange_Model_DbTable_DbCategory extends Zend_Db_Table_Abstract
{

    protected $_name = 'ln_income_expense_category';
    public function getUserId(){
    	$dbp = new Application_Model_DbTable_DbGlobal();
    	return $dbp->getUserId();
    	 
    }
	function getAllCategory($search=null,$type=null,$parent = 0, $spacing = '', $cate_tree_array = ''){
    	$db = $this->getAdapter();
    	$tr = Application_Form_FrmLanguages::getCurrentlanguage();
    	$dbp = new Application_Model_DbTable_DbGlobal();
    	$sql=" SELECT 
			c.id,
			c.parent ,
			c.title AS `name`,
			c.type,
			CASE
					WHEN  c.type = 1 THEN '".$tr->translate("INCOME")."'
					WHEN  c.type = 2 THEN '".$tr->translate("EXPENSE")."'
					END AS typess,
			c.note
			  ";
    	
    	$sql.=$dbp->caseStatusShowImage("c.status");
    	$sql.=" FROM `ln_income_expense_category` AS c WHERE 1 AND c.parent = $parent ";
    	
    	if($type!=null){
    		$sql.=" AND c.type = $type";
    	}
    	$Other=" ORDER BY c.type DESC, c.id DESC ";
    	$where = '';
    	 
    	if(!empty($search['adv_search'])){
    		$s_where = array();
    		$s_search = $search['adv_search'];
    		$s_where[] = " c.title LIKE '%{$s_search}%'";
    		$where .=' AND ('.implode(' OR ',$s_where).')';
    	}
    	if($search['type']>-1){
    		$where.= " AND c.type = ".$search['type'];
    	}
    	if($search['status']>-1){
    		$where.= " AND c.status = ".$search['status'];
    	}
    	$rows = $db->fetchAll($sql.$where.$Other);
    	if (!is_array($cate_tree_array))
    		$cate_tree_array = array();
    	if (count($rows) > 0) {
    		foreach ($rows as $row){
    			$cate_tree_array[] = array("id" => $row['id'],"name" => $spacing . $row['name'],"typess" => $row['typess'],"note" => $row['note'],"status" => $row['status']);
    			$cate_tree_array = $this->getAllCategory($search,$row['type'],$row['id'], $spacing . ' - ', $cate_tree_array);
    		}
    	}
    	return $cate_tree_array;
    }
    function addCategory($data){
    	$_db= $this->getAdapter();
    	$_db->beginTransaction();
    	try{
	    	$data = array(
	    			'type'=>$data['type'],
	    			'parent'=>$data['parent'],
	    			'title'=>$data['title'],
	    			'note'=>$data['note'],
	    			'status'=>1,
	    			'create_date'=>date("Y-m-d H:i:s"),
	    			'modify_date'=>date("Y-m-d H:i:s"),
	    			'user_id'=>$this->getUserId()
	    	);
	    	$this->_name = "ln_income_expense_category";
	    	$id = $this->insert($data);
	    	
	    	$_db->commit();
	    	return $id;
    	}catch(Exception $e){
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    	}
    
    }
    function getCagetgoryBYID($id){
    	$db = $this->getAdapter();
    	$sql="SELECT * FROM ln_income_expense_category WHERE id=$id LIMIT 1";
    	return $db->fetchRow($sql);
    }
    function getAllIncomeCategory($type=1,$idcategory=null,$parent = 0, $spacing = '', $cate_tree_array = ''){
    	if (!is_array($cate_tree_array))
    		$cate_tree_array = array();
    	$db = $this->getAdapter();
    	$sql = " SELECT id as id,title as name FROM ln_income_expense_category WHERE type=$type AND title!='' AND `parent` = $parent ";
    	if (!empty($idcategory)){
    		$sql.=" AND id != $idcategory ";
    	}
    	$query= $db->fetchAll($sql);
    
    	$rowCount = count($query);
    	if ($rowCount > 0) {
    		foreach ($query as $row){
    			$cate_tree_array[] = array("id" => $row['id'], "name" => $spacing . $row['name']);
    			$cate_tree_array = $this->getAllIncomeCategory($type,$idcategory,$row['id'], $spacing . ' - ', $cate_tree_array);
    		}
    	}
    	return $cate_tree_array;
    
    }
  
}

