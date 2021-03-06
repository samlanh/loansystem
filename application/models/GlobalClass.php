<?php

class Application_Model_GlobalClass  extends Zend_Db_Table_Abstract
{
   public static function getInvoiceNo(){	
		//return strtoupper(uniqid());
		$sub=substr(uniqid(rand(10,1000),false),rand(0,10),5);
		$date= new Zend_Date();
		$head="W".$date->get('YY-MM-d/ss');
		return $head.$sub;
   }
   public function getOptonsHtml($sql, $display, $value){
   	$db = $this->getAdapter();
   	$option = '<option value="">--- Select ---</option>';
   	foreach($db->fetchAll($sql) as $r){
   			
   		$option .= '<option value="'.$r[$value].'">'.htmlspecialchars($r[$display], ENT_QUOTES).'</option>';
   	}
   	return $option;
   }
   public function getOptonsHtmlTranslate($sql, $display, $value){
   	$tr = Application_Form_FrmLanguages::getCurrentlanguage();
   	$db = $this->getAdapter();
   	$option = '<option value="">'.$tr->translate("PLEASE_SELECT").'</option>';
   	foreach($db->fetchAll($sql) as $r){
   		$option .= '<option value="'.$r[$value].'">'.htmlspecialchars($tr->translate(strtoupper($r[$display])), ENT_QUOTES).'</option>';
   	}
   	return $option;
   }
   public function getYesNoOption(){
   	//Select Public for report
   	$tr = Application_Form_FrmLanguages::getCurrentlanguage();
   	$myopt = '<option value="">'.$tr->translate("PLEASE_SELECT").'</option>';
   	$myopt .= '<option value="Yes">Yes</option>';
   	$myopt .= '<option value="No">No</option>';
   	return $myopt;
   
   }	
   public function getImgAttachStatus($rows,$base_url, $case=''){
		if($rows){			
			$imgattach='<img src="'.$base_url.'/images/icon/attachment.png"/>';
			$imgnone='<img src="'.$base_url.'/images/icon/no-attachment.png"/>';
			if($case !== ''){
				$imgattach='<img src="'.$base_url.'/images/icon/attachment.png"/>';
				$imgnone='<img src="'.$base_url.'/images/icon/no-attachment.png"/>';
			}
			 
			foreach ($rows as $i =>$row){
				if(is_dir('docs/case_note_id_'.$row['note_id'])){
					$rows[$i]['note_id'] = $imgattach;
				}
				else{
					$rows[$i]['note_id'] = $imgnone;
				}
			}
			 
		}		
		return $rows;
	}
	/**
	 * add element "delete" to $rows
	 * @param array $rows
	 * @param string $url_delete
	 * @param string $base_url
	 * @return array $rows
	 */
	public static function getImgDelete($rows,$url_delete,$base_url){
		foreach($rows as $key=>$row){
			$url = $url_delete.$row["id"];
			$row['delete'] = '<a href="'.$url.'"><img src="'.BASE_URL.'/images/icon/cross.png"/></a>';
			$rows[$key] = $row;
		}
		return $rows;
	}
	
	/**
	 * Get Day name With multiple Languages
	 * @param string $key
	 * @var $key ('mo', 'tu', 'we', 'th', 'fr', 'sa', 'su')
	 */
	public function getDayName($key = ''){
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$day_name = array(
							'su' => $tr->translate('SU'),
							'mo' => $tr->translate('MO'),
							'tu' => $tr->translate('TU'),
							'we' => $tr->translate('WE'),
							'th' => $tr->translate('TH'),
							'fr' => $tr->translate('FR'),
							'sa' => $tr->translate('SA')							
						 );
		if(empty($key)){
			return $day_name;
		}
		return  $day_name[$key];
	}
	
	/**
	 * Get all Hour per day
	 * @param int $key
	 * @return multitype:string |Ambigous <string>
	 * @var $key = [0-23]
	 */
	public function getHours($key = ''){
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$am = $tr->translate('AM');
		$pm = $tr->translate('PM');
		$hours = array(
				'12:00 '. $pm,
				'01:00 '. $am,
				'02:00 '. $am,
				'03:00 '. $am,
				'04:00 '. $am,
				'05:00 '. $am,
				'06:00 '. $am,
				'07:00 '. $am,
				'08:00 '. $am,
				'09:00 '. $am,
				'10:00 '. $am,
				'11:00 '. $am,
				'12:00 '. $am,
				'01:00 '. $pm,
				'02:00 '. $pm,
				'03:00 '. $pm,
				'04:00 '. $pm,
				'05:00 '. $pm,
				'06:00 '. $pm,
				'07:00 '. $pm,
				'08:00 '. $pm,
				'09:00 '. $pm,
				'10:00 '. $pm,
				'11:00 '. $pm				
				); 
		if(empty($key)){
			return $hours;
		}
		return  $hours[$key];
	}
	
	/**
	 * Generate Age for child
	 */

	public function getSelectBoxOptions($options){
		$sl_opt = "";
		foreach($options as $key=>$opt){
			$sl_opt .= "<option value='".$key."'>".$opt."</option>";
		}
		return $sl_opt;
	}	
	
	/**
	 * get phone number in format
	 * @param string $str
	 * @return string
	 */
	public static function getPhoneNumber($str)
	{
		$str = str_replace(" ", "", $str);
		$firt = substr($str, 0,3);
		$second = substr($str, 3, strlen($str)-3);
		$phone = $firt." ".$second;
		return $phone;
	}
	
	/**
	 * Generate navigation for use global
	 * @author channy
	 * @param $url current of action
	 * @param $frm form for use cover of control 
	 * @param $limit number of limit record
	 * @return $record_count number of record
	 */
// 		public function getList($url,$frm,$start,$limit,$record_count){
// 			$page = new Application_Form_FrmNavigation($url, $start, $limit, $record_count);
// 			$page->init($url, $start, $limit, $record_count);//can wrong $form
// 			$nevigation = $page->navigationPage();
// 			$rows_per_page = $page->getRowsPerPage($limit, $frm);
// 			$result_row = $page->getResultRows();
// 			$arr = array(
// 					"nevigation"=>$nevigation,
// 					"rows_per_page"=>$rows_per_page,
// 					"result_row"=>$result_row);
// 			return $arr;
// 		}
// 		public function getAllMetionOption(){
// 			$_db = new Application_Model_DbTable_DbGlobal();
// 			$rows = $_db->getAllMention();
// 			$option = '';
// 			if(!empty($rows))foreach($rows as $key => $value){
// 				$option .= '<option value="'.$key.'" >'.htmlspecialchars($value, ENT_QUOTES).'</option>';
// 			}
// 			return $option;
// 		}
// 		public function getAllPayMentTermOption(){
// 			$_db = new Application_Model_DbTable_DbGlobal();
// 			$rows = $_db->getAllPaymentTerm();
// 			$option = '';
// 			if(!empty($rows))foreach($rows as $key => $value){
// 				$option .= '<option value="'.$key.'" >'.htmlspecialchars($value, ENT_QUOTES).'</option>';
// 			}
// 			return $option;
// 		}
// 		public function getAllFacultyOption(){
// 			$_db = new Application_Model_DbTable_DbGlobal();
// 			$rows = $_db->getAllFecultyName();
// 			array_unshift($rows, array('dept_id'=>-1,'en_name'=>"Add New"));
// 			$options = '';
// 			if(!empty($rows))foreach($rows as $value){
// 				$options .= '<option value="'.$value['dept_id'].'" >'.htmlspecialchars($value['en_name'], ENT_QUOTES).'</option>';
// 			}
// 			return $options;
// 		}
		
		public function getImgActive($rows,$base_url, $case='',$degree=null,$display=null){
			if($rows){
				$imgnone='<img src="'.$base_url.'/images/icon/cross.png"/>';
				$imgtick='<img src="'.$base_url.'/images/icon/apply2.png"/>';
		
				foreach ($rows as $i =>$row){
					if($degree!=null){
						$dg = new Application_Model_DbTable_DbGlobal();
						
						$rows[$i]['degree']  = $dg->getAllDegree($row['degree']);
					}
					if($display!=null){
						$rows[$i]['displayby']= ($row['displayby']==1)?'Khmer':'English';
					}
					if($row['status'] == 1){
						$rows[$i]['status']= $imgtick;
					}elseif($row['status'] == 2){
						$rows[$i]['status'] = "Completed";
					}
					else{
						$rows[$i]['status'] = $imgnone;
					}
					
				}
			}
			return $rows;
		}
		public function getSex($rows,$base_url, $case='',$type=null){
			if($rows){
				$m='M';
				$f='F';
				foreach ($rows as $i =>$row){
					if($row['sex'] == 1){
						$rows[$i]['sex'] = $f;
					}
					else{
						$rows[$i]['sex'] = $m;
					}
				}
			}
			return $rows;
		}
		public function getAllClientGroupOption(){
			$_db = new Application_Model_DbTable_DbGlobal();
			$rows = $_db->getClientByType();
			array_unshift($rows, array('client_id'=>-1,'name_en'=>"Add New"));
			$options = '';
			if(!empty($rows))foreach($rows as $value){
				$options .= '<option value="'.$value['client_id'].'" >'.htmlspecialchars($value['name_en'], ENT_QUOTES).'</option>';
			}
			return $options;
		}
		public function getAllClientCodeOption(){
			$_db = new Application_Model_DbTable_DbGlobal();
			$rows = $_db->getClientByType();
// 			array_unshift($rows, array('client_id'=>-1,'client_number'=>"Add New"));
			$options = '';
			if(!empty($rows))foreach($rows as $value){
				$options .= '<option value="'.$value['client_id'].'" >'.htmlspecialchars($value['client_number'], ENT_QUOTES).'</option>';
			}
			return $options;
		}
		public function getCollecteralOption(){
			$tr = Application_Form_FrmLanguages::getCurrentlanguage();
			$db = new Application_Model_DbTable_DbGlobal();
			$rows= $db->getCollteralType();
// 			array_unshift($rows, array('id'=>-1,'name'=>$tr->translate('ADD_NEW')));
// 			array_unshift($rows, array('id'=>'','name'=>$tr->translate('ADD_CALLTERAL_TYPE')));
			$options = '';
			if(!empty($rows))foreach($rows as $value){
				$options .= '<option value="'.$value['id'].'" >'.htmlspecialchars($value['name'], ENT_QUOTES).'</option>';
			}
			return $options;
		}
		public function getCollecteralTypeOption(){
				$options= '<option value="1" >'.htmlspecialchars('ផ្ទាល់ខ្លួន', ENT_QUOTES).'</option>';
				$options .= '<option value="2" >'.htmlspecialchars('អ្នកធានាជំនួស', ENT_QUOTES).'</option>';
			return $options;
		}
		public function getAllBranchOption(){
			$db = new Application_Model_DbTable_DbGlobal();
			$session_user=new Zend_Session_Namespace(SYSTEM_SES);
			$currentBranch = $session_user->branch_id;
			$currentlevel = $session_user->level;
			$branch = null;
			if ($currentlevel!=1){
				$branch =$currentBranch;
			}
	        $rows = $db->getAllBranchName($branch,null);
			//array_unshift($rows, array('client_id'=>-1,'name_en'=>"Add New"));
			$options = '';
// 			print_r($rows);exit();
			if(!empty($rows))foreach($rows as $value){
				$options .= '<option value="'.$value['br_id'].'" >'.htmlspecialchars($value['branch_namekh'], ENT_QUOTES).'</option>';
			}
			return $options;
		}
		public static function getInvoiceWithdraw($type=null){
			$sub=substr(uniqid(rand(10,1000),false),rand(0,10),6);
			$date= new Zend_Date();
			if($type==1){
				$head="DP-";//deposite
			}else if($type==2){
				$head="DL-";//DELAY
			}elseif($type==3){
				$head="TR-";//TRANSFER
			}elseif($type==4){
				$head="RF-";//REFUND
			}elseif($type==5){
				$head="LN-";//loan
			}elseif($type==6){
				$head="PO-";//PAYOUT
			}
			else{
				$head="WD-";//withdraw
			}
			return strtoupper($head.$sub);
		}
// 		public static function getInvoiceNo(){
// 			return strtoupper(uniqid());
// 		}
		public static function getInvoiceOwe(){
			$sub=substr(uniqid(rand(10,1000),false),rand(0,10),5);
			$date= new Zend_Date();
			$head="W".$date->get('YY-MM-d');
			return $head.$sub;
		}
}