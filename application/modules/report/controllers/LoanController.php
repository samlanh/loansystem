<?php
class Report_LoanController extends Zend_Controller_Action {
    public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
  function indexAction(){
  	
  }
function rptLoanDisburseAction(){//release all loan
  	$db  = new Report_Model_DbTable_DbLoan();
  	if($this->getRequest()->isPost()){
  		$search = $this->getRequest()->getPost();
  	}
  	else{
  		$search = array(
  				'branch_id'=>'',
  				'client_name'=>'',
  				'pay_every'=>-1,
  				'co_id'=>'',
  				'zone'=>-1,
  				'start_date'=> date('Y-m-d'),
  				'end_date'=>date('Y-m-d'));
  	}
  	$this->view->loanrelease_list=$db->getAllLoan($search);
  	$this->view->list_end_date=$search;
  	
  	$frm = new Loan_Form_FrmSearchLoan();
  	$frm = $frm->AdvanceSearch();
  	Application_Model_Decorator::removeAllDecorator($frm);
  	$this->view->frm_search = $frm;
  	
  	$key = new Application_Model_DbTable_DbKeycode();
  	$this->view->data=$key->getKeyCodeMiniInv(TRUE);
  	
  	$frmpopup = new Application_Form_FrmPopupGlobal();
  	$this->view->footerReport = $frmpopup->getFooterReport();
  }
  function rptDailyloanAction(){//release all loan
  	$db  = new Report_Model_DbTable_DbLoan();
  	if($this->getRequest()->isPost()){
  		$search = $this->getRequest()->getPost();
  	}
  	else{
  		$search = array(
  				'branch_id'=>'',
  				'client_name'=>'',
  				'pay_every'=>-1,
  				'co_id'=>'',
  				'start_date'=> '',
  				'end_date'=>date('Y-m-d'));
  	}
  	$this->view->loanrelease_list=$db->getAllDailyLoan($search);
  	$this->view->list_end_date=$search;
  	 
  	$frm = new Loan_Form_FrmSearchLoan();
  	$frm = $frm->AdvanceSearch();
  	Application_Model_Decorator::removeAllDecorator($frm);
  	$this->view->frm_search = $frm;
  	 
  	$key = new Application_Model_DbTable_DbKeycode();
  	$this->view->data=$key->getKeyCodeMiniInv(TRUE);
  	
  	$db = new Application_Model_DbTable_DbGlobal();
  	$day_inkhmer = $db->getDayInkhmerBystr(null);
  	$this->view->day_inkhmer = $day_inkhmer;
  	
  	$frmpopup = new Application_Form_FrmPopupGlobal();
  	$this->view->footerReport = $frmpopup->getFooterReport();
  }
  
  function rptLoanDisburseCoAction(){//realease by co
	  $db  = new Report_Model_DbTable_DbLoan();
	  if($this->getRequest()->isPost()){
	  		$search = $this->getRequest()->getPost();
	  	}
	  	else{
	  		$search = array(
	  				'branch_id'=>-1,
	  				'pay_every'=>'',
	  			  	'member'=>-1,
	  				'co_id'=>-1,
	  				'status'=>-1,
	  				'start_date'=> date('Y-m-d'),
	  				'end_date'=>date('Y-m-d'));
	  			
	  	}
  	$this->view->list_end_date=$search;
  	$this->view->statusopt=$search['status'];
  	
  	$this->view->loanrelease_list=$db->getAllLoanCo($search);
  	  	 
  	$frm = new Loan_Form_FrmSearchLoan();
  	$frm = $frm->AdvanceSearch();
  	Application_Model_Decorator::removeAllDecorator($frm);
  	$this->view->frm_search = $frm;
  	
  	$key = new Application_Model_DbTable_DbKeycode();
  	$this->view->data=$key->getKeyCodeMiniInv(TRUE);
  	
  	$frmpopup = new Application_Form_FrmPopupGlobal();
  	$this->view->footerReport = $frmpopup->getFooterReport();
  }
  
  function rptLoancollectAction(){//list payment that collect from client
  	$dbs = new Report_Model_DbTable_DbloanCollect();
  	$frm = new Application_Form_FrmSearchGlobal();
  	if($this->getRequest()->isPost()){
  		$search = $this->getRequest()->getPost();
  	}
  	else{
  		$search = array(
  				'branch_id'=>0,
  				'province'=>0,
  				'district_id'=>'',
  				'comm_id'=>'',
  				'village'=>'',
  				'client_name'=>'',
  				'co_id'=>0,
  				'repayment_method'=>0,
  				'start_date'=>date('Y-m-d'),
  				'end_date'=>date('Y-m-d'),
  				'status' => -1,);
  	}
  	$db  = new Report_Model_DbTable_DbLoan();
  	$this->view->rsearch=$search;

  	$this->view->loanlate_list =$db->getALLLoanlate($search);
  	
  	$frm = new Loan_Form_FrmSearchLoan();
  	$frm = $frm->AdvanceSearch();
  	Application_Model_Decorator::removeAllDecorator($frm);
  	$this->view->frm_search = $frm;
  	
  	$key = new Application_Model_DbTable_DbKeycode();
  	$this->view->data=$key->getKeyCodeMiniInv(TRUE);
  	
  	$frmpopup = new Application_Form_FrmPopupGlobal();
  	$this->view->footerReport = $frmpopup->getFooterReport();
  }
  function rptGroupmemberAction(){
  	$db  = new Report_Model_DbTable_DbLoan();
  	$id = $this->getRequest()->getParam("id");
  	$key = new Application_Model_DbTable_DbKeycode();
  	$this->view->data=$key->getKeyCodeMiniInv(TRUE);
  	if (!empty($id)){
  		$this->view->loanmember_list =$db->getALLGroupDisburse($id);
  		//print_r($db->getALLGroupDisburse($id));
    }
  }
 
  function rptPaymentAction(){
  	$db  = new Report_Model_DbTable_DbLoan();	
	$key = new Application_Model_DbTable_DbKeycode();
	$this->view->data=$key->getKeyCodeMiniInv(TRUE);
	if($this->getRequest()->isPost()){
		$search = $this->getRequest()->getPost();
	}else {
		$search = array(
				'adv_search' => '',
				'status_search' => -1,
				'status' => -1,
				'branch_id' => "",
				'client_name' => "",
				'co_id' => "",
				'start_date'=> date('Y-m-d'),
	  			'end_date'=>date('Y-m-d')
		);
	}
	$this->view->loantotalcollect_list =$rs=$db->getALLLoanPayment($search);
	$this->view->rescheduleFee = $db->getAdminFeeByReschedule($search);
	$this->view->adminFee = $db->getALLLTotalFeeByCurrency($search);
	
	
	$this->view->list_end_date = $search;
	$frm = new Loan_Form_FrmSearchLoan();
	$frm = $frm->AdvanceSearch();
	Application_Model_Decorator::removeAllDecorator($frm);
	$this->view->frm_search = $frm;
	
	$frmpopup = new Application_Form_FrmPopupGlobal();
	$this->view->footerReport = $frmpopup->getFooterReport();
  }
  function rptCommissionAction(){
  	$db  = new Report_Model_DbTable_DbLoan();
  	$key = new Application_Model_DbTable_DbKeycode();
  	$this->view->data=$key->getKeyCodeMiniInv(TRUE);
  	if($this->getRequest()->isPost()){
  		$search = $this->getRequest()->getPost();
  	}else {
  		$search = array(
  				'adv_search' => '',
  				'status_search' => -1,
  				'status' => -1,
  				'branch_id' => "",
  				'client_name' => "",
  				'co_id' => "",
  				'start_date'=> date('Y-m-d'),
  				'end_date'=>date('Y-m-d')
  		);
  	}
  	$this->view->loantotalcollect_list =$rs=$db->getALLCommission($search);
  
  	// 	$this->view->list_end_date=$search;
  
  	$this->view->list_end_date = $search;
  	$frm = new Loan_Form_FrmSearchLoan();
  	$frm = $frm->AdvanceSearch();
  	Application_Model_Decorator::removeAllDecorator($frm);
  	$this->view->frm_search = $frm;
  }
  
  function rptLoanLateAction(){
  	if($this->getRequest()->isPost()){
  		$search = $this->getRequest()->getPost();		
  	}else {
  		$search = array(
  				'adv_search'		=>	"",
  				'end_date' => date('Y-m-d'),
  				'status' => -1,
  				'branch_id'		=>	0,
  				'co_id'=>0
  		);
  	}
  	$db  = new Report_Model_DbTable_DbLoan();
  	$this->view->loanlate_list =$db->getALLLoanlate($search);
  	
  	$key = new Application_Model_DbTable_DbKeycode();
  	$this->view->data=$key->getKeyCodeMiniInv(TRUE);
  	
  	$this->view->list_end_date = $search["end_date"];
  	
  	$frm = new Loan_Form_FrmSearchLoan();
  	$frm = $frm->AdvanceSearch();
  	Application_Model_Decorator::removeAllDecorator($frm);
  	$this->view->frm_search = $frm;
  	
  	$frmpopup = new Application_Form_FrmPopupGlobal();
  	$this->view->footerReport = $frmpopup->getFooterReport();
  }
  function rptLoanLatedetailAction(){
  	if($this->getRequest()->isPost()){
  		$search = $this->getRequest()->getPost();
  	}else {
  		$search = array(
  				'adv_search'		=>	"",
  				'start_date' => date('Y-m-d'),
  				'end_date' => date('Y-m-d'),
  				'client_name'=>-1,
  				'status' => -1,
  				'branch_id'		=>	0,
  				'co_id'=>0
  		);
  	}
  	 
  	$db  = new Report_Model_DbTable_DbLoan();
  	$this->view->loanlate_list =$db->getAllLoanlateDetail($search);
  	 
  	$key = new Application_Model_DbTable_DbKeycode();
  	$this->view->data=$key->getKeyCodeMiniInv(TRUE);
  	 
  	$this->view->list_end_date = $search["end_date"];
  	 
  	$frm = new Loan_Form_FrmSearchLoan();
  	$frm = $frm->AdvanceSearch();
  	Application_Model_Decorator::removeAllDecorator($frm);
  	$this->view->frm_search = $frm;
  }
  function rptLoanNplAction(){
  	$db  = new Report_Model_DbTable_DbLoan();
  	$key = new Application_Model_DbTable_DbKeycode();
  	$this->view->data=$key->getKeyCodeMiniInv(TRUE);
  	if($this->getRequest()->isPost()){
  		$search = $this->getRequest()->getPost();
  		$search['branch_id']=$search['branch'];
  	}else{
  		$search = array(
  				'adv_search'=>'',
  				'branch_id' => '',
  				'client_name' =>'',
  				'client_code'=>'',
  				'Term'=>'',
  				'status' =>'',
  				'cash_type'=>'',
  				'end_date'=>date('Y-m-d'));
  	}
  	$this->view->LoanCollectionco_list =$db->getALLNPLLoan($search);
  	$this->view->list_end_date=$search;
  	$fm = new Loan_Form_Frmbadloan();
  	$frm = $fm->FrmBadLoan();
  	Application_Model_Decorator::removeAllDecorator($frm);
  	$this->view->frm_loan = $frm;
  	
  	$db = new Application_Model_DbTable_DbGlobal();
  	$this->view->classified_loan = $db->ClassifiedLoan();
  	
  	$frmpopup = new Application_Form_FrmPopupGlobal();
  	$this->view->footerReport = $frmpopup->getFooterReport();
  }
  function rptLoanOutstandingAction(){//loand out standing with /collection
	    $db  = new Report_Model_DbTable_DbLoan();
	  	if($this->getRequest()->isPost()){
	  		$search = $this->getRequest()->getPost();
	  	}else {
	  		$search = array(
	  				'adv_search' => "",
	  				'end_date' => date('Y-m-d'),
	  				'status' => "",
	  				'co_id' => "",
	  				'status_use'=>-1,
	  				'branch_id'		=>"",
	  				'member'=>-1
	  		);
	  	}
	  	$this->view->fordate = $search['end_date'];
	  	$rs= $db->getAllOutstadingLoan($search);
	  	$frm = new Loan_Form_FrmSearchLoan();
	  	
	  	$frms = $frm->AdvanceSearch();
	  	$key = new Application_Model_DbTable_DbKeycode();
	  	$this->view->data=$key->getKeyCodeMiniInv(TRUE);
	  	Application_Model_Decorator::removeAllDecorator($frms);
	  	
	  	$this->view->frm_search = $frms;
	  	$this->view->outstandloan = $rs;
	  	
	  	$frmpopup = new Application_Form_FrmPopupGlobal();
	  	$this->view->footerReport = $frmpopup->getFooterReport();
  }
  function rptUnpaidLoanByCoAction(){
  	$db  = new Report_Model_DbTable_DbLoan();
  	
  	$key = new Application_Model_DbTable_DbKeycode();
  	$this->view->data=$key->getKeyCodeMiniInv(TRUE);
  	if($this->getRequest()->isPost()){
  		$search = $this->getRequest()->getPost();
  		if(isset($search['btn_submit'])){
  			$this->view->LoanCollectionco_list =$db->getAllLoanByCo($search);
  		}else {
  			$collumn = array("id","branch","co_name","receipt_no","loan_number","team_group","total_principal_permonth"
  					,"total_interest","penalize_amount","amount_payment","service_charge","date_pay");
  			$this->exportFileToExcel('ln_client_receipt_money',$db->getAllLoanByCo(),$collumn);
  		}
  	}else{
  		$search = array(
  				'advance_search' => '',
  				'client_name' => "",
  				'start_date'=> date('Y-m-d'),
  				'end_date'=>date('Y-m-d'),
  				'branch_id'		=>	-1,
  				'co_id'		=> "",
  				'paymnet_type'	=> -1,
  				'status'=>"",);
  		$this->view->LoanCollectionco_list =$db->getAllLoanByCo($search);
  	}
  	$this->view->date_show=$search['end_date'];
	$this->view->start_date=$search['start_date'];
  	$frm = new Loan_Form_FrmSearchGroupPayment();
  	$fm = $frm->AdvanceSearch();
  	Application_Model_Decorator::removeAllDecorator($fm);
  	$this->view->frm_search = $fm;
  }
  function rptLoanCollectioncoAction(){
  	
  	if($this->getRequest()->isPost()){
  		$search = $this->getRequest()->getPost();
  	}else{
			$search = array(
				'adv_search' => '',
				'client_name' => -1,
				'start_date'=> date('Y-m-d'),
				'end_date'=>date('Y-m-d'),
				'branch_id'		=>	-1,
				'co_id'		=> -1,
				'paymnet_type'	=> -1,
				'status'=>"",);
	}
	$db  = new Report_Model_DbTable_DbLoan();
	$key = new Application_Model_DbTable_DbKeycode();
	$this->view->data=$key->getKeyCodeMiniInv(TRUE);
	
	$this->view->LoanCollectionco_list =$db->getALLLoanCollectionco($search);
	$this->view->rs_adminfee =$db->getAdminfeeloanGroupByCO($search);
	
	
	$this->view->date_show=$search['end_date'];
	$this->view->start_date=$search['start_date'];
	$this->view->search = $search;
  	$frm = new Loan_Form_FrmSearchGroupPayment();
  	$fm = $frm->AdvanceSearch();
  	Application_Model_Decorator::removeAllDecorator($fm);
  	$this->view->frm_search = $fm;
  	
  	$frmpopup = new Application_Form_FrmPopupGlobal();
  	$this->view->footerReport = $frmpopup->getFooterReport();
  }
function rptLoanTotalCollectAction(){
	$db  = new Report_Model_DbTable_DbLoan();	
	$key = new Application_Model_DbTable_DbKeycode();
	$this->view->data=$key->getKeyCodeMiniInv(TRUE);
	if($this->getRequest()->isPost()){
		$search = $this->getRequest()->getPost();
		if(isset($search['btn_search'])){
			$this->view->loantotalcollect_list=$db->getALLLoanTotalcollect($search);
		}
	}else {
	$search = array(
			'adv_search' => '',
			'status_search' => -1,
			'status' => -1,
			'branch_id' => "",
			'client_name' => "",
			'co_id' => "",
			'start_date' =>date('Y-m-d'),
			'end_date' => date('Y-m-d'),
	);
	$this->view->loantotalcollect_list =$rs=$db->getALLLoanTotalcollect($search);
	}
	$this->view->list_end_date=$search;
	$frm = new Loan_Form_FrmSearchLoan();
	$frm = $frm->AdvanceSearch();
	Application_Model_Decorator::removeAllDecorator($frm);
	$this->view->frm_search = $frm;
}
function rptRescheduleLoanAction(){
	$db  = new Report_Model_DbTable_DbLoan();
	$key = new Application_Model_DbTable_DbKeycode();
	$this->view->data=$key->getKeyCodeMiniInv(TRUE);
	if($this->getRequest()->isPost()){
		$search = $this->getRequest()->getPost();
	}
	else{
		$search = array(
				'branch_id'=>'',
				'client_name'=>'',
				'pay_every'=>-1,
				'start_date'=> date('Y-m-d'),
				'end_date'=>date('Y-m-d'));
	}
	$this->view->loanrelease_list=$db->getRescheduleLoan($search);
	$this->view->list_end_date=$search;
	 
	$frm = new Loan_Form_FrmSearchLoan();
	$frm = $frm->AdvanceSearch();
	Application_Model_Decorator::removeAllDecorator($frm);
	$this->view->frm_search = $frm;
	
	$frmpopup = new Application_Form_FrmPopupGlobal();
	$this->view->footerReport = $frmpopup->getFooterReport();
}
public function paymentscheduleListAction(){
	try{
		$db = new Report_Model_DbTable_DbRptPaymentSchedule();
		if($this->getRequest()->isPost()){
			$search = $this->getRequest()->getPost();
		}
		else{
			$search = array(
					'adv_search' => '',
					'status_search' => -1,
					'client_id' => -1,
					'status' => -1,
					'from_date' =>date('Y-m-d'),
					'to_date' => date('Y-m-d'),
			);
		}
		$rs_rows = $db->getAllClientPaymentListRpt($search);
		
		$glClass = new Application_Model_GlobalClass();
		$rs_rows = $glClass->getImgActive($rs_rows, BASE_URL, true);
		
		$collumns = array("BRANCH_NAME","LOAN_NO","CLIENT_NO","CUSTOMER_NAME","LOAN_AMOUNT","AMIN_FEE","INTEREST RATE","TERM_BORROW","METHOD","TIME_COLLECT","ZONE","CO_NAME",
				"STATUS");
		$link=array(
				'module'=>'report','controller'=>'loan','action'=>'rpt-paymentschedules',
		);
		$list = new Application_Form_Frmtable();
		$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array(
				'total_capital'=>$link,'loan_number'=>$link,'client_number'=>$link));
				
		}catch (Exception $e){
		Application_Form_FrmMessage::message("Application Error");
		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
	}
	
	$frm = new Loan_Form_FrmSearchLoan();
	$frm = $frm->AdvanceSearch();
	Application_Model_Decorator::removeAllDecorator($frm);
	$this->view->frm_search = $frm;
}
public function exportFileToExcel($table,$data,$thead){
	$this->_helper->layout->disableLayout();
	$db = new Report_Model_DbTable_DbExportfile();
	$finalData = $db->getFileby($table,$data,$thead);
	$filename = APPLICATION_PATH . "/tmp/$table-" . date( "m-d-Y" ) . ".xlsx";
	$realPath = realpath( $filename );
	if ( false === $realPath ){
		touch( $filename );
		chmod( $filename, 0777 );
	}
	$filename = realpath( $filename );
	$handle = fopen( $filename, "w" );
	fputcsv( $handle, $thead, "\t" );
	$this->getResponse()->setRawHeader( "Content-Type: application/vnd.ms-excel; charset=utf-8" )
	->setRawHeader( "Content-Disposition: attachment; filename=excel.xls" )
	->setRawHeader( "Content-Transfer-Encoding: binary" )
	->setRawHeader( "Expires: 0" )
	->setRawHeader( "Cache-Control: must-revalidate, post-check=0, pre-check=0" )
	->setRawHeader( "Pragma: public" )
	->setRawHeader( "Content-Length: " . filesize( $filename ) )
	->sendResponse();
	foreach ( $finalData AS $finalRow )
	{
		fputcsv( $handle,$finalRow, "\t" );
	}
	fclose( $handle );
	$this->_helper->viewRenderer->setNoRender();
	readfile( $filename );//exit();
}
function rptPaymentschedulesAction(){
	$db = new Report_Model_DbTable_DbRptPaymentSchedule();
	$id =$this->getRequest()->getParam('id');
	$id = empty($id)?0:$id;
	
	$row = $db->getPaymentSchedule($id);
	$this->view->tran_schedule=$row;
	
	if(empty($row)){
		$inFrame = $this->getRequest()->getParam('inFrame');
		$inFrame = empty($inFrame)?"":$inFrame;
 		Application_Form_FrmMessage::Sucessfull("NO_RECORD",'/report/loan/rpt-loan-disburse?inFrame='.$inFrame,2);
		exit();
	}
	$db = new Application_Model_DbTable_DbGlobal();
	$rs = $db->getClientByMemberId($id);
	if(empty($rs)){
		$inFrame = $this->getRequest()->getParam('inFrame');
		$inFrame = empty($inFrame)?"":$inFrame;
 		Application_Form_FrmMessage::Sucessfull("NO_RECORD",'/report/loan/rpt-loan-disburse?inFrame='.$inFrame,2);
		exit();
	}
	
	$this->view->client =$rs;
	$frm = new Application_Form_FrmSearchGlobal();
	$form = $frm->FrmSearchLoadSchedule();
	Application_Model_Decorator::removeAllDecorator($form);
	$this->view->form_filter = $form;
	$day_inkhmer = $db->getDayInkhmerBystr(null);
	$this->view->day_inkhmer = $day_inkhmer;
	
	$key = new Application_Model_DbTable_DbKeycode();
	$this->view->data=$key->getKeyCodeMiniInv(TRUE);
	
 }
 function rptUpdatepaymentAction(){
 	if($this->getRequest()->isPost()){
 		$_data = $this->getRequest()->getPost();
 		try {
 			$_dbmodel = new Loan_Model_DbTable_DbLoanIL();
 			$_dbmodel->updatePaymentStatus($_data);
 			Application_Form_FrmMessage::Sucessfull("UPDATE_SUCESS","/report/loan/rpt-loan-disburse");
 		}catch (Exception $e) {
 			Application_Form_FrmMessage::message("INSERT_FAIL");
 			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
 		}
 	}
 	$db = new Report_Model_DbTable_DbRptPaymentSchedule();
 	$id =$this->getRequest()->getParam('id');
	$id = empty($id)?0:$id;
	
 	$row = $db->getPaymentSchedule($id);
 	$this->view->tran_schedule=$row;
	
	if(empty($row)){
		$inFrame = $this->getRequest()->getParam('inFrame');
		$inFrame = empty($inFrame)?"":$inFrame;
 		Application_Form_FrmMessage::Sucessfull("NO_RECORD",'/report/loan/paymentschedule-list?inFrame='.$inFrame,2);
		exit();
	}
	
 	$db = new Application_Model_DbTable_DbGlobal();
 	$rs = $db->getClientByMemberId($id);
 	
 	$this->view->client =$rs;
 	$frm = new Application_Form_FrmSearchGlobal();
 	$form = $frm->FrmSearchLoadSchedule();
 	Application_Model_Decorator::removeAllDecorator($form);
 	$this->view->form_filter = $form;
 	$day_inkhmer = $db->getDayInkhmerBystr(null);
 	$this->view->day_inkhmer = $day_inkhmer;
 
 	$key = new Application_Model_DbTable_DbKeycode();
 	$this->view->data=$key->getKeyCodeMiniInv(TRUE);
 	$this->view->id = $id;
 
 }
 function rptMemberschedulesAction(){//for schedule member
 	$db = new Report_Model_DbTable_DbRptPaymentSchedule();
 	$id =$this->getRequest()->getParam('id');
	$id = empty($id)?0:$id;
	
 	$row = $db->getPaymentSchedule($id);
 	$this->view->tran_schedule=$row;
 	
	if(empty($row)){
		$inFrame = $this->getRequest()->getParam('inFrame');
		$inFrame = empty($inFrame)?"":$inFrame;
 		Application_Form_FrmMessage::Sucessfull("NO_RECORD",'/report/loan/paymentschedule-list?inFrame='.$inFrame,2);
		exit();
	}
	
 	$db = new Application_Model_DbTable_DbGlobal();
 	$rs = $db->getClientByMemberId(@$row[0]['member_id']);
 	$this->view->client =$rs;
 	$frm = new Application_Form_FrmSearchGlobal();
 	$form = $frm->FrmSearchLoadSchedule();
 	Application_Model_Decorator::removeAllDecorator($form);
 	$this->view->form_filter = $form;
 	$db= new Application_Model_DbTable_DbGlobal();
 	$day_inkhmer = $db->getDayInkhmerBystr(null);
 	$this->view->day_inkhmer = $day_inkhmer;
 
 	$key = new Application_Model_DbTable_DbKeycode();
 	$this->view->data=$key->getKeyCodeMiniInv(TRUE);
 
 }
 function rptGroupschedulesAction(){//for schedule member
 	$db = new Report_Model_DbTable_DbRptPaymentSchedule();
 	$id =$this->getRequest()->getParam('id');
	$id = empty($id)?0:$id;
	
 	$row = $db->getPaymentScheduleGroupById($id);
 	$this->view->tran_schedule=$row;
	if(empty($row)){
		$inFrame = $this->getRequest()->getParam('inFrame');
		$inFrame = empty($inFrame)?"":$inFrame;
 		Application_Form_FrmMessage::Sucessfull("NO_RECORD",'/report/loan/paymentschedule-list?inFrame='.$inFrame,2);
		exit();
	}
	
 	$db = new Application_Model_DbTable_DbGlobal();
 	$rs = $db->getClientGroupByMemberId(@$row[0]['member_id']);
 	$this->view->client =$rs;
 	$frm = new Application_Form_FrmSearchGlobal();
 	$form = $frm->FrmSearchLoadSchedule();
 	Application_Model_Decorator::removeAllDecorator($form);
 	$this->view->form_filter = $form;
 	$db= new Application_Model_DbTable_DbGlobal();
 	$day_inkhmer = $db->getDayInkhmerBystr(null);
 	$this->view->day_inkhmer = $day_inkhmer;
 
 	$key = new Application_Model_DbTable_DbKeycode();
 	$this->view->data=$key->getKeyCodeMiniInv(TRUE);
 
 }
 function rptGroupchedulesAction(){//for schedule member
 	$db = new Report_Model_DbTable_DbRptPaymentSchedule();
 	$id =$this->getRequest()->getParam('id');
	$id = empty($id) ? 0 : $id;
	
 	$row = $db->getPaymentSchedule($id);
 	$this->view->tran_schedule=$row;
 	if(empty($row)){
 		Application_Form_FrmMessage::Sucessfull("NO_RECORD",'/report/loan/paymentschedule-list',2);
		exit();
 	}
 	$db = new Application_Model_DbTable_DbGlobal();
 	$rs = $db->getClientByMemberId(@$row[0]['member_id']);
 	$this->view->client =$rs;
 	$frm = new Application_Form_FrmSearchGlobal();
 	$form = $frm->FrmSearchLoadSchedule();
 	Application_Model_Decorator::removeAllDecorator($form);
 	$this->view->form_filter = $form;
 	$db= new Application_Model_DbTable_DbGlobal();
 	$day_inkhmer = $db->getDayInkhmerBystr(null);
 	$this->view->day_inkhmer = $day_inkhmer;
 
 	$key = new Application_Model_DbTable_DbKeycode();
 	$this->view->data=$key->getKeyCodeMiniInv(TRUE);
 
 }
 function rptLoanIncomeAction(){
 	
 	$db  = new Report_Model_DbTable_DbLoan();
 	 
 	$key = new Application_Model_DbTable_DbKeycode();
 	$this->view->data=$key->getKeyCodeMiniInv(TRUE);
 	if($this->getRequest()->isPost()){
 		$search = $this->getRequest()->getPost();
 	}else{
 	$search = array(
	 	'adv_search' => '',
	 	'client_name' => -1,
	 	'start_date'=> date('Y-m-d'),
	 	'end_date'=>date('Y-m-d'),
 		'branch_id'		=>	-1,
		'co_id'		=> -1,
		'paymnet_type'	=> -1,
 		'status'=>"",);
			
 	}
 	$this->view->LoanCollectionco_list =$db->getALLLoanIcome($search);
 	$this->view->LoanFee_list =$db->getALLLTotalFeeByCurrency($search);
 	
 	$this->view->list_end_date=$search;
 	$frm = new Loan_Form_FrmSearchGroupPayment();
 	$fm = $frm->AdvanceSearch();
 	Application_Model_Decorator::removeAllDecorator($fm);
 	$this->view->frm_search = $fm;
 	
 	$frmpopup = new Application_Form_FrmPopupGlobal();
 	$this->view->footerReport = $frmpopup->getFooterReport();
 }
 function rptLoanPayoffAction(){
 	
 	$db  = new Report_Model_DbTable_DbLoan();
 	$key = new Application_Model_DbTable_DbKeycode();
 	$this->view->data=$key->getKeyCodeMiniInv(TRUE);
 	if($this->getRequest()->isPost()){
 		$search = $this->getRequest()->getPost();
 	}else{
	 	$search = array(
	 	'advance_search'  => '',
	 	'client_name' => -1,
	 	'start_date'  => date('Y-m-d'),
	 	'end_date'    => date('Y-m-d'),
	 	'branch_id'	  =>	-1,
		'co_id'		  => -1,
		'paymnet_type'=> -1,
	 	'status'      => "",);
 	}
 	$this->view->LoanCollectionco_list = $db->getALLLoanPayoff($search);
 	$this->view->list_end_date=$search;
 	$frm = new Loan_Form_FrmSearchGroupPayment();
 	$fm = $frm->AdvanceSearch();
 	Application_Model_Decorator::removeAllDecorator($fm);
 	$this->view->frm_search = $fm;
 	
 	$frmpopup = new Application_Form_FrmPopupGlobal();
 	$this->view->footerReport = $frmpopup->getFooterReport();
 }
 function rptLoanExpectIncomeAction(){
 	if($this->getRequest()->isPost()){
 		$search = $this->getRequest()->getPost();
 	}else{
 		$search = array(
 				'adv_search' => '',
 				'client_name' => -1,
 				'start_date'=> date('Y-m-d'),
 				'end_date'=>date('Y-m-d'),
 				'branch_id'		=>	-1,
 				'co_id'	=> -1,
 				'paymnet_type'	=> -1,
 				'status'=>"",);
 	}
 	$this->view->list_end_date=$search;
 	$db  = new Report_Model_DbTable_DbLoan();
 	$this->view->LoanCollectionco_list =$db->getALLLoanExpectIncome($search);
 	
 	$frm = new Loan_Form_FrmSearchGroupPayment();
 	$fm = $frm->AdvanceSearch();
 	Application_Model_Decorator::removeAllDecorator($fm);
 	$this->view->frm_search = $fm;
 	
 	$key = new Application_Model_DbTable_DbKeycode();
 	$this->view->data=$key->getKeyCodeMiniInv(TRUE);
 	$frmpopup = new Application_Form_FrmPopupGlobal();
 	$this->view->footerReport = $frmpopup->getFooterReport();
 }
 function rptBadloanAction(){
 	$db  = new Report_Model_DbTable_DbLoan();
 	$key = new Application_Model_DbTable_DbKeycode();
 	$this->view->data=$key->getKeyCodeMiniInv(TRUE);
 	if($this->getRequest()->isPost()){
 		$search = $this->getRequest()->getPost();
 	}else{
 		$search = array(
 				'adv_search'=>'',
				'branch' => '',
				'client_name' =>'',
				'co_id'=>0,
				'Term'=>'',
				'status' =>'',
				'cash_type'=>'',
				'start_date'=> date('Y-m-01'),
				'end_date'=>date('Y-m-d'));
 	}
 	$this->view->LoanCollectionco_list =$db->getALLBadloan($search);
 	$this->view->list_end_date=$search;
 	$fm = new Loan_Form_Frmbadloan();
	$frm = $fm->FrmBadLoan();
	Application_Model_Decorator::removeAllDecorator($frm);
	$this->view->frm_loan = $frm;
 }
 function rptWritoffAction(){
 	$db  = new Report_Model_DbTable_DbLoan();
 	if($this->getRequest()->isPost()){
 		$search = $this->getRequest()->getPost();
 		
 	}else{
 		$search = array(
 				'adv_search'=>'',
 				'branch' => '',
 				'client_name' =>'',
 				'client_code'=>'',
 				'Term'=>'',
 				'co_id'=>-1,
 				'status' =>'',
 				'cash_type'=>'',
 				'start_date'=> date('Y-m-d'),
 				'end_date'=>date('Y-m-d'));
 	}
 	$this->view->LoanCollectionco_list =$db->getALLWritoff($search);
 	$this->view->list_end_date=$search;
 	$fm = new Loan_Form_Frmbadloan();
 	$frm = $fm->FrmBadLoan();
 	Application_Model_Decorator::removeAllDecorator($frm);
 	$this->view->frm_loan = $frm;
 	
 	$frmpopup = new Application_Form_FrmPopupGlobal();
 	$this->view->footerReport = $frmpopup->getFooterReport();
 }
 function rptLoanXchangeAction(){
 	$db  = new Report_Model_DbTable_DbLoan();
 	$key = new Application_Model_DbTable_DbKeycode();
 	$this->view->data=$key->getKeyCodeMiniInv(TRUE);
 	if($this->getRequest()->isPost()){
 		$search = $this->getRequest()->getPost();
 	}else{
 		$search = array(
 				'adv_search'=>'',
 				'branch' => '',
 				'client_name' =>'',
 				'client_code'=>'',
 				'Term'=>'',
 				'status' =>'',
 				'cash_type'=>'',
 				'start_date'=> date('Y-m-d'),
 				'end_date'=>date('Y-m-d'));
 		
 	}
	$this->view->list_end_date=$search;
 	$this->view->Loanxchange_list =$db->getAllxchange($search);
 	$frm = new Loan_Form_FrmSearchLoan();
 	$frm = $frm->AdvanceSearch();
 	Application_Model_Decorator::removeAllDecorator($frm);
 	$this->view->frm_search = $frm;
 }
 
 function rptPaymentHistoryAction(){
 	$db  = new Report_Model_DbTable_DbLoan();
 
 	if($this->getRequest()->isPost()){
 		$search = $this->getRequest()->getPost();
 	}else {
 		$search = array(
 				'adv_search' => '',
 				'status_search' => -1,
 				'status' => -1,
 				'branch_id' => "",
 				'client_name' => "",
 				'co_id' => "",
 				'start_date' =>date('Y-m-d'),
 				'end_date' => date('Y-m-d'),
 		);
 	}
 	$this->view->loantotalcollect_list =$db->getALLLoanPayment($search);
 	$this->view->list_end_date=$search;
 	$frm = new Loan_Form_FrmSearchLoan();
 	$frm = $frm->AdvanceSearch();
 	Application_Model_Decorator::removeAllDecorator($frm);
 	$this->view->frm_search = $frm;
 	
 	$key = new Application_Model_DbTable_DbKeycode();
 	$this->view->data=$key->getKeyCodeMiniInv(TRUE);
 	
 	$frmpopup = new Application_Form_FrmPopupGlobal();
 	$this->view->footerReport = $frmpopup->getFooterReport();
 }
 function rptLoanTrasferAction(){//release all loan
 	$db  = new Report_Model_DbTable_DbLoan();
 	$key = new Application_Model_DbTable_DbKeycode();
 	$this->view->data=$key->getKeyCodeMiniInv(TRUE);
 	if($this->getRequest()->isPost()){
 		$search = $this->getRequest()->getPost();
 	}
 	else{
 		$search = array(
 				'branch_id'=>'',
 				'client_name'=>'',
 				'pay_every'=>-1,
 				'co_id'=>'',
 				'start_date'=> date('Y-m-d'),
 				'end_date'=>date('Y-m-d'));
 	}
 	$this->view->loantrasfer=$db->getAllTransferoan($search);
 	$this->view->list_end_date=$search;
 	 
 	$frm = new Loan_Form_FrmSearchLoan();
 	$frm = $frm->AdvanceSearch();
 	Application_Model_Decorator::removeAllDecorator($frm);
 	$this->view->frm_search = $frm;
 	
 	$frmpopup = new Application_Form_FrmPopupGlobal();
 	$this->view->footerReport = $frmpopup->getFooterReport();
 }
 
 
 
function rptLoanTrasferzoneAction(){//release all loan
 	$db  = new Report_Model_DbTable_DbLoan();
 	$key = new Application_Model_DbTable_DbKeycode();
 	$this->view->data=$key->getKeyCodeMiniInv(TRUE);
 	if($this->getRequest()->isPost()){
 		$search = $this->getRequest()->getPost();
 	}
 	else{
 		$search = array(
 				       'adv_search'=>'',
 				       'branch_name'=>'-1',
						'co_code'=>'1',
						'start_date'=> date('Y-m-01'),
						'end_date'=>date('Y-m-d'),
						'txt_search'=>'',
						'status' => '',
						'note'=>'',
 				);
 	}	
 	$db = new Report_Model_DbTable_DbLoan();
 	$rs_rows= $db->getAllinfoZone($search);//call frome model
 	$this->view->loantrasferzone=$db->getAllinfoZone($search);
 	$this->view->list_end_date=$search;
 	 
 	$frm = new Loan_Form_FrmSearchLoan();
 	$frm = $frm->AdvanceSearch();
 	Application_Model_Decorator::removeAllDecorator($frm);
 	$this->view->frm_search = $frm;
 	
 	$frmpopup = new Application_Form_FrmPopupGlobal();
 	$this->view->footerReport = $frmpopup->getFooterReport();
 }
 
 function rptLoanClientcoAction()
 {
 	$db  = new Report_Model_DbTable_DbLoan();
 	if($this->getRequest()->isPost()){
 		$search = $this->getRequest()->getPost();
 	}
 	else{
 		$search = array(
 				'branch_id'=>-1,
 				'pay_every'=>'',
 				'member'=>'',
 				'co_id'=>-1,
 				'start_date'=> date('Y-m-d'),
 				'end_date'=>date('Y-m-d'));
 	
 	}
 	$this->view->list_end_date=$search;
 	$this->view->loanrelease_list=$db->getClientLoanCo($search);
 	 
 	$frm = new Loan_Form_FrmSearchLoan();
 	$frm = $frm->AdvanceSearch();
 	Application_Model_Decorator::removeAllDecorator($frm);
 	$this->view->frm_search = $frm;
 	 
 	$key = new Application_Model_DbTable_DbKeycode();
 	$this->view->data=$key->getKeyCodeMiniInv(TRUE);
 }
 function rptExpenseAction(){
	 	$db = new Report_Model_DbTable_DbRptIncomeExpense();
	 	if($this->getRequest()->isPost()){
	 		$formdata=$this->getRequest()->getPost();
	 	}
	 	else{
	 		$formdata = array(
	 				"adv_search"=>'',
	 				"currency_type"=>-1,
	 				"category_id"=>"",
	 				"status"=>-1,
	 				'start_date'=> date('Y-m-d'),
	 				'end_date'=>date('Y-m-d'),
	 		);
	 	}
	 	$this->view->rs= $db->getAllExpenseReport($formdata);//call frome model
	 	$this->view->list_end_date=$formdata;
	 	
	 	$frm = new Loan_Form_FrmSearchLoan();
	 	$frm = $frm->AdvanceSearch();
	 	Application_Model_Decorator::removeAllDecorator($frm);
	 	$this->view->frm_search = $frm;
	 	
	 	
	 	$pructis=new Tellerandexchange_Form_Frmexpense();
	 	$frmexp = $pructis->FrmAddExpense();
	 	Application_Model_Decorator::removeAllDecorator($frmexp);
	 	$this->view->frm_expense=$frmexp;
	 	
	 	$frmpopup = new Application_Form_FrmPopupGlobal();
	 	$this->view->footerReport = $frmpopup->getFooterReport();
 }
 function rptIncomestatementAction(){
 	$db  = new Report_Model_DbTable_DbLoan();
 		
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
 			'interest_paidr'=>0,
 			'penalize_paidr'=>0,
 			'service_paidr'=>0,
 			
 			'interest_paidd'=>0,
 			'penalize_paidd'=>0,
 			'service_paidd'=>0,
 			
 			'interest_paidb'=>0,
 			'penalize_paidb'=>0,
 			'service_paidb'=>0,
 			
 			'adminfee_d'=>0,
 			'other_feed'=>0,
 			
 			'adminfee_r'=>0,
 			'other_feer'=>0,
 			
 			'adminfee_b'=>0,
 			'other_feeb'=>0,
 			
 			'other_incomer'=>0,
 			'other_incomed'=>0,
 			'other_incomeb'=>0,
 			
 			'expense_d'=>0,
 			'expense_r'=>0,
 			'expense_b'=>0,
 			
 			'badloan_d'=>0,
 			'badloan_r'=>0,
 			'badloan_b'=>0,
 			
 			//pawn income
 			'pawn_interestr'=>0,
 			'pawn_interestd'=>0,
 			'pawn_interestb'=>0,
 			'pawn_penalizer'=>0,
 			'pawn_penalized'=>0,
 			'pawn_penalizeb'=>0,
 			'pawn_servicefeer'=>0,
 			'pawn_servicefeed'=>0,
 			'pawn_servicefeeb'=>0,
 			'pawn_adminfeer'=>0,
 			'pawn_adminfeed'=>0,
 			'pawn_adminfeeb'=>0,
			
 			'pawn_selling_amount'=>0,
			
			//saleInstall
			'saleTotalPrincipalPaid'=>0,
 			'saleTotalInterestPaid'=>0,
 			'saleTotalPaymentPaid'=>0,
 			'saleTotalRecieveAmount'=>0,
 			'saleTotalPenalizePaid'=>0,
			
			"sale_purchase" => 0,
			"sale_retailPurchase" => 0,
 			
 		);
		
 	$incomecollect = $db->getLoanCollectIcome($search);
 	if(!empty($incomecollect)){
 		foreach($incomecollect as $row){
 			if($row['currency_type']==1){//riel
 				$income['interest_paidr']=$row['interest_paid'];
 				$income['penalize_paidr']=$row["penalize_paid"];
 				$income['service_paidr']=$row["service_paid"];
 			}elseif($row['currency_type']==2){//dollar
 				$income['interest_paidd']=$row['interest_paid'];
 				$income['penalize_paidd']=$row["penalize_paid"];
 				$income['service_paidd']=$row["service_paid"];
 			}else{//bath
 				$income['interest_paidb']=$row['interest_paid'];
 				$income['penalize_paidb']=$row["penalize_paid"];
 				$income['service_paidb']=$row["service_paid"];
 			}
 		}
 	}
 	
 	$rsotherfee = $db->getLoanadminFeeIcome($search);
 	if(!empty($rsotherfee)){
 		foreach($rsotherfee as $row){
 			if($row['curr_type']==1){//riel
 				$income['adminfee_r']=$row['admin_fee'];
 				$income['other_feer']=$row["other_fee"];
 			}elseif($row['curr_type']==2){//dollar
 				$income['adminfee_d']=$row['admin_fee'];
 				$income['other_feed']=$row["other_fee"];
 			}else{//bath
 				$income['adminfee_b']=$row['admin_fee'];
 				$income['other_feeb']=$row["other_fee"];
 			}
 		}
 	}
	
	$rescheduleFee = $db->getAdminFeeByReschedule($search);
	if(!empty($rescheduleFee)){
 		foreach($rescheduleFee as $row){
 			if($row['currency_type']==1){//riel
				$income['adminfee_r'] = empty($income['adminfee_r'])?0:$income['adminfee_r'];
 				$income['adminfee_r']=$income['adminfee_r']+$row['total_adminfee'];
 			}elseif($row['currency_type']==2){//dollar
				$income['adminfee_d'] = empty($income['adminfee_d'])?0:$income['adminfee_d'];
 				$income['adminfee_d']=$income['adminfee_d']+$row['total_adminfee'];
 			}else{//bath
				$income['adminfee_b'] = empty($income['adminfee_b'])?0:$income['adminfee_b'];
 				$income['adminfee_b']=$income['adminfee_b']+$row['total_adminfee'];
 			}
 		}
 	}
 	
 	$rsincome = $db->getAllOtherIncomeReport($search,1);
 	if(!empty($rsincome)){
 		foreach($rsincome as $row){
 			if($row['curr_type']==1){//riel
 				$income['other_incomer']=$row['total_amount'];
 			}elseif($row['curr_type']==2){//dollar
 				$income['other_incomed']=$row['total_amount'];
 			}else{//bath
 				$income['other_incomeb']=$row['total_amount'];
 			}
 		}
 	}
 	//pawn income
 	$pawnshpincome = $db->incomePawnshop($search);
 	if (!empty($pawnshpincome)) foreach ($pawnshpincome as $pwin){
 		if($pwin['currency_type']==1){//riel
 			$income['pawn_interestr']=$pwin['total_interestpaid'];
 			$income['pawn_penalizer']=$pwin['total_penalizepaid'];
 			$income['pawn_servicefeer']=$pwin['total_servicepaid'];
 		}elseif($pwin['currency_type']==2){//dollar
 			$income['pawn_interestd']=$pwin['total_interestpaid'];
 			$income['pawn_penalized']=$pwin['total_penalizepaid'];
 			$income['pawn_servicefeed']=$pwin['total_servicepaid'];
 		}else{//bath
 			$income['pawn_interestb']=$pwin['total_interestpaid'];
 			$income['pawn_penalizeb']=$pwin['total_penalizepaid'];
 			$income['pawn_servicefeeb']=$pwin['total_servicepaid'];
 		}
 	}
	$sellingPawn = $db->getTotalIncomeFromSellPawn($search);
 	if (!empty($sellingPawn)){
		$income['pawn_selling_amount']=$sellingPawn['totalSelling'];
 	}
 	$adminfeePawn = $db->incomeAdminFeePawnshop($search);
 	if (!empty($adminfeePawn)){
 		foreach ($adminfeePawn as $pwin){
	 		if($pwin['currency_type']==1){//riel
	 			$income['pawn_adminfeer']=$pwin['tAdminfee'];
	 		}elseif($pwin['currency_type']==2){//dollar
	 			$income['pawn_adminfeed']=$pwin['tAdminfee'];
	 		}else{//bath
	 			$income['pawn_adminfeeb']=$pwin['tAdminfee'];
	 		}
 		}
 	}

	$rescheduleFeePawn = $db->getAdminFeeByReschedulePawn($search);
	if(!empty($rescheduleFeePawn)){
 		foreach($rescheduleFeePawn as $row){
 			if($row['currency_type']==1){//riel
				$income['pawn_adminfeer'] = empty($income['pawn_adminfeer'])?0:$income['pawn_adminfeer'];
 				$income['pawn_adminfeer']=$income['pawn_adminfeer']+$row['total_adminfee'];
 			}elseif($row['currency_type']==2){//dollar
				$income['pawn_adminfeed'] = empty($income['pawn_adminfeed'])?0:$income['pawn_adminfeed'];
 				$income['pawn_adminfeed']=$income['pawn_adminfeed']+$row['total_adminfee'];
 			}else{//bath
				$income['pawn_adminfeeb'] = empty($income['pawn_adminfeeb'])?0:$income['pawn_adminfeeb'];
 				$income['pawn_adminfeeb']=$income['pawn_adminfeeb']+$row['total_adminfee'];
 			}
 		}
 	}
 	
 	$rsexpense = $db->getExpenseincomereport($search);
 	if(!empty($rsexpense)){
 		foreach($rsexpense as $row){
 			if($row['curr_type']==1){//riel
 				$income['expense_r']=$row['total_amount'];
 			}elseif($row['curr_type']==2){//dollar
 				$income['expense_d']=$row['total_amount'];
 			}else{//bath
 				$income['expense_b']=$row['total_amount'];
 			}
 		}
 	}
 	
 	$rsbadloan = $db->getALLTotalWritoff($search);
 	if(!empty($rsbadloan)){
 		foreach($rsbadloan as $row){
 			if($row['curr_type']==1){//riel
 				$income['badloan_r']=$row['total_amount'];
 			}elseif($row['curr_type']==2){//dollar
 				$income['badloan_d']=$row['total_amount'];
 			}else{//bath
 				$income['badloan_b']=$row['total_amount'];
 			}
 		}
 	}
	$saleInstall=$db->getTotalSaleInstallmentIncome($search);
	if(!empty($saleInstall)){
 		$income['saleTotalPrincipalPaid']	= $saleInstall['totalPrincipalPaid'];
		$income['saleTotalInterestPaid']	= $saleInstall['totalInterestPaid'];
		$income['saleTotalPaymentPaid']		= $saleInstall['totalPaymentPaid'];
		$income['saleTotalRecieveAmount']	= $saleInstall['totalRecieveAmount'];
		$income['saleTotalPenalizePaid']	= $saleInstall['totalPenalizePaid'];
 	}
	
	$purchaseExpense = $db->getTotalPurchase($search);
 	if (!empty($purchaseExpense)){
		$income['sale_purchase']=$purchaseExpense['totalAmount'];
 	}
	$search['typePurchase']=2;
	$retailPurchaseExpense = $db->getTotalPurchase($search);
 	if (!empty($retailPurchaseExpense)){
		$income['sale_retailPurchase']=$retailPurchaseExpense['totalAmount'];
 	}
	
	$this->view->rsincome=$income;
 	$this->view->list_end_date=$search;
 	
 	$frm = new Loan_Form_FrmSearchGroupPayment();
 	$fm = $frm->AdvanceSearch();
 	Application_Model_Decorator::removeAllDecorator($fm);
 	$this->view->frm_search = $fm;
 	
 	$frmpopup = new Application_Form_FrmPopupGlobal();
 	$this->view->footerReport = $frmpopup->getFooterReport();
 }
 function rptOutstandingvillageAction(){
 	$db  = new Report_Model_DbTable_DbLoan();
 	if($this->getRequest()->isPost()){
 		$search = $this->getRequest()->getPost();
 	}else {
 		$search = array(
 				'adv_search' => "",
 				'end_date' => date('Y-m-d'),
 				'status' => "",
 				'co_id' => -1,
 				'branch_id'	=>-1,
 				'member'=>''
 		);
 	}
 	$this->view->fordate = $search['end_date'];
 	$rs= $db->getLoanByVillage($search);
 	$frm = new Loan_Form_FrmSearchLoan();
 	$frms = $frm->AdvanceSearch();
 	$key = new Application_Model_DbTable_DbKeycode();
 	$this->view->data=$key->getKeyCodeMiniInv(TRUE);
 	Application_Model_Decorator::removeAllDecorator($frms);
 	$this->view->frm_search = $frms;
 	$this->view->outstandloan = $rs;
 }
 function chartdisburseAction(){
 	$db  = new Report_Model_DbTable_DbLoan();
 	$this->view->result = $db->getLoandisburseByMonth();
 }
 function rptParloanAction(){
 	$db  = new Report_Model_DbTable_DbLoan();
 	$key = new Application_Model_DbTable_DbKeycode();
 	$this->view->data=$key->getKeyCodeMiniInv(TRUE);
 	if($this->getRequest()->isPost()){
 		$search = $this->getRequest()->getPost();
 		$search['branch_id']=$search['branch'];
 	}else{
 		$search = array(
 				'adv_search'=>'',
 				'branch_id' => '',
 				'client_name' =>'',
 				'client_code'=>'',
 				'Term'=>'',
 				'status' =>'',
 				'cash_type'=>'',
 				'end_date'=>date('Y-m-d'));
 	}
 	$this->view->LoanCollectionco_list =$db->getALLParLoan($search);
 	$this->view->list_end_date=$search;
 	$fm = new Loan_Form_Frmbadloan();
 	$frm = $fm->FrmBadLoan();
 	Application_Model_Decorator::removeAllDecorator($frm);
 	$this->view->frm_loan = $frm;
 	 
 	$db = new Application_Model_DbTable_DbGlobal();
 	$this->view->classified_loan = $db->ClassifiedLoan();
 }
 function rptParbycoAction(){
 	$db  = new Report_Model_DbTable_DbLoan();
 	$key = new Application_Model_DbTable_DbKeycode();
 	$this->view->data=$key->getKeyCodeMiniInv(TRUE);
 	if($this->getRequest()->isPost()){
 		$search = $this->getRequest()->getPost();
 	}else{
 		$search = array(
 				'adv_search'=>'',
 				'branch_id' => '',
 				'client_name' =>'',
 				'client_code'=>'',
 				'client_name'=>'',
 				'status' =>'',
 				'currency_type'=>'',
 				'end_date'=>date('Y-m-d'));
 	}
 	$this->view->LoanCollectionco_list =$db->getALLParLoan($search);
 	$this->view->list_end_date=$search;
 	$frm = new Loan_Form_FrmSearchLoan();
 	$frms = $frm->AdvanceSearch();
 	Application_Model_Decorator::removeAllDecorator($frm);
 	$this->view->frm_loan = $frm;
 }
//  function contractLetterAction(){
//  		$db  = new Report_Model_DbTable_DbLnClient();
//  		$id =$this->getRequest()->getParam('id');
//  		$db  = new Report_Model_DbTable_DbLoan();
//  		$row = $db->getContractinfo($id);
//  		$this->view->loaninfo = $row ;
//  		$client_id = $row['client_id'];
//  		$this->view->calleteral_list = $db->getCalleteralByClient($client_id);
//  		//  		print_r($db->getCalleteralByClient($id));
//  }
 function rptDailypaymentAction(){
 	$db  = new Report_Model_DbTable_DbLoan();
 	$key = new Application_Model_DbTable_DbKeycode();
 	$this->view->data=$key->getKeyCodeMiniInv(TRUE);
 	if($this->getRequest()->isPost()){
 		$search = $this->getRequest()->getPost();
 	}else {
 		$search = array(
 				'adv_search' => '',
 				'status_search' => -1,
 				'status' => -1,
 				'branch_id' => "",
 				'client_name' => "",
 				'co_id' => "",
 				'currency_type'=>-1,
 				'start_date'=> date('Y-m-d'),
 				'end_date'=>date('Y-m-d')
 		);
 	}
 	$this->view->loantotalcollect_list =$db->getCollectDailyPayment($search);
 	$this->view->rsincome= $db->getAllOtherIncomeReport($search);//call frome model
 	$this->view->rsexpense= $db->getAllExpenseReport($search);//call frome model
 	$this->view->LoanFee_list =$db->getALLLFee($search);
	$this->view->rescheduleFee = $db->getAdminFeeByReschedule($search);
	
 	
 	$this->view->list_end_date = $search;
 	$frm = new Loan_Form_FrmSearchLoan();
 	$frm = $frm->AdvanceSearch();
 	Application_Model_Decorator::removeAllDecorator($frm);
 	$this->view->frm_search = $frm;
 	
 	$frmpopup = new Application_Form_FrmPopupGlobal();
 	$this->view->footerReport = $frmpopup->getFooterReport();
 }
 function rptLoanareaAction(){
 	$db  = new Report_Model_DbTable_DbloanCollect();
 	$key = new Application_Model_DbTable_DbKeycode();
 	$this->view->data=$key->getKeyCodeMiniInv(TRUE);
 	if($this->getRequest()->isPost()){
 		$search = $this->getRequest()->getPost();
 	}else{
 		$search = array(
 				'adv_search'=>'',
 				'branch_id' => '',
 				'client_name' =>'',
 				'client_code'=>'',
 				'co_id'=>'',
 				'status' =>'',
 				'currency_type'=>'',
 				'start_date'=>date('Y-m-d'),
 				'end_date'=>date('Y-m-d'));
 	}
 	$this->view->LoanCollectionco_list = $db->getALLParBYCO($search);
 	$this->view->search=$search;
 	$frm = new Loan_Form_FrmSearchLoan();
 	$frms = $frm->AdvanceSearch();
 	Application_Model_Decorator::removeAllDecorator($frm);
 	$this->view->frm_loan = $frm;
 	
 	$frmpopup = new Application_Form_FrmPopupGlobal();
 	$this->view->footerReport = $frmpopup->getFooterReport();
 }
 function rptSavingdiburseAction(){//release all loan
 	$db  = new Report_Model_DbTable_DbLoan();
 	if($this->getRequest()->isPost()){
 		$search = $this->getRequest()->getPost();
 	}
 	else{
 		$search = array(
 				'txt_search'=>'',
				'customer_code'=> -1,
				'branch_id' => -1,
				'status' => -1,
				'currency_type'=>-1,
				'account_type'=>-1,
				'start_date'=> date('Y-m-d'),
			 'end_date'=>date('Y-m-d'));
 	}
 	$this->view->loanrelease_list=$db->getAllSavingRelease($search);
 	$this->view->list_end_date=$search;
 	 
 	$frm = new Saving_Form_FrmSearch();
 	$frm = $frm->AdvanceSearch();
 	Application_Model_Decorator::removeAllDecorator($frm);
 	$this->view->frm_search = $frm;
 	 
 	$key = new Application_Model_DbTable_DbKeycode();
 	$this->view->data=$key->getKeyCodeMiniInv(TRUE);
 }
 function rptProfitcoAction(){
 	$db  = new Report_Model_DbTable_DbLoan();
 
 	if($this->getRequest()->isPost()){
 		$search = $this->getRequest()->getPost();
 	}else {
 		$search = array(
 				'adv_search' => '',
 				'status_search' => -1,
 				'status' => -1,
 				'branch_id' => "",
 				'client_name' => "",
 				'co_id' => "",
 				'start_date' =>date('Y-m-d'),
 				'end_date' => date('Y-m-d'),
 		);
 	}
 	$this->view->loantotalcollect_list =$db->getAllProfitco($search);
 	$this->view->list_end_date=$search;
 	$frm = new Loan_Form_FrmSearchLoan();
 	$frm = $frm->AdvanceSearch();
 	Application_Model_Decorator::removeAllDecorator($frm);
 	$this->view->frm_search = $frm;
 
 	$key = new Application_Model_DbTable_DbKeycode();
 	$this->view->data=$key->getKeyCodeMiniInv(TRUE);
 }
 
 function agreementAction(){
 	$db = new Application_Model_DbTable_DbAgreement();
	$id =$this->getRequest()->getParam('id');
	$id = empty($id)?0:$id;
	
	$row = $db->getLoanById($id);
	$this->view->loanInfo=$row;
	
	if(empty($row)){
		$inFrame = $this->getRequest()->getParam('inFrame');
		$inFrame = empty($inFrame)?"":$inFrame;
 		Application_Form_FrmMessage::Sucessfull("NO_RECORD",'/report/loan/rpt-loan-disburse?inFrame='.$inFrame,2);
		exit();
	}
	
	$rs = $db->getClientLoanInfo($row['customer_id']);
	$this->view->client =$rs;
	
	$collateral = $db->getLoanCollateral($id);
	$this->view->collateral = $collateral;
	
	$frm = new Application_Form_FrmSearchGlobal();
	$form = $frm->FrmSearchLoadSchedule();
	Application_Model_Decorator::removeAllDecorator($form);
	$this->view->form_filter = $form;
// 	$day_inkhmer = $db->getDayInkhmerBystr(null);
// 	$this->view->day_inkhmer = $day_inkhmer;
	
	$key = new Application_Model_DbTable_DbKeycode();
	$this->view->data=$key->getKeyCodeMiniInv(TRUE);
 }
 function recieptpaymentAction(){
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
 	
 	$frmpopup = new Application_Form_FrmPopupGlobal();
 	$this->view->loanReceipt = $frmpopup->getOfficailReceiptLoan();
 }
 function exchangereceiptAction(){
 	$key = new Application_Model_DbTable_DbKeycode();
 	$this->view->data=$key->getKeyCodeMiniInv(TRUE);
 	$db  = new Report_Model_DbTable_DbLoan();
 	$id =$this->getRequest()->getParam('id');
 	$id = empty($id)?0:$id;
 	$row = $db->getAllxchangeBYID($id);
 	$this->view->Exchange = $row;
 }
 
 function rptClosingentryAction(){
 	$db  = new Report_Model_DbTable_DbLoan();	
	$key = new Application_Model_DbTable_DbKeycode();
	$this->view->data=$key->getKeyCodeMiniInv(TRUE);
	if($this->getRequest()->isPost()){
		$search = $this->getRequest()->getPost();
	}else {
		$search = array(
				'adv_search' => '',
				'status_search' => -1,
				'status' => -1,
				'branch_id' => "",
				'client_name' => "",
				'co_id' => "",
				'start_date'=> date('Y-m-d'),
	  			'end_date'=>date('Y-m-d')
		);
	}
	$this->view->loantotalcollect_list =$rs=$db->getALLLoanPayment($search);
	$this->view->rescheduleFee = $db->getAdminFeeByReschedule($search);
	
	$this->view->list_end_date = $search;
	$frm = new Loan_Form_FrmSearchLoan();
	$frm = $frm->AdvanceSearch();
	Application_Model_Decorator::removeAllDecorator($frm);
	$this->view->frm_search = $frm;
	
	$frmpopup = new Application_Form_FrmPopupGlobal();
	$this->view->footerReport = $frmpopup->getFooterReport();
 }
 function submitentryAction(){
 	$db  = new Report_Model_DbTable_DbLoan();
 	$key = new Application_Model_DbTable_DbKeycode();
 	$this->view->data=$key->getKeyCodeMiniInv(TRUE);
 	if($this->getRequest()->isPost()){
 		$data = $this->getRequest()->getPost();
 		$db->submitClosingEngry($data);
 		Application_Form_FrmMessage::Sucessfull("Closing Entry Success", "/report/loan/rpt-closingentry");
 	}
 }
 
 function rptClosingExpenseAction(){
 	$db = new Report_Model_DbTable_DbRptIncomeExpense();
 	if($this->getRequest()->isPost()){
 		$formdata=$this->getRequest()->getPost();
 	}
 	else{
 		$formdata = array(
 				"adv_search"=>'',
 				"currency_type"=>-1,
 				"category_id"=>"",
 				"status"=>-1,
 				'start_date'=> date('Y-m-d'),
 				'end_date'=>date('Y-m-d'),
 		);
 	}
 	$this->view->rs= $db->getAllExpenseReport($formdata);//call frome model
 	$this->view->list_end_date=$formdata;
 	 
 	$frm = new Loan_Form_FrmSearchLoan();
 	$frm = $frm->AdvanceSearch();
 	Application_Model_Decorator::removeAllDecorator($frm);
 	$this->view->frm_search = $frm;
 	
 	$pructis=new Tellerandexchange_Form_Frmexpense();
 	$frmexp = $pructis->FrmAddExpense();
 	Application_Model_Decorator::removeAllDecorator($frmexp);
 	$this->view->frm_expense=$frmexp;
 	 
 	$frmpopup = new Application_Form_FrmPopupGlobal();
 	$this->view->footerReport = $frmpopup->getFooterReport();
 }
 function closingexpenseAction(){
 	$db  = new Report_Model_DbTable_DbRptIncomeExpense();
 	$key = new Application_Model_DbTable_DbKeycode();
 	$this->view->data=$key->getKeyCodeMiniInv(TRUE);
 	if($this->getRequest()->isPost()){
 		$data = $this->getRequest()->getPost();
 		$db->closingExpense($data);
 		Application_Form_FrmMessage::Sucessfull("Closing Entry Success", "/report/loan/rpt-closing-expense");
 		exit();
 	}
 }
 function rptIncomeAction(){
 	$db = new Report_Model_DbTable_DbRptIncomeExpense();
 	if($this->getRequest()->isPost()){
 		$formdata=$this->getRequest()->getPost();
 	}
 	else{
 		$formdata = array(
 				"adv_search"=>'',
 				"currency_type"=>-1,
 				"category_id"=>"",
 				"status"=>-1,
 				'start_date'=> date('Y-m-d'),
 				'end_date'=>date('Y-m-d'),
 		);
 	}
 	$this->view->rs= $db->getAllIncome($formdata);//call frome model
 	$this->view->list_end_date=$formdata;
 	 
 	$frm = new Loan_Form_FrmSearchLoan();
 	$frm = $frm->AdvanceSearch();
 	Application_Model_Decorator::removeAllDecorator($frm);
 	$this->view->frm_search = $frm;
 	
 	$pructis=new Tellerandexchange_Form_FrmIncome();
 	$frminc = $pructis->FrmAddIncome();
 	Application_Model_Decorator::removeAllDecorator($frminc);
 	$this->view->frm_income=$frminc;
 	 
 	$frmpopup = new Application_Form_FrmPopupGlobal();
 	$this->view->footerReport = $frmpopup->getFooterReport();
 }
 
 function rptClosingIncomeAction(){
 	$db = new Report_Model_DbTable_DbRptIncomeExpense();
 	if($this->getRequest()->isPost()){
 		$formdata=$this->getRequest()->getPost();
 	}
 	else{
 		$formdata = array(
 				"adv_search"=>'',
 				"currency_type"=>-1,
 				"category_id"=>"",
 				"status"=>-1,
 				'start_date'=> date('Y-m-d'),
 				'end_date'=>date('Y-m-d'),
 		);
 	}
 	$this->view->rs= $db->getAllIncome($formdata);//call frome model
 	$this->view->list_end_date=$formdata;
 		
 	$frm = new Loan_Form_FrmSearchLoan();
 	$frm = $frm->AdvanceSearch();
 	Application_Model_Decorator::removeAllDecorator($frm);
 	$this->view->frm_search = $frm;
 	
 	$pructis=new Tellerandexchange_Form_FrmIncome();
 	$frminc = $pructis->FrmAddIncome();
 	Application_Model_Decorator::removeAllDecorator($frminc);
 	$this->view->frm_income=$frminc;
 	
 		
 	$frmpopup = new Application_Form_FrmPopupGlobal();
 	$this->view->footerReport = $frmpopup->getFooterReport();
 }
 function closingincomeAction(){
 	$db  = new Report_Model_DbTable_DbRptIncomeExpense();
 	$key = new Application_Model_DbTable_DbKeycode();
 	$this->view->data=$key->getKeyCodeMiniInv(TRUE);
 	if($this->getRequest()->isPost()){
 		$data = $this->getRequest()->getPost();
 		$db->closingIncome($data);
 		Application_Form_FrmMessage::Sucessfull("Closing Entry Success", "/report/loan/rpt-closing-income");
 		exit();
 	}
 }
 
 function paymenthistoryAction(){
 	$db  = new Report_Model_DbTable_DbLoan();
 	$key = new Application_Model_DbTable_DbKeycode();
 	$this->view->data=$key->getKeyCodeMiniInv(TRUE);
 	
 	$id = $this->getRequest()->getParam('id');
 	$id = empty($id)?0:$id;
 	
 	$rs =$rs=$db->getPaymentByCustomer($id);
 	if (empty($rs)){
		$inFrame = $this->getRequest()->getParam('inFrame');
		$inFrame = empty($inFrame)?"":$inFrame;
			
 		Application_Form_FrmMessage::Sucessfull("NO_RECORD","/loan/index?inFrame=".$inFrame,2);
 		exit();
 	}
 	
 	$this->view->loantotalcollect_list = $rs;
 	$this->view->rescheduleFee = $db->getAdminFeeByCustomerReschedule($id);
 
 	$frmpopup = new Application_Form_FrmPopupGlobal();
 	$this->view->footerReport = $frmpopup->getFooterReport();
 }
 function givereceiveLetterAction(){
 	$db = new Application_Model_DbTable_DbAgreement();
 	$id =$this->getRequest()->getParam('id');
	$id = empty($id)?0:$id;
	
 	$row = $db->getLoanById($id);
 	$this->view->loanInfo=$row;
 	if(empty($row)){
		$inFrame = $this->getRequest()->getParam('inFrame');
		$inFrame = empty($inFrame)?"":$inFrame;
 		Application_Form_FrmMessage::Sucessfull("NO_RECORD",'/report/loan/rpt-loan-disburse?inFrame='.$inFrame,2);
		exit();
	}
 
 	$rs = $db->getClientLoanInfo($row['customer_id']);
 	$this->view->client =$rs;
 
 	$collateral = $db->getLoanCollateral($id);
 	$this->view->collateral = $collateral;
 
 	$frm = new Application_Form_FrmSearchGlobal();
 	$form = $frm->FrmSearchLoadSchedule();
 	Application_Model_Decorator::removeAllDecorator($form);
 	$this->view->form_filter = $form;
 
 	$key = new Application_Model_DbTable_DbKeycode();
 	$this->view->data=$key->getKeyCodeMiniInv(TRUE);
 }
 function givereceiveDocLetterAction(){
 	$db = new Application_Model_DbTable_DbAgreement();
 	$id =$this->getRequest()->getParam('id');
	$id = empty($id)?0:$id;
	
 	$row = $db->getLoanById($id);
 	$this->view->loanInfo=$row;
	
	if(empty($row)){
		$inFrame = $this->getRequest()->getParam('inFrame');
		$inFrame = empty($inFrame)?"":$inFrame;
 		Application_Form_FrmMessage::Sucessfull("NO_RECORD",'/report/loan/rpt-loan-disburse?inFrame='.$inFrame,2);
		exit();
	}
 
 	$rs = $db->getClientLoanInfo($row['customer_id']);
 	$this->view->client =$rs;
 
 	$collateral = $db->getLoanCollateral($id);
 	$this->view->collateral = $collateral;
 
 	$frm = new Application_Form_FrmSearchGlobal();
 	$form = $frm->FrmSearchLoadSchedule();
 	Application_Model_Decorator::removeAllDecorator($form);
 	$this->view->form_filter = $form;
 
 	$key = new Application_Model_DbTable_DbKeycode();
 	$this->view->data=$key->getKeyCodeMiniInv(TRUE);
 }
 
 function comfirmLetterAction(){
 	$db = new Application_Model_DbTable_DbAgreement();
 	$id =$this->getRequest()->getParam('id');
	$id = empty($id)?0:$id;
	
 	$row = $db->getLoanById($id);
 	$this->view->loanInfo=$row;
 	
	if(empty($row)){
		$inFrame = $this->getRequest()->getParam('inFrame');
		$inFrame = empty($inFrame)?"":$inFrame;
 		Application_Form_FrmMessage::Sucessfull("NO_RECORD",'/report/loan/rpt-loan-disburse?inFrame='.$inFrame,2);
		exit();
	}
 
 	$rs = $db->getClientLoanInfo($row['customer_id']);
 	$this->view->client =$rs;
 
 	$collateral = $db->getLoanCollateral($id);
 	$this->view->collateral = $collateral;
 
 	$frm = new Application_Form_FrmSearchGlobal();
 	$form = $frm->FrmSearchLoadSchedule();
 	Application_Model_Decorator::removeAllDecorator($form);
 	$this->view->form_filter = $form;
 
 	$key = new Application_Model_DbTable_DbKeycode();
 	$this->view->data=$key->getKeyCodeMiniInv(TRUE);
 }
}

