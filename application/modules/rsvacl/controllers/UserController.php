<?php

class Rsvacl_UserController extends Zend_Controller_Action
{
	const REDIRECT_URL = '/rsvacl';
	const MAX_USER = 20;
	private $activelist = array('មិនប្រើប្រាស់', 'ប្រើប្រាស់');
	private $user_typelist = array();
	
    public function init()
    {
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
    	
    	$db=new Application_Model_DbTable_DbGlobal();
    	$sql = "SELECT u.user_type_id as id,u.user_type as name FROM `rms_acl_user_type` u where u.`status`=1";
    	$this->user_typelist = $db->getGlobalDb($sql);
// 		foreach ($results as $key => $r){
// 			$this->user_typelist[$r['id']] = $r['name'];    
// 		}		
    }

    public function indexAction()
    {
		
		$db_user=new Application_Model_DbTable_DbUsers();
                
        $this->view->activelist =$this->activelist;       
        $this->view->user_typelist =$this->user_typelist;   
        $this->view->active =-1;
       $tr = Application_Form_FrmLanguages::getCurrentlanguage();
        if($this->getRequest()->isPost()){     	
        	$_data=$this->getRequest()->getPost();
        }else{
        	$_data = array(
        			'active'=>-1,
        			'user_type'=>-1,
        			'txtsearch'=>''
        	);
        }
        $rs_rows = $db_user->getUserList($_data);
        $_rs = array();
        foreach ($rs_rows as $key =>$rs){
			$branch=$tr->translate("FULLCONTROL");
        	if ($rs['typeid']!=1){
        		$branch= count(explode(",",$rs['branch_list']))." ".$tr->translate("BRANCHS");
        	}
        	$_rs[$key] =array(
        	'id'=>$rs['id'],
			'branch_access'=>$branch,
        	//'branch_name'=>$rs['branch_name'],
        	'name'=>$rs['last_name'].' '.$rs['name'],
        	'user_name'=>$rs['user_name'],
        	'user_type'=>$rs['users_type'],
        	'status'=>$rs['status']);
        }
        $list = new Application_Form_Frmtable();
        if(!empty($_rs)){
        	$glClass = new Application_Model_GlobalClass();
        	$rs_rows = $glClass->getImgActive($_rs, BASE_URL, true);
        }
        else{
        	$result = Application_Model_DbTable_DbGlobal::getResultWarning();
        }
        $collumns = array("BRANCH_ACCESS","LASTNAME_FIRSTNAME","USER_NAME","USER_TYPE","STATUS");
        $link=array(
        		'module'=>'rsvacl','controller'=>'user','action'=>'edit',
        );
        $this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('branch_access'=>$link,'user_type'=>$link,'branch_name'=>$link,'user_name'=>$link,'name'=>$link));
    	$this->view->user_type = $_data['user_type'];
    	$this->view->active = $_data['active'];
    	$this->view->txtsearch = $_data['txtsearch'];
    }
    
	
	public function addAction()
	{
			// action body
			$db_user=new Application_Model_DbTable_DbUsers();
			 
			if ($db_user->getMaxUser() > self::MAX_USER) {
				Application_Form_FrmMessage::Sucessfull('អ្នកប្រើប្រាស់របស់អ្នកបានត្រឹមតែ '.self::MAX_USER.' នាក់ ទេ!', self::REDIRECT_URL);
			}
			 
			$this->view->user_typelist =$this->user_typelist;
		
			if($this->getRequest()->isPost()){
				$userdata=$this->getRequest()->getPost();
				try {
					$db = $db_user->insertUser($userdata);
					Application_Form_FrmMessage::Sucessfull('ការបញ្ចូលជោគជ័យ', self::REDIRECT_URL);
				} catch (Exception $e) {
					$this->view->msg = 'ការបញ្ចូលមិនជោគជ័យ';
				}
			}
			$db  = new Application_Model_DbTable_DbGlobal();
			$this->view->rs_branch = $db->getAllBranchName();
			
			$user_type = $this->user_typelist;
			$this->view->user_typelist =$user_type;
			
			array_unshift($user_type, array('id'=>-1,'name'=>'បន្ថែមថ្មី'));
			$this->view->user_type = $user_type;
			
	}
	public function editAction()
	    {
	        // action body
	       $db_user=new Application_Model_DbTable_DbUsers();
	    	if($this->getRequest()->isPost()){
				$userdata=$this->getRequest()->getPost();	
				
				try {
					$db = $db_user->updateUser($userdata);				
					Application_Form_FrmMessage::Sucessfull('EDIT_SUCCESS', self::REDIRECT_URL);		
				} catch (Exception $e) {
					$this->view->msg = 'ការបញ្ចូលមិនជោគជ័យ';
				}
			}
			$us_id = $this->getRequest()->getParam('id');
			$us_id = (empty($us_id))? 0 : $us_id;
			
			$rsUser = $db_user->getUserEdit($us_id);
			
			if(empty($rsUser)){
				Application_Form_FrmMessage::Sucessfull("NO_RECORD",self::REDIRECT_URL,2);
				exit();
			}
			
			$this->view->user_edit = $rsUser;

			$user_type = $this->user_typelist;
			$this->view->user_typelist =$user_type;
				
			array_unshift($user_type, array('id'=>-1,'name'=>'បន្ថែមថ្មី'));
			$this->view->user_type = $user_type;
			
			$db  = new Application_Model_DbTable_DbGlobal();
			$this->view->rs_branch = $db->getAllBranchName();
    }
    
 
    public function changePasswordAction()
	{
		$session_user=new Zend_Session_Namespace(SYSTEM_SES);
		
		if($session_user->user_id==$this->getRequest()->getParam('id') OR $session_user->level == 1){
			$form = new RsvAcl_Form_FrmChgpwd();	
			$this->view->form=$form;
			
			if($this->getRequest()->isPost())
			{
				$db=new Rsvacl_Model_DbTable_DbUser();
				$user_id=$this->getRequest()->getParam('id');		
				if(!$user_id) $user_id=0;			
				$current_password=$this->getRequest()->getParam('current_password');
				$password=$this->getRequest()->getParam('password');
				if($db->isValidCurrentPassword($user_id,$current_password)){ 
					$db->changePassword($user_id, md5($password));	
					      //write log file 
					             $userLog= new Application_Model_Log();
					    		 $userLog->writeUserLog($user_id);
					     	  //End write log file		
					Application_Form_FrmMessage::message('Password has been changed');
					Application_Form_FrmMessage::redirector('/rsvacl/user/view-user/id/'.$user_id);
				}else{
					Application_Form_FrmMessage::message('Invalid current password');
				}
			}		
		}else{ 
			   Application_Form_FrmMessage::message('Access Denied!');
		       Application_Form_FrmMessage::redirector('/rsvacl');	
		}
		
	}
	
	function checkTitleAction(){
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db = new Application_Model_DbTable_DbUsers();
			$return=$db->CheckTitle($data);
			print_r(Zend_Json::encode($return));
			exit();
		}
	}

}
