<?php
class Other_BranchController extends Zend_Controller_Action {
	const REDIRECT_URL='/other';
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
    		$db = new Other_Model_DbTable_DbBranch();
	    	if($this->getRequest()->isPost()){
	    		$search=$this->getRequest()->getPost();
	   		 }else{
		   		 $search = array(
		      		'adv_search' => '',
		      		'status_search' => -1);
		  	 }
           	$rs_rows= $db->getAllBranch($search);
           
			$list = new Application_Form_Frmtable();
			$collumns = array("BRANCH","SHOPNAME","PREFIX_CODE","CODE","ADDRESS","TEL","OTHER","STATUS");
			$link=array(
					      'module'=>'other','controller'=>'branch','action'=>'edit',
			);
			$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('branch_namekh'=>$link,'branch_nameen'=>$link));
		}catch (Exception $e){
			Application_Form_FrmMessage::message($this->tr->translate("APPLICATION_ERROR"));
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
		$fm = new Other_Form_Frmbranch();
		$frm = $fm->Frmbranch();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_branch = $frm;
  
	}
	
	function addAction()
	{
		$_dbmodel = new Other_Model_DbTable_DbBranch();
		$allbranch = $_dbmodel->countBranch();
		if ($allbranch>=3){
			$this->_redirect("/other/branch");
		}
		if($this->getRequest()->isPost()){//check condition return true click submit button
			$_data = $this->getRequest()->getPost();
			$_dbmodel = new Other_Model_DbTable_DbBranch();
			try {
				$_dbmodel->addBranch($_data);
				if(!empty($_data['save_new'])){
					Application_Form_FrmMessage::Sucessfull($this->tr->translate("INSERT_SUCCESS"),self::REDIRECT_URL . "/branch/index/add");
				}
				Application_Form_FrmMessage::Sucessfull($this->tr->translate("INSERT_SUCCESS"),self::REDIRECT_URL . "/branch/index");
			}catch (Exception $e) {
				Application_Form_FrmMessage::message($this->tr->translate("INSERT_FAIL"));
				$err =$e->getMessage();
				Application_Model_DbTable_DbUserLog::writeMessageError($err);
			}
		}
		$fm = new Other_Form_Frmbranch();
		$frm = $fm->Frmbranch();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_branch = $frm;
	}
	
	function editAction(){
		$_dbmodel = new Other_Model_DbTable_DbBranch();
		$allbranch = $_dbmodel->countBranch();
		if ($allbranch>1){
			//$this->_redirect("/other/branch");
		}
		$id=$this->getRequest()->getParam("id");
		$id = empty($id) ? 0 : $id;
		if($this->getRequest()->isPost())
		{
			$data = $this->getRequest()->getPost();
			$db = new Other_Model_DbTable_DbBranch();
			try{
				$db->updateBranch($data,$id);
				Application_Form_FrmMessage::Sucessfull($this->tr->translate("EDIT_SUCCESS"),self::REDIRECT_URL."/branch/index");
			}catch (Exception $e){
				Application_Form_FrmMessage::message($this->tr->translate("EDIT_FAIL"));
				$err=$e->getMessage();
				Application_Model_DbTable_DbUserLog::writeMessageError($err);
			}
		}
		$db=new Other_Model_DbTable_DbBranch();
		$row=$db->getBranchById($id);
		
		if(empty($row)){
			Application_Form_FrmMessage::Sucessfull("NO_RECORD",self::REDIRECT_URL."/branch/index",2);
			exit();
		}
		
		$this->view->row = $row;
		$frm= new Other_Form_Frmbranch();
		$update=$frm->FrmBranch($row);
		$this->view->frm_branch=$update;
		Application_Model_Decorator::removeAllDecorator($update);
	}
	function getbranchAction(){
		if($this->getRequest()->isPost()){
			$post=$this->getRequest()->getPost();
			$db = new Other_Model_DbTable_DbBranch();
			$result =$db->getBranchById($post['branch_id']);
			if (!empty($result)){
				$phonsse = explode(",", $result['branch_tel']);
				$string="";
				if (!empty($phonsse)) foreach ($phonsse as $key => $s){
					$sds='';
					$br='';
					$coutnPhon = count($phonsse);
					if ($coutnPhon != ($key+1)){$br='<br>';}
					if ($key==0){ $sds='Tel:&nbsp;';}else{ $sds='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';}
					$string.='<span style="white-space:nowrap;">'.$sds.$s.'</span>'.$br;
				}
				$result['phonebr']=$string;
			}
			print_r(Zend_Json::encode($result));
			exit();
		}
	}
}

