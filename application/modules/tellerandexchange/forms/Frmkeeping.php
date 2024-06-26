<?php 
Class Tellerandexchange_Form_Frmkeeping extends Zend_Dojo_Form {
	protected $tr;
	public function init()
	{
		$this->tr = Application_Form_FrmLanguages::getCurrentlanguage();
	}
	public function FrmAddkeeping($data=null){
		
		$account_id = new Zend_Dojo_Form_Element_ValidationTextBox('account_id');
		$account_id->setAttribs(array(
				'dojoType'=>'dijit.form.ValidationTextBox',
				'class'=>'fullside',
				'required'=>true
				));
		
		
		$customer = new Zend_Dojo_Form_Element_ValidationTextBox('customer');
		$customer->setAttribs(array(
				'dojoType'=>'dijit.form.ValidationTextBox',
				'class'=>'fullside',
				'required'=>true
		));
		
		$for_date = new Zend_Dojo_Form_Element_FilteringSelect('for_date');
		$for_date->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
		));
		$options= array(1=>"1",2=>"2",3=>"3",4=>"4",5=>"5",6=>"6",7=>"7",8=>"8",9=>"9",10=>"10",11=>"11",12=>"12");
		$for_date->setMultiOptions($options);
		
		$_Date = new Zend_Dojo_Form_Element_DateTextBox('Date');
		$_Date->setAttribs(array(
				'dojoType'=>'dijit.form.DateTextBox',
				'constraints'=>"{datePattern:'dd/MM/yyyy'}",
				'required'=>true,
				'class'=>'fullside'
				
		));
		$_Date->setValue(date('Y-m-d'));
		
		$_branch_id = new Zend_Dojo_Form_Element_FilteringSelect('branch_id');
		$_branch_id->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'required' =>'true',
				'class'=>'fullside',
				'onchange'=>'filterClient();',
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
		));
		
		$db = new Application_Model_DbTable_DbGlobal();
		$rows = $db->getAllBranchName();
		$options=array(''=>$this->tr->translate("SELECT_BRANCH"));
		if(!empty($rows))foreach($rows AS $row){
			$options[$row['br_id']]=$row['branch_namekh'];
		}
		$_branch_id->setMultiOptions($options);
		
		
		$_stutas = new Zend_Dojo_Form_Element_FilteringSelect('Stutas');
		$_stutas ->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
		));
		$options= array(1=>$this->tr->translate("ACTIVE"),0=>$this->tr->translate("DEACTIVE"));
		$_stutas->setMultiOptions($options);
		
		$_Description = new Zend_Dojo_Form_Element_Textarea('Description');
		$_Description ->setAttribs(array(
				'dojoType'=>'dijit.form.SimpleTextarea',
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
		
	
		if($data!=null){
			
			$_branch_id->setValue($data['branch_id']);
			$customer->setValue($data['customer']);
			$_currency_type->setValue($data['curr_type']);
			$invoice->setValue($data['invoice']);
			$total_amount->setValue($data['total_amount']);
			$for_date->setValue($data['fordate']);
			$_Description->setValue($data['disc']);
			$_Date->setValue($data['date']);
			$_stutas->setValue($data['status']);
			$id->setValue($data['id']);
		}
		
		$this->addElements(array($customer,$invoice,$_currency_type,$account_id,$_Date ,$_stutas,$_Description,
				$total_amount,$_branch_id,$for_date,$id,));
		return $this;
		
	}	
}