<?php

class Tellerandexchange_CategoryController extends Zend_Controller_Action
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
    		$db = new Tellerandexchange_Model_DbTable_DbCategory();
    		if($this->getRequest()->isPost()){
    			$formdata=$this->getRequest()->getPost();
    		}
    		else{
    			$formdata = array(
    					"adv_search"=>'',
    					"status"=>-1,
    					"type"=>-1
    			);
    		}
    		
			$rs_rows= $db->getAllCategory($formdata);//call frome model
    		$list = new Application_Form_Frmtable();
    		$collumns = array("TITLE","TYPE","NOTE","​STATUS");
    		$link=array(
    				'module'=>'tellerandexchange','controller'=>'category','action'=>'edit',
    		);
    		$this->view->list=$list->getCheckList(0, $collumns,$rs_rows,array('parent'=>$link,'name'=>$link));
    		
    		$this->view->search = $formdata;
    	}catch (Exception $e){
    		Application_Form_FrmMessage::message("Application Error");
    		echo $e->getMessage();
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    	}
    	$frm = new Loan_Form_FrmSearchLoan();
    	$frm = $frm->AdvanceSearch();
    	Application_Model_Decorator::removeAllDecorator($frm);
    	$this->view->frm_search = $frm;
    }
    public function addAction()
    {
    	if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();	
			$db = new Tellerandexchange_Model_DbTable_DbCategory();				
			try {
				$db->addCategory($data);
				if(!empty($data['saveclose'])){
					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/tellerandexchange/category");
				}else{
					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/tellerandexchange/category/add");
				}				
			} catch (Exception $e) {
				Application_Form_FrmMessage::message("INSERT_FAIL");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
    	$pructis=new Tellerandexchange_Form_FrmCategory();
    	$frm = $pructis->FrmAddCategory();
    	Application_Model_Decorator::removeAllDecorator($frm);
    	$this->view->frm_expense=$frm;
    }
 
    public function editAction()
    {
    	$db = new Tellerandexchange_Model_DbTable_DbCategory();
    	if($this->getRequest()->isPost()){
    		$data=$this->getRequest()->getPost();
    		try {
    			$db->updateCategory($data);
    			Application_Form_FrmMessage::Sucessfull("UPDATE_SUCCESS","/tellerandexchange/category");
    		} catch (Exception $e) {
    			Application_Form_FrmMessage::message("UPDATE_FAIL");
    			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    		}
    	}
    	
    	$id = $this->getRequest()->getParam('id');
    	$id = empty($id)?0:$id;
    	$row = $db->getCagetgoryBYID($id);
    	if (empty($row)){
    		Application_Form_FrmMessage::Sucessfull("NO_RECORD","/tellerandexchange/category",2);
    		exit();
    	}
    	$this->view->row = $row;
    	$pructis=new Tellerandexchange_Form_FrmCategory();
    	$frm = $pructis->FrmAddCategory($row);
    	Application_Model_Decorator::removeAllDecorator($frm);
    	$this->view->frm_expense=$frm;
    }
    function getParentAction(){
    	if($this->getRequest()->isPost()){
    		$data = $this->getRequest()->getPost();
    		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
    		$db = new Tellerandexchange_Model_DbTable_DbCategory();
    		$idcategory = empty($data['idcategory'])?0:$data['idcategory'];
    		$result = $db->getAllIncomeCategory($data['type'],$idcategory);
    		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
    		array_unshift($result, array ( 'id' => 0,'name' => $tr->translate("SELECT_PARENT")));
    	
    		print_r(Zend_Json::encode($result));
    		exit();
    	}
    }
    
}







