<?php
class Installment_ShopController extends Zend_Controller_Action {
	const REDIRECT_URL='/';
	public function init()
	{
		$this->tr=Application_Form_FrmLanguages::getCurrentlanguage();
		/* Initialize action controller here */
		header('content-type: text/html; charset=utf8');
		defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	public function indexAction(){
      try{
    		$db = new Installment_Model_DbTable_DbShop();
	    	if($this->getRequest()->isPost()){
	    		$search=$this->getRequest()->getPost();
	   		 }else{
		   		 $search = array(
		      		'adv_search' => '',
		      		'status_search' => -1);
		  	 }
           	$rs_rows= $db->getAllShop($search);
           
			$list = new Application_Form_Frmtable();
			$collumns = array("SHOPNAME_KH","SHOPNAME_EN","PREFIX_CODE","CODE","ADDRESS","TEL","OTHER","STATUS");
			$link=array(
					      'module'=>'installment','controller'=>'shop','action'=>'edit',
			);
			$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('shop_namekh'=>$link,'shop_nameen'=>$link));
		}catch (Exception $e){
			Application_Form_FrmMessage::message($this->tr->translate("APPLICATION_ERROR"));
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
		$fm = new Installment_Form_Frmshop();
		$frm = $fm->Frmshop();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_branch = $frm;
	}
	
	function addAction()
	{
		$_dbmodel = new Installment_Model_DbTable_DbShop();
		if($this->getRequest()->isPost()){//check condition return true click submit button
			$_data = $this->getRequest()->getPost();
			$_dbmodel = new Installment_Model_DbTable_DbShop();
			try {
				$_dbmodel->addShop($_data);
				if(!empty($_data['save_new'])){
					Application_Form_FrmMessage::Sucessfull($this->tr->translate("INSERT_SUCCESS"),self::REDIRECT_URL . "/installment/shop/add");
				}
				Application_Form_FrmMessage::Sucessfull($this->tr->translate("INSERT_SUCCESS"),self::REDIRECT_URL . "/installment/shop");
			}catch (Exception $e) {
				Application_Form_FrmMessage::message($this->tr->translate("INSERT_FAIL"));
				$err =$e->getMessage();
				Application_Model_DbTable_DbUserLog::writeMessageError($err);
			}
		}
		$fm = new Installment_Form_Frmshop();
		$frm = $fm->Frmshop();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_branch = $frm;
	}
	
	function editAction(){
		$_dbmodel = new Installment_Model_DbTable_DbShop();
		$id=$this->getRequest()->getParam("id");
		if($this->getRequest()->isPost())
		{
			$data = $this->getRequest()->getPost();
			$db = new Installment_Model_DbTable_DbShop();
			try{
				$db->updateBranch($data,$id);
				Application_Form_FrmMessage::Sucessfull($this->tr->translate("EDIT_SUCCESS"),self::REDIRECT_URL."/installment/shop");
			}catch (Exception $e){
				Application_Form_FrmMessage::message($this->tr->translate("EDIT_FAIL"));
				$err=$e->getMessage();
				Application_Model_DbTable_DbUserLog::writeMessageError($err);
			}
		}
		$db=new Installment_Model_DbTable_DbShop();
		$row=$db->getShopbyId($id);
		$this->view->row = $row;
		$frm= new Installment_Form_Frmshop();
		$update=$frm->Frmshop($row);
		$this->view->frm_branch=$update;
		Application_Model_Decorator::removeAllDecorator($update);
	}
	function getshopbyidAction(){
		if($this->getRequest()->isPost()){
			$_data = $this->getRequest()->getPost();
			$db = new Application_Model_DbTable_DbGlobal();
			$Client = $db->getAllShopBranch($_data['branch_id']);
			print_r(Zend_Json::encode($Client));
			exit();
		}
	}
}