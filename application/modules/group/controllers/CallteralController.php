<?php
class Group_CallteralController extends Zend_Controller_Action {
	const REDIRECT_URL='/group';
	protected $tr;
	public function init()
	{
		$this->tr=Application_Form_FrmLanguages::getCurrentlanguage();
		/* Initialize action controller here */
		header('content-type: text/html; charset=utf8');
		defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	public function indexAction(){
			try{
				$db = new Group_Model_DbTable_DbCallteral();
			    	if($this->getRequest()->isPost()){
			    		$search=$this->getRequest()->getPost();
			    	}
			    	else{
			    			$search = array(
			    					'adv_search' 	=> '',
			    					'status_search' => -1,
			    					'client_name'	=>'',
			    					'start_date'	=> date('Y-m-d'),
									'end_date'		=>date('Y-m-d'));
			    	}
			$rs_rows= $db->geteAllcallteral($search);//call frome model
			
			$list = new Application_Form_Frmtable();
			$collumns = array("BRANCH_NAME","LOAN_NO","CLIENT_NO","CUSTOMER_NAME","COLLTERAL_CODE","STAFF_NAME","AND_NAME","RELATIVE_WITH","DATE","STATUS");
			$link=array(
					'module'=>'group','controller'=>'callteral','action'=>'edit',
			);
			$this->view->list=$list->getCheckList(0, $collumns,$rs_rows,array('branch_name'=>$link,'loan_number'=>$link,'collecteral_code'=>$link,'client_code'=>$link,'name_kh'=>$link));
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
		$fm=new Group_Form_Frmcallterals();
		$frm=$fm->FrmCallTeral();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_callteral=$frm;
	}
	public function addAction(){
		$db = new Group_Model_DbTable_DbClient();
		$id = $this->getRequest()->getParam("id");
		$row = $db->getClientById($id);
		if(!empty($row)){
		$row['co_id']='';
		$row['guarantor']=$row['spouse_name'];
		$row['relative']=$row['relate_with'];
		$row['guarantor_relative']=$row['guarantor_with']; 
		$row['note']=$row['remark']; 
		$row['id']='';
		$code = Group_Model_DbTable_DbCallteral::getCallteralCode();
			$row['collecteral_code']=$code;
		}
		if($this->getRequest()->isPost()){
			$calldata=$this->getRequest()->getPost();
			$db_call = new Group_Model_DbTable_DbCallteral();
			try {
				$db = $db_call->addcallteral($calldata);
				if(!empty($calldata['save_close'])){
					Application_Form_FrmMessage::Sucessfull('INSERT_SUCCESS', self::REDIRECT_URL . '/callteral/index');
				}else{
					Application_Form_FrmMessage::Sucessfull('INSERT_SUCCESS', self::REDIRECT_URL . '/callteral/add');
				}
			} catch (Exception $e) { 
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
				Application_Form_FrmMessage::message("Application Error");
				
			}
		}
		$fm = new Group_Form_Frmcallterals();
		$frm = $fm->FrmCallTeral($row);
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_callteral = $frm;
		$this->view->row=$row;
		
		$db = new Application_Model_DbTable_DbGlobal();
		
		$db = new Application_Model_GlobalClass();
		$this->view->collect_option = $db->getCollecteralOption();
		$this->view->owner_type = $db->getCollecteralTypeOption();
		
		$dbpop = new Application_Form_FrmPopupGlobal();
		$this->view->frm_popup_callecteral = $dbpop->frmPopupCallecterallType();
		$db = new Application_Model_DbTable_DbGlobal();
		$rs=$db->getCollteralType();
		array_unshift($rs, array ( 'id' => -1,'name' => $this->tr->translate("ADD_NEW")));
		$this->view->call_all= $rs;
		
	}
	
	public function editAction()
	{
		if($this->getRequest()->isPost()){
				$calldata=$this->getRequest()->getPost();
				$db_call = new Group_Model_DbTable_DbCallteral();
				try{
					$db = $db_call->updatecallteral($calldata);
					Application_Form_FrmMessage::Sucessfull('EDIT_SUCCESS', self::REDIRECT_URL. '/callteral/index');
				} catch (Exception $e) {
					Application_Form_FrmMessage::message("INSERT_FAIL");
				    Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
				}
		}
		$id = $this->getRequest()->getParam('id');
		
		$db = new Group_Model_DbTable_DbCallteral();
		$row  = $db->getecallteralbyid($id);
		if(empty($id) OR empty($row) ){
			Application_Form_FrmMessage::Sucessfull('NO_RECORD', self::REDIRECT_URL. '/callteral/index',2);
			exit();
		}
		
		$this->view->client_id = $row['client_id'];
		$this->view->branch_id = $row['branch_id'];
		$this->view->loan_id = $row['loan_id'];
		$this->view->rows = $db->getCallecteralDetailById($id);
		
		$fm = new Group_Form_Frmcallterals();
		$frm = $fm->FrmCallTeral($row);
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_callteral = $frm;
		
		$db = new Application_Model_DbTable_DbGlobal();
		
		$db = new Application_Model_GlobalClass();
		$this->view->collect_option = $db->getCollecteralOption();
		$this->view->owner_type = $db->getCollecteralTypeOption();
		
		$dbpop = new Application_Form_FrmPopupGlobal();
		$this->view->frm_popup_callecteral = $dbpop->frmPopupCallecterallType();
		$db = new Application_Model_DbTable_DbGlobal();
		$rs=$db->getCollteralType();
		array_unshift($rs, array ( 'id' => -1,'name' => $this->tr->translate("ADD_NEW")));
		$this->view->call_all= $rs;
    }

    function getclientcollateralbyloanAction(){//At callecteral when click client
    	if($this->getRequest()->isPost()){
    		$db = new Group_Model_DbTable_DbCallteral();
    		$data = $this->getRequest()->getPost();
    		$code = $db->getClientCallateralBYLoanId($data['loan_id']);
    		print_r(Zend_Json::encode($code));
    		exit();
    	}
    }
}