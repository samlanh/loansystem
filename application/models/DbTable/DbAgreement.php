<?php

class Application_Model_DbTable_DbAgreement extends Zend_Db_Table_Abstract
{

    protected $_name = 'ln_client';
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace(SYSTEM_SES);
    	return $session_user->user_id;
    	 
    }
    public function getLoanById($loan_id){//for loan Agreement
    	$sql="
    	SELECT l.*,
	    	br.branch_namekh AS branch_name,
	    	br.branch_nameen AS shop_name,
	    	br.br_address AS br_address,
	    	br.gm_name,
	    	br.gm_sex,
	       (SELECT name_kh FROM `ln_view` WHERE TYPE =11 AND br.gm_sex=key_code LIMIT 1) AS gmSexTitle,
	    	br.gm_dob,
	    	br.gm_nationality,
	    	br.gm_nation_id,
	    	br.gm_issue_date,
	    	br.gm_occupation,
	    	br.gm_address,
	    	br.with_gm_name,
	    	(SELECT name_kh FROM `ln_view` WHERE TYPE =11 AND br.with_gm_sex=key_code LIMIT 1) AS withGmSexTitle,
	    	br.with_gm_dob,
	    	br.with_gm_nationality,
	    	br.with_gm_nation_id,
	    	br.with_gm_issue_date,
	    	br.with_gm_occupation,
	    	br.with_gm_is,
    	(SELECT curr_namekh FROM `ln_currency` WHERE id = l.currency_type LIMIT 1) AS currency_type,
    	(SELECT name_en FROM `ln_view` WHERE TYPE =14 AND l.pay_term=key_code LIMIT 1) AS payTermEN,
    	(SELECT name_kh FROM `ln_view` WHERE TYPE =14 AND l.pay_term=key_code LIMIT 1) AS payTermKH,
    	(SELECT CONCAT(last_name ,' ',first_name)  FROM `rms_users` WHERE id = l.user_id LIMIT 1) AS user_name
    	
    	FROM
    	`ln_loan` AS l,
		ln_branch AS br
    	WHERE br.br_id =l.branch_id ";
    	
    	$dbp = new Application_Model_DbTable_DbGlobal();
    	$sql.=$dbp->getAccessPermission("l.branch_id");
    	
    	if(!empty($loan_id)){
    		$sql.=" AND l.id = $loan_id LIMIT 1";
    	}
    	
    	$db=$this->getAdapter();
    	return $db->fetchRow($sql);
    }
    public function getLoanCollateral($Loanid){
    	$db = $this->getAdapter();
    	$sql="SELECT cd.*,
    	(SELECT ct.title_kh FROM `ln_callecteral_type` AS ct WHERE ct.id = cd.`collecteral_type` LIMIT 1) AS calleteralTitle,
		(SELECT v.name_kh FROM `ln_view` AS v WHERE v.type=21 AND v.key_code= cd.`owner_type` LIMIT 1) AS ownerType
			FROM 
			`ln_client_callecteral_detail` AS cd,
			`ln_client_callecteral` AS c
			WHERE c.`id` = cd.`client_coll_id`
			AND c.`loan_id` = $Loanid AND cd.`status` =1 AND cd.`is_return` =0";
    	return $db->fetchAll($sql);
    }
	function getClientLoanInfo($clientID){
		$db = $this->getAdapter();
		$this->_name ="ln_client";
		$sql ="SELECT 
			c.*,
			(SELECT branch_namekh FROM `ln_branch` WHERE br_id = c.branch_id LIMIT 1) AS branch_name ,
			(SELECT name_en FROM `ln_view` WHERE TYPE =11 AND c.sex=key_code LIMIT 1) AS sexEN,
			(SELECT name_kh FROM `ln_view` WHERE TYPE =11 AND c.sex=key_code LIMIT 1) AS sexKH,
			(SELECT name_en FROM `ln_view` WHERE TYPE =11 AND c.join_sex=key_code LIMIT 1) AS join_sexEN,
			(SELECT name_kh FROM `ln_view` WHERE TYPE =11 AND c.join_sex=key_code LIMIT 1) AS join_sexKH,
			(SELECT village_namekh FROM `ln_village` WHERE vill_id=c.village_id) AS village_name,
			(SELECT name_kh FROM `ln_view` WHERE TYPE =23 AND c.`client_d_type`=id LIMIT 1) AS docTypKH,
			(SELECT name_en FROM `ln_view` WHERE TYPE =23 AND c.`client_d_type`=id LIMIT 1) AS docTypEN,
			(SELECT name_kh FROM `ln_view` WHERE TYPE =23 AND c.`join_d_type`=id LIMIT 1) AS joinDocTypKH,
			(SELECT name_en FROM `ln_view` WHERE TYPE =23 AND c.`join_d_type`=id LIMIT 1) AS joinDocTypEN,
			(SELECT name_kh FROM `ln_view` WHERE TYPE =23 AND c.`guarantor_d_type`=id LIMIT 1) AS guarantorDocTypKH,
			(SELECT name_en FROM `ln_view` WHERE TYPE =23 AND c.`guarantor_d_type`=id LIMIT 1) AS guarantorDocTypEN,
			(SELECT name_kh FROM `ln_view` WHERE TYPE =11 AND c.spouse_gender=key_code LIMIT 1) AS guarantorGender,
			(SELECT vl.`village_namekh` FROM ln_village AS vl WHERE vl.vill_id = c.`village_id` LIMIT 1 ) AS villageName,
			(SELECT cm.`commune_namekh` FROM ln_commune AS cm WHERE cm.com_id = c.`com_id` LIMIT 1 ) AS communeName,
			(SELECT `d`.`district_namekh` FROM `ln_district` AS `d` WHERE (`d`.`dis_id` = c.dis_id ) LIMIT 1) AS `district_name`,
			(SELECT province_kh_name FROM `ln_province` WHERE province_id= c.pro_id LIMIT 1) AS province_kh_name,
			(SELECT  CONCAT(first_name,' ', last_name) FROM rms_users WHERE id=c.user_id )AS user_name
		FROM $this->_name AS c WHERE c.`client_id` = $clientID LIMIT 1";
		return $db->fetchRow($sql);
	}
	function getClientPawnShopInfo($clientID){
		$db = $this->getAdapter();
		$this->_name ="ln_clientsaving";
		$sql = "
		SELECT
		c.*,
		(SELECT branch_namekh FROM `ln_branch` WHERE br_id = c.branch_id LIMIT 1) AS branch_name ,
		(SELECT name_en FROM `ln_view` WHERE TYPE =11 AND c.sex=key_code LIMIT 1) AS sexEN,
		(SELECT name_kh FROM `ln_view` WHERE TYPE =11 AND c.sex=key_code LIMIT 1) AS sexKH,
		(SELECT village_namekh FROM `ln_village` WHERE vill_id=c.village_id) AS village_name,
		(SELECT name_kh FROM `ln_view` WHERE TYPE =23 AND c.`client_d_type`=id LIMIT 1) AS docTypKH,
		(SELECT name_en FROM `ln_view` WHERE TYPE =23 AND c.`client_d_type`=id LIMIT 1) AS docTypEN,
		(SELECT name_kh FROM `ln_view` WHERE TYPE =23 AND c.`join_d_type`=id LIMIT 1) AS joinDocTypKH,
		(SELECT name_en FROM `ln_view` WHERE TYPE =23 AND c.`join_d_type`=id LIMIT 1) AS joinDocTypEN,
		(SELECT cm.`commune_namekh` FROM ln_commune AS cm WHERE cm.com_id = c.`com_id` LIMIT 1 ) AS communeName,
		(SELECT `d`.`district_namekh` FROM `ln_district` AS `d` WHERE (`d`.`dis_id` = c.dis_id ) LIMIT 1) AS `district_name`,
		(SELECT province_kh_name FROM `ln_province` WHERE province_id= c.pro_id LIMIT 1) AS province_kh_name,
		(SELECT  CONCAT(first_name,' ', last_name) FROM rms_users WHERE id=c.user_id )AS user_name,
		(SELECT name_en FROM `ln_view` WHERE TYPE =11 AND c.join_sex=key_code LIMIT 1) AS joinSexEN,
		(SELECT name_kh FROM `ln_view` WHERE TYPE =11 AND c.join_sex=key_code LIMIT 1) AS joinSexKH,
		(SELECT name_kh FROM `ln_view` WHERE TYPE =23 AND c.`guarantor_d_type`=id LIMIT 1) AS guarantorDocTypKH,
		(SELECT name_kh FROM `ln_view` WHERE TYPE =11 AND c.guarantor_gender=key_code LIMIT 1) AS guarantor_gender,
		(SELECT name_en FROM `ln_view` WHERE TYPE =23 AND c.`guarantor_d_type`=id LIMIT 1) AS guarantorDocTypEN
		FROM $this->_name AS c WHERE c.`client_id` = $clientID LIMIT 1";
		return $db->fetchRow($sql);
	}
	function getPawnShopInfo($pawnID){
		$db = $this->getAdapter();
		$this->_name ="ln_pawnshop";
		$sql = " SELECT s.*,
			(SELECT branch_namekh FROM `ln_branch` WHERE br_id =branch_id LIMIT 1) AS branch,
			(SELECT branch_nameen FROM `ln_branch` WHERE br_id =branch_id LIMIT 1) AS branch_nameen,
			(SELECT br_address FROM `ln_branch` WHERE br_id =branch_id LIMIT 1) AS br_address,
			(SELECT branch_tel FROM `ln_branch` WHERE br_id =branch_id LIMIT 1) AS branch_tel,
			(SELECT name_kh FROM `ln_clientsaving` WHERE client_id = s.customer_id LIMIT 1) AS client_name_kh,
			CONCAT(release_amount,(SELECT symbol FROM `ln_currency` WHERE id =s.currency_type LIMIT 1)) AS amountWithCurrency,
			(SELECT curr_namekh FROM `ln_currency` WHERE id = s.currency_type LIMIT 1) AS currencyType,
			CONCAT(total_duration,(SELECT name_en FROM `ln_view` WHERE TYPE = 14 AND key_code = term_type )) term_type,
			(SELECT product_kh FROM `ln_pawnshopproduct` WHERE id=s.product_id LIMIT 1) AS product_name,
			(SELECT name_en FROM `ln_view` WHERE TYPE =14 AND s.term_type=key_code LIMIT 1) AS payTermEN,
    	(SELECT name_kh FROM `ln_view` WHERE TYPE =14 AND s.term_type=key_code LIMIT 1) AS payTermKH,
			(SELECT first_name FROM `rms_users` WHERE id=user_id) AS user_name,
			(SELECT SUM(rep.extra_loan) FROM `ln_pawnshop_reschedule` AS rep WHERE rep.pawnshop_id = s.`id` LIMIT 1) AS extra_loan
		 FROM $this->_name AS s
		WHERE s.`id` = $pawnID ";
		$where="";
		$dbp = new Application_Model_DbTable_DbGlobal();
		$where.= $dbp->getAccessPermission("s.branch_id");
		
		$sql.=" LIMIT 1";
		return $db->fetchRow($sql);
	}
	function getPawnShopRescheduleInfo($pawnRescheduleID){
		$db = $this->getAdapter();
		$sql="
		SELECT
		c.*,
		(SELECT branch_namekh FROM `ln_branch` WHERE br_id =c.`branch_id` LIMIT 1) AS branch,
		(SELECT branch_nameen FROM `ln_branch` WHERE br_id =c.`branch_id` LIMIT 1) AS branch_nameen,
		(SELECT br_address FROM `ln_branch` WHERE br_id =c.`branch_id` LIMIT 1) AS br_address,
		(SELECT branch_tel FROM `ln_branch` WHERE br_id =c.`branch_id` LIMIT 1) AS branch_tel,
		s.`loan_number`,
		(SELECT name_kh FROM `ln_clientsaving` WHERE client_id = s.customer_id LIMIT 1) AS client_name_kh,
		(SELECT curr_namekh FROM `ln_currency` WHERE id = s.currency_type LIMIT 1) AS currencyType,
		(SELECT product_kh FROM `ln_pawnshopproduct` WHERE id=s.product_id LIMIT 1) AS product_name,
		(SELECT name_en FROM `ln_view` WHERE TYPE =14 AND s.term_type=key_code LIMIT 1) AS payTermEN,
		(SELECT name_kh FROM `ln_view` WHERE TYPE =14 AND s.term_type=key_code LIMIT 1) AS payTermKH,
		(SELECT first_name FROM `rms_users` WHERE id=c.user_id) AS user_name,
		s.customer_id,
		c.`level` AS reschdule_time,
		s.release_amount,
		(s.`release_amount`-c.`extra_loan`) as pawnbalancce
		
		 FROM `ln_pawnshop_reschedule` AS c,
		`ln_pawnshop` AS s 
		  WHERE s.`id` = c.`pawnshop_id` AND c.`id` = $pawnRescheduleID LIMIT 1
		";
		return $db->fetchRow($sql);
	}
}

