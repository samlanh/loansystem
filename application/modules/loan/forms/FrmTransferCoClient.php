<?php 
Class Loan_Form_FrmTransferCoClient extends Zend_Dojo_Form {
	protected $tr;
	public function init()
	{
		$this->tr = Application_Form_FrmLanguages::getCurrentlanguage();
	}
	public function FrmTransfer($data=null){
		
		$request=Zend_Controller_Front::getInstance()->getRequest();
		
		$db = new Application_Model_DbTable_DbGlobal();
		$db_co = new Loan_Model_DbTable_DbTransferCoClient();
		$branch_name = new Zend_Dojo_Form_Element_FilteringSelect('branch_name');
    	$branch_name->setAttribs(array(
    			'dojoType'=>'dijit.form.FilteringSelect',
    			'class'=>'fullside',
    			'required' =>'true',
    			'autoComplete'=>"false",
    			'queryExpr'=>'*${0}*',
    	));
    	
    	$rows = $db->getAllBranchName();
    	$options=array(''=>$this->tr->translate("Choose Branch"));
    	if(!empty($rows))
    		foreach($rows AS $row){
    		$options[$row['br_id']]=$row['branch_namekh'];
    	}
    	$branch_name->setMultiOptions($options);
    	$branch_name->setValue($request->getParam('branch_name'));    	
		
		$_date= new Zend_Dojo_Form_Element_DateTextBox('Date');
		$_date->setAttribs(array(
				'dojoType'=>'dijit.form.DateTextBox',
				'class'=>'fullside',
				'constraints'=>"{datePattern:'dd/MM/yyyy'}"
				
		));
		$_date->setValue(date('Y-m-d'));
		$co_code = new Zend_Dojo_Form_Element_FilteringSelect('co_code');
		$co_code->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
				'onchange'=>"getClientInfo(1);",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
		));
	
		$_member = new Zend_Dojo_Form_Element_FilteringSelect('member');
		$_member->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
				'onchange'=>'getClientInfo(1);',
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
		));
		
		$db = new Application_Model_DbTable_DbGlobal();
		$options = $db->getGroupCodeById(2,0,1);
		$_member->setMultiOptions($options);
		
		$_customer_code = new Zend_Dojo_Form_Element_FilteringSelect('customer_code');
		$_customer_code->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
				'onchange'=>'getClientInfo(2);',
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
		));
		$group_opt = $db->getGroupCodeById(1,0,1);//code,individual,option
		$_customer_code->setMultiOptions($group_opt);
		
		
		$row_co = $db->getAllCOName();
		$options_co =array(''=>$this->tr->translate("SELECT_CREDIT_OFFICER"));
		if (!empty($row_co))
			foreach ($row_co AS $row_cos){
			$options_co[$row_cos['co_id']] = $row_cos['co_khname'];
		}
		$co_code->setMultiOptions($options_co);
		$co_code->setValue($request->getParam('co_code'));
		
		
		
		$formc_co = new Zend_Dojo_Form_Element_FilteringSelect('name_co');
		$formc_co->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
			    'onchange'=>"getClientInfo(2);",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				));
		$options = $db->getAllCOName(1);
		$options['']=$this->tr->translate("SELECT_TO_CREDIT_OFFICER");
		$formc_co->setMultiOptions($options);
		$formc_co->setValue($request->getParam('name_co'));
		
		$name_client = new Zend_Dojo_Form_Element_FilteringSelect('name_client');
		$name_client->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
				'onchange'=>"getClientInfo(3);",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
		));
		$row_co = $db_co->getcoinfo();
		$options_co =array(''=>$this->tr->translate("SELECT_CUSTOMER"));
		if (!empty($row_co))
			foreach ($row_co AS $row_cos){
			$options_co[$row_cos['customer_id']] = $row_cos['customer_name'];
		}
		$name_client->setMultiOptions($options_co);
		
		$code_client = new Zend_Dojo_Form_Element_FilteringSelect('code_client');
		$code_client->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
				'onchange'=>"getClientInfo(4);",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
		));
		$row_number = $db_co->getcoinfo();
		$options_numbers=array(''=>$this->tr->translate("SELECT_CUSTOMER_CODE"));
		if (!empty($row_number))
			foreach ($row_number AS $row_client){
			$options_numbers[$row_client['id']] = $row_client['loan_cus'];
		}
		$code_client->setMultiOptions($options_numbers);
		
		$loan_number= new Zend_Dojo_Form_Element_FilteringSelect('loan_number');
		$loan_number->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
				'onchange'=>"getClientInfo(5);",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
		));
		$row_number = $db_co->getcoinfo();
		$options_from =array(''=>$this->tr->translate("Select Loan Number"));
		if (!empty($row_number))
			foreach ($row_number AS $row_numbers){
			$options_from[$row_numbers['id']] = $row_numbers['loan_cus'];
		}		
		$loan_number->setMultiOptions($options_from);
		$loan_number->setValue($request->getParam("loan_number")); 
		
		$loan_client = new Zend_Dojo_Form_Element_FilteringSelect('loan_client');
		$loan_client->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
				'onchange'=>"getClientInfo(3);",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
		));
		$loan_clientgroup = $db_co->getcoinfo();
		$options_loan_client =array(''=>$this->tr->translate("SELECT_CUSTOMER_NAME"));
		if (!empty($loan_clientgroup))
			foreach ($loan_clientgroup AS $loan_clientgroups){
			$options_loan_client[$loan_clientgroups['customer_id']] = $loan_clientgroups['customer_name'];
		}
		$loan_client->setMultiOptions($options_loan_client);
		$loan_client->setValue($request->getParam('loan_client'));
		
		$desc = new Zend_Dojo_Form_Element_TextBox('note');
		$desc ->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		$desc->setValue($request->getParam('note'));
		
		$note = new Zend_Dojo_Form_Element_Textarea('Note');
		$note ->setAttribs(array(
				'dojoType'=>'dijit.form.SimpleTextarea',
				'class'=>'fullside',
				'required' =>true,
				'style'=>'width:98%'
		));
		
		$user_id = new Zend_Dojo_Form_Element_FilteringSelect('user_id');
		$user_id->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
		));
		$row_froms = $db_co->getcoinfo();
		$options_from =array(''=>$this->tr->translate("PLEASE_SELECT"));
		if (!empty($row_froms))
			foreach ($row_froms AS $row_from){
		}
		$user_id->setMultiOptions($options_from);	

		$_arr = array(1=>$this->tr->translate("ACTIVE"),0=>$this->tr->translate("DACTIVE"),-1=>$this->tr->translate("ALL"));
		$_status = new Zend_Dojo_Form_Element_FilteringSelect("status");
		$_status->setMultiOptions($_arr);
		$_status->setValue($request->getParam('status'));
		$_status->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'required'=>'true',
				'missingMessage'=>'Invalid Module!',
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				'class'=>'fullside'));
		
		
		$star_date = new Zend_Dojo_Form_Element_DateTextBox('start_date');
		$star_date->setAttribs(array('dojoType'=>'dijit.form.DateTextBox','class'=>'fullside',
				'constraints'=>"{datePattern:'dd/MM/yyyy'}"));
		$date = $request->getParam("start_date");
		if(empty($date)){
		}
		$star_date->setValue($date);
		
		$_enddate = new Zend_Dojo_Form_Element_DateTextBox('end_date');
		$_enddate->setAttribs(array('dojoType'=>'dijit.form.DateTextBox',
				'required'=>'true',
				'class'=>'fullside',
				'constraints'=>"{datePattern:'dd/MM/yyyy'}"
		));
		$date = $request->getParam("end_date");
		
		if(empty($date)){
			$date = date("Y-m-d");
		}
		$_enddate->setValue($date);

		$_btn_search = new Zend_Dojo_Form_Element_SubmitButton('btn_search');
		$_btn_search->setAttribs(array(
				'dojoType'=>'dijit.form.Button',
				'iconclass'=>'dijitIconSearch',
		));
		
		if($data!=null){				
			$branch_name->setValue($data['branch_id']);
			$co_code->setValue($data['code_to']);
			$formc_co->setValue($data['to']);
			$name_client->setValue($data['client_id']);
			$code_client->setValue($data['client_id']);			
			$_status->setValue($data['status']);
			$_date->setValue($data['date']);
			$note->setValue($data['note']);
			$loan_number->setValue($data['loan_id']);
			$loan_client->setValue($data['loan_id']);
			$_member->setValue($data['client_id']);
			$_member->setValue($data['client_id']);
			$_customer_code->setValue($data['client_id']);
		}		
		
		$this->addElements(array($_customer_code,$_member,$_btn_search,$desc,$star_date,$_enddate,$loan_client,$loan_number,$_status,$branch_name,$_date,$formc_co,$name_client,$co_code,$code_client,$note,$user_id));
		return $this;
	}	
}