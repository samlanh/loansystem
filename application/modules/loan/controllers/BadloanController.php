<?php
class Loan_BadloanController extends Zend_Controller_Action {
	
	public function init()
	{
		/* Initialize action controller here */
		header('content-type: text/html; charset=utf8');
		defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
		$this->tr = Application_Form_FrmLanguages::getCurrentlanguage();
	}
	public function indexAction()
	{
		try{
			$db = new Loan_Model_DbTable_DbBadloan();
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
			}
			else{
				$search = array(
					    'adv_search'=>'',
						'branch' => '',
						'client_name' =>'',
						'client_code'=>'',
						'Term'=>'',
						'status' =>'',
						'cash_type'=>'',
						'start_date'=> date('Y-m-01'),
						'end_date'=>date('Y-m-d'));
			}
			$rs_row= $db->getAllBadloan($search);//call frome model
			$list = new Application_Form_Frmtable();
			$collumns = array("BRANCH_NAME","LOAN_NO","CUSTOMER_NAME","LOSS_DATE"
					,"TOTAL_PRINCEPLE","INTERREST_AMOUNT","TERM","NOTE","DATE","STATUS");
			$link=array(
					'module'=>'loan','controller'=>'badloan','action'=>'edit',
			);
			$this->view->list=$list->getCheckList(0, $collumns,$rs_row,array('branch_namekh'=>$link,'loan_number'=>$link,'client_name_en'=>$link,
					'total_amount'=>$link,'intrest_amount'=>$link,'loss_date'=>$link));
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
		$fm = new Loan_Form_Frmbadloan();
		$frm = $fm->FrmBadLoan();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_loan = $frm;
	}
	
	public function addAction(){
		if($this->getRequest()->isPost()){//check condition return true click submit button
			$_data = $this->getRequest()->getPost();
			try {		
				$_dbmodel = new Loan_Model_DbTable_DbBadloan();
				$_dbmodel->addbadloan($_data);
				if(isset($_data['save'])){
					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/loan/badloan/add");
				}elseif(isset($_data['save_close'])){
					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/loan/badloan/");
				}				
			}catch (Exception $e) {
				Application_Form_FrmMessage::message("INSERT_FAIL");
				$err =$e->getMessage();
				Application_Model_DbTable_DbUserLog::writeMessageError($err);
			}
		}
		$fm = new Loan_Form_Frmbadloan();
		$frm = $fm->FrmBadLoan();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_loan = $frm;
		
		$db = new Application_Model_DbTable_DbGlobal();
		$this->view->allclient = $db->getAllClient();
		$this->view->allclient_number = $db->getAllClientNumber();
		
		$id = $this->getRequest()->getParam('id');
		$id = empty($id) ? 0 : $id;
		if(!empty($id)){
			if(empty($id)){
				$id=0;
			}
			$this->view->rsid=$id;
			$db = new Loan_Model_DbTable_DbLoandisburse();
			$rs = $db->getTranLoanByIdWithBranch($id,1);
			if(empty($rs)){
				Application_Form_FrmMessage::Sucessfull("NO_RECORD","/loan/index/index",2);
			}
			
			$this->view->rsloan =  $rs;
		}
	}
	
	public function editAction()
	{
		$id=$this->getRequest()->getParam('id');
		$_dbmodel = new Loan_Model_DbTable_DbBadloan();
		if($this->getRequest()->isPost()){//check condition return true click submit button
			$_data = $this->getRequest()->getPost();
			try {
				$_dbmodel->updatebadloan($_data);
				Application_Form_FrmMessage::Sucessfull("EDIT_SUCCESS","/loan/badloan/");
			}catch (Exception $e) {
				Application_Form_FrmMessage::message("EDIT_FAIL");
				$err =$e->getMessage();
				Application_Model_DbTable_DbUserLog::writeMessageError($err);
			}
		}
		$row=$_dbmodel->getLoanedit($id);
		if (empty($row)){
			Application_Form_FrmMessage::Sucessfull("NO_RECORD","/loan/badloan/",2);
			exit();
		}
		$this->view->row = $row;
		$fm = new Loan_Form_Frmbadloan();
		$frm = $fm->FrmBadLoan($row);
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_loan = $frm;
	
		$db = new Application_Model_DbTable_DbGlobal();
		$this->view->allclient = $db->getAllClient();
		$this->view->allclient_number = $db->getAllClientNumber();
	}
	
	public function oldeditAction()
	{
		// action body
	if($this->getRequest()->isPost()){//check condition return true click submit button
			$_data = $this->getRequest()->getPost();
			try {
				//print_r($_data);exit();
				$_dbmodel = new Loan_Model_DbTable_DbBadloan();
				if(isset($_data['save'])){
					if($this->getRequest()->getParam('id')==$_data['client_name']){
						$_dbmodel->updatebadloan($_data);
					}else{
						$_dbmodel->updatebadloan_bad($_data);
						
					}
				}elseif(isset($_data['save_close'])){
					if($this->getRequest()->getParam('id')==$_data['client_name']){
						$_dbmodel->updatebadloan_bad($_data);
					}else{
						$_dbmodel->updatebadloan_bad($_data);
					}
				}
				Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/loan/badloan");
			}catch (Exception $e) {
				Application_Form_FrmMessage::message("INSERT_FAIL");
				$err =$e->getMessage();
				Application_Model_DbTable_DbUserLog::writeMessageError($err);
			}
	      }
		$id = $this->getRequest()->getParam('id');
		// 		if(empty($id)){
		// 			Application_Form_FrmMessage::Sucessfull('ការ​បញ្ចូល​​ជោគ​ជ័យ', self::REDIRECT_URL);
		// 		}
		$db = new Loan_Model_DbTable_DbBadloan();
		$row  = $db->getbadloanbyid($id);
		$fm = new Loan_Form_Frmbadloan();
		$frm = $fm->FrmBadLoan($row);
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_loan = $frm;
		$db = new Application_Model_DbTable_DbGlobal();
		$this->view->allclient = $db->getAllClient();
		$this->view->allclient_number = $db->getAllClientNumber();
		 
	}
	public function getloaninfoAction(){
		if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();
			$db=new Loan_Model_DbTable_DbBadloan();
			$row=$db->getLoanInfo($data['loan_id']);
			print_r(Zend_Json::encode($row));
			exit();
		}
	}
	
	public function getloaninfoByLoanidAction(){
		if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();
			$db=new Loan_Model_DbTable_DbBadloan();
			$row=$db->getLoanInfoByNumberLoanId($data['loan_id']);
			print_r(Zend_Json::encode($row));
			exit();
		}
	}
	public function getloaninfoByLoanideditAction(){
		if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();
			$db=new Loan_Model_DbTable_DbBadloan();
			$row=$db->getLoanInfoByNumberLoanIdEdit($data['loan_id']);
			print_r(Zend_Json::encode($row));
			exit();
		}
	}
}

