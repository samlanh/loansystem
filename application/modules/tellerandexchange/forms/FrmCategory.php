<?php 
Class Tellerandexchange_Form_FrmCategory extends Zend_Dojo_Form {
	protected $tr;
	public function init()
	{
		$this->tr = Application_Form_FrmLanguages::getCurrentlanguage();
	}
	public function FrmAddCategory($data=null){
		
		$title = new Zend_Dojo_Form_Element_ValidationTextBox('title');
		$title->setAttribs(array(
				'dojoType'=>'dijit.form.ValidationTextBox',
				'class'=>'fullside',
				'required'=>true,
		
		));
		
		$_types = new Zend_Dojo_Form_Element_FilteringSelect('type');
		$_types ->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				'onChange'=>'getParentCategory();'
		));
		$options_type= array(1=>$this->tr->translate("INCOME"),2=>$this->tr->translate("EXPENSE"));
		$_types->setMultiOptions($options_type);
		
		
		$_stutas = new Zend_Dojo_Form_Element_FilteringSelect('stutas');
		$_stutas ->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
		));
		$options= array(1=>$this->tr->translate("ACTIVE"),0=>$this->tr->translate("DEACTIVE"));
		$_stutas->setMultiOptions($options);
		
		$_note = new Zend_Dojo_Form_Element_Textarea('note');
		$_note ->setAttribs(array(
				'dojoType'=>'dijit.form.Textarea',
				'class'=>'fullside',
				'style'=>'width:99%',
		));
		
		
		$id = new Zend_Form_Element_Hidden("id");
		
	
		if($data!=null){
			
			$title->setValue($data['title']);
			$_types->setValue($data['type']);
			$_types ->setAttribs(array(
					'readOnly'=>'readOnly',
			));
			$_stutas->setValue($data['status']);
			$_note->setValue($data['note']);
			$id->setValue($data['id']);
		}
		
		$this->addElements(array(
				$title,
				$_types,
				$_stutas,
				$_note,
				$id,
				));
		return $this;
		
	}	
}