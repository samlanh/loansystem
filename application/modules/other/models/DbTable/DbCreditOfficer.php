<?php

class Other_Model_DbTable_DbCreditOfficer extends Zend_Db_Table_Abstract
{

    protected $_name = 'ln_co';
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('authloan');
    	return $session_user->user_id;
    	 
    }
	public function addCreditOfficer($_data){
		try{
// 			$photoname = str_replace(" ", "_", $_data['first_name']) . '.jpg';
// 			$upload = new Zend_File_Transfer();
// 			$upload->addFilter('Rename',
// 					array('target' => PUBLIC_PATH . '/images/'. $photoname, 'overwrite' => true) ,'photo');
// 			$receive = $upload->receive();
// 			if($receive)
// 			{
// 				$_data['photo'] = $photoname;
// 			}
// 			else{
// 				$_data['photo']="";
// 			}
// 			unset($_data['MAX_FILE_SIZE']);
			$_arr=array(
					'branch_id'	  => $_data['branch_id'],
					'co_code'	  => $_data['co_id'],
					'co_khname'	  => $_data['name_kh'],
					'co_firstname'=> $_data['first_name'],
					'co_lastname' => '',//$_data['last_name'],
					'position_id' =>$_data['position'],
					'sex'		  => $_data['co_sex'],
					'national_id'	  => $_data['national_id'],
					'address'	  => $_data['address'],
					'pob'	      => $_data['pob'],
					'degree'	      => $_data['degree'],
					'tel'	  	  => $_data['tel'],
					'email'	      => $_data['email'],
					'create_date' => Zend_Date::now(),
// 					'status'      => $_data['status'],
					'user_id'	  => $this->getUserId(),
					'basic_salary'=> $_data['basic_salary'],
					'start_date'  => $_data['start_date'],
					'end_date'	  => $_data['end_date'],
					'contract_no' => $_data['contract_no'],
					'note'		  => $_data['note'],
					'shift'		  => $_data['shift'],
					'workingtime' => $_data['workingtime'],
					'annual_lives'=>$_data['annual_lives'],
// 					'photo'=>$_data['photo'],
					'department_id'=>$_data['department_id'],
					'figer_print_id'=>$_data['figer_print_id'],
			);
			
			$part= PUBLIC_PATH.'/images/profile/co/';
			if (!file_exists($part)) {
				mkdir($part, 0777, true);
			}
			$photo_name = $_FILES['photo']['name'];
			if (!empty($photo_name)){
				$tem =explode(".", $photo_name);
				$image_name_stu = "co_".date("Y").date("m").date("d").time().".".end($tem);
				$tmp = $_FILES['photo']['tmp_name'];
				if(move_uploaded_file($tmp, $part.$image_name_stu)){
					move_uploaded_file($tmp, $part.$image_name_stu);
					$_arr['photo']=$image_name_stu;
				}
			}
			
			if(!empty($_data['id'])){
				$where = 'co_id = '.$_data['id'];
				$_arr['status']=$_data['status'];
				return  $this->update($_arr, $where);
			}else{
				$_arr['status']=1;
				return  $this->insert($_arr);
			}
		}catch(Exception $e){
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
		
	}
	public function addCoByAjax($data){
		try{
			$arr = array(
					'co_khname'	  => $data['last_name'],
					'co_firstname'=> $data['first_name'],
					'co_lastname' => $data['last_name'],
					'displayby'	  => 1,
					'position_id' =>1,
					'sex'		  => $data['co_sex'],
					'tel'	  	  => $data['tel'],
					'email'	      => $data['email'],
					'create_date' => Zend_Date::now(),
					'status'      => 1,
					'user_id'	  => $this->getUserId(),
					'basic_salary'=> 0,
			);
			return $this->insert($arr);
		}catch(Exception $e){
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
		
	}
	public function getCOById($id){
		$db = $this->getAdapter();
		$sql = "SELECT * FROM $this->_name WHERE co_id = ".$db->quote($id);
		$sql.=" LIMIT 1 ";
		$row=$db->fetchRow($sql);
		return $row;
	}
	function getAllCreditOfficer($search=null){
		$db = $this->getAdapter();
		$sql = "SELECT 
					co_id,
					(SELECT branch_namekh FROM `ln_branch` WHERE br_id =branch_id LIMIT 1) AS branch_name ,
					co_code,co_khname,CONCAT(co_khname) AS co_engname,national_id,address,
					tel,email,address,(SELECT name_kh FROM ln_view WHERE type=20 AND key_code=degree) AS degree,
					(SELECT department_kh FROM ln_department WHERE id=department_id) AS department_id,
					annual_lives ";
		
		$dbp = new Application_Model_DbTable_DbGlobal();
		$sql.=$dbp->caseStatusShowImage("status");
		$sql.=" FROM $this->_name WHERE 1 ";
		
		$order=" ORDER BY co_id DESC";
		$where = '';
		
		if($search['status_search']>-1){
			$where.= " AND status = ".$search['status_search'];
		}
		if(!empty($search['degree'])){
			$where.=" AND degree = ".$search['degree'];
		}
		if(!empty($search['branch_id'])){
			$where.=" AND branch_id = ".$search['branch_id'];
		}
		if(!empty($search['adv_search'])){
			$s_where = array();
			$s_search = str_replace(' ', '', addslashes(trim($search['adv_search'])));
			$s_where[] = "REPLACE(co_khname,' ','') 	 	LIKE '%{$s_search}%'";
			$s_where[] = "REPLACE(co_firstname,' ','') 	 	LIKE '%{$s_search}%'";
			$s_where[] = "REPLACE(co_lastname,' ','')  		LIKE '%{$s_search}%'";
			$s_where[]= "REPLACE(national_id,' ','')  		LIKE '%{$s_search}%'";
			$s_where[] = "REPLACE(tel,' ','')   			LIKE '%{$s_search}%'";
			$s_where[] = "REPLACE(email,' ','')  			LIKE '%{$s_search}%'";
			$s_where[] = "REPLACE(address,' ','')  			LIKE '%{$s_search}%'";
			$s_where[]="REPLACE(annual_lives,' ','')  		LIKE '%{$s_search}%'";
			$where .=' AND ('.implode(' OR ',$s_where).')';
		}
		return $db->fetchAll($sql.$where.$order);	
	}	
}

