<?php
class Report_Model_DbTable_Dbpawn extends Zend_Db_Table_Abstract
{
      public function getAllLoan($search = null){//rpt-loan-released/
      	 $db = $this->getAdapter();
      	 $sql = "SELECT
			  `l`.`id`             AS `id`,
			  `l`.`loan_number`    AS `loan_number`,
			  `l`.`branch_id`      AS `branch_id`,
			  `l`.`customer_id`    AS `client_id`,
			  `l`.`release_amount`    AS `total_capital`,
			  `l`.`interest_rate`  AS `interest_rate`,
			  `l`.`level`          AS `loan_level`,
			  `l`.`term_type`          AS `term_type`,
			   l.admin_fee,
			   l.is_dach,
			   l.is_completed,
			  (SELECT name_kh FROM `ln_view` WHERE type = 14 AND key_code =l.term_type ) term_type,
			  `l`.`currency_type`  AS `curr_type`,
			  `l`.`is_completed`   AS `is_completed`,
			  `l`.`total_duration` AS `total_duration`,
			  `l`.`date_release`   AS `date_release`,
			  `l`.`date_line`      AS `date_line`,
			  `l`.`status`         AS `status`,
			  `c`.`client_number` AS  `client_number`,
			  `c`.`name_en`  AS `client_name`,
			  `c`.`phone` AS `phone`,
			  `c`.`name_kh` AS `client_khname`,
			  (SELECT
			     `ln_branch`.`branch_namekh`
			   FROM `ln_branch`
			   WHERE (`ln_branch`.`br_id` = `l`.`branch_id`)
			   LIMIT 1) AS `branch_name`,
			  (SELECT
			     `ln_currency`.`symbol`
			   FROM `ln_currency`
			   WHERE (`ln_currency`.`id` = `l`.`currency_type`)
			   LIMIT 1) AS `currency_type`,
			  (SELECT
			     `ln_view`.`name_en`
			   FROM `ln_view`
			   WHERE ((`ln_view`.`type` = 14)
			          AND (`ln_view`.`key_code` = `l`.`term_type`))
			   LIMIT 1) AS `termborrow`,
			   (SELECT product_kh FROM `ln_pawnshopproduct` WHERE id=l.product_id) as product_name,
			   product_description,
			   est_amount
			 
			FROM 
				`ln_pawnshop` AS  `l`,
				ln_clientsaving As c
			WHERE (`l`.`status` = 1)
			      AND `c`.`client_id` = `l`.`customer_id` ";
      	 $where ='';
      
	    $from_date =(empty($search['start_date']))? '1': " date_release >= '".$search['start_date']." 00:00:00'";
	    $to_date = (empty($search['end_date']))? '1': " date_release <= '".$search['end_date']." 23:59:59'";
	    $where.= " AND ".$from_date." AND ".$to_date;

     	if($search['branch_id']>0){
    		$where.=" AND `l`.`branch_id` = ".$search['branch_id'];
    	}
    	if($search['members']>0){
    		$where.=" AND client_id = ".$search['members'];
    	}
    	
    	if($search['product_id']>0){
    		$where.=" AND l.product_id = ".$search['product_id'];
    	}
    	if($search['currency_type']>0){
    		$where.=" AND `l`.`currency_type` = ".$search['currency_type'];
    	}
		if(($search['completed_status'])>-1){
    		$where.= " AND l.is_completed=".$search['completed_status'];
    	}
		if(($search['dach_status'])>-1){
    		$where.= " AND l.is_dach=".$search['dach_status'];
    	}
      	 if(!empty($search['adv_search'])){
      	 	$s_where = array();
      	 	$s_search = addslashes(trim($search['adv_search']));
      	 	$s_search = str_replace(' ', '',$s_search);
      	 	$s_where[] = " REPLACE(loan_number,' ','')  LIKE '%{$s_search}%'";
      	 	$s_where[] = " REPLACE(client_number,' ','')LIKE '%{$s_search}%'";
      	 	$s_where[] = " REPLACE(c.name_kh,' ','')  LIKE '%{$s_search}%'";
      	 	$s_where[] = " REPLACE(release_amount,' ','')LIKE '%{$s_search}%'";
      	 	$s_where[] = " REPLACE(interest_rate,' ','')LIKE '%{$s_search}%'";
      	 	$where .=' AND ( '.implode(' OR ',$s_where).')';
      	 }
      	 $dbp = new Application_Model_DbTable_DbGlobal();
      	 $where.=$dbp->getAccessPermission('`l`.`branch_id`');
      	 $where.=" ORDER BY `l`.`branch_id`,`l`.`currency_type`";
      	 return $db->fetchAll($sql.$where);
      }
      public function getPaymentSchedule($id){
      	$db=$this->getAdapter();
      	$sql = "SELECT * FROM `ln_pawnshop_detail` WHERE pawn_id = $id ";
      	
      	$sql.=" ORDER BY installment_amount ASC,date_payment ASC ";
      	return $db->fetchAll($sql);
      }
      public function getAllDailyLoan($search = null){//rpt-loan-released/
      	$db = $this->getAdapter();
      	$end_date = $search['end_date'];
      	$sql = "SELECT v.*,      		
      		(SELECT COUNT(d.id) FROM ln_loan_detail AS d WHERE d.loan_id=v.id AND d.is_completed=0  AND d.date_payment <'2017-11-27'  ORDER BY d.`date_payment` ASC LIMIT 1) AS amount_late,
      		(SELECT SUM(d.principal_permonth) FROM  ln_loan_detail AS d WHERE d.is_completed=0 AND d.loan_id=v.id AND d.date_payment <'2017-11-27'  LIMIT 1) AS total_principal,
			(SELECT SUM(d.principle_after) FROM  ln_loan_detail AS d WHERE d.is_completed=0 AND d.loan_id=v.id AND d.date_payment <'2017-11-27' LIMIT 1) AS principle_after,
			(SELECT SUM(d.total_interest_after) FROM  ln_loan_detail AS d WHERE d.is_completed=0 AND d.loan_id=v.id AND d.date_payment <'2017-11-27' LIMIT 1) AS total_interest_after,
			(SELECT SUM(d.total_payment_after) FROM  ln_loan_detail AS d WHERE d.is_completed=0 AND d.loan_id=v.id AND d.date_payment <'2017-11-27' LIMIT 1) AS total_payment_after
     	  FROM v_dailyloan AS v WHERE 1 ";
      	$where ='';
      
      	$from_date =(empty($search['start_date']))? '1': " v.date_release >= '".$search['start_date']." 00:00:00'";
      	$to_date = (empty($search['end_date']))? '1': " v.date_release <= '".$search['end_date']." 23:59:59'";
      	$where.= " AND ".$from_date." AND ".$to_date;
      
      	if($search['branch_id']>0){
      		$where.=" AND v.branch_id = ".$search['branch_id'];
      	}
      	if($search['client_name']>0){
      		$where.=" AND v.client_id = ".$search['client_name'];
      	}
      	if($search['co_id']>0){
      		$where.=" AND v.co_id = ".$search['co_id'];
      	}
      	if($search['pay_every']>0){
      		$where.=" AND v.pay_term_id = ".$search['pay_every'];
      	}
      	 
      	if(!empty($search['adv_search'])){
      		$s_where = array();
      		$s_search = addslashes(trim($search['adv_search']));
      		$s_where[] = " v.branch_name LIKE '%{$s_search}%'";
      		$s_where[] = " v.loan_number LIKE '%{$s_search}%'";
      		$s_where[] = " v.client_number LIKE '%{$s_search}%'";
      		$s_where[] = " v.client_name LIKE '%{$s_search}%'";
      		$s_where[] = " v.co_name LIKE '%{$s_search}%'";
      		$s_where[] = " v.total_capital LIKE '%{$s_search}%'";
      		$s_where[] = " v.interest_rate LIKE '%{$s_search}%'";
      		$where .=' AND ( '.implode(' OR ',$s_where).')';
      	}
      	$order = " ORDER BY (CASE DAYOFWEEK(v.first_payment) WHEN 1 THEN 8 ELSE DAYOFWEEK(v.first_payment) END),v.first_payment DESC ";
      	return $db->fetchAll($sql.$where.$order);
      }
      
      public function getAllLoanCo($search = null){//rpt-loan-released
      	$db = $this->getAdapter();

      	$sql = "SELECT * FROM v_released_co WHERE 1";
      	$where ='';
      	$from_date =(empty($search['start_date']))? '1': " date_release >= '".$search['start_date']." 00:00:00'";
      	$to_date = (empty($search['end_date']))? '1': " date_release <= '".$search['end_date']." 23:59:59'";
      	$where.= " AND ".$from_date." AND ".$to_date;
      	
      	if($search['branch_id']>0){
      		$where.=" AND branch_id = ".$search['branch_id'];
      	}
      	if($search['member']>0){
      		$where.=" AND client_id = ".$search['member'];
      	}
      	if($search['co_id']>0){
      		$where.=" AND co_id = ".$search['co_id'];
      	}
      	if($search['pay_every']>0){
      		$where.=" AND pay_term_id = ".$search['pay_every'];
      	}
      	if($search['status']>-1){
      		$where.=" AND is_completed = ".$search['status'];
      	}
      	if(!empty($search['adv_search'])){
      		$s_where = array();
      		$s_search = addslashes(trim($search['adv_search']));
      		$s_where[] = " branch_name LIKE '%{$s_search}%'";
      		$s_where[] = " loan_number LIKE '%{$s_search}%'";
      		$s_where[] = " client_number LIKE '%{$s_search}%'";
      		$s_where[] = " client_name LIKE '%{$s_search}%'";
      		$s_where[] = " total_capital LIKE '%{$s_search}%'";
      		$s_where[] = " other_fee LIKE '%{$s_search}%'";
      		$s_where[] = " admin_fee LIKE '%{$s_search}%'";
      		$s_where[] = " name_en LIKE '%{$s_search}%'";
      		$s_where[] = " total_duration LIKE '%{$s_search}%'";
      		$s_where[] = " interest_rate LIKE '%{$s_search}%'";
      		$s_where[] = " loan_type LIKE '%{$s_search}%'";
      		$where .=' AND ('.implode(' OR ',$s_where).')';
      	}
      	$order = " ORDER BY co_id DESC";
      	return $db->fetchAll($sql.$where.$order);
      }
      public function getAllOutstadingLoan($search=null){
      	$db = $this->getAdapter();
      	$where="";
      	$to_date = (empty($search['end_date']))? '1': " date_release <= '".$search['end_date']." 23:59:59'";
      	$where.= "  AND ".$to_date;

      	$sql="
				SELECT
			  `l`.`id`             AS `id`,
			  `l`.`loan_number`    AS `loan_number`,
			  `l`.`branch_id`      AS `branch_id`,
			  `l`.`customer_id`    AS `client_id`,
			  `l`.`release_amount` AS `total_capital`,
			  `l`.`interest_rate`  AS `interest_rate`,
			  `l`.`level`          AS `loan_level`,
			  `l`.`currency_type`  AS `curr_type`,
			  `l`.`total_duration` AS `total_duration`,
			  `l`.`date_release`   AS `date_release`,
			  `l`.`date_line`      AS `date_line`,
			  `l`.`status`         AS `status`,
			  `c`.`client_number` AS  `client_number`,
			  `c`.`name_en`  AS `client_name`,
			  `c`.`name_kh` AS `client_khname`,
			  (SELECT
			     `ln_branch`.`branch_namekh`
			   FROM `ln_branch`
			   WHERE (`ln_branch`.`br_id` = `l`.`branch_id`)
			   LIMIT 1) AS `branch_name`,
			  (SELECT
			     `ln_currency`.`symbol`
			   FROM `ln_currency`
			   WHERE (`ln_currency`.`id` = `l`.`currency_type`)
			   LIMIT 1) AS `currency_type`,
			  (SELECT
			     `ln_view`.`name_kh`
			   FROM `ln_view`
			   WHERE ((`ln_view`.`type` = 14)
			          AND (`ln_view`.`key_code` = `l`.`term_type`))
			   LIMIT 1) AS `termborrow`,
			   COALESCE((SELECT product_kh FROM `ln_pawnshopproduct` WHERE id=l.product_id LIMIT 1),'') AS product_name,
			   l.product_description,
			   est_amount,
			  (SELECT
			     SUM(`ln_pawn_receipt_money`.`principal_paid`)
			   FROM `ln_pawn_receipt_money`
			   WHERE ((`ln_pawn_receipt_money`.`status` = 1)
			          AND `l`.`id`=ln_pawn_receipt_money.loan_id)) AS `total_principaid`
			 
			FROM 
				`ln_pawnshop` AS  `l`,
				ln_clientsaving AS c
			WHERE 
				(`l`.`status` = 1)
			      AND `c`.`client_id` = `l`.`customer_id` 
				  AND l.is_dach=0 ";
      	if($search['branch_id']>0){
      		$where.=" AND `l`.`branch_id` = ".$search['branch_id'];
      	}
      	if($search['members']>0){
           		$where.=" AND `l`.`customer_id`  = ".$search['members'];
      	}
      	if($search['product_id']>0){
      		$where.=" AND l.product_id = ".$search['product_id'];
      	}
      	if($search['status_use']>-1){
      		$where.=" AND l.is_dach = ".$search['status_use'];
      	}
      	if(!empty($search['adv_search'])){
			$s_where = array();
			$s_search = str_replace(' ', '', addslashes(trim($search['adv_search'])));
      		
      		$s_where[] = " REPLACE(l.loan_number,' ','')  LIKE '%{$s_search}%'";
      		$s_where[] = " REPLACE(COALESCE((SELECT product_kh FROM `ln_pawnshopproduct` WHERE id=l.product_id LIMIT 1),''),' ','')  LIKE '%{$s_search}%'";
      		$s_where[] = " REPLACE(l.product_description,' ','')  LIKE '%{$s_search}%'";
      		$s_where[] = " REPLACE(c.client_number,' ','')  LIKE '%{$s_search}%'";
      		$s_where[] = " REPLACE(c.name_en,' ','')  LIKE '%{$s_search}%'";
      		$s_where[] = " REPLACE(c.name_kh,' ','')  LIKE '%{$s_search}%'";
      		$s_where[] = " REPLACE(l.release_amount,' ','')  LIKE '%{$s_search}%'";
      		$s_where[] = " REPLACE(l.total_duration,' ','')  LIKE '%{$s_search}%'";
      	   $where .=' AND ('.implode(' OR ',$s_where).')';
      	}
      	$dbp = new Application_Model_DbTable_DbGlobal();
      	$where.=$dbp->getAccessPermission('`l`.`branch_id`');
      	$order=" ORDER BY l.id DESC";
      	return $db->fetchAll($sql.$where.$order);
      }
      public function getALLLoancollect($search = null){
      	$db = $this->getAdapter();
//       	$sql="SELECT id,
//       	(SELECT loan_number FROM ln_loan_member WHERE loan_number=(SELECT lm.loan_number FROM ln_loan_member AS lm  WHERE lm.member_id LIMIT 1) LIMIT 1 ) AS loan_number,
//       	(SELECT name_kh FROM ln_client WHERE client_id = (SELECT lm.client_id FROM ln_loan_member AS lm  WHERE lm.member_id LIMIT 1) LIMIT 1 ) AS client_name
//       	,(SELECT branch_namekh FROM ln_branch WHERE br_id= branch_id LIMIT 1) AS branch_id,
//       	(SELECT co_khname FROM ln_co WHERE co_id=(SELECT co_id FROM ln_loan_group WHERE g_id=(SELECT lm.client_id FROM ln_loan_member AS lm  WHERE lm.member_id LIMIT 1) LIMIT 1 )LIMIT 1 ) AS co,
//       	total_principal,total_interest,STATUS
//       	,total_payment,date_payment FROM ln_loanmember_funddetail WHERE 1 ";
      	
      	$from_date =(empty($search['start_date']))? '1': "f.date_payment >= '".$search['start_date']." 00:00:00'";
      	$to_date = (empty($search['end_date']))? '1': "f.date_payment <= '".$search['end_date']." 23:59:59'";
      	$where = " AND ".$from_date." AND ".$to_date;
      	
      	$Other =" ORDER BY co_name ,id DESC ";
      	$sql = " SELECT 
				  f.id ,
				  f.total_principal ,
				  f.total_interest ,
				  f.status ,
				  f.total_payment ,
				  f.date_payment ,
				  m.loan_number ,  
				  (SELECT name_kh FROM ln_client WHERE client_id=m.client_id) AS client_name , 
				  (SELECT branch_namekh FROM ln_branch WHERE br_id= m.branch_id LIMIT 1) AS branch_id ,
				  (SELECT co_khname FROM ln_co WHERE co_id=(SELECT co_id FROM ln_loan_group WHERE g_id= m.group_id LIMIT 1) LIMIT 1) AS co,
				  (SELECT co_firstname FROM ln_co WHERE co_id=(SELECT co_id FROM ln_loan_group WHERE g_id= m.group_id LIMIT 1) LIMIT 1) AS co_name
				  FROM `ln_loanmember_funddetail` AS f ,`ln_loan_member` AS m WHERE m.member_id = f.member_id 
				  AND f.is_completed=0 AND f.status=1 AND m.is_completed=0 ";
      	if(!empty($search['txtsearch'])){
      		$s_where = array();
      		$s_search = addslashes(trim($search['txtsearch']));
      		$s_where[] = " loan_number LIKE '%{$s_search}%'";
      		$s_where[]=" client_name LIKE '%{$s_search}%'";
      		$where .=' AND ('.implode(' OR ',$s_where).')';
      	
      	}
      	//echo $sql.$where.$Other;
      	return $db->fetchAll($sql.$where.$Other);
      }
     
      public function getALLGroupDisburse($id=null){
      	$db = $this->getAdapter();
      	$sql="SELECT *  
				FROM
				`v_loangroupmember` WHERE `group_id`= $id";
      	
      	//$Other =" ORDER BY member_id ASC";
//       	$where = '';
//       	if(!empty($search['txtsearch'])){
//       		$s_where = array();
//       		$s_search = $search['txtsearch'];
//       		$s_where[] = " chart_id LIKE '%{$s_search}%'";
//       		$s_where[]=" group_id LIKE '%{$s_search}%'";
//       		$where .=' AND '.implode(' OR ',$s_where).'';      		 
      //	}
      	return $db->fetchAll($sql);
      }
      public function getALLPayment(){
      	$db = $this->getAdapter();
      	$sql="select * from ln_client_receipt_money";
      	return $db->fetchAll($sql);
      }
      public function getALLLoanlate($search = null){
   		$end_date = $search['end_date'];
      	$db = $this->getAdapter();
		$sql=" SELECT 			 
				  b.branch_namekh,				 
				  c.`client_number`,
				  c.`name_kh`,
				  c.`guarantor_name`,
				(SELECT `ln_village`.`village_namekh` FROM `ln_village` WHERE (`ln_village`.`vill_id` = `c`.`village_id`) LIMIT 1) AS `village_name`,
				(SELECT ln_commune.`commune_namekh` FROM `ln_commune` WHERE (ln_commune.`com_id` = `c`.`com_id`) LIMIT 1) AS `commune_name`,
				(SELECT `d`.`district_namekh` FROM `ln_district` `d` WHERE (`d`.`dis_id` = `c`.`dis_id`) LIMIT 1) AS `district_name`,
				(SELECT province_kh_name FROM `ln_province` WHERE province_id= c.pro_id  LIMIT 1) AS province_en_name,
				  c.`phone`,
				  l.`id` AS pawnId,
				  l.`release_amount` AS total_capital,
				  l.`loan_number`,
				  l.interest_rate  AS interest_rate,
				  l.`date_release`,
				  l.`date_line`,
				  l.`total_duration`,
				  l.`currency_type` AS curr_type,
				  l.`admin_fee`,
				  l.`product_description`,
				  d.id AS currentScheduleId,
				  (SELECT shh.id FROM `ln_pawnshop_detail` AS shh  WHERE  shh.pawn_id = d.`pawn_id` ORDER BY shh.id DESC LIMIT 1 ) AS lastScheduleId,
				  (SELECT `ln_currency`.`symbol` FROM `ln_currency` WHERE (`ln_currency`.`id` = l.`currency_type` ) LIMIT 1) AS `currency_type`,
				  (SELECT `ln_view`.`name_en` FROM `ln_view` WHERE ((`ln_view`.`type` = 14) AND (`ln_view`.`key_code` = `l`.`term_type`)) LIMIT 1) AS `termborrow`,
				  (SELECT `crm`.`date_input` FROM (`ln_pawn_receipt_money` `crm`) WHERE ((`crm`.`loan_id` = l.`id`)) ORDER BY `crm`.`date_input` DESC LIMIT 1) AS `last_pay_date`,
				  SUM(d.`principle_after`) AS principle_after,
				  SUM(d.`total_interest_after`) AS total_interest_after,
				  SUM(d.`total_payment_after`) AS total_payment_after,
				  d.`date_payment` ,
				  COUNT(l.`id`) AS amount_late,
				 l.`branch_id`,
				 `d`.`installment_amount`   AS `times`,
				  (SELECT p.product_kh FROM `ln_pawnshopproduct` AS p WHERE p.id = l.`product_id` LIMIT 1) AS productTitle
				FROM
				  `ln_pawnshop_detail` AS d,
				  `ln_pawnshop` AS l,
				  `ln_clientsaving` AS c ,
				  `ln_branch` AS b 
				WHERE 
				  d.`is_completed` = 0 
				  AND l.`id` = d.`pawn_id` 
				  AND l.`status` = 1 
				  AND l.is_dach = 0
				  AND d.`status`=1
				  AND c.`client_id` = l.`customer_id` 
				  AND b.`br_id`=l.branch_id 
				  AND ((principle_after+total_interest_after)>0) ";
      	$where='';
      	if(!empty($search['adv_search'])){
			$s_where = array();
			$s_search = str_replace(' ', '', addslashes(trim($search['adv_search'])));
      		
      		$s_where[] = " REPLACE(b.branch_namekh,' ','') LIKE '%{$s_search}%'";
      		$s_where[] = " REPLACE(l.`currency_type`,' ','') LIKE '%{$s_search}%'";
      		$s_where[] = " REPLACE(l.loan_number,' ','') LIKE '%{$s_search}%'";
      		$s_where[] = " REPLACE(l.product_description,' ','') LIKE '%{$s_search}%'";
      		$s_where[] = " REPLACE(c.client_number,' ','') LIKE '%{$s_search}%'";
      		$s_where[] = " REPLACE(c.name_kh,' ','') LIKE '%{$s_search}%'";
      		$s_where[] = " REPLACE(d.principle_after,' ','') LIKE '%{$s_search}%'";
      		$s_where[] = " REPLACE(d.total_interest_after,' ','') LIKE '%{$s_search}%'";
      		$s_where[] = " REPLACE(d.total_payment_after,' ','') LIKE '%{$s_search}%'";
      		$where .=' AND ('.implode(' OR ',$s_where).')';
      	}
      	  
      	if(!empty($search['end_date'])){
			$where.=" AND d.date_payment < '$end_date'";
		}
      	if($search['branch_id']>0){
      		$where.=" AND l.`branch_id` = ".$search['branch_id'];
      	}
		if(!empty($search['members'])){
			$where.=" AND l.`customer_id`  = ".$search['members'];
		}
      	$dbp = new Application_Model_DbTable_DbGlobal();
      	$where.=$dbp->getAccessPermission('l.`branch_id`');
        $group_by =" GROUP BY l.`id` ORDER BY d.`date_payment` ASC";
        return $db->fetchAll($sql.$where.$group_by);
      }
      
	  public function getAllLoanlateDetail($search = null){
      	$end_date = $search['end_date'];
      	$db = $this->getAdapter();
      	$sql="SELECT
		      	CONCAT(`co_firstname`,' ',co.`co_lastname`) AS co_name ,
		      	b.branch_namekh,
		      	co.`co_id`,
		      	c.`client_number`,
		      	c.`name_kh`,
		      	(SELECT `ln_village`.`village_name` FROM `ln_village` WHERE (`ln_village`.`vill_id` = `c`.`village_id`) limit 1) AS `village_name`,
		      	(SELECT ln_commune.`commune_name` FROM `ln_commune` WHERE (ln_commune.`com_id` = `c`.`com_id`) LIMIT 1) AS `commune_name`,
		      	(SELECT `d`.`district_name` FROM `ln_district` `d` WHERE (`d`.`dis_id` = `c`.`dis_id`) LIMIT 1) AS `district_name`,
		      	(SELECT province_en_name FROM `ln_province` WHERE province_id= c.pro_id  LIMIT 1) AS province_en_name,
		      	c.`phone`,
		      	l.`loan_amount`,
		      	l.`loan_number`,
		      	l.`interest_rate`,
		      	l.`date_release`,
		      	l.`date_line`,
		      	l.`total_duration`,
		      	l.`time_collect`,
		      	l.`currency_type` AS curr_type,
		      	l.`collect_typeterm`,
		      	
		      	(SELECT `ln_currency`.`symbol` FROM `ln_currency` WHERE (`ln_currency`.`id` = l.`currency_type` ) limit 1) AS `currency_type`,
		      	(SELECT `ln_view`.`name_en` FROM `ln_view` WHERE ((`ln_view`.`type` = 14) AND (`ln_view`.`key_code` = `l`.`pay_term`)) limit 1) AS `Term Borrow`,
		      	(SELECT `crm`.`date_input` FROM (`ln_client_receipt_money` `crm` JOIN `ln_client_receipt_money_detail` `crmd`) WHERE ((`crm`.`id` = `crmd`.`receipt_id`) AND (`crmd`.`lfd_id` = ld.`id`)) ORDER BY `crm`.`date_input` DESC LIMIT 1) AS `last_pay_date`,
		      	(ld.`principal_permonth`) AS total_principal,
		      	(ld.`principle_after`) AS principle_after,
		      	(ld.`total_interest_after`) AS total_interest_after,
		      	(ld.`total_payment_after`) AS total_payment_after,
		      	(ld.`penelize`) AS penelize,
		      	
		      	ld.`date_payment` ,
		      	l.`branch_id`
		    FROM
		      	`ln_loan_detail` AS ld,
		      	`ln_loan` AS l,
		      	`ln_co` AS co,
		      	`ln_client` AS c ,
		      	`ln_branch` AS b
	      	WHERE ld.`is_completed` = 0
	      		and l.id = ld.loan_id
		      	AND `l`.`is_badloan`=0
		      	AND l.`status` = 1
		      	AND ld.`status` = 1
		      	AND co.`co_id` = l.`co_id`
		      	AND c.`client_id` = l.`customer_id`
		      	AND b.`br_id`=l.`branch_id`
		      	AND l.is_reschedule!=1 ";
      	$where='';
      	if(!empty($search['adv_search'])){
      		$s_search = addslashes(trim($search['adv_search']));
      		$s_where[] = " b.branch_namekh LIKE '%{$s_search}%'";
      		$s_where[] = "  lm.`currency_type` LIKE '%{$s_search}%'";
      		$s_where[] = " lm.loan_number LIKE '%{$s_search}%'";
      		$s_where[] = " c.client_number LIKE '%{$s_search}%'";
      		$s_where[] = " c.name_kh LIKE '%{$s_search}%'";
      		$s_where[] = " f.principle_after LIKE '%{$s_search}%'";
      		$s_where[] = " f.principal_permonth LIKE '%{$s_search}%'";
      		$s_where[] = " f.total_interest_after LIKE '%{$s_search}%'";
      		$s_where[] = " f.total_payment_after LIKE '%{$s_search}%'";
      		$where .=' AND ('.implode(' OR ',$s_where).')';
      	}
      	if(($search['co_id']>0)){
      		$where.=" AND co.`co_id` =".$search['co_id'];//before use collect by
      	}
      	if(($search['client_name']>0)){
      		$where.=" AND lm.`client_id` =".$search['client_name'];//before use collect by
      	}
      	$from_date =(empty($search['start_date']))? '1': "  ld.date_payment > '".$search['start_date']." 00:00:00'";
      	$to_date = (empty($search['end_date']))? '1': "  ld.date_payment < '".$search['end_date']."  00:00:00'";
      	$where.= " AND ".$from_date." AND ".$to_date;
      	
      	if($search['branch_id']>0){
      		$where.=" AND f.`branch_id` = ".$search['branch_id'];
      	}
      	$group_by = " GROUP BY l.`customer_id`,ld.`date_payment` ORDER BY ld.`date_payment` ASC";
      	//$group_by = " GROUP BY lm.`member_id` ORDER BY f.`date_payment` ASC";
      	//echo $sql.$where.$group_by;
      	return $db->fetchAll($sql.$where.$group_by);
      }
      
      
      public function getALLLoandateline(){
      	//$to_date = (empty($search['to_date']))? '1': "date_payment <= '".$search['to_date']." 23:59:59'";
      	$db = $this->getAdapter();
//       	$sql="select g.level,(select first_name from rms_users where id=g.group_id) as first_name,(select last_name from rms_users where id=g.co_id)as last_name
// 		,g.zone_id,g.date_release,g.date_line,g.create_date,g.total_duration,g.first_payment,g.time_collect
// 		,g.collect_typeterm,g.pay_term,g.payment_method,g.holiday,g.is_renew,g.branch_id,g.loan_type,g.status,g.is_verify,g.is_badloan,g.teller_id
// 		,m.chart_id,m.member_id,m.loan_number,m.currency_type,m.total_capital,m.admin_fee,m.interest_rate,m.loan_cycle,m.loan_purpose,m.pay_before
// 		,m.pay_after,m.graice_period,m.amount_collect_principal,m.show_barcode,m.is_completed,m.semi from ln_loan_group as g,ln_loan_member as m where m.group_id = g.g_id";
      	$sql="SELECT g.level,(SELECT first_name FROM rms_users WHERE id=g.group_id) AS first_name,(SELECT last_name FROM rms_users WHERE id=g.co_id)AS last_name
		,g.zone_id,g.date_release,g.`date_release` AS date_line,g.create_date,g.total_duration,g.first_payment,g.time_collect
		,g.collect_typeterm,g.pay_term,g.payment_method,g.holiday,g.is_renew,g.branch_id,g.loan_type,g.status,g.is_verify,g.is_badloan,g.teller_id
		,m.chart_id,m.member_id,m.loan_number,m.currency_type,m.total_capital,m.admin_fee,m.interest_rate,m.loan_cycle,m.loan_purpose,m.pay_before
		,m.pay_after,m.graice_period,m.amount_collect_principal,m.show_barcode,m.is_completed FROM ln_loan_group AS g,ln_loan_member AS m WHERE m.group_id = g.g_id";
      	return $db->fetchAll($sql);
      }
      public function getALLLoanTotalcollect($search=null){
       	$db = $this->getAdapter();
        $start_date = $search['start_date'];
   		$end_date = $search['end_date'];
		$sql="SELECT * FROM v_getcollect WHERE is_completed = 0 ";
		$where ='';		
		if(!empty($search['start_date']) or !empty($search['end_date'])){
			$where.=" AND date_payment BETWEEN '$start_date' AND '$end_date'";
		}
		if($search['branch_id']>0){
			$where.=" AND branch_id= ".$search['branch_id'];
		}
		if($search['members']>0){
			$where.=" AND client_id = ".$search['members'];
		}
		 
		if($search['product_id']>0){
			$where.=" AND l.product_id = ".$search['product_id'];
		}
		if($search['currency_type']>0){
			$where.=" AND `l`.`currency_type` = ".$search['currency_type'];
		}
			if(!empty($search['adv_search'])){
				$s_where = array();
				$s_search = addslashes(trim($search['adv_search']));
				$s_where[] = " branch_name LIKE '%{$s_search}%'";
				$s_where[] = " client_name LIKE '%{$s_search}%'";
				$s_where[] = " co_name LIKE '%{$s_search}%'";
				$s_where[] = " total_principal LIKE '%{$s_search}%'";
				$s_where[] = " principal_permonth LIKE '%{$s_search}%'";
				$s_where[] = " total_interest LIKE '%{$s_search}%'";
				$s_where[] = " total_payment LIKE '%{$s_search}%'";
				$s_where[] = " amount_day LIKE '%{$s_search}%'";
				$where .=' AND ('.implode(' OR ',$s_where).')';
			}
		$order=" ORDER BY currency_type DESC ";
		return $db->fetchAll($sql.$where.$order);
      }
      public function getALLLoanPayment($search=null){
      	$db = $this->getAdapter();
			$sql="SELECT
				  (SELECT
				     `ln_branch`.`branch_namekh`
				   FROM `ln_branch` WHERE (`ln_branch`.`br_id` = `crm`.`branch_id`)
				   LIMIT 1) AS `branch_name`,
				  (SELECT `ln_currency`.`symbol`
				   FROM `ln_currency`
				   WHERE (`ln_currency`.`id` = `crm`.`currency_type`) LIMIT 1) AS `currency_typeshow`,
				  (SELECT `l`.`loan_number` FROM `ln_pawnshop` `l` WHERE (`l`.`id` = `crm`.`loan_id`) LIMIT 1) AS `loan_number`,
				  (SELECT `l`.`product_id` FROM `ln_pawnshop` `l` WHERE (`l`.`id` = `crm`.`loan_id`) LIMIT 1) AS `product_id`, 
				  (SELECT psp.product_kh FROM `ln_pawnshopproduct` AS psp WHERE psp.id = (SELECT `l`.`product_id` FROM `ln_pawnshop` `l` WHERE (`l`.`id` = `crm`.`loan_id`) LIMIT 1) LIMIT 1) AS proTitle,
				  (SELECT `c`.`name_kh` FROM `ln_clientsaving` `c` WHERE (`c`.`client_id` = `crm`.`client_id`) LIMIT 1) AS `client_name`,
				  (SELECT  `c`.`client_number` FROM `ln_clientsaving` `c` WHERE (`c`.`client_id` = `crm`.`client_id`) LIMIT 1) AS `client_number`,
				  (SELECT `u`.`first_name` FROM `rms_users` `u` WHERE (`u`.`id` = `crm`.`user_id`)) AS `user_name`,
				  `crm`.`id`                   AS `id`,
				  `crm`.`receipt_no`           AS `receipt_no`,
				  `crm`.`branch_id`            AS `branch_id`,
				  `crm`.`date_pay`             AS `date_pay`,
				  `crm`.`date_payment`         AS `date_payment`,
				  `crm`.`date_input`           AS `date_input`,
				  `crm`.`note`                 AS `note`,
				  `crm`.`user_id`              AS `user_id`,
				  `crm`.`status`               AS `status`,
				  `crm`.`payment_option`       AS `payment_option`,
				  `crm`.`currency_type`        AS `currency_type`,
				  `crm`.`is_payoff`            AS `is_payoff`,
				  `crm`.`total_payment`        AS `total_payment`,
				  `crm`.`principal_amount`     AS `principal_amount`,
				  `crm`.`interest_amount`      AS `interest_amount`,
				  `crm`.`principal_paid`       AS `principal_paid`,
				  `crm`.`interest_paid`        AS `interest_paid`,
				  `crm`.`service_paid`         AS `service_paid`,
				  `crm`.`penalize_paid`        AS `penalize_paid`,
				  `crm`.`total_paymentpaid`    AS `total_paymentpaid`,
				  `crm`.`recieve_amount`       AS `amount_recieve`,
				  `crm`.`return_amount`        AS `return_amount`,
				  `crm`.`penalize_amount`      AS `penelize`,
				  `crm`.`service_chargeamount` AS `service_charge`,
				  `crm`.`client_id`            AS `client_id`,
				  `crm`.`paid_times`           AS `paid_times`,
				  `crm`.`is_closed`           AS `is_closed`
				  
				FROM (`ln_pawn_receipt_money` `crm`
				   JOIN `ln_pawn_receipt_money_detail` `d`)
				WHERE ((`crm`.`status` = 1)
				       AND (`crm`.`id` = `d`.`receipt_id`)
				       AND (`crm`.`status` = 1))
				";
      	$from_date =(empty($search['start_date']))? '1': " crm.date_input >= '".$search['start_date']." 00:00:00'";
      	$to_date = (empty($search['end_date']))? '1': " crm.date_input <= '".$search['end_date']." 23:59:59'";
      	$where = " AND ".$from_date." AND ".$to_date;
      	
      	if($search['branch_id']>0){
      		$where.=" AND `crm`.branch_id= ".$search['branch_id'];
      	}
      	if($search['members']>0){
      		$where.=" AND `crm`.`client_id` = ".$search['members'];
      	} 
      	if (!empty($search['product_id'])){
	      	if($search['product_id']>0){
	      		$where.=" AND (SELECT `l`.`product_id` FROM `ln_pawnshop` `l` WHERE (`l`.`id` = `crm`.`loan_id`) LIMIT 1) = ".$search['product_id'];
	      	}
      	}
		if(!empty($search['adv_search'])){
// 			$s_search = addslashes(trim($search['adv_search']));
// 			if($s_search=='បង់មុន'){
// 	      		$where.=" AND payment_option = 2 ";
// 				$search['adv_search']='';
// 			}
		}
      	if(!empty($search['adv_search'])){
      		$s_where = array();
      		$s_search = addslashes(trim($search['adv_search']));
      		$s_where[] = " (SELECT `ln_branch`.`branch_namekh`  FROM `ln_branch` WHERE (`ln_branch`.`br_id` = `crm`.`branch_id`)   LIMIT 1) LIKE '%{$s_search}%'";
      		$s_where[] = " (SELECT `l`.`loan_number` FROM `ln_pawnshop` `l` WHERE (`l`.`id` = `crm`.`loan_id`) LIMIT 1) LIKE '%{$s_search}%'";
      		$s_where[] = " (SELECT  `c`.`client_number` FROM `ln_clientsaving` `c` WHERE (`c`.`client_id` = `crm`.`client_id`) LIMIT 1) LIKE '%{$s_search}%'";
      		$s_where[] = " (SELECT `c`.`name_kh` FROM `ln_clientsaving` `c` WHERE (`c`.`client_id` = `crm`.`client_id`) LIMIT 1) LIKE '%{$s_search}%'";
      		$s_where[] = " `crm`.`receipt_no` LIKE '%{$s_search}%'";
      		$where .=' AND ('.implode(' OR ',$s_where).')';			
			if($s_search=='បង់មុន'){
      			$where.=" AND `crm`.`payment_option` = 2 ";
      		}
      	}
      	if (!empty($search['currency_type'])){
      		$where.=" AND `crm`.`currency_type` = ".$search['currency_type'];
      	}
      	$dbp = new Application_Model_DbTable_DbGlobal();
      	$where.=$dbp->getAccessPermission('`crm`.branch_id');
      	$order=" GROUP BY `crm`.`id` ORDER BY `crm`.`id` DESC ";
//       	echo $sql.$where.$order;
      	return $db->fetchAll($sql.$where.$order);
      }
      public function getAllPaymentHistory($search=null){
      	$db = $this->getAdapter();
      	$sql="SELECT
		      	(SELECT
		      	`ln_branch`.`branch_namekh`
		      	FROM `ln_branch` WHERE (`ln_branch`.`br_id` = `crm`.`branch_id`)
		      	LIMIT 1) AS `branch_name`,
		      	(SELECT `ln_currency`.`symbol`
		      	FROM `ln_currency`
		      	WHERE (`ln_currency`.`id` = `crm`.`currency_type`) LIMIT 1) AS `currency_typeshow`,
		      	(SELECT `l`.`loan_number` FROM `ln_pawnshop` `l` WHERE (`l`.`id` = `crm`.`loan_id`) LIMIT 1) AS `loan_number`,
		      	(SELECT `c`.`name_kh` FROM `ln_clientsaving` `c` WHERE (`c`.`client_id` = `crm`.`client_id`) LIMIT 1) AS `client_name`,
		      	(SELECT  `c`.`client_number` FROM `ln_clientsaving` `c` WHERE (`c`.`client_id` = `crm`.`client_id`) LIMIT 1) AS `client_number`,
		      	(SELECT `u`.`first_name` FROM `rms_users` `u` WHERE (`u`.`id` = `crm`.`user_id`)) AS `user_name`,
		      	`crm`.`id`                   AS `id`,
		      	`crm`.`receipt_no`           AS `receipt_no`,
		      	`crm`.`branch_id`            AS `branch_id`,
		      	`crm`.`date_pay`             AS `date_pay`,
		      	`crm`.`date_payment`         AS `date_payment`,
		      	`crm`.`date_input`           AS `date_input`,
		      	`crm`.`note`                 AS `note`,
		      	`crm`.`user_id`              AS `user_id`,
		      	`crm`.`status`               AS `status`,
		      	`crm`.`payment_option`       AS `payment_option`,
		      	`crm`.`currency_type`        AS `currency_type`,
		      	`crm`.`is_payoff`            AS `is_payoff`,
		      	`crm`.`total_payment`        AS `total_payment`,
		      	`crm`.`principal_amount`     AS `principal_amount`,
		      	`crm`.`interest_amount`      AS `interest_amount`,
		      	`crm`.`principal_paid`       AS `principal_paid`,
		      	`crm`.`interest_paid`        AS `interest_paid`,
		      	`crm`.`service_paid`         AS `service_paid`,
		      	`crm`.`penalize_paid`        AS `penalize_paid`,
		      	`crm`.`total_paymentpaid`    AS `total_paymentpaid`,
		      	`crm`.`recieve_amount`       AS `amount_recieve`,
		      	`crm`.`return_amount`        AS `return_amount`,
		      	`crm`.`penalize_amount`      AS `penelize`,
		      	`crm`.`service_chargeamount` AS `service_charge`,
		      	`crm`.`client_id`            AS `client_id`,
		      	`crm`.`paid_times`           AS `paid_times`
		      	FROM (`ln_pawn_receipt_money` `crm`
		      	JOIN `ln_pawn_receipt_money_detail` `d`)
		      	WHERE ((`crm`.`status` = 1)
		      	AND (`crm`.`id` = `d`.`receipt_id`)
		      	AND (`crm`.`status` = 1))
		      	 ";
      	$from_date =(empty($search['start_date']))? '1': " date_input >= '".$search['start_date']." 00:00:00'";
      	$to_date = (empty($search['end_date']))? '1': " date_input <= '".$search['end_date']." 23:59:59'";
      	$where = " AND ".$from_date." AND ".$to_date;
      	 
      	if($search['branch_id']>0){
      		$where.=" AND branch_id= ".$search['branch_id'];
      	}
      	if($search['members']>0){
      		$where.=" AND client_id = ".$search['members'];
      	}
      
      	if(!empty($search['adv_search'])){
      		// 			$s_search = addslashes(trim($search['adv_search']));
      		// 			if($s_search=='បង់មុន'){
      		// 	      		$where.=" AND payment_option = 2 ";
      		// 				$search['adv_search']='';
      		// 			}
      	}
      	if(!empty($search['adv_search'])){
      		$s_where = array();
      		$s_search = addslashes(trim($search['adv_search']));
      		$s_where[] = " branch_name LIKE '%{$s_search}%'";
      		$s_where[] = " loan_number LIKE '%{$s_search}%'";
      		$s_where[] = " client_number LIKE '%{$s_search}%'";
      		$s_where[] = " client_name LIKE '%{$s_search}%'";
      		$s_where[] = " receipt_no LIKE '%{$s_search}%'";
      		$where .=' AND ('.implode(' OR ',$s_where).')';
      		if($s_search=='បង់មុន'){
      			$where.=" AND payment_option = 2 ";
      		}
      	}
      	$dbp = new Application_Model_DbTable_DbGlobal();
      	$where.=$dbp->getAccessPermission('branch_id');
      	
      	$order=" GROUP BY `crm`.`client_id`,`crm`.`id` ORDER BY `crm`.`client_id` ASC,`crm`.`loan_id` ASC,`crm`.`date_pay` ASC,`crm`.`receipt_no` ASC ";
      	return $db->fetchAll($sql.$where.$order);
      }
      public function getALLCommission($search=null){
      	$db = $this->getAdapter();
      	$sql="SELECT * FROM v_comissionco WHERE status=1 ";
      	$from_date =(empty($search['start_date']))? '1': " date_input >= '".$search['start_date']." 00:00:00'";
      	$to_date = (empty($search['end_date']))? '1': " date_input <= '".$search['end_date']." 23:59:59'";
      	$where = " AND ".$from_date." AND ".$to_date;
      	 
      	if($search['branch_id']>0){
      		$where.=" AND branch_id= ".$search['branch_id'];
      	}
      	if($search['client_name']>0){
      		$where.=" AND client_id = ".$search['client_name'];
      	}
      	if($search['co_id']>0){
      		$where.=" AND co_id = ".$search['co_id'];
      	}
      	if(!empty($search['adv_search'])){
      		$s_where = array();
      		$s_search = addslashes(trim($search['adv_search']));
      		$s_where[] = " branch_name LIKE '%{$s_search}%'";
      		$s_where[] = " loan_number LIKE '%{$s_search}%'";
      		$s_where[] = " client_number LIKE '%{$s_search}%'";
      		$s_where[] = " client_name LIKE '%{$s_search}%'";
      		$s_where[] = " co_name LIKE '%{$s_search}%'";
      		$s_where[] = " total_principal_permonth LIKE '%{$s_search}%'";
      		$s_where[] = " total_interest LIKE '%{$s_search}%'";
      		//$s_where[] = " amount_payment LIKE '%{$s_search}%'";
      		$s_where[] = " penalize_amount LIKE '%{$s_search}%'";
      		$s_where[] = " service_charge LIKE '%{$s_search}%'";
      		$s_where[] = " receipt_no LIKE '%{$s_search}%'";
      		$s_where[] = " receipt_no LIKE '%{$s_search}%'";
      		$where .=' AND ('.implode(' OR ',$s_where).')';
      	}
      	$order=" ORDER BY id DESC ";
      		return $db->fetchAll($sql.$where.$order);
      
      }
      
      
      
      
      
      
      
      public function getALLLoanIcome($search=null){
		$start_date = $search['start_date'];
    	$end_date = $search['end_date'];
    	
    	$db = $this->getAdapter();
    	$sql = " SELECT * FROM v_getcollectmoney where status=1 ";
//     	$sql = "SELECT lcrm.`id`,
// 					lcrm.`receipt_no`,
// 					lcrm.`loan_number`,lcrm.service_charge,
// 					(SELECT c.`name_kh` FROM `ln_client` AS c WHERE c.`client_id`=lcrm.`group_id`) AS team_group ,
// 					lcrm.`total_principal_permonth`,
// 					lcrm.`total_payment`,
//     			  (SELECT symbol FROM `ln_currency` WHERE id =lcrm.currency_type) AS currency_typeshow ,lcrm.currency_type,
// 					lcrm.`recieve_amount`,
// 					lcrm.`total_interest`,lcrm.amount_payment,
// 					lcrm.`penalize_amount`,
// 					lcrm.`date_pay`,
// 					lcrm.`date_input`,
// 				    (SELECT co.`co_khname` FROM `ln_co` AS co WHERE co.`co_id`=lcrm.`co_id`) AS co_name,
//     				(SELECT b.`branch_namekh` FROM `ln_branch` AS b WHERE b.`br_id`=lcrm.`branch_id`) AS branch
// 				FROM `ln_client_receipt_money` AS lcrm WHERE lcrm.is_group=0 AND lcrm.`status`=1";
    	$where ='';
    	if(!empty($search['advance_search'])){
    		//print_r($search);
    		$s_where = array();
    		$s_search = addslashes(trim($search['advance_search']));
    		$s_where[] = "client_name LIKE '%{$s_search}%'";
    		$s_where[] = " client_number LIKE '%{$s_search}%'";
    		$s_where[] = " co_name LIKE '%{$s_search}%'";
    		$s_where[] = " loan_number LIKE '%{$s_search}%'";
    		
    		$where .=' AND ('.implode(' OR ',$s_where).')';
    	}
    	if($search['status']!=""){
    		$where.= " AND status = ".$search['status'];
    	}
    	
    	if(!empty($search['start_date']) or !empty($search['end_date'])){
    		$where.=" AND date_input BETWEEN '$start_date' AND '$end_date'";
    	}
    	if($search['client_name']>0){
    		$where.=" AND client_id = ".$search['client_name'];
    	}
    	if($search['branch_id']>0){
    		$where.=" AND branch_id= ".$search['branch_id'];
    	}
    	if($search['co_id']>0){
    		$where.=" AND `co_id`= ".$search['co_id'];
    	}
    	$dbp = new Application_Model_DbTable_DbGlobal();
    	$where.=$dbp->getAccessPermission('branch_id');
    	//$where='';
    	$order="";
    	$order = " ORDER BY id DESC";
    	return $db->fetchAll($sql.$where.$order);
      }
      
      public function getALLLoanCollectionco($search=null){
      	$start_date = $search['start_date'];
      	$end_date = $search['end_date'];
      	 
      	$db = $this->getAdapter();
		$sql ="  SELECT 
				  crm.`receipt_no`,
				  crm.`date_input`,
				  crm.`co_id`,
				  crm.`payment_option`,
				  crm.`recieve_amount`,
				  l.`loan_number`,
				  (SELECT c.`phone` FROM ln_client AS c WHERE c.`client_id`=l.`customer_id`) AS phone,
				  (SELECT b.`branch_namekh` FROM `ln_branch` AS b WHERE b.`br_id`=crm.`branch_id`) AS branch,
				  (SELECT CONCAT(c.`co_code`,'-',c.`co_khname`) FROM ln_co AS c WHERE c.`co_id`=crm.`co_id`) AS co_name,
				  (SELECT c.`client_number` FROM ln_client AS c WHERE c.`client_id`=l.`customer_id`) AS client_code,
				  (SELECT c.`name_kh` FROM ln_client AS c WHERE c.`client_id`=l.`customer_id`) AS client_name,
				  l.`loan_type`,
				  l.`total_duration`,
				  l.`time_collect`,
				  l.`collect_typeterm`,
				  l.`date_release`,
				  l.`date_line`,
				  l.`interest_rate`,
				  l.`loan_amount` as capital,
				
				crm.principal_amount AS principal_amount,
				crm.interest_amount AS total_interest,
				crmd.`penelize_amount` AS penelize_amount,
				crm.`service_chargeamount` AS service_charge,
				(`crm`.`total_payment`) AS `total_payment`,
				
				crm.`recieve_amount` AS amount_recieve,
				crm.`return_amount` AS return_amount,
				`crm`.`principal_paid` AS `principal_paid`,
				(crm.`interest_paid`) AS interest_paid,
				(crm.`service_paid`) AS service_paid,
				crm.total_paymentpaid,
				 crm.`currency_type` AS curr_type,
				 crm.`date_payment`,
				 
				(SELECT `ln_currency`.`symbol` FROM `ln_currency` WHERE (`ln_currency`.`id` = crm.`currency_type`)) AS `currency_type`,
      			(SELECT `ln_view`.`name_en` FROM `ln_view` WHERE ((`ln_view`.`type` = 14) AND (`ln_view`.`key_code` = l.`pay_term` ))) AS name_en
				FROM
				  `ln_client_receipt_money` AS crm,
				  `ln_client_receipt_money_detail` AS crmd,
				  `ln_loan` AS l,
				  `ln_loan_detail` AS ld
				WHERE 
					crm.status=1
				  AND crmd.`lfd_id` = ld.`id` 
				  AND crmd.`receipt_id`=crm.`id`
				  AND l.`customer_id`=crm.`client_id`
				  and l.id = ld.loan_id
				  and l.id = crm.loan_id ";
      	$where ='';
      	if(!empty($search['advance_search'])){
      		//print_r($search);
      		$s_where = array();
      		$s_search = addslashes(trim($search['advance_search']));
      		$s_where[] = " l.`loan_number` LIKE '%{$s_search}%'";
      		$s_where[] = " crm.`receipt_no` LIKE '%{$s_search}%'";
      		$s_where[] = " crmd.`total_payment` LIKE '%{$s_search}%'";
      		$s_where[] = " crmd.`total_interest` LIKE '%{$s_search}%'";
      		$s_where[] = " crmd.`penelize_amount` LIKE '%{$s_search}%'";
      		$s_where[] = " crmd.`service_charge` LIKE '%{$s_search}%'";
      		$where .=' AND ('.implode(' OR ',$s_where).')';
      	}
      	if($search['status']!=""){
      		$where.= " AND crm.status = ".$search['status'];
      	}
      	 
      	if(!empty($search['start_date']) or !empty($search['end_date'])){
      		$where.=" AND crm.`date_input` BETWEEN '$start_date' AND '$end_date'";
      	}
      	if($search['client_name']>0){
      		$where.=" AND crmd.`client_id`= ".$search['client_name'];
      	}
      	if($search['branch_id']>0){
      		$where.=" AND crm.`branch_id`= ".$search['branch_id'];
      	}
      	if($search['co_id']>0){
      		$where.=" AND crm.`co_id`= ".$search['co_id'];
      	}
      	if($search['paymnet_type']>0){
      		$where.=" AND crm.`payment_option`= ".$search['paymnet_type'];
      	}
      	 
      //	`d`.`crm_id`
      	$groupby="  GROUP BY crmd.`receipt_id` ORDER BY crm.`co_id` ASC , crm.`date_input` DESC ";
      	return $db->fetchAll($sql.$where.$groupby);
      }
      public function getALLLTotalFee($search=null){
      	$start_date = $search['start_date'];
      	$end_date = $search['end_date'];
      
      	$db = $this->getAdapter();
      	$sql = "SELECT  SUM(admin_fee) AS admin_fee,SUM(other_fee) AS other_fee  FROM
      	v_loanreleased WHERE (admin_fee> 0 OR other_fee>0 )";
      	$where ='';
      	if(!empty($search['advance_search'])){
      		$s_where = array();
      		$s_search = addslashes(trim($search['advance_search']));
      		$s_where[] = " loan_number LIKE '%{$s_search}%'";
      		$s_where[] = " client_name LIKE '%{$s_search}%'";
      		$s_where[] = " total_capital LIKE '%{$s_search}%'";
      		$s_where[] = " admin_fee LIKE '%{$s_search}%'";
      		$s_where[] = " other_fee LIKE '%{$s_search}%'";
      		$where .=' AND ('.implode(' OR ',$s_where).')';
      	}
      	if($search['branch_id']>0){
      		$where.=" AND `branch_id`= ".$search['branch_id'];
      	}
      	if(!empty($search['start_date']) or !empty($search['end_date'])){
      		$where.=" AND date_release BETWEEN '$start_date' AND '$end_date'";
      	}
      	$dbp = new Application_Model_DbTable_DbGlobal();
      	$where.=$dbp->getAccessPermission('branch_id');
      	$order = " GROUP BY curr_type ORDER BY currency_type";
      	return $db->fetchRow($sql.$where.$order);
      }
      public function getALLLFee($search=null){
      	$start_date = $search['start_date'];
      	$end_date = $search['end_date'];
    		
      	$db = $this->getAdapter();
      	$sql = "SELECT  SUM(admin_fee) AS admin_fee,
						currency_type
      					 FROM 
      				ln_pawnshop WHERE status = 1 AND (admin_fee>0)";
		$where ='';
      	if(!empty($search['advance_search'])){
      		$s_where = array();
      		$s_search = addslashes(trim($search['advance_search']));
      		$s_where[] = " loan_number LIKE '%{$s_search}%'";
      		$where .=' AND ('.implode(' OR ',$s_where).')';
      	}
      	if($search['branch_id']>0){
      		$where.=" AND `branch_id`= ".$search['branch_id'];
      	}
      	if(!empty($search['start_date']) or !empty($search['end_date'])){
      		$from_date =(empty($search['start_date']))? '1': " `date_release` >= '".$search['start_date']." 00:00:00'";
      		$to_date = (empty($search['end_date']))? '1': " `date_release` <= '".$search['end_date']." 23:59:59'";
      		$where = " AND ".$from_date." AND ".$to_date;
      	}
      	
      	$dbp = new Application_Model_DbTable_DbGlobal();
      	$where.=$dbp->getAccessPermission('branch_id');
      	
      	$order = " GROUP BY currency_type ORDER BY currency_type";
      	return $db->fetchAll($sql.$where.$order);
      }
public function getALLLoanPayoff($search=null){
      	$db = $this->getAdapter();
      	$sql ="SELECT
			  `crm`.`id` AS `id`,
			  (SELECT
			     `ln_branch`.`branch_namekh`
			   FROM `ln_branch`
			   WHERE (`ln_branch`.`br_id` = `crm`.`branch_id`)
			   LIMIT 1) AS `branch_name`,
			  (SELECT
			     `ln_view`.`name_kh`
			   FROM `ln_view`
			   WHERE ((`ln_view`.`key_code` = `l`.`term_type`)
			          AND (`ln_view`.`type` = 1))
			   LIMIT 1) AS `termborrow`,
			  (SELECT
			     `c`.`name_kh`
			   FROM `ln_client` `c`
			   WHERE (`c`.`client_id` = `crm`.`client_id`)
			   LIMIT 1) AS `client_name`,
			  (SELECT
			     `c`.`client_number`
			   FROM `ln_client` `c`
			   WHERE (`c`.`client_id` = `crm`.`client_id`)
			   LIMIT 1) AS `client_number`,
			  `l`.`term_type`         AS `term_type`,
			  `l`.`loan_number`       AS `loan_number`,
			  `l`.`branch_id`         AS `branch_id`,
			  `l`.`customer_id`       AS `client_id`,
			  `l`.`release_amount`       AS `total_capital`,
			  `l`.`interest_rate`     AS `interest_rate`,
			  `l`.`currency_type`     AS `curr_type`,
			  `l`.`total_duration`    AS `total_duration`,
			  `l`.`date_release`      AS `date_release`,
			  `l`.`date_line`         AS `date_line`,
			  `l`.`term_type`         AS `term_type`,
			  (SELECT name_kh FROM `ln_view` WHERE type = 14 AND key_code = term_type ) term_type,
			  (SELECT
			     `ln_currency`.`symbol`
			   FROM `ln_currency`
			   WHERE (`ln_currency`.`id` = `crm`.`currency_type`)
			   LIMIT 1) AS `currency_type`,
			  `l`.`level`                  AS `loan_level`,
			  `crm`.`receipt_no`           AS `receipt_no`,
			  `crm`.`date_pay`             AS `date_pay`,
			  `crm`.`date_payment`         AS `date_payment`,
			  `crm`.`date_input`           AS `date_input`,
			  `crm`.`total_payment`        AS `total_payment`,
			  `crm`.`principal_amount`     AS `principal_amount`,
			  `crm`.`interest_amount`      AS `interest_amount`,
			  `crm`.`service_chargeamount` AS `service_chargeamount`,
			  `crm`.`penalize_amount`      AS `penelize`,
			  `crm`.`principal_paid`       AS `principal_paid`,
			  `crm`.`interest_paid`        AS `interest_paid`,
			  `crm`.`service_paid`         AS `service_paid`,
			  `crm`.`penalize_paid`        AS `penalize_paid`,
			  `crm`.`total_paymentpaid`    AS `total_paymentpaid`,
			  `crm`.`recieve_amount`       AS `amount_recieve`,
			  `crm`.`return_amount`        AS `return_amount`
			 
			FROM (`ln_pawnshop` `l`
			   JOIN `ln_pawn_receipt_money` `crm`)
			WHERE ((`crm`.`loan_id` = `l`.`id`)
			       AND (`crm`.`payment_option` = 4)) ";
    $where ='';
    $from_date =(empty($search['start_date']))? '1': " date_pay >= '".$search['start_date']." 00:00:00'";
    $to_date = (empty($search['end_date']))? '1': " date_pay <= '".$search['end_date']." 23:59:59'";
    $where= " AND ".$from_date." AND ".$to_date;
      	 
    if(!empty($search['advance_search'])){
      	$s_where = array();
      	$s_search = trim(addslashes($search['advance_search']));
      	$s_where[] = " `loan_number` LIKE '%{$s_search}%'";
      	$s_where[] = " `total_payment` LIKE '%{$s_search}%'";
      	$s_where[] = " `total_interest` LIKE '%{$s_search}%'";
      	$where .=' AND ('.implode(' OR ',$s_where).')';
    }
    if($search['branch_id']>0){
       $where.=" AND `crm`.`branch_id`= ".$search['branch_id'];
    }
    if($search['members']>0){
    	$where.=" AND client_id = ".$search['members'];
    }
    if($search['product_id']>0){
    	$where.=" AND l.product_id = ".$search['product_id'];
    }
    if($search['currency_type']>0){
    	$where.=" AND `l`.`currency_type` = ".$search['currency_type'];
    }   
    $dbp = new Application_Model_DbTable_DbGlobal();
    $sql.=$dbp->getAccessPermission('`crm`.`branch_id`');
    
    $order = " ORDER BY id DESC ";
    return $db->fetchAll($sql.$where.$order);
}
      public function getALLLoanExpectIncome($search=null){
      	$from_date =(empty($search['start_date']))? '1': " date_payment >= '".$search['start_date']." 00:00:00'";
      	$to_date = (empty($search['end_date']))? '1': " date_payment <= '".$search['end_date']." 23:59:59'";
      	$where= " AND ".$from_date." AND ".$to_date;
      	
      	$db = $this->getAdapter();
      	$sql ="SELECT
				  `d`.`id`                   AS `id`,
				  `d`.`principal_permonth`   AS `principal_permonth`,
				  `d`.`total_interest`       AS `total_interest`,
				  `d`.`principle_after`      AS `principle_after`,
				  `d`.`total_interest_after` AS `total_interest_after`,
				  `d`.`status`               AS `status`,
				  `d`.`date_payment`         AS `date_payment`,
				  (SELECT
				     `b`.`branch_namekh`
				   FROM `ln_branch` `b`
				   WHERE (`b`.`br_id` = `l`.`branch_id`)
				   LIMIT 1) AS `branch_name`,
				   
				  `l`.`date_release`         AS `date_release`,
				  `l`.`date_line`            AS `date_line`,
				  `l`.`total_duration`       AS `total_duration`,
				  (SELECT
				     `ln_view`.`name_en`
				   FROM `ln_view`
				   WHERE ((`ln_view`.`type` = 14)
				          AND (`ln_view`.`key_code` = `l`.`term_type`))
				   LIMIT 1) AS `termborrow`,
				  (SELECT
				     `ln_currency`.`symbol`
				   FROM `ln_currency`
				   WHERE (`ln_currency`.`id` = `l`.`currency_type`)
				   LIMIT 1) AS `currency_typeshow`,
				  `l`.`currency_type`        AS `currency_type`,
				  `l`.`branch_id`            AS `branch_id`,
				  `l`.`loan_number`          AS `loan_number`,
				  `l`.`interest_rate`        AS `interest_rate`,
				  `l`.`release_amount`          AS `total_capital`,
				  `c`.`client_number` AND `client_number`,
				  `c`.`name_kh`
				   
				FROM 
					(`ln_pawnshop_detail` `d`
				   JOIN `ln_pawnshop` AS `l`,
				   `ln_clientsaving` `c` )
				WHERE (`c`.`client_id` = `l`.`customer_id`
					   AND(`l`.`id` = `d`.`pawn_id`)      
				       AND (`d`.`status` = 1)
				       AND (`l`.`status` = 1)
				       AND (`l`.`is_dach` = 0)
      	            ) ";
      	if(!empty($search['advance_search'])){
      		$s_where = array();
      		$s_search = addslashes(trim($search['advance_search']));
      		$s_where[] = " loan_number LIKE '%{$s_search}%'";
      		$s_where[] = " client_number LIKE '%{$s_search}%'";
      		$s_where[] = " name_kh LIKE '%{$s_search}%'";
      		$s_where[] = " release_amount LIKE '%{$s_search}%'";
      		$s_where[] = " interest_rate LIKE '%{$s_search}%'";
      		$s_where[] = " total_duration LIKE '%{$s_search}%'";
      		$where .=' AND ( '.implode(' OR ',$s_where).')';
      	}

      	if($search['branch_id']>0){
      		$where.=" AND `l`.`branch_id` = ".$search['branch_id'];
      	}
      	if($search['members']>0){
      		$where.=" AND client_id = ".$search['members'];
      	}
      	 
      	if($search['product_id']>0){
      		$where.=" AND l.product_id = ".$search['product_id'];
      	}
      	if($search['currency_type']>0){
      		$where.=" AND `l`.`currency_type` = ".$search['currency_type'];
      	}
      	$dbp = new Application_Model_DbTable_DbGlobal();
      	$where.=$dbp->getAccessPermission('`l`.`branch_id`');
      	
      	$group_by = " GROUP BY `l`.`id`,`d`.`date_payment` 
      		ORDER BY currency_type ASC, date_payment DESC ";
      	return $db->fetchAll($sql.$where.$group_by);
      }
public function getALLBadloan($search=null){
      	$start_date = $search['start_date'];
      	$end_date = $search['end_date'];
      
      	$db = $this->getAdapter();
    	
    	$sql = "SELECT l.id,loan_number,b.branch_namekh,
    	CONCAT((SELECT client_number FROM `ln_client` WHERE client_id = l.client_code LIMIT 1),' - ',		
    	(SELECT name_en FROM `ln_client` WHERE client_id = l.client_code LIMIT 1)) AS client_name_en,
  		l.loss_date, l.`cash_type`,(SELECT c.symbol FROM `ln_currency` AS c WHERE c.status = 1 AND c.id = l.`cash_type`) AS currency_typeshow,
		l.total_amount ,l.intrest_amount ,CONCAT (l.tem,' Days')as tem,l.note,l.date,l.status FROM `ln_badloan` AS l,ln_branch AS b 
		WHERE b.br_id = l.branch AND l.is_writoff= 0";    	
    	$where='';
    	if(($search['status']>0)){
    		$where.=" AND l.status =".$search['status'];
    	}
    	if(!empty($search['start_date']) or !empty($search['end_date'])){
    		$where.=" AND l.date BETWEEN '$start_date' AND '$end_date'";
    	}
    	if(!empty($search['branch'])){
    		$where.=" AND b.br_id = ".$search['branch'];
    	}
    	if(!empty($search['client_name'])){
    		$where.=" AND l.client_code = ".$search['client_name'];
    	}
    	if(!empty($search['client_code'])){
    		$where.=" AND l.client_code = ".$search['client_code'];
    	}
    	if(!empty($search['Term'])){
    		$where.=" AND l.tem = ".$search['Term'];
    	}
    	if(!empty($search['cash_type'])){
    		$where.=" AND l.`cash_type` = ".$search['cash_type'];
    	}
    	if(!empty($search['adv_search'])){
    		$s_where=array();
    		$s_search=addslashes(trim($search['adv_search']));
    		$s_where[]=" l.note LIKE '%{$s_search}%'";
    		$s_where[]=" total_amount LIKE '%{$s_search}%'";
    		$s_where[]=" intrest_amount LIKE '%{$s_search}%'";
    		$s_where[]=" l.tem = '{$s_search}' ";
    		$where .=' AND ('.implode(' OR ',$s_where).' )';
    	}
    	$order = ' ORDER BY l.`cash_type` ';
    	return $db->fetchAll($sql.$where.$order);
}
public function getALLWritoff($search=null){
	$db = $this->getAdapter();
	$sql = "SELECT
				`l`.`id`             AS `id`,
				`l`.`loan_number`    AS `loan_number`,
				`l`.`branch_id`      AS `branch_id`,
				`l`.`customer_id`    AS `client_id`,
				`l`.`release_amount` AS `loan_amount`,
				`l`.`interest_rate`  AS `interest_rate`,
				`l`.`currency_type`  AS `curr_type`,
				`l`.`total_duration` AS `total_duration`,
				`l`.`date_release`   AS `date_release`,
				`l`.`date_line`      AS `date_line`,
				(SELECT name_kh FROM `ln_view` WHERE type = 14 AND key_code = term_type ) term_type,
				`c`.`client_number` AS  `client_number`,
				`c`.`name_en`  AS `client_name`,
				`c`.`phone` AS `phone`,
				`c`.`name_kh` AS `client_khname`,
				(SELECT
				`ln_branch`.`branch_namekh`
				FROM `ln_branch`
				WHERE (`ln_branch`.`br_id` = `l`.`branch_id`)
				LIMIT 1) AS `branch_name`,
				(SELECT
				`ln_currency`.`symbol` FROM `ln_currency`
				WHERE (`ln_currency`.`id` = `l`.`currency_type`)
				LIMIT 1) AS `currency_type`,
				(SELECT
				`ln_view`.`name_en`
				FROM `ln_view`
				WHERE ((`ln_view`.`type` = 14)
				AND (`ln_view`.`key_code` = `l`.`term_type`))
				LIMIT 1) AS `termborrow`,
				(SELECT product_kh FROM `ln_pawnshopproduct` WHERE id=l.product_id LIMIT 1) AS product_name,
				product_description,
				est_amount,
				 (SELECT `ln_village`.`village_namekh` FROM `ln_village` WHERE (`ln_village`.`vill_id` = `c`.`village_id`) limit 1) AS `village_name`,
					(SELECT `c`.`commune_namekh` FROM `ln_commune` `c` WHERE (`c`.`com_id` = `c`.`com_id`) LIMIT 1) AS `commune_name`,
					(SELECT `d`.`district_namekh` FROM `ln_district` `d` WHERE (`d`.`dis_id` = `c`.`dis_id`) LIMIT 1) AS `district_name`,
					(SELECT province_kh_name FROM `ln_province` WHERE province_id= c.pro_id  LIMIT 1) AS province_en_name,
					d.date_dach,
					d.note as note_dach	
				FROM
				`ln_pawnshop` AS  `l`,
				ln_clientsaving AS c,
				`ln_pawnshopdach` AS d
				WHERE 
					(`l`.`status` = 1)
					AND `c`.`client_id` = `l`.`customer_id`
					AND d.pawn_id=l.id
				  AND l.is_dach=1 ";
	$where ='';
	
	$from_date =(empty($search['start_date']))? '1': " date_dach >= '".$search['start_date']." 00:00:00'";
	$to_date = (empty($search['end_date']))? '1': " date_dach <= '".$search['end_date']." 23:59:59'";
	$where.= " AND ".$from_date." AND ".$to_date;
	
	if($search['branch_id']>0){
		$where.=" AND `l`.`branch_id` = ".$search['branch_id'];
	}
	if($search['members']>0){
		$where.=" AND client_id = ".$search['members'];
	}
	if($search['product_id']>0){
		$where.=" AND l.product_id = ".$search['product_id'];
	}
	if($search['currency_type']>0){
		$where.=" AND `l`.`currency_type` = ".$search['currency_type'];
	}
	
	if(!empty($search['adv_search'])){
		$s_where = array();
		$s_search = addslashes(trim($search['adv_search']));
		$s_search = str_replace(' ', '',$s_search);
		$s_where[] = " REPLACE(loan_number,' ','')  LIKE '%{$s_search}%'";
		$s_where[] = " REPLACE(client_number,' ','')LIKE '%{$s_search}%'";
		$s_where[] = " REPLACE(c.name_kh,' ','')  LIKE '%{$s_search}%'";
		$s_where[] = " REPLACE(release_amount,' ','')LIKE '%{$s_search}%'";
		$s_where[] = " REPLACE(interest_rate,' ','')LIKE '%{$s_search}%'";
		$where .=' AND ( '.implode(' OR ',$s_where).')';
	}
	$dbp = new Application_Model_DbTable_DbGlobal();
	$where.=$dbp->getAccessPermission('`l`.`branch_id`');
	$where.=" ORDER BY `l`.`branch_id`,`l`.`currency_type`";
	return $db->fetchAll($sql.$where);
//       	$db = $this->getAdapter();
//       	 $sql = "SELECT  
// 		 	v.*,ci.phone,
//             (SELECT `ln_village`.`village_namekh` FROM `ln_village` WHERE (`ln_village`.`vill_id` = `ci`.`village_id`) limit 1) AS `village_name`,
// 			(SELECT `c`.`commune_namekh` FROM `ln_commune` `c` WHERE (`c`.`com_id` = `ci`.`com_id`) LIMIT 1) AS `commune_name`,
// 			(SELECT `d`.`district_namekh` FROM `ln_district` `d` WHERE (`d`.`dis_id` = `ci`.`dis_id`) LIMIT 1) AS `district_name`,
// 			(SELECT province_kh_name FROM `ln_province` WHERE province_id= ci.pro_id  LIMIT 1) AS province_en_name	
// 		 FROM  v_badloan AS v,ln_client AS ci 
//       	 	WHERE v.client_id =ci.client_id ";
//       	$where='';
//       	$from_date =(empty($search['start_date']))? '1': " v.payof_date >= '".$search['start_date']." 00:00:00'";
//       	$to_date = (empty($search['end_date']))? '1': " v.payof_date <= '".$search['end_date']." 23:59:59'";
      	
//       	$where.= " AND ".$from_date." AND ".$to_date;

//       	if(!empty($search['branch'])){
//       		$where.=" AND v.branch_id = ".$search['branch'];
//       	}
//       	if(!empty($search['client_name'])){
//       		$where.=" AND v.client_id = ".$search['client_name'];
//       	}
//       	if(($search['co_id'])>0){
//       		$where.=" AND v.co_id = ".$search['co_id'];
//       	}
//       	if(!empty($search['Term'])){
//       		$where.=" AND v.tem = ".$search['Term'];
//       	}
//       	if(!empty($search['cash_type'])){
//       		$where.=" AND v.`curr_type` = ".$search['cash_type'];
//       	}
//       	if(!empty($search['adv_search'])){
//       		$s_where=array();
//       		$s_search=addslashes(trim($search['adv_search']));
//       		$s_where[] = " v.branch_name LIKE '%{$s_search}%'";
//       		$s_where[] = " v.loan_number LIKE '%{$s_search}%'";
//       		$s_where[] = " v.client_number LIKE '%{$s_search}%'";
//       		$s_where[] = " v.client_name LIKE '%{$s_search}%'";
//       		$s_where[] = " v.co_name LIKE '%{$s_search}%'";
//       		$s_where[] = " v.loan_amount LIKE '%{$s_search}%'";
      		
//       		$s_where[] = " v.interest_rate LIKE '%{$s_search}%'";
//       		$s_where[] = " v.loan_type LIKE '%{$s_search}%'";
      		
//       		$where .=' AND ('.implode(' OR ',$s_where).' )';
//       	}
//        	$order = ' ORDER BY v.co_name ';
//       	return $db->fetchAll($sql.$where.$order);
      }
      public function getALLNPLLoan($search=null){
      	    $db = $this->getAdapter();
      		$end_date =(empty($search['end_date']))? '1': " date_payment <= '".$search['end_date']." 23:59:59'";
      	    $db = $this->getAdapter();

      		$sql="SELECT * FROM v_getnplloan ";
      		$where=" WHERE ".$end_date;
      		if(!empty($search['adv_search'])){
      			
      			$s_where = array();
      			$s_search = addslashes(trim($search['adv_search']));
      			$s_where[] = " branch_name LIKE '%{$s_search}%'";
      			$s_where[] = " `loan_number` LIKE '%{$s_search}%'";
      			$s_where[] = " `client_number` LIKE '%{$s_search}%'";
      			$s_where[] = " `name_kh` LIKE '%{$s_search}%'";
      			$s_where[] = " `total_capital` LIKE '%{$s_search}%'";
      			$s_where[] = " `interest_rate` LIKE '%{$s_search}%'";
      			$s_where[] = " `total_duration` LIKE '%{$s_search}%'";
      			$s_where[] = " `term_borrow` LIKE '%{$s_search}%'";
      			$s_where[] = " `total_principal` LIKE '%{$s_search}%'";
      			
      			$where .=' AND ('.implode(' OR ',$s_where).')';
      		}
      		if($search['branch_id']>0){
      			$where.=" AND `branch_id` = ".$search['branch_id'];
      		}
      		if(!empty($search['cash_type'])){
      			$where.=" AND `curr_type` = ".$search['cash_type'];
      		}
      		//echo $sql.$where;
      		return $db->fetchAll($sql.$where);
      }
      public function getAllxchange($search = null){
      	$db = $this->getAdapter();
      	$sql = "SELECT * FROM `v_xchange` WHERE 1";
      	$where ='';
      	$from_date =(empty($search['start_date']))? '1': "statusDate >= '".$search['start_date']." 00:00:00'";
      	$to_date = (empty($search['end_date']))? '1': "statusDate <= '".$search['end_date']." 23:59:59'";
      	$where.= " AND ".$from_date." AND ".$to_date;
      	
//       	if($search['branch_id']>0){
//       		$where.=" AND branch_id = ".$search['branch_id'];
//       	}
//       	if($search['client_name']>0){
//       		$where.=" AND client_id = ".$search['client_name'];
//       	}
      	if(!empty($search['adv_search'])){
      		$s_where = array();
      		$s_search = trim(addcslashes($search['adv_search']));
      		$s_where[] = " branch_name LIKE '%{$s_search}%'";
      		$s_where[] = " client_name LIKE '%{$s_search}%'";
      		$s_where[] = " changedAmount LIKE '%{$s_search}%'";
      		$s_where[]=" fromAmount LIKE '%{$s_search}%'";
      		$s_where[] = " rate LIKE '%{$s_search}%'";
      		$s_where[]=" recieptNo LIKE '%{$s_search}%'";
      		$s_where[] = " recievedAmount LIKE '%{$s_search}%'";
      		$s_where[]=" status_in LIKE '%{$s_search}%'";
      		$s_where[] = " statusDate LIKE '%{$s_search}%'";
      		$s_where[]=" toAmount LIKE '%{$s_search}%'";
      		$s_where[]=" toAmountType LIKE '%{$s_search}%'";
      		$s_where[]=" fromAmountType LIKE '%{$s_search}%'";
      		$s_where[]=" from_to LIKE '%{$s_search}%'";
      		$s_where[]=" recievedType LIKE '%{$s_search}%'";
      		$s_where[]=" specail_customer LIKE '%{$s_search}%'";
      		
      		$where .=' AND ('.implode(' OR ',$s_where).')';
      	
      	}
      	$order=" ORDER BY id DESC";
//       	echo $sql.$where;
      	return $db->fetchAll($sql.$where.$order);
      	
      } 
      public function getRescheduleLoan($search = null){//rpt-loan-released/
      	$db = $this->getAdapter();
      	$sql = "SELECT * FROM v_rescheduleloan WHERE 1";
      	$where ='';
      
      	$from_date =(empty($search['start_date']))? '1': " reschedule_date >= '".$search['start_date']." 00:00:00'";
      	$to_date = (empty($search['end_date']))? '1': " reschedule_date <= '".$search['end_date']." 23:59:59'";
      	$where.= " AND ".$from_date." AND ".$to_date;
      
      	if($search['branch_id']>0){
      		$where.=" AND branch_id = ".$search['branch_id'];
      	}
      	if($search['client_name']>0){
      		$where.=" AND client_id = ".$search['client_name'];
      	}
      	
      	if($search['pay_every']>0){
      		$where.=" AND pay_term_id = ".$search['pay_every'];
      	}
      	if(!empty($search['adv_search'])){
      		$s_where = array();
      		$s_search = addslashes(trim($search['adv_search']));
      		$s_where[] = " branch_name LIKE '%{$s_search}%'";
      		$s_where[] = " re_loan_number LIKE '%{$s_search}%'";
      		$s_where[] = " client_number LIKE '%{$s_search}%'";
      		$s_where[] = " client_name LIKE '%{$s_search}%'";
      		
      		$s_where[] = " total_capital LIKE '%{$s_search}%'";
      		$s_where[] = " re_amount LIKE '%{$s_search}%'";
      		$s_where[] = " re_interest_rate LIKE '%{$s_search}%'";
      		
      		$s_where[] = " loan_type LIKE '%{$s_search}%'";
      		$where .=' AND ('.implode(' OR ',$s_where).')';
      	}
      	$order=" ORDER BY id DESC";
      	//echo $sql.$where;
      	return $db->fetchAll($sql.$where.$order);
      }
//       public function getAllLoanByCo($search=null){
//       	$start_date = $search['start_date'];
//       	$end_date = $search['end_date'];
//       	$db = $this->getAdapter();
//       	$sql="SELECT 
// 				  CONCAT(co.`co_code`,',',co.`co_khname`,'-',co.`co_firstname`,' ',co.`co_lastname`) AS co_name ,
// 				  b.branch_namekh,
// 				  co.`co_id`,
// 				  lm.`loan_number`,
// 				  c.`client_number`,
// 				  c.`name_kh`,
// 				  c.`phone`,
// 				  lm.`total_capital`,
// 				  lm.`interest_rate`,
// 				  lg.`date_release`,
// 				  lg.`date_line`,
// 				  lg.`total_duration`,
// 				  lm.`currency_type` AS curr_type,
// 				  lm.`collect_typeterm`,
// 				  lm.`pay_after`,
// 				  (SELECT `ln_currency`.`symbol` FROM `ln_currency` WHERE (`ln_currency`.`id` = lm.`currency_type`)) AS `currency_type`,
// 				  (SELECT `ln_view`.`name_en` FROM `ln_view` WHERE ((`ln_view`.`type` = 14) AND (`ln_view`.`key_code` = `lg`.`pay_term`))) AS `Term Borrow`,
// 				  (SELECT `crm`.`date_input` FROM (`ln_client_receipt_money` `crm` JOIN `ln_client_receipt_money_detail` `crmd`) WHERE ((`crm`.`loan_number` = lm.`loan_number`)
// 			       AND (`crm`.`id` = `crmd`.`crm_id`) AND (`crmd`.`lfd_id` = f.`id`)) ORDER BY `crm`.`date_input` DESC LIMIT 1) AS `last_pay_date`,
// 				  SUM(f.`total_principal`) AS total_principal,
// 				  SUM(f.`principle_after`) AS principle_after,
// 				  SUM(f.`total_interest_after`) AS total_interest_after,
// 				  SUM(f.`total_payment_after`) AS total_payment_after,
// 				  SUM(f.`penelize`) AS penelize,
// 				  SUM(f.`service_charge`) AS service_charge,
// 				  f.`date_payment` 
// 				FROM
// 				  `ln_loanmember_funddetail` AS f,
// 				  `ln_loan_group` AS lg,
// 				  `ln_loan_member` AS lm,
// 				  `ln_co` AS co,
// 				  `ln_client` AS c ,
//       			  `ln_branch` AS b 
// 				WHERE f.`is_completed` = 0 
// 				  AND lg.`g_id` = lm.`group_id` 
// 				  AND lm.`member_id` = f.`member_id` 
// 				  AND lg.`status` = 1 
// 				  AND co.`co_id` = lg.`co_id` 
// 				  AND c.`client_id` = lm.`client_id` 
// 				  AND b.`br_id`=f.`branch_id`
// 				";
//       	$where ='';
//       	$group_by=" GROUP BY lm.`group_id`,f.`date_payment` ";
//       	$order = " ORDER BY lg.`group_id`";
//       if(!empty($search['start_date']) or !empty($search['end_date'])){
//       		$where.=" AND f.`date_payment` BETWEEN '$start_date' AND '$end_date'";
//       	}
//       	if($search['client_name']!=""){
//       		$where.=" AND lg.`group_id`= ".$search['client_name'];
//       	}
//       	if($search['branch_id']>-1){
//       		$where.=" AND f.`branch_id`= ".$search['branch_id'];
//       	}
//       	if($search['co_id']!=""){
//       		$where.=" AND co.`co_id` = ".$search['co_id'];
//       	}
//       	if($search['status']!=""){
//       		$where.=" AND lm.`status`=".$search['status'];
//       	}
//       	if(!empty($search['advance_search'])){
//       		$s_where = array();
//       		$s_search = addslashes(trim($search['advance_search']));
//       		$s_where[] = " b.branch_namekh LIKE '%{$s_search}%'";
//       		$s_where[] = " lm.`loan_number` LIKE '%{$s_search}%'";
//       		$s_where[] = " name_kh LIKE '%{$s_search}%'";
//       		$s_where[] = " lm.total_capital LIKE '%{$s_search}%'";
//       		$where .=' AND ('.implode(' OR ',$s_where).')';
//       	}
//        	//echo $sql.$where.$group_by.$order;
//       	return $db->fetchAll($sql.$where.$group_by.$order);
//       }
//       public function getAllTransferoan($search = null){//rpt-loan-released/
//       	$db = $this->getAdapter();
//       	$sql = "SELECT * FROM v_gettransferloan WHERE 1";
//       	$where ='';
      
//       	$from_date =(empty($search['start_date']))? '1': " date >= '".$search['start_date']." 00:00:00'";
//       	$to_date = (empty($search['end_date']))? '1': " date <= '".$search['end_date']." 23:59:59'";
//       	$where.= " AND ".$from_date." AND ".$to_date;
      
//       	if($search['branch_id']>0){
//       		$where.=" AND branch_id = ".$search['branch_id'];
//       	}
//       	if($search['client_name']>0){
//       		$where.=" AND client_id = ".$search['client_name'];
//       	}
//       	if($search['co_id']>0){
//       		$where.=" AND ( `from` = ".$search['co_id']." OR `to` = ".$search['co_id'].") ";
//       	}
// //       	if($search['pay_every']>0){
// //       		$where.=" AND pay_term_id = ".$search['pay_every'];
// //       	}
//       	if(!empty($search['adv_search'])){
//       		$s_where = array();
//       		$s_search = addslashes(trim($search['adv_search']));
//       		$s_where[] = " branch_name LIKE '%{$s_search}%'";
//       		$s_where[] = " loan_number LIKE '%{$s_search}%'";
//       		$s_where[] = " client_number LIKE '%{$s_search}%'";
//       		$s_where[] = " client_name LIKE '%{$s_search}%'";
//       		$s_where[] = " from_coname LIKE '%{$s_search}%'";
//       		$s_where[] = " to_coname LIKE '%{$s_search}%'";
// //       		$s_where[] = " other_fee LIKE '%{$s_search}%'";
// //       		$s_where[] = " admin_fee LIKE '%{$s_search}%'";
// //       		$s_where[] = " interest_rate LIKE '%{$s_search}%'";
// //       		$s_where[] = " loan_type LIKE '%{$s_search}%'";
//       		$where .=' AND ('.implode(' OR ',$s_where).')';
//       	}
//       	return $db->fetchAll($sql.$where);
//       }
      
// public function getAllinfoZone($search = null){
//     	$db = $this->getAdapter();
//     	$sql = " SELECT id, 
// 	(SELECT branch_namekh FROM `ln_branch` 
// 	WHERE `ln_branch`.`br_id`=`ln_tranfser_zone`.`branch_id`)
// 	AS branch_id,

// 	(SELECT zone_name FROM `ln_zone`
// 	WHERE`ln_zone`.`zone_id`=`ln_tranfser_zone`.`from_zone`) 
// 	AS `from_zone`,
	
	
// 	(SELECT co_khname FROM `ln_co`
// 	WHERE `ln_co`.`co_id`=`ln_tranfser_zone`.`to_co`)
// 	AS to_co ,date, note, 
	
// 	(SELECT name_en FROM `ln_view` 
// 	WHERE TYPE=3 AND key_code =status)
// 	AS status 
	
//  	FROM `ln_tranfser_zone` WHERE 1 ";
    	
//     	$where='';
    	
//     	$from_date =(empty($search['start_date']))? '1': " date >= '".$search['start_date']." 00:00:00'";
//     	$to_date = (empty($search['end_date']))? '1': " date <= '".$search['end_date']." 23:59:59'";
//     	$where = " AND ".$from_date." AND ".$to_date;
    	
//     	//$order=" ORDER BY  DESC ";
// 		if($search["branch_name"]>-1){
// 		  	$where.=' AND branch_id='.$search["branch_name"];
// 		  }
// 	if(!empty($search['co_code'])){
//     		$where.=" AND to_co =".$search['co_code'];
//     	}
  
//        if(!empty($search['adv_search'])){
//     		$s_where=array();
//     		$s_search=addslashes(trim($search['adv_search']));
//     		$s_where[]=" note LIKE '%{$s_search}%'";
//     		$where .=' AND '.implode(' OR ',$s_where).' ';
//     	}
//     	//echo $sql.$where;
//     	return $db->fetchAll($sql.$where);
//     }
      
//       public function getClientLoanCo($search = null){//rpt-loan-released
//       	$db = $this->getAdapter();
      
//       	$sql = "SELECT *,sum(total_capital) as alltotal_principle,count(level) as totallevel FROM v_released_co WHERE 1";
//       	$where ='';
//       	$from_date =(empty($search['start_date']))? '1': " date_release >= '".$search['start_date']." 00:00:00'";
//       	$to_date = (empty($search['end_date']))? '1': " date_release <= '".$search['end_date']." 23:59:59'";
//       	$where.= " AND ".$from_date." AND ".$to_date;
      	 
//       	if($search['branch_id']>0){
//       		$where.=" AND branch_id = ".$search['branch_id'];
//       	}
//       	if($search['member']>0){
//       		$where.=" AND client_id = ".$search['member'];
//       	}
//       	if($search['co_id']>0){
//       		$where.=" AND co_id = ".$search['co_id'];
//       	}
//       	if($search['pay_every']>0){
//       		$where.=" AND pay_term_id = ".$search['pay_every'];
//       	}
//       	if(!empty($search['adv_search'])){
//       		$s_where = array();
//       		$s_search = addslashes(trim($search['adv_search']));
//       		$s_where[] = " branch_name LIKE '%{$s_search}%'";
      		
//       		$s_where[] = " client_number LIKE '%{$s_search}%'";
//       		$s_where[] = " client_name LIKE '%{$s_search}%'";
// //       		$s_where[] = " total_capital LIKE '%{$s_search}%'";
// //       		$s_where[] = " other_fee LIKE '%{$s_search}%'";
// //       		$s_where[] = " admin_fee LIKE '%{$s_search}%'";
//       		$s_where[] = " name_en LIKE '%{$s_search}%'";
//       		$s_where[] = " client_khname LIKE '%{$s_search}%'";
      		
//       		$s_where[] = " loan_type LIKE '%{$s_search}%'";
//       		$where .=' AND ('.implode(' OR ',$s_where).')';
//       	}
//       	$order = " GROUP BY client_id ORDER BY co_id DESC,totallevel DESC ";
//       	return $db->fetchAll($sql.$where.$order);
//       }
      function roundhundred($n,$cu_type){
      	if($cu_type==1){
      		$y = round($n);
      		$a = $y%100 > 0 ? ($y-($y%100)+100) : $y;
      		$x= $a;
      	}else{
      		$total = $n;
      		$x = number_format($total,2);
      	}
      	return $x;
      }
public function getLoanCollectIcome($search=null){
      	$start_date = $search['start_date'];
      	$end_date = $search['end_date'];
      	 
      	$db = $this->getAdapter();
      	$sql = " SELECT 
      		SUM(principal_paid) AS principal_paid,
      		SUM(interest_paid) AS interest_paid,
      	    SUM(service_paid) AS service_paid,
      	    SUM(penalize_paid) AS penalize_paid,
      	    currency_type
      	FROM v_getcollectmoney where status=1 ";
      	$where ='';
      	
      	if(!empty($search['start_date']) or !empty($search['end_date'])){
      		$where.=" AND date_input BETWEEN '$start_date' AND '$end_date'";
      	}
      	if($search['branch_id']>0){
      		$where.=" AND branch_id= ".$search['branch_id'];
      	}
      	if($search['currency_type']>0){
      		$where.=" AND currency_type= ".$search['currency_type'];
      	}
      	$dbp = new Application_Model_DbTable_DbGlobal();
      	$where.=$dbp->getAccessPermission('branch_id');
      	
      	$order = " Group by currency_type ORDER BY id DESC";
      	return $db->fetchAll($sql.$where.$order);
}
// public function getLoanadminFeeIcome($search=null){
//       	$start_date = $search['start_date'];
//       	$end_date = $search['end_date'];
      
//       	$db = $this->getAdapter();
//       	$sql = "SELECT SUM(admin_fee) AS admin_fee,SUM(other_fee) AS other_fee,curr_type FROM
//       	v_loanreleased WHERE 1 ";
//       	$where ='';
//       	if($search['branch_id']>0){
//       		$where.=" AND `branch_id`= ".$search['branch_id'];
//       	}
//       	if(!empty($search['start_date']) or !empty($search['end_date'])){
//       		$where.=" AND date_release BETWEEN '$start_date' AND '$end_date'";
//       	}
//       	if($search['currency_type']>0){
//       		$where.=" AND curr_type= ".$search['currency_type'];
//       	}
//       	$order = " GROUP BY curr_type ORDER BY curr_type ";
//       	return $db->fetchAll($sql.$where.$order);
// }
      
      function getExpenseincomereport($search=null){
      	$this->_name='ln_income_expense';
      	$db = $this->getAdapter();
      	$session_user=new Zend_Session_Namespace(SYSTEM_SES);
      	$from_date =(empty($search['start_date']))? '1': " date >= '".$search['start_date']." 00:00:00'";
      	$to_date = (empty($search['end_date']))? '1': " date <= '".$search['end_date']." 23:59:59'";
      	
      	$where = " WHERE ".$from_date." AND ".$to_date;
      
      	$sql=" SELECT id,
      	(SELECT branch_namekh FROM `ln_branch` WHERE ln_branch.br_id = branch_id LIMIT 1) AS branch_name,
      	curr_type,SUM(total_amount) AS total_amount,disc,date,status 
      	FROM $this->_name ";
      	
      	if($search['currency_type']>0){
      		$where.= " AND curr_type = ".$search['currency_type'];
      	}
      	if($search['branch_id']>0){
      		$where.=" AND `branch_id`= ".$search['branch_id'];
      	}
      	$dbp = new Application_Model_DbTable_DbGlobal();
      	$where.=$dbp->getAccessPermission('branch_id');
      	
      	$order=" GROUP BY curr_type order by id desc ";
      	return $db->fetchAll($sql.$where.$order);
      }
// 	  function getLoanByVillage(){
//       	$db = $this->getAdapter();
//       	$sql=" SELECT `l`.`loan_number`     AS `loan_number`,
// 				  (`l`.`loan_amount`) AS `total_capital`,
// 				  `l`.`interest_rate`   AS `interest_rate`,
// 				  `l`.`currency_type`   AS `curr_type`,
// 				  `l`.`customer_id`       AS `client_id`,
// 				  `l`.`branch_id` AS `br_id`,
// 				  `l`.`date_release`   AS `date_release`,
// 				  `l`.`date_line`      AS `date_line`,
// 				  `l`.`co_id`          AS `co_id`,
// 				  `l`.`total_duration` AS `total_duration`,
// 				  `l`.`loan_type`      AS `loantype`,
// 				  (SELECT
// 				     `ln_branch`.`branch_namekh`
// 				   FROM `ln_branch`
// 				   WHERE (`ln_branch`.`br_id` = `l`.`branch_id`)
// 				   LIMIT 1) AS `branch_name`,
// 				   `c`.`client_number`,
// 				   `c`.`name_kh` AS `client_kh`,
// 				   `c`.`name_en` AS `client_en`,
//    				(SELECT `ln_village`.`village_name` FROM `ln_village` WHERE (`c`.`village_id`) LIMIT 1 ) AS `village_name`,
// 				(SELECT `cm`.`commune_name` FROM `ln_commune` `cm` WHERE (`cm`.`com_id` = `c`.`com_id`) LIMIT 1 ) AS `commune_name`,
// 				(SELECT `d`.`district_name` FROM `ln_district` `d` WHERE (`d`.`dis_id` = `c`.`dis_id`) LIMIT 1 ) AS `district_name`,
// 				(SELECT province_en_name FROM `ln_province` WHERE province_id= c.pro_id  LIMIT 1 ) AS province_en_name,
				
// 				(SELECT
// 				     `ln_currency`.`symbol`
// 				   FROM `ln_currency`
// 				   WHERE (`ln_currency`.`id` = `l`.`currency_type`)) AS `currency_type`,
// 				  (SELECT SUM(principal_paid) FROM `ln_client_receipt_money` AS cd WHERE cd.loan_id =l.id ) AS total_payment,			 	  
// 				  (SELECT `ln_view`.`name_en` FROM `ln_view` WHERE ((`ln_view`.`type` = 24) AND (`ln_view`.`key_code` = `l`.`for_loantype`)) LIMIT 1) AS `loan_type`,
// 				  (SELECT `ln_view`.`name_en` FROM `ln_view` WHERE ((`ln_view`.`type` = 14) AND (`ln_view`.`key_code` = `l`.`pay_term`))) AS `pay_term`,
// 				  (SELECT `ln_co`.`co_khname` FROM `ln_co` WHERE (`ln_co`.`co_id` = `l`.`co_id`)) AS `co_name`,
// 				  (SELECT `ln_view`.`name_en` FROM `ln_view` WHERE ((`ln_view`.`type` = 14) AND (`ln_view`.`key_code` = `l`.`pay_term`))) AS `name_en`
// 				FROM (`ln_loan` AS `l`,ln_client AS c )				   
// 				WHERE (
// 				        (`l`.`status` = 1)
// 				       AND `l`.`customer_id`= c.client_id
// 				       AND (`l`.`is_completed` = 0))
// 				GROUP BY `l`.`id` ORDER BY 
//                c.pro_id,c.dis_id,c.com_id,c.village_id  ";
//       	return $db->fetchAll($sql);
//       }
//       public function getALLTotalWritoff($search=null){
//       	$db = $this->getAdapter();
//       	$sql = " SELECT
//       	curr_type,
//       	(v.total_amount) AS total_amount
//       		FROM  v_badloan AS v 
//       	WHERE v.status=1 ";
//       	$where='';
//       	$from_date =(empty($search['start_date']))? '1': " v.payof_date >= '".$search['start_date']." 00:00:00'";
//       	$to_date = (empty($search['end_date']))? '1': " v.payof_date <= '".$search['end_date']." 23:59:59'";
      	 
//       	$where.= " AND ".$from_date." AND ".$to_date;
      
//       	if(!empty($search['branch'])){
//       		$where.=" AND v.branch_id = ".$search['branch'];
//       	}
      	
//       	if(!empty($search['currency_type'])){
//       		$where.=" AND v.`curr_type` = ".$search['currency_type'];
//       	}
//       	$order = ' GROUP BY v.`curr_type` ORDER BY v.co_name ';
//       	return $db->fetchAll($sql.$where.$order);
//       }
//       function getLoandisburseByMonth(){
//       	$sql="SELECT
// 			   SUM(`m`.`total_capital`) AS `total_capital`,
// 			   `m`.`currency_type`   AS `curr_type`,
// 			  `lg`.`date_release`   AS `date_release`,
// 			  (SELECT
// 			     `ln_currency`.`symbol`
// 			   FROM `ln_currency`
// 			   WHERE (`ln_currency`.`id` = `m`.`currency_type`)) AS `currency_type`,
// 			  (SELECT
// 			     `ln_view`.`name_en`
// 			   FROM `ln_view`
// 			   WHERE ((`ln_view`.`type` = 24)
// 			          AND (`ln_view`.`key_code` = `lg`.`for_loantype`))
// 			   LIMIT 1) AS `loan_type`
// 			FROM (`ln_loan_group` `lg`
// 			   JOIN `ln_loan_member` `m`)
// 			WHERE ((`lg`.`g_id` = `m`.`group_id`)
// 			       AND (`m`.`status` = 1)
// 			       AND (`lg`.`is_reschedule` <> 1)
// 			       AND (`lg`.`g_id` = `m`.`group_id`))
// 			GROUP BY YEAR(date_release),MONTH(date_release),`m`.`currency_type` ";
//       	$db = $this->getAdapter();
//       	return $db->fetchAll($sql);
//       }
      public function getALLParLoan($search=null){
      	$db = $this->getAdapter();
      	$end_date = $search['end_date'];
      	$sql="SELECT * FROM v_getloanpar ";
      	$where=" WHERE 1 ";
      	if(!empty($search['adv_search'])){
      		 
//       		$s_where = array();
//       		$s_search = addslashes(trim($search['adv_search']));
//       		$s_where[] = " branch_name LIKE '%{$s_search}%'";
//       		$s_where[] = " `loan_number` LIKE '%{$s_search}%'";
//       		$s_where[] = " `client_number` LIKE '%{$s_search}%'";
//       		$s_where[] = " `name_kh` LIKE '%{$s_search}%'";
//       		$s_where[] = " `total_capital` LIKE '%{$s_search}%'";
//       		$s_where[] = " `interest_rate` LIKE '%{$s_search}%'";
//       		$s_where[] = " `total_duration` LIKE '%{$s_search}%'";
//       		$s_where[] = " `term_borrow` LIKE '%{$s_search}%'";
//       		$s_where[] = " `total_principal` LIKE '%{$s_search}%'";
      		 
//       		$where .=' AND ('.implode(' OR ',$s_where).')';
      	}
//       	if($search['branch_id']>0){
//       		$where.=" AND `branch_id` = ".$search['branch_id'];
//       	}
      	if(!empty($search['end_date'])){
      		$where.=" AND date_payment < '$end_date'";
      	}
//       	if(!empty($search['cash_type'])){
//       		$where.=" AND `curr_type` = ".$search['cash_type'];
//       	}

      	return $db->fetchAll($sql.$where);
      }
      public function getLoanlateById($search = null){
      	$end_date = $search['end_date'];
      	$db = $this->getAdapter();
      	$sql="
      		SELECT
      	(SELECT `crm`.`date_input` FROM (`ln_client_receipt_money` `crm` JOIN `ln_client_receipt_money_detail` `crmd`) 
      	WHERE `crm`.`loan_number` = lm.`loan_number`) AS date_input,
      	SUM(f.`total_principal`) AS total_principal,
      	SUM(f.`principle_after`) AS principle_after,
      	SUM(f.`total_interest_after`) AS total_interest_after,
      	SUM(f.`total_payment_after`) AS total_payment_after,
      	SUM(f.`penelize`) AS penelize,
      	SUM(f.`service_charge`) AS service_charge,
      	f.`date_payment` 
      	FROM
      	`ln_loanmember_funddetail` AS f,
      	`ln_loan_group` AS lg,
      	`ln_loan_member` AS lm,
      	`ln_branch` AS b
      	WHERE 
      	f.`is_completed` = 0
      	AND lg.`g_id` = lm.`group_id`
      	AND lm.`member_id` = f.`member_id`
      	AND lg.`status` = 1
      	AND lm.`status` = 1
      	AND f.`status`=1
      	AND b.`br_id`=f.`branch_id`
      	AND lm.is_reschedule!=1 ";
      	$where=" AND lm.loan_number= '".$search['loan_number']."'";
//       	if(!empty($search['adv_search'])){
//       		$s_search = addslashes(trim($search['adv_search']));
//       		$s_where[] = " b.branch_namekh LIKE '%{$s_search}%'";
//       		$s_where[] = "  lm.`currency_type` LIKE '%{$s_search}%'";
 //     		$s_where[] = " lm.loan_number LIKE '%{$s_search}%'";
//       		$s_where[] = " c.client_number LIKE '%{$s_search}%'";
//       		$s_where[] = " c.name_kh LIKE '%{$s_search}%'";
//       		$s_where[] = " f.principle_after LIKE '%{$s_search}%'";
//       		$s_where[] = " f.principal_permonth LIKE '%{$s_search}%'";
//       		$s_where[] = " f.total_interest_after LIKE '%{$s_search}%'";
//       		$s_where[] = " f.total_payment_after LIKE '%{$s_search}%'";
//       		$where .=' AND ('.implode(' OR ',$s_where).')';
//       	}
      	if(!empty($search['end_date'])){
      		$where.=" AND f.date_payment < '$end_date'";
      	}
      	
      	if($search['branch_id']>0){
      		$where.=" AND f.`branch_id` = ".$search['branch_id'];
      	}
      	if($search['client_name']>0){
      		$where.=" AND lm.client_id = ".$search['client_name'];
      	}
        if($search['currency_type']>0){
      		$where.=" AND lm.currency_type = ".$search['currency_type'];
      	}
      	$group_by = " GROUP BY lm.`group_id`,f.`date_payment` ORDER BY f.`date_payment` ASC";
      	$row =  $db->fetchAll($sql.$where.$group_by);
      	$result =array();
      	$total_principal=0;$total_interest=0;
      	foreach ($row as $rs){
      		$total_principal = $total_principal+$rs['principle_after'];
      		$total_interest = $total_interest+$rs['total_interest_after'];
      	}
      	$result["principle_after"]=$total_principal;
      	$result["total_interest_after"]=$total_interest;
      	return $result;
      }
      function getContractinfo($id){
      	$sql = "SELECT lm.*,g.total_duration,g.pay_term,
      	(SELECT curr_namekh FROM `ln_currency` WHERE id=lm.currency_type LIMIT 1) as curr_type
      	FROM ln_loan_member AS lm ,ln_loan_group AS g WHERE lm.member_id = g.g_id
      	AND lm.member_id = $id LIMIT 1";
      	$db = $this->getAdapter();
      	return $db->fetchRow($sql);
      }
      function getCalleteralByClient($client_id){
      	$db = $this->getAdapter();
   $sql =" SELECT
`d`.`number_collecteral` AS `number_collecteral`,
`d`.`is_return`          AS `is_return`,
`d`.`issue_date`         AS `issue_date`,
`d`.`note`               AS `note`,
d.collecteral_code, 
d.owner_name AS c_ownername,
relative ,   
(SELECT
`ct`.`title_en`
FROM `ln_callecteral_type` `ct`
WHERE (`ct`.`id` = `d`.`collecteral_type`)) AS `collecteral_title_en`,
(SELECT
`ct`.`title_kh`
FROM `ln_callecteral_type` `ct`
WHERE (`ct`.`id` = `d`.`collecteral_type`)) AS `collecteral_title_kh`,
(SELECT
`ln_view`.`name_kh`
FROM `ln_view`
WHERE ((`ln_view`.`type` = 21)
     AND (`ln_view`.`key_code` = `d`.`owner_type`))) AS `collecteral_owner`,
 d.number_collecteral,
  d.issue_date,
  d.note as coll_note,
  (SELECT
     `ln_branch`.`branch_namekh`
   FROM `ln_branch`
   WHERE (`ln_branch`.`br_id` = `c`.`branch_id`)
   LIMIT 1) AS `branch_name`,
  (SELECT
     `ln_co`.`co_khname`
   FROM `ln_co`
   WHERE (`ln_co`.`co_id` = `c`.`co_id`)
   LIMIT 1) AS `co_id`,
  `d`.`collecteral_code`   AS `collecteral_code`,
  `d`.`owner_name` AS `owner_name`,
  `cl`.`client_number` AS `client_code`,
  `cl`.`name_kh` AS `name_kh`,
  `cl`.`name_en` AS `client_name`,
  (SELECT name_kh FROM `ln_view` WHERE TYPE=11 AND key_code=cl.sex) AS gender,
  `cl`.`client_id`          AS `client_id`,
  `cl`.`branch_id`          AS `branch_id`,
   cl.`join_with`,
   cl.`relate_with`,
   cl.`spouse_name`,
   cl.dob_guarantor,
   cl.guarantor_with,
   cl.dob,
   cl.house,cl.street,
(SELECT `ln_village`.`village_namekh` FROM `ln_village` WHERE (`ln_village`.`vill_id` = `cl`.`village_id`)) AS `village_name`,
(SELECT `c`.`commune_name` FROM `ln_commune` `c` WHERE (`c`.`com_id` = `cl`.`com_id`) LIMIT 1) AS `commune_name`,
(SELECT `d`.`district_namekh` FROM `ln_district` `d` WHERE (`d`.`dis_id` = `cl`.`dis_id`) LIMIT 1) AS `district_name`,
(SELECT province_kh_name FROM `ln_province` WHERE province_id= cl.pro_id  LIMIT 1) AS province_en_name,
(SELECT name_kh FROM `ln_view` WHERE id = cl.client_d_type LIMIT 1) AS client_doctype,
cl.nation_id,
dob_join_acc ,
`c`.`date`               AS `date`,
`c`.`status`             AS `status`
FROM (
ln_client AS cl ,
`ln_client_callecteral` `c`
JOIN `ln_client_callecteral_detail` `d`)
WHERE ((
cl.client_id = c.client_id AND
`c`.`id` = `d`.`client_coll_id`)
AND (`c`.`status` = 1)
AND (`d`.`status` = 1)
AND cl.client_id = $client_id )";
      	$order = " ORDER BY client_id ";
      	return $db->fetchAll($sql.$order);
      }
      function getAllExpenseReport($search=null){
      	$db = $this->getAdapter();
      	$session_user=new Zend_Session_Namespace(SYSTEM_SES);
      	$from_date =(empty($search['start_date']))? '1': " date >= '".$search['start_date']." 00:00:00'";
      	$to_date = (empty($search['end_date']))? '1': " date <= '".$search['end_date']." 23:59:59'";
      	$where = " WHERE ".$from_date." AND ".$to_date;
      
      	$sql=" SELECT id,
      	(SELECT branch_namekh FROM `ln_branch` WHERE ln_branch.br_id =branch_id LIMIT 1) AS branch_name,
      	account_id,
      	(SELECT symbol FROM `ln_currency` WHERE ln_currency.id =curr_type) AS currency_type,invoice,
      	curr_type,
      	total_amount,disc,date,status FROM ln_income_expense ";
      
      	if (!empty($search['adv_search'])){
      		$s_where = array();
      		$s_search = trim(addslashes($search['adv_search']));
      		$s_where[] = " account_id LIKE '%{$s_search}%'";
      		$s_where[] = " title LIKE '%{$s_search}%'";
      		$s_where[] = " total_amount LIKE '%{$s_search}%'";
      		$s_where[] = " invoice LIKE '%{$s_search}%'";
      
      		$where .=' AND ('.implode(' OR ',$s_where).')';
      	}
      	if($search['status']>-1){
      		$where.= " AND status = ".$search['status'];
      	}
//       	if($search['currency_type']>-1){
//       		$where.= " AND curr_type = ".$search['currency_type'];
//       	}
      	$dbp = new Application_Model_DbTable_DbGlobal();
      	$where.=$dbp->getAccessPermission('branch_id');
      	$order=" order by id desc ";
      	return $db->fetchAll($sql.$where.$order);
      }
      function getAllOtherIncomeReport($search=null){
      	$db = $this->getAdapter();
      	$session_user=new Zend_Session_Namespace(SYSTEM_SES);
      	$from_date =(empty($search['start_date']))? '1': " date >= '".$search['start_date']." 00:00:00'";
      	$to_date = (empty($search['end_date']))? '1': " date <= '".$search['end_date']." 23:59:59'";
      	$where = " WHERE ".$from_date." AND ".$to_date;
      
      	$sql=" SELECT id,
      	(SELECT branch_namekh FROM `ln_branch` WHERE ln_branch.br_id =branch_id LIMIT 1) AS branch_name,
      	account_id,SUM(total_amount) AS total_amount,
      	disc,date,curr_type,status FROM ln_income ";
      
      	if($search['currency_type']>0){
      		$where.= " AND curr_type = ".$search['currency_type'];
      	}
      	$dbp = new Application_Model_DbTable_DbGlobal();
      	$where.=$dbp->getAccessPermission('branch_id');
      	
      	$order=" GROUP BY curr_type order by curr_type desc ";
      	return $db->fetchAll($sql.$where.$order);
      }
      function getTotalOtherIncomeReport($search=null){
      	$db = $this->getAdapter();
      	$session_user=new Zend_Session_Namespace(SYSTEM_SES);
      	$from_date =(empty($search['start_date']))? '1': " date >= '".$search['start_date']." 00:00:00'";
      	$to_date = (empty($search['end_date']))? '1': " date <= '".$search['end_date']." 23:59:59'";
      	$where = " WHERE ".$from_date." AND ".$to_date;
      
      	$sql=" SELECT id,
      	(SELECT branch_namekh FROM `ln_branch` WHERE ln_branch.br_id =branch_id LIMIT 1) AS branch_name,
      	account_id,
      	(SELECT symbol FROM `ln_currency` WHERE ln_currency.id =curr_type) AS currency_type,invoice,
      	curr_type,
      	SUM(total_amount) total_amount,disc,date,status FROM ln_income ";
      
      	if (!empty($search['adv_search'])){
      		$s_where = array();
      		$s_search = trim(addslashes($search['adv_search']));
      		$s_where[] = " account_id LIKE '%{$s_search}%'";
      		$s_where[] = " title LIKE '%{$s_search}%'";
      		$s_where[] = " total_amount LIKE '%{$s_search}%'";
      		$s_where[] = " invoice LIKE '%{$s_search}%'";
      
      		$where .=' AND ('.implode(' OR ',$s_where).')';
      	}
      	if($search['status']>-1 ){
      		$where.= " AND status = ".$search['status'];
      	}
      	if($search['currency_type']>-1){
      	    $where.= " AND curr_type = ".$search['currency_type'];
        }
        $dbp = new Application_Model_DbTable_DbGlobal();
        $where.=$dbp->getAccessPermission('branch_id');
        
      	$order=" order by id desc ";
//       	echo$sql.$where.$order;exit();
      	return $db->fetchRow($sql.$where.$order);
      }
      public function getCollectDailyPayment($search=null){
      	$db = $this->getAdapter();
      	$sql="SELECT * FROM v_getcollectmoney WHERE status=1 ";
      	$from_date =(empty($search['start_date']))? '1': " date_input >= '".$search['start_date']." 00:00:00'";
      	$to_date = (empty($search['end_date']))? '1': " date_input <= '".$search['end_date']." 23:59:59'";
      	$where = " AND ".$from_date." AND ".$to_date;
      	 
      	if($search['branch_id']>0){
      		$where.=" AND branch_id= ".$search['branch_id'];
      	}
      	if($search['client_name']>0){
      		$where.=" AND client_id = ".$search['client_name'];
      	}
      	if($search['co_id']>0){
      		$where.=" AND co_id = ".$search['co_id'];
      	}
//       	if($search['currency_type']>-1){
//       		$where.= " AND currency_type = ".$search['currency_type'];
//         }
      	$dbp = new Application_Model_DbTable_DbGlobal();
      	$where.=$dbp->getAccessPermission('branch_id');
      	
      	$order=" ORDER BY id DESC ";
      	return $db->fetchAll($sql.$where.$order);
      } 
   function getReceiveByLoanNumber($loan_number,$search){
      	$db = $this->getAdapter();
      	$to_date = (empty($search['end_date']))? '1': " date_input < '".$search['end_date']." 23:59:59'";
      	$where = " AND ".$to_date;
		$sql=" SELECT
				crm.`payment_option`,
				`crm`.`total_principal_permonth` AS `principle_amount`,
				(crm.`total_interest`) AS interest,
				(crm.`penalize_amount`) AS penelize,
				(crm.`service_charge`) AS service,
				crm.`return_amount` AS return_amount,
				crm.`recieve_amount` AS amount_recieve,
				(`crm`.`total_payment`) AS `payment`,
				SUM(crmd.`principal_permonth`) AS total_principal_permonth,
				SUM(crmd.`total_payment`) AS total_payment,
				SUM(crmd.`total_interest`) AS total_interest,
				SUM(crmd.`total_recieve`) AS recieve_amount,
				SUM(crmd.`penelize_amount`) AS penelize_amount,
				SUM(crmd.`service_charge`) AS service_charge
				FROM
				`ln_client_receipt_money` AS crm,
				`ln_client_receipt_money_detail` AS crmd
				WHERE 
				crmd.`crm_id`=crm.`id` 
				AND crmd.`loan_number`='".$loan_number."' ";
		$group="  GROUP BY crmd.`crm_id` ";
		$principle_amount=0;
		$row =  $db->fetchAll($sql.$where.$group);
		if(!empty($row)){
			$interest=0;
			$alltotal=0;
			$alltotal_interest = 0;
			$total_penelize=0;
			foreach ($row as $rs){
				if($rs["payment_option"]==4){
					$principle= $rs["principle_amount"];
					$total_pay = $rs["payment"];
					$interest= $rs["interest"];
					$recieve = $rs["amount_recieve"]-$rs["return_amount"];
					$penelize = $rs["penelize"];
					$service_charge =$rs["service"];
					$is_set=1;
					// 					}
				}else{
					$is_set=0;
					$service_charge = $rs["service_charge"];
					$interest = $rs["total_interest"];
					$principle = $rs["total_principal_permonth"];
					$penelize = $rs["penelize"];
					$recieve = $rs["recieve_amount"]-$rs["return_amount"];
					$total_pay = $rs["total_payment"];
				}
				$new_service = $recieve-$service_charge;
				if($new_service>=0){
					$service = $service_charge;
					$new_penelize = $new_service - $penelize;
					if($new_penelize>=0){
						$penelize_amount =  $penelize;
						$new_interest = $new_penelize - $interest;
						if($new_interest>=0){
							$interest_amount = $interest;
							//echo $interest_amount;exit();
							$new_printciple = $new_interest - $principle;
							if($new_printciple>=0){
								$principle_amount = $principle;
								// 									exit();
							}else{
								$principle_amount = abs($new_interest);
								// 									echo  $principle_amount;
							}
						}else{
							$interest_amount = abs($new_penelize);
							$principle_amount=0;
	
						}
					}else{
						$penelize_amount = abs($new_service);
						$interest_amount =0;
						$principle_amount=0;
	
					}
				}else{
					$service = abs($recieve);
					$penelize_amount = 0;
					$interest_amount =0;
					$principle_amount=0;
				}
				$alltotal = $alltotal+$principle_amount;
				$alltotal_interest = $alltotal_interest+$interest_amount;
				$total_penelize = $total_penelize+$penelize_amount;
			}
		}else{
			$alltotal=0;
			$alltotal_interest=0;
			$total_penelize=0;
		}
		 $data['total_collectprinciple']=$alltotal;
		 $data['total_interest']=$alltotal_interest;
		 $data['total_penelize']=$total_penelize;
		 return $data;
      }
      function getReceiveByLoanNumberPeriod($loan_number,$search){
      	$db = $this->getAdapter();
      	$from_date =(empty($search['start_date']))? '1': " date_input >= '".$search['start_date']." 00:00:00'";
      	$to_date = (empty($search['end_date']))? '1': " date_input < '".$search['end_date']." 23:59:59'";
      	$where = " AND ".$from_date." AND ".$to_date;
      	$sql=" SELECT
      	crm.`payment_option`,
      	`crm`.`total_principal_permonth` AS `principle_amount`,
      	(crm.`total_interest`) AS interest,
      	(crm.`penalize_amount`) AS penelize,
      	(crm.`service_charge`) AS service,
      	crm.`return_amount` AS return_amount,
      	crm.`recieve_amount` AS amount_recieve,
      	(`crm`.`total_payment`) AS `payment`,
      	SUM(crmd.`principal_permonth`) AS total_principal_permonth,
      	SUM(crmd.`total_payment`) AS total_payment,
      	SUM(crmd.`total_interest`) AS total_interest,
      	SUM(crmd.`total_recieve`) AS recieve_amount,
      	SUM(crmd.`penelize_amount`) AS penelize_amount,
      	SUM(crmd.`service_charge`) AS service_charge
      	FROM
      	`ln_client_receipt_money` AS crm,
      	`ln_client_receipt_money_detail` AS crmd
      	WHERE
      	crmd.`crm_id`=crm.`id`
      	AND crmd.`loan_number`='".$loan_number."' ";
      	$group="  GROUP BY crmd.`crm_id`";
      	$principle_amount=0;
      	$row =  $db->fetchAll($sql.$where.$group);
      	if(!empty($row)){
      		$interest=0;
      		$alltotal=0;
      		$alltotal_interest = 0;
      		$total_penelize=0;
      		foreach ($row as $rs){
      			if($rs["payment_option"]==4){
      				$principle= $rs["principle_amount"];
      				$total_pay = $rs["payment"];
      				$interest= $rs["interest"];
      				$recieve = $rs["amount_recieve"]-$rs["return_amount"];
      				$penelize = $rs["penelize"];
      				$service_charge =$rs["service"];
      				$is_set=1;
      				// 					}
      			}else{
      				$is_set=0;
      				$service_charge = $rs["service_charge"];
      				$interest = $rs["total_interest"];
      				$principle = $rs["total_principal_permonth"];
      				$penelize = $rs["penelize"];
      				$recieve = $rs["recieve_amount"]-$rs["return_amount"];
      				$total_pay = $rs["total_payment"];
      			}
      			$new_service = $recieve-$service_charge;
      			if($new_service>=0){
      				$service = $service_charge;
      				$new_penelize = $new_service - $penelize;
      				if($new_penelize>=0){
      					$penelize_amount =  $penelize;
      					$new_interest = $new_penelize - $interest;
      					if($new_interest>=0){
      						$interest_amount = $interest;
      						$new_printciple = $new_interest - $principle;
      						if($new_printciple>=0){
      							$principle_amount = $principle;
      						}else{
      							$principle_amount = abs($new_interest);
      						}
      					}else{
      						$interest_amount = abs($new_penelize);
      						$principle_amount=0;
      					}
      				}else{
      					$penelize_amount = abs($new_service);
      					$interest_amount =0;
      					$principle_amount=0;
      
      				}
      			}else{
      				$service = abs($recieve);
      				$penelize_amount = 0;
      				$interest_amount =0;
      				$principle_amount=0;
      			}
      			$alltotal = $alltotal+$principle_amount;
      			$alltotal_interest = $alltotal_interest+$interest_amount;
      			$total_penelize = $total_penelize+$penelize_amount;
      		}
      	}else{
      		$alltotal=0;
      		$alltotal_interest=0;
      		$total_penelize=0;
      	}
      	$data['total_collectprinciple']=$alltotal;
      	$data['total_interest']=$alltotal_interest;
      	$data['total_penelize']=$total_penelize;
      	return $data;
      }
      function getAllSavingRelease($search){
//       	$from_date =(empty($search['start_date']))? '1': " saving_date >= '".$search['start_date']." 00:00:00'";
//       	$to_date = (empty($search['end_date']))? '1': " saving_date <= '".$search['end_date']." 23:59:59'";
//       	$where = " AND ".$from_date." AND ".$to_date;
      	$where="";
      	 
      	$db = $this->getAdapter();
      	$sql="
      	SELECT id,saving_number,
      	(SELECT branch_namekh FROM `ln_branch` WHERE br_id =ln_savingaccount.branch_id LIMIT 1) AS branch,
      	saving_number,
      	name_kh AS client_name_kh,
      	name_en AS client_name_en,
      	reciept_no,withdrawing,currency_type AS curr_type,(SELECT symbol FROM `ln_currency` WHERE id = currency_type LIMIT 1) currency_type,
      	deposit_amount,interest_rate,
      	(SELECT name_en FROM `ln_view` WHERE type=28 AND key_code=saving_method LIMIT 1) saving_method  ,
      	(SELECT name_en FROM `ln_view` WHERE type=1 AND key_code=term_type LIMIT 1) term_type, withdrawing,level,
      	saving_date,saving_close FROM
        `ln_savingaccount`,ln_clientsaving where ln_clientsaving.client_id = ln_savingaccount.client_id ";
      	if(!empty($search['adv_search'])){
      		$s_where = array();
      		$s_search = trim(addslashes($search['adv_search']));
      		$s_where[] = " saving_number LIKE '%{$s_search}%'";
      		$s_where[] = " deposit_amount LIKE '%{$s_search}%'";
      		$s_where[] = " interest_rate LIKE '%{$s_search}%'";
      		$s_where[] = " reciept_no LIKE '%{$s_search}%'";
      		$where .=' AND ('.implode(' OR ',$s_where).')';
      	}
      	
      	if(($search['account_type'])>0){
      		$where.= " AND saving_method = ".$search['account_type'];
      	}
      	if(($search['branch_id'])>0){
      		$where.= " AND ln_savingaccount.branch_id = ".$search['branch_id'];
      	}
      	if(($search['currency_type'])>0){
      		$where.= " AND ln_savingaccount.currency_type=".$search['currency_type'];
      	}
      	
      	
      	$order = " ORDER BY id DESC";
      	$db = $this->getAdapter();
      	//      	echo $sql.$where.$order;
      	return $db->fetchAll($sql.$where.$order);
      }
      public function getAllProfitco($search=null){
      	$db = $this->getAdapter();
      	$sql="SELECT * FROM v_profitcocollect WHERE status=1 ";
      	$from_date =(empty($search['start_date']))? '1': " date_input >= '".$search['start_date']." 00:00:00'";
      	$to_date = (empty($search['end_date']))? '1': " date_input <= '".$search['end_date']." 23:59:59'";
      	$where = " AND ".$from_date." AND ".$to_date;
      	 
      	if($search['branch_id']>0){
      		$where.=" AND branch_id= ".$search['branch_id'];
      	}
      	if($search['client_name']>0){
      		$where.=" AND client_id = ".$search['client_name'];
      	}
      	if($search['co_id']>0){
      		$where.=" AND co_id = ".$search['co_id'];
      	}
      	if(!empty($search['adv_search'])){
      		$s_where = array();
      		$s_search = addslashes(trim($search['adv_search']));
      		$s_where[] = " branch_name LIKE '%{$s_search}%'";
      		$s_where[] = " loan_number LIKE '%{$s_search}%'";
      		$s_where[] = " client_number LIKE '%{$s_search}%'";
      		$s_where[] = " client_name LIKE '%{$s_search}%'";
      		$s_where[] = " co_name LIKE '%{$s_search}%'";
      		$s_where[] = " total_principal_permonth LIKE '%{$s_search}%'";
      		$s_where[] = " total_interest LIKE '%{$s_search}%'";
      		//$s_where[] = " amount_payment LIKE '%{$s_search}%'";
      		$s_where[] = " penalize_amount LIKE '%{$s_search}%'";
      		$s_where[] = " service_charge LIKE '%{$s_search}%'";
      		$s_where[] = " receipt_no LIKE '%{$s_search}%'";
      		$s_where[] = " receipt_no LIKE '%{$s_search}%'";
      		$where .=' AND ('.implode(' OR ',$s_where).')';
      	}
      	$order=" ORDER BY id DESC ";
      		return $db->fetchAll($sql.$where.$order);
      
      }
      
      public function getPawnShopByID($id){
      	$db = $this->getAdapter();
      	$sql = "SELECT
      	`l`.`id`             AS `id`,
      	`l`.`loan_number`    AS `loan_number`,
      	`l`.`branch_id`      AS `branch_id`,
      	`l`.`customer_id`    AS `client_id`,
      	`l`.`release_amount`    AS `total_capital`,
      	`l`.`interest_rate`  AS `interest_rate`,
      	 l.interest_type,
      	`l`.`level`          AS `loan_level`,
      	`l`.`currency_type`  AS `curr_type`,
      	`l`.`is_completed`   AS `is_completed`,
      	`l`.`total_duration` AS `total_duration`,
      	`l`.`date_release`   AS `date_release`,
      	`l`.`first_payment`   AS `first_payment`,
      	`l`.`date_line`      AS `date_line`,
      	`l`.`status`         AS `status`,
      	`c`.`client_number` AS  `client_number`,
      	`c`.`name_en`  AS `client_name`,
      	`c`.`phone` AS `phone`,
      	`c`.`name_kh` AS `client_khname`,
      	(SELECT
      	`ln_branch`.`branch_namekh`
      	FROM `ln_branch`
      	WHERE (`ln_branch`.`br_id` = `l`.`branch_id`)
      	LIMIT 1) AS `branch_name`,
      	(SELECT
      	`ln_branch`.`branch_nameen`
      	FROM `ln_branch`
      	WHERE (`ln_branch`.`br_id` = `l`.`branch_id`)
      	LIMIT 1) AS `branch_nameen`,
      	(SELECT
      	`ln_branch`.`br_address`
      	FROM `ln_branch`
      	WHERE (`ln_branch`.`br_id` = `l`.`branch_id`)
      	LIMIT 1) AS `br_address`,
      	(SELECT
      	`ln_branch`.`branch_tel`
      	FROM `ln_branch`
      	WHERE (`ln_branch`.`br_id` = `l`.`branch_id`)
      	LIMIT 1) AS `branch_tel`,
      	(SELECT
      	`ln_currency`.`symbol`
      	FROM `ln_currency`
      	WHERE (`ln_currency`.`id` = `l`.`currency_type`)
      	LIMIT 1) AS `currency_type`,
      	(SELECT
      	`ln_view`.`name_en`
      	FROM `ln_view`
      	WHERE ((`ln_view`.`type` = 14)
      	AND (`ln_view`.`key_code` = `l`.`term_type`))
      	LIMIT 1) AS `termborrow`,
      	(SELECT product_kh FROM `ln_pawnshopproduct` WHERE id=l.product_id) as product_name,
      	product_description,
      	est_amount,
      (SELECT CONCAT(COALESCE(last_name,''),' ',COALESCE(first_name,'')) FROM `rms_users` WHERE rms_users.id=l.`user_id` LIMIT 1) AS user_name
      	FROM
      	`ln_pawnshop` AS  `l`,
      	ln_clientsaving As c
      	WHERE (`l`.`status` = 1)
      	AND `c`.`client_id` = `l`.`customer_id` AND `l`.`id` = $id";
      	$where ='';
      	$dbp = new Application_Model_DbTable_DbGlobal();
      	$where.= $dbp->getAccessPermission("l.branch_id");
      	$where.=" ORDER BY `l`.`branch_id`,`l`.`currency_type` LIMIT 1";
      	return $db->fetchRow($sql.$where);
      }
      
      public function getPawnShopPaymentBYId($id){
      	$db = $this->getAdapter();
      	$sql="SELECT
      	(SELECT
      	`ln_branch`.`branch_namekh`
      	FROM `ln_branch` WHERE (`ln_branch`.`br_id` = `crm`.`branch_id`) LIMIT 1) AS `branch_name`,
      		(SELECT `ln_branch`.`br_address` FROM `ln_branch` WHERE (`ln_branch`.`br_id` = `crm`.`branch_id`) LIMIT 1) AS `br_address`,
   	(SELECT `ln_branch`.`branch_tel` FROM `ln_branch` WHERE (`ln_branch`.`br_id` = `crm`.`branch_id`) LIMIT 1) AS `branch_tel`,
   	(SELECT `ln_branch`.`branch_nameen` FROM `ln_branch` WHERE (`ln_branch`.`br_id` = `crm`.`branch_id`) LIMIT 1) AS `branch_nameen`,
   	
      	(SELECT `ln_currency`.`symbol`
      	FROM `ln_currency`
      	WHERE (`ln_currency`.`id` = `crm`.`currency_type`) LIMIT 1) AS `currency_typeshow`,
      	(SELECT `l`.`loan_number` FROM `ln_pawnshop` `l` WHERE (`l`.`id` = `crm`.`loan_id`) LIMIT 1) AS `loan_number`,
      	(SELECT `c`.`name_kh` FROM `ln_clientsaving` `c` WHERE (`c`.`client_id` = `crm`.`client_id`) LIMIT 1) AS `client_name`,
      	(SELECT  `c`.`client_number` FROM `ln_clientsaving` `c` WHERE (`c`.`client_id` = `crm`.`client_id`) LIMIT 1) AS `client_number`,
      	(SELECT CONCAT(COALESCE(`u`.`last_name`,''),' ',COALESCE(`u`.`first_name`,'')) FROM `rms_users` AS u WHERE u.id=crm.`user_id` LIMIT 1) AS user_name,
      	
      	`crm`.`id`                   AS `id`,
      	`crm`.`receipt_no`           AS `receipt_no`,
      	`crm`.`branch_id`            AS `branch_id`,
      	`crm`.`date_pay`             AS `date_pay`,
      	`crm`.`date_payment`         AS `date_payment`,
      	`crm`.`date_input`           AS `date_input`,
      	`crm`.`note`                 AS `note`,
      	`crm`.`user_id`              AS `user_id`,
      	`crm`.`status`               AS `status`,
      	`crm`.`payment_option`       AS `payment_option`,
      	`crm`.`currency_type`        AS `currency_type`,
      	`crm`.`is_payoff`            AS `is_payoff`,
      	`crm`.`total_payment`        AS `total_payment`,
      	`crm`.`principal_amount`     AS `principal_amount`,
      	`crm`.`interest_amount`      AS `interest_amount`,
      	`crm`.`principal_paid`       AS `principal_paid`,
      	`crm`.`interest_paid`        AS `interest_paid`,
      	`crm`.`service_paid`         AS `service_paid`,
      	`crm`.`penalize_paid`        AS `penalize_paid`,
      	`crm`.`total_paymentpaid`    AS `total_paymentpaid`,
      	`crm`.`recieve_amount`       AS `amount_recieve`,
      	`crm`.`return_amount`        AS `return_amount`,
      	`crm`.`penalize_amount`      AS `penelize`,
      	`crm`.`service_chargeamount` AS `service_charge`,
      	`crm`.`client_id`            AS `client_id`,
      	`crm`.`paid_times`           AS `paid_times`,
      	ps.`product_id`         AS `product_id`,
      	(SELECT p.product_en FROM `ln_pawnshopproduct` AS p WHERE p.id = ps.`product_id` LIMIT 1) AS proTitle,
      	(SELECT p.product_kh FROM `ln_pawnshopproduct` AS p WHERE p.id = ps.`product_id` LIMIT 1) AS proTitleKh,
      	ps.`product_description`        AS `product_description`
      	FROM `ln_pawn_receipt_money` `crm`,
      	`ln_pawn_receipt_money_detail` `d`,
		`ln_pawnshop` `ps`
      	WHERE (`crm`.`status` = 1)
      	AND (`crm`.`id` = `d`.`receipt_id`)
      	AND (`crm`.`loan_id` = ps.id)
      	AND (`crm`.`status` = 1)
      	AND crm.id = $id
      	GROUP BY `crm`.`id` ";
      
      	$sql.=" ORDER BY `crm`.`id` DESC LIMIT 1";
      	return $db->fetchRow($sql);
      }
      
      function getAdminFeeByReschedule($search){
      	$db = $this->getAdapter();
      	$sql="SELECT SUM(ps.`admin_fee`) AS total_adminfee,ps.`currency_type` FROM `ln_pawnshop_reschedule` AS ps 
				WHERE 1
				";
      	$where ="";
      	if($search['branch_id']>0){
      		$where.=" AND ps.`branch_id` = ".$search['branch_id'];
      	}
      	if(!empty($search['start_date']) or !empty($search['end_date'])){
      		$from_date =(empty($search['start_date']))? '1': " ps.`create_date` >= '".$search['start_date']." 00:00:00'";
      		$to_date = (empty($search['end_date']))? '1': " ps.`create_date` <= '".$search['end_date']." 23:59:59'";
      		$where.= " AND ".$from_date." AND ".$to_date;
      	}
      	
      	$where.=" GROUP BY ps.`currency_type`";
      	return $db->fetchAll($sql.$where);
      }
      
      
      function submitClosingEngry($data){
      	$db = $this->getAdapter();
      	if(!empty($data['id_selected'])){
      		$ids = explode(',', $data['id_selected']);
      		$key = 1;
      		$arr = array(
      				"is_closed"=>1,
      		);
      		foreach ($ids as $i){
      			$this->_name="ln_pawn_receipt_money";
      			$where="id= ".$i;
      			$this->update($arr, $where);
      		}
      	}
      }
	  
	  public function getPaymentListPawnByID($pawnID){
      	$db = $this->getAdapter();
			$sql="SELECT
				  (SELECT
				     `ln_branch`.`branch_namekh`
				   FROM `ln_branch` WHERE (`ln_branch`.`br_id` = `crm`.`branch_id`)
				   LIMIT 1) AS `branch_name`,
				  (SELECT `ln_currency`.`symbol`
				   FROM `ln_currency`
				   WHERE (`ln_currency`.`id` = `crm`.`currency_type`) LIMIT 1) AS `currency_typeshow`,
				  (SELECT `l`.`loan_number` FROM `ln_pawnshop` `l` WHERE (`l`.`id` = `crm`.`loan_id`) LIMIT 1) AS `loan_number`,
				  (SELECT `l`.`product_id` FROM `ln_pawnshop` `l` WHERE (`l`.`id` = `crm`.`loan_id`) LIMIT 1) AS `product_id`, 
				  (SELECT psp.product_kh FROM `ln_pawnshopproduct` AS psp WHERE psp.id = (SELECT `l`.`product_id` FROM `ln_pawnshop` `l` WHERE (`l`.`id` = `crm`.`loan_id`) LIMIT 1) LIMIT 1) AS proTitle,
				  (SELECT `c`.`name_kh` FROM `ln_clientsaving` `c` WHERE (`c`.`client_id` = `crm`.`client_id`) LIMIT 1) AS `client_name`,
				  (SELECT  `c`.`client_number` FROM `ln_clientsaving` `c` WHERE (`c`.`client_id` = `crm`.`client_id`) LIMIT 1) AS `client_number`,
				  (SELECT `u`.`first_name` FROM `rms_users` `u` WHERE (`u`.`id` = `crm`.`user_id`)) AS `user_name`,
				  crm.*
				  
				FROM (`ln_pawn_receipt_money` `crm`
				   JOIN `ln_pawn_receipt_money_detail` `d`)
				WHERE ((`crm`.`status` = 1)
				       AND (`crm`.`id` = `d`.`receipt_id`)
				       AND (`crm`.`status` = 1))
				";
      	$where = " AND crm.loan_id = $pawnID ";
      	$dbp = new Application_Model_DbTable_DbGlobal();
      	$where.=$dbp->getAccessPermission('`crm`.branch_id');
      	$order=" GROUP BY `crm`.`id` ORDER BY `crm`.`id` DESC ";
      	return $db->fetchAll($sql.$where.$order);
      }
	  
	  public function getTotalAdminFeeByPawn($pawnID){
      	$db = $this->getAdapter();
      	$sql = "SELECT  SUM(admin_fee) AS admin_fee,
						currency_type
      					 FROM 
      				ln_pawnshop WHERE status = 1 AND (admin_fee>0)";
		$where = " AND id = $pawnID ";
      	return $db->fetchRow($sql.$where);
      }
	  
	  function getTotalAdminFeeByReschedule($pawnID){
      	$db = $this->getAdapter();
      	$sql="SELECT SUM(ps.`admin_fee`) AS total_adminfee,ps.`currency_type` FROM `ln_pawnshop_reschedule` AS ps 
				WHERE 1
				";
      	$where = " AND ps.pawnshop_id = $pawnID ";
      	$where.=" GROUP BY ps.`currency_type`";
      	return $db->fetchAll($sql.$where);
      }
	  
	  
	  function checkPawnPenalty($data){
		
		$pawnId= empty($data["pawnId"]) ? 0 : $data["pawnId"];
		$endDate= empty($data["endDate"]) ? 0 : $data["endDate"];
		$currType= empty($data["curr_type"]) ? 0 : $data["curr_type"];
		
		$key = new Application_Model_DbTable_DbKeycode();
		$keyvalue=$key->getKeyCodeMiniInv();
		$graicePariod = empty($keyvalue['graicePariod']) ? 0 : $keyvalue['graicePariod'];
		$penaltyCalculateDay = empty($keyvalue['penaltyCalculateDay']) ? 1 : $keyvalue['penaltyCalculateDay'];
	
		$penaltyType = empty($keyvalue['penaltyType']) ? 1 : $keyvalue['penaltyType'];
		$penaltyValue = empty($keyvalue['penaltyValue']) ? 0 : $keyvalue['penaltyValue'];
		$penaltyValueDollar = empty($keyvalue['penaltyValueDollar']) ? 0 : $keyvalue['penaltyValueDollar'];
		$penaltyValueBath = empty($keyvalue['penaltyValueBath']) ? 0 : $keyvalue['penaltyValueBath'];
	
	
		$ps=0;
		if($currType==1){
			$ps=$penaltyValue;
		}else if($currType==2){
			$ps=$penaltyValueDollar;
		}else{
			$ps=$penaltyValueBath;
		}
										
		
		$db = $this->getAdapter();
		
		$sql="SELECT 
				DATEDIFF('$endDate',sh.date_payment) AS total_latedate
			FROM `ln_pawnshop_detail` AS sh 
			WHERE sh.date_payment <= '$endDate 23:59:59' 
			AND sh.pawn_id = $pawnId 
			AND sh.is_completed=0
			GROUP BY sh.pawn_id";
		$total_latedate = $db->fetchOne($sql);
		$latedate = $total_latedate - $graicePariod;
		if($latedate<=0){
			return 0;
		}
		
		$deductAmtDay = 0;
		if($total_latedate > $graicePariod){
			if($penaltyCalculateDay==2){
				$deductAmtDay=$graicePariod;
			}
		}
		
		if($penaltyType==1){
		$sql="SELECT 
				SUM(((($ps/100))*sh.total_payment_after*(DATEDIFF('$endDate',sh.date_payment)-$deductAmtDay))) AS penaltyAmount
				,SUM(sh.`principle_after`) AS principleAmount
				,CASE 
					WHEN SUM(sh.`principle_after`) > 0 AND sh.id = (SELECT shh.id FROM `ln_pawnshop_detail` AS shh  WHERE  shh.pawn_id = sh.`pawn_id` ORDER BY shh.id DESC LIMIT 1 )
						THEN l.admin_fee 
					ELSE '0'
				END AS adminFee
			 FROM `ln_pawnshop_detail` AS sh 
				JOIN `ln_pawnshop` AS l ON l.id = sh.`pawn_id`
			WHERE sh.date_payment <= '$endDate 23:59:59' 
			AND sh.pawn_id = $pawnId 
			AND sh.is_completed=0
			GROUP BY sh.pawn_id";
		}else{
			$sql="SELECT
				SUM(($ps* (DATEDIFF('$endDate',sh.date_payment)-$deductAmtDay) )) AS penaltyAmount
				,SUM(sh.`principle_after`) AS principleAmount
				,CASE 
					WHEN SUM(sh.`principle_after`) > 0 AND sh.id = (SELECT shh.id FROM `ln_pawnshop_detail` AS shh  WHERE  shh.pawn_id = sh.`pawn_id` ORDER BY shh.id DESC LIMIT 1 )
						THEN l.admin_fee 
					ELSE '0'
				END AS adminFee
			FROM `ln_pawnshop_detail` AS sh 
				JOIN `ln_pawnshop` AS l ON l.id = sh.`pawn_id`
			WHERE sh.date_payment <= '$endDate 23:59:59'
			AND sh.pawn_id = $pawnId
			AND sh.is_completed=0
			GROUP BY sh.pawn_id
			";
		}
		/*
		 * sh.date_payment,
			DATEDIFF('$end_date',sh.date_payment) AS defday
		 * */
		
		$penalty = $db->fetchRow($sql);
		return $penalty;
	}
 }

