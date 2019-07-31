<?php
class Tellerandexchange_Model_DbTable_DbIncome extends Zend_Db_Table_Abstract
{
	protected $_name = 'ln_income';
	public function getUserId(){
		$session_user=new Zend_Session_Namespace(SYSTEM_SES);
		return $session_user->user_id;
	
	}
	function getInvoiceNo($branch_id){
		$db = $this->getAdapter();
		$sql = " SELECT count(id) FROM ln_income WHERE branch_id = $branch_id";
		$amount = $db->fetchOne($sql);
		$pre = 'inc1:';
		$result = $amount + 1;
		$length = strlen((int)$result);
		for($i = $length;$i < 4 ; $i++){
			$pre.='0';
		}
		return $pre.$result;
	}
	
	function addIncome($data){
		$_db= $this->getAdapter();
		$_db->beginTransaction();
		try{
			$reciept_no = $this->getInvoiceNo($data['branch_id']);
			$data = array(
					'branch_id'=>$data['branch_id'],
					'reciept_no'=>$reciept_no,
					
					'curr_type'=>$data['currency_type'],
					'date'=>$data['Date'],
					'category_id'	=>$data['category_id'],
					'invoice'=>$data['invoice'],
					'account_id'=>$data['account_id'],
					'total_amount'=>$data['total_amount'],
					'tran_type'=>1,
					'disc'=>$data['Description'],
					'status'=>1,
					'create_date'	=>date("Y-m-d H:i:s"),
					'modify_date'	=>date("Y-m-d H:i:s"),
					'user_id'=>$this->getUserId()
					
			);
			$id=$this->insert($data);
			$_db->commit();
			return $id;
		}catch(Exception $e){
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
 	}
	 function updatIncome($data){
	 	$_db= $this->getAdapter();
	 	$_db->beginTransaction();
	 	try{
			$arr = array(
					'branch_id'=>$data['branch_id'],
					
					'curr_type'=>$data['currency_type'],
					'date'=>$data['Date'],
					'category_id'	=>$data['category_id'],
					'invoice'=>$data['invoice'],
					'account_id'=>$data['account_id'],
					'total_amount'=>$data['total_amount'],
					'tran_type'=>1,
					'disc'=>$data['Description'],
					'status'=>$data['Stutas'],
					'modify_date'	=>date("Y-m-d H:i:s"),
					'user_id'=>$this->getUserId()
			);
			$where=" id = ".$data['id'];
			$this->update($arr, $where);
			$id = $data['id'];
			
			$_db->commit();
			return $id;
		}catch(Exception $e){
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	}
	
	function getexpensebyid($id){
		$db = $this->getAdapter();
		$sql=" SELECT * FROM $this->_name WHERE id=$id ";
		return $db->fetchRow($sql);
	}

	function getAllIncome($search=null){
		$db = $this->getAdapter();
		$from_date =(empty($search['start_date']))? '1': " date >= '".$search['start_date']." 00:00:00'";
		$to_date = (empty($search['end_date']))? '1': " date <= '".$search['end_date']." 23:59:59'";
		$where = " WHERE ".$from_date." AND ".$to_date;
		
		$sql=" SELECT id,
		(SELECT branch_namekh FROM `ln_branch` WHERE ln_branch.br_id =branch_id LIMIT 1) AS branch_name,
		reciept_no,
		invoice,
		(SELECT c.title FROM `ln_income_expense_category` AS c WHERE c.id = category_id LIMIT 1) AS category,
		account_id,
		(SELECT symbol FROM `ln_currency` WHERE ln_currency.id =curr_type) AS currency_type, invoice,
		total_amount,disc,date  ";
		
		$dbp = new Application_Model_DbTable_DbGlobal();
		$sql.=$dbp->caseStatusShowImage("status");
		$sql.=" FROM $this->_name ";
		
		if (!empty($search['adv_search'])){
			$s_where = array();
			$s_search = str_replace(' ', '', addslashes(trim($search['adv_search'])));
			$s_where[] = "REPLACE(account_id,' ','')  	LIKE '%{$s_search}%'";
			$s_where[] = "REPLACE(title,' ','')  		LIKE '%{$s_search}%'";
			$s_where[] = "REPLACE(total_amount,' ','')  LIKE '%{$s_search}%'";
			$s_where[] = "REPLACE(invoice,' ','')  		LIKE '%{$s_search}%'";
			$where .=' AND ('.implode(' OR ',$s_where).')';
		}
		if($search['status']>-1){
			$where.= " AND status = ".$search['status'];
		}
		if($search['currency_type']>-1){
			$where.= " AND curr_type = ".$search['currency_type'];
		}
		if(!empty($search['category_id'])){
			$where.= " AND category_id = ".$search['category_id'];
		}
	    $order=" order by id desc ";
		return $db->fetchAll($sql.$where.$order);
	}



}