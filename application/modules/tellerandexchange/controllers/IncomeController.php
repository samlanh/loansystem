<?php

class Tellerandexchange_IncomeController extends Zend_Controller_Action
{
	const REDIRECT_URL = '/tellerandexchange/income';
	
    public function init()
    {
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
    }

    public function indexAction()
    {
    	try{
    		$db = new Tellerandexchange_Model_DbTable_DbIncome();
    		if($this->getRequest()->isPost()){
    			$formdata=$this->getRequest()->getPost();
    		}
    		else{
    			$formdata = array(
    					"adv_search"=>'',
    					"currency_type"=>-1,
    					'status'=>1,
    					"category_id"=>"",
    					'start_date'=> date('Y-m-d'),
    					'end_date'=>date('Y-m-d'),
    			);
    		}
    		
			$rs_rows= $db->getAllIncome($formdata);//call frome model
    		
    		$list = new Application_Form_Frmtable();
    		$collumns = array("BRANCH_NAME","RECIEPT_NO","INVOICE","CATEGORY","TITLE","CURRENCY","​AMT_PAY","NOTE","DATE","STATUS");
    		$link=array(
    				'module'=>'tellerandexchange','controller'=>'income','action'=>'edit',
    		);
    		$this->view->list=$list->getCheckList(0, $collumns,$rs_rows,array('branch_name'=>$link,'account_id'=>$link,'reciept_no'=>$link));
    	}catch (Exception $e){
    		Application_Form_FrmMessage::message("Application Error");
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    	}
    	$frm = new Loan_Form_FrmSearchLoan();
    	$frm = $frm->AdvanceSearch();
    	Application_Model_Decorator::removeAllDecorator($frm);
    	$this->view->frm_search = $frm;
    	
    	$pructis=new Tellerandexchange_Form_FrmIncome();
    	$frm = $pructis->FrmAddIncome();
    	Application_Model_Decorator::removeAllDecorator($frm);
    	$this->view->frm_income=$frm;
    }
    public function addAction()
    {
    	if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();	
			$db = new Tellerandexchange_Model_DbTable_DbIncome();				
			try {
				$db->addIncome($data);
				if(!empty($data['saveclose'])){
					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS",self::REDIRECT_URL);
				}else{
					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS",self::REDIRECT_URL."/add");
				}
				Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS",self::REDIRECT_URL."/add");
			} catch (Exception $e) {
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
				Application_Form_FrmMessage::message("INSERT_FAIL");
			}
		}
    	$pructis=new Tellerandexchange_Form_FrmIncome();
    	$frm = $pructis->FrmAddIncome();
    	Application_Model_Decorator::removeAllDecorator($frm);
    	$this->view->frm_expense=$frm;
    }
 
    public function editAction()
    {
    	$db = new Tellerandexchange_Model_DbTable_DbIncome();
    	if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();	
			try {
				$db->updatIncome($data);				
				Application_Form_FrmMessage::Sucessfull("UPDATE_SUCCESS",self::REDIRECT_URL);	
			} catch (Exception $e) {
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
				Application_Form_FrmMessage::message("UPDATE_FAIL");
			}
		}
		$id = $this->getRequest()->getParam('id');
		$id = empty($id)?0:$id;
		$row  = $db->getexpensebyid($id);
		if (empty($row)){
			Application_Form_FrmMessage::Sucessfull('NO_RECORD', self::REDIRECT_URL);
			exit();
		}
		if ($row['is_closed']==1){
			Application_Form_FrmMessage::Sucessfull('Can not delete this record', self::REDIRECT_URL);
			exit();
		}
    	$pructis=new Tellerandexchange_Form_FrmIncome();
    	$frm = $pructis->FrmAddIncome($row);
    	Application_Model_Decorator::removeAllDecorator($frm);
    	$this->view->frm_expense=$frm;
    	
    }
    
    function getrecieptnoAction(){
    	if($this->getRequest()->isPost()){
    		$data = $this->getRequest()->getPost();
    		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
    		$db = new Tellerandexchange_Model_DbTable_DbIncome();
    		$result = $db->getInvoiceNo($data['branch_id']);
    		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
    		print_r(Zend_Json::encode($result));
    		exit();
    	}
    }

}







