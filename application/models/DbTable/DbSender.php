<?php

class Application_Model_DbTable_DbSender extends Zend_Db_Table_Abstract
{

    protected $_name = 'cs_sender';
    
	//function get user info from database
	public function addSender($_data)//submit by from
	{	
		$_arr = array(
				"sender_name" => $_data["sender_name"],
				"tel"		  => $_data["tel"],
				"email"		  => $_data["email"],
				"address"	  => $_data["address"],
				"status"	  => $_data["status"]
				);
		$this->insert($_arr);
			
	}
	public function updateSender($_data)//submit by from
	{
		//print_r($_data);exit();
		
		$_arr = array(
				"sender_name" => $_data["sender_name"],
				"tel"		  => $_data["tel"],
				"email"		  => $_data["email"],
				"address"	  => $_data["address"],
				"status"	  => $_data["status"]
		);
		$where = $this->getAdapter()->quoteInto("sender_id=?", $_data['id']);
		$this->update($_arr, $where);
			
	}
	public function addNewSender($_data)//submit by ajax
	{
		$db = $this->getAdapter();
		$address = "";
		$sender_type=0;$acc_no=0;
		if(!empty($_data["address"])){
			$address = $_data["address"];
		}
		if(!empty($_data["sender_type"])){
			$sender_type = $_data["sender_type"];
		}
		if(!empty($_data["acc_no"])){
			$acc_no = $_data["acc_no"];
			$sql = "SELECT acc_no FROM cs_sender where sender_type=1 AND acc_no='".$_data["acc_no"]."' LIMIT 1";
			$rs_acc = $db->fetchRow($sql);
			if(!empty($rs_acc)){
				$db = new Application_Model_DbTable_DbKbank();
				$acc_no = $db ->getAccountNumberForKBank();
			}
			
		}
		$sql = "SELECT sender_name FROM cs_sender where sender_type=$sender_type AND sender_name='".$_data["sender_name"]."' LIMIT 1";
		$rs = $db->fetchRow($sql);
		if(!empty($rs)){
			return -1;
		}
		
		
		$_arr = array(
				"sender_name" => $_data["sender_name"],
				"tel"		  => $_data["sender_tel"],
				"acc_no"	  => $acc_no,
				"address"     => $address,
				"sender_type" => $sender_type,
		);
		$id=$this->insert($_arr);
		return $id;
	}
	public function _buildQuery($search = ''){
		$sql = "SELECT sender_id, sender_name,tel,email,address,status FROM cs_sender WHERE sender_name!='' " ;
		
			if(!empty($search['txtsearch'])){
				$sql.=" AND sender_name LIKE '%".$search['txtsearch']."%' ";
			}
// 			$status = $search["active"];
// 			if($status>-1){
// 				$sql.=" AND status ='".$search['active']."'";
// 			}

		
		//echo $sql;
		return $sql;
	}
	public function getAllSenderList($search, $start, $limit){
		$db = $this->getAdapter();
		$sql = $this->_buildQuery($search)." LIMIT ".$start.", ".$limit;
		if ($limit == 'All') {
			$sql = $this->_buildQuery($search);
		}
		
		//echo $sql;exit();
		return $db->fetchAll($sql);
	}
	public function getCountallSender($search = ''){//for count recode has select 
		$db = $this->getAdapter();
		$sql = $this->_buildQuery();
		if(!empty($search)){
			$sql = $this->_buildQuery($search);
		}
		$_result = $db->fetchAll($sql);
		//echo count($_result);exit();
		return count($_result);
	}
	public function getAllSenderName(){
		$db = $this->getAdapter();
		$sql = " SELECT DISTINCT sender_name AS id,sender_name as name FROM cs_sender
		WHERE status=1 AND sender_name!='' " ;
		//print_r($db->fetchAll($sql));
		//exit();
		return $db->fetchAll($sql);
	}
	public function getAllSenderKbank($sender_type=null){
		$db = $this->getAdapter();
		$type = 1;
		if($sender_type==null){
			$sql = " SELECT sender_id AS id,sender_name as name,acc_no AS acc_code,tel FROM cs_sender
			WHERE status=1 AND sender_name!='' " ;
		}else{
			$sql = " SELECT sender_id AS id,sender_name as name,acc_no AS acc_code,tel FROM cs_sender
			WHERE status=1 AND sender_name!='' AND sender_type= $sender_type " ;
		}
		
		return $db->fetchAll($sql);
	}
	public function getAllOwerName(){
		$db = $this->getAdapter();
		$sql = " SELECT 
				  DISTINCT sender_name AS id,
				  sender_name AS name 
				FROM
				  cs_transactions_owe 
				WHERE t.`status` = 0 AND `status_loan` != 3 
				  AND sender_name != '' " ;
		//print_r($db->fetchAll($sql));
		//exit();
		
		return $db->fetchAll($sql);
	}
	public function getAllSenderNameOwe(){//add sender owe and fund
		$db = $this->getAdapter();
		$sql = "SELECT DISTINCT
				s.sender_name AS id,
				s.sender_name AS `name`
				FROM
				cs_transactions_owe AS t
				INNER JOIN cs_sender AS s ON s.sender_id = t.sender_name
				WHERE
				t.`status` = 0 AND 
				t.status_loan != 3 AND
				t.sender_name != '0'
				ORDER BY
				t.sender_name ASC" ;
		//print_r($db->fetchAll($sql));
		//exit();
	
		return $db->fetchAll($sql);
	}
	public function getAllSenderPayout(){//add sender from borrow loan
		$db = $this->getAdapter();
		$sql = "SELECT DISTINCT
				s.sender_id AS id,
				s.sender_name AS `name`
				FROM
				cs_payout AS b
				INNER JOIN cs_sender AS s ON s.sender_id = b.sender_id
				WHERE
				b.po_status = 0
				ORDER BY
				`name` ASC" ;
		return $db->fetchAll($sql);
	}
	public function getAllSenderBorrow(){//add sender from borrow loan
		$db = $this->getAdapter();
		$sql = "SELECT DISTINCT
				s.sender_id AS id,
				s.sender_name AS `name`
				FROM
				cs_borrow AS b
				INNER JOIN cs_sender AS s ON s.sender_id = b.sender_id
				WHERE
				b.delete_status = 0 AND
				b.`status` <> 3
				ORDER BY
				`name` ASC" ;
	
		return $db->fetchAll($sql);
	}
	public function getAllSenderNameFound(){//add sender owe and fund
		$db = $this->getAdapter();
		$sql = "SELECT DISTINCT
				s.sender_id AS id,
				s.sender_name AS `name`
				FROM
				cs_trans_found AS f
				INNER JOIN cs_sender AS s ON s.sender_id = f.sender_id
				WHERE
				f.`status` = 0
				ORDER BY
				s.sender_name ASC" ;
	
		return $db->fetchAll($sql);
	}
	
	public function getAllSenderNameOweAll(){//add sender owe and fund
		$db = $this->getAdapter();
		$sql = "SELECT DISTINCT
				s.sender_name AS id,
				s.sender_name AS `name`
				FROM
				cs_transactions_owe AS t
				INNER JOIN cs_sender AS s ON s.sender_id = t.sender_name
				WHERE
				t.`status` = 0 AND 
				t.sender_name != '0'
				ORDER BY
				t.sender_name ASC" ;
		//print_r($db->fetchAll($sql));
		//exit();
	
		return $db->fetchAll($sql);
	}
	public function getSenderNameById($id){
		$db = $this->getAdapter();
		$sql = "SELECT * FROM cs_sender
		WHERE sender_id = ".$id ;
		return $db->fetchRow($sql);
	}
    public function getSenderIdbyInvoice($invoice_found){
    	$db = $this->getAdapter();
    	$sql = "SELECT
				s.sender_id
				FROM
				cs_transactions_owe AS t
				INNER JOIN cs_sender AS s ON s.sender_id = t.sender_name
				WHERE
				t.invoice_no = '".$invoice_found."' LIMIT 1";
    	return $db->fetchOne($sql);
    }
    public function getTranIdbyInvoiceInFund($invoice_found){
    	$db = $this->getAdapter();
    	$sql = "SELECT
			    tran_id
				FROM
				cs_trans_found
				WHERE
				invoice_found = '".$invoice_found."' LIMIT 1";
    	return $db->fetchOne($sql);
    }

}

