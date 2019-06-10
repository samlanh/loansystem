<?php
class Group_ChangecollteralController extends Zend_Controller_Action {
	const REDIRECT_URL='/group/changecollteral';protected $tr;
	public function init()
	{$this->tr=Application_Form_FrmLanguages::getCurrentlanguage();
		/* Initialize action controller here */
		header('content-type: text/html; charset=utf8');
		defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	public function indexAction(){
			try{
				$db = new Group_Model_DbTable_DbChangeCollteral();
		    		if($this->getRequest()->isPost()){
		    			$search=$this->getRequest()->getPost();
		    		}else{
		    			$search = array(
		    					'adv_search' => '',
		    					'loan_number'=>'',
		    					'client_name'=>'',
		    					'status_search' => -1,
		    					'start_date'=>date('Y-m-d'),
		    					'end_date'=>date('Y-m-d'));
		    		}
				$rs_rows= $db->getAllChangeCollteral($search);//call frome model
				$arr=array();
				if(!empty($rs_rows))foreach ($rs_rows as $index =>$rs ){
					$arr[]=array(
						'id'=>$rs['id'],
						'branch_id'=>$rs['branch_id'],
						'loan_number'=>$rs['loan_number'],
						'customer_code'=>$rs['customer_code'],
						'client_name'=>$rs['client_name'],
						'from'=>'from',
						'to'=>'to',
						'date'=>$rs['date'],
						'note'=>$rs['note'],
						'user_id'=>$rs['user_id'],
						'status'=>$rs['status'],
					);
					
					$rows = $db->getColleteralById($rs['id']);
					$from_array='';
					$to_array='';
					if (!empty($rows)){
						foreach($rows as $key =>$row){
		                    if(!empty($rows[$key+1])){
		                   		$to_array.=$row['collateral']. ' ,';
		                        $from_array.=$row['from_collateral']. ' ,';
							}else{
		                       $to_array.=$row['collateral'];
		                       $from_array.=$row['from_collateral'];
							}
						}
					}
					$arr[$index]['from']=$from_array;
					$arr[$index]['to']=$to_array;
				}
			
			$list = new Application_Form_Frmtable();
			$collumns = array("BRANCH_NAME","LOAN_NO","CUSTOMER_CODE","CUSTOMER_NAME","CHANGE_FROM","CHANGE_TO","DATE","NOTE","BY","STATUS");
			$link=array(
					'module'=>'group','controller'=>'changecollteral','action'=>'edit',
			);
			$this->view->list=$list->getCheckList(0, $collumns,$arr,array('branch_id'=>$link,'owner_code_id'=>$link,'owner_id'=>$link));
			$this->view->search = $search;
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
		$fm = new Group_Form_Frmchangecollteral();
		$frm = $fm->FrmChangeCollteral();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_changeCollteral = $frm;
	}
	public function addAction(){
		if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();
			$db = new Group_Model_DbTable_DbChangeCollteral();
			try {
				 $db->addChangeCollteral($data);
				if(!empty($data['save_new'])){
					Application_Form_FrmMessage::Sucessfull('INSERT_SUCCESS', self::REDIRECT_URL . '/add');
				}elseif(!empty($data['save_close'])){
					Application_Form_FrmMessage::Sucessfull('INSERT_SUCCESS', self::REDIRECT_URL . '/index');
				}else{
					Application_Form_FrmMessage::Sucessfull('INSERT_SUCCESS', self::REDIRECT_URL . '/add');
				}
			} catch (Exception $e) { 
				Application_Form_FrmMessage::Sucessfull('INSERT_FAIL', self::REDIRECT_URL . '/add');
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
		
		$dbpop = new Application_Form_FrmPopupGlobal();
		$this->view->frm_popup_callecteral = $dbpop->frmPopupCallecterallType();
		$db = new Application_Model_DbTable_DbGlobal();
		$rs=$db->getCollteralType();
		array_unshift($rs, array ( 'id' => -1,'name' => $this->tr->translate("ADD_NEW")));
		$this->view->call_all= $rs;
	}
	public function editAction()
	{
		$db = new Group_Model_DbTable_DbChangeCollteral();
		if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();
			$db = new Group_Model_DbTable_DbChangeCollteral();
			try {
				$db->updateChangeCollteral($data);
				Application_Form_FrmMessage::Sucessfull('EDIT_SUCCESS', self::REDIRECT_URL . '/index');
			} catch (Exception $e) {
				Application_Form_FrmMessage::Sucessfull('EDIT_FAIL', self::REDIRECT_URL . '/index');
			}
		}
		$id = $this->getRequest()->getParam('id');
		if(empty($id)){
			Application_Form_FrmMessage::Sucessfull($this->tr->translate('RECORD_NOT_EXIST'), self::REDIRECT_URL. '/index');
		}
		$row  = $db->getChangeCollteralbyid($id);
		if(empty($row)){
			Application_Form_FrmMessage::Sucessfull($this->tr->translate('RECORD_NOT_EXIST'), self::REDIRECT_URL. '/index');
		}
		$this->view->row = $row;
		$this->view->rows = $db->getAllCollateralDetailById($id);
		
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
		
		$dbpop = new Application_Form_FrmPopupGlobal();
		$this->view->frm_popup_callecteral = $dbpop->frmPopupCallecterallType();
		$db = new Application_Model_DbTable_DbGlobal();
		$rs=$db->getCollteralType();
		array_unshift($rs, array ( 'id' => -1,'name' => $this->tr->translate("ADD_NEW")));
		$this->view->call_all= $rs;
    }
    public function getOwnerinfoAction(){
    	if($this->getRequest()->isPost()){
    		$data=$this->getRequest()->getPost();
    		$db =new Group_Model_DbTable_DbChangeCollteral();
    		$row=$db->getOwnerInfo($data['client_id']);
    		print_r(Zend_Json::encode($row));
    		exit();
    	}
    }
}
