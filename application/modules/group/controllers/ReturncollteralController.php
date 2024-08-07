<?php
class Group_ReturncollteralController extends Zend_Controller_Action {
	const REDIRECT_URL='/group/returncollteral';protected $tr;
	public function init()
	{$this->tr=Application_Form_FrmLanguages::getCurrentlanguage();
		/* Initialize action controller here */
		header('content-type: text/html; charset=utf8');
		defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	public function indexAction(){
			try{
				$db = new Group_Model_DbTable_DbReturnCollteral();
	    		if($this->getRequest()->isPost()){
	    			$search=$this->getRequest()->getPost();
	    		}
	    		else{
	    			$search = array(
	    					'adv_search' => '',
	    					'status_search' => -1,
	    					'start_date'=>date('Y-m-d'),
	    					'end_date'=>date('Y-m-d'),
	    					'branch_id'=>'');
	    		}
			$rs_rowss= $db->getAllReturnCollteral($search);//call frome model
			$list = new Application_Form_Frmtable();
			$collumns = array("BRANCH_NAME","LOAN_NO","CUSTOMER_CODE","CUSTOMER_NAME","GIVER_NAME","RECEIVER_NAME","DATE","NOTE","BY_USER","STATUS");
			$link=array(
					'module'=>'group','controller'=>'returncollteral','action'=>'edit',
			);
			$this->view->list=$list->getCheckList(0, $collumns,$rs_rowss,array('branch_id'=>$link,'loan_number'=>$link,'client_name'=>$link,'giver_name'=>$link,'receiver_name'=>$link,'date'=>$link));
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
		$fm = new Group_Form_Frmreturncollteral();
		$frm = $fm->FrmReturnCollteral();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_returnCollteral = $frm;
	}
	public function addAction(){
		if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();
			$db = new Group_Model_DbTable_DbReturnCollteral();
			try {
				 $db->addReturnCollteral($data);
				if(!empty($data['save_close'])){
					Application_Form_FrmMessage::Sucessfull('INSERT_SUCCESS', self::REDIRECT_URL . '/index');
				}else{
					Application_Form_FrmMessage::Sucessfull('INSERT_SUCCESS', self::REDIRECT_URL . '/add');
				}
			} catch (Exception $e) { 
				Application_Form_FrmMessage::Sucessfull('INSERT_FAIL', self::REDIRECT_URL . '/index');
			}
		}
		$fm = new Group_Form_Frmchangecollteral();
		$frm = $fm->FrmChangeCollteral();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_changeCollteral = $frm;
		
		$db = new Application_Model_DbTable_DbGlobal();
		$this->view->allclient = $db->getAllClient();
		$this->view->allclient_number = $db->getAllClientNumber();
		$db = new Application_Model_GlobalClass();
		$this->view->collect_option = $db->getCollecteralOption();
		$this->view->owner_type = $db->getCollecteralTypeOption();
		
	}
	public function editAction()
	{
		$db = new Group_Model_DbTable_DbReturnCollteral();
		if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();
		try {
				$db->updateReturnCollteral($data);
				Application_Form_FrmMessage::Sucessfull('EDIT_SUCCESS', self::REDIRECT_URL .'/index');
			} catch (Exception $e) {
				Application_Form_FrmMessage::Sucessfull('EDIT_FAIL', self::REDIRECT_URL . '/index');
			}
		}
		
		$id = $this->getRequest()->getParam('id');
		$id = empty($id) ? 0 : $id;
		$row  = $db->getReturnCollteralbyid($id);
		if(empty($row)){
			Application_Form_FrmMessage::Sucessfull($this->tr->translate('NO_RECORD'), self::REDIRECT_URL,2);
			exit();
		}
		$this->view->row = $row;
		$this->view->rows = $db->getAllReturnCollateralDetail($id);
		
		$fm = new Group_Form_Frmchangecollteral();
		$frm = $fm->FrmChangeCollteral($row);
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_changeCollteral = $frm;
		
		$db = new Application_Model_DbTable_DbGlobal();
		$this->view->allclient = $db->getAllClient();
		$this->view->allclient_number = $db->getAllClientNumber();
		$db = new Application_Model_GlobalClass();
		$this->view->collect_option = $db->getCollecteralOption();
		$this->view->owner_type = $db->getCollecteralTypeOption();
		
    }
    public function getOwnerinfoAction(){
    	if($this->getRequest()->isPost()){
    		$data=$this->getRequest()->getPost();
    		$db =new Group_Model_DbTable_DbChangeCollteral();
    		$row=$db->getOwnerInfo($data['owner_id']);
    		print_r(Zend_Json::encode($row));
    		exit();
    	}
    }
}
