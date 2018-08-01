<?php
class Tellerandexchange_TransfercashController extends Zend_Controller_Action
{

	const REDIRECT_URL = '/transfer';
	private $activelist = array('មិនប្រើ​ប្រាស់', 'ប្រើ​ប្រាស់');
	private $user_typelist = array();
	
    private $statuslist = array(
    	'ផ្ញើរ',
    	'ផ្ញើររួច',
        'ទទួល',
        'បើក​រួច',
        'មោឃៈ',
        'ផុត​កំណត់'
        );

    private $loanlist = array(
        'មិនជំពាក់',
        'ជំពាក់'
        );
        
    private $tran_typelist = array(
        'ផ្ញើរ',
        'ទទួល'
        );

    public function init()
    {
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
    	 
    }

    public function indexAction()
    {
        // action body	
        //Get Session User
    	$trans = array();
        try {
	        $db_tran=new Tellerandexchange_Model_DbTable_DbTransactioncash();
	        
	        if($this->getRequest()->isPost()){        	
	        	$formdata=$this->getRequest()->getPost();
	       		
	       		$this->view->txtsearch  = $formdata['txt_search'];  
	        	$this->view->status = $formdata['status'];
	        	$this->view->type_money = $formdata['type_money'];
	        	$this->view->agent = $formdata['agent'];
	        	$this->view->province =  $formdata['province'];
	        	$this->view->loan =  $formdata['status_loan'];
	        }
	        else{
	        	$formdata = array(
	        						'txt_search'=>"",
	        						'status'=>-1,
						        	'status_loan'=>-1,
						        	'province'=>-1,
						        	'agent'=>-1,
	        						'from_date'=>date('Y-m-d'),
						        	'to_date'=>date('Y-m-d'),
	        						'type_money'=>-1
	        					  );
	        	
	        }
	        $trans = $db_tran->getTransactionListTotal($formdata);
	        
	        $this->view->from_date=$formdata['from_date'];
	        $this->view->to_date=$formdata['to_date'];
	        $this->view->status =$formdata['status'];
	        $this->view->statuslist =$this->statuslist ;
	        $this->view->type_money = $formdata['type_money'];
	        $this->view->txtsearch  = $formdata['txt_search'];
	        $this->view->loan = $formdata['status_loan'] ;
	        $this->view->loanlist =  $this->loanlist;
	        
	        $cur = new Application_Model_DbTable_DbCurrencies();
	        $this->view->currencylist = $cur->getCurrencyList();
// 	        $this->view->province = $session_transfer->province;
// 	        $this->view->agent = $session_transfer->agent;
	       
	       
	         
	        $pro = new Application_Model_DbTable_DbProvinces();
	        $this->view->provincelist = $pro->getProvinceList();
	       
	        	
	        $agent = new Application_Model_DbTable_DbAgents();
	        $this->view->agentlist = $agent->getAgentListSelect();
	       
	        
	        if (!empty($trans)){
	        }
	        else{
	        	$result = array('err'=>1, 'msg'=>'មិន​ទាន់​មាន​ទន្និន័យ​នូវ​ឡើយ​ទេ!');
	        }
	       
	        $this->view->trancounter = $db_tran->getTranCounter();

        } catch (Exception $e) {
        }	
        $list = new Application_Form_Frmtable();
        $collumns = array("ឈ្មោះ​អ្នក​ផ្ញើរ","ឈ្មោះ​អ្នក​ទទួល","លេខ​​​អ្នក​ទទួល","លេខវិក័យបត្រ","ចំនួនទឺកប្រាក់","សេវាកម្ម","ឈ្មោះ​សាខា","ថ្ងៃ​ប្រតិបត្តិ","ថ្ងៃផុតកំណត់","ស្ថានការណ៍","ជំពាក់");
        $link=array(
        		'module'=>'subagent','controller'=>'index','action'=>'edited',
        );
        $this->view->list=$list->getCheckList(0, $collumns,$trans,array('name'=>$link,'agent_name'=>$link));
    }

    public function editedAction()
    {
        // action body    
        //Get value from url
        $mt_id = $this->getRequest()->getParam('mt_id');
        $mt_id = (empty($mt_id))? 0 : $mt_id; 
        
        $db_mtran=new Tellerandexchange_Model_DbTable_DbTransactioncash();
//         $exist=$db_mtran->TransFundExist($mt_id);
        if(empty($mt_id)){
        	Application_Form_FrmMessage::Sucessfull('មិនអាចទាញយកទិន្នន័យមកកែប្រែបានទេ !', self::REDIRECT_URL);//សំរាប់អតិជនពីមុនពេលសងខ្លះហើយ មិនអាចកែប្រែបានទេ
        }
        
        $session_user=new Zend_Session_Namespace('auth');
        $this->view->user_name = $session_user->last_name .' '. $session_user->first_name;
        
        $db_keycode = new Application_Model_DbTable_DbKeycode();
		$this->view->keycode = $db_keycode->getKeyCodeMiniInv();
        
        $this->view->tran_typelist = $this->tran_typelist;
        
    	$pro = new Application_Model_DbTable_DbProvinces();
		$this->view->provinces = $pro->getProvinceList();
		
		$cur = new Application_Model_DbTable_DbCurrencies();
		$this->view->currency = $cur->getCurrencyList();
		
		$subagent = new Application_Model_DbTable_DbSubAgent();
		$this->view->subagent = $subagent->getSubAgentListSelectTrns();
		
		$agent = new Application_Model_DbTable_DbAgents();
		$this->view->agent = $agent->getAgentListSelectTrns();
		
		$sender = new Application_Model_DbTable_DbSender();
		$this->view->sender = $sender->getAllSenderName();
		//print_r( $sender->getAllSenderName());
		
		$db_rate=new Application_Model_DbTable_DbRate();
		$this->view->rate =  $db_rate->getCurrentRate();
		
		$this->view->editdata = $db_mtran->getTransactionDetailByID($mt_id);
		
		$this->view->statuslist =  $this->statuslist;
		
		if($this->getRequest()->isPost()){
			$formdata=$this->getRequest()->getPost();	
// 			print_r($formdata); exit();		
			try {
				$db = $db_mtran->updateMoneyTransfer($formdata);
				Application_Form_FrmMessage::Sucessfull('ការ​បញ្ចូល​​ជោគ​ជ័យ', self::REDIRECT_URL);			
			} catch (Exception $e) {
				$this->view->msg = 'ការ​បញ្ចូល​មិន​ជោគ​ជ័យ';
			}
		}        
    }

    public function addAction()
    {
       
		
		if($this->getRequest()->isPost()){
			$formdata=$this->getRequest()->getPost();	
// 			print_r($formdata); exit();
			//$ans = false;					
			try {
				$db_mtran=new Tellerandexchange_Model_DbTable_DbTransactioncash();
				$id = $db_mtran->dectectMoneyTransaction($formdata);
				if(!empty($id) && empty($formdata['confirmForm']) ){
// 				if(empty($formdata['confirmForm']) ){
					$msg="លេខ​ទូរស័ព្ទ​​អ្នក​ទទួល​នេះ​មាន​ម្តងរួច​ហើយ, តើ​អ្នក​ចង់​បញ្ចូល​វា​ម្ដង​ទៀត​មែន​ទេ?";
					$com = (empty($formdata['commission']))? '' : $formdata['commission'];
					$loan = (empty($formdata['loan']))? '' : $formdata['loan'];
					$is_kbank = (empty($formdata['is_kbank']))? 0 : $formdata['is_kbank'];
					
					$minus = (empty($formdata['minus']))? '' : $formdata['minus'];
					$sub_agent_id = (empty($formdata['sub_agent_id']))? '' : $formdata['sub_agent_id'];
					$base_url = Application_Form_FrmMessage::getUrl("/");
					print("<form action='".$base_url."transfer/index/add' method='post'>
								<input type='hidden' name='tran_type' value='".$formdata['tran_type']."' />
								<input type='hidden' name='province' value='".$formdata['province']."' />
								<input type='hidden' name='agent_id' value='".$formdata['agent_id']."' />
								<input type='hidden' name='sub_agent_id' value='".$sub_agent_id."' />
								<input type='hidden' name='sender' value='".$formdata['sender']."' />
								<input type='hidden' name='reciever' value='".$formdata['reciever']."' />
								<input type='hidden' name='reciever_tel' value='".$formdata['reciever_tel']."' />
								<input type='hidden' name='send_date' value='".$formdata['send_date']."' />
								<input type='hidden' name='epx_date' value='".$formdata['epx_date']."' />
								<input type='hidden' name='type_money' value='".$formdata['type_money']."' />
								<input type='hidden' name='amount' value='".$formdata['amount']."' />
								<input type='hidden' name='rate_perday' value='".$formdata['rate_perday']."' />
								<input type='hidden' name='commission' value='".$com."' />
								<input type='hidden' name='commission_agent' value='".$formdata['commission_agent']."' />
								<input type='hidden' name='gave' value='".$formdata['gave']."' />
								<input type='hidden' name='total' value='".$formdata['total']."' />
								<input type='hidden' name='recieve_money' value='".$formdata['recieve_money']."' />
								<input type='hidden' name='return_money' value='".$formdata['return_money']."' />
								<input type='hidden' name='invoice_no' value='".$formdata['invoice_no']."' />
							
								<input type='hidden' name='debt_dollar' value='".$formdata['debt_dollar']."' />
								<input type='hidden' name='debt_bath' value='".$formdata['debt_bath']."' />
								<input type='hidden' name='debt_khmer' value='".$formdata['debt_khmer']."' />
								<input type='hidden' name='BD' value='".$formdata['BD']."' />
								<input type='hidden' name='RD' value='".$formdata['RD']."' />
								<input type='hidden' name='RB' value='".$formdata['RB']."' />
								<input type='hidden' name='is_kbank' value='".$is_kbank."' />
							    <input type='hidden' name='loan' value='".$loan."' />
								<input type='hidden' name='minus' value='".$minus."' />
								<input type='hidden' name='confirmForm' value='1' />
							</form>
							<script type='text/javascript'>
								if(confirm('".$msg."')){
									document.forms[0].submit();
								}
								else{
									window.location = '".$base_url."transfer/index/add';
								}
							</script>"); 
					
					exit();	
				}
				//print_r($formdata);exit();
				$db = $db_mtran->insertMoneyTransfer($formdata);				
				Application_Form_FrmMessage::Sucessfull('ការ​បញ្ចូល​​ជោគ​ជ័យ', self::REDIRECT_URL . '/index/add');
						
			} catch (Exception $e) {
				echo $e->getMessage();
				$this->view->msg = 'ការ​បញ្ចូល​មិន​ជោគ​ជ័យ';
			}
		}
		// action body
		//user name for report
		$session_user=new Zend_Session_Namespace('auth');
		$this->view->user_name = $session_user->last_name .' '. $session_user->first_name;
		
		$db_keycode = new Application_Model_DbTable_DbKeycode();
		$this->view->keycode = $db_keycode->getKeyCodeMiniInv();
		
		$pro = new Application_Model_DbTable_DbProvinces();
		$this->view->provinces = $pro->getProvinceList();
		
		$subagent = new Application_Model_DbTable_DbSubAgent();
		$this->view->subagent = $subagent->getSubAgentListSelectTrns();
		
		$agent = new Application_Model_DbTable_DbAgents();
		$this->view->agent = $agent->getAgentListSelectTrns();
		
		$sender = new Application_Model_DbTable_DbSender();
		$_sender = $sender->getAllSenderName();
		array_unshift($_sender, array ('id' => '-1',"name"=>"បន្ថែមឈ្មោះអ្នកផ្ញើរ") );
		$this->view->sender = $_sender;
		
		$cur = new Application_Model_DbTable_DbCurrencies();
		$this->view->currency = $cur->getCurrencyList();
		
		
		$this->view->invoice_no = Application_Model_GlobalClass::getInvoiceWithdraw(3);
		
		$this->view->tran_typelist = $this->tran_typelist;
		$db_rate=new Application_Model_DbTable_DbRate();
		$this->view->rate =  $db_rate->getCurrentRate();
        
    }

    public function viewAction()
    {
        // action body
        //Get value from url
        $mt_id = $this->getRequest()->getParam('mt_id');
        
        //Get Data form database
        $db_tran=new Tellerandexchange_Model_DbTable_DbTransactioncash();
		
        $this->view->traninfo = $db_tran->getTransactionDetailByIDView($mt_id);        
    }

    public function paidMoneyAction()
    {
        // action body
        $mt_id = $this->getRequest()->getParam('mt_id');
		
	    $db_tran=new Tellerandexchange_Model_DbTable_DbTransactioncash();
	    
	    try {
	    	$db = $db_tran->updateMoneyTransferByStatus($mt_id, 1);				
			Application_Form_FrmMessage::Sucessfull('ការ​បញ្ចូល​​ជោគ​ជ័យ', self::REDIRECT_URL);			
		} catch (Exception $e) {
			$this->view->msg = 'ការ​បញ្ចូល​មិន​ជោគ​ជ័យ';
		}
        
    }
    public function senderAction(){
	
    		// action body
    		//create sesesion
    		$session_search_sender=new Zend_Session_Namespace('search_sender');
    		if(empty($session_search_sender->limit)){
    			$session_search_sender->limit =  Application_Form_FrmNavigation::getLimit();
    			$session_search_sender->active  = -1;
//     			$session_search_sender->user_type = -1;
    			$session_search_sender->txtsearch = '';
    			$session_search_sender->lock();
    		}
    		 
    		//start page nevigation
    		$limit = $session_search_sender->limit;
    		$start = $this->getRequest()->getParam('limit_satrt',0);
    		 
    		$db_user=new Application_Model_DbTable_DbUsers();//not used
    	
    		$db = new Application_Model_DbTable_DbSender();
    		
    		$this->view->activelist =$this->activelist;
    		$this->view->active = $session_search_sender->active;
    	
    		$this->view->user_typelist =$this->user_typelist;
//     		$this->view->user_type = $session_search_sender->user_type;
    	
    	
    		if($this->getRequest()->isPost()){
    			$_data=$this->getRequest()->getPost();
    			//set session when submit
    			$session_search_sender->unlock();
    			$session_search_sender->limit =  $_data['rows_per_page'];
    			$session_search_sender->active  = $_data['active'];
    			$session_search_sender->txtsearch = $_data['txtsearch'];
    			$session_search_sender->lock();
    	
    			//set value for display
    			$this->view->txtsearch  = $_data['txtsearch'];
    			$this->view->active  = $_data['active'];
    			$limit = $_data['rows_per_page'];
    			//print_r($_data);
    			
    			$senders = $db->getAllSenderList($_data, $start, $limit);
    			$record_count = $db->getCountallSender($_data);
    		}
    		else{
    			if($session_search_sender->active > -1 || !empty($session_search_sender->txtsearch) ){
    				$sender_seach_data = array(
    						'active'=>$session_search_sender->active,
    						'txtsearch'=>$session_search_sender->txtsearch
    				);
    				$senders = $db->getAllSenderList($sender_seach_data,$start, $limit);
    				$record_count = $db->getCountallSender($sender_seach_data);
    			}
    			else{
    				
    				$senders = $db->getAllSenderList(null,$start, $limit);
    				$record_count = $db->getCountallSender();
    			}
    		}
    		$result = array();
    		$row_num = $start;
    		foreach ($senders as $i => $sender) {
    			$result[$i] = array(
    					'num' => (++$row_num),
    					'id' => $sender['sender_id'],
    					'name' => $sender['sender_name'],
    					'tel' => $sender['tel'],
    					'email' =>  $sender['email'],
    					'address' =>  $sender['address'],
    					'active' => $this->activelist[$sender['status']] ,
    			);
    		}
    		$this->view->senderlist = Zend_Json::encode($result);
    		$page = new Application_Form_FrmNavigation("/transfer/index/sender", $start, $limit, $record_count);
    		$page->init("/transfer/index/sender", $start, $limit, $record_count);
    		$this->view->nevigation = $page->navigationPage();
    		$this->view->rows_per_page = $page->getRowsPerPage($limit, 'frmlist_sender');
    		$this->view->result_row = $page->getResultRows();
    	
    	
    }
    public function addSenderAction()
    {
    	if($this->getRequest()->isPost()){
    		try {
    			$data=$this->getRequest()->getPost();
    			$db = new Application_Model_DbTable_DbSender();
    			$db->addSender($data);
    			Application_Form_FrmMessage::Sucessfull('ការ​បញ្ចូល​​ជោគ​ជ័យ',"/transfer/index/add-sender");
    		} catch (Exception $e) {
    			echo $e->getMessage();
    			$this->view->msg = 'ការ​បញ្ចូល​មិន​ជោគ​ជ័យ';
    		}
    	}
    	$frm_sender  = new Application_Form_FrmSender();
    	$frm_sender = $frm_sender->frmSender(null);
    	Application_Model_Decorator::removeAllDecorator($frm_sender);
    	$this->view->frm_sender = $frm_sender;
    }
    public function updateSenderAction()
    {
    	$id = $this->getRequest()->getParam("id");
    	$_datadb = new Application_Model_DbTable_DbSender();
    	$row = $_datadb->getSenderNameById($id);
    	if($this->getRequest()->isPost()){
    		try {
    			$data=$this->getRequest()->getPost();
    			$db = new Application_Model_DbTable_DbSender();
    			$db->updateSender($data);
    			Application_Form_FrmMessage::Sucessfull('ការ​បញ្ចូល​​ជោគ​ជ័យ',"/transfer/index/sender");
    		} catch (Exception $e) {
    			//echo $e->getMessage();
    			$this->view->msg = 'ការ​បញ្ចូល​មិន​ជោគ​ជ័យ';
    		}
    	}
    	$frm_sender  = new Application_Form_FrmSender();
    	$frm = $frm_sender->frmSender($row);
    	Application_Model_Decorator::removeAllDecorator($frm);
    	$this->view->tel = $row['tel'];
    	$this->view->frm_sender = $frm;
    }
    
    public function addnewsenderAction(){//by ajax
    	if($this->getRequest()->isPost()){
    		$data=$this->getRequest()->getPost();
    		$db = new Application_Model_DbTable_DbSender();
    		$arr = $db->addNewSender($data);//ex:{"acc_no":"000011","sender_id":"104"}
    		
//     		$suc = array('id'=>$id);
//     		print_r(Zend_Json::encode($suc));
    		
    		print_r(Zend_Json::encode($arr));
    		exit();
    	}
    }
    public function getInfotransferAction(){//by get default info when deposit to kbank
    	if($this->getRequest()->isPost()){
    		$data=$this->getRequest()->getPost();
    		
    		$db_draw = new Application_Model_DbTable_DbKbank();
    		$sender_id = $db_draw->getSenderIdbyName($data['sender_name']);
    		
    		$db = new Application_Model_DbTable_DbGlobal();
    		$sql = "  SELECT recieve_province,agent_id,sub_agent_id FROM `cs_deposit`
    		WHERE recieve_province!=0 AND sender_id = $sender_id ";
    		$rs = $db->getGlobalDbRow($sql);
    		print_r(Zend_Json::encode($rs));
    		exit();
    	}
    }
    public function checkPermissonAction(){//chekc permission can debt or not if has owe now
    	if($this->getRequest()->isPost()){
    		$data=$this->getRequest()->getPost();
    
    		$db_draw = new Application_Model_DbTable_DbKbank();
    		$sender_id = $db_draw->getSenderIdbyName($data['sender_name']);
    		$money_type = $data['money_type'];
    
    		$db = new Application_Model_DbTable_DbGlobal();
    		/*$sql = "  SELECT sender_name as sender_id FROM `cs_transactions_owe` AS d,`cs_trans_found` AS f WHERE d.id = f.tran_id AND
						f.sender_id = $sender_id AND f.current_type = $money_type ";*/
    		$sql = "SELECT status_loan FROM `cs_transactions_owe` AS d WHERE amount_type = $money_type AND 
    			sender_name=$sender_id AND status_loan!=3 AND status !=1 LIMIT 1";
    		$rs = $db->getGlobalDbRow($sql);
    		if(empty($rs)){ $rs = -1;}
    		print_r(Zend_Json::encode($rs));
    		exit();
    	}
    }

}
