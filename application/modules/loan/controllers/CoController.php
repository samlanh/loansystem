<?php
class Loan_CoController extends Zend_Controller_Action {
	const REDIRECT_URL = '/loan';
    public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
    	$this->tr = Application_Form_FrmLanguages::getCurrentlanguage();
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	
	public function indexAction(){
		try{
			$db = new Other_Model_DbTable_DbCreditOfficer();
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
			}
			else{
				$search = array(
						'adv_search' => '',
						'status_search' => 1);
			}
			$rs_rows= $db->getAllCreditOfficer($search);
			$list = new Application_Form_Frmtable();
			$collumns = array("BRANCH_NAME","STAFF_CODE","NAME_KH","NAME_EN","NATIONAL_ID","ADDRESS","PHONE",
					"EMAIL","DEGREE","DEPARTMENT","ANNUAL_LIVES","STATUS");
			$link=array(
					'module'=>'loan','controller'=>'co','action'=>'edit',
			);
			$this->view->list=$list->getCheckList(0, $collumns,$rs_rows,array('co_code'=>$link,'co_khname'=>$link,'co_engname'=>$link));
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
		
		$fm = new Other_Form_FrmCO();
   		$frm_co=$fm->FrmAddCO();
   		Application_Model_Decorator::removeAllDecorator($frm_co);
   		$this->view->frm_co = $frm_co;
	
	}
	
   function addAction(){
   	if($this->getRequest()->isPost()){
   		$_data = $this->getRequest()->getPost();
   		$db_co = new Other_Model_DbTable_DbCreditOfficer();
   		 
   		try{
   			$db_co->addCreditOfficer($_data);
   				if(!empty($_data['save_close'])){
   					Application_Form_FrmMessage::Sucessfull('INSERT_SUCCESS', self::REDIRECT_URL . '/co/index');
				}else{
					Application_Form_FrmMessage::Sucessfull('INSERT_SUCCESS', self::REDIRECT_URL . '/co/add');
				}
				Application_Form_FrmMessage::Sucessfull('INSERT_SUCCESS', self::REDIRECT_URL . '/co/add');
   		}catch(Exception $e){
   			Application_Form_FrmMessage::message("INSERT_FAIL");
   			$err =$e->getMessage();
   			Application_Model_DbTable_DbUserLog::writeMessageError($err);
   		}
   	}
   	$frm = new Other_Form_FrmCO();
   	$frm_co=$frm->FrmAddCO();
   	Application_Model_Decorator::removeAllDecorator($frm_co);
   	$this->view->frm_co = $frm_co;
   	
   	$frmpopup = new Application_Form_FrmPopupGlobal();
   	$this->view->frmpopupdepartment = $frmpopup->frmPopupDepartment();
   }
   
   function editAction(){
	   	$db_co = new Other_Model_DbTable_DbCreditOfficer();
	   	if($this->getRequest()->isPost()){
	   		$_data = $this->getRequest()->getPost();
	   		try{
	   			$db_co->addCreditOfficer($_data);
	   			Application_Form_FrmMessage::Sucessfull("EDIT_SUCCESS",'/loan/co');
	   		}catch(Exception $e){
	   			Application_Form_FrmMessage::message("EDIT_FAIL");
	   			$err =$e->getMessage();
	   			Application_Model_DbTable_DbUserLog::writeMessageError($err);
	   		}
	   	}
	   	$id = $this->getRequest()->getParam("id");
	   	$row = $db_co->getCOById($id);
	   	$this->view->row=$row;
	   	$this->view->photo = $row['photo'];
		if(empty($row)){
			Application_Form_FrmMessage::Sucessfull($this->tr->translate('NO_RECORD'), '/loan/co',2);
			exit();
		}
	   	$frm = new Other_Form_FrmCO();
	   	$frm_co=$frm->FrmAddCO($row);
	   	Application_Model_Decorator::removeAllDecorator($frm_co);
	   	$this->view->frm_co = $frm_co;
	   	
	   	$frmpopup = new Application_Form_FrmPopupGlobal();
	   	$this->view->frmpopupdepartment = $frmpopup->frmPopupDepartment();
   }
   
   public function addNewcoAction(){
   	if($this->getRequest()->isPost()){
   		$data = $this->getRequest()->getPost();
//    		$data['status']=1;
//    		$data['co_id']='';
//    		$data['name_kh']='';
   		$db_co = new Other_Model_DbTable_DbCreditOfficer();
   		$id = $db_co->addCoByAjax($data);
   		print_r(Zend_Json::encode($id));
   		exit();
   	}
   }
   
	 public function addnewdepartmentAction(){
	   	if($this->getRequest()->isPost()){
	   		$db = new Payroll_Model_DbTable_DbDepartment();
	   		$_data = $this->getRequest()->getPost();
	   		$id = $db->addDepartmentPop($_data);
	   		
	   		$dbGb = new Application_Model_DbTable_DbGlobal();
	   		$rs = $dbGb->getAllDepartment(null,null);
	   		
	   		array_unshift($rs,array ( 'id' => "",'name' => $this->tr->translate("SELECT_DEPARTMENT")), array ( 'id' => -1,'name' => $this->tr->translate("ADD_NEW")));
	   		$arr=array(
	   				'datastore' =>$rs,
	   				'id'		=>$id
	   		);
	   		
	   		print_r(Zend_Json::encode($arr));
	   		exit();
	   	}
   }
   function getstaffcodeAction(){
   	if($this->getRequest()->isPost()){
   		$db = new Application_Model_DbTable_DbGlobal();
   		$_data = $this->getRequest()->getPost();
   		$id = $db->getStaffNumberByBranch($_data['branch_id']);
   		print_r(Zend_Json::encode($id));
   		exit();
   	}
   }
}
