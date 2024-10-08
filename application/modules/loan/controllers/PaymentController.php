<?php
class Loan_PaymentController extends Zend_Controller_Action {
    public function init()
    {    	
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	public function indexAction(){
		try{
			$db = new Loan_Model_DbTable_DbLoanILPayment();
		if($this->getRequest()->isPost()){
				$formdata=$this->getRequest()->getPost();
				$search = array(
					'advance_search' => $formdata['advance_search'],
					'client_name'=>$formdata['client_name'],
					'start_date'=>$formdata['start_date'],
					'end_date'=>$formdata['end_date'],
					'status'=>$formdata['status'],
					'branch_id'		=>	$formdata['branch_id'],
					'co_id'		=>	$formdata['co_id'],
					'paymnet_type'	=> $formdata["paymnet_type"],
				);
			}
			else{
				$search = array(
					'adv_search' => '',
					'client_name' => -1,
					'start_date'=> date('Y-m-d'),
					'end_date'=>date('Y-m-d'),
					'branch_id'		=>	-1,
					'co_id'		=> -1,
					'paymnet_type'	=> -1,
					'status'=>"",
				);
			}
			$rs_rows= $db->getAllIndividuleLoan($search);
			$result = array();
			$list = new Application_Form_Frmtable();
			$collumns = array("BRANCH_NAME","LOAN_NO","CUSTOMER_NAME","RECIEPT_NO","TOTAL_PRINCEPLE","TOTAL_INTEREST","PENALIZE AMOUNT","SERVICE_CHARGE","RECEIVE_AMOUNT","PAY_DATE","DATE","CO_NAME"
				);
// 			"DELETE","PAYMENT_RECEIPT"
			$link=array(
					'module'=>'loan','controller'=>'payment','action'=>'edit',
			);
// 			$link1=array(
// 					'module'=>'loan','controller'=>'payment','action'=>'delete',
// 			);
// 			$link2=array(
// 					'module'=>'report','controller'=>'loan','action'=>'recieptpayment',
// 			);
// 			$tr = Application_Form_FrmLanguages::getCurrentlanguage();
// 			$delete = $tr->translate("DELETE");
// 			$reciept = $tr->translate("PAYMENT_RECEIPT");
// 			$delete=>$link1,$reciept=>$link2
			$this->view->list=$list->getCheckList(10, $collumns, $rs_rows,array());
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}	
		$frm = new Loan_Form_FrmSearchGroupPayment();
		$fm = $frm->AdvanceSearch();
		Application_Model_Decorator::removeAllDecorator($fm);
		$this->view->frm_search = $fm;
  }
  function addAction()
  {
  	$db = new Loan_Model_DbTable_DbLoanILPayment();
  	$db_global = new Application_Model_DbTable_DbGlobal();
		if($this->getRequest()->isPost()){
			$_data = $this->getRequest()->getPost();
			$identify = $_data["identity"];
			try {
				if($identify==""){
					Application_Form_FrmMessage::Sucessfull("Client no laon to pay!","/loan/payment/",2);
					exit();
				}else {
					$db->addILPayment($_data);
					if(!empty($_data['save_close'])){
			        	//Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/loan/payment/index");
					}else{
						//Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/loan/payment/add");
					}
					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/loan/payment/index");
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
		
		
		$db_keycode = new Application_Model_DbTable_DbKeycode();
		$this->view->keycode = $db_keycode->getKeyCodeMiniInv();
		
		$this->view->graiceperiod = $db_keycode->getSystemSetting(9);
		
		$this->view->client = $db->getAllClient();
		$this->view->clientCode = $db->getAllClientCode();
		
		$session_user=new Zend_Session_Namespace(SYSTEM_SES);
		$this->view->user_name = $session_user->last_name .' '. $session_user->first_name;
		$this->view->user_type = $session_user->level;
		$this->view->loan_number = $db_global->getLoanNumberByBranch(1);
		
		$id = $this->getRequest()->getParam('id');
		if(!empty($id)){
			if(empty($id)){$id=0;}
			$this->view->rsid=$id;
			$db = new Loan_Model_DbTable_DbLoandisburse();
			$this->view->rsloan =  $db->getTranLoanByIdWithBranch($id,1);
		}
		
		$frmpopup = new Application_Form_FrmPopupGlobal();
		$this->view->loanReceipt = $frmpopup->getOfficailReceiptLoan();
	}	
	function editAction()
	{
		$id = $this->getRequest()->getParam("id");
		$db_global = new Application_Model_DbTable_DbGlobal();
		$db = new Loan_Model_DbTable_DbLoanILPayment();
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
		$db = new Loan_Model_DbTable_DbLoanILPayment();
		$payment_il = $db->getLoanPaymentById($id);
		if (!empty($payment_il)){
			if ($payment_il['is_closed']==1){
				Application_Form_FrmMessage::Sucessfull("Can not delete this record","/loan/payment",2);
				exit();
			}
			
			$sale_id = empty($payment_il['loan_id'])?0:$payment_il['loan_id'];
  			$lastPaymentRecord = $db->getLastPaymentRecord($sale_id);
  			$lastPayId = empty($lastPaymentRecord['id'])?0:$lastPaymentRecord['id'];
  			if ($lastPayId!=$id){
  				Application_Form_FrmMessage::Sucessfull("Only Last Payment Receipt Can Delete","/loan/payment",2);
				exit();
  			}
		}
		$delete_sms=$tr->translate('CONFIRM_DELETE');
		echo "<script language='javascript'>
		var txt;
		var r = confirm('$delete_sms');
		if (r == true) {";
			echo "window.location ='".Zend_Controller_Front::getInstance()->getBaseUrl()."/loan/payment/deletereceipt/id/".$id."'";
		echo"}";
		echo"else {";
			echo "window.location ='".Zend_Controller_Front::getInstance()->getBaseUrl()."/loan/payment'";
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
		$db = new Loan_Model_DbTable_DbLoanILPayment();
		try {
			$dbacc = new Application_Model_DbTable_DbUsers();
			$rs = $dbacc->getAccessUrl($module,$controller,"delete");
			if(!empty($rs)){
				$db->deleteRecord($id);
				Application_Form_FrmMessage::Sucessfull("DELETE_SUCCESS","/loan/payment/");exit();
			}
			Application_Form_FrmMessage::Sucessfull("You don't have permission to delete this record","/loan/payment/",2);exit();
			
		}catch (Exception $e) {
			Application_Form_FrmMessage::message("INSERT_FAIL");
		}
	}
	
	
	function cancelIlPayment(){
// 		$db = new Loan_Model_DbTable_DbLoanILPayment();
		$db = new Loan_Model_DbTable_DbGroupPayment();
		if($this->getRequest()->isPost()){
			$_data = $this->getRequest()->getPost();
			$identity = $_data["identity"];
			try {
				$row = $db->cancelIlPayment($_data);
				print_r(Zend_Json::encode($row));
				exit();
			}catch (Exception $e) {
				$err =$e->getMessage();
				Application_Model_DbTable_DbUserLog::writeMessageError($err);
			}
		}
	}
	function ilQuickPaymentAction(){
		$db = new Loan_Model_DbTable_DbLoanILPayment();
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			//print_r($data);
			$db->quickPayment($data);
			
		}
		$frm = new Loan_Form_FrmIlPayment();
		$frm_loan=$frm->quickPayment();
		Application_Model_Decorator::removeAllDecorator($frm_loan);
		$db_keycode = new Application_Model_DbTable_DbKeycode();
		$this->view->keycode = $db_keycode->getKeyCodeMiniInv();
		
		$this->view->graiceperiod = $db_keycode->getSystemSetting(9);
		$this->view->frm_ilpayment = $frm_loan;
		
		$this->view->co = $db->getAllCo();
	}
	function getAllLoanByCoIdAction(){
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			
			$db = new Loan_Model_DbTable_DbLoanILPayment();
			$row = $db->getAllLoanByCoId($data);
			print_r(Zend_Json::encode($row));
			exit();
		}
	}
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
			$db = new Loan_Model_DbTable_DbLoanILPayment();
			$row = $db->getLastPayDate($data);
			print_r(Zend_Json::encode($row));
			exit();
		}
	
	}
	function getLastPaymentDateByLoanAction(){// get last payment in client reciept money for caculate penalize for client 
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db = new Loan_Model_DbTable_DbLoanILPayment();
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
			//$this->_redirect("/product/index/index");
		}
	
	}
	public function generateBarcodeAction(){
			$loan_code = $this->getRequest()->getParam('loan_code');
	// 		if(!empty($id)){
	// 			$db = new Application_Model_DbTable_DbGlobal();
	// 			$sql=" SELECT p_code FROM tb_product WHERE pro_id = ".$id." LIMIT 1 ";
	// 			$row=$db->getGlobalDbRow($sql);
	// 			$_itemcode=$row["p_code"];
				header('Content-type: image/png');
				
				$this->_helper->layout()->disableLayout();
				//$barcodeOptions = array('text' => "$_itemcode",'barHeight' => 30);
				$barcodeOptions = array('text' => "$loan_code",'barHeight' => 40);
				//'font' => 4(set size of label),//'barHeight' => 40//set height of img barcode
				$rendererOptions = array();
				$renderer = Zend_Barcode::factory(
						'code128', 'image', $barcodeOptions, $rendererOptions
				)->render();
	// 		}
		
		}
	
	function getIlLoanDetailAction(){//តារាងត្រូវបង់អីឡូវ
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db = new Loan_Model_DbTable_DbLoanILPayment();
			$row = $db->getLoanPaymentByLoanNumber($data);
			print_r(Zend_Json::encode($row));
			exit();
		}
	}
	function getAllIlLoanDetailAction(){
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db = new Loan_Model_DbTable_DbLoanILPayment();
			$row = $db->getAllLoanPaymentByLoanNumber($data);
			print_r(Zend_Json::encode($row));
			exit();
		}
	}
	
	function getLoanHasPayByLoanNumberAction(){
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db = new Loan_Model_DbTable_DbLoanILPayment();
			$loan_number = $data["loan_number"];
			$row = $db->getLaonHasPayByLoanNumber($loan_number);
			print_r(Zend_Json::encode($row));
			exit();
		}
	}
	function getrpnumberAction(){//get receipt by co
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db = new Loan_Model_DbTable_DbLoanILPayment();
			$co_id = $data["co_id"];
			$row = $db->getIlPaymentNumber();//getIlPaymentRPNumber($co_id);
			print_r(Zend_Json::encode($row));
			exit();
		}
	}
	
	function addpaymentajaxAction(){
		if($this->getRequest()->isPost()){
			$_data = $this->getRequest()->getPost();
			$db = new Loan_Model_DbTable_DbLoanILPayment();
			$receipt_id = $db->addILPayment($_data);
			$row = $db->getLoanPaymentById($receipt_id);
			print_r(Zend_Json::encode($row));
			exit();
		}
	}
	function updaterecieptpaymentAction(){
		$db  = new Report_Model_DbTable_DbLoan();
		$key = new Application_Model_DbTable_DbKeycode();
		$this->view->data=$key->getKeyCodeMiniInv(TRUE);
		$id =$this->getRequest()->getParam('id');
		$id = empty($id)?0:$id;
		$row = $db->getLoanPaymentById($id);
		
		if(empty($row)){
			$inFrame = $this->getRequest()->getParam('inFrame');
			$inFrame = empty($inFrame)?"":$inFrame;
			Application_Form_FrmMessage::Sucessfull("NO_RECORD",'/loan/payment?inFrame='.$inFrame,2);
			exit();
		}
	
		$this->view->loanPayment = $row ;
		if($this->getRequest()->isPost()){
			$_data = $this->getRequest()->getPost();
			$db = new Loan_Model_DbTable_DbLoanILPayment();
			$receipt_id = $db->updateReceiptLoan($_data);
			Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS",'/loan/payment/updaterecieptpayment/id/'.$receipt_id );
			exit();
		}
		
	 }
	 
	function getpenaltyandfeeAction(){
		if($this->getRequest()->isPost()){
			$_data = $this->getRequest()->getPost();
			$db = new Report_Model_DbTable_DbLoan();
			$row = $db->checkLaonPenalty($_data);
			print_r(Zend_Json::encode($row));
			exit();
		}
	}
}

