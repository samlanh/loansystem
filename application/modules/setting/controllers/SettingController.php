<?php
class Setting_SettingController extends Zend_Controller_Action {
	
	
public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
	}
	public function indexAction()
	{
		$db = new Setting_Model_DbTable_DbLabel();
		$data = $db->getAllSystemSetting();
		$this->view->data = $data;	
		if($this->getRequest()->isPost()){
			$post=$this->getRequest()->getPost();
			try {
				$db = $db->updateKeyCode($post, $data);
				Application_Form_FrmMessage::Sucessfull('EDIT_SUCCESS','/setting/setting');
			} catch (Exception $e) {
				$this->view->msg = 'ការបញ្ចូលមិនជោគជ័យ';
			}
		}
	}
	public function addAction(){
		$this->_redirect('/setting/setting');
	}
}

