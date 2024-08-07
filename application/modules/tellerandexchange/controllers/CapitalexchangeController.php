<?php

class Tellerandexchange_CapitalexchangeController extends Zend_Controller_Action
{
	const REDIRECT_URL='/tellerandexchange/capitalexchange';
	public function init()
	{
		header('content-type: text/html; charset=utf8');
		defined('BASE_URL') || define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	public function indexAction(){
		try{
			$db = new Tellerandexchange_Model_DbTable_DbCapitalAgent();
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
			}
			else{
				$search = array(
						'agent_id' => '',
						'status' => -1,
						'from_date' =>date('Y-m-d'),
						'to_date' => date('Y-m-d'),
				);
			}
			$this->view->list_search=$search;
			$rs_rows= $db->getCapitalAgency($search);
			
			$list = new Application_Form_Frmtable();
			
			$collumns = array("Agent Name","Currency","Amount","DATE");
			$link=array(
					'module'=>'tellerandexchange','controller'=>'capitalexchange','action'=>'edit',
			);

			$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('agency'=>$link,'currencyKH'=>$link,'amount'=>$link));
			
			$usr_mod = new Application_Model_DbTable_DbUsers();
			
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			echo $e->getMessage();exit();
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
		$this->view->users = $usr_mod->getUserListSelect();
		$frm = new Application_Form_FrmAdvanceSearch();
		$frm = $frm->AdvanceSearch();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_search = $frm;
	}
	public function addAction()
	{
		$db = new Tellerandexchange_Model_DbTable_DbCapitalAgent();
		try {
			if($this->getRequest()->isPost()){
				$data=$this->getRequest()->getPost();
				$db->addCapitalCurrency($data);
				if(!empty($data['save_new'])){
					Application_Form_FrmMessage::Sucessfull('INSERT_SUCCESS', self::REDIRECT_URL . '/add');
				}else{	
					Application_Form_FrmMessage::Sucessfull('INSERT_SUCCESS', self::REDIRECT_URL);
				}
			}
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
		$frm = new Tellerandexchange_Form_FrmCapitalAgent();
		$frm = $frm->FrmCapitalAgent();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm = $frm;
	}
	public function editAction()
	{
		

		$id = $this->getRequest()->getParam('id',0);
		$db = new Tellerandexchange_Model_DbTable_DbCapitalAgent();
		try {
			if($this->getRequest()->isPost()){
				$data=$this->getRequest()->getPost();
				$db->editCapitalCurrency($data);
				$this->_redirect(self::REDIRECT_URL);
					Application_Form_FrmMessage::Sucessfull('EDIT_SUCESS', self::REDIRECT_URL);
			}
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
		$thisrow = $db->getCapitalByID($id);
		
		if (empty($thisrow)){
			Application_Form_FrmMessage::Sucessfull('No Record', self::REDIRECT_URL);
		}
		
		$this->view->capitaldetail = $db->getCapitalByAgentIDAndDate($thisrow);
		$frm = new Tellerandexchange_Form_FrmCapitalAgent();
		$frm = $frm->FrmCapitalAgent($thisrow);
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm = $frm;
		
	}
	function getcurrencyrowAction(){
		if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();
			$db = new Tellerandexchange_Model_DbTable_DbCapitalAgent();
			$rs = $db->getCurrencyRow($data);
			print_r(Zend_Json::encode($rs));
			exit();
		}
	}
	
	function getcurrencyroweditAction(){
		if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();
			$db = new Tellerandexchange_Model_DbTable_DbCapitalAgent();
			$rs = $db->getCurrencyRowEdit($data);
			print_r(Zend_Json::encode($rs));
			exit();
		}
	}

	protected function _helpfilteroption($data){
		$tmp = array();
		foreach ($data as $i =>$d){
			foreach ($d as $key => $val){
				$tmp[$i][$key] = $val;
			}
			$bath=0; $rail=0; $dollar=0;
			if($d['symbol'] === "$"){
				$bath=1; $rail=1;
			}
			elseif($d['symbol'] === "B"){
				$dollar=1; $rail=1;
			}
			elseif($d['symbol'] === "R"){
				$bath=1; $dollar=1;
			}
			$tmp[$i]["dollar"] = $dollar;
			$tmp[$i]["bath"] = $bath;
			$tmp[$i]["rail"] = $rail;
		}
		return $tmp;
	}
	function gettoexchangeAction(){
		if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();
			$db = new Tellerandexchange_Model_DbTable_Dbexchange();
			$rs = $db->getToExchange($data['from_amount_type']);
			print_r(Zend_Json::encode($rs));
			exit();
		}
	}
	function getratevalueAction(){
		if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();
			$db = new Tellerandexchange_Model_DbTable_Dbexchange();
			$rs = $db->getExchangeRateValue($data);
			print_r(Zend_Json::encode($rs));
			exit();
		}
	}
	
	public function withdrawalAction()
	{
		$db = new Tellerandexchange_Model_DbTable_DbCapitalAgent();
		try {
			if($this->getRequest()->isPost()){
				$data=$this->getRequest()->getPost();
				$db->withdrawalcapital($data);
				if(!empty($data['save_new'])){
					Application_Form_FrmMessage::Sucessfull('INSERT_SUCCESS', self::REDIRECT_URL . '/add');
				}else{
					Application_Form_FrmMessage::Sucessfull('INSERT_SUCCESS', self::REDIRECT_URL);
				}
			}
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			echo $e->getMessage();exit();
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
		$frm = new Tellerandexchange_Form_FrmCapitalAgent();
		$frm = $frm->FrmCapitalAgent();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm = $frm;
	}
	function getcurrencyrowwithdrawalAction(){
		if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();
			$db = new Tellerandexchange_Model_DbTable_DbCapitalAgent();
			$rs = $db->getCurrencyRowEditWithdrawal($data);
			print_r(Zend_Json::encode($rs));
			exit();
		}
	}
  }