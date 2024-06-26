<?php
class tellerandexchange_indexController extends Zend_Controller_Action {
	private $activelist = array('មិនប្រើប្រាស់', 'ប្រើប្រាស់');
const REDIRECT_URL='/tellerandexchange/index';
	public function init()
	{
		header('content-type: text/html; charset=utf8');
		defined('BASE_URL') || define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	public function indexAction(){
		try{
			$db_tran=new Application_Model_DbTable_DbGlobal();
			
			$session_transfer=new Zend_Session_Namespace('search_xhcange');
			$db = new Tellerandexchange_Model_DbTable_Dbexchange();
			$session_user=new Zend_Session_Namespace(SYSTEM_SES);
			$user_id = $session_user->user_id;
		
			
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
			}
			else{
				$search = array(
						'user_id' => -1,
						'status' => -1,
						'from_date' =>date('Y-m-d'),
						'to_date' => date('Y-m-d'),
				);
			}
			$this->view->list_search=$search;
// 			$rs_row= $db->getAllExchangeListMulti($search);
			$rs_row= $db->getAllSigleExchange($search);
			
			$glClass = new Application_Model_GlobalClass();
			$rs_rows = $glClass->getImgActive($rs_row, BASE_URL, true);
			$list = new Application_Form_Frmtable();
			
			$collumns = array("DATE","Exchage To","Exchage Amount","Exchange Rate","Exchage Amount Received","RECIEVED_AMOUNT","RETURN_AMOUNT","STATUS","RECEIPT");
			$link=array(
					'module'=>'tellerandexchange','controller'=>'index','action'=>'edit',
			);
			$linkXcreceipt=array(
					'module'=>'report','controller'=>'loan','action'=>'exchangereceipt',
			);
			$tr = Application_Form_FrmLanguages::getCurrentlanguage();
			$exchange=$tr->translate("Exchange Receipt");
			$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('statusDate'=>$link,'from_to'=>$link,'fromAmount'=>$link,$exchange=>$linkXcreceipt));
		  
			$usr_mod = new Application_Model_DbTable_DbUsers();
			$this->view->users = $usr_mod->getUserListSelect();
			$this->view->user_id = $user_id;
			//$this->view->from_date=$session_transfer->from_date;
			//$this->view->to_date=$session_transfer->to_date;
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			echo $e->getMessage();exit();
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
		$frm = new Application_Form_FrmAdvanceSearch();
		$frm = $frm->AdvanceSearch();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_search = $frm;
	}
	public function addAction()
	{
	  // action body       
        //user name for report
        $session_user=new Zend_Session_Namespace(SYSTEM_SES);
        $this->view->user_name = $session_user->last_name .' '. $session_user->first_name;
        
        $db_keycode = new Application_Model_DbTable_DbKeycode();
		$this->view->keycode = $db_keycode->getKeyCodeMiniInv();
		
		$cur = new Application_Model_DbTable_DbCurrencies();
		$currency = $cur->getCurrencyList();
		
		$this->view->currency = $this->_helpfilteroption($currency);
		
		$this->view->inv_no = Application_Model_GlobalClass::getInvoiceNo();
		
		if($this->getRequest()->isPost()){
			$formdata=$this->getRequest()->getPost();	
			$db_exc=new Tellerandexchange_Model_DbTable_DbxChangeMoney();	
			try {
				$id = $db_exc->save($formdata);
				Application_Form_FrmMessage::Sucessfull('ការបញ្ចូលជោគជ័យ', self::REDIRECT_URL . '/add');
			} catch (Exception $e) {
				$this->view->msg = 'ការបញ្ចូលមិនជោគជ័យ';
			}
		}
		$dbEx = new Tellerandexchange_Model_DbTable_Dbexchange();
		$this->view->allExchangeRate = $dbEx->getAllExchangeRate();
	}
// 	public function editAction()
	
//  	{
 		
 		
 		
//  		if($this->getRequest()->isPost()){
//  			$formdata=$this->getRequest()->getPost();
//  			//print_r($formdata);exit();
//  			$db_exc=new Tellerandexchange_Model_DbTable_DbxChangeMoney();
//  			try {
//  				$id = $db_exc->save($formdata);
//  				Application_Form_FrmMessage::Sucessfull('ការបញ្ចូលជោគជ័យ', self::REDIRECT_URL . '/index/add');
//  			} catch (Exception $e) {
//  				$this->view->msg = 'ការបញ្ចូលមិនជោគជ័យ';
//  			}
//  		}
 		
//  		$session_user=new Zend_Session_Namespace(SYSTEM_SES);
//  		$this->view->user_name = $session_user->last_name .' '. $session_user->first_name;
 			
//  		$db_keycode = new Application_Model_DbTable_DbKeycode();
//  		$this->view->keycode = $db_keycode->getKeyCodeMiniInv();
 			
//  		$cur = new Application_Model_DbTable_DbCurrencies();
//  		$currency = $cur->getCurrencyList();
 			
//  		$this->view->currency = $this->_helpfilteroption($currency);
 			
//  		$this->view->inv_no = Application_Model_GlobalClass::getInvoiceNo();
 		
//  		$id = $this->getRequest()->getParam('id');
//  		$db_exc=new Tellerandexchange_Model_DbTable_Dbexchange();
//  		$rs=$db_exc->getxchangById($id);
//  		$this->view->rs=$rs;
//  	print_r($rs);
//  }

 
 public function editAction()
 {
 	$id = $this->getRequest()->getParam('id',0);
 	$db_exc=new Tellerandexchange_Model_DbTable_DbxChangeMoney();
 	if($this->getRequest()->isPost()){
 		$formdata=$this->getRequest()->getPost();
 		
 		try {
//  			$formdata['id']=$id;
 				$id = $db_exc->editExchange($formdata);
				Application_Form_FrmMessage::Sucessfull("EDIT_SUCESS",self::REDIRECT_URL);
 		} catch (Exception $e) {
 			$this->view->msg = 'ការបញ្ចូលមិនជោគជ័យ';
 			echo $e->getMessage();exit();
 		}
 	}
 	// action body
 	//Get value from url
 	
 	$session_user=new Zend_Session_Namespace(SYSTEM_SES);
 	$this->view->user_name = $session_user->last_name .' '. $session_user->first_name;
 	
 	$db_keycode = new Application_Model_DbTable_DbKeycode();
 	$this->view->keycode = $db_keycode->getKeyCodeMiniInv();
 	
 	$cur = new Application_Model_DbTable_DbCurrencies();
 	$currency = $cur->getCurrencyList();
 	
 	$this->view->currency = $this->_helpfilteroption($currency);
//  	$this->view->inv_no = Application_Model_GlobalClass::getInvoiceNo();
 	
 	$rs=$db_exc->getxchangById($id);
//  	print_r($rs);

 	$this->view->dataedit=$rs;
 	
 	$dbEx = new Tellerandexchange_Model_DbTable_Dbexchange();
 	$this->view->allExchangeRate = $dbEx->getAllExchangeRate();
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
}

