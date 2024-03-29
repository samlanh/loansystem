<?php 
Class Loan_Form_FrmIlPayment extends Zend_Dojo_Form {
	protected $tr;
	public function init()
	{
		$this->tr = Application_Form_FrmLanguages::getCurrentlanguage();
	}
	public function FrmAddIlPayment($data=null){
		
		$db = new Application_Model_DbTable_DbGlobal();
		
		$request=Zend_Controller_Front::getInstance()->getRequest();
		$session_user=new Zend_Session_Namespace(SYSTEM_SES);
		$currentBranch = $session_user->branch_id;
		$currentlevel = $session_user->level;
		
		$old_penelize = new Zend_Form_Element_Hidden("old_penelize");
		$old_penelize->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		
		$old_service_charge = new Zend_Form_Element_Hidden("old_service_charge");
		$old_service_charge->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		
		$_interest_rate = new Zend_Dojo_Form_Element_TextBox("interest_rate");
		$_interest_rate->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
				'required' =>'true'
		));
		
		$_interest_ratelabel = new Zend_Dojo_Form_Element_TextBox("interest_ratelabel");
		$_interest_ratelabel->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
				'required' =>'true',
				'readOnly'=>true,
				
		));
		
		$term_opt = $db->getVewOptoinTypeByType(14,1,3);
		$_payterm = new Zend_Dojo_Form_Element_FilteringSelect('payment_term');
		$_payterm->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
				'required' =>'true',
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
		));
		$_payterm->setMultiOptions($term_opt);
		
		$_currency_type = new Zend_Dojo_Form_Element_FilteringSelect('currency_type');
		$_currency_type->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
		));
		$opt = array(-1=>"--Select Currency Type--",2=>"Dollar",1=>'Khmer',3=>"Bath");
		$_currency_type->setMultiOptions($opt);
		
		$_groupid = new Zend_Dojo_Form_Element_FilteringSelect('client_id');
		$_groupid->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
 				//'onchange'=>'getLaonPayment(3);getAllLaonPayment(3);',
				'required'=>true,
				'readOnly'=>'readOnly',
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				));
		$rows = $db ->getClientByType();
		$options=array(''=>'-----Select------');
		if(!empty($rows))foreach($rows AS $row){
			$options[$row['client_id']]=$row['name_kh'].','.$row['province_en_name'].','.$row['district_name'].','.$row['commune_name'].','.$row['village_name'];
		}
		$_groupid->setMultiOptions($options);
		
		$_client_code = new Zend_Dojo_Form_Element_FilteringSelect('client_code');
		$_client_code->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
				'required'=>true,
				'readOnly'=>'readOnly',
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
		));
		
		$option_client_number = array(''=>'-----Select------');
		if(!empty($rows))foreach($rows AS $row){
			$option_client_number[$row['client_id']]=$row['client_number'];
		}
		$_client_code->setMultiOptions($option_client_number);
		
		
		$row_loan_number = $db->getAllLoanNumber(1);
		$options=array(''=>'');
		if(!empty($row_loan_number))foreach($row_loan_number AS $row){
			$options[$row['loan_number']]=$row['loan_number'];
		}
		$_loan_number = new Zend_Dojo_Form_Element_FilteringSelect('loan_number');
		$_loan_number->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
				'onChange'=>'getLaonHasPayByLoanNumber(1);getLaonPayment(1);getAllLaonPayment(1);',
				'required'=>true,
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
		));
		$_loan_number->setMultiOptions($options);
		
		$old_loan_number = new Zend_Form_Element_Hidden("old_loan_number");
		$old_loan_number->setAttribs(array(
				'dojoType'	=>	'dijit.form.TextBox',
		));
		
		
		$_amount_receive = new Zend_Dojo_Form_Element_NumberTextBox('amount_receive');
		$_amount_receive->setAttribs(array(
				'dojoType'	=>	'dijit.form.NumberTextBox',
				'class'		=>	'fullside',
				'onKeyup'	=>	'totalReturn();',
				'style'		=>	'color:red;',
				'required'=>true,
		));
		
		$old_amount_receive = new Zend_Form_Element_Text('old_amount_receive');
		$old_amount_receive->setAttribs(array(
				'dojoType'	=>	'dijit.form.TextBox',
				'style'		=>	'color:red;',
		));
		
		$_amount_return = new Zend_Dojo_Form_Element_NumberTextBox('amount_return');
		$_amount_return->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'style'=>'color:red;',
				'readonly'=>true
		));
		
		$_service_charge = new Zend_Dojo_Form_Element_NumberTextBox('service_charge');
		$_service_charge->setAttribs(array(
				'dojoType'	=>'dijit.form.NumberTextBox',
				'class'		=>'fullside',
				'onkeyup'	=>	'calTotal();'
		));
		$_service_charge->setValue(0);
		
		$_branch_id = new Zend_Dojo_Form_Element_FilteringSelect('branch_id');
		$_branch_id->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
				'required' =>'true',
				'OnChange'	=>	'filterLoanNumber();filterClient();',
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
		));
		
		$rows = $db->getAllBranchName();
		$options=array(''=>$this->tr->translate("SELECT_BRANCH_NAME"));
		if(!empty($rows))foreach($rows AS $row){
			$options[$row['br_id']]=$row['branch_namekh'];
		}
		$_branch_id->setMultiOptions($options);
		
		if ($currentlevel!=1){
			$_branch_id->setValue($currentBranch);
			$_branch_id->setAttribs(array(
					'readonly'=>true
			));
		}else{
			$_branch_id->setValue($request->getParam("branch_id"));
		}
		
		$_coid = new Zend_Dojo_Form_Element_FilteringSelect('co_id');
		$rows = $db ->getAllCOName();
		$options=array(''=>"------Select------",-1=>"Add New");
		if(!empty($rows))foreach($rows AS $row) $options[$row['co_id']]=$row['co_khname'];
		$_coid->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
		 						//'onchange'=>'getLoan(1);'
		));
		$_coid->setMultiOptions($options);
		
		$_cocode = new Zend_Dojo_Form_Element_FilteringSelect('co_code');
		$rows = $db ->getAllCOName();
		$options=array(''=>"------Select------",-1=>"Add New");
		if(!empty($rows))foreach($rows AS $row) $options[$row['co_id']]=$row['co_code'];
		$_cocode->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
				'readOnly'=>'readOnly',
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
		));
		$_cocode->setMultiOptions($options);
		
		$_priciple_amount = new Zend_Dojo_Form_Element_NumberTextBox('priciple_amount');
		$_priciple_amount->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'readOnly'=>'readOnly'
		));
		
		$_loan_fee = new Zend_Dojo_Form_Element_NumberTextBox('loan_fee');
		$_loan_fee->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'readOnly'=>'readOnly'
		));
		
		$_os_amount = new Zend_Dojo_Form_Element_NumberTextBox('os_amount');
		$_os_amount->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'readOnly'=>'readOnly',
				'required'=>true,
		));
		
		$_rate = new Zend_Dojo_Form_Element_NumberTextBox('total_interest');
		$_rate->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'required' =>'true',
				'style'=>'color:red;',
				'onKeyup'=>'calTotal();',
				'required'=>true,
		));
		
		$_penalize_amount = new Zend_Dojo_Form_Element_NumberTextBox('penalize_amount');
		$_penalize_amount->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'required'=>true,
				'onKeyup'=>'calTotal();'
		));
		$_penalize_amount->setValue(0);
		
		$_total_payment = new Zend_Dojo_Form_Element_NumberTextBox('total_payment');
		$_total_payment->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
				'required' =>'true',
				'style'=>'color:red;',
				'required'=>true,
				'readOnly'=>'readOnly'
		));
		
		$_hide_total_payment = new Zend_Form_Element_Text('hide_total_payment');
		$_hide_total_payment->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
		));
		
		$_note = new Zend_Dojo_Form_Element_TextBox('note');
		$_note->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		
		$_collect_date = new Zend_Dojo_Form_Element_DateTextBox('collect_date');
		$_collect_date->setAttribs(array(
				'dojoType'=>'dijit.form.DateTextBox',
				'class'=>'fullside',
				'constraints'=>"{datePattern:'dd/MM/yyyy'}",
				'required' =>'true',
				'Onchange'	=>	'calculateTotal();'
		));
		
		$settingEnalbleDate =0;
		if ($settingEnalbleDate==1){
			$session_user=new Zend_Session_Namespace(SYSTEM_SES);
			if($session_user->level!=1){
				$_collect_date->setAttribs(array(
						'readonly'=>true,
						));
			}
		}
		$c_date = date('Y-m-d');
		$_collect_date->setValue($c_date);
		
// 		$date_input = new Zend_Dojo_Form_Element_DateTextBox('date_input');
// 		$date_input->setAttribs(array(
// 				'dojoType'=>'dijit.form.DateTextBox',
// 				'class'=>'fullside',
// 				'required' =>true
// 		));
// 		$date_input->setValue($c_date);

		$date_input = new Zend_Form_Element_Hidden("date_input");
		$date_input->setValue($c_date);
		
		$reciever = new Zend_Form_Element_Hidden("reciever");
		$reciever->setAttribs(array('dojoType'=>'dijit.form.TextBox'));
		
		$discount = new Zend_Dojo_Form_Element_TextBox("discount");
		$discount->setAttribs(array('dojoType'=>'dijit.form.TextBox','class'=>'fullside'));
		
		$reciept_no = new Zend_Dojo_Form_Element_TextBox("reciept_no");
		$reciept_no->setAttribs(array('dojoType'=>'dijit.form.TextBox','class'=>'fullside',
		//'readonly'=>true,
				'style'=>'color:red; font-weight: bold;'));
		$db_installment = new Installment_Model_DbTable_DbInstallmentPayment();
		$loan_number = $db_installment->getIlPaymentNumber();
		$reciept_no->setValue($loan_number);
		$id = new Zend_Form_Element_Hidden("id");
		$id->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		
		$option_pay = new Zend_Dojo_Form_Element_FilteringSelect('option_pay');
		$option_pay->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
				'OnChange'=>'payOption();',
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
		));
		$option_status = array(1=>'បង់ធម្មតា',2=>'បង់មុន',3=>'បង់រំលស់ប្រាក់ដើម',4=>'បង់ផ្តាច់');
		$option_pay->setMultiOptions($option_status);
		
		$amount_payment_term = new Zend_Form_Element_Hidden("amount_payment_term");
		$amount_payment_term->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		
		$installment_date = new Zend_Form_Element_Hidden("installment_date");
		
		$old_tota_pay = new Zend_Form_Element_Text("oldTotalPay");
		$old_tota_pay->setAttribs(array('dojoType'=>'dijit.form.TextBox','class'=>'fullside',));
		
		$release_date = new Zend_Dojo_Form_Element_TextBox("release_date");
		$release_date->setAttribs(array('dojoType'=>'dijit.form.TextBox','class'=>'fullside','readOnly'=>'readOnly'));
		
		$old_release_date = new Zend_Form_Element_Hidden("old_release_date");
		$old_release_date->setAttribs(array('dojoType'=>'dijit.form.TextBox','class'=>'fullside','readOnly'=>'readOnly'));
		
		$loan_level= new Zend_Dojo_Form_Element_TextBox("load_level");
		$loan_level->setAttribs(array('dojoType'=>'dijit.form.TextBox','class'=>'fullside','readOnly'=>'readOnly'));
		
		$remain= new Zend_Dojo_Form_Element_NumberTextBox("remain");
		$remain->setAttribs(array('dojoType'=>'dijit.form.TextBox','class'=>'fullside','readOnly'=>'readOnly'));
		
		$total_amount_loan = new Zend_Dojo_Form_Element_TextBox("total_amount_loan");
		$total_amount_loan->setAttribs(array('dojoType'=>'dijit.form.TextBox','class'=>'fullside','readOnly'=>'readOnly'));
		
		$loan_period = new Zend_Dojo_Form_Element_TextBox("loan_period");
		$loan_period->setAttribs(array('dojoType'=>'dijit.form.TextBox','class'=>'fullside','readOnly'=>'readOnly'));
		
		$candition_payment = new Zend_Dojo_Form_Element_TextBox("pay_condition");
		$candition_payment->setAttribs(array('dojoType'=>'dijit.form.TextBox','class'=>'fullside','readOnly'=>'readOnly'));
		
		$options = $db->getAllPaymentMethod(null,1);
		$payment_method= new Zend_Dojo_Form_Element_FilteringSelect("payment_method");
		$payment_method->setAttribs(array('dojoType'=>'dijit.form.FilteringSelect','class'=>'fullside','readOnly'=>'readOnly','autoComplete'=>"false",
				'queryExpr'=>'*${0}*',));
		$payment_method->setMultiOptions($options);
		
		$using_date = new Zend_Dojo_Form_Element_DateTextBox("using_date");
		$using_date->setAttribs(array(
				'dojoType'=>'dijit.form.DateTextBox',
				'class'=>'fullside',
				'constraints'=>"{datePattern:'dd/MM/yyyy'}",
				'required' =>true
		));
		
		$_last_payment_date = new Zend_Form_Element_Hidden("last_payment_date");
		$_last_payment_date->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		
		$payment_date = new Zend_Dojo_Form_Element_DateTextBox("payment_date");
		$payment_date->setAttribs(array(
				'dojoType'=>'dijit.form.DateTextBox',
				'constraints'=>"{datePattern:'dd/MM/yyyy'}",
				'class'=>'fullside',));
		
		$amount_late = new Zend_Dojo_Form_Element_TextBox("amount_late");
		$amount_late->setAttribs(array('dojoType'=>'dijit.form.TextBox','class'=>'fullside','readOnly'=>'readOnly'));
		
		$amount_paid = new Zend_Dojo_Form_Element_TextBox("paid_before");
		$amount_paid->setAttribs(array('dojoType'=>'dijit.form.TextBox','class'=>'fullside','readOnly'=>'readOnly'));
		
		$last_payment = new Zend_Dojo_Form_Element_DateTextBox("last_payment");
		$last_payment->setAttribs(array('dojoType'=>'dijit.form.DateTextBox',
				'constraints'=>"{datePattern:'dd/MM/yyyy'}",
				'class'=>'fullside','readOnly'=>'readOnly'));
		
		
		$installment_no = new Zend_Dojo_Form_Element_TextBox("installment_no");
		$installment_no->setAttribs(array('dojoType'=>'dijit.form.TextBox','class'=>'fullside','readOnly'=>'readOnly'));
		
		
		if($data!=""){
			$id->setValue($data["id"]);
// 			$_groupid->setValue($data["group_id"]);
// 			$_loan_number->setValue($data["loan_number"]);
// 			$_branch_id->setValue($data["branch_id"]);
// 			$_client_code->setValue($data["group_id"]);
// 			$reciept_no->setValue($data["receipt_no"]);
// 			$_coid->setValue($data["co_id"]);
// 			$option_pay->setValue($data["status"]);
// 			$_amount_receive->setValue($data["recieve_amount"]);
// 			$_amount_return->setValue($data["return_amount"]);
// 			$_penalize_amount->setValue($data["penalize_amount"]);
// 			$_total_payment->setValue($data["total_payment"]);
// 			$_priciple_amount->setValue($data["principal_amount"]);
// 			$_os_amount->setValue($data["total_principal_permonth"]);
//  		$discount->setValue($data["total_discount"]);
// 			$_rate->setValue($data["total_interest"]);
// 			$_note->setValue($data["note"]);
// 			$date_input->setValue($data["date_input"]);
// 			$_collect_date->setValue(date("y-m-d"));
// 			$_service_charge->setValue($data["service_charge"]);
// 			$reciever->setValue($data["receiver_id"]);
// 			$_currency_type->setValue($data["currency_type"]);
// 			$amount_payment_term->setValue($data["amount_term"]);
// 			$_interest_rate->setValue($data["interest_rate"]);
// 			$_payterm->setValue($data["collect_typeterm"]);
// 			$_collect_date->setValue($data["date_pay"]);
// 			$old_tota_pay->setValue($data["total_payment"]-$data["service_charge"]);
		}
		$this->addElements(array($last_payment,$payment_date,$installment_no,$amount_late,$amount_paid,$_interest_ratelabel,$old_amount_receive,$old_loan_number,$old_release_date,$old_service_charge,$old_penelize,$_cocode,$_last_payment_date,$using_date,$total_amount_loan,$loan_period,$candition_payment,$payment_method,$release_date,$loan_level,$remain,$old_tota_pay,$installment_date,$amount_payment_term,$_interest_rate,$_payterm,$_currency_type,$id,$option_pay,$date_input,$reciept_no,$reciever,$discount,$id,$_groupid,$_coid,$_priciple_amount,$_loan_fee,$_os_amount,$_rate,
				$_penalize_amount,$_collect_date,$_total_payment,$_note,$_service_charge,$_amount_return,
				$_amount_receive,$_client_code,$_loan_number,$_branch_id,$_hide_total_payment));
		return $this;
		
	}

	public function FrmGroupPayment($data=null){
	
		$db = new Application_Model_DbTable_DbGlobal();
		
		$old_penelize = new Zend_Form_Element_Hidden("old_penelize");
		$old_penelize->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
				//'required' =>'true'
		));
		
		$old_loan_number = new Zend_Form_Element_Hidden("old_loan_number");
		$old_loan_number->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
		));
		
		$old_service_charge = new Zend_Form_Element_Hidden("old_service_charge");
		$old_service_charge->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
				//'required' =>'true'
		));
		
		$_interest_rate = new Zend_Dojo_Form_Element_TextBox("interest_rate");
		$_interest_rate->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
				'required' =>'true',
				'readOnly'=>'readOnly'
		));
		
		$term_opt = $db->getVewOptoinTypeByType(14,1,3);
		$_payterm = new Zend_Dojo_Form_Element_FilteringSelect('payment_term');
		$_payterm->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
				'required' =>'true',
				'readOnly'=>'readOnly',
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
		));
		$_payterm->setMultiOptions($term_opt);
		
		$_currency_type = new Zend_Dojo_Form_Element_FilteringSelect('currency_type');
		$_currency_type->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
				'readOnly'=>'readOnly',
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
		));
		$opt = array(-1=>"--Select Currency Type--",2=>"Dollar",1=>'Khmer',3=>"Bath");
		$_currency_type->setMultiOptions($opt);
		//$_currency_type->setValue($request->getParam("currency_type"));
		
// 		$_groupid = new Zend_Dojo_Form_Element_FilteringSelect('client_id');
// 		$_groupid->setAttribs(array(
// 				'dojoType'=>'dijit.form.FilteringSelect',
// 				'class'=>'fullside',
//  				//'onchange'=>'getLaonPayment(3);getAllLaonPayment(3);',
// 				'required'=>true
// 				));
		$rows = $db ->getClientByType(1);
// 		$options=array(''=>'-----Select------');
// 		if(!empty($rows))foreach($rows AS $row){
// // 			$options[$row['client_id']]=$row['name_en'].','.$row['province_en_name'].','.$row['district_name'].','.$row['commune_name'].','.$row['village_name'];
// 			$options[$row['client_id']]=$row['name_en'].','.$row['province_en_name'];
// 		}
// 		$_groupid->setMultiOptions($options);
		
		$_client_code = new Zend_Dojo_Form_Element_FilteringSelect('client_code');
		$_client_code->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
				//'readOnly'=>'readOnly',
				//'onchange'=>'getLaonHasPayByLoanNumber(2);getLaonPayment(2);getAllLaonPayment(2);',
				'required'=>true,
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
		));
		
		$option_client_number = array(''=>'-----Select------');
		if(!empty($rows))foreach($rows AS $row){
			$option_client_number[$row['client_id']]=$row['client_number']."-".$row['name_en'];
		}
		$_client_code->setMultiOptions($option_client_number);
		
// 		$_loan_number = new Zend_Dojo_Form_Element_TextBox('loan_number');
// 		$_loan_number->setAttribs(array(
// 				'dojoType'=>'dijit.form.TextBox',
// 				'class'=>'fullside',
// 				//'onKeyUp'=>'getLaonPayment(1);getAllLaonPayment(1);'
// 				'required'=>true
// 		));
		$a = Application_Model_GlobalClass::getDefaultAdapter();
		$sql = "SELECT 
			  l.`loan_number` 
			FROM
			  `ln_loan` AS l
			WHERE l.is_badloan=0 AND l.status=1 AND l.`loan_type` =2 GROUP BY l.`loan_number` ";
		$row_loan_number = $a->fetchAll($sql);
		$options=array(''=>'');
		if(!empty($row_loan_number))foreach($row_loan_number AS $row){
			$options[$row['loan_number']]=$row['loan_number'];
		}
		$_loan_number = new Zend_Dojo_Form_Element_FilteringSelect('loan_number');
		$_loan_number->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
				'onChange'=>'getLaonPayment();
							 getAllLaonPayment();
							 getPaymentHasByLoan();',
				'required'=>true,
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
		));
		$_loan_number->setMultiOptions($options);
		
		
		$_amount_receive = new Zend_Dojo_Form_Element_NumberTextBox('amount_receive');
		$_amount_receive->setAttribs(array(
				'dojoType'	=>	'dijit.form.NumberTextBox',
				'class'		=>	'fullside',
				'onkeyup'	=>	'totalReturn();',
				'style'		=>	'color:red;',
				'required'	=>	true,
				
		));
		
		$old_amount_receive = new Zend_Form_Element_Hidden('old_amount_receive');
		$old_amount_receive->setAttribs(array(
				'dojoType'	=>	'dijit.form.TextBox',
				'style'		=>	'color:red;',
				//'required'	=>	true,
		
		));
		
		$_amount_return = new Zend_Dojo_Form_Element_NumberTextBox('amount_return');
		$_amount_return->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'style'=>'color:red;',
				//'required'=>true,
				'readonly'=>true
		));
		
		$_service_charge = new Zend_Dojo_Form_Element_NumberTextBox('service_charge');
		$_service_charge->setAttribs(array(
				'dojoType'	=>'dijit.form.NumberTextBox',
				'class'		=>'fullside',
				//'onkeyUp'=>'totalReturn();'
				'onKeyup'	=>	'doTotalByServ();'
		));
		$_service_charge->setValue(0);
		
		$_branch_id = new Zend_Dojo_Form_Element_FilteringSelect('branch_id');
		$_branch_id->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
				'required' =>'true',
				'OnChange'	=>	'filterLoanNumber();',
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
		));
		
		$rows = $db->getAllBranchName();
		$options=array(''=>'--------Select----------');
		if(!empty($rows))foreach($rows AS $row){
			$options[$row['br_id']]=$row['branch_namekh'];
		}
		$_branch_id->setMultiOptions($options);
		
		
		//$_coid = new Zend_Dojo_Form_Element_FilteringSelect('co_id');
		$_coid = new Zend_Dojo_Form_Element_FilteringSelect('co_id');
		$rows = $db ->getAllCOName();
		$options=array(''=>"------Select------",-1=>"Add New");
		if(!empty($rows))foreach($rows AS $row) $options[$row['co_id']]=$row['co_khname'];
		$_coid->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
				'readOnly'=>'readOnly',
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
		 						//'onchange'=>'getLoan(1);'
		));
		$_coid->setMultiOptions($options);
		
		$_cocode = new Zend_Dojo_Form_Element_FilteringSelect('co_code');
		$rows = $db ->getAllCOName();
		$options=array(''=>"------Select------",-1=>"Add New");
		if(!empty($rows))foreach($rows AS $row) $options[$row['co_id']]=$row['co_code'];
		$_cocode->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
				'readOnly'=>'readOnly',
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				//'onchange'=>'getLoan(2);'
		));
		$_cocode->setMultiOptions($options);
		
		$_priciple_amount = new Zend_Dojo_Form_Element_NumberTextBox('priciple_amount');
		$_priciple_amount->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'readOnly'=>'readOnly'
		));
		
		$_loan_fee = new Zend_Dojo_Form_Element_NumberTextBox('loan_fee');
		$_loan_fee->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'readOnly'=>'readOnly'
		));
		
		$_os_amount = new Zend_Dojo_Form_Element_NumberTextBox('os_amount');
		$_os_amount->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'readOnly'=>'readOnly',
				'required'=>true,
		));
		
		$_rate = new Zend_Dojo_Form_Element_NumberTextBox('total_interest');
		$_rate->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'required' =>'true',
				'style'=>'color:red;',
				'readOnly'=>'readOnly',
				'required'=>true,
		));
// 		$value_interest = 2.5;
// 		$_rate->setValue($value_interest);
		
		$_penalize_amount = new Zend_Dojo_Form_Element_NumberTextBox('penalize_amount');
		$_penalize_amount->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'required'=>true,
				'onKeyup'=>'doTotalBypene();'
		));
		$_penalize_amount->setValue(0);
		
		$_total_payment = new Zend_Dojo_Form_Element_NumberTextBox('total_payment');
		$_total_payment->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'required' =>'true',
				'style'=>'color:red;',
				'required'=>true,
				'readOnly'=>'readOnly'
		));
		
		$_hide_total_payment = new Zend_Form_Element_Text('hide_total_payment');
		$_hide_total_payment->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
		));
		
		$_note = new Zend_Dojo_Form_Element_TextBox('note');
		$_note->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
				//'required' =>'true'
		));
		
		$_collect_date = new Zend_Dojo_Form_Element_DateTextBox('collect_date');
		$_collect_date->setAttribs(array(
				'dojoType'=>'dijit.form.DateTextBox',
				'class'=>'fullside',
				'required' =>'true',
				'constraints'=>"{datePattern:'dd/MM/yyyy'}",
				'Onchange'	=>	'calculateTotal();'
		));
		$c_date = date('Y-m-d');
		$_collect_date->setValue($c_date);
		
// 		$date_input = new Zend_Dojo_Form_Element_DateTextBox('date_input');
// 		$date_input->setAttribs(array(
// 				'dojoType'=>'dijit.form.DateTextBox',
// 				'class'=>'fullside',
// 				'required' =>true
// 		));
// 		$date_input->setValue($c_date);

		$date_input = new Zend_Form_Element_Hidden("date_input");
		$date_input->setValue($c_date);
		
		$reciever = new Zend_Form_Element_Hidden("reciever");
		$reciever->setAttribs(array('dojoType'=>'dijit.form.TextBox'));
		
		$discount = new Zend_Dojo_Form_Element_TextBox("discount");
		$discount->setAttribs(array('dojoType'=>'dijit.form.TextBox','class'=>'fullside'));
		
		$reciept_no = new Zend_Dojo_Form_Element_TextBox("reciept_no");
		$reciept_no->setAttribs(array('dojoType'=>'dijit.form.TextBox','class'=>'fullside','readonly'=>true,
				'style'=>'color:red; font-weight: bold;'));
		$db_loan = new Loan_Model_DbTable_DbLoanILPayment();
		$loan_number = $db_loan->getIlPaymentNumber();
		$reciept_no->setValue($loan_number);
		$id = new Zend_Form_Element_Hidden("id");
		$id->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		
		$option_pay = new Zend_Dojo_Form_Element_FilteringSelect('option_pay');
		$option_pay->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
				'OnChange'=>'getPayOption();',
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
		));
		$option_status = array(1=>'បង់ធម្មតា',2=>'បង់មុន',3=>'បង់រំលស់ប្រាក់ដើម',4=>'បង់ផ្តាច់');
		$option_pay->setMultiOptions($option_status);
		
		$amount_payment_term = new Zend_Form_Element_Hidden("amount_payment_term");
		$amount_payment_term->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		
// 		$id = new Zend_Form_Element_Text('id');
// 		$id->setAttrib('dojoType', 'dijit.form.TextBox');
		
		$installment_date = new Zend_Form_Element_Hidden("installment_date");
		$installment_date->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		
		$old_tota_pay = new Zend_Form_Element_Text("oldTotalPay");
		$old_tota_pay->setAttribs(array('dojoType'=>'dijit.form.TextBox','class'=>'fullside',));
		
		$release_date = new Zend_Dojo_Form_Element_TextBox("release_date");
		$release_date->setAttribs(array('dojoType'=>'dijit.form.TextBox','class'=>'fullside','readOnly'=>'readOnly'));
		
		$old_release_date = new Zend_Form_Element_Hidden("old_release_date");
		$old_release_date->setAttribs(array('dojoType'=>'dijit.form.TextBox','class'=>'fullside','readOnly'=>'readOnly'));
		
		$loan_level= new Zend_Dojo_Form_Element_TextBox("load_level");
		$loan_level->setAttribs(array('dojoType'=>'dijit.form.TextBox','class'=>'fullside','readOnly'=>'readOnly'));
		
		$remain= new Zend_Dojo_Form_Element_NumberTextBox("remain");
		$remain->setAttribs(array('dojoType'=>'dijit.form.TextBox','class'=>'fullside','readOnly'=>'readOnly'));
		
		$total_amount_loan = new Zend_Dojo_Form_Element_TextBox("total_amount_loan");
		$total_amount_loan->setAttribs(array('dojoType'=>'dijit.form.TextBox','class'=>'fullside','readOnly'=>'readOnly'));
		
		$loan_period = new Zend_Dojo_Form_Element_TextBox("loan_period");
		$loan_period->setAttribs(array('dojoType'=>'dijit.form.TextBox','class'=>'fullside','readOnly'=>'readOnly'));
		
		$candition_payment = new Zend_Dojo_Form_Element_TextBox("pay_condition");
		$candition_payment->setAttribs(array('dojoType'=>'dijit.form.TextBox','class'=>'fullside','readOnly'=>'readOnly'));
		
		$options = $db->getAllPaymentMethod(null,1);
		$payment_method= new Zend_Dojo_Form_Element_FilteringSelect("payment_method");
		$payment_method->setAttribs(array('dojoType'=>'dijit.form.FilteringSelect','class'=>'fullside','readOnly'=>'readOnly',
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',));
		$payment_method->setMultiOptions($options);
		
		$using_date = new Zend_Dojo_Form_Element_DateTextBox("using_date");
		$using_date->setAttribs(array(
				'dojoType'=>'dijit.form.DateTextBox',
				'constraints'=>"{datePattern:'dd/MM/yyyy'}",
				'class'=>'fullside',
				'required' =>true
		));
		
		$_last_payment_date = new Zend_Form_Element_Hidden("last_payment_date");
		$_last_payment_date->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
				//'required' =>true
		));
		
		if($data!=""){
			$id->setValue($data["id"]);
// 			$_groupid->setValue($data["group_id"]);
// 			$_loan_number->setValue($data["loan_number"]);
// 			$_branch_id->setValue($data["branch_id"]);
// 			$_client_code->setValue($data["group_id"]);
// 			$reciept_no->setValue($data["receipt_no"]);
// 			$_coid->setValue($data["co_id"]);
// 			$option_pay->setValue($data["status"]);
// 			$_amount_receive->setValue($data["recieve_amount"]);
// 			$_amount_return->setValue($data["return_amount"]);
// 			$_penalize_amount->setValue($data["penalize_amount"]);
// 			$_total_payment->setValue($data["total_payment"]);
// 			$_priciple_amount->setValue($data["principal_amount"]);
// 			$_os_amount->setValue($data["total_principal_permonth"]);
// // 			$discount->setValue($data["total_discount"]);
// 			$_rate->setValue($data["total_interest"]);
// 			$_note->setValue($data["note"]);
// 			$date_input->setValue($data["date_input"]);
// 			$_collect_date->setValue(date("y-m-d"));
// 			$_service_charge->setValue($data["service_charge"]);
// 			$reciever->setValue($data["receiver_id"]);
// 			$_currency_type->setValue($data["currency_type"]);
// 			$amount_payment_term->setValue($data["amount_term"]);
// 			$_interest_rate->setValue($data["interest_rate"]);
// 			$_payterm->setValue($data["collect_typeterm"]);
// 			$_collect_date->setValue($data["date_pay"]);
// 			$old_tota_pay->setValue($data["total_payment"]-$data["service_charge"]);
		}
		$this->addElements(array($old_amount_receive,$old_loan_number,$_client_code,$_loan_number,$old_release_date,$old_service_charge,$old_penelize,$_cocode,$_last_payment_date,$using_date,
				$total_amount_loan,$loan_period,$candition_payment,$payment_method,$release_date,$loan_level,$remain,$old_tota_pay,
				$installment_date,$amount_payment_term,$_interest_rate,$_payterm,$_currency_type,$id,$option_pay,$date_input,$reciept_no,
				$reciever,$discount,$id,$_coid,$_priciple_amount,$_loan_fee,$_os_amount,$_rate,
				$_penalize_amount,$_collect_date,$_total_payment,$_note,$_service_charge,$_amount_return,
				$_amount_receive,$_branch_id,$_hide_total_payment));
		return $this;
	}
	public function quickPayment(){
		$db = new Application_Model_DbTable_DbGlobal();
		
		$branch_id = new Zend_Dojo_Form_Element_FilteringSelect("branch_id");
		$branch_id->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
				'required' =>'true',
				'OnChange'	=> 'filterCo();',
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
		));
		
		$rows = $db->getAllBranchName();
		$options=array(''=>'------Select------');
		if(!empty($rows))foreach($rows AS $row){
			$options[$row['br_id']]=$row['branch_namekh'];
		}
		$branch_id->setMultiOptions($options);
		
		$_coid = new Zend_Dojo_Form_Element_FilteringSelect('co_id');
		$rows = $db ->getAllCOName();
		$options=array(''=>"------Select------",-1=>"Add New");
		if(!empty($rows))foreach($rows AS $row) $options[$row['co_id']]=$row['co_khname'];
		$_coid->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				//'onchange'=>'getLoan();'
		));
		$_coid->setMultiOptions($options);
		
		$_cocode = new Zend_Dojo_Form_Element_FilteringSelect('co_code');
		$rows = $db ->getAllCOName();
		$options=array(''=>"------Select------",-1=>"Add New");
		if(!empty($rows))foreach($rows AS $row) $options[$row['co_id']]=$row['co_code'];
		$_cocode->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
				'onchange'=>'getLoan();',
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
		));
		$_cocode->setMultiOptions($options);
		
		
		$_priciple_amount = new Zend_Dojo_Form_Element_NumberTextBox('priciple_amount');
		$_priciple_amount->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'readOnly'=>'readOnly'
		));
		
		$_loan_fee = new Zend_Dojo_Form_Element_NumberTextBox('loan_fee');
		$_loan_fee->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'readOnly'=>'readOnly'
		));
		
		$_os_amount = new Zend_Dojo_Form_Element_NumberTextBox('os_amount');
		$_os_amount->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'readOnly'=>'readOnly',
				'required'=>true,
		));
		
		$_rate = new Zend_Dojo_Form_Element_NumberTextBox('total_interest');
		$_rate->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'required' =>'true',
				'style'=>'color:red;',
				'readOnly'=>'readOnly',
				'required'=>true,
		));
		// 		$value_interest = 2.5;
		// 		$_rate->setValue($value_interest);
		
		$_penalize_amount = new Zend_Dojo_Form_Element_NumberTextBox('penalize_amount');
		$_penalize_amount->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'required'=>true,
				'readOnly'=>'readOnly'
		));
		$_penalize_amount->setValue(0);
		
		$_total_payment = new Zend_Dojo_Form_Element_NumberTextBox('total_payment');
		$_total_payment->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'required' =>'true',
				'style'=>'color:red;',
				'required'=>true,
				'readOnly'=>'readOnly'
		));
		
		$_hide_total_payment = new Zend_Form_Element_Hidden('hide_total_payment');
		$_hide_total_payment->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
		));
		
		$_note = new Zend_Dojo_Form_Element_TextBox('note');
		$_note->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
				//'required' =>'true'
		));
		
		$_collect_date = new Zend_Dojo_Form_Element_DateTextBox('collect_date');
		$_collect_date->setAttribs(array(
				'dojoType'=>'dijit.form.DateTextBox',
				'class'=>'fullside',
				'required' =>'true',
				'constraints'=>"{datePattern:'dd/MM/yyyy'}",
				//'Onchange'	=>	'getLoan();calculateTotal();'
		));
		$c_date = date('Y-m-d');
		$_collect_date->setValue($c_date);
		
		$date_input = new Zend_Form_Element_Hidden('date_input');
		$date_input->setAttribs(array(
				//'dojoType'=>'dijit.form.DateTextBox',
				'class'=>'fullside',
				'required' =>true
		));
		$date_input->setValue($c_date);
		
		$_interest_rate = new Zend_Dojo_Form_Element_TextBox("interest_rate");
		$_interest_rate->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
				'required' =>'true'
		));
		
		$_service_charge = new Zend_Dojo_Form_Element_NumberTextBox('service_charge');
		$_service_charge->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'readOnly'=>'readOnly',
				//'onkeyUp'=>'totalReturn();'
		));
		$_service_charge->setValue(0);
		
		$total_amount_loan = new Zend_Dojo_Form_Element_TextBox("total_amount_loan");
		$total_amount_loan->setAttribs(array('dojoType'=>'dijit.form.TextBox','class'=>'fullside','readOnly'=>'readOnly'));
		
		$reciever = new Zend_Form_Element_Hidden("reciever");
		$reciever->setAttribs(array('dojoType'=>'dijit.form.TextBox'));
		
		$_amount_return = new Zend_Dojo_Form_Element_NumberTextBox('amount_return');
		$_amount_return->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'readOnly'=>'readOnly',
				//'onkeyup'	=>	'netReturn();'
		));
		$_rate = new Zend_Dojo_Form_Element_NumberTextBox('total_interest');
		$_rate->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'required' =>'true',
				'style'=>'color:red;',
				'readOnly'=>'readOnly',
				'required'=>true,
		));
		
		$_amount_receive = new Zend_Dojo_Form_Element_NumberTextBox('amount_receive');
		$_amount_receive->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'onKeyup'=>'netReturn();',
				'style'=>'color:red;',
				'required'=>true
		));
		
		$_currency_type = new Zend_Dojo_Form_Element_FilteringSelect('currency_type');
		$_currency_type->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				//'onchange'=>'getLoan();'
		));
		$opt = array(-1=>"--Select Currency Type--",2=>"Dollar",1=>'Khmer',3=>"Bath");
		$_currency_type->setMultiOptions($opt);
		$_currency_type->setValue(2);
		
		$reciever = new Zend_Form_Element_Hidden("reciever");
		$reciever->setAttribs(array('dojoType'=>'dijit.form.TextBox'));
		
		$id = new Zend_Form_Element_Hidden("id");
		$id->setAttribs(array('dojoType'=>'dijit.form.TextBox'));
		
		$this->addElements(array($id,$reciever,$_currency_type,$date_input,$_note,$_amount_receive,$_rate,$_amount_return,$_service_charge,$branch_id,$_cocode,$_coid,$_collect_date,$_os_amount,$_penalize_amount,$_priciple_amount,$_total_payment));
		return $this;
	}
}