<?php

class Group_Model_DbTable_DbReturnCollteral extends Zend_Db_Table_Abstract
{
    protected $_name = 'ln_return_collteral';
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace(SYSTEM_SES);
    	return $session_user->user_id;
    }
    function addReturnCollteral($data){
    	$db=$this->getAdapter();
    	$db->beginTransaction();
    	try {
    		
    		$_arr=array(
    				'branch_id'		=>$data['branch_id'],
    				'loan_id'		=>$data['loan_number'],
    				'client_id'		=>$data['client_name'],
    				'giver_name'	=>$data['receiver_name'],
    				'receiver_name'	=>$data['giver_name'],
    				'date'			=>$data['date'],
    				'user_id'		=>$this->getUserId(),
    				'note'			=>$data['_note'],
    				'status'			=>1,
    				'create_date'			=>date("Y-m-d H:i:s"),
    				'modify_date'			=>date("Y-m-d H:i:s"),
    		);
    		$this->_name='ln_return_collteral';
    		$return_id = $this->insert($_arr);
    		
    		$ids =  explode(',', $data['record_row']);
    		if (!empty($ids)){
	    		foreach($ids as $i){
	    		     $this->_name='ln_return_collteral_detail';
					 $array=array(
					 		'return_id'			=>$return_id,
					 		'collect_type'		=>$data['collect_type'.$i],
					 		'owner_type'		=>$data['owner_type'.$i],
					 		'owner_name'		=>$data['owner_name'.$i],
					 		'number_collteral'	=>$data['number_collteral'.$i],
					 		'issue_date'		=>$data['issue_date'.$i],
					 		'note'				=>$data['note'.$i],
					 		'collteraldetail_id' =>$data['coid'.$i]
					 		);
				  	$this->insert($array);
	    			
	    			
	    			$this->_name='ln_client_callecteral_detail';//what relationship
	    			$array = array(
	    					'note'		=>'return by change collateral',
	    					'is_return'	=>1
	    			);
	    			$where = " id = ".$data['coid'.$i];
	    			$this->update($array, $where);
	    			
	    		}
    		}

    	    $db->commit();
    	    return $return_id;
    	}catch (Exception $e){
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    		$db->rollBack();
    	}
	}
	function updateReturnCollteral($data){
		$db=$this->getAdapter();
		$db->beginTransaction();
		try {
			$id = $data['id'];
			$oldrecord = $this->getAllReturnCollateralDetail($id);
			if (!empty($oldrecord)){
				$this->_name='ln_client_callecteral_detail';//what relationship
				foreach ($oldrecord as $old){
					$array = array(
							'note'		=>'',
							'is_return'	=>0
					);
					$where = " id = ".$old['collteraldetail_id'];
					$this->update($array, $where);
				}
			}
			
			$_arr=array(
					'branch_id'		=>$data['branch_id'],
					'loan_id'		=>$data['loan_number'],
					'client_id'		=>$data['client_name'],
					'giver_name'	=>$data['giver_name'],
					'receiver_name'	=>$data['receiver_name'],
					'date'			=>$data['date'],
					'user_id'		=>$this->getUserId(),
					'note'			=>$data['_note'],
					'status'		=>$data['Stutas'],
					'modify_date'	=>date("Y-m-d H:i:s"),
					
			);
			$this->_name = "ln_return_collteral";
			$where=" id = ".$data['id'];
			$this->update($_arr, $where);
			
			$this->_name='ln_return_collteral_detail';
			$where = 'return_id = '.$data['id'];
			$this->delete($where);
			
			$ids =  explode(',', $data['record_row']);
			if (!empty($ids)){
				foreach($ids as $i){
					$this->_name='ln_return_collteral_detail';
					$array=array(
							'return_id'			=>$data['id'],
							'collect_type'		=>$data['collect_type'.$i],
							'owner_type'		=>$data['owner_type'.$i],
							'owner_name'		=>$data['owner_name'.$i],
							'number_collteral'	=>$data['number_collteral'.$i],
							'issue_date'		=>$data['issue_date'.$i],
							'note'				=>$data['note'.$i],
							'collteraldetail_id'				=>$data['coid'.$i]
					);
					$this->insert($array);
			
					$this->_name='ln_client_callecteral_detail';//what relationship
					$array = array(
							'note'		=>'return by change collateral',
							'is_return'	=>1
					);
					$where = " id = ".$data['coid'.$i];
					$this->update($array, $where);
			
				}
			}
			$db->commit();
			
			return $data['id'];
		}catch (Exception $e){
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    		$db->rollBack();
		}
	}
	function getReturnCollteralbyid($id){
		$db = $this->getAdapter();
		$db->beginTransaction();
		try {
			$sql=" SELECT 
				    *,note AS return_note
			    FROM $this->_name WHERE id = ".$db->quote($id);
			
			$dbp = new Application_Model_DbTable_DbGlobal();
			$sql.=$dbp->getAccessPermission("branch_id");
			$sql.=" LIMIT 1";
			return $db->fetchRow($sql);
		}catch (Exception $e){
			$db->rollBack();
		}
	}
	function getAllReturnCollteral($search=null){
		$db=$this->getAdapter();
		$db->beginTransaction();
		try {
			$from_date =(empty($search['start_date']))? '1': " rc.date >= '".$search['start_date']." 00:00:00'";
			$to_date = (empty($search['end_date']))? '1': " rc.date <= '".$search['end_date']." 23:59:59'";
			$where = " WHERE ".$from_date." AND ".$to_date;
			
			$sql=" SELECT rc.id,
			(SELECT branch_namekh FROM ln_branch WHERE br_id = rc.branch_id LIMIT 1) AS branch_id,
			(SELECT l.loan_number FROM `ln_loan` AS l WHERE l.id=rc.loan_id LIMIT 1) as loan_number,
			(SELECT CONCAT(COALESCE(client_number,'')) FROM ln_client AS c WHERE c.client_id=rc.client_id LIMIT 1) AS client_number, 
			(SELECT CONCAT(name_kh,' ',name_en) FROM ln_client AS c WHERE c.client_id=rc.client_id LIMIT 1) AS client_name, 
			rc.giver_name,rc.receiver_name
			      ,rc.date,rc.note,
			       (SELECT user_name FROM rms_users WHERE id=rc.user_id LIMIT 1) AS user_id
			         ";
			
			$dbp = new Application_Model_DbTable_DbGlobal();
			$sql.=$dbp->caseStatusShowImage("rc.status");
			$sql.=" FROM `ln_return_collteral` AS rc ";
			
			if($search['status_search']>-1){
				$where.=" AND rc.status=".$search['status_search'];
			}
			if(!empty($search['branch_id'])){
				$where.=" AND rc.branch_id=".$search['branch_id'];
			}
			if(!empty($search['adv_search'])){
				$s_where=array();
				$s_search = str_replace(' ', '', addslashes(trim($search['adv_search'])));
				$s_where[]="REPLACE(rc.giver_name,' ','')     LIKE '%{$s_search}%'";
				$s_where[]="REPLACE(rc.receiver_name,' ','')  LIKE '%{$s_search}%'";
				$s_where[]="REPLACE(rc.note,' ','')           LIKE '%{$s_search}%'";
				$s_where[]="REPLACE((SELECT CONCAT(name_kh,' ',name_en) FROM ln_client AS c WHERE c.client_id=rc.client_id LIMIT 1),' ','')
				           LIKE '%{$s_search}%'";
				$s_where[]="REPLACE((SELECT CONCAT(client_number) FROM ln_client AS c WHERE c.client_id=rc.client_id LIMIT 1),' ','')
				LIKE '%{$s_search}%'";
				$where .=' AND ('.implode(' OR ',$s_where).')';
			}

            $order = " ORDER BY rc.id DESC";
            $where.=$dbp->getAccessPermission("rc.branch_id");
			return $db->fetchAll($sql.$where.$order);
			$db->commit();
		}catch (Exception $e){
			$db->rollBack();
		}
	}
	function getAllReturnCollateralDetail($id){//ajax
		$db = $this->getAdapter();
		$sql = "SELECT * FROM ln_return_collteral_detail WHERE return_id = $id AND status=1 ";
		return $db->fetchAll($sql);
	}
	
	public static function getCallteralCode(){
		$db = new Application_Model_DbTable_DbGlobal();
		$sql = "SELECT COUNT(id) AS amount FROM `ln_client_callecteral`";
		$acc_no= $db->getGlobalDbRow($sql);
		$acc_no=$acc_no['amount'];
		$new_acc_no= (int)$acc_no+1;
		$acc_no= strlen((int)$acc_no+1);
		$pre = "";
		for($i = $acc_no;$i<6;$i++){
			$pre.='0';
		}
		return "CL".$pre.$new_acc_no;
	}
	function getOwnerInfo($id){//ajax
		$db = $this->getAdapter();
		$sql = "SELECT id,(SELECT name_en FROM ln_client WHERE client_id=client_name) AS client_name,owner,
			(SELECT title_kh FROM ln_callecteral_type WHERE id=callate_type) AS collteral_type,
			callate_type,(SELECT id FROM `ln_changecollteral` WHERE id=$id) AS changecollteral_id,
			number_collteral FROM `ln_client_callecteral` WHERE id=$id AND status=1  LIMIT 1";
		return $db->fetchRow($sql);
	}
}

