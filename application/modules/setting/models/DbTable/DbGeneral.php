<?php

class Setting_Model_DbTable_DbGeneral extends Zend_Db_Table_Abstract
{

    protected $_name = 'rms_setting';
    
    public function geLabelByKeyName($keyName){
    	$db = $this->getAdapter();
    	$sql = " SELECT s.`code`,s.keyName,s.keyValue 
				FROM `rms_setting` AS s
				WHERE s.status=1 
				AND s.`keyName` ='$keyName' LIMIT 1";
    	return $db->fetchRow($sql);
    }
	public function updateWebsitesetting($data){
		try{
			$part= PUBLIC_PATH.'/images/';
			$name = $_FILES['photo']['name'];
			$size = $_FILES['photo']['size'];
			$photo='';
			if (!empty($name)){
				$tem =explode(".", $name);
				$image_name = "logo.".end($tem);
				$tmp = $_FILES['photo']['tmp_name'];
				if(move_uploaded_file($tmp, $part.$image_name)){
					$photo = $image_name;
				}
				else
					$string = "Image Upload failed";
					
				$arr = array(
						'keyValue'=>$photo,
				);
				$where=" keyName= 'logo'";
				$this->update($arr, $where);
			}
			$arr = array('keyValue'=>$data['client_company_name'],);
			$where=" keyName= 'client_company_name'";
			$this->update($arr, $where);
			
			$arr = array('keyValue'=>$data['label_animation'],);
			$where=" keyName= 'label_animation'";
			$this->update($arr, $where);
			
			$arr = array('keyValue'=>$data['smsWarnningKH'],);
			$where=" keyName= 'sms-warnning-kh'";
			$this->update($arr, $where);
			
			$arr = array('keyValue'=>$data['reciept_kh'],);
			$where=" keyName= 'reciept_kh'";
			$this->update($arr, $where);
			
			$arr = array('keyValue'=>$data['exchange_ratetitle'],);
			$where=" keyName= 'exchange_ratetitle'";
			$this->update($arr, $where);
			
			$arr = array('keyValue'=>$data['exchange_reciept'],);
			$where=" keyName= 'exchange_reciept'";
			$this->update($arr, $where);
			
			$arr = array('keyValue'=>$data['comment'],);
			$where=" keyName= 'comment'";
			$this->update($arr, $where);
			
			$arr = array('keyValue'=>$data['brand_client'],);
			$where=" keyName= 'brand_client'";
			$this->update($arr, $where);
			
			$arr = array('keyValue'=>$data['brand_holiday'],);
			$where=" keyName= 'brand_holiday'";
			$this->update($arr, $where);
			
			$arr = array('keyValue'=>$data['brand_call'],);
			$where=" keyName= 'brand_call'";
			$this->update($arr, $where);
			
			$arr = array('keyValue'=>$data['rptTransferTitleKh'],);
			$where=" keyName= 'rpt-transfer-title-kh'";
			$this->update($arr, $where);
			
			$arr = array('keyValue'=>$data['branchAddClient'],);
			$where=" keyName= 'branch-add-client'";
			$this->update($arr, $where);
			
			$arr = array('keyValue'=>$data['telClient'],);
			$where=" keyName= 'tel-client'";
			$this->update($arr, $where);
			
			$arr = array('keyValue'=>$data['client_website'],);
			$where=" keyName= 'client_website'";
			$this->update($arr, $where);
			
			$arr = array('keyValue'=>$data['email_client'],	);
			$where=" keyName= 'email_client'";
			$this->update($arr, $where);
			
// 			$arr = array('keyValue'=>$data['power_by'],	);
// 			$where=" keyName= 'power_by'";
// 			$this->update($arr, $where);
			
// 			$arr = array('keyValue'=>$data['branchTel'],	);
// 			$where=" keyName= 'branch-tel'";
// 			$this->update($arr, $where);
			
// 			$arr = array('keyValue'=>$data['branch_add'],	);
// 			$where=" keyName= 'branch_add'";
// 			$this->update($arr, $where);
			
// 			$arr = array('keyValue'=>$data['branch_email'],	);
// 			$where=" keyName= 'branch_email'";
// 			$this->update($arr, $where);
			
			$dbg = new Application_Model_DbTable_DbGlobal();
			$rows = $this->geLabelByKeyName('penaltyType');
			if (empty($rows)){
				$arr = array('keyValue'=>$data['penaltyType'],'keyName'=>'penaltyType','user_id'=>$dbg->getUserId());
				$this->insert($arr);
			}else{
				$arr = array('keyValue'=>$data['penaltyType'],);
				$where=" keyName= 'penaltyType'";
				$this->update($arr, $where);
			}
			
			$rows = $this->geLabelByKeyName('penaltyValue');
			if (empty($rows)){
				$arr = array('keyValue'=>$data['penaltyValue'],'keyName'=>'penaltyValue','user_id'=>$dbg->getUserId());
				$this->insert($arr);
			}else{
				$arr = array('keyValue'=>$data['penaltyValue'],);
				$where=" keyName= 'penaltyValue'";
				$this->update($arr, $where);
			}
			
			$rows = $this->geLabelByKeyName('penaltyValueDollar');
			if (empty($rows)){
				$arr = array('keyValue'=>$data['penaltyValueDollar'],'keyName'=>'penaltyValueDollar','user_id'=>$dbg->getUserId());
				$this->insert($arr);
			}else{
				$arr = array('keyValue'=>$data['penaltyValueDollar'],);
				$where=" keyName= 'penaltyValueDollar'";
				$this->update($arr, $where);
			}
			
			$rows = $this->geLabelByKeyName('penaltyValueBath');
			if (empty($rows)){
				$arr = array('keyValue'=>$data['penaltyValueBath'],'keyName'=>'penaltyValueBath','user_id'=>$dbg->getUserId());
				$this->insert($arr);
			}else{
				$arr = array('keyValue'=>$data['penaltyValueBath'],);
				$where=" keyName= 'penaltyValueBath'";
				$this->update($arr, $where);
			}
			
			$rows = $this->geLabelByKeyName('graicePariod');
			if (empty($rows)){
				$arr = array('keyValue'=>$data['graicePariod'],'keyName'=>'graicePariod','user_id'=>$dbg->getUserId());
				$this->insert($arr);
			}else{
				$arr = array('keyValue'=>$data['graicePariod'],);
				$where=" keyName= 'graicePariod'";
				$this->update($arr, $where);
			}
			
			$rows = $this->geLabelByKeyName('penaltyCalculateDay');
			if (empty($rows)){
				$arr = array('keyValue'=>$data['penaltyCalculateDay'],'keyName'=>'penaltyCalculateDay','user_id'=>$dbg->getUserId());
				$this->insert($arr);
			}else{
				$arr = array('keyValue'=>$data['penaltyCalculateDay'],);
				$where=" keyName= 'penaltyCalculateDay'";
				$this->update($arr, $where);
			}
			
			
		}catch(Exception $e){
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	}
	
}

