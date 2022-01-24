<?php
class Other_ZoneController extends Zend_Controller_Action {
	const REDIRECT_URL='/other';
	protected $tr;
    public function init()
    {    	
     /* Initialize action controller here */
    	$this->tr=Application_Form_FrmLanguages::getCurrentlanguage();
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	public function indexAction(){
		try{
			$db = new Other_Model_DbTable_DbZone();
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
			}
			else{
				$search = array(
						'adv_search' => '',
						'search_status' => 1);
			}
			$rs_rows= $db->getAllZoneArea($search);
			$list = new Application_Form_Frmtable();
			$collumns = array("BRANCH_NAME","ZONE_NAME","ZONE_NUMBER","DATE","STATUS","BY");
			$link=array(
					'module'=>'other','controller'=>'zone','action'=>'edit',
			);
			$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('branch'=>$link,'zone_name'=>$link,'zone_num'=>$link));
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
			$frm = new Other_Form_FrmZone();
   			$frm_co=$frm->FrmAddZone();
   			Application_Model_Decorator::removeAllDecorator($frm_co);
   			$this->view->frm_zone = $frm_co;
   			$this->view->result = $search;
	}
   function addAction(){
   	if($this->getRequest()->isPost()){
   		try{
   			$_data = $this->getRequest()->getPost();
   			$db = new Other_Model_DbTable_DbZone();
   			$db->addZone($_data);
   			if(!empty($_data['save_close'])){
   				Application_Form_FrmMessage::Sucessfull($this->tr->translate('INSERT_SUCCESS'), self::REDIRECT_URL.'/zone/index');
   			}else{
   				Application_Form_FrmMessage::Sucessfull($this->tr->translate('INSERT_SUCCESS'), self::REDIRECT_URL.'/zone/add');
   			}
   			Application_Form_FrmMessage::Sucessfull($this->tr->translate('INSERT_SUCCESS'), self::REDIRECT_URL.'/zone/add');
   		}catch(Exception $e){
   			Application_Form_FrmMessage::message($this->tr->translate('INSERT_FAIL'));
   			$err =$e->getMessage();
   			Application_Model_DbTable_DbUserLog::writeMessageError($err);
   		}
   	}
   	$frm = new Other_Form_FrmZone();
   	$frm_co=$frm->FrmAddZone();
   	Application_Model_Decorator::removeAllDecorator($frm_co);
   	$this->view->frm_zone = $frm_co;
   }
   function editAction(){
   	$db = new Other_Model_DbTable_DbZone();
	   	if($this->getRequest()->isPost()){
	   		try{
	   			$_data = $this->getRequest()->getPost();
	   			$db->addZone($_data);
	   			Application_Form_FrmMessage::Sucessfull($this->tr->translate('EDIT_SUCCESS'),self::REDIRECT_URL.'/zone/index');
	   		}catch(Exception $e){
	   			Application_Form_FrmMessage::message($this->tr->translate('EDIT_FAIL'));
	   			$err =$e->getMessage();
	   			Application_Model_DbTable_DbUserLog::writeMessageError($err);
	   		}
	   	}
	   	$id=$this->getRequest()->getParam('id');
	   	$row = $db->getZoneById($id);
	   	if(empty($row)){
	   		$this->_redirect('/other/zone');
	   	}
	   	$frm = new Other_Form_FrmZone();
	   	$frm_co=$frm->FrmAddZone($row);
	   	Application_Model_Decorator::removeAllDecorator($frm_co);
	   	$this->view->frm_zone = $frm_co;
   }
   public function addNewzoneAction(){
   	if($this->getRequest()->isPost()){
   		$data = $this->getRequest()->getPost();
   		$data['status']=1;
   		$db_co = new Other_Model_DbTable_DbZone();
   		$id = $db_co->addZone($data);
   		
   		$dbGb = new Application_Model_DbTable_DbGlobal();
   		$rs= $dbGb->getZoneList();
   		array_unshift($rs,array ( 'id' => "",'name' => $this->tr->translate("SELECT_ZONE")), array ( 'id' => -1,'name' => $this->tr->translate("ADD_NEW")));
   		$arr=array(
   				'datastore' =>$rs,
   				'id'		=>$id
   		);
   		print_r(Zend_Json::encode($arr));
   		exit();
   	}
   }
}

