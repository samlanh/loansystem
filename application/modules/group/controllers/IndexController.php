<?php
class Group_indexController extends Zend_Controller_Action {
	const REDIRECT_URL = '/group/index';
	protected $tr;
	public function init()
	{
		header('content-type: text/html; charset=utf8');
		defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
		$this->tr=Application_Form_FrmLanguages::getCurrentlanguage();
	}
	public function indexAction(){
		try{
			$db = new Group_Model_DbTable_DbClient();
			if($this->getRequest()->isPost()){
				$formdata=$this->getRequest()->getPost();
				$search = array(
						'adv_search' => $formdata['adv_search'],
						'branch_id'=>$formdata['branch_id'],
						'province_id'=>$formdata['province'],
						'comm_id'=>$formdata['commune'],
						'district_id'=>$formdata['district'],
						'village'=>$formdata['village'],
						'status'=>$formdata['status'],
						'start_date'=> $formdata['start_date'],
						'end_date'=>$formdata['end_date']
						);
			}
			else{
				$search = array(
						'adv_search' => '',
						'branch_id'=>'',
						'status' => -1,
						'province_id'=>0,
						'district_id'=>'',
						'comm_id'=>'',
						'village'=>'',
						'start_date'=> date('Y-m-d'),
						'end_date'=>date('Y-m-d'));
			}
			
			$rs_rows= $db->getAllClients($search);
			
			$list = new Application_Form_Frmtable();
			$collumns = array("BRANCH_NAME","CUSTOMER_CODE","CLIENTNAME_KH","CLIENTNAME_EN","SEX","PHONE","HOUSE","STREET","VILLAGE","SPOUSE_NAME",
					"DATE","BY_USER","STATUS");
			$link=array(
					'module'=>'group','controller'=>'index','action'=>'edit',
			);
			$link1=array(
					'module'=>'group','controller'=>'index','action'=>'view',
			);
			$this->view->list=$list->getCheckList(10, $collumns, $rs_rows,array('branch_name'=>$link,'client_number'=>$link,'name_kh'=>$link,'name_en'=>$link));
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	
		$frm = new Application_Form_FrmAdvanceSearch();
		$frm = $frm->AdvanceSearch();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_search = $frm;
		
		$fm = new Group_Form_FrmClient();
		$frm = $fm->FrmAddClient();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_client = $frm;
		$db= new Application_Model_DbTable_DbGlobal();
		$this->view->district = $db->getAllDistricts();
		$this->view->commune_name = $db->getCommune();
		$this->view->village_name = $db->getVillage();
		
		$this->view->result=$search;	
	}
	
	public function addAction(){
		if($this->getRequest()->isPost()){
				$data = $this->getRequest()->getPost();
				$db = new Group_Model_DbTable_DbClient();
				try{
					$id= $db->addClient($data);
					if($data['chackcall']==1){
						Application_Form_FrmMessage::message("វានឹងបន្ថែមទ្រព្យបញ្ចាំរបស់អតិថិជនដោយស្វ័យប្រវត្តិ!");
						Application_Form_FrmMessage::redirectUrl("/group/callteral/add/id/".$id);
					}
					if (!empty($data['save_close'])){
						if($data['chackcall']==1){
							Application_Form_FrmMessage::message("វានឹងបន្ថែមទ្រព្យបញ្ចាំរបស់អតិថិជនដោយស្វ័យប្រវត្តិ!");
							Application_Form_FrmMessage::redirectUrl("/group/callteral/add/id/".$id);
						}else{
							Application_Form_FrmMessage::redirectUrl("/group/index");
							Application_Form_FrmMessage::message("ការ​បញ្ចូល​ជោគ​ជ័យ !");
						}
					}
					Application_Form_FrmMessage::Sucessfull('ការ​បញ្ចូល​ជោគ​ជ័យ !',"/group/index/add");
			}catch (Exception $e){
				Application_Form_FrmMessage::message("Application Error");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		$db = new Application_Model_DbTable_DbGlobal();
		$client_type = $db->getclientdtype();
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		array_unshift($client_type,array(
		'id' => -1,
		'name' => $tr->translate("ADD_NEW"),
		 ) );
		$this->view->clienttype = $client_type;
		$fm = new Group_Form_FrmClient();
		$frm = $fm->FrmAddClient();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_client = $frm;
		$dbpop = new Application_Form_FrmPopupGlobal();
		$this->view->frm_popup_village = $dbpop->frmPopupVillage();
		$this->view->frm_popup_comm = $dbpop->frmPopupCommune();
		$this->view->frm_popup_district = $dbpop->frmPopupDistrict();
		$this->view->frm_popup_clienttype = $dbpop->frmPopupclienttype();
	}
	
	public function editAction(){
		$db = new Group_Model_DbTable_DbClient();
		if($this->getRequest()->isPost()){
			try{
				$data = $this->getRequest()->getPost();
				$id= $db->addClient($data);
				if($data['chackcall']==1){
					Application_Form_FrmMessage::message("វានឹងបន្ថែមទ្រព្យបញ្ចាំរបស់អតិថិជនដោយស្វ័យប្រវត្តិ!");
					Application_Form_FrmMessage::redirectUrl("/group/callteral/add/id/".$id);
				}
				//if(!empty($data['save_close'])){
					Application_Form_FrmMessage::Sucessfull('EDIT_SUCCESS',"/group/index");
				//}
			}catch (Exception $e){
				Application_Form_FrmMessage::message("EDIT_FAILE");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		$id = $this->getRequest()->getParam("id");
		$row = $db->getClientById($id);
	        $this->view->row=$row;
		$this->view->photo = $row['photo_name'];
		if(empty($row)){
			Application_Form_FrmMessage::Sucessfull($this->tr->translate('NO_RECORD'), "/group/index",2);
			exit();	
		}
		
		$fm = new Group_Form_FrmClient();
		$frm = $fm->FrmAddClient($row);
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_client = $frm;
		
		$dbpop = new Application_Form_FrmPopupGlobal();
		$this->view->frm_popup_village = $dbpop->frmPopupVillage();
		$this->view->frm_popup_comm = $dbpop->frmPopupCommune();
		$this->view->frm_popup_district = $dbpop->frmPopupDistrict();
		$this->view->frm_popup_clienttype = $dbpop->frmPopupclienttype();
		
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$db = new Application_Model_DbTable_DbGlobal();
		$client_type = $db->getclientdtype();
		array_unshift($client_type,array(
				'id' => -1,
				'name' => $tr->translate("ADD_NEW"),
		) );
		$this->view->clienttype = $client_type;
	}
	
	function viewAction(){
		$id = $this->getRequest()->getParam("id");
		$id = empty($id) ? 0 : $id;
		
		$db = new Group_Model_DbTable_DbClient();
		$result = $db->getClientDetailInfo($id);
		if(empty($result)){
			Application_Form_FrmMessage::Sucessfull("NO_RECORD","/group/index",2);
			exit();
		}
		$this->view->client_list = $result;
	}
	public function addNewclientAction(){//ajax
		if($this->getRequest()->isPost()){
			$db = new Group_Model_DbTable_DbClient();
			$data = $this->getRequest()->getPost();
			$_data['status']=1;
			$id = $db->addClient($data);
			print_r(Zend_Json::encode($id));
			exit();
		}
	}
	function getgroupcodeAction(){
		if($this->getRequest()->isPost()){
			$db = new Group_Model_DbTable_DbClient();
			$data = $this->getRequest()->getPost();
			$code = $db->getGroupCodeBYId($data);
			print_r(Zend_Json::encode($code));
			exit();
		}
	}
	function getclientcodeAction(){
		if($this->getRequest()->isPost()){
			$db = new Group_Model_DbTable_DbClient();
			$data = $this->getRequest()->getPost();
			$code = $db->getClientCode($data['branch_id']);
			print_r(Zend_Json::encode($code));
			exit();
		}
	}
	function getclientinfoAction(){//At callecteral when click client
		if($this->getRequest()->isPost()){
			$db = new Group_Model_DbTable_DbClient();
			$data = $this->getRequest()->getPost();
			$code = $db->getClientDetailInfo($data);
			print_r(Zend_Json::encode($code));
			exit();
		}
	}
	function getclientcollateralAction(){//At callecteral when click client
		if($this->getRequest()->isPost()){
			$db = new Group_Model_DbTable_DbClient();
			$data = $this->getRequest()->getPost();
			$code = $db->getClientCallateralBYId($data['client_id']);
			print_r(Zend_Json::encode($code));
			exit();
		}
	}
	function insertDistrictAction(){//At callecteral when click client
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db_district = new Other_Model_DbTable_DbDistrict();
			$district=$db_district->addDistrictByAjax($data);
			print_r(Zend_Json::encode($district));
			exit();
		}
	}
	function insertcommuneAction(){//At callecteral when click client
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db_commune = new Other_Model_DbTable_DbCommune();
			$commune=$db_commune->addCommunebyAJAX($data);
			print_r(Zend_Json::encode($commune));
			exit();
		}
	}
	function addVillageAction(){//At callecteral when click client
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db_village = new Other_Model_DbTable_DbVillage();
			$village=$db_village->addVillage($data);
			print_r(Zend_Json::encode($village));
			exit();
		}
	}
	function insertDocumentTypeAction(){//At callecteral when click client
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$data['status']=1;
			$data['display_by']=1;
			$db = new Other_Model_DbTable_DbLoanType();
			$id = $db->addViewType($data);
			print_r(Zend_Json::encode($id));
			exit();
		}
	}
	function insertClientAction(){//At callecteral when click client
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db=new Group_Model_DbTable_DbClient();
			$row=$db->addIndividaulClient($data);
			print_r(Zend_Json::encode($row));
			exit();
		}
	
	}
	function getclientnumberbybranchAction(){
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db = new Application_Model_DbTable_DbGlobal();
			$dataclient=$db->getAllClientNumber($data['branch_id']);
			if (empty($data['no_addnew'])){
				array_unshift($dataclient, array('id' => "-1",'branch_id'=>$data['branch_id'],'name'=>$this->tr->translate("Add New Client")) );
			}
			print_r(Zend_Json::encode($dataclient));
			exit();
		}
	}
	
	function getclientbybranchAction(){//At callecteral when click client /loan customer
		if($this->getRequest()->isPost()){
			 $data = $this->getRequest()->getPost();
			 $db = new Application_Model_DbTable_DbGlobal();
             $dataclient=$db->getAllClient($data['branch_id']);
			 print_r(Zend_Json::encode($dataclient));
			exit();
		}
	}
	function getclieninstallmentAction(){//At callecteral when click client
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db = new Application_Model_DbTable_DbGlobal();
			$dataclient=$db->getAllClientinstallment($data['branch_id']);
			print_r(Zend_Json::encode($dataclient));
			exit();
		}
	}
	function getcliencodeinstallmentAction(){//At callecteral when click client
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db = new Application_Model_DbTable_DbGlobal();
			$dataclient=$db->getAllClientcodeinstallment($data['branch_id']);
			print_r(Zend_Json::encode($dataclient));
			exit();
		}
	}
	
	function getGroupclientbybranchAction(){//At callecteral when click client
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db = new Application_Model_DbTable_DbGlobal();
			$dataclient=$db->getAllClientGroup($data['branch_id']);
			array_unshift($dataclient, array('id' => "-1",'branch_id'=>$data['branch_id'],'name'=>$this->tr->translate("Add New Client")) );
			print_r(Zend_Json::encode($dataclient));
			exit();
		}
	}
	function getGoupCodebybranchAction(){//At callecteral when click client
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db = new Application_Model_DbTable_DbGlobal();
			$dataclient=$db->getAllClientGroupCode($data['branch_id']);
			array_unshift($dataclient, array('id' => "-1",'branch_id'=>$data['branch_id'],'name'=>$this->tr->translate("Add New Client")) );
			print_r(Zend_Json::encode($dataclient));
			exit();
		}
	}
	function addjobAction(){
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db = new Application_Model_DbTable_DbGlobal();
			$dataclient=$db->getAllClientGroupCode($data['branch_id']);
			array_unshift($dataclient, array('id' => "-1",'branch_id'=>$data['branch_id'],'name'=>$this->tr->translate("Add New Client")) );
			print_r(Zend_Json::encode($dataclient));
			exit();
		}
	}
	
	function addCallteralTypeAction(){ 
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db_insert = new Group_Model_DbTable_DbCallecteralltype();
			$row=$db_insert->addCallteralAjax($data);
			$result = array("id"=>$row);
			print_r(Zend_Json::encode($row));
			exit();
		}
	}
	
}

