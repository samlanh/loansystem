<?php
class Installment_PaymentController extends Zend_Controller_Action {
    public function init()
    {    	
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	public function indexAction(){
		try{
			$db = new Installment_Model_DbTable_DbInstallmentPayment();
		if($this->getRequest()->isPost()){
				$formdata=$this->getRequest()->getPost();
				$search = array(
					'adv_search' => $formdata['adv_search'],
					'member'=>$formdata['member'],
					'start_date'=>$formdata['start_date'],
					'end_date'=>$formdata['end_date'],
					'status'=>$formdata['status'],
					'branch_id'		=>	$formdata['branch_id'],
					'paymnet_type'	=> $formdata["paymnet_type"],
					);
			}
			else{
				$search = array(
					'adv_search' => '',
					'member' => -1,
					'start_date'=> date('Y-m-d'),
					'end_date'=>date('Y-m-d'),
					'branch_id'		=>	-1,
					'paymnet_type'	=> -1,
					'status'=>"-1",);
			}
			$rs_rows= $db->getAllinstallmentpayment($search);
			$result = array();
			$list = new Application_Form_Frmtable();
			$collumns = array("BRANCH_NAME","INSTALLMENT_NO","CUSTOMER_NAME","RECIEPT_NO","PAID_TIME","TOTAL_PRINCEPLE",
					"TOTAL_INTEREST","PENALIZE AMOUNT","RECEIVE_AMOUNT","PAY_DATE","DATE","STATUS"
				);
			$link=array(
					'module'=>'loan','controller'=>'payment','action'=>'edit',
			);
			$this->view->list=$list->getCheckList(10, $collumns, $rs_rows,array());
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}	
		$frm = new Installment_Form_FrmSearchInstallment();
		$frm = $frm->AdvanceSearch();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_search = $frm;
  }
  function addAction()
  {
  	$db = new Installment_Model_DbTable_DbInstallmentPayment();
  	$db_global = new Application_Model_DbTable_DbGlobal();
		if($this->getRequest()->isPost()){
			$_data = $this->getRequest()->getPost();
			$identify = $_data["identity"];
			try {
				if($identify==""){
					Application_Form_FrmMessage::Sucessfull("Client no laon to pay!","/installment/payment/");
				}else {
					$db->addILPayment($_data);
					if (isset($_data['save_new'])){
						Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/installment/payment/add");
					}else{
						Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/installment/payment/");
					}
				}
			}catch (Exception $e) {
				Application_Form_FrmMessage::message("INSERT_FAIL");
				$err =$e->getMessage();
				Application_Model_DbTable_DbUserLog::writeMessageError($err);
			}
		}
		$frm = new Loan_Form_FrmIlPayment();
		$frm_loan=$frm->FrmAddIlPayment();
		Application_Model_Decorator::removeAllDecorator($frm_loan);
		$this->view->frm_ilpayment = $frm_loan;
		
		$list = new Application_Form_Frmtable();
		$collumns = array("ឈ្មោះមន្ត្រីឥណទាន","ថ្ងៃបង់ប្រាក់","ប្រាក់ត្រូវបង់","ប្រាក់ដើមត្រូវបង់","អាត្រាការប្រាក់","ប្រាក់ផាកពិន័យ","ប្រាក់បានបង់សរុប","សមតុល្យ","កំណត់សម្គាល់");
		$link=array(
				'module'=>'group','controller'=>'Client','action'=>'edit',
		);
		$this->view->list=$list->getCheckList(0, $collumns, array(),array('client_number'=>$link,'name_kh'=>$link,'name_en'=>$link));
		
		$db_keycode = new Application_Model_DbTable_DbKeycode();
		$this->view->keycode = $db_keycode->getKeyCodeMiniInv();
		
		$this->view->graiceperiod = $db_keycode->getSystemSetting(9);
		$session_user=new Zend_Session_Namespace(SYSTEM_SES);
		$this->view->user_name = $session_user->last_name .' '. $session_user->first_name;
		
		$this->view->loan_number = $db_global->getSaleinstallmentByBranch(1);
		
		$id = $this->getRequest()->getParam('id');
		if(!empty($id)){
			if(empty($id)){$id=0;}
			$this->view->rsid=$id;
			$db = new Installment_Model_DbTable_DbInstallment();
			$this->view->rsloan =  $db->getTranLoanByIdWithBranch($id,1);
		}
		
		$frmpopup = new Application_Form_FrmPopupGlobal();
		$this->view->saleInstallReceipt = $frmpopup->getOfficailReceiptSaleInstall();
	}	
	
	function editAction()
	{
		$id = $this->getRequest()->getParam("id");
		$db_global = new Application_Model_DbTable_DbGlobal();
		$db = new Installment_Model_DbTable_DbInstallmentPayment();
		$db1 = new Loan_Model_DbTable_DbGroupPayment();
		if($this->getRequest()->isPost()){
			$_data = $this->getRequest()->getPost();
			$identify = $_data["identity"];
			try {
				if($identify==""){
					Application_Form_FrmMessage::Sucessfull("Client no laon to pay!","/loan/ilpayment/");
				}else{
					$db->updateIlPayment($_data);
					//Application_Form_FrmMessage::Sucessfull("EDIT_SUCCESS","/loan/ilpayment/");
				}
			}catch (Exception $e) {
				//echo $e->getMessage();
				Application_Form_FrmMessage::message("INSERT_FAIL");
				$err =$e->getMessage();
				Application_Model_DbTable_DbUserLog::writeMessageError($err);
			}
		}
		$payment_il = $db->getIlPaymentByID($id);
		$this->view->ilPaymentById= $payment_il;
		
		$getIlDetail = $db->getIlDetail($id);
		
		$frm = new Loan_Form_FrmIlPayment();
		$frm_loan=$frm->FrmAddIlPayment($payment_il);
		Application_Model_Decorator::removeAllDecorator($frm_loan);
		$this->view->frm_ilpayment = $frm_loan;
		$this->view->ilPayent = $getIlDetail;
		$this->view->client_id=$payment_il["group_id"];
		$this->view->client_code=$payment_il["group_id"];
		$this->view->branch_id=$payment_il["branch_id"];
		$this->view->loan_number=$payment_il["loan_numbers"];
		
		$this->view->client = $db->getAllClient();
		$this->view->clientCode = $db->getAllClientCode();
		
		$db_keycode = new Application_Model_DbTable_DbKeycode();
		$this->view->keycode = $db_keycode->getKeyCodeMiniInv();
		
		$this->view->graiceperiod = $db_keycode->getSystemSetting(9);
		
		$session_user=new Zend_Session_Namespace(SYSTEM_SES);
		$this->view->user_name = $session_user->last_name .' '. $session_user->first_name;
		
// 		$this->view->loan_numbers = $db_global->getLoanNumberByBranch(1);
		$this->view->loan_numbers = $db->getAllLoanNumberByBranch(1);
	}
	
	function deleteAction(){
		$id = $this->getRequest()->getParam("id");
		$id = empty($id)?0:$id;
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$db = new Installment_Model_DbTable_DbInstallmentPayment();
		$payment_il = $db->getInstallPaymentBYId($id);
		if (empty($payment_il)){
			Application_Form_FrmMessage::Sucessfull("NO_RECORD",'/installment/payment',2);
			exit();
		}
		if (!empty($payment_il)){
			if ($payment_il['is_closed']==1){
				Application_Form_FrmMessage::Sucessfull("Can not delete this record","/installment/payment");exit();
			}
			$sale_id = empty($payment_il['loan_id'])?0:$payment_il['loan_id'];
  			$lastPaymentRecord = $db->getLastPaymentRecord($sale_id);
  			$lastPayId = empty($lastPaymentRecord['id'])?0:$lastPaymentRecord['id'];
  			if ($lastPayId!=$id){
  				Application_Form_FrmMessage::Sucessfull("Only Last Payment Receipt Can Delete","/installment/payment",2);
				exit();
  			}
		}
		$delete_sms=$tr->translate('CONFIRM_DELETE');
		echo "<script language='javascript'>
		var txt;
		var r = confirm('$delete_sms');
		if (r == true) {";
		echo "window.location ='".Zend_Controller_Front::getInstance()->getBaseUrl()."/installment/payment/deletereceipt/id/".$id."'";
		echo"}";
		echo"else {";
		echo "window.location ='".Zend_Controller_Front::getInstance()->getBaseUrl()."/installment/payment'";
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
		$db = new Installment_Model_DbTable_DbInstallmentPayment();
		try {
			$dbacc = new Application_Model_DbTable_DbUsers();
			$rs = $dbacc->getAccessUrl($module,$controller,"delete");
			if(!empty($rs)){
				$db->deleteRecord($id);
				Application_Form_FrmMessage::Sucessfull("DELETE_SUCCESS","/installment/payment/");
			}
			Application_Form_FrmMessage::Sucessfull("You don't have permission to delete this record","/installment/payment/",2);
		}catch (Exception $e) {
			Application_Form_FrmMessage::message("INSERT_FAIL");
			echo $e->getMessage();
		}
	}
	
// 	function getAllLoanByCoIdAction(){
// 		if($this->getRequest()->isPost()){
// 			$data = $this->getRequest()->getPost();
			
// 			$db = new Installment_Model_DbTable_DbInstallmentPayment();
// 			$row = $db->getAllLoanByCoId($data);
// 			print_r(Zend_Json::encode($row));
// 			exit();
// 		}
// 	}
	function getLoannumberAction(){
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db = new Loan_Model_DbTable_DbLoanIL();
			$row = $db->getLoanPaymentByLoanNumber($data);
			print_r(Zend_Json::encode($row));
			exit();
		}
	}
	
	function getLastPayDateAction(){// get last payment date in loan fundetail by for caculate interest in payoff for client 
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db = new Installment_Model_DbTable_DbInstallmentPayment();
			$row = $db->getLastPayDate($data);
			print_r(Zend_Json::encode($row));
			exit();
		}
	}
	function getLastPaymentDateByLoanAction(){// get last payment in client reciept money for caculate penalize for client 
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db = new Installment_Model_DbTable_DbInstallmentPayment();
			$row = $db->getLastPaymentDate($data);
			print_r(Zend_Json::encode($row));
			exit();
		}
	}
	public function showBarcodesAction(){
		$this->_helper->layout()->disableLayout();
		$id = $this->getRequest()->getParam('id');
		if(!empty($id)){
			$ids=explode(',', $id);
			$this->view->pro_id = $ids;
		}
		else{
		}
	}
	public function generateBarcodeAction(){
		$loan_code = $this->getRequest()->getParam('loan_code');
		header('Content-type: image/png');
		$this->_helper->layout()->disableLayout();
		$barcodeOptions = array('text' => "$loan_code",'barHeight' => 40);
		$rendererOptions = array();
		$renderer = Zend_Barcode::factory(
				'code128', 'image', $barcodeOptions, $rendererOptions
		)->render();
	}
	function getinstalllmentpaymentAction(){//តារាងត្រូវបង់អីឡូវ
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db = new Installment_Model_DbTable_DbInstallmentPayment();
			$row = $db->getLoanPaymentByLoanNumber($data);
			print_r(Zend_Json::encode($row));
			exit();
		}
	}
	function getallpaymentAction(){//tab2
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db = new Installment_Model_DbTable_DbInstallmentPayment();
			$row = $db->getAllLoanPaymentByLoanNumber($data);
			print_r(Zend_Json::encode($row));
			exit();
		}
	}
	function getschedulepaidAction(){//tab3
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db = new Installment_Model_DbTable_DbInstallmentPayment();
			$loan_number = $data["loan_number"];
			$row = $db->getloanschedulepaid($loan_number);
			print_r(Zend_Json::encode($row));
			exit();
		}
	}
	function getrpnumberAction(){//get receipt by co
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db = new Installment_Model_DbTable_DbInstallmentPayment();
			$co_id = $data["co_id"];
			$row = $db->getIlPaymentRPNumber($co_id);
			print_r(Zend_Json::encode($row));
			exit();
		}
	}
	
	function addpaymentajaxAction(){
		if($this->getRequest()->isPost()){
			$_data = $this->getRequest()->getPost();
			$db = new Installment_Model_DbTable_DbInstallmentPayment();
			$receipt_id = $db->addILPayment($_data);
			$row = $db->getInstallPaymentBYId($receipt_id);
			print_r(Zend_Json::encode($row));
			exit();
		}
	}
}