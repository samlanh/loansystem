<?php
class Report_Model_DbTable_DbRptIncomeExpense extends Zend_Db_Table_Abstract
{
	
	function getAllIncome($search=null){//report
		$db = $this->getAdapter();
		$session_user=new Zend_Session_Namespace(SYSTEM_SES);
		$from_date =(empty($search['start_date']))? '1': " date >= '".$search['start_date']." 00:00:00'";
		$to_date = (empty($search['end_date']))? '1': " date <= '".$search['end_date']." 23:59:59'";
		$where = " WHERE ".$from_date." AND ".$to_date;
		$this->_name="ln_income";
		$sql=" SELECT *,
		(SELECT branch_namekh FROM `ln_branch` WHERE ln_branch.br_id =branch_id LIMIT 1) AS branch_name,
		(SELECT c.title FROM `ln_income_expense_category` AS c WHERE c.id = category_id LIMIT 1) AS category,
		(SELECT symbol FROM `ln_currency` WHERE ln_currency.id =curr_type) AS currency_type
		FROM $this->_name ";
	
		if (!empty($search['adv_search'])){
			$s_where = array();
			$s_search = trim(addslashes($search['adv_search']));
			$s_where[] = " account_id LIKE '%{$s_search}%'";
			$s_where[] = " title LIKE '%{$s_search}%'";
			$s_where[] = " total_amount LIKE '%{$s_search}%'";
			$s_where[] = " invoice LIKE '%{$s_search}%'";
			$s_where[] = " reciept_no LIKE '%{$s_search}%'";
			$s_where[] = " (SELECT c.title FROM `ln_income_expense_category` AS c WHERE c.id = category_id LIMIT 1) LIKE '%{$s_search}%'";
	
			$where .=' AND ('.implode(' OR ',$s_where).')';
		}
		$where.= " AND status = 1 ";
		
		if($search['currency_type']>-1){
			$where.= " AND curr_type = ".$search['currency_type'];
		}
		if(!empty($search['category_id'])){
			$where.= " AND category_id = ".$search['category_id'];
		}
		if(!empty($search['branch_id'])){
			if($search['branch_id']>0){
				$where.=" AND branch_id= ".$search['branch_id'];
			}
    	}
		$dbp = new Application_Model_DbTable_DbGlobal();
		$where.=$dbp->getAccessPermission('branch_id');
		$order=" order by id desc ";
		return $db->fetchAll($sql.$where.$order);
	}
	
	function closingIncome($data){
		$db = $this->getAdapter();
		if(!empty($data['id_selected'])){
			$ids = explode(',', $data['id_selected']);
			$key = 1;
			$arr = array(
					"is_closed"=>1,
			);
			foreach ($ids as $i){
				$this->_name="ln_income";
				$where="id= ".$i;
				$this->update($arr, $where);
			}
		}
	}
	
	
	function getAllExpenseReport($search=null){
		$db = $this->getAdapter();
		$session_user=new Zend_Session_Namespace(SYSTEM_SES);
		$from_date =(empty($search['start_date']))? '1': " date >= '".$search['start_date']." 00:00:00'";
		$to_date = (empty($search['end_date']))? '1': " date <= '".$search['end_date']." 23:59:59'";
		$where = " WHERE ".$from_date." AND ".$to_date;
		$this->_name="ln_income_expense";
		$sql=" SELECT *,
					(SELECT branch_namekh FROM `ln_branch` WHERE ln_branch.br_id =branch_id LIMIT 1) AS branch_name,
					(SELECT c.title FROM `ln_income_expense_category` AS c WHERE c.id = category_id LIMIT 1) AS category,
					(SELECT symbol FROM `ln_currency` WHERE ln_currency.id =curr_type) AS currency_type
			FROM $this->_name ";
		$where.= " AND status = 1 ";
		if (!empty($search['adv_search'])){
			$s_where = array();
			$s_search = trim(addslashes($search['adv_search']));
			$s_where[] = " account_id LIKE '%{$s_search}%'";
			$s_where[] = " title LIKE '%{$s_search}%'";
			$s_where[] = " total_amount LIKE '%{$s_search}%'";
			$s_where[] = " invoice LIKE '%{$s_search}%'";
			$s_where[] = " reciept_no LIKE '%{$s_search}%'";
			$s_where[] = " (SELECT c.title FROM `ln_income_expense_category` AS c WHERE c.id = category_id LIMIT 1) LIKE '%{$s_search}%'";
	
			$where .=' AND ('.implode(' OR ',$s_where).')';
		}
		
		if($search['currency_type']>-1){
			$where.= " AND curr_type = ".$search['currency_type'];
		}
		if(!empty($search['category_id'])){
			$where.= " AND category_id = ".$search['category_id'];
		}
		if(!empty($search['branch_id'])){
			if($search['branch_id']>0){
				$where.=" AND branch_id= ".$search['branch_id'];
			}
    	}
    	
		$dbp = new Application_Model_DbTable_DbGlobal();
		$where.=$dbp->getAccessPermission('branch_id');
		
		$order=" order by id desc ";
		return $db->fetchAll($sql.$where.$order);
	}
	
	function closingExpense($data){
		$db = $this->getAdapter();
		if(!empty($data['id_selected'])){
			$ids = explode(',', $data['id_selected']);
			$key = 1;
			$arr = array(
					"is_closed"=>1,
			);
			foreach ($ids as $i){
				$this->_name="ln_income_expense";
				$where="id= ".$i;
				$this->update($arr, $where);
			}
		}
	}
}