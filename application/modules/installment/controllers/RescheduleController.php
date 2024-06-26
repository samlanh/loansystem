<?php
class Installment_RescheduleController extends Zend_Controller_Action {
    public function init()
    {    	
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	private $sex=array(1=>'M',2=>'F');
	public function indexAction(){
		try{
		    if($this->getRequest()->isPost()){
 				$search = $this->getRequest()->getPost();
 			}
			else{
				$search = array(
					'txt_search'=>'',
					'member'=> -1,
					'repayment_method' => -1,
					'branch_id' => -1,
					'category_id' => -1,
					'status' => -1,
					'selling_type'=>-1,
					'completed_status'=>-1,
					'start_date'=> date('Y-m-d'),
					'end_date'=>date('Y-m-d'),
				);
			}
			$db = new Installment_Model_DbTable_DbInstallment();
			$rs_rows= $db->getAllSale($search);
			$list = new Application_Form_Frmtable();
			$collumns = array("BRANCH_NAME","SHOP_NAME","INSTALLMENT_NO","CUSTOMER_NAME","PRODUCT_CATEGORY","ITEM_NAME","SELLING_PRICE",
					"SOLD_DATE","INVOICE_NO","SALE_TYPE","REPAYMENT_TYPE","INSTALLMENT_DURATION","COMPLETED","BY","STATUS");
		
			$link=array(
					'module'=>'installment','controller'=>'index','action'=>'view',
			);
			$link_info=array('module'=>'installment','controller'=>'index','action'=>'edit',);
			$link_schedule=array('module'=>'report','controller'=>'loan','action'=>'rpt-paymentschedules',);
				
			$link_payment=array('module'=>'installment','controller'=>'payment','action'=>'add',);
			//array('បោះពុម្ភ'=>$link_schedule,'Click Here'=>$link_payment,'payment_method'=>$link_info,'client_name_kh'=>$link_info,'client_name_en'=>$link_info,'total_capital'=>$link_info)
			$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array(),0);
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}	
		$frm = new Installment_Form_FrmSearchInstallment();
		$frm = $frm->AdvanceSearch();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_search = $frm;
		$db = new Application_Model_DbTable_DbGlobal();
		$this->view->rs = $search;
		
		$db = new Installment_Model_DbTable_DbProduct();
		$row_cat = $db->getCategory();
		array_unshift($row_cat,array(
				'id' => -1,
				'name' => 'ជ្រើសរើសប្រភេទ',
		) );
		$this->view->rs_cate=$row_cat;
  }
  function addAction()
  {
  	if($this->getRequest()->isPost()){
  		$_data = $this->getRequest()->getPost();
  		try{
  			$_dbmodel = new Installment_Model_DbTable_DbReschedule();
  			$_dbmodel->addRescheduleInstallment($_data);
  			Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/installment/index");
  		}catch (Exception $e) {
  			Application_Form_FrmMessage::message("INSERT_FAIL");
  			Application_Model_DbTable_DbUserLog::writeMessageError($err =$e->getMessage());
  		}
  	}
  	$id = $this->getRequest()->getParam('id');
  	$id = empty($id)?0:$id;
  	$db_g = new Application_Model_DbTable_DbGlobal();
  	
  	$db = new Installment_Model_DbTable_DbInstallmentPayment();
  	$row = $db->getSaleinstallbyid($id);
  	
  	if (empty($row)){
  		Application_Form_FrmMessage::Sucessfull("NO_RECORD","/installment",2);exit();
  	}
  	
  	$frm = new Installment_Form_FrmLoan();
  	$frm_loan=$frm->FrmAddLoan();
  	Application_Model_Decorator::removeAllDecorator($frm_loan);
  	$this->view->frm_loan = $frm_loan;
  	$this->view->datarow = $row;
  	
  	$db = new Installment_Model_DbTable_DbProduct();
  	$row_cat = $db->getCategory();
  		
  	array_unshift($row_cat,array(
  			'id' => -1,
  			'name' => 'ជ្រើសរើសប្រភេទផលិតផល',
  	) );
  	$this->view->rs_cate=$row_cat;
  	
  	$db_global = new Application_Model_DbTable_DbGlobal();
  	$this->view->loan_number = $db_global->getSaleinstallmentByBranch(1);
}
	public function editAction(){
		if($this->getRequest()->isPost()){
			$_data = $this->getRequest()->getPost();
			try{
				$_dbmodel = new Installment_Model_DbTable_DbInstallment();
				$_dbmodel->updateInstallmentById($_data);
				Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/installment/index");
			}catch (Exception $e) {
				Application_Form_FrmMessage::message("INSERT_FAIL");
				Application_Model_DbTable_DbUserLog::writeMessageError($err =$e->getMessage());
			}
		}
		$id = $this->getRequest()->getParam('id');
		$db_g = new Application_Model_DbTable_DbGlobal();
	
		$db = new Installment_Model_DbTable_DbInstallmentPayment();
		$row = $db->getSaleinstallbyid($id);
		
		$frm = new Installment_Form_FrmLoan();
		$frm_loan=$frm->FrmAddLoan();
		Application_Model_Decorator::removeAllDecorator($frm_loan);
		$this->view->frm_loan = $frm_loan;
		$this->view->datarow = $row;
	
		$db = new Installment_Model_DbTable_DbProduct();
		$row_cat = $db->getCategory();
			
		array_unshift($row_cat,array(
				'id' => -1,
				'name' => 'ជ្រើសរើសប្រភេទផលិតផល',
		) );
		$this->view->rs_cate=$row_cat;
	}
	function getreceiptnumberAction(){
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db = new Application_Model_DbTable_DbGlobal();
			$loan_number = $db->getInvoiceReceipt($data['branch_id']);
			print_r(Zend_Json::encode($loan_number));
			exit();
		}
	}
	public function submitloanAction(){//ajax submit installment
		if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();
			$_dbmodel = new Installment_Model_DbTable_DbInstallment();//new
			$loan_id = $_dbmodel->addSaleInstallment($data);
			$suc = array('sms'=>'ប្រាក់ឥណទានត្រូវបានបញ្ចូលដោយជោគជ័យ !');
			print_r(Zend_Json::encode($loan_id));
			exit();
		}
	}	
	
// 	public function addloanAction(){
// 		if($this->getRequest()->isPost()){
// 			$data=$this->getRequest()->getPost();
// 			$db = new Loan_Model_DbTable_DbLoan();
// 			$id = $db->addNewLoanGroup($data);
// 			$suc = array('sms'=>'ប្រាក់ឥណទានត្រូវបានបញ្ចូលដោយជោគជ័យ !');
// 			print_r(Zend_Json::encode($suc));
// 			exit();
// 		}
// 	}
	public function viewAction(){
		$id = $this->getRequest()->getParam('id');
		$id = empty($id) ? 0 : $id;
		$db_g = new Application_Model_DbTable_DbGlobal();
		if(empty($id)){
			Application_Form_FrmMessage::Sucessfull("NO_RECORD","/installment/index/index",2);
			exit();
		}
		$db = new Loan_Model_DbTable_DbLoandisburse();
		$row = $db->getLoanviewById($id);
		$this->view->tran_rs = $row;
	}
	function getLoanlevelAction(){
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db = new Installment_Model_DbTable_DbInstallment();
			$row = $db->getLoanLevelByClient($data['client_id']);
			print_r(Zend_Json::encode($row));
		    exit();
		}
	}
// 	public function getLoaninfoAction(){//from repayment schedule
// 		if($this->getRequest()->isPost()){
// 			$data=$this->getRequest()->getPost();
// 			$db=new Loan_Model_DbTable_DbRepaymentSchedule();
// 			$row=$db->getLoanInfo($data['loan_id']);
// 			print_r(Zend_Json::encode($row));
// 			exit();
// 		}
// 	}
// 	function getloanBymemberidAction(){
// 		if($this->getRequest()->isPost()){
// 			$data=$this->getRequest()->getPost();
// 			$db=new Loan_Model_DbTable_DbRepaymentSchedule();
// 			$row=$db->getLoanInfoBymemberId($data['loan_id']);
// 			print_r(Zend_Json::encode($row));
// 			exit();
// 		}
// 	}
//     function getloannumberAction(){
//     			if($this->getRequest()->isPost()){
//     				$data = $this->getRequest()->getPost();
//     				$db = new Application_Model_DbTable_DbGlobal();
// 		            $loan_number = $db->getLoanNumber($data);
//     				print_r(Zend_Json::encode($loan_number));
//     				exit();
//     			}
//     }
	public function testAction($result=null,$table='ln_branch'){
	}
	function addloantestAction(){
		if($this->getRequest()->isPost()){
			$_data = $this->getRequest()->getPost();
				$_dbmodel = new Installment_Model_DbTable_DbInstallment();
				$rows_return=$_dbmodel->previewschedule($_data);
				print_r(Zend_Json::encode($rows_return));
				exit();
		}
	}
// 	function addNewloantypeAction(){
// 	if($this->getRequest()->isPost()){
// 			$data = $this->getRequest()->getPost();
// 			$data['status']=1;
// 			$data['display_by']=1;
// 			$db = new Other_Model_DbTable_DbLoanType();
// 			$id = $db->addViewType($data);
// 			print_r(Zend_Json::encode($id));
// 			exit();
// 		}
// 	}
	/* vandy get Client Installment Information for show on Invoice  */
	function getclientinsinfoAction(){
		if($this->getRequest()->isPost()){
			$_data = $this->getRequest()->getPost();
			$db = new Loan_Model_DbTable_DbLoanIL();
			$Client = $db->getInstallmentClientInfo($_data['clientID']);
			print_r(Zend_Json::encode($Client));
			exit();
		}
	}
}