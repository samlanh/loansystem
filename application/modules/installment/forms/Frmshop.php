<?php 
Class Installment_Form_Frmshop extends Zend_Dojo_Form {
	protected $tr;
	protected $tvalidate =null;//text validate
	protected $filter=null;
	protected $t_num=null;
	protected $text=null;
	protected $tarea=null;
	public function init()
	{
		$this->tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$this->tvalidate = 'dijit.form.ValidationTextBox';
		$this->filter = 'dijit.form.FilteringSelect';
		$this->text = 'dijit.form.TextBox';
		$this->tarea = 'dijit.form.SimpleTextarea';
	}
	public function Frmshop($data=null){
		
		$request=Zend_Controller_Front::getInstance()->getRequest();
		
		$dbgb = new Application_Model_DbTable_DbGlobal();
		
		$_title = new Zend_Dojo_Form_Element_TextBox('adv_search');
		$_title->setAttribs(array('dojoType'=>$this->tvalidate,
				'onkeyup'=>'this.submit()',
				'class'=>'fullside',
				'placeholder'=>$this->tr->translate("SEARCH_BRANCH_INFO")
		));
		$_title->setValue($request->getParam("adv_search"));
		
		$_status=  new Zend_Dojo_Form_Element_FilteringSelect('status_search');
		$_status->setAttribs(array('dojoType'=>$this->filter,'autoComplete'=>"false" ,'queryExpr'=>'*${0}*','class'=>'fullside',));
		$_status_opt = array(
				-1=>$this->tr->translate("ALL"),
				1=>$this->tr->translate("ACTIVE"),
				0=>$this->tr->translate("DACTIVE"));
		$_status->setMultiOptions($_status_opt);
		$_status->setValue($request->getParam("status_search"));
		
		$_btn_search = new Zend_Dojo_Form_Element_SubmitButton('btn_search');
		$_btn_search->setAttribs(array(
				'dojoType'=>'dijit.form.Button',
				'iconclass'=>'dijitIconSearch',
				'value'=>' Search ',
		
		));
		
		$br_id = new Zend_Dojo_Form_Element_TextBox('br_id');
		$br_id->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
				'readOnly'=>'readOnly',
				'style'=>'color:red',
				'onkeyup'=>'Calcuhundred()'
				));
		$br_code=Other_Model_DbTable_DbBranch::getBranchCode();
		$br_id->setValue($br_code);
		
		$branch_namekh = new Zend_Dojo_Form_Element_ValidationTextBox('branch_namekh');
		$branch_namekh->setAttribs(array(
				'dojoType'=>'dijit.form.ValidationTextBox',
				'class'=>'fullside',
				'required'=>true,
				));

		$branch_nameen = new Zend_Dojo_Form_Element_ValidationTextBox('branch_nameen');
		$branch_nameen->setAttribs(array(
				'dojoType'=>'dijit.form.ValidationTextBox',
				'class'=>'fullside',
				));
		
// 		$db=new Report_Model_DbTable_DbParamater();
// 		$rows=$db->getAllBranch();
// 		$opt_branch = array(''=>$this->tr->translate("SELECT_BRANCH_NAME"));
// 		if(!empty($rows))foreach($rows AS $row) $opt_branch[$row['br_id']]=$row['branch_namekh'];
		
		$branch_id = new Zend_Dojo_Form_Element_FilteringSelect('branch_id');
		$branch_id->setAttribs(array(
				'dojoType'=>$this->filter,
				'class'=>'fullside',
				'required'=>true,
				'autoComplete'=>"false" ,'queryExpr'=>'*${0}*',
				'onkeyup'=>'Caltweenty()'
		));
		$rows = $dbgb->getAllBranchName();
		$opt_branch=array(''=>$this->tr->translate("Choose Branch"));
		if(!empty($rows))foreach($rows AS $row){
			$opt_branch[$row['br_id']]=$row['branch_namekh'];
		}
		$branch_id->setMultiOptions($opt_branch);
		$branch_id->setValue($request->getParam('branch_id'));
		
		$branch_code = new Zend_Dojo_Form_Element_NumberTextBox('branch_code');
		$branch_code->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
				'readOnly'=>'readOnly',
				'style'=>'color:red',
				'onkeyup'=>'Calcuhundred()'
				));
		$db_code=Other_Model_DbTable_DbBranch::getBranchCode();
		$branch_code->setValue($db_code);
		
		$branch_tel = new Zend_Dojo_Form_Element_NumberTextBox('branch_tel');
		$branch_tel->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
				));
		
		$_fax = new Zend_Dojo_Form_Element_TextBox('fax ');
		$_fax->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
				));
		
		///*** result of calculator ///***
		$branch_note = new Zend_Dojo_Form_Element_TextBox('branch_note');
		$branch_note->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
// 				'readonly'=>true
				));
		
		$prefix_code = new Zend_Dojo_Form_Element_ValidationTextBox('prefix_code');
		$prefix_code->setAttribs(array(
				'dojoType'=>'dijit.form.ValidationTextBox',
				'class'=>'fullside',
		));
		
		
		$branch_status = new Zend_Dojo_Form_Element_FilteringSelect('branch_status');
		$branch_status->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
				'autoComplete'=>"false" ,'queryExpr'=>'*${0}*',
// 				'readonly'=>true
				));
		$options = array(1=>$this->tr->translate("ACTIVE"),
					     0=>$this->tr->translate("DACTIVE"));
		$branch_status->setMultiOptions($options);
		
		$branch_display = new Zend_Dojo_Form_Element_FilteringSelect('branch_display');
		$branch_display->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
				'autoComplete'=>"false" ,'queryExpr'=>'*${0}*',
				));
		$_display_opt = array(
				1=>$this->tr->translate("NAME_KHMER"),
				2=>$this->tr->translate("NAME_EN"));
		$branch_display->setMultiOptions($_display_opt);
		
		$br_address = new Zend_Dojo_Form_Element_TextBox('br_address');
		$br_address->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
	
		$_description = new Zend_Dojo_Form_Element_Textarea('description');
		$_description->setAttribs(array(
				'dojoType'=>'dijit.form.Textarea',
				'class'=>'fullside',
				'style'=>'min-height: 50px;'
		));
		
		
		$gm_name = new Zend_Dojo_Form_Element_TextBox('gm_name');
		$gm_name->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		$_gm_sex = new Zend_Dojo_Form_Element_FilteringSelect('gm_sex');
		$_gm_sex->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
		));
		$opt_status = $dbgb->getVewOptoinTypeByType(11,1);
		unset($opt_status[-1]);
		unset($opt_status['']);
		$_gm_sex->setMultiOptions($opt_status);
		
		$gm_dob= new Zend_Dojo_Form_Element_DateTextBox('gm_dob');
		$gm_dob->setAttribs(array('dojoType'=>'dijit.form.DateTextBox',
				'constraints'=>"{datePattern:'dd/MM/yyyy'}",'class'=>'fullside',
		));
		$gm_nationality = new Zend_Dojo_Form_Element_TextBox('gm_nationality');
		$gm_nationality->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		$gm_nationality->setValue("ខ្មែរ");
		
		$gm_nation_id = new Zend_Dojo_Form_Element_TextBox('gm_nation_id');
		$gm_nation_id->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		$gm_issue_date = new Zend_Dojo_Form_Element_DateTextBox('gm_issue_date');
		$gm_issue_date->setAttribs(array('dojoType'=>'dijit.form.DateTextBox',
				'constraints'=>"{datePattern:'dd/MM/yyyy'}",'class'=>'fullside',
		));
		
		$gm_occupation = new Zend_Dojo_Form_Element_TextBox('gm_occupation');
		$gm_occupation->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		
		$_gm_address = new Zend_Dojo_Form_Element_Textarea('gm_address');
		$_gm_address->setAttribs(array(
				'dojoType'=>'dijit.form.Textarea',
				'class'=>'fullside',
				'style'=>'min-height: 50px;'
		));
		
		$with_gm_name = new Zend_Dojo_Form_Element_ValidationTextBox('with_gm_name');
		$with_gm_name->setAttribs(array(
				'dojoType'=>'dijit.form.ValidationTextBox',
				'class'=>'fullside',
		));
		$_with_gm_sex = new Zend_Dojo_Form_Element_FilteringSelect('with_gm_sex');
		$_with_gm_sex->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
		));
		$opt_status = $dbgb->getVewOptoinTypeByType(11,1);
		unset($opt_status[-1]);
		unset($opt_status['']);
		$_with_gm_sex->setMultiOptions($opt_status);
		
		$with_gm_dob= new Zend_Dojo_Form_Element_DateTextBox('with_gm_dob');
		$with_gm_dob->setAttribs(array('dojoType'=>'dijit.form.DateTextBox',
				'constraints'=>"{datePattern:'dd/MM/yyyy'}",'class'=>'fullside',
		));
		$with_gm_nationality = new Zend_Dojo_Form_Element_TextBox('with_gm_nationality');
		$with_gm_nationality->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		$with_gm_nationality->setValue("ខ្មែរ");
		
		$with_gm_nation_id = new Zend_Dojo_Form_Element_TextBox('with_gm_nation_id');
		$with_gm_nation_id->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		$with_gm_issue_date = new Zend_Dojo_Form_Element_DateTextBox('with_gm_issue_date');
		$with_gm_issue_date->setAttribs(array('dojoType'=>'dijit.form.DateTextBox',
				'constraints'=>"{datePattern:'dd/MM/yyyy'}",'class'=>'fullside',
		));
		
		$with_gm_occupation = new Zend_Dojo_Form_Element_TextBox('with_gm_occupation');
		$with_gm_occupation->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		
		$with_gm_is = new Zend_Dojo_Form_Element_TextBox('with_gm_is');
		$with_gm_is->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		
		$_id = new Zend_Form_Element_Hidden('id');
		if(!empty($data)){
			$br_id->setValue($data['shop_id']);
			$prefix_code->setValue($data['prefix']);
			$branch_namekh->setValue($data['shop_namekh']);
			$branch_nameen->setValue($data['shop_nameen']);
			$branch_id->setValue($data['branch_id']);
			$br_address->setValue($data['br_address']);
			$branch_tel->setValue($data['branch_tel']);
			$branch_code->setValue($data['branch_code']);
			$_fax->setValue($data['fax']);
			$branch_note->setValue($data['other']);
			$branch_status->setValue($data['status']);
			$branch_display->setValue($data['displayby']);
			$_description->setValue($data['description']);
			
			$gm_name->setValue($data['gm_name']);
			$_gm_sex->setValue($data['gm_sex']);
			if (!empty($data['gm_dob'])){
			$gm_dob->setValue(date("Y-m-d",strtotime($data['gm_dob'])));
			}
			$gm_nationality->setValue($data['gm_nationality']);
			$gm_nation_id->setValue($data['gm_nation_id']);
			$gm_issue_date->setValue($data['gm_issue_date']);
			$gm_occupation->setValue($data['gm_occupation']);
			$_gm_address->setValue($data['gm_address']);
			$with_gm_name->setValue($data['with_gm_name']);
			$_with_gm_sex->setValue($data['with_gm_sex']);
			if (!empty($data['with_gm_dob'])){
			$with_gm_dob->setValue(date("Y-m-d",strtotime($data['with_gm_dob'])));
			}
			$with_gm_nationality->setValue($data['with_gm_nationality']);
			$with_gm_nation_id->setValue($data['with_gm_nation_id']);
			$with_gm_issue_date->setValue($data['with_gm_issue_date']);
			$with_gm_occupation->setValue($data['with_gm_occupation']);
			$with_gm_is->setValue($data['with_gm_is']);
		}
		
		$this->addElements(array($branch_id,$prefix_code,$_btn_search,$_title,$_status,$br_id,$branch_namekh,$branch_nameen,$br_address,$branch_code,$branch_tel,$_fax ,$branch_note,
				$branch_status,$branch_display,$_description,
				
				$gm_name,
				$_gm_sex,
				$gm_dob,
				$gm_nationality,
				$gm_nation_id,
				$gm_issue_date,
				$gm_occupation,
				$_gm_address,
				$with_gm_name,
				$_with_gm_sex,
				$with_gm_dob,
				$with_gm_nationality,
				$with_gm_nation_id,
				$with_gm_issue_date,
				$with_gm_occupation,
				$with_gm_is
				));
		
		return $this;
		
	}
	
}