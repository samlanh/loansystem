<?php 
Class Pawnshop_Form_FrmSearchPayment extends Zend_Dojo_Form {
	protected $tr;
	public function init()
	{
		$this->tr = Application_Form_FrmLanguages::getCurrentlanguage();
	}
	public function AdvanceSearch ($data=null){
		
		$session_user=new Zend_Session_Namespace(SYSTEM_SES);
		$currentBranch = $session_user->branch_id;
		$currentlevel = $session_user->level;
		
		$request=Zend_Controller_Front::getInstance()->getRequest();
		//$db = new Loan_Model_DbTable_DbGroupPayment();
		
		$payment_type = new Zend_Dojo_Form_Element_FilteringSelect("paymnet_type");
		$payment_type->setAttribs(array('class'=>'fullside','dojoType'=>'dijit.form.FilteringSelect'));
		$options= array(''=>'ប្រភេទបង់ប្រាក់',1=>'បង់ធម្មតា',2=>'បង់មុន',3=>'បង់រំលស់ប្រាក់ដើម');
		$payment_type->setMultiOptions($options);
		$payment_type->setValue($request->getParam("paymnet_type"));
		
		$branch = new Zend_Dojo_Form_Element_FilteringSelect("branch_id");
		$branch->setAttribs(array('class'=>'fullside','dojoType'=>'dijit.form.FilteringSelect'));
		$opt_branch = array(-1=>$this->tr->translate("SELECT_BRANCH_NAME"));
		$dbs = new Application_Model_DbTable_DbGlobal();
		$rows = $dbs->getAllBranchName();
			if(!empty($rows))foreach($rows AS $row){
				$opt_branch[$row['br_id']]=$row['branch_namekh'];
			}
		$branch->setMultiOptions($opt_branch);
		//$branch->setValue($request->getParam("branch_id"));
		
		//Set Value Current Branch
		if ($currentlevel!=1){
			$branch->setValue($currentBranch);
			$branch->setAttribs(array(
					'readonly'=>true
			));
		}else{
			$branch->setValue($request->getParam("branch_id"));
		}
		
		$advnceSearch = new Zend_Dojo_Form_Element_TextBox("advance_search");
		$advnceSearch->setAttribs(array('class'=>'fullside'
				,'dojoType'=>'dijit.form.TextBox'
				,'placeholder'=>$this->tr->translate("ADVANCE_SEARCH")));
		
		$client_name = new Zend_Dojo_Form_Element_FilteringSelect("client_name");
		$opt_client = array(''=>'ជ្រើសរើស ឈ្មោះអតិថិជន');
		
		$client_name->setAttribs(array('class'=>'fullside','dojoType'=>'dijit.form.FilteringSelect'));
		
		$db_global = new Pawnshop_Model_DbTable_DbPayment();
		$rows = $db_global->getClientNamebyBranch();
		
// 		$rows = $db->getIndividuleClient();
		if(!empty($rows))foreach($rows AS $row){
			$opt_client[$row['id']]=$row['name'];
		}
		$client_name->setMultiOptions($opt_client);
		
		
// 		$g_client_name = new Zend_Dojo_Form_Element_FilteringSelect("g_client_name");
// 		$opt_client = array(''=>'ជ្រើសរើស ឈ្មោះអតិថិជន');
// 		$rows = $db->getAllClient();
// 		if(!empty($rows))foreach($rows AS $row){
// 			$opt_client[$row['id']]=$row['name'];
// 		}
// 		$g_client_name->setMultiOptions($opt_client);
// 		$g_client_name->setAttribs(array('class'=>'fullside','dojoType'=>'dijit.form.FilteringSelect'));
		
// 		$_coid = new Zend_Dojo_Form_Element_FilteringSelect('co_id');
// 		$_coid->setAttribs(array(
// 				'dojoType'=>'dijit.form.FilteringSelect',
// 				'onchange'=>'popupCheckCO();',
// 				'class'=>'fullside'
// 		));
// 		$options = $dbs->getAllCOName(1);
// 		$_coid->setMultiOptions($options);
// 		$_coid->setValue($request->getParam("co_id"));
		
		$start_date = new Zend_Dojo_Form_Element_DateTextBox("start_date");
		$start_date->setAttribs(array('class'=>'fullside','dojoType'=>'dijit.form.DateTextBox',
				'placeholder'=>$this->tr->translate("ចាប់ពីថ្ងៃ"),
				'constraints'=>"{datePattern:'dd/MM/yyyy'}"));
		$_date = $request->getParam("start_date");
		if(empty($_date)){
			//$_date = date('Y-m-d');
		}
		$start_date->setValue($_date);
		
		$date = date("y-m-d");
		$end_date = new Zend_Dojo_Form_Element_DateTextBox("end_date");
		$end_date->setAttribs(array('class'=>'fullside','dojoType'=>'dijit.form.DateTextBox',
				'constraints'=>"{datePattern:'dd/MM/yyyy'}",
				'placeholder'=>$this->tr->translate("រហូតដល់ថ្ងៃ")));
		$_date = $request->getParam("end_date");
		if(empty($_date)){
			$_date = date('Y-m-d');
		}
		$end_date->setValue($_date);
		
		$status = new Zend_Dojo_Form_Element_FilteringSelect("status");
		$status->setAttribs(array('class'=>'fullside','dojoType'=>'dijit.form.FilteringSelect','placeholder'=>$this->tr->translate("ស្ថានការ")));
		$opt_status = array(''=>'ជ្រើសរើស ស្ថានការ','1'=>'ដំណើការ','2'=>'មិនដំណើការ');
		$status->setMultiOptions($opt_status);
		
		$submit = new Zend_Dojo_Form_Element_SubmitButton("btn_submit");
		$submit->setAttribs(array('dojoType'=>'dijit.form.Button',
				'iconclass'=>'dijitIconSearch',
				'label'=>'Search'));
		
		$advnceSearch->setValue($request->getParam("advance_search"));
		$client_name->setValue($request->getParam("client_name"));
		$status->setValue($request->getParam("status"));
		
		$_currency_type = new Zend_Dojo_Form_Element_FilteringSelect('currency_type');
		$_currency_type->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
		));
		
		$opt = array(-1=>"--Select Currency Type--",2=>"Dollar",1=>'Khmer',3=>"Bath");
		$_currency_type->setMultiOptions($opt);
		$_currency_type->setValue($request->getParam("currency_type"));
		
		if($data!=null){
			$advnceSearch->setValue($request->getParam("advance_search"));
			$client_name->setValue($request->getParam("client_name"));
			$start_date->setValue($request->getParam("start_date"));
			$end_date->setValue($request->getParam("end_date"));
			$status->setValue($request->getParam("status"));
		}
		$this->addElements(array($_currency_type,$payment_type,$branch,$submit,$advnceSearch,$client_name,$start_date,$end_date,$status));
		return $this;
		
	}	
}