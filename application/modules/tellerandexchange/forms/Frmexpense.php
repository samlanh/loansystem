<?php 
Class Tellerandexchange_Form_Frmexpense extends Zend_Dojo_Form {
	protected $tr;
	public function init()
	{
		$this->tr = Application_Form_FrmLanguages::getCurrentlanguage();
	}
	public function FrmAddExpense($data=null){
		
		$request=Zend_Controller_Front::getInstance()->getRequest();
		
		$account_id = new Zend_Dojo_Form_Element_ValidationTextBox('account_id');
		$account_id->setAttribs(array(
				'dojoType'=>'dijit.form.ValidationTextBox',
				'class'=>'fullside',
				'required'=>true,
				
				));
		
		$customer = new Zend_Dojo_Form_Element_ValidationTextBox('customer');
		$customer->setAttribs(array(
				'dojoType'=>'dijit.form.ValidationTextBox',
				'class'=>'fullside',
				'required'=>true
		));
		
		$today = date("Y-m-d");
		$for_date = new Zend_Dojo_Form_Element_FilteringSelect('for_date');
		$for_date->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				'constraints'=>"{max:'$today',datePattern:'dd/MM/yyyy'}",
		));
// 		$options= array(1=>"1",2=>"2",3=>"3",4=>"4",5=>"5",6=>"6",7=>"7",8=>"8",9=>"9",10=>"10",11=>"11",12=>"12");
// 		$for_date->setMultiOptions($options);
		
		$_Date = new Zend_Dojo_Form_Element_DateTextBox('Date');
		$_Date->setAttribs(array(
				'dojoType'=>'dijit.form.DateTextBox',
				'required'=>true,
				'class'=>'fullside',
				'constraints'=>"{max:'$today',datePattern:'dd/MM/yyyy'}",
				
		));
		$_Date->setValue(date('Y-m-d'));
		
		$session_user=new Zend_Session_Namespace(SYSTEM_SES);
		if($session_user->level!=1){
			$_Date->setAttribs(array(
					'readonly'=>true,
			));
		}
		
		$_branch_id = new Zend_Dojo_Form_Element_FilteringSelect('branch_id');
		$_branch_id->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'required' =>'true',
				'class'=>'fullside',
				'onchange'=>'getReceiptNo();',
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
		));
		
		$db = new Application_Model_DbTable_DbGlobal();
		$rows = $db->getAllBranchName();
		$options=array(''=>$this->tr->translate("PLEASE_SELECT_BRANCH"));
		if(!empty($rows))foreach($rows AS $row){
			$options[$row['br_id']]=$row['branch_namekh'];
		}
		$_branch_id->setMultiOptions($options);
		$_branch_id->setValue($request->getParam("branch_id"));
		
		
		$_stutas = new Zend_Dojo_Form_Element_FilteringSelect('Stutas');
		$_stutas ->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
		));
		$options= array(1=>"ប្រើប្រាស់",0=>"មិនប្រើប្រាស់");
		$_stutas->setMultiOptions($options);
		
		$_Description = new Zend_Dojo_Form_Element_Textarea('Description');
		$_Description ->setAttribs(array(
				'dojoType'=>'dijit.form.Textarea',
				'class'=>'fullside',
				'style'=>'width:98%',
		));
		$total_amount=new Zend_Dojo_Form_Element_NumberTextBox('total_amount');
		$total_amount->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'required'=>true
				));
		
		$invoice=new Zend_Dojo_Form_Element_TextBox('invoice');
		$invoice->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		
		$_reciept_no=new Zend_Dojo_Form_Element_TextBox('reciept_no');
		$_reciept_no->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
				'readOnly'=>true,
				'style'=>"color:red; font-weight: bold;",
		));
		
		
		$id = new Zend_Form_Element_Hidden("id");
		
		$_currency_type = new Zend_Dojo_Form_Element_FilteringSelect('currency_type');
		$_currency_type->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
		));
		$opt = $db->getVewOptoinTypeByType(15,1,3,1);
		$_currency_type->setMultiOptions($opt);
		$_currency_type->setValue(2);
		
		
		$dbcategory = new Tellerandexchange_Model_DbTable_DbCategory();
		$_category_id = new Zend_Dojo_Form_Element_FilteringSelect('category_id');
		$_category_id->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'required' =>'true',
				'class'=>'fullside',
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
		));
		
		$result = $dbcategory->getAllIncomeCategory(2);
		$options_category=array(''=>$this->tr->translate("PLEASE_SELECT_CATEGORY"));
		if(!empty($result))foreach($result AS $row){
			$options_category[$row['id']]=$row['name'];
		}
		$_category_id->setMultiOptions($options_category);
	
		$_category_id->setValue($request->getParam("category_id"));
		
		
		if($data!=null){
			
			$_branch_id->setValue($data['branch_id']);
			$_branch_id->setAttribs(array(
					'readOnly'=>true,
			));
			
			$account_id->setValue($data['account_id']);
			//$customer->setValue($data['customer']);
			$total_amount->setValue($data['total_amount']);
			$for_date->setValue($data['fordate']);
			$_Description->setValue($data['disc']);
			$_Date->setValue($data['date']);
			$_stutas->setValue($data['status']);
			$invoice->setValue($data['invoice']);
			$id->setValue($data['id']);
			$_reciept_no->setValue($data['reciept_no']);
			$_category_id->setValue($data['category_id']);
		}
		
		$this->addElements(array($customer,$invoice,$_currency_type,$account_id,$_Date ,$_stutas,$_Description,
				$total_amount,$_branch_id,$for_date,$id,
				$_reciept_no,$_category_id
				));
		return $this;
		
	}	
}