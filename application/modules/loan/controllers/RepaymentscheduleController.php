<?php
class Loan_RepaymentScheduleController extends Zend_Controller_Action {
    public function init()
    {    	
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	public function indexAction(){
		try{
		    if($this->getRequest()->isPost()){
 				$search = $this->getRequest()->getPost();
 				
 			}else{
				$search = array(
						'txt_search'=>'',
						'client_name'=> -1,
						'repayment_method' => -1,
						'branch_id' => -1,
						'co_id' => -1,
						'status' => -1,
						'currency_type'=>-1,
						'pay_every'=>-1,
						'start_date'=> date('Y-m-d'),
						'end_date'=>date('Y-m-d'),
						 );
			}
			$db = new Loan_Model_DbTable_DbLoanIL();
			$rs_rows= $db->getAllRescheduleLoan($search,1);
			
			$list = new Application_Form_Frmtable();
			$collumns = array("BRANCH_NAME","LOAN_NO","CUSTOMER_NAME","LOAN_AMOUNT","INTEREST_RATE","REPAYMENT_TYPE","TERM_BORROW","ZONE_NAME",
					"CO_NAME","RELEASE_DATE","STATUS");
			$link=array(
					'module'=>'loan','controller'=>'repaymentschedule','action'=>'view',
			);
			$link_info=array('module'=>'loan','controller'=>'repaymentschedule','action'=>'edit',);
			$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array(),0);
			//array('loan_number'=>$link,'payment_method'=>$link_info,'client_name_kh'=>$link_info,'client_name_en'=>$link_info,'total_capital'=>$link_info)
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}	
		$frm = new Loan_Form_FrmSearchLoan();
		$frm = $frm->AdvanceSearch();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_search = $frm;
  }
  
  function addAction()
  {
//   	$db = new Loan_Model_DbTable_DbRepaymentSchedule();
//   	print_r($db->getLoanInfoBymemberId(12,1));exit();
  	
		if($this->getRequest()->isPost()){
			$_data = $this->getRequest()->getPost();
			try {
				$_dbmodel = new Loan_Model_DbTable_DbRepaymentSchedule();
				$_dbmodel->addRepayMentSchedule($_data);
				
				if(!empty($_data["inFrame"])){
					$loanId = empty($_data["get_laonnumber"]) ? 0 : $_data["get_laonnumber"];
					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS", "/report/loan/rpt-paymentschedules/id/".$loanId."?inFrame=true");
				}else{
					if(!empty($_data['saveclose'])){
						Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/loan/repaymentschedule");
					}else{
						Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/loan/repaymentschedule/add");
					}
				}
				
				
			}catch (Exception $e) {
				Application_Form_FrmMessage::message("INSERT_FAIL");
				$err =$e->getMessage();
				Application_Model_DbTable_DbUserLog::writeMessageError($err);
			}
		}
		$frm = new Loan_Form_FrmLoan();
		$frm_loan=$frm->FrmAddLoan();
		Application_Model_Decorator::removeAllDecorator($frm_loan);
		$this->view->frm_loan = $frm_loan;
        $db = new Application_Model_DbTable_DbGlobal();
        $this->view->allclient = $db->getAllClient();
        $this->view->allclient_number = $db->getAllClientNumber();
        $frmpopup = new Application_Form_FrmPopupGlobal();
        $this->view->frmpupoploantype = $frmpopup->frmPopupLoanTye();
        $this->view->frmPopupZone = $frmpopup->frmPopupZone();
        
        $db_keycode = new Application_Model_DbTable_DbKeycode();
        $this->view->keycode = $db_keycode->getKeyCodeMiniInv();
        
        $this->view->graiceperiod = $db_keycode->getSystemSetting(9);
        
		$db = new Setting_Model_DbTable_DbLabel();
		$this->view->setting=$db->getAllSystemSetting();
		
		$id = $this->getRequest()->getParam('id');
		
		$inFrame = $this->getRequest()->getParam('inFrame');
		$inFrame = empty($inFrame)?"":$inFrame;
		
		if(!empty($id)){
			if(empty($id)){
				$id=0;
			}
			$this->view->rsid=$id;
			$db = new Loan_Model_DbTable_DbLoandisburse();
			$rs = $db->getTranLoanByIdWithBranch($id,1);
			if(empty($rs)){
				Application_Form_FrmMessage::Sucessfull("NO_RECORD","/loan/index/index?inFrame=".$inFrame,2);
			}
			$this->view->rsloan = $rs;
		}
		$this->view->inFrame = $inFrame;
	}	
// 	public function addloanAction(){
// 		if($this->getRequest()->isPost()){
// 			$data=$this->getRequest()->getPost();
// 			$db = new Loan_Model_DbTable_DbRepaymentSchedule();
// 			$id = $db->addNewLoanGroup($data);
// 			$suc = array('sms'=>'ប្រាក់ឥណទានត្រូវបានបញ្ចូលដោយជោគជ័យ !');
// 			print_r(Zend_Json::encode($suc));
// 			exit();
// 		}
// 	}
	public function viewAction(){
		// 		$this->_helper->layout()->disableLayout();
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
	
	public function editAction(){
		if($this->getRequest()->isPost()){
			$_data = $this->getRequest()->getPost();
			try{
				$_dbmodel = new Loan_Model_DbTable_DbRepaymentSchedule();
				$_dbmodel->updateRepaymentSchedule($_data);
				Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/loan/repaymentschedule/index");
			}catch (Exception $e) {
				Application_Form_FrmMessage::message("INSERT_FAIL");
				Application_Model_DbTable_DbUserLog::writeMessageError($err =$e->getMessage());
			}
		}
		
		$id = $this->getRequest()->getParam('id');
		$id = empty($id) ? 0 : $id;
		
		$db_g = new Application_Model_DbTable_DbGlobal();
		$rs = $db_g->getLoanFundExist($id);
		if($rs==true){
			Application_Form_FrmMessage::Sucessfull("LOAN_FUND_EXIST","/loan/repaymentschedule/index");
		}
		
		$db = new Loan_Model_DbTable_DbLoanIL();
		$row = $db->getTranLoanByIdWithBranch($id,1,1);
// 		if(empty($row)){ Application_Form_FrmMessage::Sucessfull("NO_RECORD","/loan/repaymentschedule/index",2); }
		
		$frm = new Loan_Form_FrmLoan();
		$frm_loan=$frm->FrmAddLoan($row);
		Application_Model_Decorator::removeAllDecorator($frm_loan);
		$this->view->frm_loan = $frm_loan;
		
		$db = new Application_Model_DbTable_DbGlobal();
		$this->view->allclient = $db->getAllClient();
		$this->view->allclient_number = $db->getAllClientNumber();
		$this->view->datarow = $row;
	}
	
	function getloanRescheduleAction(){
		if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();
			$db=new Loan_Model_DbTable_DbRepaymentSchedule();
			$row=$db->getLaoForRepaymentSchedule($data['loan_id']);
			print_r(Zend_Json::encode($row));
			exit();
		}
	}
}

