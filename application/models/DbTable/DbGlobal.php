<?php

class Application_Model_DbTable_DbGlobal extends Zend_Db_Table_Abstract
{
	public function setName($name){
		$this->_name=$name;
	}
	public static function getUserId(){
		$session_user=new Zend_Session_Namespace(SYSTEM_SES);
		return $session_user->user_id;
	}
	function currentlang(){
		$session_lang=new Zend_Session_Namespace('lang');
		return $session_lang->lang_id;
	}
 function getAllLocationByUser($user_id,$branch_name='id'){
//     	$db = $this->getAdapter();
//     	$result = $this->getUserInfo();
//     	if($result['level']==1){
//     		return " 1 ";
//     	}
//     	$sql=" SELECT * FROM `tb_acl_ubranch` WHERE user_id=$user_id ";
//     	$rows = $db->fetchAll($sql);
    	$s_where = array();
    	$where='';
//     	if(!empty($rows)){
//     		foreach ($rows as $rs){
//     			$s_where[] = $branch_name." = {$rs['location_id']}";
//     		}
//     		$where .=' '.implode(' OR ',$s_where).'';
//     	}
    	return $where;
    }
	public function getUserInfo(){
		$session_user=new Zend_Session_Namespace(SYSTEM_SES);
		$userName=$session_user->user_name;
		$GetUserId= $session_user->user_id;
		$level = $session_user->level;
		$location_id = $session_user->location_id;
		$info = array("user_name"=>$userName,"user_id"=>$GetUserId,"level"=>$level,"branch_id"=>$location_id);
		return $info;
	}
	public static function GlobalgetUserId(){
		$session_user=new Zend_Session_Namespace(SYSTEM_SES);
		return $session_user->user_id;
	}
	public function getAccessPermission($branch_str='branch_id'){
		
		$session_user=new Zend_Session_Namespace(SYSTEM_SES);
// 		$currentBranch = $session_user->branch_id;
		$session_user=new Zend_Session_Namespace(SYSTEM_SES);
		$branch_list = $session_user->branch_list;
		
		$currentlevel = $session_user->level;
		$result="";
		$currentBranch = empty($branch_list)?0:$branch_list;
		if($currentlevel==1){
			return $result;
		}
		else{
			//$where = " AND ".$branch." = $currentBranch";
			$result.= " AND $branch_str IN ($currentBranch)";
// 			$sql_string = $this->getAllLocationByUser($result['user_id'],$branch);
// 			$result = " AND (".$sql_string.")";
		}
		return $result;
	}
	function getAllLocation($opt=null){
		$db=$this->getAdapter();
		$sql=" SELECT id,`name` FROM `tb_sublocation` WHERE `name`!='' AND status=1  ";
		$result = $this->getUserInfo();
		$sql.= " AND ".$this->getAllLocationByUser($result['user_id']);
		$row =  $db->fetchAll($sql);
		if($opt==null){
			return $row;
		}else{
			$options=array();
			$request=Zend_Controller_Front::getInstance()->getRequest();
			$module = $request->getModuleName();
			if($module=='report'){
				$options=array(-1=>"Select Location");
			}
			if(!empty($row)) foreach($row as $read) $options[$read['id']]=$read['name'];
			return $options;
		}
	}
	public  function caseStatusShowImage($status="status"){
		$base_url = Zend_Controller_Front::getInstance()->getBaseUrl();
		$imgnone='<img src="'.$base_url.'/images/icon/cross.png"/>';
		$imgtick='<img src="'.$base_url.'/images/icon/apply2.png"/>';
		$string=", CASE
		WHEN  $status = 1 THEN '$imgtick'
		WHEN  $status = 0 THEN '$imgnone'
		END AS status ";
		return $string;
	}
	public function init()
	{
		$this->tr = Application_Form_FrmLanguages::getCurrentlanguage();
	}
	function getCurrentDatePayment($id){
		$db = $this->getAdapter();
		$sql="SELECT c.`date_input` FROM `ln_client_receipt_money` AS c WHERE c.status=1 AND c.`id`=$id ";
		return $db->fetchOne($sql);
	}
	function getLastDatePayment($id){
		$db = $this->getAdapter();
		$sql="SELECT crm.`date_input` FROM `ln_client_receipt_money` AS crm,`ln_client_receipt_money_detail` AS crmd 
		WHERE crm.status =1 AND crm.`id`!=$id AND crm.`id`=(SELECT crl.`crm_id` FROM `ln_client_receipt_money_detail` AS crl WHERE crl.`crm_id`=crm.`id` AND crl.`loan_number`=(SELECT c.loan_number FROM `ln_client_receipt_money_detail` AS c WHERE c.`crm_id`=crmd.id AND c.`crm_id`=$id LIMIT 1) LIMIT 1)  ORDER BY crm.`date_input` DESC LIMIT 1 ";
		return $db->fetchOne($sql);
	}
	public function getLoanNumberByBranch($type){
		$db = $this->getAdapter();
			$sql="SELECT l.`id` AS id,
				CONCAT((SELECT name_kh FROM `ln_client` WHERE client_id = l.customer_id LIMIT 1),'-',l.`loan_number`) AS `name`,
				l.`branch_id` 
				FROM `ln_loan` AS l
				WHERE 
					l.`is_completed` = 0 
			  		AND l.status=1 
			  		ORDER BY l.loan_number DESC ";
			//AND l.is_badloan=0 
			return $db->fetchAll($sql);
	}
	public function getSaleinstallmentByBranch($type){
		$db = $this->getAdapter();
		$sql="SELECT l.`id` AS id,
				CONCAT((SELECT name_kh FROM `ln_ins_client` WHERE client_id = l.customer_id LIMIT 1),'-',l.`sale_no`) AS `name`,
				l.`branch_id`
				FROM `ln_ins_sales_install` AS l
				WHERE
				l.`is_completed` = 0
				AND l.status=1
			ORDER BY l.sale_no DESC ";
		return $db->fetchAll($sql);
	}
	public function getGlobalDb($sql)
  	{
  		$db=$this->getAdapter();
  		$row=$db->fetchAll($sql);  		
  		if(!$row) return NULL;
  		return $row;
  	}
  	public function getGlobalDbRow($sql)
  	{
  		$db=$this->getAdapter();  		
  		$row=$db->fetchRow($sql);
  		if(!$row) return NULL;
  		return $row;
  	}
  	public static function getActionAccess($action)
    {
    	$arr=explode('-', $action);
    	return $arr[0];    	
    }     
    public function isRecordExist($conditions,$tbl_name){
		$db=$this->getAdapter();		
		$sql="SELECT * FROM ".$tbl_name." WHERE ".$conditions." LIMIT 1"; 
		$row= count($db->fetchRow($sql));
		if(!$row) return NULL;
		return $row;	
    }
    /*for select 1 record by id of earch table by using params*/
    public function GetRecordByID($conditions,$tbl_name){
    	$db=$this->getAdapter();
    	$sql="SELECT * FROM ".$tbl_name." WHERE ".$conditions." LIMIT 1";
    	$row = $this->fetchRow($sql);
    	return $row;
    	$row= $db->fetchRow($sql);
    	return $row;
    }
    /**
     * insert record to table $tbl_name
     * @param array $data
     * @param string $tbl_name
     */
    public function addRecord($data,$tbl_name){
    	$this->setName($tbl_name);
    	return $this->insert($data);
    }
    public function updateRecord($data,$id,$tbl_name){
    	$this->setName($tbl_name);
    	$where=$this->getAdapter()->quoteInto('id=?',$id);
    	$this->update($data,$where);    	
    }   
    public function DeleteRecord($tbl_name,$id){
    	$db = $this->getAdapter();
		$sql = "UPDATE ".$tbl_name." SET status=0 WHERE id=".$id;
		return $db->query($sql);
    } 
     public function DeleteData($tbl_name,$where){
    	$db = $this->getAdapter();
		$sql = "DELETE FROM ".$tbl_name.$where;
		return $db->query($sql);
    } 
    public function getDayInkhmerBystr($str){
    	
    	$rs=array(
    			'Mon'=>'ចន្ទ',
    			'Tue'=>'អង្គារ',
    			'Wed'=>'ពុធ',
    			'Thu'=>"ព្រហ",
    			'Fri'=>"សុក្រ",
    			'Sat'=>"សៅរី",
    			'Sun'=>"អាទិត្យ");
    	if($str==null){
    		return $rs;
    	}else{
    	return $rs[$str];
    	}
    
    }
    public function convertStringToDate($date, $format = "Y-m-d H:i:s")
    {
    	if(empty($date)) return NULL;
    	$time = strtotime($date);
    	return date($format, $time);
    }   
    public static function getResultWarning(){
          return array('err'=>1,'msg'=>'មិន​ទាន់​មាន​ទន្និន័យ​នូវ​ឡើយ​ទេ!');	
    }
   /*@author Mok Channy
    * for use session navigetor 
    * */
//    public static function SessionNavigetor($name_space,$array=null){
//    	$session_name = new Zend_Session_Namespace($name_space);
//    	return $session_name;   	
//    }
   public function getAllProvince(){
   	$this->_name='ln_province';
   	$sql = " SELECT province_id,CONCAT(province_kh_name) province_en_name FROM $this->_name WHERE status=1 AND province_kh_name!='' ORDER BY province_id DESC";
   	$db = $this->getAdapter();
   	return $db->fetchAll($sql);
   }
   public function getAllDistrict(){
   	$this->_name='ln_district';
   	$sql = " SELECT dis_id,pro_id,CONCAT(district_name,'-',district_namekh) district_name FROM $this->_name WHERE status=1 AND district_name!='' ";
   	$db = $this->getAdapter();
   	return $db->fetchAll($sql);
   }
   public function getAllDistricts(){
   	$this->_name='ln_district';
   	$sql = " SELECT dis_id AS id,pro_id,CONCAT(district_namekh) name FROM $this->_name WHERE status=1 AND district_name!='' ";
   	$db = $this->getAdapter();
   	return $db->fetchAll($sql);
   }
   public function getCommune(){
   	$this->_name='ln_commune';
   	$sql = " SELECT com_id,com_id AS id,commune_name,CONCAT(commune_namekh) AS name,district_id FROM $this->_name WHERE status=1 AND commune_name!='' ";
   	$db = $this->getAdapter();
   	return $db->fetchAll($sql);
   }
//    public function getCommuneByDistrict($district_id){
// 	   	$this->_name='ln_commune';
// 	   	$sql = " SELECT com_id,com_id AS id,commune_name,CONCAT(commune_name,'-',commune_namekh) AS name,district_id FROM $this->_name WHERE status=1 AND 
// 	   	commune_name!='' AND district_id=$district_id ORDER BY commune_name ASC";
// 	   	$db = $this->getAdapter();
// 	   	return $db->fetchAll($sql);
//    }
   
   public function getVillage(){
   	$this->_name='ln_village';
   	$sql = " SELECT vill_id,vill_id AS id,village_name,CONCAT(village_namekh) AS name,commune_id FROM $this->_name WHERE status=1 AND village_name!='' ";
   	$db = $this->getAdapter();
   	return $db->fetchAll($sql);
   }
   public function getZoneList($option=null){
//    	$this->_name='ln_zone';
//    	$sql = " CALL `stGetAllZone`() ";
//    	$db = $this->getAdapter();
//    	$rows =  $db->fetchAll($sql);
   	$db = $this->getAdapter();
   	$sql = "SELECT
		   	zone_id AS id ,
		   	CONCAT(COALESCE(zone_name,''),'-',COALESCE(zone_num,'')) AS name,
		   	zone_id,
		   	zone_name,
		   	zone_num
   	
   		FROM ln_zone
   		WHERE status=1 AND  zone_name!='' ";
   	$rows= $db->fetchAll($sql);
   	
   	$tr = Application_Form_FrmLanguages::getCurrentlanguage();
   	if(!empty($rows)){
   		if($option!=null){
   		$request=Zend_Controller_Front::getInstance()->getRequest();
   		$action = $request->getControllerName();
   		if($action=='loan'){
   			$options=array(-1=>$tr->translate("SELECT_ZONE"));}
	   		if(!empty($rows))foreach($rows as $rs){
	   				$options[$rs['id']]=$rs['name'];
	   		}
   			return $options;
   		}
   	}
   	return $rows;
   }
   public function getAllCOName($option=null,$branch_id=0){
   	$this->_name='ln_co';
   	$sql = " SELECT 
   		branch_id,co_id AS id, CONCAT(co_firstname,' - ',co_khname,' - ',co_code) AS name ,
   		co_id ,co_firstname,co_khname,co_code 
   		FROM ln_co 
   		WHERE STATUS=1 AND co_khname!='' AND `position_id`=1 ";
   	
   	$dbp = new Application_Model_DbTable_DbGlobal();
   	$sql.=$dbp->getAccessPermission("branch_id");
   	if($branch_id>0){
   		$sql.=" AND branch_id=".$branch_id;
   	}
   	
   	$db = $this->getAdapter();
   	$rows =  $db->fetchAll($sql);
   	$tr = Application_Form_FrmLanguages::getCurrentlanguage();
   	$options = array(''=>$tr->translate("SELECT_CREDIT_OFFICER"),'-1'=>$tr->translate("ADD_NEW"));
   	if(!empty($rows)){
   	if($option!=null){
   			foreach($rows as $rs){
   				$options[$rs['co_id']]=$rs['co_khname'];}
   				return $options;
   		}
   	}
   	return $rows;
   }
   public function getAllCoNameOnly($branch_id=0){
   	$db= $this->getAdapter();
   	$sql = " SELECT co_id AS id, CONCAT(co_khname,' - ',co_code) AS name
   	  FROM ln_co WHERE STATUS=1 AND co_khname!='' AND `position_id`=1 ";
   	
   	$dbp = new Application_Model_DbTable_DbGlobal();
   	$sql.=$dbp->getAccessPermission("branch_id");
   	if($branch_id>0){
   		$sql.=" AND branch_id=".$branch_id;
   	}
   	return $db->fetchAll($sql);
   }
   public function getAllCurrency($id,$opt = null){
	   	$sql = "SELECT * FROM ln_currency WHERE status = 1 ";
	   	if($id!=null){
	   		$sql.=" AND id = $id";
	   	}
	   	$rows = $this->getAdapter()->fetchAll($sql);
	   	if($opt!=null){
	   		$options=array();
	   		if(!empty($rows))foreach($rows AS $row){
	   			$options[$row['id']]=($row['displayby']==1)?$row['displayby']:$row['curr_nameen'];
	   		}
	   		return $options;
	   	}else{
	   		return $rows;
	   	}
   	
   }
   public function getNewReceiptId(){
   	$this->_name='ln_callecteralllist';
   	$db = $this->getAdapter();
   	$sql=" SELECT id ,code_call FROM $this->_name ORDER BY id DESC LIMIT 1 ";
   	$acc_no = $db->fetchOne($sql);
   	$new_acc_no= (int)$acc_no+1;
   	$acc_no= strlen((int)$acc_no+1);
   	$pre = "";
   	for($i = $acc_no;$i<5;$i++){
   		$pre.='0';
   	}
   	return $pre.$new_acc_no;
   }
   
   public function getCodecallId(){
   	$this->_name='ln_callecteralllist';
   	$db = $this->getAdapter();
   	$sql=" SELECT id ,code_call FROM $this->_name ORDER BY id DESC LIMIT 1 ";
   	$acc_no = $db->fetchOne($sql);
   	$new_acc_no= (int)$acc_no+1;
   	$acc_no= strlen((int)$acc_no+1);
   	$pre = "";
   	for($i = $acc_no;$i<5;$i++){
   		$pre.='0';
   	}
   	return $pre.$new_acc_no;
   }
   
   public function getNewClientId(){
   	$this->_name='ln_client';
   	$db = $this->getAdapter();
   	$sql=" SELECT client_id ,client_number FROM $this->_name ORDER BY client_id DESC LIMIT 1 ";
   	$acc_no = $db->fetchOne($sql);
   	$new_acc_no= (int)$acc_no+1;
   	$acc_no= strlen((int)$acc_no+1);
   	$pre = "";
   	for($i = $acc_no;$i<6;$i++){
   		$pre.='0';
   	}
   	return $pre.$new_acc_no;
   }
   public function countinstallmentClient(){
   	$db = $this->getAdapter();
   	$sql="SELECT client_id ,client_number FROM ln_ins_client ORDER BY client_id DESC LIMIT 1 ";
   	$acc_no = $db->fetchOne($sql);
   	$new_acc_no= (int)$acc_no+1;
   	$acc_no= strlen((int)$acc_no+1);
   	$pre = "";
   	for($i = $acc_no;$i<6;$i++){
   		$pre.='0';
   	}
   	return $pre.$new_acc_no;
   }
   public function getNewInvoiceExchange(){
   	$this->_name='ln_exchange';
   	$db = $this->getAdapter();
   	$sql=" SELECT id FROM $this->_name ORDER BY id DESC LIMIT 1 ";
   	$acc_no = $db->fetchOne($sql);
   	$new_acc_no= (int)$acc_no+1;
   	$acc_no= strlen((int)$acc_no+1);
   	$pre = "";
   	for($i = $acc_no;$i<6;$i++){
   		$pre.='0';
   	}
   	return $pre.$new_acc_no;
   }
   public function getLoanNumber($data=array('branch_id'=>1,'is_group'=>0)){
   	$this->_name='ln_loan';
   	$db = $this->getAdapter();
   	if(empty($data['is_group'])){
   		$data['is_group']=0;
   	}
   	
   	$sql=" SELECT COUNT(id)  FROM $this->_name WHERE branch_id=".$data['branch_id']." LIMIT 1 ";
   	$pre = $this->getPrefixCode($data['branch_id'])."L";
   	
   
   	$acc_no = $db->fetchOne($sql);
   	
   	$new_acc_no= (int)$acc_no+1;
   	$acc_no= strlen((int)$acc_no+1);
   	
   	for($i = $acc_no;$i<5;$i++){
   		$pre.='0';
   	}
   	return $pre.$new_acc_no;
   }
   public function getSavingNumber($data=array('branch_id'=>2)){
   	$this->_name='ln_savingaccount';
   	$db = $this->getAdapter();
   		$sql=" SELECT COUNT(id) FROM $this->_name WHERE branch_id=".$data['branch_id']." LIMIT 1 ";
   		$pre = $this->getPrefixCode($data['branch_id'])."S";
   	$acc_no = $db->fetchOne($sql);
   	$new_acc_no= (int)$acc_no+1;
   	$acc_no= strlen((int)$acc_no+1);
   	for($i = $acc_no;$i<5;$i++){
   		$pre.='0';
   	}
   	return $pre.$new_acc_no;
   }
   function getPrefixCode($branch_id){
   	$db  = $this->getAdapter();
   	$sql = " SELECT prefix FROM `ln_branch` WHERE br_id = $branch_id  LIMIT 1";
   	return $db->fetchOne($sql);
   }
   public function getStaffNumberByBranch($branch_id){
   	$this->_name='ln_co';
   	$db = $this->getAdapter();
   		$sql = "SELECT COUNT(co_id)FROM $this->_name WHERE branch_id=".$branch_id." LIMIT 1 ";
   		$pre = $this->getPrefixCode($branch_id)."-";
   	$acc_no = $db->fetchOne($sql);
   
   	$new_acc_no= (int)$acc_no+1;
   	$acc_no= strlen((int)$acc_no+1);
   
   	for($i = $acc_no;$i<5;$i++){
   		$pre.='0';
   	}
   	return $pre.$new_acc_no;
   }
 
   public function getClientByType($type=null,$client_id=null ,$row=null){
   $this->_name='ln_client';
   $where='';
   if($type!=null){
   	$where=' AND is_group = 1';
   }
   	$sql = " SELECT client_id,name_kh,name_en,client_number,
   				(SELECT `ln_village`.`village_namekh` FROM `ln_village` WHERE (`ln_village`.`vill_id` = `ln_client`.`village_id`) limit 1) AS `village_name`,
				(SELECT `c`.`commune_namekh` FROM `ln_commune` `c` WHERE (`c`.`com_id` = `ln_client`.`com_id`) LIMIT 1) AS `commune_name`,
				(SELECT `d`.`district_namekh` FROM `ln_district` `d` WHERE (`d`.`dis_id` = `ln_client`.`dis_id`) LIMIT 1) AS `district_name`,
				(SELECT province_kh_name FROM `ln_province` WHERE province_id= ln_client.pro_id  LIMIT 1) AS province_en_name

   	FROM $this->_name WHERE status=1 AND (name_en!='' OR  name_kh!='' )";
   
   	$db = $this->getAdapter();
   	if($row!=null){
   		if($client_id!=null){ $where.=" AND client_id  =".$client_id ." LIMIT 1";}
   		return $db->fetchRow($sql.$where);
   	}
   	return $db->fetchAll($sql.$where);
   }
   
   public function getClientSavingByType($type=null,$client_id=null ,$row=null){
   	$this->_name='ln_clientsaving';
   	$where='';
   	if($type!=null){
   		$where=' AND is_group = 1';
   	}
   	$sql = " SELECT client_id,name_kh,name_en,client_number,
   	(SELECT `ln_village`.`village_namekh` FROM `ln_village` WHERE (`ln_village`.`vill_id` = `ln_clientsaving`.`village_id`) limit 1) AS `village_name`,
   	(SELECT `c`.`commune_namekh` FROM `ln_commune` `c` WHERE (`c`.`com_id` = `ln_clientsaving`.`com_id`) LIMIT 1) AS `commune_name`,
   	(SELECT `d`.`district_namekh` FROM `ln_district` `d` WHERE (`d`.`dis_id` = `ln_clientsaving`.`dis_id`) LIMIT 1) AS `district_name`,
   	(SELECT province_kh_name FROM `ln_province` WHERE province_id= ln_clientsaving.pro_id  LIMIT 1) AS province_en_name
   
   	FROM $this->_name WHERE status=1 AND (name_en!='' OR  name_kh!='' )";
   	 
   	$db = $this->getAdapter();
   	if($row!=null){
   		if($client_id!=null){
   			$where.=" AND client_id  =".$client_id ." LIMIT 1";
   		}
   		return $db->fetchRow($sql.$where);
   	}
   	return $db->fetchAll($sql.$where);
   }
   
   public function getAssetByType($type=null,$Asset_id=null ,$row=null){
   	$this->_name='ln_account_name';
   	$where='';
   	if($type!=null){
   		$where=' AND is_group = 1';
   	}
   	$sql = "SELECT id,account_code,account_name_en FROM $this->_name WHERE STATUS=1 AND parent_id=49";
   
   	$db = $this->getAdapter();
   	if($row!=null){
   		if($Asset_id!=null){
   			$where.=" AND id  =".$Asset_id ." LIMIT 1";
   		}
   		return $db->fetchRow($sql.$where);
   	}
   	return $db->fetchAll($sql.$where);
   }
   
   public function getOwnerByType($type=null,$customer_id=null ,$row=null){
   	$this->_name='ln_callecteralllist';
   	$where='';
   	if($type!=null){
   		$where=' AND is_group = 1';
   	}
   	$sql = "SELECT branch,receipt,code_call,
            customer_id,(SELECT name_en FROM ln_client WHERE client_id=customer_id) AS customer_name,
   			type_call,owner_call,callnumber,create_date,date_debt,
   			term,amount_term,date_line,curr_type,amount_debt,note,user_id,status,is_verify,verify_by,
   			is_fund FROM $this->_name  WHERE status=1 AND customer_id!='' ";
   	$db = $this->getAdapter();
   	if($row!=null){
   		if($customer_id!=null){
   			$where.=" AND id  =".$customer_id ." LIMIT 1";
   		}
   		return $db->fetchRow($sql.$where);
   	}
   	return $db->fetchAll($sql.$where);
   }
    
   
   public static function getCurrencyType($curr_type){
   	$curr_option = array(
   			1=>'រៀល',
   			2=>'ដុល្លា'
   			);
   	return $curr_option[$curr_type];
   	
   }
   public function getAllSituation($id = null){
   	$_status = array(
   			1=>$this->tr->translate("Single"),
   			2=>$this->tr->translate("Married"),
   			3=>$this->tr->translate("Windowed"),
   			4=>$this->tr->translate("Mindowed")
   	);
   	if($id==null)return $_status;
   	else return $_status[$id];
   }
   public function GetAllIDType($id = null){
   	$_status = array(
   			1=>$this->tr->translate("National ID"),
   			2=>$this->tr->translate("Family Book"),
   			3=>$this->tr->translate("Resident Book"),
   			4=>$this->tr->translate("Other")
   	);
   	if($id==null)return $_status;
   	else return $_status[$id];
   }
   public function getAllDegree($id=null){
   	$tr= Application_Form_FrmLanguages::getCurrentlanguage();
   	$opt_degree = array(
   			''=>$this->tr->translate($tr->translate("SELECT_DEGREE")),
   			1=>$this->tr->translate("Diploma"),
   			2=>$this->tr->translate("Associate"),
   			3=>$this->tr->translate("Bechelor"),
   			4=>$this->tr->translate("Master"),
   			5=>$this->tr->translate("PhD")
   	);
   	if($id==null)return $opt_degree;
   	else return $opt_degree[$id]; 
  }
  public function getAllBranchName($branch_id=null,$opt=null){
  	$db = $this->getAdapter();
  	$tr = Application_Form_FrmLanguages::getCurrentlanguage();
  	$sql= "SELECT br_id,br_id AS id,branch_namekh as name,branch_namekh,
  	branch_nameen,br_address,branch_code,branch_tel,displayby
  	FROM `ln_branch` WHERE branch_namekh !='' AND status=1 ";
  	
  	$sql.= $this->getAccessPermission('br_id');
  	
  	if($branch_id!=null){
  		$sql.=" AND br_id=$branch_id LIMIT 1";
  	}
  	$row = $db->fetchAll($sql);
  	
  	if($opt==null){
  		return $row;
  	}else{
  		$options=array(0=>$tr->translate("SELECT_PROJECT"));
  		if(!empty($row)) foreach($row as $read) $options[$read['br_id']]=$read['branch_namekh'];
  		return $options;
  	}
  }
  function countDaysByDate($start,$end){
  	$earlier = new DateTime($start);
  	$later = new DateTime($end);
  	
  	return $abs_diff = $later->diff($earlier)->format("%a"); //3
  	
//   	$first_date = strtotime($start);
//   	$second_date = strtotime($end);
//   	$offset = $second_date-$first_date;
//   	return floor($offset/60/60/24);
  
  }

 public function returnAfterHoliday($holiday_option,$date){
	  $rs = $this->checkHolidayExist($holiday_option,$date);
	  if(is_array($rs)){
	  	$d = new DateTime($rs['start_date']);
	  	$d->modify( 'next day' );//here check for holiday_option
	  	$date =  $d->format( 'Y-m-d' );
	  	$this->returnAfterHoliday($holiday_option,$date);
	  }else{
	  	echo $date;
	  	return $date;
	  }
  }
  public function getClientByMemberId($loan_id){//for loan
  	
  	$sql="SELECT l.level,
			l.date_release,
			l.total_duration,
			l.first_payment,
			l.pay_term,
			l.payment_method,
			l.loan_type,
	  		(SELECT b.branch_namekh FROM `ln_branch` AS b WHERE b.br_id =l.branch_id LIMIT 1) AS branch_name,
	  		(SELECT b.br_address FROM `ln_branch` AS b WHERE b.br_id =l.branch_id LIMIT 1) AS branchAddress,
	  		(SELECT b.branch_tel FROM `ln_branch` AS b WHERE b.br_id =l.branch_id LIMIT 1) AS branchTel,
	  		(SELECT co_khname FROM `ln_co` WHERE co_id =l.co_id LIMIT 1) AS co_khname,
	  		(SELECT co_firstname FROM `ln_co` WHERE co_id =l.co_id LIMIT 1) AS co_enname,
	  		(SELECT displayby FROM `ln_co` WHERE co_id =l.co_id LIMIT 1) AS displayby,
	  		(SELECT tel FROM `ln_co` WHERE co_id =l.co_id LIMIT 1) AS tel,
	  		(SELECT client_number FROM `ln_client` WHERE client_id = l.customer_id LIMIT 1) AS client_number,
	  		(SELECT name_kh FROM `ln_client` WHERE client_id = l.customer_id LIMIT 1) AS client_name_kh,
	  		(SELECT name_en FROM `ln_client` WHERE client_id = l.customer_id LIMIT 1) AS client_name_en,
	  		(SELECT phone FROM `ln_client` WHERE client_id = l.customer_id LIMIT 1) AS client_phone,
	  		(SELECT displayby FROM `ln_client` WHERE client_id = l.customer_id LIMIT 1) AS displayclient,
	  		l.customer_id AS client_id,
	  		(SELECT curr_namekh FROM `ln_currency` WHERE id = l.currency_type LIMIT 1) AS currency_type
	  		,l.loan_amount AS total_capital,l.loan_number,
	  		l.interest_rate,l.branch_id,
	        (SELECT CONCAT(last_name ,' ',first_name)  FROM `rms_users` WHERE id = l.user_id LIMIT 1) AS user_name
  		FROM 
  	   `ln_loan` AS l WHERE 1 ";
  	
	  	if(!empty($loan_id)){
	  		$sql.=" AND l.id = $loan_id";
	  	}
	  	
	  	$dbp = new Application_Model_DbTable_DbGlobal();
	  	$sql.=$dbp->getAccessPermission("branch_id");
	  	
	  	$db=$this->getAdapter();
  	    return $db->fetchRow($sql);
  	
  }
  public function getClientPawnshop($loan_id){//for pawn
  	$sql="SELECT l.level,
	  	l.date_release,
	  	l.total_duration,
	  	l.first_payment,
	  	l.payment_method,
	  	l.release_amount AS total_capital,l.loan_number,
	  	l.interest_rate,
	  	l.branch_id,
	  	l.customer_id AS client_id,
	  	(SELECT branch_namekh FROM `ln_branch` WHERE br_id =l.branch_id LIMIT 1) AS branch_name,
	  	(SELECT client_number FROM `ln_clientsaving` WHERE client_id = l.customer_id LIMIT 1) AS client_number,
	  	(SELECT name_kh FROM `ln_clientsaving` WHERE client_id = l.customer_id LIMIT 1) AS client_name_kh,
	  	(SELECT name_en FROM `ln_clientsaving` WHERE client_id = l.customer_id LIMIT 1) AS client_name_en,
	  	(SELECT phone FROM `ln_clientsaving` WHERE client_id = l.customer_id LIMIT 1) AS client_phone,
	  	(SELECT name_kh FROM `ln_view` WHERE type = 14 AND key_code = l.term_type ) term_type,
	  	(SELECT curr_namekh FROM `ln_currency` WHERE id = l.currency_type LIMIT 1) AS currency_type,
	  	(SELECT CONCAT(last_name ,' ',first_name)  FROM `rms_users` WHERE id = l.user_id LIMIT 1) AS user_name
  	FROM
  		`ln_pawnshop` AS l WHERE 1 ";
  	if(!empty($loan_id)){
  		$sql.=" AND l.id = $loan_id";
  	}
  	
  	$dbp = new Application_Model_DbTable_DbGlobal();
  	$sql.= $dbp->getAccessPermission("l.branch_id");
  	
  	$db=$this->getAdapter();
  	return $db->fetchRow($sql);
  }
  function getAllPaymentMethod($payment_id=null,$option = null){
  	$sql = "SELECT * FROM ln_payment_method WHERE status = 1 ";
  	if($payment_id!=null){
  		$sql.=" AND id = $payment_id";
  	}
  	$rows = $this->getAdapter()->fetchAll($sql);
  	if($option!=null){
  		$options=array();
  		if(!empty($rows))foreach($rows AS $row){
  			$options[$row['id']]=($row['displayby']==1)?$row['payment_namekh']:$row['payment_nameen'];
  		}
  		return $options;
  	}else{
  		return $rows;
  	}
  }
  public function getAllStaffPosition($id=null,$option = null){
  	$db = $this->getAdapter();
  	$sql=" SELECT id,position_en,position_kh,displayby 
  			FROM `ln_position` WHERE status =1 ";
  	if($id!=null){
  		$sql.=" AND id = $id LIMIT 1";
  	}
  	$rows = $db->fetchAll($sql);
  	$tr = Application_Form_FrmLanguages::getCurrentlanguage();
  	if($option!=null){
  		$options=array(''=>$tr->translate("PLEASE_SELECT"));
  		if(!empty($rows))foreach($rows AS $row){
  			$options[$row['id']]=($row['displayby']==1)?$row['position_kh']:$row['position_en'];
  		}
  		return $options;
  	}else{
  		return $rows;
  	}
  }
  
  public function getAllDepartment($id=null,$option = null){
  	$db = $this->getAdapter();
  	$sql=" SELECT id,
	  	department_kh,
	  	department_en,
	  	displayby,
	  	department_kh AS name
  	FROM `ln_department` WHERE status =1 ";
  	if($id!=null){
  		$sql.=" AND id = $id LIMIT 1";
  	}
  	$tr = Application_Form_FrmLanguages::getCurrentlanguage();
  	$rows = $db->fetchAll($sql);
  	if($option!=null){
  		$options=array(''=>$tr->translate("SELECT_DEPARTMENT"),'-1'=>$tr->translate("ADD_NEW"));
  		if(!empty($rows))foreach($rows AS $row){
  			$options[$row['id']]=($row['displayby']==1)?$row['department_kh']:$row['department_kh'];
  		}
  		return $options;
  	}else{
  		return $rows;
  	}
  }
  public  function getclientdtype(){
  	$db = $this->getAdapter();
  	$sql="SELECT id,key_code,CONCAT(name_kh) AS name ,displayby FROM `ln_view` WHERE name_en!='' AND status =1 AND type=23";
  	$rows = $db->fetchAll($sql);
  	return $rows;
  }
  public function getVewOptoinTypeByType($type=null,$option = null,$limit =null,$first_option =null){
  	$db = $this->getAdapter();
  	
  	$tr = Application_Form_FrmLanguages::getCurrentlanguage();
  	$sql="SELECT id,key_code,name_kh AS name_en ,displayby FROM `ln_view` WHERE status =1 ";//just concate
  	if($type!=null){
  		$sql.=" AND type = $type ";
  	}
  	if($limit!=null){
  		$sql.=" LIMIT $limit ";
  	}
  	$rows = $db->fetchAll($sql);
  	if($option!=null){
  		$options=array();
  		if($first_option==null){//if don't want to get first select
  			$options=array(''=>$tr->translate("PLEASE_SELECT"),-1=>$tr->translate("ADD_NEW"),);
  		}
  		if(!empty($rows))foreach($rows AS $row){
  			$options[$row['key_code']]=$row['name_en'];//($row['displayby']==1)?$row['name_kh']:$row['name_en'];
  		}
  		return $options;
  	}else{
  		return $rows;
  	}
  }
  public function getVewByType($type=null){
  	$db = $this->getAdapter();
  	$sql="SELECT key_code AS id,name_kh AS name FROM `ln_view` WHERE status =1 AND name_kh!='' ";
  	if($type!=null){
  		$sql.=" AND type = $type ";
  	}
  	$rows = $db->fetchAll($sql);
  	return $rows;
  }
  
  public function getVewOptoinTypeBys($option = null,$limit =null){
  	$db = $this->getAdapter();
  	$tr = Application_Form_FrmLanguages::getCurrentlanguage();
  	$sql="SELECT id,title_en,title_kh,displayby,date,status FROM ln_callecteral_type WHERE status =1 ";
  	if($limit!=null){
  		$sql.=" LIMIT $limit ";
  	}
  	$rows = $db->fetchAll($sql);
  	if($option!=null){
  		$options=array(''=>$tr->translate("PLEASE_SELECT"));
  		if(!empty($rows))foreach($rows AS $row){
  			$options[$row['id']]=($row['displayby']==1)?$row['title_kh']:$row['title_en'];
  		}
  		return $options;
  	}else{
  		return $rows;
  	}
  }
  public function getCollteralType($option = null,$limit =null){
  	$db = $this->getAdapter();
  	$sql="SELECT id,title_kh As name FROM `ln_callecteral_type` WHERE status =1 and title_kh!='' ";
  	if($limit!=null){
  		$sql.=" LIMIT $limit ";
  	}
  	$rows = $db->fetchAll($sql);
  	if($option!=null){
//   		$options=array(''=>"-----Select Callecteral Type-----",'-1'=>"Add New");
//   		if(!empty($rows))foreach($rows AS $row){
//   			$options[$row['id']]=($row['displayby']==1)?$row['title_kh']:$row['title_en'];
//   		}
//   		return $options;
  		return $rows;
  	}else{
  		return $rows;
  	}
  }
  
  
 public function setReportParam($arr_param,$file){
  	$contents = file_get_contents('.'.$file);
  	if($arr_param!=null){
  		foreach($arr_param as $key=>$read){
  			$contents=str_replace('@'.$key, $read, $contents);
  		}
  	}
  	$info=pathinfo($file);
  	$newfile=$info['dirname'].'/_'.$info['basename'];
  	file_put_contents('.'.$newfile, $contents);
  	return $newfile;
  }
  public function getHeadBudgetList($type,$start){
  	$heads=$this->getDibursementInYear($type, $start);
  	$str='<tr>';
  	foreach($heads as $value){
  		$str.='<td class="tdheader">'.$value.'</td>';
  	}
  	return $str.'</tr>';
  }
//   public function getContent($rows, $type){
//   	$str='';
//   	if($rows){
//   		$i=0;
//   		foreach($rows as $read){
//   			$i++;
//   			$str.='<tr><td class="no">'.$i.'</td>';
//   			$temp='';
//   			$c=0;
//   			foreach($read as $key=>$value){
//   				if($key!='id'){
//   					if ($type == 'payment'){
//   						if ($key == 'amount' || $key == 'amount_kh'){
//   							$str.='<td align="right">'.number_format($value,2).'</td>';
//   						}
//   						elseif ($key == "rate"){
//   							$str.='<td align="right">'.number_format($value).'</td>';
//   						}
//   						elseif ($key == "create_date"){
//   							$str.='<td align="center">'. date( "d, M Y", strtotime($value)) .'</td>';
//   						}
//   						elseif ($key == "years"){
//   							$str.='<td align="center">'. $value .'</td>';
//   						}
//   						else{
//   							$str.='<td>'.$value.'</td>';
//   						}
//   					}
//   					elseif(!($key=='title_english' || $key=='title_khmer')){
//   						$str.='<td>'.$this->checkValue($value).'</td>';
//   					}
//   					else{
//   						$c++;
//   						if($c==1)$temp=$value;
//   						elseif($c==2){
//   							$str.='<td>'.$temp.'<br/>'.$value.'<br/></td>'; $temp='';$c=0;
//   						}
//   					}
//   				}
//   			}
//   			$str.'</tr>';
  
//   		}
//   	}
//   	return $str;
//   }
//   public function checkValue($value)
//   {
//   	if($value=='' || $value==0) return '-';
//   	return $value;
  
//   }
  public function getSubDaysByPaymentTerm($pay_term,$amount_collect = null){
  	if($pay_term==3){
  		$amount_days =30;
  	}elseif($pay_term==2){
  		$amount_days =7;
  	}else{
  		$amount_days =1;
  	}
  	return $amount_days;//;*$amount_collect;//return all next day collect laon form customer
  }
 function preveiwDate($next_payment,$times){
 	if($times==2){
 		$next_payment = date("Y-m-d", strtotime("$next_payment -1 month"));
 		return $next_payment;
 	}else{
 		return $next_payment;
 	}
 }
//  public function getNextPayment($str_next,$next_payment,$amount_amount,$holiday_status=null,$first_payment=null){//code make slow
//  	$default_day = Date("d",strtotime($first_payment));
//  	$prev_month=$next_payment;
//  	for($i=0;$i<$amount_amount;$i++){
//  		if($default_day>28){
//  			if($str_next!='+1 month'){
//  				$next_payment = date("Y-m-d", strtotime("$next_payment $str_next"));
//  				$default_day='d';
//  				if($str_next=='+1 day'){//if day
//  				}else{
//  					$next_payment = date("Y-m-$default_day", strtotime("$next_payment $str_next"));//code here have problem
//  				}
//  			}else{
//  				/*update*/
//  				if($default_day==31){
//  					$date= new DateTime($next_payment);
//  					$date->modify('+1 day');
//  					$next_payment = $date->format("Y-m-t");
//  					return $next_payment;
//  				}elseif($default_day==30 OR $default_day==29){
//  					$date= new DateTime($prev_month);
//  					$pre_month = $date->format("m");
//  					$prev_month = $pre_month;
//  					if($pre_month=='01'){
//  						$date= new DateTime($next_payment);
//  						$next_payment = $date->format("Y-02-20");
//  						$date= new DateTime($next_payment);
//  						$next_payment = $date->format("Y-m-t");
//  					}//for Feb
//  					else{
//  						$date= new DateTime($next_payment);
//  						$date->modify('+1 month');
//  						$next_payment = $date->format("Y-m-$default_day");
//  					}
//  				}else{//for 29
 						
//  				}
//  			}
//  		}else{
//  			if($str_next!='+1 month'){
//  				$default_day='d';
//  			}
//  			$next_payment = date("Y-m-$default_day", strtotime("$next_payment $str_next"));
//  		}
//  	}
//  	if($holiday_status==3){//normal
//  		if($str_next=='+1 day'){
//  			while($next_payment!=$this->checkHolidayExist($next_payment,$holiday_status)){
//  				$next_payment = $this->checkHolidayExist($next_payment,$holiday_status);
//  			}
//  		}
//  		return $next_payment;//if normal day
//  	}else{//check for sat and sunday
//  		if($default_day<=28){
//  			while($next_payment!=$this->checkHolidayExist($next_payment,$holiday_status)){
//  				$next_payment = $this->checkHolidayExist($next_payment,$holiday_status);
//  			}
//  		}
//  		return $next_payment;
//  	}
 	 
//  }
 public function getNextPayment($str_next,$next_payment,$amount_amount,$holiday_status=null,$first_payment=null){//code make slow
 
 	$default_day = Date("d",strtotime($first_payment));
 	$prev_month=$next_payment;
 	for($i=0;$i<$amount_amount;$i++){
 		if($default_day>28){
 			if($str_next!='+1 month'){//week,day
 				$next_payment = date("Y-m-d", strtotime("$next_payment $str_next"));
 				$default_day='d';
 				if($str_next=='+1 day'){//if day
 				}else{
 					//$next_payment = date("Y-m-$default_day", strtotime("$next_payment $str_next"));//code here have problem
 				}
 			}else{//months
 				if($default_day==31){
 					$date= new DateTime($next_payment);
 					$date->modify('+1 day');
 					$next_payment = $date->format("Y-m-t");
 					 					
 				}else if($default_day==29 OR $default_day==30){
 					$date= new DateTime($prev_month);
 					$pre_month = $date->format("m");
 					$prev_month = $pre_month;
 					 
 					if($pre_month=='01'){
 						$date= new DateTime($next_payment);
 						$next_payment = $date->format("Y-02-20");
 						$date= new DateTime($next_payment);
 						$next_payment = $date->format("Y-m-t");
 					}//for Feb
 					else{
 						$date= new DateTime($next_payment);
 						$date->modify('+1 month');
 						$next_payment = $date->format("Y-m-$default_day");
 					}
 					
 					//$next_payment = date("Y-m-t",strtotime("$next_payment"));
 					
 				}
//  				else{
//  					if ($default_day!=date("d",strtotime($next_payment))){
//  						$next_payment = date("Y-m-$default_day",strtotime("$next_payment +0 month"));
//  					}else{
//  						$next_payment = date("Y-m-$default_day",strtotime("$next_payment +1 month"));
//  					}
//  				}
 			}
 		}else{
 			if($str_next!='+1 month'){
 				$default_day='d';
 			}
 			$date= new DateTime($next_payment);
 			$date->modify($str_next);
 			$next_payment = $date->format("Y-m-$default_day");
 		}
 	}
 	
//  	return $next_payment;//removee
 	if($holiday_status==3){//normal
 		if($str_next=='+1 day'){
 			while($next_payment!=$this->checkHolidayExist($next_payment,$holiday_status)){
 				$next_payment = $this->checkHolidayExist($next_payment,$holiday_status);
 			}
 		}
 		return $next_payment;
 	}
	else{//check for sat and sunday
 			while($next_payment!=$this->checkHolidayExist($next_payment,$holiday_status)){
 				$next_payment = $this->checkHolidayExist($next_payment,$holiday_status);
 			}
 			return $next_payment;
 	}
 		
 }
 public function checkHolidayExist($date_next,$holiday_option){//for check collect payment in holiday or not
 	$db = $this->getAdapter();
 	$sql="SELECT start_date FROM `ln_holiday` WHERE start_date='".$date_next."'";
 	$rs =  $db->fetchRow($sql);
 	$db = new Setting_Model_DbTable_DbLabel();
 	$array = $db->getAllSystemSetting();
 	if(!empty($rs)){//ករណីមាន
 		$d = new DateTime($rs['start_date']);
 		if($holiday_option==1){
 			$str_option = 'previous day';
 		}elseif($holiday_option==2){
 			$str_option = 'next day';
 		}else{
 			return  $d->format('Y-m-d' );
 		}
 		$d->modify($str_option); //here check for holiday option //can next day,next week,next month
 		$date_next =  $d->format('Y-m-d');
 		
 		$date= new DateTime($date_next);
 		$day_work = $date->format("D");
 		
 		
 		if($day_work=='Sat' OR $day_work=='Sun' ){//if
 			if(($day_work=='Sat' AND $array['work_saturday']==1) OR ($day_work=='Sun' AND $array['work_sunday']==1)){//sat working
 				return $date_next;
 			}else if($day_work=='Sat' AND $array['work_saturday']==0){//sat not working
 				if($holiday_option==1){//after
 					$str_next = '+2 day';//why
 				}else if($holiday_option==2){
 					$str_next = '+1 day';
 
 				}else{//before
 					$str_next = '-1 day';//thu
 				}
 				$d->modify($str_next); //here check for holiday option //can next day,next week,next month
 				$date_next =  $d->format('Y-m-d' );
 				return $date_next;
 			}else{//sun not working continue to monday // but not check if mon day not working
 				if($holiday_option==2){//after
 					$str_next = '+1 day';
 				}else{//before
 					$str_next = '-1 day';//thu
 				}
 				$d->modify($str_next); //here check for holiday option //can next day,next week,next month
 				$date_next =  $d->format('Y-m-d');
 				return $date_next;
 			}
 		}else{
 			return $date_next;
 		}
 	}
 	else{
 		
 		$d= new DateTime($date_next);
 		$day_work = $d->format("D");
 		
 		if($day_work=='Sat' OR $day_work=='Sun' ){
 			if(($day_work=='Sat' AND $array['work_saturday']==1) OR ($day_work=='Sun' AND $array['work_sunday']==1)){//sat working
 				return $date_next;
 			}else if($day_work=='Sat' AND $array['work_saturday']==0){//sat not working
 				$str_next = '+2 day';
 				$d->modify($str_next); //here check for holiday option //can next day,next week,next month
 				$date_next =  $d->format('Y-m-d');
 				return $date_next;
 			}else{//sun not working continue to monday // but not check if mon day not working
 				$str_next = '+1 day';
 				$d->modify($str_next); //here check for holiday option //can next day,next week,next month
 				$date_next =  $d->format('Y-m-d');
 				return $date_next;
 			}
 		}else{
 			return $date_next;
 		}
 	}
 }
function checkDefaultDate($str_next,$next_payment,$amount_amount,$holiday_status=null,$first_payment=null){
  	$default_day = Date("d",strtotime($first_payment));
  	$prev_month=$next_payment;
  	
//   	for($i=0;$i<$amount_amount;$i++){
//   		if($default_day>28){
//   			$date= new DateTime($next_payment);
//   			$date->modify($str_next);
//   			$next_payment = $date->format("Y-m-d");
  			
//   			if($str_next!='+1 month'){
//   				$default_day='d';
//   				$date= new DateTime($next_payment);
//   				$date->modify($str_next);
//   				$next_payment = $date->format("Y-m-$default_day");
//   			}else{
// //   				$date= new DateTime($next_payment);
// //   				$date->modify($str_next);
// //   				$next_payment = $date->format("Y-m-$default_day");
// //   				$str_next='-1 month';
  				
// //   				$date= new DateTime($next_payment);
// //   				$date->modify($str_next);
// //   				$next_payment = $date->format("Y-m-d");
//   			}
//   		}else{
//   			if($str_next!='+1 month'){
//   				$default_day='d';
//   			}
//   			$date= new DateTime($next_payment);
//   			$date->modify($str_next);
//   			$next_payment = $date->format("Y-m-$default_day");
//   		}
//   	}
  	if($default_day>28){
  		if($str_next!='+1 month'){//week,day
  			$next_payment = date("Y-m-d", strtotime("$next_payment $str_next"));
  			$default_day='d';
  			if($str_next=='+1 day'){//if day
  			}else{
  				$next_payment = date("Y-m-$default_day", strtotime("$next_payment $str_next"));//code here have problem
  			}
  		}else{//months
  			if($default_day==31){
  				$date= new DateTime($next_payment);
  				$date->modify('+1 day');
  				$next_payment = $date->format("Y-m-t");
  	
  			}else if($default_day==29 OR $default_day==30){
  				$date= new DateTime($prev_month);
  				$pre_month = $date->format("m");
  				$prev_month = $pre_month;
  					
  				if($pre_month=='01'){
  					$date= new DateTime($next_payment);
  					$next_payment = $date->format("Y-02-20");
  					$date= new DateTime($next_payment);
  					$next_payment = $date->format("Y-m-t");
  				}//for Feb
  				else{
  					$date= new DateTime($next_payment);
  					$date->modify('+1 month');
  					$next_payment = $date->format("Y-m-$default_day");
  				}
  			}
  			
  		}
  	}else{
  		if($str_next!='+1 month'){
  			$default_day='d';
  		}
  		$date= new DateTime($next_payment);
  		$date->modify($str_next);
  		$next_payment = $date->format("Y-m-$default_day");
  	}
  		return $next_payment;
  }
	  
  function checkFirstHoliday($next_payment,$holiday_status){
  	if($holiday_status==3){
  		return $next_payment;//if normal day
  	}else{
  		while($next_payment!=$this->checkHolidayExist($next_payment,$holiday_status)){
  			$next_payment = $this->checkHolidayExist($next_payment,$holiday_status);
  		}
  		return $next_payment;
  	}
  }
  function checkEndOfMonth($default_day,$next_payment,$str_next){//default = 31 ,
  	if($str_next=='+1 month'){
  		$str_next='-1 month';
  	}else if($str_next=='+1 week'){
  		$str_next='-1 week';
  	}else{
  		$str_next='-1 day';
  	}
  	
  	$next_payment = date("Y-m-d", strtotime("$next_payment $str_next"));
  	$m = (integer) date('m',strtotime($next_payment));
  	$end_date   = date('Y-m-d',mktime(1,1,1,++$m,0,date('Y',strtotime($next_payment))));
  	return $end_date;
  	
  }
  public function getAmountDayByTerm($pay_term){
  	if($pay_term==3){
  		$amount_day = 30;
  	}elseif($pay_term==2){
  		$amount_day = 7;
  	}else{
  		$amount_day = 1;
  	}
  	return $amount_day;
  }
  public function getNextDateById($pay_term,$amount_next_day){
  	if($pay_term==3){
  		$str_next = '+1 month';
  	}elseif($pay_term==2){
  		$str_next = '+1 week';
  	}else{
  		$str_next = '+1 day';
  	}
  	return $str_next;
  }
  
  public function CountDayByDate($start,$end){
	$date = $this->countDaysByDate($start,$end);
  	return $date;
  }
  public function CurruncyTypeOption(){
  	$db = $this->getAdapter();
  	$rows=array(2=>"ដុល្លា",3=>"បាត",1=>"រៀល");
  	$option='';
  	if(!empty($rows))foreach($rows as $key=>$value){
  		$option .= '<option value="'.$key.'" >'.htmlspecialchars($value, ENT_QUOTES).'</option>';
  	}
  	return $option;
  }
  public function getSystemSetting($keycode){
  	$db = $this->getAdapter();
  	$sql = "SELECT * FROM `ln_system_setting` WHERE keycode ='".$keycode."'";
//   	echo $sql;
  	return $db->fetchRow($sql);
  }
  static function getPaymentTermById($id=null){
  	$arr = array(
  			1=>"ថ្ងៃ",
  			2=>"អាទិត្យ",
  			3=>"ខែ");
  	if($id!=null){
		return $arr[$id];
  	}
  	return $arr;
  	
  }
  public function getAccountBranchByOther($acc_id, $br_id ,$curr_id,$balance=null,$increase=null){
		$sql =" SELECT * FROM ln_account_branch 
		WHERE  account_id = $acc_id AND branch_id=$br_id AND currency_type = $curr_id LIMIT 1";
  	$db = $this->getAdapter();
  	$row =  $db->fetchRow($sql);
  	$increase = ($increase==1)?'+':'-'; 
	$table='ln_account_branch';
  	if(empty($row)){
  		$arr =array(
  				'account_id'=>$acc_id,
				'branch_id'=>$br_id,
  				'currency_type'=>$curr_id,
				'balance'=>$increase.$balance,
				'user_id'=>self::getUserId(),
				'date'=>date('Y-m-d'),
  				);
		$db->insert($table, $arr);
  		return $arr;
  	}else{

 		$where ='id = '.$row['id'] ;
  		$data = array(
  				'balance'=>($increase.$balance)+$row['balance']
  				);
  		$db->update($table,$data,$where);
  	}
  }
  public function getGroupCodeById($diplayby=1,$group_type,$opt=null){
  	$db = $this->getAdapter();
  	$sql = " CALL `stGetAllClientType`($group_type)";
  	$result = $db->fetchAll($sql);
  	$tr= Application_Form_FrmLanguages::getCurrentlanguage();
  	$options=array(''=>$tr->translate("PLEASE_SELECT"));
  	if($opt!=null){
		if(!empty($result))foreach($result AS $row){
			if($group_type==1){
				$label = ($diplayby==1)?$row['group_code']:$row['client_number'].','.$row['name_kh'];//.','.$row['province_en_name'].','.$row['district_name'].','.$row['commune_name'].','.$row['village_name'];	
			}else{
				$label = ($diplayby==1)?$row['client_number']:$row['name_kh'];//.','.$row['province_en_name'].','.$row['district_name'].','.$row['commune_name'].','.$row['village_name'];	
			}
  			$options[$row['client_id']]=$label;
		}  
  		return $options;	
  	}else{
  		return $result;
  	}
  }
  public function getLoanFundExist($loan_id){
  	$sql = "CALL `stgetLoanFundExist`($loan_id) ";
  	$db = $this->getAdapter();
  	$result = $db->fetchRow($sql);
  	if(!empty($result)){
  		return true;}
  	else{ 
  		return false;}
  }
  
  function getAllClientGroup($branch_id=null){
  	$db = $this->getAdapter();
  	$sql = " SELECT c.`client_id` AS id  ,c.`branch_id`,
  	CONCAT(c.client_number ,'-',c.`name_en`,'-',c.`name_kh`) AS name , client_number
  	FROM `ln_client` AS c WHERE c.`name_en`!='' AND c.status=1 AND c.is_group=1 " ;
  	if($branch_id!=null){
  		$sql.=" AND c.`branch_id`= $branch_id ";
  
  	}
  	$sql.=" ORDER BY id DESC";
  	return $db->fetchAll($sql);
  }
  function getAllClientGroupCode($branch_id=null){
  	$db = $this->getAdapter();
  	$sql = " SELECT c.`client_id` AS id  ,c.`branch_id`,
  	group_code AS name
  	FROM `ln_client` AS c WHERE c.`name_en`!='' AND c.status=1 AND c.is_group=1 " ;
  	if($branch_id!=null){
  		$sql.=" AND c.`branch_id`= $branch_id ";
  
  	}
  	$sql.=" ORDER BY id DESC";
  	return $db->fetchAll($sql);
  }
  function getAllClientNumber($branch_id=null){
//   	$db = $this->getAdapter();
//   	$sql = " SELECT c.`client_id` AS id  ,c.client_number AS name, c.`branch_id`
//   	FROM `ln_client` AS c WHERE (c.`name_en`!='' OR c.name_kh!='') AND c.status=1  " ;
//   	if($branch_id!=null){
//   		$sql.=" AND c.`branch_id`= $branch_id ";
//   	}
//   	$sql.="  order BY c.`client_id` DESC ";
//   	return $db->fetchAll($sql);
		return $this->getAllClient($branch_id);
  }
  function getAllClient($branch_id=null){//loan
  	$db = $this->getAdapter();
  	$sql = " SELECT c.`client_id` AS id  ,c.`branch_id`,
  	CONCAT(COALESCE(c.`client_number`,''),'-',COALESCE(c.`name_kh`,'')) AS name , client_number
  	FROM `ln_client` AS c WHERE (c.`name_en`!='' OR c.name_kh!='') AND c.status=1  " ;
  	if($branch_id!=null){
  		$sql.=" AND c.`branch_id`= $branch_id ";
  	}
  	$sql.="  order BY c.`client_id` DESC ";
  	return $db->fetchAll($sql);
  }
  
  function getClientIdBYMemberId($member_id){
  	$db = $this->getAdapter();
		$sql = " SELECT l.co_id,l.customer_id AS client_id  
			FROM  `ln_loan` AS l 
          WHERE l.status=1 AND l.id = $member_id GROUP BY l.id ";
  	return $db->fetchRow($sql);
  }

  function getAllLoanNumber($type){//type ==1 is ilPayment, type==2 is group payment
  	$db = $this->getAdapter();
  	$sql ="SELECT 
  		l.`loan_number` 
  			FROM `ln_loan` AS l 
  		WHERE 
  		l.`is_completed` = 0 
  		AND l.status=1 
  		AND l.is_badloan=0 ";
  	if($type==1){
  		$sql.=" AND l.`loan_type` = 1 ";
  	}else{
  		$sql.=" AND l.`loan_type` =2 ";
  	}
  	$sql.=" ORDER BY loan_number ASC ";
  
  	return $db->fetchAll($sql);
  }
  
  function getAllViewType($opt=null){
  		$db = $this->getAdapter();
  	$sql ="SELECT * FROM `ln_view_type`";
  	
  	$result = $db->fetchAll($sql);
  	$options=array('-1'=>$this->tr->translate("Select View Type"));
  	if($opt!=null){
  		if(!empty($result))foreach($result AS $row){
  			    $options[$row['id']]=$row['name'];
  		}
  		return $options;
  	}else{
  		return $result;
  	}
  	
  }
  public function getLoanAllLoanNumber($diplayby=1,$opt=null){
  	$db = $this->getAdapter();
  	$sql = "CALL `stGetAllLoanNumber`";
  	$result = $db->fetchAll($sql);
  	$options=array(''=>$this->tr->translate("Select Loan Number"));
  	if($opt!=null){
  		if(!empty($result))foreach($result AS $row){
  			$options[$row['id']]= $row['customer_name']."-".$row['loan_number'];
  		}
  		return $options;
  	}else{
  		return $result;
  	}
  }
  
  public function getAllLoanNumbers($diplayby=1,$opt=null){
  	$db = $this->getAdapter();
  	$sql="SELECT l.id,
	(SELECT name_kh FROM `ln_client` WHERE ln_client.client_id=l.customer_id LIMIT 1) AS customer_name,
	loan_number FROM `ln_loan` AS l WHERE l.status=1 AND l.is_completed = 0 ORDER BY l.id DESC ";
  	$result = $db->fetchAll($sql);
  	$options=array(''=>$this->tr->translate("Select Loan Number"));
  	if($opt!=null){
  		if(!empty($result))foreach($result AS $row){
  			$options[$row['id']]= $row['customer_name']."-".$row['loan_number'];
  		}
  		return $options;
  	}else{
  		return $result;
  	}
  }
  
  public function ClassifiedLoan($type=26){
  	$db = $this->getAdapter();
  	$sql="SELECT id,key_code,name_en FROM `ln_view` WHERE status =1 ";//just concate
  	if($type!=null){
  		$sql.=" AND type = $type ";
  	}
  	 $rows = $db->fetchAll($sql);
  	$opt = array();
  	if(!empty($rows))foreach($rows AS $row){
  		$opt[$row['key_code']]=$row['name_en'];
  	}
  	return $opt;
//   	if($option!=null){
//   		$options=array();
//   		if($first_option==null){//if don't want to get first select
//   			$options=array(''=>"-----ជ្រើសរើស-----",-1=>"Add New",);
//   		}
//   		if(!empty($rows))foreach($rows AS $row){
//   			$options[$row['key_code']]=$row['name_en'];//($row['displayby']==1)?$row['name_kh']:$row['name_en'];
//   		}
//   		return $options;
//   	}else{
//   		return $rows;
//   	}
  }
  
  public function getJobName(){
  	$db = $this->getAdapter();
  	$sql= "SELECT DISTINCT `job` FROM `ln_client` WHERE job!='' ";
  	return $db->fetchAll($sql);
  }
  public function getAllInterest($option=null){
  	$this->_name='ln_zone';
  	$sql = " SELECT id,value,label FROM `in_ln_interest` WHERE value > 0 ";
  	$db = $this->getAdapter();
  	$rows =  $db->fetchAll($sql);
  	if($option!=null){
  		if(!empty($rows))foreach($rows as $rs){
  			$options[$rs['value']]=$rs['label'];
  		}
  		return $options;
  	}
  	return $rows;
  }
  function getIndividuleClient(){
  	$db = $this->getAdapter();
  	$sql = "SELECT c.`client_id` AS id ,c.`name_kh` AS name ,c.`branch_id`,c.`client_number` FROM `ln_client` AS c WHERE c.`is_group`=0  AND c.`name_en`!='' " ;
  	return $db->fetchAll($sql);
  }
//   function getAllClient(){
//   	$db = $this->getAdapter();
//   	$sql = "SELECT c.`client_id` AS id ,c.`name_kh` AS name ,c.`branch_id`,c.`client_number` FROM `ln_client` AS c WHERE c.`is_group`=1  AND c.`name_en`!='' " ;
//   	return $db->fetchAll($sql);
//   }
  public function getAllProduct(){
  	$sql=" SELECT v.id,v.product_en,v.product_kh,v.product_kh AS name,
  	v.status FROM ln_pawnshopproduct AS v WHERE v.status=1 ";
  	$Other=" ORDER BY  v.id desc ";
  	$db = $this->getAdapter();
  	return $db->fetchAll($sql);
  }
  public function getPawnshoNumber($customer_id=1){
  	$db = $this->getAdapter();
  	$sql=" SELECT level FROM ln_pawnshop WHERE customer_id=".$customer_id." LIMIT 1 ";
  	$level = $db->fetchOne($sql);
  	return $level;
  }
  public function getPawnReceipt($branch_id){
  	$db = $this->getAdapter();
  	$sql=" SELECT COUNT(id) FROM ln_pawnshop WHERE branch_id=".$branch_id." LIMIT 1 ";
  	$pre = $this->getPrefixCode($branch_id)."PS";
  	$acc_no = $db->fetchOne($sql);
  	$new_acc_no= (int)$acc_no+1;
  	$acc_no= strlen((int)$acc_no+1);
  	for($i = $acc_no;$i<5;$i++){
  		$pre.='0';
  	}
  	return $pre.$new_acc_no;
  }
  public function getInvoiceReceipt($branch_id){
  	$db = $this->getAdapter();
  	$sql="SELECT COUNT(id) FROM ln_ins_sales_install WHERE branch_id=".$branch_id." LIMIT 1";
  	$pre = $this->getPrefixCode($branch_id)."IN";
  	$acc_no = $db->fetchOne($sql);
  	$new_acc_no= (int)$acc_no+1;
  	$acc_no= strlen((int)$acc_no+1);
  	for($i = $acc_no;$i<5;$i++){
  		$pre.='0';
  	}
  	return $pre.$new_acc_no;
  }
//   function deleteLoanDetail(){not use
//   	$db = $this->getAdapter();
//   	$sql="SELECT old_loanid FROM `ln_loan_detail` WHERE old_loanid NOT IN (
// 	SELECT l.old_id FROM `ln_loan` AS l,
// 	ln_loan_detail AS d WHERE l.old_id=d.old_loanid GROUP BY d.old_loanid 
// 	) GROUP BY old_loanid";
//   	$rs = $this->getAdapter()->fetchAll($sql);
//   	    if(!empty($rs)){
//   	    	foreach ($rs as $r){
//   	    		$this->_name="ln_loan_detail";
//   	    		$where=" old_loanid='".$r['old_loanid']."'";
//   	    		$this->delete($where);
// 		  }
// 		}
		  	
//   }
  function updateVillage(){//tab2
//   	$sql="
//   		SELECT n.vill_id,o.villageid,village 
// 	  		FROM `tlb_village`  AS o,`ln_village` AS n
// 			WHERE o.village = n.village_namekh 
// 			GROUP BY n.village_namekh ";
//   	$rs = $this->getAdapter()->fetchAll($sql);
//   	if(!empty($rs)){
//   		$this->_name="ln_client";
//   		foreach($rs as $r){
//   		    $data= array(
//   		    		'village_id'=>$r['vill_id']
//   		    		);
//   		    $where=" village_id = ".$r['villageid'];
//   		    $this->update($data, $where);
//   		}
//   	}

//   		$sql="SELECT n.com_id,o.communeid,commune_namekh 
//  	  		FROM `tbl_commune`  AS o,`ln_commune` AS n
//  			WHERE o.commune = n.commune_namekh
//  			GROUP BY n.commune_namekh  ";
//   		$rs = $this->getAdapter()->fetchAll($sql);
//   	  	if(!empty($rs)){
//   	  		$this->_name="ln_client";
//   	  		foreach($rs as $r){
//   	  		    $data= array(
//   	  		    		'com_id'=>$r['com_id']
//   	  		    		);
//   	  		    $where=" com_id = ".$r['communeid'];
//   	  		    $this->update($data, $where);
//   	  		}
//   	  	}

//   		$sql="SELECT n.dis_id,o.districtid,district_namekh 
// 	  		FROM `tbl_district`  AS o,`ln_district` AS n
// 			WHERE o.district = n.district_namekh
// 			GROUP BY n.district_namekh ";
  		
//   		$rs = $this->getAdapter()->fetchAll($sql);
//   		if(!empty($rs)){
//   			$this->_name="ln_client";
//   			foreach($rs as $r){
//   				$data= array(
//   						'dis_id'=>$r['dis_id']
//   				);
//   				$where=" dis_id = ".$r['districtid'];
//   				$this->update($data, $where);
//   			}
//   		}
  }
  function updateGender(){
  	//   	$sql="SELECT clientid,sex_main FROM `tbl_cif` ";
  	//   	$rs = $this->getAdapter()->fetchAll($sql);
  	//   	if(!empty($rs)){
  	//   		$this->_name="ln_client";
  
  	//   		foreach($rs as  $r){
  	//   			$gender=2;
  	//   			if($r['sex_main']=='ប្រុស'){
  	//   				$gender=1;
  	//   			}
  	//   			$arr =array(
  	//   					'sex'=>$gender
  	//   					);
  	//   			$where="client_id = ".$r['clientid'];
  	//   			$this->update($arr, $where);
  	//   		}
  	//   	}
  }
  
  function checkLoanexist(){//delete loan
//   	$sql="SELECT old_id,loan_amount,
//   		(SELECT SUM(principal) FROM `tbl_repayment` 
//   		WHERE tbl_repayment.LoanID=ln_loan.old_id LIMIT 1) AS principal 
//   	FROM `ln_loan` WHERE 1";
//   	$rs = $this->getAdapter()->fetchAll($sql);
//     if(!empty($rs)){
//     	foreach ($rs as $r){
//     		if($r['principal']!=$r['loan_amount']){
    			
//     		}else{//ដាច់
//     			$this->_name="ln_loan";
//     			$where=" old_id='".$r['old_id']."'";
//     			$this->delete($where);
//     			$this->_name="ln_loan_detail";
//     			$where=" loan_id='".$r['old_id']."'";
//     			$this->delete($where);
//     		}
//     	}	
//     }
  }
//   function updateCompletedschedule(){
//   	$sql="SELECT p.loanid,l.loan_amount aS realdata, SUM(p.principal) AS loan_amount 
// 			FROM `tbl_repayment` p,
// 			`ln_loan` AS l
// 		 WHERE l.old_id=p.loanid AND p.PayType!='FULL' GROUP BY  loanid  ";
//   	$rs = $this->getAdapter()->fetchAll($sql);
//    foreach($rs as $r){
//    		$loan_amount = $r['loan_amount'];
// 	   	$sql="SELECT installment_amount,old_paymentid,id,principal_permonth,total_interest_after FROM  `ln_loan_detail` WHERE old_loanid='".$r['loanid']."'";
// 	   	$row = $this->getAdapter()->fetchAll($sql);
// 	   	foreach($row as $res){
// 	   		$is_completed=0;
// 	   		$this->_name="ln_loan_detail";
// 	   		$loan_amount = $loan_amount-$res['principal_permonth'];
// 	   		if($loan_amount>0){
// 	   			$is_completed=1;
// 	   			$interest = 0;
// 	   			$principal = 0;
// 	   		}else{//check មើលថាទិន្នចុងក្រោយបានបង់ការខ្លះនៅ?
// 	   			$interest = $res['total_interest_after'];
// 	   			$principal = abs($loan_amount);
// 	   			$sql="SELECT SUM(interest) AS interest FROM `tbl_repayment` WHERE LoanID='".$r['loanid']."' AND TermNo=".$res['installment_amount'];
// 	   			$interest = $this->getAdapter()->fetchOne($sql);
// 	   			if($interest>0){
// 	   				$interest = $res['total_interest_after']-$interest;
// 	   				if($interest<0){
// 	   					$interest=0;
// 	   				}
// 	   			}
// 	   			if($principal==0){
// 	   				$is_completed=1;
// 	   			}
// 	   		}
// 	   			$arr = array(
// 	   				'is_completed'=>$is_completed,
// 	   			    'principle_after'=>$principal,
// 	   				'total_interest_after'=>$interest
// 	   			);
// 	   		$where ="id = ".$res['id'];
// 	   		$this->update($arr, $where);
// 	   		if($loan_amount<=0){
// 	   			break;
// 	   		}
// 	   	}
//    }
//   }
 function updateRemainLoan(){
//    	$sql="SELECT l.id,old_id,loan_amount,(SELECT SUM(p.principal) 
//    		FROM `tbl_repayment` AS p 
//   	WHERE l.old_id=p.LoanID  GROUP BY p.LoanID LIMIT 1) AS principal 
//   	FROM `ln_loan` AS l ";
//   	$rs = $this->getAdapter()->fetchAll($sql);
  	
//   	foreach($rs as $r){
//   		$this->_name='ln_loan';
//   		$arr = array("loan_amount"=>$r['loan_amount']-$r['principal']
//   				);
//   		$where="old_id = '".$r['old_id']."'";
//   		$this->update($arr, $where);
  		
//   		$this->_name='ln_loan_detail';
//   			$arr = array("loan_id"=>$r['id']
//   		);
//   		$where="old_loanid = '".$r['old_id']."'";
//   		$this->update($arr, $where);
//   	}
}
/*function updateResetrecord(){
  	$sql="SELECT id ,old_id FROM `ln_loan`";
  	$rs =$this->getAdapter()->fetchAll($sql);
  	$this->_name="ln_loan_detail";
  	foreach($rs as $r){
  		$data = array(
  				'loan_id'=>$r['id']
  				);
  		$where ="loan_id ='".$r['old_id']."'";
  		$this->update($data, $where);
  	}
}*/
  /*public function getupdateLoantoprefixed($data=array('branch_id'=>1)){//សម្រាប់ update preview old loan
  	$this->_name='ln_loan';
  	$db = $this->getAdapter();
  
  	$sql="SELECT id FROM `ln_loan`";
  	$rs = $db->fetchAll($sql);
  	$prefixed = $this->getPrefixCode($data['branch_id'])."L";
  	foreach($rs as $r){
  		$pre = $prefixed;
  		$loan_num=$r['id'];
  		$acc_no= strlen((int)$loan_num);
  		for($i = $acc_no;$i<4;$i++){
  			$pre.='0';
  		}
  		$data = array(
  			'loan_number'=>$pre.$loan_num
  		); 
  		$where = "id = ".$r['id'];
  		$this->update($data, $where);
  	}
  }*/

  function deleteFull(){
  	$sql="SELECT 
			l.old_id,
			p.PayType,
			l.loan_amount
			FROM 
			`ln_loan` AS l,
			`tbl_repayment` AS p
			 WHERE 
			 p.loanid=l.old_id
			   GROUP BY  l.old_id limit 0, 500 ";

  	$rs = $this->getAdapter()->fetchAll($sql);

  	foreach($rs as $r){
  		$this->_name="ln_loan";
  		$where="old_id='".$r['old_id']."'";
  		$this->delete($where);
  		
  		
  		$this->_name="ln_loan_detail";
  		$where=" old_loanid ='".$r['old_id']."'";
  		$this->delete($where);
  	}
			$sql="
			SELECT 
			l.old_id,
			p.PayType,
			l.loan_amount,
			SUM(p.principal) AS paid
			FROM 
			`ln_loan` AS l,
			`tbl_repayment` AS p
			
			 WHERE 
			 p.loanid=l.old_id
			 AND loan_amount=(SELECT SUM(principal) FROM tbl_repayment WHERE tbl_repayment.loanid=l.old_id GROUP BY tbl_repayment.loanid )
			   GROUP BY  l.old_id LIMIT 300";
			
		  	$rs = $this->getAdapter()->fetchAll($sql);
		  	foreach($rs as $r){
		  		if($r['loan_amount']==$r['paid']){
			  		$this->_name="ln_loan";
			  		$where=" old_id ='".$r['old_id']."'";
			  		$this->delete($where);
			
			  		$this->_name="ln_loan_detail";
			  		$where=" old_loanid ='".$r['old_id']."'";
			  		$this->delete($where);
		  		}
		  	}
  }
  function usedonlytimes(){
  	$sql="SELECT l.old_id,l.id,l.loan_amount ,
			(SELECT SUM(`principle_after`) FROM `ln_loan_detail` AS d WHERE d.loan_id=l.id) AS total_schedule
			FROM `ln_loan` AS l";
  	$rs = $this->getAdapter()->fetchAll($sql);
  	foreach($rs as $r ){
  		if($r['total_schedule']>$r['loan_amount']){
  		   		$loan_amount = ($r['total_schedule']-$r['loan_amount']);
  			   	$sql="SELECT installment_amount,old_paymentid,id,principal_permonth,total_interest_after FROM  `ln_loan_detail` WHERE old_loanid='".$r['old_id']."' ORDER BY installment_amount ASC";
  			   	$row = $this->getAdapter()->fetchAll($sql);
  			   	foreach($row as $res){
  			   		$is_completed=0;
  			   		$this->_name="ln_loan_detail";
  			   		$loan_amount = $loan_amount-$res['principal_permonth'];
  			   		if($loan_amount>0){
  			   			$is_completed=1;
  			   			$interest = 0;
  			   			$principal = 0;
  			   		}else{//check មើលថាទិន្នចុងក្រោយបានបង់ការខ្លះនៅ?
  			   			$principal = abs($loan_amount);
  			   			$interest = 0;
  			   			$is_completed=0;
  			   		}
  			   			$arr = array(
  					   				'is_completed'=>$is_completed,
  					   			    'principle_after'=>$principal,
  					   				'total_interest_after'=>$interest
  					   			);
  			   		$where ="id = ".$res['id'];
  			   		$this->update($arr, $where);
  			   		if($loan_amount<=0){
  			   			break;
  			   		}
  			   	}
  	}
  	}
  }
  function getAllClientPawn($branch_id=null,$opt=null){//ajax
	  	$db = $this->getAdapter();
	  	$sql = " SELECT c.`client_id` AS id  ,c.`branch_id`,
	  	c.`name_kh` AS name , client_number
	  	FROM `ln_clientsaving` AS c WHERE (c.`name_en`!='' OR name_kh !='' ) AND c.status=1  " ;
	  	if($branch_id!=null){
	  		$sql.=" AND c.`branch_id`= $branch_id ";
	  
	  	}
	  	$result =  $db->fetchAll($sql);
	  	$tr = Application_Form_FrmLanguages::getCurrentlanguage();
	  	if($opt!=null){
	  		$options=array(''=>$tr->translate("Choose Customer"));
	  		if(!empty($result))foreach($result AS $row){
	  			$options[$row['id']]= $row['name'];
	  		}
	  		return $options;
	  	}else{
	  		return $result;
	  	}
  }
 function getAllClientinstallment($branch_id=null,$opt=null){//installment
	  	$db = $this->getAdapter();
	  	$sql = " SELECT c.`client_id` AS id  ,c.`branch_id`,
	  	c.`name_kh` AS name , client_number
	  	FROM `ln_ins_client` AS c WHERE (c.`name_en`!='' OR c.name_kh!='') AND c.status=1  " ;
	  	if($branch_id!=null){
	  		$sql.=" AND c.`branch_id`= $branch_id ";
	  
	  	}
	  	$sql.="  order BY c.`client_id` DESC ";
	  	$result =  $db->fetchAll($sql);
	  	$tr = Application_Form_FrmLanguages::getCurrentlanguage();
	  	if($opt!=null){
	  		$options=array(''=>$tr->translate("Choose Customer"));
	  		if(!empty($result))foreach($result AS $row){
	  			$options[$row['id']]= $row['name'];
	  		}
	  		return $options;
	  	}else{
	  		return $result;
	  	} 
	}
  function getAllClientcodeinstallment($branch_id=null){//installment
  	$db = $this->getAdapter();
  	$sql = " SELECT c.`client_id` AS id  ,c.`branch_id`,
  	c.`client_number` AS name , client_number
  	FROM `ln_ins_client` AS c WHERE (c.`name_en`!='' OR c.name_kh!='') AND c.status=1  " ;
  	if($branch_id!=null){
  		$sql.=" AND c.`branch_id`= $branch_id ";
  
  	}
  	$sql.="  order BY c.`client_id` DESC ";
  	return $db->fetchAll($sql);
  }
  public function getAllLoanbybranch($branch_id){
  	$db = $this->getAdapter();
  	$sql="SELECT l.id,
	  	CONCAT(loan_number,(SELECT name_kh FROM `ln_client` WHERE ln_client.client_id=l.customer_id LIMIT 1)) AS name
	  	FROM `ln_loan` AS l 
  			WHERE l.status=1 
  				AND l.is_completed = 0 
  				AND l.branch_id=$branch_id
  				ORDER BY l.id DESC ";
  	return $db->fetchAll($sql);
  }
	
  public function getGeneralSaleNumber($branch_ID){
  	$this->_name='ln_ins_generalsale';
  	$db = $this->getAdapter();
  
  	$sql=" SELECT COUNT(id)  FROM $this->_name WHERE branch_id=".$branch_ID." LIMIT 1 ";
  	$pre = $this->getPrefixCode($branch_ID)."GS";
  
  	 
  	$acc_no = $db->fetchOne($sql);
  
  	$new_acc_no= (int)$acc_no+1;
  	$acc_no= strlen((int)$acc_no+1);
  
  	for($i = $acc_no;$i<5;$i++){
  		$pre.='0';
  	}
  	return $pre.$new_acc_no;
  }
  
  function getMonthInkhmer($month){
  	$khmermonth = array("01"=>"មករា", "02"=>"កុម្ភៈ", "03"=>"មិនា", "04"=>"មេសា", "05"=>"ឧសភា", "06"=>"មិថុនា", "07"=>"កក្កដា","08"=>"សីហា", "09"=>"កញ្ញា", "10"=>"តុលា","11"=>"វិច្ឆិកា", "12"=>"ធ្នូ");
  	$monthKH = empty($khmermonth[$month])?"":$khmermonth[$month];
  	return $monthKH;
  }
  function getNumberInkhmer($number){
  	$khmernumber = array("០","១","២","៣","៤","៥","៦","៧","៨","៩");
  	$spp = str_split($number);
  	$num="";
  	foreach ($spp as $ss){
  		 
  		if (!empty($khmernumber[$ss])){
  			$num.=$khmernumber[$ss];
  		}else{
  			$num.=$ss;
  		}
  	}
  	return $num;
  }
  
  public function getCustomerNearlyPaymentLoan(){
  	
  	$search['start_date'] = date('Y-m-d');
  	$search['end_date']= date('Y-m-d');
  	$end_date = $search['end_date'];
  	$db = $this->getAdapter();
  	$sql=" SELECT
	  			l.id,
			  	`co_khname` AS co_name ,
			  	co_code,
			  	b.branch_namekh,
			  	co.`co_id`,
			  	c.`client_number`,
			  	c.`name_kh`,
			  	c.`spouse_name`,
			  	c.`phone`,
			  	l.`loan_amount` as total_capital,
			  	l.`loan_number`,
			  	l.interest_rate  AS interest_rate,
			  	l.`date_release`,
			  	l.`date_line`,
			  	l.`total_duration`,
			  	l.`time_collect`,
			  	l.`currency_type` AS curr_type,
			  	l.`collect_typeterm`,
			  	(SELECT pm.payment_nameen FROM ln_payment_method as pm WHERE pm.id = l.`payment_method`) as payment_method_title,
			  	(SELECT `ln_currency`.`symbol` FROM `ln_currency` WHERE (`ln_currency`.`id` = l.`currency_type` ) LIMIT 1) AS `currency_type`,
			  	(SELECT `ln_view`.`name_en` FROM `ln_view` WHERE ((`ln_view`.`type` = 14) AND (`ln_view`.`key_code` = `l`.`pay_term`)) LIMIT 1) AS `Term Borrow`,
			  	(SELECT `crm`.`date_input` FROM (`ln_client_receipt_money` `crm`) WHERE ((`crm`.`loan_id` = l.`id`)) ORDER BY `crm`.`date_input` DESC LIMIT 1) AS `last_pay_date`,
			  	SUM(d.`principle_after`) AS principle_after,
			  	SUM(d.`total_interest_after`) AS total_interest_after,
			  	SUM(d.`total_payment_after`) AS total_payment_after,
			  	SUM(d.`penelize`) AS penelize,
			  	d.`date_payment` ,
			  	`d`.`installment_amount`   AS `times`,
			  	COUNT(l.`id`) AS amount_late,
		  		l.`branch_id`
		  	FROM
			  	`ln_loan_detail` AS d,
			  	`ln_loan` AS l,
			  	`ln_co` AS co,
			  	`ln_client` AS c ,
			  	`ln_branch` AS b
		  	WHERE
			  	d.`is_completed` = 0
			  	AND `l`.`is_badloan`=0
			  	AND l.`id` = d.`loan_id`
			  	AND l.`status` = 1
			  	AND d.`status`=1
			  	AND co.`co_id` = l.`co_id`
			  	AND c.`client_id` = l.`customer_id`
			  	AND b.`br_id`=l.branch_id ";
  	$where="";
  	$where.=" AND d.date_payment <='$end_date'";
  	$group_by = " GROUP BY l.`id` ORDER BY d.`date_payment` DESC,l.`customer_id` ASC,d.id ASC ";
  	return $db->fetchAll($sql.$where.$group_by);
  }
  
  public function getCustomerNearlyPaymentPawn(){
  	$search['start_date'] = date('Y-m-d');
  	$search['end_date']= date('Y-m-d');
  	$end_date = $search['end_date'];
  	
  	$db = $this->getAdapter();
  	$sql=" SELECT
	  	l.`id`,
	  	b.branch_namekh,
	  	c.`client_number`,
	  	c.`name_kh`,
	  	c.`guarantor_name`,
	  	c.`phone`,
	  	l.`release_amount` AS total_capital,
	  	l.`loan_number`,
	  	l.interest_rate  AS interest_rate,
	  	l.`date_release`,
	  	l.`date_line`,
	  	l.`total_duration`,
	  	l.`currency_type` AS curr_type,
	  	(SELECT `ln_currency`.`symbol` FROM `ln_currency` WHERE (`ln_currency`.`id` = l.`currency_type` ) LIMIT 1) AS `currency_type`,
	  	(SELECT `ln_view`.`name_en` FROM `ln_view` WHERE ((`ln_view`.`type` = 14) AND (`ln_view`.`key_code` = `l`.`term_type`)) LIMIT 1) AS `termborrow`,
	  	(SELECT `crm`.`date_input` FROM (`ln_pawn_receipt_money` `crm`) WHERE ((`crm`.`loan_id` = l.`id`)) ORDER BY `crm`.`date_input` DESC LIMIT 1) AS `last_pay_date`,
	  	SUM(d.`principle_after`) AS principle_after,
	  	SUM(d.`total_interest_after`) AS total_interest_after,
	  	SUM(d.`total_payment_after`) AS total_payment_after,
	  	d.`date_payment` ,
	  	COUNT(l.`id`) AS amount_late,
	  	l.`branch_id`,
	  	`d`.`installment_amount`   AS `times`,
	  	(SELECT p.product_kh FROM `ln_pawnshopproduct` AS p WHERE p.id = l.`product_id` LIMIT 1) AS productTitle,
	  	COUNT(l.`id`) AS amount_late
  	
  	FROM
	  	`ln_pawnshop_detail` AS d,
	  	`ln_pawnshop` AS l,
	  	`ln_clientsaving` AS c ,
	  	`ln_branch` AS b
  	WHERE
	  	d.`is_completed` = 0
	  	AND l.`id` = d.`pawn_id`
	  	AND l.`status` = 1
	  	AND l.is_dach = 0
	  	AND d.`status`=1
	  	AND c.`client_id` = l.`customer_id`
	  	AND b.`br_id`=l.branch_id ";
  	$where='';
  	 
  	$where.=" AND d.date_payment < '$end_date'";
  	$where.=$this->getAccessPermission('l.`branch_id`');
  	$group_by = " GROUP BY l.`id` ORDER BY d.`date_payment` DESC,c.`client_id` ASC ,d.id ASC ";
  	return $db->fetchAll($sql.$where.$group_by);
  }
  public function getCustomerNearlyPaymentSaleInstall(){
  	$search['start_date'] = date('Y-m-d');
  	$search['end_date']= date('Y-m-d');
  	$end_date = $search['end_date'];
  	 
  	$db = $this->getAdapter();
  	$sql=" SELECT
		  	l.`id`,
		  	b.branch_namekh,
		  	c.`client_number`,
		  	c.`name_kh`,
		  	c.`guarantor_name`,
		  	c.`phone`,
		  	
		  	l.`sale_no` AS loan_number,
		  	l.interest_rate  AS interest_rate,
		  	l.`date_sold` AS date_release,
		  	l.`date_line`,
		  	l.`duration` AS total_duration,
  	
		  	(SELECT `crm`.`date_input` FROM (`ln_ins_receipt_money` `crm`) WHERE ((`crm`.`loan_id` = l.`id`)) ORDER BY `crm`.`date_input` DESC LIMIT 1) AS `last_pay_date`,
		  	SUM(d.`principle_after`) AS principle_after,
		  	SUM(d.`total_interest_after`) AS total_interest_after,
		  	SUM(d.`total_payment_after`) AS total_payment_after,
		  	d.`date_payment` ,
		  	COUNT(l.`id`) AS amount_late,
		  	l.`branch_id`,
		  	`d`.`installment_amount`   AS `times`,
		  	(SELECT p.item_name FROM `ln_ins_product` AS p WHERE p.id = l.`product_id` LIMIT 1) AS productTitle,
		  	COUNT(l.`id`) AS amount_late
  	
	  	FROM
		  	`ln_ins_sales_installdetail` AS d,
		  	`ln_ins_sales_install` AS l,
		  	`ln_ins_client` AS c ,
		  	`ln_branch` AS b
	  	WHERE
		  	d.`is_completed` = 0
		  	AND l.`id` = d.`sale_id`
		  	AND l.`status` = 1
		  	AND l.is_completed = 0
		  	AND d.`status`=1
		  	AND c.`client_id` = l.`customer_id`
		  	AND b.`br_id`=l.branch_id ";
  	$where='';
  	$where.=" AND d.date_payment < '$end_date'";
  	$where.=$this->getAccessPermission('l.`branch_id`');
  	$group_by = " GROUP BY l.`id` ORDER BY d.`date_payment` DESC,c.`client_id` ASC ,d.id ASC ";
  	return $db->fetchAll($sql.$where.$group_by);
  }
  
  function getAllCo($branch_id=null){
  	$db = $this->getAdapter();
  	$sql="";
  	$sql = " SELECT c.`co_id` AS id  ,
  	c.`branch_id`,
  	CONCAT(COALESCE(c.`co_code`,''),'-',COALESCE(c.`co_khname`,'')) AS name ,
  	c.`co_id`,
  	c.co_code,
  	c.`co_khname`
  	FROM `ln_co` AS c WHERE (c.`co_khname`!='') AND c.status=1  " ;
  	if($branch_id!=null){
  		$sql.=" AND c.`branch_id`= $branch_id ";
  	}
  	$sql.=" ORDER BY CONCAT(COALESCE(c.`co_code`,''),'-',COALESCE(c.`co_khname`,'')) DESC ";
  	return $db->fetchAll($sql);
  }
  
  function getCollectPaymentSqlSt(){
  	$sql=" SELECT
			  (SELECT  `ln_branch`.`branch_namekh`  FROM `ln_branch` WHERE (`ln_branch`.`br_id` = `crm`.`branch_id`) LIMIT 1) AS `branch_name`,
			  (SELECT `ln_currency`.`symbol` FROM `ln_currency` WHERE (`ln_currency`.`id` = `crm`.`currency_type`) LIMIT 1) AS `currency_typeshow`,
			  (SELECT `c`.`co_khname` FROM `ln_co` `c` WHERE (`c`.`co_id` = `crm`.`co_id`) LIMIT 1) AS `co_name`,
			  (SELECT `l`.`loan_number` FROM `ln_loan` `l`  WHERE (`l`.`id` = `crm`.`loan_id`) LIMIT 1) AS `loan_number`,
			  (SELECT `c`.`name_kh` FROM `ln_client` `c`  WHERE (`c`.`client_id` = `crm`.`client_id`) LIMIT 1) AS `client_name`,
			  (SELECT `c`.`client_number`  FROM `ln_client` `c` WHERE (`c`.`client_id` = `crm`.`client_id`) LIMIT 1) AS `client_number`,
			  (SELECT `u`.`first_name` FROM `rms_users` `u` WHERE (`u`.`id` = `crm`.`user_id`)) AS `user_name`,
			  `crm`.`id`                   AS `id`,
			  `crm`.`co_id`                AS `co_id`,
			  `crm`.`loan_id`              AS `loan_id`,
			  `crm`.`client_id`            AS `client_id`,
			  `crm`.`receipt_no`           AS `receipt_no`,
			  `crm`.`branch_id`            AS `branch_id`,
			  `crm`.`date_pay`             AS `date_pay`,
			  `crm`.`date_payment`         AS `date_payment`,
			  `crm`.`date_input`           AS `date_input`,
			  `crm`.`note`                 AS `note`,
			  `crm`.`user_id`              AS `user_id`,
			  `crm`.`status`               AS `status`,
			  `crm`.`payment_option`       AS `payment_option`,
			  `crm`.`currency_type`        AS `currency_type`,
			  `crm`.`is_payoff`            AS `is_payoff`,
			  `crm`.`begining_balance`     AS `begining_balance`,
			  `crm`.`total_payment`        AS `total_payment`,
			  `crm`.`principal_amount`     AS `principal_amount`,
			  `crm`.`interest_amount`      AS `interest_amount`,
			  `crm`.`principal_paid`       AS `principal_paid`,
			  `crm`.`interest_paid`        AS `interest_paid`,
			  `crm`.`service_paid`         AS `service_paid`,
			  `crm`.`penalize_paid`        AS `penalize_paid`,
			  `crm`.`total_paymentpaid`    AS `total_paymentpaid`,
			  `crm`.`recieve_amount`       AS `amount_recieve`,
			  `crm`.`return_amount`        AS `return_amount`,
			  `crm`.`penalize_amount`      AS `penelize`,
			  `crm`.`service_chargeamount` AS `service_charge`,
			  `crm`.`client_id`            AS `client_id`,
			  `crm`.`paid_times`           AS `paid_times`,
			  	crm.is_closed,
			  CASE
				WHEN  crm.payment_option = 1 THEN 'បង់ធម្មតា'
				WHEN  crm.payment_option = 2 THEN 'បង់មុន'
				WHEN  crm.payment_option = 3 THEN 'បង់រំលស់ប្រាក់ដើម'
				WHEN  crm.payment_option = 4 THEN 'បង់ផ្តាច់'
			END AS payment_option_title
			
			FROM `ln_client_receipt_money` `crm`
			WHERE ((`crm`.`status` = 1)
			       AND (`crm`.`status` = 1))
  
  	";
  	return $sql;
  }
  
  function sqlGetCalleteral(){
  	$sql="
		  SELECT
		  `c`.`id`                 AS `id`,
		  `c`.`loan_id`            AS `loan_id`,
		  `d`.`number_collecteral` AS `number_collecteral`,
		  `d`.`is_return`          AS `is_return`,
		  `d`.`issue_date`         AS `issue_date`,
		  `d`.`note`               AS `note`,
		   (SELECT `l`.`loan_number` FROM `ln_loan` `l`  WHERE (`l`.`id` = `c`.`loan_id`) LIMIT 1) AS `loan_number`,
		   
		  (SELECT `ln_branch`.`branch_namekh`  FROM `ln_branch` WHERE (`ln_branch`.`br_id` = `c`.`branch_id`)  LIMIT 1) AS `branch_name`,
		  (SELECT `ln_co`.`co_khname` FROM `ln_co` WHERE (`ln_co`.`co_id` = `c`.`co_id`) LIMIT 1) AS `co_id`,
		  `d`.`collecteral_code`   AS `collecteral_code`,
		  
		  `d`.`owner_name`         AS `owner_name`,
		  (SELECT `ln_client`.`client_number`  FROM `ln_client` WHERE (`ln_client`.`client_id` = `c`.`client_id`)  LIMIT 1) AS `client_code`,
		  `d`.`owner_name`         AS `collecteral_type`,
		  (SELECT   `ln_view`.`name_kh` FROM `ln_view` WHERE ((`ln_view`.`type` = 21)  AND (`ln_view`.`key_code` = `d`.`owner_type`))) AS `collecteral_owner`,
		  (SELECT `ct`.`title_en` FROM `ln_callecteral_type` `ct` WHERE (`ct`.`id` = `d`.`collecteral_type`)) AS `collecteral_title_en`,
		  (SELECT `ct`.`title_kh` FROM `ln_callecteral_type` `ct` WHERE (`ct`.`id` = `d`.`collecteral_type`)) AS `collecteral_title_kh`,
		  (SELECT `ln_client`.`name_kh` FROM `ln_client` WHERE (`ln_client`.`client_id` = `c`.`client_id`)  LIMIT 1) AS `name_kh`,
		  (SELECT `ln_client`.`name_en` FROM `ln_client` WHERE (`ln_client`.`client_id` = `c`.`client_id`) LIMIT 1) AS `client_name`,
		  `c`.`client_id`          AS `client_id`,
		  `c`.`branch_id`          AS `branch_id`,
		  (SELECT `ln_client`.`join_with` FROM `ln_client` WHERE (`ln_client`.`client_id` = `c`.`client_id`) LIMIT 1) AS `join_with`,
		  (SELECT `ln_client`.`relate_with` FROM `ln_client` WHERE (`ln_client`.`client_id` = `c`.`client_id`) LIMIT 1) AS `relative`,
		  (SELECT `ln_client`.`spouse_name` FROM `ln_client` WHERE (`ln_client`.`client_id` = `c`.`client_id`) LIMIT 1) AS `spouse_name`,
		  `c`.`date`               AS `date`,
		  `c`.`status`             AS `status`
		FROM (`ln_client_callecteral` `c`
		   JOIN `ln_client_callecteral_detail` `d`)
		WHERE ((`c`.`id` = `d`.`client_coll_id`)
		       AND (`c`.`status` = 1)
		       AND (`d`.`status` = 1))
  	";
  	return $sql;
  }
  function getAllZonebyBranch($branch_id){
  	$sql="SELECT
		  	zone_id AS id ,
		  	CONCAT(COALESCE(zone_name,''),'-',COALESCE(zone_num,'')) AS name,
		  	zone_id,
		  	zone_name,
		  	zone_num
		  	 
		  	FROM ln_zone
		  	WHERE status=1 AND  zone_name!='' ";
  	if($branch_id>0){
  		$sql.=" AND branch_id=".$branch_id;
  	}
  	$db = $this->getAdapter();
  	return $db->fetchAll($sql);
  
  }
  function getAllShopBranch($branch_id=null){
	  	$sql="SELECT shop_id AS id,shop_namekh AS `name` FROM `ln_lns_shop` WHERE shop_namekh!='' ";
	  	if($branch_id!=null){
	  		$sql.="AND branch_id=".$branch_id;
	  	}
	  	$db = $this->getAdapter();
	  	return $db->fetchAll($sql);
  }
}
?>