<?php

class Tellerandexchange_ExpenseController extends Zend_Controller_Action
{
	const REDIRECT_URL = '/tellerandexchange/expense';
	
    public function init()
    {
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
    }
    public function indexAction()
    {
    	try{
    		$db = new Tellerandexchange_Model_DbTable_DbExpense();
    		if($this->getRequest()->isPost()){
    			$formdata=$this->getRequest()->getPost();
    		}
    		else{
    			$formdata = array(
    					"adv_search"=>'',
    					"currency_type"=>-1,
    					"category_id"=>"",
    					"status"=>1,
    					'start_date'=> date('Y-m-d'),
    					'end_date'=>date('Y-m-d'),
    			);
    		}
    		
			$rs_rows= $db->getAllExpense($formdata);//call frome model
    		$list = new Application_Form_Frmtable();
    		$collumns = array("BRANCH_NAME","RECIEPT_NO","INVOICE","CATEGORY","TITLE","CURRENCY","​AMT_PAY","BRANCH_NOTE","DATE","STATUS");
    		$link=array(
    				'module'=>'tellerandexchange','controller'=>'expense','action'=>'edit',
    		);
    		$this->view->list=$list->getCheckList(0, $collumns,$rs_rows,array('branch_name'=>$link,'account_id'=>$link,'reciept_no'=>$link));
    	}catch (Exception $e){
    		Application_Form_FrmMessage::message("Application Error");
    		echo $e->getMessage();
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    	}
    	$frm = new Loan_Form_FrmSearchLoan();
    	$frm = $frm->AdvanceSearch();
    	Application_Model_Decorator::removeAllDecorator($frm);
    	$this->view->frm_search = $frm;
    	
    	$pructis=new Tellerandexchange_Form_Frmexpense();
    	$frmexp = $pructis->FrmAddExpense();
    	Application_Model_Decorator::removeAllDecorator($frmexp);
    	$this->view->frm_expense=$frmexp;
    }
    public function addAction()
    {
    	if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();	
			$db = new Tellerandexchange_Model_DbTable_DbExpense();				
			try {
				$db->addexpense($data);
				if(!empty($data['saveclose'])){
					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/tellerandexchange/expense");
				}else{
					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/tellerandexchange/expense/add");
				}				
				Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/tellerandexchange/expense/add");
			} catch (Exception $e) {
				Application_Form_FrmMessage::message("INSERT_FAIL");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
    	$pructis=new Tellerandexchange_Form_Frmexpense();
    	$frm = $pructis->FrmAddExpense();
    	Application_Model_Decorator::removeAllDecorator($frm);
    	$this->view->frm_expense=$frm;
    }
 
    public function editAction()
    {
    	if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();	
			$db = new Tellerandexchange_Model_DbTable_DbExpense();				
			try {
				$db = $db->updatExpense($data);				
				Application_Form_FrmMessage::Sucessfull('UPDATE_SUCCESS', self::REDIRECT_URL);		
			} catch (Exception $e) {
				Application_Form_FrmMessage::message("UPDATE_FAIL");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		$id = $this->getRequest()->getParam('id');
		$id = empty($id)?0:$id;
		$db = new Tellerandexchange_Model_DbTable_DbExpense();
		$row  = $db->getexpensebyid($id);
		if (empty($row)){
			Application_Form_FrmMessage::Sucessfull('NO_RECORD', self::REDIRECT_URL);
			exit();
		}
		if ($row['is_closed']==1){
			Application_Form_FrmMessage::Sucessfull('Can not delete this record', self::REDIRECT_URL);
			exit();
		}
    	$pructis=new Tellerandexchange_Form_Frmexpense();
    	$frm = $pructis->FrmAddExpense($row);
    	Application_Model_Decorator::removeAllDecorator($frm);
    	$this->view->frm_expense=$frm;
    	
    }
    function getrecieptnoAction(){
    	if($this->getRequest()->isPost()){
    		$data = $this->getRequest()->getPost();
    		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
    		$db = new Tellerandexchange_Model_DbTable_DbExpense();
    		$result = $db->getInvoiceNo($data['branch_id']);
    		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
    		print_r(Zend_Json::encode($result));
    		exit();
    	}
    }
}







