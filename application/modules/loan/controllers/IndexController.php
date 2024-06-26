<?php
class Loan_IndexController extends Zend_Controller_Action {
    public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
    	$this->tr = Application_Form_FrmLanguages::getCurrentlanguage();
	}
	public function indexAction(){
		try{
		    if($this->getRequest()->isPost()){
 				$search = $this->getRequest()->getPost();
 			}
			else{
				$search = array(
					'txt_search'=>'',
					'deposit'=>'',
					'client_name'=> -1,
					'repayment_method' => -1,
					'branch_id' => -1,
					'co_id' => -1,
					'status' => -1,
					'currency_type'=>-1,
					'pay_every'=>-1,
					'start_date'=> date('Y-m-d'),
					'end_date'=>date('Y-m-d'),
				);
			}
			$db = new Loan_Model_DbTable_DbLoanIL();
			$rs_rows= $db->getAllIndividuleLoan($search);
			$list = new Application_Form_Frmtable();
			$collumns = array("BRANCH_NAME","LOAN_NO","CUSTOMER_NAME","LOAN_AMOUNT","INTEREST_RATE","ADMIN_FEE","REPAYMENT_TYPE","TERM_BORROW","ZONE_NAME","CO_NAME",
				"RELEASED_DATE","COMPLETED","Bad Loan","USER","STATUS");
			//,"SCHEDULE_PAYMENT","ADD_PAYMENT"
			$link=array(
					'module'=>'loan','controller'=>'index','action'=>'view',
			);
			$link_info=array('module'=>'loan','controller'=>'index','action'=>'edit',);
			//$link_schedule=array('module'=>'report','controller'=>'loan','action'=>'rpt-paymentschedules',);
				
			
			//$link_payment=array('module'=>'loan','controller'=>'payment','action'=>'add',);
			//'បោះពុម្ភ'=>$link_schedule,'Click Here'=>$link_payment,
			$this->view->list=$list->getCheckList(10, $collumns, $rs_rows,array('branch'=>$link,'loan_number'=>$link,'payment_method'=>$link_info,'client_name_kh'=>$link_info,'client_name_en'=>$link_info,'total_capital'=>$link_info),0);
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}	
		$frm = new Loan_Form_FrmSearchLoan();
		$frm = $frm->AdvanceSearch();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_search = $frm;
		$db = new Application_Model_DbTable_DbGlobal();
  }
  
  function addAction()
  {
  	$dbs = new Application_Model_DbTable_DbKeycode();
  	$rsd = $dbs->getKeyCodeMiniInv();
		if($this->getRequest()->isPost()){
			$_data = $this->getRequest()->getPost();
			try {
				$_dbmodel = new Loan_Model_DbTable_DbLoandisburse();//new
				$loan_id = $_dbmodel->addNewLoanIL($_data);
				if(!empty($_data['saveclose'])){
					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/loan");
				}else{
					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/loan/index/add");
				}
				Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/loan/index/add");
				return $loan_id;
			}catch (Exception $e) {
				Application_Form_FrmMessage::message("INSERT_FAIL");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		$frm = new Loan_Form_FrmLoan();
		$frm_loan=$frm->FrmAddLoan();
		Application_Model_Decorator::removeAllDecorator($frm_loan);
		$this->view->frm_loan = $frm_loan;

		$frmpopup = new Application_Form_FrmPopupGlobal();
		$this->view->frmpupoploantype = $frmpopup->frmPopupLoanTye();
		$this->view->frmPopupZone = $frmpopup->frmPopupZone();
		$this->view->frmpupopinfoclient = $frmpopup->frmPopupindividualclient();
		$this->view->frmPopupCO = $frmpopup->frmPopupCO();
		
		$db = new Setting_Model_DbTable_DbLabel();
		$this->view->setting=$db->getAllSystemSetting();
		
		$db = new Application_Model_DbTable_DbGlobal();
		$co_name = $db->getAllCoNameOnly();
		array_unshift($co_name,array(
		        'id' => -1,
		        'name' => $this->tr->translate("ADD_NEW"),
		) );
	    $this->view->co_name=$co_name;
	}
	public function submitloanAction(){
		if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();
			$_dbmodel = new Loan_Model_DbTable_DbLoandisburse();//new
			$loan_id = $_dbmodel->addNewLoanIL($data);
			$suc = array('sms'=>'ប្រាក់ឥណទានត្រូវបានបញ្ចូលដោយជោគជ័យ !');
			print_r(Zend_Json::encode($loan_id));
			exit();
		}
	}	
	
	public function addloanAction(){
		if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();
			$db = new Loan_Model_DbTable_DbLoan();
			$id = $db->addNewLoanGroup($data);
			$suc = array('sms'=>'ប្រាក់ឥណទានត្រូវបានបញ្ចូលដោយជោគជ័យ !');
			print_r(Zend_Json::encode($suc));
			exit();
		}
	}
	public function editAction(){
		if($this->getRequest()->isPost()){
			$_data = $this->getRequest()->getPost();
			try{
				$_dbmodel = new Loan_Model_DbTable_DbLoandisburse();
				$_dbmodel->updateLoanById($_data);
				Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/loan/index/index");
			}catch (Exception $e) {
				Application_Form_FrmMessage::message("INSERT_FAIL");
				Application_Model_DbTable_DbUserLog::writeMessageError($err =$e->getMessage());
			}
		}
		$id = $this->getRequest()->getParam('id');
		$db_g = new Application_Model_DbTable_DbGlobal();
		$rs = $db_g->getLoanFundExist($id);
		if($rs==true){ 	Application_Form_FrmMessage::Sucessfull("LOAN_FUND_EXIST","/loan/index/index");}
		$db = new Loan_Model_DbTable_DbLoandisburse();
		$row = $db->getTranLoanByIdWithBranch($id,1);
		if(empty($row)){
			Application_Form_FrmMessage::Sucessfull("NO_RECORD","/loan/index/index",2);
			exit();
		}
		
		$frm = new Loan_Form_FrmLoan();
		$frm_loan=$frm->FrmAddLoan($row);
		Application_Model_Decorator::removeAllDecorator($frm_loan);
		$this->view->frm_loan = $frm_loan;
		$this->view->datarow = $row;
		
		$db = new Application_Model_DbTable_DbGlobal();
		$this->view->allclient = $db->getAllClient();
		$this->view->allclient_number = $db->getAllClientNumber();
		
		$db = new Application_Model_DbTable_DbGlobal();
		$co_name = $db->getAllCoNameOnly();
		array_unshift($co_name,array(
				'id' => -1,
				'name' => $this->tr->translate("ADD_NEW"),
		) );
		$this->view->co_name=$co_name;
		
		$frmpopup = new Application_Form_FrmPopupGlobal();
		$this->view->frmpupoploantype = $frmpopup->frmPopupLoanTye();
		$this->view->frmPopupZone = $frmpopup->frmPopupZone();
	}
	public function viewAction(){
		$id = $this->getRequest()->getParam('id');
		$id = empty($id) ? 0 : $id;
		$db_g = new Application_Model_DbTable_DbGlobal();
		if(empty($id)){
			Application_Form_FrmMessage::Sucessfull("NO_RECORD","/loan/index/index",2);
			exit();
		}
		$db = new Loan_Model_DbTable_DbLoandisburse();
		$row = $db->getLoanviewById($id);
		$this->view->tran_rs = $row;
	}
	function getLoanlevelAction(){
		if($this->getRequest()->isPost()){
				$data = $this->getRequest()->getPost();
				$db = new Loan_Model_DbTable_DbLoanIL();
				$row = $db->getLoanLevelByClient($data['client_id'],$data['type']);
				print_r(Zend_Json::encode($row));
			    exit();
		}
	}
	public function getLoaninfoAction(){//from repayment schedule
		if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();
			$db=new Loan_Model_DbTable_DbRepaymentSchedule();
			$row=$db->getLoanInfo($data['loan_id']);
			print_r(Zend_Json::encode($row));
			exit();
		}
	}
	function getloanBymemberidAction(){
		if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();
			$db=new Loan_Model_DbTable_DbRepaymentSchedule();
			$row=$db->getLoanInfoBymemberId($data['loan_id']);
			print_r(Zend_Json::encode($row));
			exit();
		}
	}
// 	function getLoannumberAction(){
// 		if($this->getRequest()->isPost()){
// 			$data = $this->getRequest()->getPost();
// 			$db = new Loan_Model_DbTable_DbLoanIL();
// 			$row = $db->getLoanPaymentByLoanNumber($data['loan_number']);
// 			print_r(Zend_Json::encode($row));
// 			exit();
// 		}
// 	}
    function getloannumberAction(){
    			if($this->getRequest()->isPost()){
    				$data = $this->getRequest()->getPost();
    				$db = new Application_Model_DbTable_DbGlobal();
		            $loan_number = $db->getLoanNumber($data);
    				print_r(Zend_Json::encode($loan_number));
    				exit();
    			}
    }
	public function testAction($result=null,$table='ln_branch'){

	}
	function addloantestAction(){
		if($this->getRequest()->isPost()){
			$_data = $this->getRequest()->getPost();
				$_dbmodel = new Loan_Model_DbTable_DbLoanILtest();
				$rows_return=$_dbmodel->addNewLoanILTest($_data);
				print_r(Zend_Json::encode($rows_return));
				exit();
		}
	}
	function addNewloantypeAction(){
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$data['status']=1;
			$data['display_by']=1;
			$db = new Other_Model_DbTable_DbLoanType();
			$id = $db->addViewType($data);
			
			$info = $db->getListViewById($id);
			$keycode = empty($info['key_code'])?0:$info['key_code'];
			
			$type = $data['type'];
			$dbGb = new Application_Model_DbTable_DbGlobal();
			$rs= $dbGb->getVewByType($type);
			array_unshift($rs,array ( 'id' => "",'name' => $this->tr->translate("PLEASE_SELECT")), array ( 'id' => -1,'name' => $this->tr->translate("ADD_NEW")));
			$arr=array(
					'datastore' =>$rs,
					'key_code'		=>$keycode
					);
			
			print_r(Zend_Json::encode($arr));
			exit();
		}
	}
	
	function getConameAction(){
		if($this->getRequest()->isPost()){
			$_data = $this->getRequest()->getPost();
			$branch_id = !empty($_data['branch_id'])?$_data['branch_id']:0;
			$db = new Application_Model_DbTable_DbGlobal();
			$co_name = $db->getAllCoNameOnly($branch_id);
			$optionss = $db ->getAllCOName(1,$branch_id);
			array_unshift($co_name,array(
					'id' => -1,
					'name' => $this->tr->translate("ADD_NEW"),
			) );
			print_r(Zend_Json::encode($co_name));
			exit();
		}
	}
	function getallloanbybranchAction(){
		if($this->getRequest()->isPost()){
			$_data = $this->getRequest()->getPost();
			$db = new Application_Model_DbTable_DbGlobal();
			$rs = $db->getAllLoanbybranch($_data['branch_id']);
			print_r(Zend_Json::encode($rs));
			exit();
		}
	}
}

