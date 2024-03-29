<?php 
Class Installment_Form_FrmInstallment extends Zend_Dojo_Form {
	protected $tr;
public function init()
	{
		$this->tr = Application_Form_FrmLanguages::getCurrentlanguage();
	}
	public function FrmAddLoan($data=null){
		
		$session_user=new Zend_Session_Namespace(SYSTEM_SES);
		$currentBranch = $session_user->branch_id;
		$currentlevel = $session_user->level;
		
		$request=Zend_Controller_Front::getInstance()->getRequest();
		$_title = new Zend_Dojo_Form_Element_TextBox('adv_search');
		$_title->setAttribs(array('dojoType'=>'dijit.form.TextBox',
				'onkeyup'=>'this.submit()','class'=>'fullside',
				'placeholder'=>$this->tr->translate("ADVANCE_SEARCH")
		));
		$_title->setValue($request->getParam("adv_search"));
		
		$_loan_code = new Zend_Dojo_Form_Element_TextBox('loan_code');
		$_loan_code->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'readonly'=>true,
				'class'=>'fullside',
				'style'=>'color:red; font-weight: bold;'
		));
		$db = new Application_Model_DbTable_DbGlobal();
// 		$loan_number = $db->getPawnshoNumber();
// 		$_loan_code->setValue($loan_number);
		
		
		$_loan_codes = new Zend_Dojo_Form_Element_TextBox('loan_codes');
		$_loan_codes->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'readonly'=>true,
				'class'=>'fullside',
				'style'=>'color:red; font-weight: bold;'
		));

		
		$_client_code = new Zend_Dojo_Form_Element_TextBox('client_code');
		$_client_code->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));

		$_client_codes = new Zend_Dojo_Form_Element_TextBox('client_codes');
		$_client_codes->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		
		$dbs = new Loan_Model_DbTable_DbLoanIL();
		$_members = new Zend_Dojo_Form_Element_FilteringSelect('members');
		$_members->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
		));
		
		$dbc = new Application_Model_DbTable_DbGlobal();
		$opt=$dbc->getAllClientinstallment(null,1);
		$_members->setMultiOptions($opt);
		$_members->setValue($request->getParam("members"));
		
		$_level = new Zend_Dojo_Form_Element_NumberTextBox('level');
		$_level->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'required' =>'true'
		));
		$_level->setValue(1);
		
		$_currency_type = new Zend_Dojo_Form_Element_FilteringSelect('currency_type');
		$_currency_type->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
		));
		$action = $request->getActionName();
		
		$display=1;
		if($action!='add' AND $action!='edit'){
			$display=null;
		}
		$opt = $db->getVewOptoinTypeByType(15,1,3,$display);
		unset($opt['-1']);
		$_currency_type->setMultiOptions($opt);
		$_currency_type->setValue(2);
		
		$_currency_type->setValue($request->getParam("currency_type"));
		
 		$dbs = new Loan_Model_DbTable_DbLoanss();
		$_amount = new Zend_Dojo_Form_Element_NumberTextBox('total_amount');
		$_amount->setAttribs(array(
						'dojoType'=>'dijit.form.NumberTextBox',
						'class'=>'fullside',
						'required' =>'true',
				        'onkeyup'=>'calCulateAdminFee();'
		));
		
		$_rate =  new Zend_Dojo_Form_Element_NumberTextBox("interest_rate");
		$_rate->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'required'=>true,
				'class'=>'fullside',
				'invalidMessage'=>'អាចបញ្ជូលពី 1 ដល់'));
				
		$_period = new Zend_Dojo_Form_Element_NumberTextBox('period');
		$_period->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'required' =>'true',
				'class'=>'fullside',
				'onkeyup'=>'calCulatePeriod();'
		));
		$_period->setValue(1);
		
		$_releasedate = new Zend_Dojo_Form_Element_DateTextBox('release_date');
		$_releasedate->setAttribs(array(
				'dojoType'=>'dijit.form.DateTextBox',
				'required' =>'true',
				'class'=>'fullside',
				'onchange'=>'checkReleaseDate();',
				'constraints'=>"{datePattern:'dd/MM/yyyy'}"
		));
		$s_date = date('Y-m-d');
		$_releasedate->setValue($s_date);
		
		
		$_dateline = new Zend_Dojo_Form_Element_DateTextBox('date_line');
		$_dateline->setAttribs(array(
				'dojoType'=>'dijit.form.DateTextBox',
				'class'=>'fullside',
				'required' =>'true',
				'constraints'=>"{datePattern:'dd/MM/yyyy'}"
		));
		
		$term_opt = $db->getVewOptoinTypeByType(14,1,3,1);
		$_payterm = new Zend_Dojo_Form_Element_FilteringSelect('payment_term');
		$_payterm->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
				'required' =>'true'
		));
		$_payterm->setMultiOptions($term_opt);
		$_pay_every = new Zend_Dojo_Form_Element_FilteringSelect('pay_every');
		$_pay_every->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'required' =>'true',
				'class'=>'fullside',
				'onchange'=>'calCulatePeriod();'
		));
		$_pay_every->setValue(3);
		$_pay_every->setMultiOptions($term_opt);
		
		$_branch_id = new Zend_Dojo_Form_Element_FilteringSelect('branch_id');
		$_branch_id->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'required' =>'true',
				'class'=>'fullside',
				'onchange'=>'filterClient();'
		));
		
		$rows = $db->getAllBranchName();
		$options=array(''=>$this->tr->translate("SELECT_BRANCH_NAME"));
			if(!empty($rows))foreach($rows AS $row){
				$options[$row['br_id']]=$row['branch_namekh'];
			}
		$_branch_id->setMultiOptions($options);
		
		//Set Value Current Branch
		if ($currentlevel!=1){
			$_branch_id->setValue($currentBranch);
			$_branch_id->setAttribs(array(
					'readonly'=>true
			));
		}else{
			$_branch_id->setValue($request->getParam("branch_id"));
		}
		
		$_repayment_method = new Zend_Dojo_Form_Element_FilteringSelect('repayment_method');
		$_repayment_method->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'required' =>'true',
				'class'=>'fullside',
				'onchange'=>'chechPaymentMethod()'
		));
		$options = $db->getAllPaymentMethod(null,1);
		$_repayment_method->setMultiOptions($options);
		
		$pro_type=new Zend_Dojo_Form_Element_FilteringSelect('product_id');
		$pro_type->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'required' =>'true',
				'class'=>'fullside',
				'onchange'=>'calCulatePeriod()'
		));
		$options=array(-1=>"Select Product");
		$rows = $db->getAllProduct();
		if(!empty($rows))foreach($rows AS $row){
			$options[$row['id']]=$row['product_kh'];
		}
		$pro_type->setMultiOptions($options);
		$pro_type->setValue($request->getParam("product_id"));
		
		
		$_status = new Zend_Dojo_Form_Element_FilteringSelect('status_using');
		$_status->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
				'required' =>'true'
		));
		$options= array(1=>"Active",0=>"Cancel");
		$_status->setMultiOptions($options);
		
		$_interest = new Zend_Dojo_Form_Element_TextBox("interest");
		$_interest->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
				'required' =>'true'
		));
		
		$_estimate = new Zend_Dojo_Form_Element_NumberTextBox("estimatevalue");
		$_estimate->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'required' =>'true'
		));
		$_estimate->setValue(0);
		
		$description = new Zend_Dojo_Form_Element_TextBox("description");
		$description->setAttribs(array(
				'dojoType'=>'dijit.form.Textarea',
				'class'=>'fullside height200',
				'rows'=>'200'
		));

		$withdrawal = new Zend_Dojo_Form_Element_TextBox("withdrawal");
		$withdrawal->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		
		$receipt_num = new Zend_Dojo_Form_Element_TextBox("receipt_num");
		$receipt_num->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		
		$_first_payment = new Zend_Dojo_Form_Element_DateTextBox('first_payment');
		$_first_payment->setAttribs(array(
				'dojoType'=>'dijit.form.DateTextBox',
				'required' =>'true',
				'class'=>'fullside',
				'onchange'=>'calCulateEndDate();',
				'constraints'=>"{datePattern:'dd/MM/yyyy'}"
		));
		
		$_instalment_date = new Zend_Form_Element_Hidden("instalment_date");
		$_release_date = new Zend_Form_Element_Hidden("old_release_date");
		$_interest_rate = new Zend_Form_Element_Hidden("old_rate");
		$_old_payterm = new Zend_Form_Element_Hidden("old_payterm");
		
		$_start_date = new Zend_Dojo_Form_Element_DateTextBox('start_date');
		$_start_date->setAttribs(array('dojoType'=>'dijit.form.DateTextBox',
				'class'=>'fullside',
				'onchange'=>'CalculateDate();',
				'constraints'=>"{datePattern:'dd/MM/yyyy'}"));
		$_date = $request->getParam("start_date");
		
		if(empty($_date)){
		}
		$_start_date->setValue($_date);
		
		$end_date = new Zend_Dojo_Form_Element_DateTextBox('end_date');
		$end_date->setAttribs(array('dojoType'=>'dijit.form.DateTextBox','required'=>'true',
				'constraints'=>"{datePattern:'dd/MM/yyyy'}",
				'class'=>'fullside',
		));
		$_date = $request->getParam("end_date");
		
		if(empty($_date)){
			$_date = date("Y-m-d");
		}
		$end_date->setValue($_date);
		
		$opt = array(-1=>$this->tr->translate("PRODUCT_SHOP"));
		$shop_id = new Zend_Form_Element_Select("shop_id");
		$shop_id->setAttribs(array(
				'class'=>'form-control',
				'onChange'=>'',
				'class'=>'fullside',
				'dojoType'=>'dijit.form.FilteringSelect',
		));
		$row = $db->getAllShopBranch();
		if(!empty($row)){
			foreach ($row as $rs){
				$opt[$rs["id"]] = $rs["name"];
			}
		}
		$shop_id->setMultiOptions($opt);
		$shop_id->setValue($request->getParam("shop_id"));
		
		$_province = new Zend_Dojo_Form_Element_FilteringSelect('province');
		$_province->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
				'onchange'=>'filterDistrict();',
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
		
		));
		
		$rows =  $db->getAllProvince();
		$options=array($this->tr->translate("SELECT_PROVINCE")); //array(''=>"------Select Province------",-1=>"Add New");
		if(!empty($rows))foreach($rows AS $row){
			if($row['province_en_name']=="ភ្នំពេញ"){
				//exit();
				$_province->setValue($row['province_id']);
			}
			$options[$row['province_id']]=$row['province_en_name'];
		}
		$_province->setMultiOptions($options);
		$_province->setValue($request->getParam("province"));
		
		$_id = new Zend_Form_Element_Hidden('id');
		if($data!=null){
			$_branch_id->setValue($data['branch_id']);
			$_loan_code->setValue($data['loan_number']);
			$_level->setValue($data['level']);
			$_releasedate->setValue($data['date_release']);
			$_first_payment->setValue($data['first_payment']);
			$_dateline->setValue($data['date_line']);
			$receipt_num->setValue($data['receipt_num']);
			$_currency_type->setValue($data['currency_type']);
			$_amount->setValue($data['release_amount']);
			$_period->setValue($data['total_duration']);
			$_rate->setValue($data['interest_rate']);//
			
			$pro_type->setValue($data['product_id']);
			$description->setValue($data['product_description']);
			$_estimate->setValue($data['est_amount']);
			
			$_pay_every->setValue($data['term_type']);
			$_id->setValue($data['id']);
			$_status->setValue($data['status']);
		}
		$this->addElements(array($_province,$shop_id,$_start_date,$end_date,$_title,$description,$_estimate,$_first_payment,$receipt_num,$withdrawal,$pro_type,$_level,$_old_payterm,$_interest_rate,$_release_date,$_instalment_date,
				$_interest,
				$_client_codes,$_loan_codes,$_members,
				$_client_code,$_branch_id,$_currency_type,$_amount,$_rate,$_releasedate
				,$_payterm,$_status,$_period,$_repayment_method,$_pay_every,$_loan_code,
				$_dateline,$_id));
		return $this;
		
	}	
}