<?php

class Installment_Model_DbTable_DbClient extends Zend_Db_Table_Abstract
{

    protected $_name = 'ln_ins_client';
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace(SYSTEM_SES);
    	return $session_user->user_id;
    	 
    }
	public function addClient($_data){
		$photoname = str_replace(" ", "_","ins".$_data['name_en']) . '.jpg';
		$upload = new Zend_File_Transfer();
		$upload->addFilter('Rename',
				array('target' => PUBLIC_PATH . '/images/'. $photoname, 'overwrite' => true) ,'photo');
		$receive = $upload->receive();
		if($receive)
		{
			$_data['photo'] = $photoname;
		}
		else{
			$_data['photo']="";
		}
		try{
		$client_code=$_data['client_no'];
		$_arr=array(
				'branch_id'	  => $_data['branch_id'],
				'client_number'=> $client_code,
				'name_kh'	  => $_data['name_kh'],
				'name_en'	  => $_data['name_en'],
				'sex'	      => $_data['sex'],
				'phone'	      => $_data['phone'],
				'client_d_type'      => $_data['client_d_type'],
				'nation_id'=>$_data['national_id'],
				'dob'			=>$_data['dob_client'],
				'job'        =>$_data['job'],
				'sit_status'  => $_data['situ_status'],
				'photo_name'  =>$_data['photo'],
				'pro_id'      => $_data['province'],
				'dis_id'      => $_data['district'],
				'com_id'      => $_data['commune'],
				'village_id'  => $_data['village'],
				'street'	  => $_data['street'],
				'house'	      => $_data['house'],
				
				'guarantor_name' => $_data['spouse'],
				'guarantor_gender' => $_data['guarantor_gender'],
				'guarantor_d_type'=> $_data['guarantor_d_type'],
				'guarantor_nationid'=>$_data['spouse_nationid'],
				'dob_guarantor'=>$_data['dob_guarantor'],
				'guarantor_tel'=>$_data['guarantor_tel'],
				'guarantor_with'=>$_data['guarantor_with'],
				'guarantor_address'=> $_data['guarantor_address'],
				'remark'	  => $_data['desc'],
				'create_date' => date("Y-m-d"), 
				'status'      => $_data['status'],
				'user_id'	  => $this->getUserId(),
		);
// 		print_r($_data['guarantor_d_type']);exit();
		if(!empty($_data['id'])){
			$where = 'client_id = '.$_data['id'];
			$this->update($_arr, $where);
			return $_data['id'];
		}else{
			return  $this->insert($_arr);
		}
		}catch(Exception $e){
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	}
	public function getClientById($id){
		$db = $this->getAdapter();
		$sql = "SELECT * FROM $this->_name WHERE client_id = ".$db->quote($id);
		$dbp = new Application_Model_DbTable_DbGlobal();
		$sql.=$dbp->getAccessPermission('branch_id');
		$sql.=" LIMIT 1 ";
		$row=$db->fetchRow($sql);
		return $row;
	}
	public function getClientDetailInfo($id){
		$db = $this->getAdapter();
		$sql = "SELECT c.*, (SELECT commune_namekh FROM `ln_commune` WHERE com_id = c.com_id   LIMIT 1) AS commune_name
		,(SELECT district_namekh FROM `ln_district` AS ds WHERE dis_id = c.dis_id  LIMIT 1) AS district_name
		,(SELECT province_kh_name FROM `ln_province` WHERE province_id= c.pro_id  LIMIT 1) AS province_en_name
		,(SELECT village_namekh FROM `ln_village` WHERE vill_id = c.village_id  LIMIT 1) AS village_name ,
		(SELECT name_en FROM `ln_view` WHERE TYPE = 5 AND key_code = c.sit_status) AS sit_status , 
		(SELECT branch_nameen FROM `ln_branch` WHERE br_id =c.branch_id LIMIT 1) AS branch_name ,
		(SELECT name_kh FROM `ln_view` WHERE TYPE = 11 AND key_code =c.sex) AS sex ,
		(SELECT name_kh FROM `ln_view` WHERE TYPE = 23 AND id =c.`client_d_type`) AS client_d_type ,
		(SELECT name_kh FROM `ln_view` WHERE TYPE = 23 AND id =c.`guarantor_d_type`) AS guarantor_d_type 
		 FROM `ln_ins_client` AS c WHERE client_id =  ".$db->quote($id);
		
		$dbp = new Application_Model_DbTable_DbGlobal();
		$sql.=$dbp->getAccessPermission('branch_id');
		
		$sql.=" LIMIT 1 ";
// 		(SELECT name_kh FROM `ln_view` WHERE TYPE = 23 AND key_code =c.`client_d_type`) AS client_d_type ,
// 		(SELECT name_kh FROM `ln_view` WHERE TYPE = 23 AND key_code =c.`guarantor_d_type`) AS guarantor_d_type
		$row=$db->fetchRow($sql);
		return $row;
	}
	public function getClientCallateralBYId($client_id){
		$db = $this->getAdapter();
		$sql = " SELECT cc.id AS client_coll ,cd.* FROM `ln_ins_client_callecteral` AS cc , `ln_ins_client_callecteral_detail` AS cd WHERE  
		         cd.is_return=0 AND cd.client_coll_id = cc.id AND cc.client_id = ".$client_id;
		return $db->fetchAll($sql);
	}
    function getViewClientByGroupId($group_id){
    	$db = $this->getAdapter();
    	$sql=" SELECT * FROM $this->_name WHERE client_id=
    	(SELECT customer_id FROM `ln_loan` WHERE customer_id=".$db->quote($group_id)." LIMIT 1)";
    	$row=$db->fetchRow($sql);
    	return $row;
    }
	function getAllClients($search = null){		
		$db = $this->getAdapter();
		$from_date =(empty($search['start_date']))? '1': "create_date >= '".$search['start_date']." 00:00:00'";
		$to_date = (empty($search['end_date']))? '1': "create_date <= '".$search['end_date']." 23:59:59'";
		$where = " WHERE (name_kh!='' OR  name_en!='') AND ".$from_date." AND ".$to_date;		
		$sql = "SELECT client_id,
		(SELECT branch_namekh FROM `ln_branch` WHERE br_id =branch_id LIMIT 1) AS branch_name ,
		client_number,name_kh,name_en,
		(SELECT name_en FROM `ln_view` WHERE TYPE =11 AND sex=key_code LIMIT 1) AS sex
		,phone,house,street,
		(SELECT village_namekh FROM `ln_village` WHERE vill_id=village_id) AS village_name
		    ,guarantor_name,guarantor_tel,
		    create_date,
		    (SELECT  CONCAT(first_name,' ', last_name) FROM rms_users WHERE id=user_id )AS user_name
			  ";
		$dbp = new Application_Model_DbTable_DbGlobal();
		$sql.=$dbp->caseStatusShowImage("status");
		$sql.=" FROM $this->_name ";
		
		if(!empty($search['adv_search'])){
			$s_where = array();
			$s_search = str_replace(' ', '', addslashes(trim($search['adv_search'])));
			$s_where[] = "REPLACE(client_number,' ','') 	LIKE '%{$s_search}%'";
			$s_where[] = "REPLACE(name_en,' ','')   		LIKE '%{$s_search}%'";
			$s_where[] = "REPLACE(name_kh,' ','')   		LIKE '%{$s_search}%'";
			$s_where[] = "REPLACE(phone,' ','')   			LIKE '%{$s_search}%'";
			$s_where[] = "REPLACE(house,' ','')   			LIKE '%{$s_search}%'";
			$s_where[] = "REPLACE(street,' ','')  	 		LIKE '%{$s_search}%'";
			$s_where[] = "REPLACE(guarantor_name,' ','')  		LIKE '%{$s_search}%'";
			$where .=' AND ('.implode(' OR ',$s_where).')';
		}
		if($search['status']>-1){
			$where.= " AND status = ".$search['status'];
		}
		if($search['province_id']>0){
			$where.=" AND pro_id= ".$search['province_id'];
		}
		if(!empty($search['district_id'])){
			$where.=" AND dis_id= ".$search['district_id'];
		}
		if(!empty($search['comm_id'])){
			$where.=" AND com_id= ".$search['comm_id'];
		}
		if(!empty($search['village'])){
			$where.=" AND village_id= ".$search['village'];
		}
		$where.=$dbp->getAccessPermission('branch_id');
		
		$order=" ORDER BY client_id DESC";
		return $db->fetchAll($sql.$where.$order);	
	}
	public function getGroupCodeBYId($data){
		$db = $this->getAdapter();
		if($data['is_group']==1){
			$sql = "SELECT COUNT(client_id) AS number FROM `ln_ins_client`
			      WHERE is_group =1 AND branch_id = ".$data['branch_id'] ;
			    $acc_no = $db->fetchOne($sql);
				$new_acc_no= (int)$acc_no+1;
				$acc_no= strlen((int)$acc_no+1);
				$pre ="G".$this->getPrefixCode($data['branch_id']);
				for($i = $acc_no;$i<3;$i++){
					$pre.='0';
				}
				return $pre.$new_acc_no;
		}else{
			$sql = " SELECT group_code FROM `ln_ins_client`
			WHERE client_id = ".$data['group_id'] ;
			 $rs = $db->fetchOne($sql);
			if(empty($rs)){return ''; }else{
				return $rs;
			}
		}
		
	}
	function getPrefixCode($branch_id){
		$db  = $this->getAdapter();
		$sql = " SELECT prefix FROM `ln_branch` WHERE br_id = $branch_id  LIMIT 1";
		return $db->fetchOne($sql);
	}	
	public function getClientCode($branch_id){//for get client by branch
		$db = $this->getAdapter();
			$sql = "SELECT (client_id) AS number FROM `ln_ins_client`
			WHERE branch_id = ".$branch_id." ORDER BY client_id DESC ";
		$acc_no = $db->fetchOne($sql);
		$new_acc_no= (int)$acc_no+1;
		$acc_no= strlen((int)$acc_no+1);
		$pre =$this->getPrefixCode($branch_id);
		for($i = $acc_no;$i<6;$i++){
			$pre.='0';
		}
		return $pre.$new_acc_no;
	}
	public function addIndividaulClient($_data){
		
		$client_code = $this->getClientCode($_data['branch_id']);
			$_arr=array(
					'is_group'=>0,
					'group_code'=>'',
					'parent_id'=>0,
					'client_number'=>$client_code,
					'name_kh'	  => $_data['name_kh'],
					'name_en'	  => $_data['name_en'],
					'sex'	      => $_data['sex'],
					'sit_status'  => $_data['situ_status'],
					'dis_id'      => $_data['district'],
					'village_id'  => $_data['village'],
					'street'	  => $_data['street'],
					'house'	      => $_data['house'],
					'branch_id'  => $_data['branch_id'],
					'job'        =>$_data['job'],
					'phone'	      => $_data['phone'],
					'create_date' => date("Y-m-d"),
					'client_d_type'      => $_data['client_d_type'],
					'user_id'	  => $this->getUserId(),
					'dob'			=>$_data['dob_client'],	
					'pro_id'      => $_data['province'],
					'com_id'      => $_data['commune'],
			);
				$this->_name = "ln_ins_client";
				$id =$this->insert($_arr);
				return array('id'=>$id,'client_code'=>$client_code);
	}
	
	
	public function getAllOutstadingLoan($customerId){
		$db = $this->getAdapter();
		$where="";
		$sql="SELECT
		(SELECT  `ln_branch`.`branch_namekh` FROM `ln_branch`  WHERE (`ln_branch`.`br_id` = `s`.`branch_id`) LIMIT 1) AS `branch_name`,
		c.client_number AS `client_number`,
		c.name_kh AS `client_kh`,
		c.`client_number` AS client_number,
		c.name_en AS `client_en`,
		(SELECT inp.item_name FROM `ln_ins_product` AS inp WHERE inp.id = s.`product_id` LIMIT 1) AS item_name,
		(SELECT inc.name FROM `ln_ins_category` AS inc WHERE inc.id = s.`cate_id` LIMIT 1) AS cateName,
		s.*,
		(SELECT  `ln_ins_receipt_money`.`paid_times` FROM `ln_ins_receipt_money` WHERE ((`ln_ins_receipt_money`.`status` = 1) AND (`s`.`id` = `ln_ins_receipt_money`.`loan_id`))
		ORDER BY `ln_ins_receipt_money`.`paid_times` DESC
		LIMIT 1) AS `installment_amount`,
		(SELECT
		SUM(`ln_ins_receipt_money`.`principal_paid`)
		FROM `ln_ins_receipt_money`
		WHERE ((`ln_ins_receipt_money`.`status` = 1)
		AND (`s`.`id` = `ln_ins_receipt_money`.`loan_id`))) AS `total_principaid`,
		(SELECT
		SUM(`ln_ins_receipt_money`.`interest_paid`)
		FROM `ln_ins_receipt_money`
		WHERE ((`ln_ins_receipt_money`.`status` = 1)
		AND (`s`.`id` = `ln_ins_receipt_money`.`loan_id`))) AS `total_interest_paid`,
		(SELECT
		SUM(`ln_ins_receipt_money`.`total_paymentpaid`)
		FROM `ln_ins_receipt_money`
		WHERE ((`ln_ins_receipt_money`.`status` = 1)
		AND (`s`.`id` = `ln_ins_receipt_money`.`loan_id`))) AS `total_paymentpaid`
		FROM
		`ln_ins_sales_install` AS s,
		`ln_ins_client` AS c
		WHERE c.`client_id` = s.`customer_id` AND s.status=1 ";
		$where.=" AND c.`client_id` = $customerId";
		$dbp = new Application_Model_DbTable_DbGlobal();
		$where.=$dbp->getAccessPermission('`s`.`branch_id`');
	
		$order=" ORDER BY s.id DESC";
		return $db->fetchAll($sql.$where.$order);
	}
}

