<?php
class Report_InstallmentsController extends Zend_Controller_Action {
    public function init()
    {    	
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	function indexAction(){
		
	}
	function saleAction(){
		$db  = new Report_Model_DbTable_DbInventory();
		if($this->getRequest()->isPost()){
    			$search = $this->getRequest()->getPost();
    		}
    		else{
    			$search=array(
    				'adv_search' => '',
    				'supllier'=>'',
    				'branch_id'=>'',
    				'shop_id'=>-1,
    				'start_date'=> date('Y-m-d'),
    				'end_date'=>date('Y-m-d'),
    				'status'=>-1,
    				'category'=>'',
    				'selling_type'=>-1,
    				'product_type'=>-1,
					'completed_status'=>-1,
    					
    			);
    		}
		$row = $db->getSaleInventory($search);
		$this->view->sale = $row;
		$this->view->search = $search;
		$form=new Installment_Form_FrmSale();
		$form=$form->searchSale();
		Application_Model_Decorator::removeAllDecorator($form);
		$this->view->form_search=$form;
		
		$dbpro = new Installment_Model_DbTable_DbProduct();
		$this->view->producttype = $dbpro->getProducttype();
		
		$key = new Application_Model_DbTable_DbKeycode();
		$this->view->data=$key->getKeyCodeMiniInv(TRUE);
		
		$frmpopup = new Application_Form_FrmPopupGlobal();
		$this->view->footerReport = $frmpopup->getFooterReport();
	}
	function saleinvoiceAction(){
		$id=$this->getRequest()->getParam('id');
		$id = empty($id)?0:$id;
		$db  = new Report_Model_DbTable_DbInventory();
		$row = $db->getSaleInventoryById($id);
		if (empty($row)){
			Application_Form_FrmMessage::Sucessfull("NO_RECORD",'/report/installments/sale',2);
			exit();
		}
		$this->view->sale = $row;
		
	}
	function agreementAction(){
		$id=$this->getRequest()->getParam('id');
		$id = empty($id)?0:$id;
		$db  = new Report_Model_DbTable_DbInventory();
		$row = $db->getSaleInventoryById($id);
		if (empty($row)){
			Application_Form_FrmMessage::Sucessfull("NO_RECORD",'/report/installments/sale',2);
			exit();
		}
		$this->view->sale = $row;
		
		$key = new Application_Model_DbTable_DbKeycode();
		$this->view->data=$key->getKeyCodeMiniInv(TRUE);
	}
	function salescheduleAction(){
		$id=$this->getRequest()->getParam('id');
		$id = empty($id)?0:$id;
		$db  = new Report_Model_DbTable_DbInventory();
		$row = $db->getSaleInventoryById($id);
		if (empty($row)){
			Application_Form_FrmMessage::Sucessfull("NO_RECORD",'/report/installments/sale',2);
			exit();
		}
		$this->view->sale = $row;
		$this->view->schedule = $db->getSaleInventorySchedule($id);
		
		$key = new Application_Model_DbTable_DbKeycode();
		$this->view->data=$key->getKeyCodeMiniInv(TRUE);
	}
	function inventoryAction(){
		$db  = new Report_Model_DbTable_DbInventory();
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
		}else{
			$data = array(
					'adv_search'=>	'',
					'branch_id'	=>	-1,
					'category'	=>	-1,
					'status'	=>  -1,
					'product_type'=>-1
			);
		}
		$row = $db->getInventory($data);
		$this->view->inventory = $row;
		$formFilter = new Installment_Form_FrmProduct();
		$this->view->formFilter = $formFilter->add();
		Application_Model_Decorator::removeAllDecorator($formFilter);
		
		$key = new Application_Model_DbTable_DbKeycode();
		$this->view->data=$key->getKeyCodeMiniInv(TRUE);
		
		$frmpopup = new Application_Form_FrmPopupGlobal();
		$this->view->footerReport = $frmpopup->getFooterReport();
	}
	function nearlyourstockAction(){
		$db  = new Report_Model_DbTable_DbInventory();
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
		}else{
			$data = array(
					'adv_search'=>	'',
					'branch_id'	=>	-1,
					'category'	=>	-1,
					'status'	=>  -1
			);
		}
		$row = $db->productNearlyOutStock($data);
		$this->view->inventory = $row;
		$formFilter = new Installment_Form_FrmProduct();
		$this->view->formFilter = $formFilter->add();
		Application_Model_Decorator::removeAllDecorator($formFilter);
		
		$key = new Application_Model_DbTable_DbKeycode();
		$this->view->data=$key->getKeyCodeMiniInv(TRUE);
		
		$frmpopup = new Application_Form_FrmPopupGlobal();
		$this->view->footerReport = $frmpopup->getFooterReport();
	}
	function purchaseAction(){
		$db  = new Report_Model_DbTable_DbInventory();
		if($this->getRequest()->isPost()){
    			$search = $this->getRequest()->getPost();
    		}
    		else{
    			$search=array(
    				'adv_search' => '',
    				'supllier'=>'',
    				'branch_id'=>'',
    				'start_date'=> "",
    				'end_date'=>date('Y-m-d'),
    				'status'=>-1,
    			);
    		}
		$row = $db->getAllInventoryPurchase($search);
		$this->view->purchse = $row;
		$this->view->search = $search;
		$form=new Installment_Form_FrmPurchase();
		$form=$form->searchPurchase();
		Application_Model_Decorator::removeAllDecorator($form);
		$this->view->form_search=$form;
	
		$key = new Application_Model_DbTable_DbKeycode();
		$this->view->data=$key->getKeyCodeMiniInv(TRUE);
		
		$frmpopup = new Application_Form_FrmPopupGlobal();
		$this->view->footerReport = $frmpopup->getFooterReport();
	}
	function purchasedetailAction(){
		$id=$this->getRequest()->getParam('id');
		$id = empty($id)?0:$id;
		$db  = new Report_Model_DbTable_DbInventory();
		$row = $db->getPurchaseById($id);
		if (empty($row)){
			Application_Form_FrmMessage::Sucessfull("Don't have record","/report/installments");
		}
		$this->view->purchase = $row;
		$this->view->purchaseDetail = $db->getPurchseDetail($id);
	}
	function sumarystockAction(){
		$db  = new Report_Model_DbTable_DbInventory();
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
		}else{
			$data = array(
					'adv_search'=>	'',
					'branch_id'	=>	-1,
					'category'	=>	-1,
					'product_type'	=>	-1,
					'status'	=>  -1,
					'start_date'=> date('Y-m-d'),
					'end_date'=>date('Y-m-d'),
			);
		}
		$this->view->search = $data;
		$summaryStock= $db->getSumaryStock($data);
		$this->view->sumaryStok = $summaryStock;
		$formFilter = new Installment_Form_FrmProduct();
		$this->view->formFilter = $formFilter->add();
		Application_Model_Decorator::removeAllDecorator($formFilter);
		
		$key = new Application_Model_DbTable_DbKeycode();
		$this->view->data=$key->getKeyCodeMiniInv(TRUE);
		$frmpopup = new Application_Form_FrmPopupGlobal();
		$this->view->footerReport = $frmpopup->getFooterReport();
	}
	function rptPaymentAction(){
		$db  = new Report_Model_DbTable_DbInventory();
		$key = new Application_Model_DbTable_DbKeycode();
		$this->view->data=$key->getKeyCodeMiniInv(TRUE);
		if($this->getRequest()->isPost()){
			$search = $this->getRequest()->getPost();
		}else {
			$search = array(
					'adv_search' => '',
					'status_search' => -1,
					'status' => -1,
					'branch_id' => -1,
					'members' => -1,
					'start_date'=> date('Y-m-d'),
					'end_date'=>date('Y-m-d'));
		}
		$this->view->loantotalcollect_list =$rs=$db->getALLInstallmentPayment($search);
		
		$db  = new Report_Model_DbTable_DbInventory();
		$row = $db->getGeneralSaleInventory($search);
		$this->view->sale = $row;
		
		$this->view->list_end_date = $search;
	
		$key = new Application_Model_DbTable_DbKeycode();
		$this->view->data=$key->getKeyCodeMiniInv(TRUE);
		
		$frm = new Installment_Form_FrmInstallment();
		$frm = $frm->FrmAddLoan();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_search = $frm;
		
		$formFilter = new Installment_Form_FrmProduct();
		$this->view->formFilter = $formFilter->add();
		Application_Model_Decorator::removeAllDecorator($formFilter);
		
		$frmpopup = new Application_Form_FrmPopupGlobal();
		$this->view->footerReport = $frmpopup->getFooterReport();
	}
	function rptPaymentHistoryAction(){
		$db  = new Report_Model_DbTable_DbInventory();
		if($this->getRequest()->isPost()){
			$search = $this->getRequest()->getPost();
		}else {
			$search = array(
					'adv_search' => "",
					'branch_id'	  => -1,
					'members'=>-1,
		 			'currency_type'=>-1,
			 		'start_date'  => date('Y-m-d'),
			 		'end_date'    => date('Y-m-d'),
					'paymnet_type'=> -1,);
		}
		$search['orderBy']="1";
		$this->view->loantotalcollect_list =$db->getALLInstallmentPayment($search);
		$this->view->list_end_date=$search;
	
		$key = new Application_Model_DbTable_DbKeycode();
		$this->view->data=$key->getKeyCodeMiniInv(TRUE);
		
		$frm = new Installment_Form_FrmInstallment();
		$frm = $frm->FrmAddLoan();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_search = $frm;
		
		$formFilter = new Installment_Form_FrmProduct();
		$this->view->formFilter = $formFilter->add();
		Application_Model_Decorator::removeAllDecorator($formFilter);
		
		$frmpopup = new Application_Form_FrmPopupGlobal();
		$this->view->footerReport = $frmpopup->getFooterReport();
	}
	function rptLoanOutstandingAction(){//loand out standing with /collection
		$db  = new Report_Model_DbTable_DbInventory();
		if($this->getRequest()->isPost()){
			$search = $this->getRequest()->getPost();
		}else {
			$search = array(
				'adv_search'=>'',
				'branch_id'=>'',
				'members'=>'',
				'category'=>-1,
				'shop_id'=>-1,
				'currency_type'=>-1,
				'status_use'=>-1,
				'product_type'=>-1,
				'end_date'=>date('Y-m-d')
			);
		}
		$this->view->fordate = $search['end_date'];
		$this->view->search = $search;
		$this->view->outstandloan= $db->getAllOutstadingLoan($search);
		$frm = new Loan_Form_FrmSearchLoan();
	
		$dbpro = new Installment_Model_DbTable_DbProduct();
		$this->view->producttype = $dbpro->getProducttype();
		
		$key = new Application_Model_DbTable_DbKeycode();
		$this->view->data=$key->getKeyCodeMiniInv(TRUE);
	
		$frm = new Installment_Form_FrmInstallment();
		$frm = $frm->FrmAddLoan();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_search = $frm;
		
		$formFilter = new Installment_Form_FrmProduct();
		$this->view->formFilter = $formFilter->add();
		Application_Model_Decorator::removeAllDecorator($formFilter);
		
		$frmpopup = new Application_Form_FrmPopupGlobal();
		$this->view->footerReport = $frmpopup->getFooterReport();
	}
	function rptLoancollectAction(){//list payment that collect from client
		$dbs = new Report_Model_DbTable_DbInventory();
		$frm = new Application_Form_FrmSearchGlobal();
		if($this->getRequest()->isPost()){
			$search = $this->getRequest()->getPost();
		}
		else{
			$search = array(
					'branch_id'=>0,
					'members'=>-1,
					'province'=>0,
					'district_id'=>'',
					'comm_id'=>'',
					'village'=>'',
					'shop_id'=>'-1',
					'start_date'=> date('Y-m-d'),
					'end_date'=>date('Y-m-d'),
					'status' => -1,);
		}
		$this->view->date_show=$search['end_date'];
		$this->view->rsearch=$search;
		
		$row = $dbs->getAllLnClient($search);
		$this->view->tran_schedule=$row;
	
		$this->view->list_end_dates = $search["end_date"];
		
		$frm = new Installment_Form_FrmInstallment();
		$frm = $frm->FrmAddLoan();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_search = $frm;
		
		$formFilter = new Installment_Form_FrmProduct();
		$this->view->formFilter = $formFilter->add();
		Application_Model_Decorator::removeAllDecorator($formFilter);
		 
		$key = new Application_Model_DbTable_DbKeycode();
		$this->view->data=$key->getKeyCodeMiniInv(TRUE);
		
		$frmpopup = new Application_Form_FrmPopupGlobal();
		$this->view->footerReport = $frmpopup->getFooterReport();
	}
	function rptIncomestatementAction(){
		$key = new Application_Model_DbTable_DbKeycode();
		$this->view->data=$key->getKeyCodeMiniInv(TRUE);
		if($this->getRequest()->isPost()){
			$search = $this->getRequest()->getPost();
			$search['status']=-1;
		}else{
			$search = array(
						
					'start_date'=> date('Y-m-d'),
					'end_date'=>date('Y-m-d'),
					'branch_id'		=>	-1,
					"currency_type"=>0,);
		}
	
		$income = array(
			'principal_paid'=>0,
 			'interest_paid'=>0,
 			'penalize_paid'=>0,
 			'service_paid'=>0,
 			'adminfee'=>0,
 			'other_fee'=>0,
 			'other_income'=>0,
 			'expense'=>0,
 			'badloan'=>0,
 			
 		);
		$dbInsta  = new Report_Model_DbTable_DbInventory();
		$InstallmentCollect = $dbInsta->getInstallmentCollectIcome($search);
		if (!empty($InstallmentCollect)) foreach ($InstallmentCollect as $rs){
			$income['interest_paid']=$rs['interest_paid'];
			$income['penalize_paid']=$rs["penalize_paid"];
			$income['principal_paid']=$rs["principal_paid"];
		}
		
	
		$this->view->rsincome=$income;
		$this->view->list_end_date=$search;
	
		$frm = new Loan_Form_FrmSearchGroupPayment();
		$fm = $frm->AdvanceSearch();
		Application_Model_Decorator::removeAllDecorator($fm);
		$this->view->frm_search = $fm;
	}
	function retailreceiptAction(){
		$id=$this->getRequest()->getParam('id');
		$id = empty($id)?0:$id;
		$db  = new Report_Model_DbTable_DbInventory();
		
		$row = $db->getRetailPurchaseByID($id);
		if (empty($row)){
			Application_Form_FrmMessage::Sucessfull("NO_RECORD","/report/installments",2);
			exit();
		}
		$this->view->purchase = $row;
		$this->view->purchaseDetail = $db->getPurchaseDetailByID($id);
	}
	
	function recieptpaymentAction(){
		$db  = new Report_Model_DbTable_DbInventory();
		$key = new Application_Model_DbTable_DbKeycode();
		$this->view->data=$key->getKeyCodeMiniInv(TRUE);
		$id =$this->getRequest()->getParam('id');
		$id = empty($id)?0:$id;
		$row = $db->getInstallPaymentBYId($id);
		if (empty($row)){
			Application_Form_FrmMessage::Sucessfull("NO_RECORD",'/installment/payment',2);
			exit();
		}
		$this->view->loanPayment = $row;
		$frmpopup = new Application_Form_FrmPopupGlobal();
		$this->view->saleInstallReceipt = $frmpopup->getOfficailReceiptSaleInstall();
	}
	
	function rptgeneralsaleAction(){
		$db  = new Report_Model_DbTable_DbInventory();
		if($this->getRequest()->isPost()){
			$search = $this->getRequest()->getPost();
		}
		else{
			$search=array(
					'adv_search' => '',
					'branch_id'=>'',
					'start_date'=> "",
					'end_date'=>date('Y-m-d'),
					'status'=>-1,
			);
		}
		$row = $db->getGeneralSaleInventory($search);
		$this->view->sale = $row;
		$this->view->search = $search;
		$form=new Installment_Form_FrmSale();
		$form=$form->searchSale();
		Application_Model_Decorator::removeAllDecorator($form);
		$this->view->form_search=$form;
		
		$key = new Application_Model_DbTable_DbKeycode();
		$this->view->data=$key->getKeyCodeMiniInv(TRUE);
	}
	function generalsaleinvoiceAction(){
		$id=$this->getRequest()->getParam('id');
		$id = empty($id)?0:$id;
		$db  = new Report_Model_DbTable_DbInventory();
		$row = $db->getGeneralsaleById($id);
		if (empty($row)){
			Application_Form_FrmMessage::Sucessfull("Don't have record","/report/installments/rptgeneralsale");
		}
		$this->view->sale = $row;
		$this->view->saleDetail = $db->getGeneraldetailSaleById($id);
	}
	
	function rptClosingentryAction(){
		$db  = new Report_Model_DbTable_DbInventory();
		$key = new Application_Model_DbTable_DbKeycode();
		$this->view->data=$key->getKeyCodeMiniInv(TRUE);
		if($this->getRequest()->isPost()){
			$search = $this->getRequest()->getPost();
		}else {
			$search = array(
					'adv_search' => '',
					'status_search' => -1,
					'status' => -1,
					'branch_id' => -1,
					'members' => -1,
					'start_date'=> date('Y-m-d'),
					'end_date'=>date('Y-m-d'));
		}
		$this->view->loantotalcollect_list =$rs=$db->getALLInstallmentPayment($search);
	
// 		$db  = new Report_Model_DbTable_DbInventory();
// 		$row = $db->getGeneralSaleInventory($search);
// 		$this->view->sale = $row;
	
		$this->view->list_end_date = $search;
	
		$key = new Application_Model_DbTable_DbKeycode();
		$this->view->data=$key->getKeyCodeMiniInv(TRUE);
	
		$frm = new Installment_Form_FrmInstallment();
		$frm = $frm->FrmAddLoan();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_search = $frm;
	
		$frmpopup = new Application_Form_FrmPopupGlobal();
		$this->view->footerReport = $frmpopup->getFooterReport();
	}
	
	function submitentryAction(){
		$db  = new Report_Model_DbTable_DbInventory();
		$key = new Application_Model_DbTable_DbKeycode();
		$this->view->data=$key->getKeyCodeMiniInv(TRUE);
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db->submitClosingEngry($data);
			Application_Form_FrmMessage::Sucessfull("Closing Entry Success", "/report/installments/rpt-closingentry");
		}
	}
	
 function paymenthistoryAction(){

		$db  = new Report_Model_DbTable_DbInventory();
		$key = new Application_Model_DbTable_DbKeycode();
		$this->view->data=$key->getKeyCodeMiniInv(TRUE);
		$id =$this->getRequest()->getParam('id');
		$id = empty($id)?0:$id;
	
		$row =$rs=$db->getPaymentListInstallmentById($id);
		if (empty($row)){
			Application_Form_FrmMessage::Sucessfull("NO_RECORD","/installment/index",2);exit();
		}
		$this->view->loantotalcollect_list = $row;
		
		$frmpopup = new Application_Form_FrmPopupGlobal();
		$this->view->footerReport = $frmpopup->getFooterReport();
	}
}

