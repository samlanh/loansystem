<?php
class Pawnshop_IndexController extends Zend_Controller_Action {
    protected $tr;
	public function init()
    {    	
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
		$this->tr=Application_Form_FrmLanguages::getCurrentlanguage();
	}
	public function indexAction(){
		try{
		    if($this->getRequest()->isPost()){
 				$search = $this->getRequest()->getPost();
 			}
			else{
				$search = array(
						'txt_search'=>'',
						'members'=> -1,
						'product_id' => -1,
						'branch_id' => -1,
						'co_id' => -1,
						'status' => -1,
						'currency_type'=>-1,
						'completed_status'=>-1,
						'dach_status'=>-1,
						'start_date'=> date('Y-m-d'),
						'end_date'=>date('Y-m-d'),);
			}
			$db = new Pawnshop_Model_DbTable_DbPawnshop();
			$rs_rows= $db->getAllPawnshop($search);
			$list = new Application_Form_Frmtable();
			$collumns = array("BRANCH_NAME","PAWN_CODE","CUSTOMER_NAME","RECEIPT","PAWN_AMOUNT","ADMIN_FEE",
					"REPAYMENT_TYPE","PAWNSHOP_DURATION",
					"INTEREST RATE","PRODUCT_NAME","PAWN_DATE","PAWN_ENDDATE","BY_USER","COMPLETED","PAWN_DEAD","STATUS");
			$link_info=array('module'=>'pawnshop','controller'=>'index','action'=>'edit',);

			$this->view->list=$list->getCheckList(10, $collumns, $rs_rows,array('branch'=>$link_info,'loan_number'=>$link_info,'receipt_num'=>$link_info,'client_name_kh'=>$link_info,'client_name_en'=>$link_info,'total_capital'=>$link_info),0);
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}	
		$frm = new Pawnshop_Form_FrmPawnshop();
		$frm = $frm->FrmAddLoan();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_search = $frm;
  }
  function addAction()
  {
		if($this->getRequest()->isPost()){
			$_data = $this->getRequest()->getPost();
			try {
				$_dbmodel = new Pawnshop_Model_DbTable_DbPawnshop();
				$_dbmodel->addPawnshop($_data);
				if(!empty($_data['saveclose'])){
					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/pawnshop");
				}else{
					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/pawnshop/index/add");
				}
			}catch (Exception $e) {
				Application_Form_FrmMessage::message("INSERT_FAIL");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		$frm = new Pawnshop_Form_FrmPawnshop();
		$frm_loan=$frm->FrmAddLoan();
		Application_Model_Decorator::removeAllDecorator($frm_loan);
		$this->view->frm_loan = $frm_loan;

		$frmpopup = new Application_Form_FrmPopupGlobal();
		$this->view->frmpupopinfoclient = $frmpopup->frmPopupindividualclient();
		
		$db = new Setting_Model_DbTable_DbLabel();
		$this->view->setting=$db->getAllSystemSetting();
		
		$db = new Application_Model_DbTable_DbGlobal();
		$product = $db->getAllProduct();
		array_unshift($product,array(
		        'id' => -1,
		        'name' => $this->tr->translate('ADD_NEW'),
		) );
	    $this->view->product_store=$product;
	    
	    $fm = new Pawnshop_Form_Frmpawnproduct();
	    $frm = $fm->FrmViewType();
	    Application_Model_Decorator::removeAllDecorator($frm);
	    $this->view->Form_Frmcallecterall = $frm;
	    
	    $session_user=new Zend_Session_Namespace(SYSTEM_SES);
	    $this->view->user_name = $session_user->last_name .' '. $session_user->first_name;
	    $this->view->leveluser = $session_user->level;
	    
	    $key = new Application_Model_DbTable_DbKeycode();
	    $this->view->data=$key->getKeyCodeMiniInv(TRUE);
	    
	    $db = new Setting_Model_DbTable_DbLabel();
	    $this->view->setting=$db->getAllSystemSetting();
	}
public function editAction(){
	if($this->getRequest()->isPost()){
		$_data = $this->getRequest()->getPost();
		try {
			$_dbmodel = new Pawnshop_Model_DbTable_DbPawnshop();
			$_dbmodel->updatePawnshop($_data);
			Application_Form_FrmMessage::Sucessfull("EDIT_SUCCESS","/pawnshop");

		}catch (Exception $e) {
			Application_Form_FrmMessage::message("INSERT_FAIL");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	}
	$db_g = new Application_Model_DbTable_DbGlobal();
	$id = $this->getRequest()->getParam('id');
	$id = empty($id) ? 0 : $id;
	
	$db = new Pawnshop_Model_DbTable_DbPawnshop();
	$haspayment = $db->getPawnshopDetailByIdHaspaid($id);
	if (!empty($haspayment)){
		Application_Form_FrmMessage::Sucessfull("This pawn has some payment can not edit","/pawnshop",2);
		exit();
	}
	$row = $db->getPawnshopById($id);
	if (empty($row)){
		Application_Form_FrmMessage::Sucessfull("NO_RECORD","/pawnshop",2);
		exit();
	}
	
	$this->view->rs = $row;
	$frm = new Pawnshop_Form_FrmPawnshop();
	$frm_loan=$frm->FrmAddLoan($row);
	Application_Model_Decorator::removeAllDecorator($frm_loan);
	$this->view->frm_loan = $frm_loan;

	$frmpopup = new Application_Form_FrmPopupGlobal();
	$this->view->frmpupopinfoclient = $frmpopup->frmPopupindividualclient();

	$db = new Setting_Model_DbTable_DbLabel();
	$this->view->setting=$db->getAllSystemSetting();

	$db = new Application_Model_DbTable_DbGlobal();
	$product = $db->getAllProduct();
	array_unshift($product,array(
	        'id' => -1,
	        'name' => $this->tr->translate('ADD_NEW'),
	) );
    $this->view->product_store=$product;
    
    $fm = new Pawnshop_Form_Frmpawnproduct();
    $frm = $fm->FrmViewType();
    Application_Model_Decorator::removeAllDecorator($frm);
    $this->view->Form_Frmcallecterall = $frm;
    
    $key = new Application_Model_DbTable_DbKeycode();
    $this->view->data=$key->getKeyCodeMiniInv(TRUE);
}	
public function addloanAction(){
	if($this->getRequest()->isPost()){
		$data=$this->getRequest()->getPost();
		$db = new Pawnshop_Model_DbTable_DbPawnshop();
		$id = $db->addPawnshop($data);
		$suc = array('sms'=>'ប្រាក់ឥណទានត្រូវបានបញ្ចូលដោយជោគជ័យ !');
		print_r(Zend_Json::encode($suc));
		exit();
	}
}
public function viewAction(){
	$id = $this->getRequest()->getParam('id');
	$id = empty($id) ? 0 : $id;
	$db_g = new Application_Model_DbTable_DbGlobal();
	if(empty($id)){
		Application_Form_FrmMessage::Sucessfull("NO_RECORD","/loan/index/index",2);
		exit();
	}
	$db = new Loan_Model_DbTable_DbLoanIL();
	$row = $db->getLoanviewById($id);
	$this->view->tran_rs = $row;
}
function getLoanlevelAction(){
	if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db = new Pawnshop_Model_DbTable_DbPawnshop();
			$row = $db->getLoanLevelByClient($data['client_id'],$data['type']);
			print_r(Zend_Json::encode($row));
		    exit();
	}
	
}
function getLoanlevelschAction(){
	if($this->getRequest()->isPost()){
		$data = $this->getRequest()->getPost();
		$db = new Pawnshop_Model_DbTable_DbPawnshop();
		$row = $db->getLoanRescheduleLevel($data['loan_code'],$data['type']);
		print_r(Zend_Json::encode($row));
		exit();
	}

}
public function getLoaninfoAction(){//from repayment schedule
	if($this->getRequest()->isPost()){
		$data=$this->getRequest()->getPost();
		$db=new Loan_Model_DbTable_DbRepaymentSchedule();
		$row=$db->getLoanInfo($data['loan_id']);
		print_r(Zend_Json::encode($row));
		exit();
	}
}
function getloanBymemberidAction(){
	if($this->getRequest()->isPost()){
		$data=$this->getRequest()->getPost();
		$db=new Loan_Model_DbTable_DbRepaymentSchedule();
		$row=$db->getLoanInfoBymemberId($data['member_id']);
		print_r(Zend_Json::encode($row));
		exit();
	}
}

public function testAction($result=null,$table='ln_branch'){

}
function addpawnshoptestAction(){
	if($this->getRequest()->isPost()){
		$_data = $this->getRequest()->getPost();
		$_dbmodel = new Pawnshop_Model_DbTable_DbPawnshop();
		$rows_return=$_dbmodel->addPawnshoptest($_data);
		print_r(Zend_Json::encode($rows_return));
		exit();
	}
}
function getloannumberAction(){//get loan level
	if($this->getRequest()->isPost()){
		$data = $this->getRequest()->getPost();
		$db = new Application_Model_DbTable_DbGlobal();
		$loan_number = $db->getPawnshoNumber($data['customer_id']);
		print_r(Zend_Json::encode($loan_number));
		exit();
	}
}
function getreceiptnumberAction(){
	if($this->getRequest()->isPost()){
		$data = $this->getRequest()->getPost();
		$db = new Application_Model_DbTable_DbGlobal();
		$loan_number = $db->getPawnReceipt($data['branch_id']);
		print_r(Zend_Json::encode($loan_number));
		exit();
	}
}
function addNewloantypeAction(){
	if($this->getRequest()->isPost()){
		$data = $this->getRequest()->getPost();
		$data['status']=1;
		$data['display_by']=1;
		$db = new Other_Model_DbTable_DbLoanType();
		$id = $db->addViewType($data);
		print_r(Zend_Json::encode($id));
		exit();
		}
	}
}

