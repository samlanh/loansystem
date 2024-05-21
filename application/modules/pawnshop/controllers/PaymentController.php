<?php
class Pawnshop_PaymentController extends Zend_Controller_Action {
    public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	private $sex=array(1=>'M',2=>'F');
	public function indexAction(){
		try{
			$db = new Pawnshop_Model_DbTable_DbPayment();
		if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
				$search['advance_search'] = $search['advance_search'];
			}else{
				$search = array(
						'advance_search' => '',
						'client_name' => -1,
						'start_date'=> date('Y-m-d'),
						'end_date'=>date('Y-m-d'),
						'branch_id'		=>	-1,
					);
			}
			$rs_rows= $db->getAllPawnPayment($search);
			$result = array();
			$list = new Application_Form_Frmtable();
			$collumns = array("BRANCH_NAME","PAWN_CODE","CUSTOMER_NAME","RECIEPT_NO","PAID_PRINCIPAL",
					"INTERREST_AMOUNT","TOTAL_PENELIZE","RECEIVE_AMOUNT","PAY_DATE","DAY_PAYMENT","STATUS"
				);
			$link=array(
					'module'=>'pawnshop','controller'=>'payment','action'=>'edit',);
			$this->view->list=$list->getCheckList(10, $collumns, $rs_rows,array());
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			echo $e->getMessage();
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}	
		$frm = new Pawnshop_Form_FrmSearchPayment();
		$fm = $frm->AdvanceSearch();
		Application_Model_Decorator::removeAllDecorator($fm);
		$this->view->frm_search = $fm;
  }
  function addAction()
  {
		  	$db = new Pawnshop_Model_DbTable_DbPayment();
		  	$db_global = new Application_Model_DbTable_DbGlobal();
		  	if($this->getRequest()->isPost()){
		  		$_data = $this->getRequest()->getPost();
		  		$identify = $_data["identity"];
		  		try {
		  			if($identify==""){
		  				Application_Form_FrmMessage::Sucessfull("Client no laon to pay!","/pawnshop/payment");
		  			}else {
		  				$db->addPawnpayment($_data);
		  			}
		  		}catch (Exception $e) {
		  			Application_Form_FrmMessage::message("INSERT_FAIL");
		  			$err =$e->getMessage();
		  			Application_Model_DbTable_DbUserLog::writeMessageError($err);
		  		}
		  	}
  		$frm = new Pawnshop_Form_FrmPayment();
  		$frm_loan=$frm->FrmAddPayment();
  		Application_Model_Decorator::removeAllDecorator($frm_loan);
  		$this->view->frm_ilpayment = $frm_loan;
		  	
	  	$db_keycode = new Application_Model_DbTable_DbKeycode();
	  	$this->view->keycode = $db_keycode->getKeyCodeMiniInv();
	  	
	  	$this->view->graiceperiod = $db_keycode->getSystemSetting(9);
		  	
		$db_global = new Pawnshop_Model_DbTable_DbPayment();
		$this->view->client = $db_global->getClientNamebyBranch();
		$this->view->clientCode = $db_global->getClientCodebyBranch();
		
		$session_user=new Zend_Session_Namespace(SYSTEM_SES);
		$this->view->user_name = $session_user->last_name .' '. $session_user->first_name;
		$this->view->leveluser = $session_user->level;
		
		
		$this->view->loan_number = $db_global->getPawnAccountNumber(1);
		$id = $this->getRequest()->getParam('id');
		if(!empty($id)){
			if(empty($id)){
				$id=0;
			}
			$this->view->rsid=$id;
			$db = new Pawnshop_Model_DbTable_DbPawnshop();
			$this->view->rsloan =  $db->getPawnshopById($id);
		}
		
		$frmpopup = new Application_Form_FrmPopupGlobal();
		$this->view->pawnReceipt = $frmpopup->getOfficailReceiptPawn();
	}
	function deleteAction(){
		$id = $this->getRequest()->getParam("id");
		$id = empty($id)?0:$id;
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$db = new Pawnshop_Model_DbTable_DbPayment();
		$payment_il = $db->getPawnShopPaymentByID($id);
		if (!empty($payment_il)){
			if ($payment_il['is_closed']==1){
				Application_Form_FrmMessage::Sucessfull("Can not delete this record","/pawnshop/payment");exit();
			}
			$sale_id = empty($payment_il['loan_id'])?0:$payment_il['loan_id'];
  			$lastPaymentRecord = $db->getLastPaymentRecord($sale_id);
  			$lastPayId = empty($lastPaymentRecord['id'])?0:$lastPaymentRecord['id'];
  			if ($lastPayId!=$id){
  				Application_Form_FrmMessage::Sucessfull("Only Last Payment Receipt Can Delete","/pawnshop/payment",2);
				exit();
  			}
		}
		$delete_sms=$tr->translate('CONFIRM_DELETE');
		echo "<script language='javascript'>
		var txt;
		var r = confirm('$delete_sms');
		if (r == true) {";
		echo "window.location ='".Zend_Controller_Front::getInstance()->getBaseUrl()."/pawnshop/payment/deletereceipt/id/".$id."'";
		echo"}";
		echo"else {";
		echo "window.location ='".Zend_Controller_Front::getInstance()->getBaseUrl()."/pawnshop/payment'";
		echo"}
		</script>";
	}
		
	function deletereceiptAction()
	{//check permission first
		$request=Zend_Controller_Front::getInstance()->getRequest();
		$action=$request->getActionName();
		$controller=$request->getControllerName();
		$module=$request->getModuleName();
		
		$id = $this->getRequest()->getParam("id");
		$db = new Pawnshop_Model_DbTable_DbPayment();
		try {
			$dbacc = new Application_Model_DbTable_DbUsers();
			$rs = $dbacc->getAccessUrl($module,$controller,"delete");
			$rs=1;
			if(!empty($rs)){
				$db->deletePawnpayment($id);
				Application_Form_FrmMessage::Sucessfull("DELETE_SUCCESS","/pawnshop/payment/");
			}else{
				Application_Form_FrmMessage::Sucessfull("NO_PERMISSION","/pawnshop/payment/",2);
			}
		}catch (Exception $e) {
			Application_Form_FrmMessage::message("INSERT_FAIL");
			echo $e->getMessage();
		}
	}
	function editAction()
	{
		$id = $this->getRequest()->getParam("id");
		$db_global = new Application_Model_DbTable_DbGlobal();
			
		$db = new Loan_Model_DbTable_DbLoanILPayment();
		if($this->getRequest()->isPost()){
			$_data = $this->getRequest()->getPost();
			$identify = $_data["identity"];
			try {
				if($identify==""){
					Application_Form_FrmMessage::Sucessfull("Client no laon to pay!","/loan/ilpayment/");
				}else{
					$db->updateIlPayment($_data);
				}
			}catch (Exception $e) {
				Application_Form_FrmMessage::message("INSERT_FAIL");
				$err =$e->getMessage();
				Application_Model_DbTable_DbUserLog::writeMessageError($err);
			}
		}
		$frm = new Pawnshop_Form_FrmPayment();
		$frm_loan=$frm->FrmAddPayment();
		Application_Model_Decorator::removeAllDecorator($frm_loan);
		$this->view->frm_ilpayment = $frm_loan;
		//test
		$this->view->frm_ilpayment = $frm_loan;
	}


// 	function getLoannumberAction(){
// 		if($this->getRequest()->isPost()){
// 			$data = $this->getRequest()->getPost();
// 			$db = new Loan_Model_DbTable_DbLoanIL();
// 			$row = $db->getLoanPaymentByLoanNumber($data);
// 			print_r(Zend_Json::encode($row));
// 			exit();
// 		}
// 	}
	function getsavingpaymentAction(){//tab1
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db = new Pawnshop_Model_DbTable_DbPayment();
			$row = $db->getPawnPaymentByID($data['pawnid']);
			print_r(Zend_Json::encode($row));
			exit();
		}
	}
	function getpawndetailAction(){//tab2
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db = new Pawnshop_Model_DbTable_DbPayment();
			$row = $db->getAllLoanPaymentByLoanNumber($data);
			print_r(Zend_Json::encode($row));
			exit();
		}
	}
	function getschedulepaidAction(){//tab3
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db = new Pawnshop_Model_DbTable_DbPayment();
			$loan_number = $data["pawnid"];
			$row = $db->getschedulepaid($loan_number);
			print_r(Zend_Json::encode($row));
			exit();
		}
	}
	function addpaymentajaxAction(){
		if($this->getRequest()->isPost()){
			$_data = $this->getRequest()->getPost();
			$db = new Pawnshop_Model_DbTable_DbPayment();
			$receipt_id = $db->addPawnpayment($_data);
			$row = $db->getPawnPaymentByIdForPrint($receipt_id);
			print_r(Zend_Json::encode($row));
			exit();
		}
	}
	
}

