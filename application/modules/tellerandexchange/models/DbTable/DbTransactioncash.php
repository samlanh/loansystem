<?php

class Tellerandexchange_Model_DbTable_DbTransactioncash extends Zend_Db_Table_Abstract
{

    protected $_name = 'cs_money_transactions';
    

	function getTransactionList($start, $limit){		
		$db = $this->getAdapter();
		
		$sql = $this->_buildQuery()." LIMIT ".$start.", ".$limit;
		if ($limit == 'All') {
			$sql = $this->_buildQuery();
		}		
		return $db->fetchAll($sql);
	}
		
	function getTransactionListBy($search, $start, $limit){
		$db = $this->getAdapter();
				
		$orderby = " ORDER BY mt.`status`, mt.`id` DESC";
		
		$sql = $this->_buildQuery($search).$orderby." LIMIT ".$start.", ".$limit;
		if ($limit == 'All') {
			$sql = $this->_buildQuery($search).$orderby;
		}
		return $db->fetchAll($sql);
	}
	
	function getTransactionReport($search){
		$db = $this->getAdapter();
		$orderby = " ORDER BY mt.`status`, mt.`send_date` DESC";
		$sql = $this->_buildQuery($search);	
		return $db->fetchAll($sql.$orderby);
	}
	
	function getTransactionReportIncome($search){
		$db = $this->getAdapter();
		$orderby = " ORDER BY mt.`amount_type`, mt.`send_date`";
		$sql = $this->_buildQuery($search);
		if ($search['status'] < 0){
			$sql .= ' AND mt.`status` != 4 AND mt.`status` != 5';
		}
		// 		echo $sql.$orderby;
		$datas = $db->fetchAll($sql.$orderby);
	
// 		$balance = $this->getBalanceForAgents($search, $orderby,true);
// 		$rd = $this->mergeTransaction($datas, $balance);
		// 			print_r($rd); exit;
		return $datas;
	}
	
	function getTransactionReportTotalOnly($search){
		$db = $this->getAdapter();
		$orderby = " ORDER BY mt.`agent_id` , mt.`tran_type`, mt.`amount_type`, mt.`send_date`";
		$sql = $this->_buildQuery($search);
		if ($search['status'] < 0){
			$sql .= ' AND mt.`status` != 4 AND mt.`status` != 5';
		}
	
		$datas = $db->fetchAll($sql.$orderby);
		return $datas;
	}
	
	function getTransactionReportTotal($search){
		$db = $this->getAdapter();
		$orderby = " ORDER BY mt.`agent_id` , mt.`tran_type`, mt.`amount_type`, mt.`send_date`";		
		$sql = $this->_buildQuery($search);
		if ($search['status'] < 0){
			$sql .= ' AND mt.`status` != 4 AND mt.`status` != 5';
		}
		
		$datas = $db->fetchAll($sql.$orderby);
		$balance = $this->getBalanceForAgents($search);
		$rd = $this->mergeTransaction($datas, $balance);
// 		print_r($rd); exit;
		return $rd;
	}
	
	function mergeTransaction($tran, $balance){
		$rd = array();
		if(!empty($tran)){
			$rd = array_merge($tran, $balance);
// 			print_r($rd); 
// 			echo "<br><br>"; 
			$rd = $this->sortTransaction($rd);
// 			print_r($rd); exit();
		}
		else{			
			$rd = $this->sortTransaction($balance);
		}
		return $rd;
	}
	
	function sortTransaction($tran){
// 		$result = null;
		if (!$length = count($tran)) {
			return $tran;
		}		
		for($i=0; $i < $length; $i++){
			for($j=$i+1; $j < $length; $j++){
				if($tran[$i]['agent_id'] == $tran[$j]['agent_id']  &&  $tran[$i]['tran_type'] == $tran[$j]['tran_type']
						&& $tran[$i]['cus_id'] == $tran[$j]['cus_id'] && $tran[$i]['send_date'] > $tran[$j]['send_date']){
					$tmp = $tran[$i];
					$tran[$i] = $tran[$j];
					$tran[$j] = $tmp;
				}
				elseif($tran[$i]['agent_id'] == $tran[$j]['agent_id']  &&  $tran[$i]['tran_type'] == $tran[$j]['tran_type'] 
						&& $tran[$i]['cus_id'] > $tran[$j]['cus_id']){
						$tmp = $tran[$i];
						$tran[$i] = $tran[$j];
						$tran[$j] = $tmp;
				}
				elseif($tran[$i]['agent_id'] == $tran[$j]['agent_id']  &&  $tran[$i]['tran_type'] > $tran[$j]['tran_type']){
						$tmp = $tran[$i];
						$tran[$i] = $tran[$j];
						$tran[$j] = $tmp;
				}
				elseif($tran[$i]['agent_id'] > $tran[$j]['agent_id']){
						$tmp = $tran[$i];
						$tran[$i] = $tran[$j];
						$tran[$j] = $tmp;
				}
			}
		}
		return $tran;
	}
	
	function getBalanceForAgents($search){
// 		print_r($search); exit;
		$db = $this->getAdapter();
		//$s_date = date_format(date_create($search["from_date"]), "d/m/Y");
		$s_date = $search["from_date"];
		$sql = "SELECT
				  cur.`symbol`,
				  cur.`id` AS cus_id,
				  '000000000' AS `reciever_tel`,
				  a.`name` AS agentname,
				  'លុយចាស់សល់' AS `sender_name`,
				  'លុយចាស់សល់' AS `reciever_name`,
				  mt.`tran_type`,
				  sa.`name` AS subname,
				  '$s_date' AS `send_date`,
				  0 AS `commission_agent`,
				  cur.`name` AS amount_type,
				  p.`name` AS pro_name,
				  a.`tel` AS agent_tel,
				  a.`id` AS agent_id,
				  0 AS `transfer_money` 
				FROM
				  `cs_agents` AS a 
				  INNER JOIN `cs_money_transactions` AS mt 
				    ON (a.`id` = mt.`agent_id`) 
				  INNER JOIN `cs_currencies` AS cur 
				    ON (cur.`id` = mt.`amount_type`) 
				  INNER JOIN `cs_countries` AS con 
				    ON (con.`id` = cur.`country_id`) 
				  INNER JOIN `cs_provinces` AS p 
				    ON (p.`id` = a.`province`) 
				  LEFT JOIN `cs_sub_agents` AS sa 
				    ON (mt.`subagent_id` = sa.`id`)";
		
		$orderby = " ORDER BY mt.`agent_id` , mt.`tran_type`, mt.`amount_type`";
		$groupby = " GROUP BY mt.`agent_id`, mt.`amount_type` ";
		$where = " WHERE DATE(mt.`send_date`) < DATE('".$search["from_date"]."') ";
		
		
		if ($search['agent'] >= 0){
			$where .= ' AND a.`id` = '. $search['agent'];
		}
			
		if ($search['status_loan'] >= 0){
			$where .= ' AND mt.`status_loan` = '. $search['status_loan'];
		}
		
		if ($search['status'] >= 0){
			$where .= ' AND mt.`status` = '. $search['status'];
		}
		elseif ($search['status'] < 0){
			$where .= ' AND mt.`status` != 4 AND mt.`status` != 5';
		}
		
		if ($search['type_money'] >= 0){
			$where .= ' AND mt.`amount_type` = '. $search['type_money'];
		}
		
// 		echo $sql.$where.$groupby.$orderby; exit;
		$datas = $db->fetchAll($sql.$where.$groupby.$orderby);
// 		print_r($datas); exit;
		$tmp = array(); $ind=0;
		foreach ($datas AS $i => $d){			
			$tmp_b = $this->getBalanceForAgent($d['agent_id'], $d['cus_id'],$search["from_date"]);
// 			echo $tmp_b . "<br>";
			if($tmp_b != 0){
				$tran_type = ($tmp_b < 0)? 1:0;
				//$ind = $d['agent_id'] . "_" . $tran_type . "_" . $d['cus_id'];				
				$datas[$i]["tran_type"] = $tran_type;
				$datas[$i]["transfer_money"] = abs($tmp_b);
				$tmp[$ind] = $datas[$i];
				$ind++;
			}
		}
		return $tmp;
	}
	
	function getBalanceForAgent($agent_id, $amount_type, $date){			
		$db = $this->getAdapter();
		$sql = "SELECT
				  SUM(mt.`transfer_money`) + SUM(mt.`commission_agent`) AS balance
				FROM
				  `cs_money_transactions` AS mt
				WHERE DATE(mt.`send_date`) < DATE('$date')
						AND mt.`status` != 4 AND mt.`status` != 5 
					 	AND mt.`agent_id` = ".$agent_id."
				  		AND mt.`amount_type` = ".$amount_type ."
						AND mt.`tran_type` = " ;
// 		echo $sql."<br><br>"; exit();
		$in = $db->fetchOne($sql. "0");
		$out = $db->fetchOne($sql. "1");
// 		print_r($datas); exit();
		if(empty($in)) $in=0;
		if(empty($out)) $out=0;
		return $in - $out;
	}
	
	function getTransactionReportAutoPrint(){//old
		$db = $this->getAdapter();
		$orderby = " ORDER BY mt.`agent_id`, mt.`subagent_id`, mt.`amount_type`";
	
		$where = "WHERE mt.`status` = 0";
	
		$sql = "SELECT
		mt.`reciever_tel`
		, a.`name` as agentname
		, a.`id` AS agent_id
		, mt.`sender_name`
		, mt.`reciever_name`
		, sa.`name` as subname
		, mt.`transfer_money`
		, mt.`commission_agent`
		, cur.`name` AS amount_type
		, cur.`country_id` AS money_type
		, mt.`id` AS tran_id
		, mt.`send_date`
		, mt.`subagent_id`
		FROM
		`cs_agents` AS a
		INNER JOIN `cs_money_transactions`  AS mt ON (a.`id` = mt.`agent_id`)
		INNER JOIN `cs_currencies` AS cur ON (cur.`id` = mt.`amount_type`)
		LEFT JOIN `cs_sub_agents` AS sa ON (mt.`subagent_id` = sa.`id`)";
		return $db->fetchAll($sql.$where.$orderby);
	}
	
	function getTransactionToBranch(){
		$db = $this->getAdapter();
		$orderby = " GROUP BY mt.`reciever_tel`,`subagent_id`,mt.`agent_id` ORDER BY mt.`agent_id`, mt.`subagent_id`, mt.`amount_type`";
		$where = "WHERE mt.`status` = 0";
		
		$sql = "SELECT
					 mt.`reciever_tel`
				    , a.`name` as agentname
				    , a.`id` AS agent_id
				    , mt.`sender_name`
				    , mt.`reciever_name`				    
				    , sa.`name` as subname				    
				    , mt.`transfer_money`
					, mt.`commission_agent`
				    , cur.`name` AS amount_type
				    , cur.`country_id` AS money_type
				    , mt.`id` AS tran_id
				    , mt.`subagent_id`
				FROM
				    `cs_agents` AS a
				    INNER JOIN `cs_money_transactions`  AS mt ON (a.`id` = mt.`agent_id`)				        
				   	INNER JOIN `cs_currencies` AS cur ON (cur.`id` = mt.`amount_type`)
				    LEFT JOIN `cs_sub_agents` AS sa ON (mt.`subagent_id` = sa.`id`)";	
		return $db->fetchAll($sql.$where.$orderby);
	}
	//
	function getTransByAgent($agent_id){
		$db=$this->getAdapter();
		$sql = "SELECT
					mt.`id` AS tran_id
				FROM
				    `cs_agents` AS a
				    INNER JOIN `cs_money_transactions`  AS mt ON (a.`id` = mt.`agent_id`)				        
				    WHERE mt.`status` = 0 AND mt.`agent_id`= $agent_id ";
		//echo $sql;
		return $db->fetchAll($sql);		
	}
	function getTransByower($sender_name){//FOR GET tran owe by sender name
		$db=$this->getAdapter();
		$sql = "SELECT
		mt.`id` AS tran_id
		FROM
		`cs_agents` AS a
		INNER JOIN `cs_money_transactions`  AS mt ON (a.`id` = mt.`agent_id`)
		WHERE mt.status_loan = 1 AND mt.`sender_name`= '$sender_name'";
		//echo $sql;
		return $db->fetchAll($sql);
	}
	function TransFundExist($transa_id){
		$db =$this->getAdapter();
		$sql = " SELECT 
				  money_tran_id 
				FROM
				  `cs_transactions_owe` AS w,
				  `cs_trancs_found_detail` AS f
				WHERE  f.`owe_id` = w.`id` AND w.money_tran_id = $transa_id LIMIT 1";
		return $db->fetchOne($sql);
	}
	function updateOwerbyTran($_data){
		$s_where = array();
		$where = '';
		for($i=0; $i< count($_data);$i++){
			$s_where[] = "`id` = ".$_data["get_transac".$i];
		}
		$where .= implode(' OR ',$s_where);
		$data=array( 'status_loan'=>0);
	    return  $this->update($data, $where);
	}
	
	function getTransByTel($tel,$agent,$sub_agent){
		$db=$this->getAdapter();
		$sql = "SELECT
                      mt.`transfer_money`
				    , mt.`commission_agent`
				    , cur.`country_id` AS money_type
				FROM
				    `cs_agents` AS a
				    INNER JOIN `cs_money_transactions`  AS mt ON (a.`id` = mt.`agent_id`)				        
				   	INNER JOIN `cs_currencies` AS cur ON (cur.`id` = mt.`amount_type`)
				    LEFT JOIN `cs_sub_agents` AS sa ON (mt.`subagent_id` = sa.`id`)
				    WHERE mt.`status` = 0 AND  mt.`reciever_tel` = $tel
				    AND(mt.`subagent_id`=$sub_agent AND mt.`agent_id`= $agent )";
		return $db->fetchAll($sql);		
	}
	//for owe
	function getTransactionOwe($data=null){//not yet delete some field that not use
		$db = $this->getAdapter();
		$orderby = " GROUP BY mt.`sender_name`, mt.`reciever_tel` ORDER BY  mt.`sender_name`, mt.`reciever_tel`, mt.`amount_type` ";
		$where = " WHERE mt.`status_loan` = 1 ";
		
		$from_date =(empty($data['from_date']))? '1': "mt.send_date >= '".$data['from_date']." 00:00:00'";
		$to_date = (empty($data['to_date']))? '1': "mt.send_date <= '".$data['to_date']." 23:59:59'";
		$where.= " AND ".$from_date." AND ".$to_date;
		
		if(!empty($data)){
			$sender = $data['sender'];
			if($sender!=-1)
			$where.=" AND mt.`sender_name`= '$sender'";
		}
	
		$sql = "SELECT
		mt.`reciever_tel`
		, a.`name` as agentname
		, a.`id` AS agent_id
		, mt.`subagent_id`
		, mt.`sender_name`
		, cur.`name` AS amount_type
		, cur.`country_id` AS money_type
		, mt.`id` AS tran_id
		, sa.`name` as subname	
		FROM
		`cs_agents` AS a
		INNER JOIN `cs_money_transactions`  AS mt ON (a.`id` = mt.`agent_id`)
		INNER JOIN `cs_currencies` AS cur ON (cur.`id` = mt.`amount_type`)
		LEFT JOIN `cs_sub_agents` AS sa ON (mt.`subagent_id` = sa.`id`)";
		
		//echo $sql.$where.$orderby;
		return $db->fetchAll($sql.$where.$orderby);
// 		$sql = "CAll getAllOwer()";
// 		echo $sql;exit();
// 		return $db->fetchAll($sql);
	}
	function getTransBySender($tel,$sender){//get money loan from sender name
		$db=$this->getAdapter();
		$sql = "SELECT
		mt.`transfer_money`
		, mt.`commission`
		, cur.`country_id` AS money_type
		FROM
		`cs_agents` AS a
		INNER JOIN `cs_money_transactions`  AS mt ON (a.`id` = mt.`agent_id`)
		INNER JOIN `cs_currencies` AS cur ON (cur.`id` = mt.`amount_type`)
		LEFT JOIN `cs_sub_agents` AS sa ON (mt.`subagent_id` = sa.`id`)
		WHERE mt.`status_loan` = 1 AND mt.`reciever_tel` = $tel AND  mt.`sender_name` = '$sender'";
		return $db->fetchAll($sql);
	}
	
	function getTransactionListTotal($search=''){
		$from_date =(empty($search['from_date']))? '1': "mt.send_date >= '".$search['from_date']." 00:00:00'";
		$to_date = (empty($search['to_date']))? '1': "mt.send_date <= '".$search['to_date']." 23:59:59'";
		$where = "WHERE ".$from_date." AND ".$to_date;
		
		
// 		if($session_user->level == 0){        
//         	$where .=" AND mt.`user_id` = ".$session_user->user_id;
// 		}		
// 		$sql = "SELECT
// 					mt.`id`
// 				    , mt.`sender_name`
// 				    , mt.`reciever_name`
// 				    , mt.`invoice_no`
// 				    , mt.`transfer_money`
// 				    , sa.`name` as subname
// 				    , mt.`send_date`
// 				    , mt.`recieved_date`
// 				    , mt.`expire_date`
// 				    , cur.`symbol`
// 				    , cur.`id` AS cus_id
// 				    , mt.`reciever_tel`
// 				 , mt.`amount`
// 				    , mt.`status`
// 				    , mt.`status_loan`
// 				    , a.`name` as agentname
// 				    , mt.`tran_type`
// 				    , mt.`commission`
// 				    , mt.`commission_agent`
// 				    , cur.`name` AS amount_type
// 				    , CONCAT(u.`last_name`,' ', u.`first_name`) AS user_name
// 				    , p.`name` AS pro_name
// 				    , a.`tel` AS agent_tel
// 					, mt.`agent_id`
// 				FROM
// 				    `cs_agents` AS a
// 				    INNER JOIN `cs_money_transactions`  AS mt ON (a.`id` = mt.`agent_id`)
// 				    INNER JOIN `cs_users`  AS u ON (u.`id` = mt.`user_id`)
// 				   	INNER JOIN `cs_currencies` AS cur ON (cur.`id` = mt.`amount_type`)
// 				  	INNER JOIN `cs_countries`  AS con ON (con.`id` = cur.`country_id`)
// 				    INNER JOIN `cs_provinces` AS p ON (p.`id` = a.`province`)
// 				    LEFT JOIN `cs_sub_agents` AS sa ON (mt.`subagent_id` = sa.`id`) ";
		
		$sql = "SELECT
					mt.`id`
				    , mt.`sender_name`
				    , mt.`reciever_name`
				    , mt.`reciever_tel`
				    ,mt.`invoice_no`
				    , CONCAT(mt.`transfer_money`,cur.`symbol`)
				    , commission
				    , sa.`name` as subname	
				    , mt.`send_date`	    
				    , mt.`expire_date`
				    , mt.`status`
				    , mt.`status_loan`
				
				FROM
				    `cs_agents` AS a
				    INNER JOIN `cs_money_transactions`  AS mt ON (a.`id` = mt.`agent_id`)
				    INNER JOIN `cs_users`  AS u ON (u.`id` = mt.`user_id`)    
				   	INNER JOIN `cs_currencies` AS cur ON (cur.`id` = mt.`amount_type`)
				  	INNER JOIN `cs_countries`  AS con ON (con.`id` = cur.`country_id`) 
				    INNER JOIN `cs_provinces` AS p ON (p.`id` = a.`province`)
				    LEFT JOIN `cs_sub_agents` AS sa ON (mt.`subagent_id` = sa.`id`) ";
		
		
		
		if(empty($search)){
			return $sql.$where;
		}
		
		if (!empty($search['txt_search'])){
			$s_where = array();
			if(is_numeric($search['txt_search'])){	
				$s_where[] = "mt.amount = {$search['txt_search']}";
			}
			$s_where[] = "mt.reciever_tel LIKE '{$search['txt_search']}%'";
			$s_where[] = "mt.sender_name LIKE '%{$search['txt_search']}%'";
	        $where .= ' AND ('.implode(' OR ',$s_where).')';
		}
		
// 		if($session_user->level){
	
// 			if ($search['agent'] >= 0){
// 				$where .= ' AND a.`id` = '. $search['agent'];
// 			}
// 			if (!empty($search['sender']) AND $search['sender'] >= 0){
// 				$where .= " AND mt.`sender_name` LIKE '%".$search['sender']."%'";
// 			}
			
// 			if ($search['status_loan'] >= 0){
// 				$where .= ' AND mt.`status_loan` = '. $search['status_loan'];
// 			}
// 		}
	
		if ($search['status'] >= 0){
			$where .= ' AND mt.`status` = '. $search['status'];
		}
		
		if (!empty($search['user_id']) && $search['user_id'] >= 0){
			$where .= ' AND mt.`user_id` = '. $search['user_id'];
		}
		
		if ($search['type_money'] >= 0){
			$where .= ' AND mt.`amount_type` = '. $search['type_money'];
		}
		$db = $this->getAdapter();
		return $db->fetchAll($sql.$where); 
	}
	
	function getTransactionDetailByIDView($id){
		$db = $this->getAdapter();
		$sql = "SELECT
					  mt.`id`
				    , mt.`amount`				    
				    , cur.`symbol`
				    , cur.`name` AS cur_type
				    , mt.`send_date`
				    , mt.`recieved_date`
				    , mt.`expire_date`
				    , mt.`status`
				    , a.`name` AS agent_name
				    , mt.`sender_name`
				    , mt.`reciever_name`
				    , p.`name` AS pro_name
				    , mt.`reciever_tel`
				    , mt.`commission`
				    , mt.`commission_percent`
					, mt.`recieved`
					, mt.`cut_service`
					, mt.`transfer_money`
				    , mt.`total_money`
				    , mt.`return_money`
				    , mt.`invoice_no`				    
				    , mt.`status_loan`
				    
				FROM
				    `cs_agents` AS a
				    INNER JOIN `cs_money_transactions`  AS mt ON (a.`id` = mt.`agent_id`)    
				   	INNER JOIN `cs_currencies` AS cur ON (cur.`id` = mt.`amount_type`)
				  	INNER JOIN `cs_countries`  AS con ON (con.`id` = cur.`country_id`) 
				    INNER JOIN `cs_provinces` AS p ON (p.`id` = a.`province`)
				WHERE mt.`id` = ". $id;
		
		return $db->fetchRow($sql);
	}
	
	function getTransactionDetailByID($id){
		$db = $this->getAdapter();
		$sql = "SELECT
					mt.`id`
				    , mt.`amount`
				    , mt.`send_date`
				    , mt.`amount_type`
				    , mt.`recieved_date`
				    , mt.`invoice_no`
				    , mt.`expire_date`
				    , mt.`status`
				    , mt.`status_loan`
				    , a.`id` AS agent_id
				    , a.`name` AS agent_name
				    , mt.`sender_name`
				    , mt.`reciever_name`
				    , a.`province`
				    , mt.`reciever_tel`
				    , mt.`commission`
				    , mt.`commission_percent`
					, mt.`recieved`
					, mt.`cut_service`
					, mt.`transfer_money`
				    , mt.`total_money`
				    , mt.`return_money`
				    , sa.`id` AS subagent_id
				    , sa.`name` AS subagent_name
				    , mt.`commission_agent`
				    , mt.`tran_type`
				    , mt.is_kbank
				    , (SELECT `rate_perday` FROM `cs_transactions_owe` WHERE money_tran_id=mt.`id` ) AS rate_perday
				    , (SELECT `exchange_rate_db` FROM `cs_transactions_owe` WHERE money_tran_id=mt.`id` ) AS BD
				    , (SELECT `exchange_rate_dr` FROM `cs_transactions_owe` WHERE money_tran_id=mt.`id` ) AS RD
				    , (SELECT `exchange_rate_rb` FROM `cs_transactions_owe` WHERE money_tran_id=mt.`id` ) AS RB
				    			    
				FROM
				    `cs_agents` AS a
				    INNER JOIN `cs_money_transactions`  AS mt ON (a.`id` = mt.`agent_id`)
				    LEFT JOIN `cs_sub_agents`  AS sa ON (sa.`id` = mt.`subagent_id`)
				WHERE mt.`id` = ". $id;
		
		return $db->fetchRow($sql);
	}
	
	function dectectMoneyTransaction($data){
		
		$db = $this->getAdapter();
		$sql = "SELECT	`id`				    				    
				FROM `cs_money_transactions`
				WHERE ";
		
		//$sub_id= (empty($data['sub_agent_id'])) ? 0 : $data['sub_agent_id']; 
		$where= "DATEDIFF(send_date, '".$data['send_date']."') = 0"
				." AND reciever_tel ='".$data['reciever_tel']."'";
				/*." AND tran_type = ".$data['tran_type']
				."' AND amount = ".$data['amount']
				." AND amount_type = ".$data['type_money']
				." AND tran_type = ".$data['tran_type']
				." AND agent_id = ".$data['agent_id']
				." AND subagent_id = ".$sub_id;*/
				
		$_res = $db->fetchRow($sql.$where);
		
		return $_res['id'];
	}

	function insertMoneyTransfer($data)
    {    
    	$percent =  (empty($data['percent']))? 0 : $data['percent'];
    	$send_date = $data['send_date'].' '.date('h:i:s');
    	$exp_date = $data['epx_date'].' '.date('h:i:s');    
    	$session_user=new Zend_Session_Namespace('auth');   	
    	   
    	 $db = $this->getAdapter();
    	 $db->beginTransaction();
    	 try {
    	 	$agent_id = $data['agent_id'];
    	 	$_data=array(
    					'sender_name'=>$data['sender'],
						'reciever_name'=>$data['reciever'],
						'reciever_tel'=>$data['reciever_tel'],
						'invoice_no'=>$data['invoice_no'],
						'amount'=>$data['amount'],
						'amount_type'=>$data['type_money'],
						'commission'=>(empty($data['commission'])) ? 0 :$data['commission'],
						'commission_type'=>$data['type_money'],
						'commission_percent'=>$percent,
    					'commission_agent'=>$data['commission_agent'],
						'recieved'=>$data['recieve_money'],
						'recieved_type'=>$data['type_money'],
						'status'=>($data['tran_type'] == 0) ? 0 : 2,
    					'status_loan'=>(empty($data['loan'])) ? 0 : $data['loan'],
						'cut_service'=>(empty($data['minus'])) ? 0 : $data['minus'],
						'agent_id'=>$agent_id,
    					'subagent_id'=>(empty($data['sub_agent_id'])) ? 0 : $data['sub_agent_id'],						
						'send_date'=>$send_date,
						'expire_date'=>$exp_date,
						'transfer_money'=>$data['gave'],
						'total_money'=>$data['total'],    					
						'return_money'=>$data['return_money'],
    					'tran_type'=>$data['tran_type'],
    	 				'is_kbank'=> (empty($data['is_kbank'])) ? 0 : $data['is_kbank'],
						'user_id'=>$session_user->user_id										
    	           ); 
    	 	$tran_no = $this->insert($_data);
    	 	
    	 	$db_sender = new Application_Model_DbTable_DbKbank();
    	 	$sender_id = $db_sender->getSenderIdbyName($data['sender']);
    	 	$db_rate=new Application_Model_DbTable_DbRate();
    	 	$rate = $db_rate->getCurrentRate();
    	 	$_data['sender_name']=$sender_id;
    	 	$_data['exchange_rate_db'] =  $rate['BD'];
	    	$_data['exchange_rate_dr'] =  $rate['RD'];
	    	$_data['exchange_rate_rb'] =  $rate['RB'];
	    	$_data['is_orgdebt'] =  1;
	    	
	    	$db_cap = new Application_Model_DbTable_DbCapital();//for data capital
    	 	if(!empty($data['loan'])){//IF DEBT
    	 		$this->_name="cs_transactions_owe";
    	 	
    	 		$_data['money_tran_id']=$tran_no;
    	 		$_data['rate_perday']=$data['rate_perday'];
    	 		$money_type = 1;
    	 		if($data['type_money']==1){
    	 			$total_debt = $data['total']+$data['debt_dollar'];
    	 			
    	 		}elseif($data['type_money']==2){
    	 			$total_debt = $data['total']+$data['debt_bath'];
    	 			$money_type = 2;
    	 		}else{
    	 			$total_debt = $data['total']+$data['debt_khmer'];
    	 			$money_type = 3;
    	 		}
    	 		$this->updateStatustoPaid($sender_id,$money_type);//update old tran debt to paid below add new debt
    	 		
    	 		
    	 		$_data['total_money_owe']=$total_debt;
    	 		unset($_data['return_money']);
    	 		unset($_data['is_kbank']);
    	 		$this->insert($_data);
    	 		
    	 		if($data['tran_type'] == 0){//debt with tran type = transfer
    	 			$amount = $data['total'];
    	 		}else{
    	 			$amount = -$data['total'];//យើងអស់លុយប៉ុន្តែយើងជំពាក់គេ
    	 		}
				$income = 0;
// 				$tran_type=7;//remain debt before pls ckeck condition  ;
				$tran_type=2;//remain debt ;
    	 		
    	 	}else{
    	 		
    	 		//add owe if money kbank differ money transfer
    	 		$_data['money_tran_id']=$tran_no;
    	 		$_data['rate_perday']=$data['rate_perday'];
    	 		$_data['total_money_owe']=$data['total'];
    	 		$_data['status_loan'] = 1;
    	 		unset($_data['return_money']);
    	 		
//     	 		if($data['tran_type'] == 0){//with tran type = transfer
//     	 			$amount = $data['total'];
//     	 		}else{
//     	 			$amount = -$data['total'];//វេចូល
//     	 		}
//     	 		$income = 1;
    	 		if($data['tran_type'] == 0){//with tran type = transfer
    	 			$amount = $data['total'];
    	 		}else{
    	 			$amount = -$data['total'];//វេចូល
    	 		}
    	 		
    	 		//	for add withdraw to send if sender use kbank account
    	 		if(!empty($data['is_kbank'])){
    	 			if($data['type_money']==1 AND empty($data['debt_dollar'])){//if kbank no money for transfer
    	 				$this->AddOweTransationBysender($_data);
    	 			}elseif($data['type_money']==2 AND empty($data['debt_bath'])){
    	 				$this->AddOweTransationBysender($_data);
    	 			}elseif($data['type_money']==3 AND empty($data['debt_khmer'])){
    	 				$this->AddOweTransationBysender($_data);
    	 			}
    	 				
    	 			$w_dollar = 0;
    	 			$w_bath = 0;$w_riel = 0;$b_dollar = 0;$b_bath = 0;$b_riel = 0;
    	 			$invoice = Application_Model_GlobalClass::getInvoiceWithdraw();
    	 			
    	 			$db_draw = new Application_Model_DbTable_DbKbank();
    	 			$sender_id = $db_draw->getSenderIdbyName($data['sender']);//covert sender name to sender id
    	 			
    	 			$remain_from_kbank = 0;
    	 			$total_withdraw =  $data['total'];
    	 			if($data['type_money']==1){
    	 				$w_dollar = $data['total'];
    	 				$remain_from_kbank = $data['debt_dollar']-$data['total'];
    	 				if($data['debt_dollar']<$data['total']){
    	 					$w_dollar =$data['debt_dollar'];
    	 					$total_withdraw = $w_dollar;
    	 				}
    	 			}elseif($data['type_money']==2){
    	 				$w_bath = $data['total'];
    	 				$remain_from_kbank = $data['debt_bath']-$data['total'];
    	 				if($data['debt_bath']<$data['total']){
    	 					$w_bath =$data['debt_bath'];
    	 					$total_withdraw = $w_bath;
    	 				}
    	 			}else{
    	 				$w_riel = $data['total'];
    	 				$remain_from_kbank = $data['debt_khmer']-$data['total'];
    	 				if($data['debt_khmer']<$data['total']){
    	 					$w_riel = $data['debt_khmer'];
    	 					$total_withdraw = $w_riel;
    	 				}
    	 			}
    	 			
    	 			///if transfer more thand money in kbank so we add this send dept
    	 			
    	 			if($remain_from_kbank < 0){
    	 				$_data['total_money_owe'] = - $remain_from_kbank;//if money tranfer larg than money in kbank
    	 				$debt_id = $this->AddOweTransationBysender($_data);
    	 				 
    	 				$arr =array(
    	 						'tran_id' 	=>$tran_no,//add transaction debt if money in bank small than transfer
    	 						'tran_type' =>8,//status remain debt
    	 						'curr_type'	=>$data['type_money'],
    	 						'amount'	=>-$remain_from_kbank,
    	 						'user_id'	=>$session_user->user_id,
    	 						'income'=>	0,
    	 			
    	 				);
    	 				$ass = $db_cap->addMoneyToCapitalDetail($arr);
    	 				 
    	 			}
    	 			//if money have in account kbank
    	 			if(!empty($data['debt_dollar'])){
    	 				$b_dollar = $data['debt_dollar'];
    	 			}
    	 			if(!empty($data['debt_bath'])){
    	 				$b_bath = $data['debt_bath'];
    	 			}
    	 			if(!empty($data['debt_khmer'])){
    	 				$b_riel= $data['debt_khmer'];
    	 			}
    	 			//     	 		$amount = 0;
    	 			if($b_dollar>0 OR $b_bath OR $b_riel>0){
    	 				$_arr = array(
    	 						'sender'=>$sender_id,
    	 						'invoice_no'=>$invoice,
    	 						'withdraw_dollar'=>$w_dollar,
    	 						'withdraw_bath'=>$w_bath,
    	 						'withdraw_riel'=>$w_riel,
    	 						'debt_dollar'=>$b_dollar,
    	 						'debt_bath'=>$b_bath,
    	 						'debt_riel'=>$b_riel,
    	 						'send_date'=>$data['send_date'],
    	 				);
    	 				$db_draw->addWithdrawBySendName($_arr);
    	 				$tran_no= $db->lastInsertId();
    	 				 
    	 				$arr =array(
    	 						'tran_id' 	=>$tran_no,// withdraw money to capital detail
    	 						'tran_type' =>7,
    	 						'curr_type'	=>$data['type_money'],
    	 						'amount'	=> -$total_withdraw,
    	 						'user_id'	=>$session_user->user_id,
    	 						'income'=>	0,
    	 							
    	 				);
    	 				$ass = $db_cap->addMoneyToCapitalDetail($arr);
    	 			}
    	 				
    	 			$income = 0;
//     	 			if($data['tran_type'] == 0){//with tran type = transfer
//     	 				$amount = $data['total'];
//     	 			}else{
//     	 				$amount = -$data['total'];//វេចូល
//     	 			}
	    	 		
    	 		}else{
//     	 			if($data['tran_type'] == 0){//with tran type = transfer
//     	 				$amount = $data['total'];
//     	 			}else{
//     	 				$amount = -$data['total'];//វេចូល
//     	 			}
    	 			$income = 1;
    	 			///update date current capital if get money from sender////////////////////
    	 			$rs = $db_cap->DetechCapitalExist($session_user->user_id, $data['type_money']);
    	 			if(!empty($rs)){//update new user
    	 				$arr = array(
    	 						'amount'=>$rs['amount']+$amount
    	 				);
    	 				$db_cap->updateCurrentBalanceById($rs['id'],$arr);
    	 			}else{
    	 				$date = date("Y-m-d H:i:s");//change it to current edit
    	 				$arr =array(
    	 						'amount'=>$amount,
    	 						'currencyType'=>$data['type_money'],
    	 						'userid'=>$session_user->user_id,
    	 						'statusDate'=>$date
    	 				);
    	 				$db_cap->AddCurrentBalanceById($arr);
    	 			}
    	 		}
    	 		$tran_type = 2;//transfer
    	 		
    	 	}
    	 	////for add capital detail
    	 	$date = date("Y-m-d H:i:s");//change it to current edit
    	 	$arr =array(
    	 			'tran_id' 	=>$tran_no,
    	 			'tran_type' =>$tran_type,
    	 			'curr_type'	=>$data['type_money'],
    	 			'amount'	=>$amount,
    	 			'user_id'	=>$session_user->user_id,
    	 			'income'=>$income,
    	 			
    	 	);
    	 	
    	 	$ass = $db_cap->addMoneyToCapitalDetail($arr);
    	 	return  $db->commit();
    	 } catch (Exception $e) {
    	 	$db->rollBack();
    	 }
    }
    public function updateStatustoPaid($sender_id,$money_type){
    	$this->_name='cs_transactions_owe';
    	$sql =" SELECT id,status_loan FROM `cs_transactions_owe` AS d WHERE amount_type = $money_type AND 
    			sender_name=$sender_id AND status_loan!=3 ";
    	$db = $this->getAdapter();
    	$rows = $db->fetchAll($sql);
    	if(!empty($rows)){
    		foreach($rows As $key =>$row){
    			$arr = array(
    					'status_loan'=>3   //paid already
    					);
    			$where = $db->quoteInto('id=?', $row['id']);
    			$this->update($arr, $where);
    			
    		}
    	}
    	
    }
    public function AddOweTransationBysender($_arr){
    	$this->_name="cs_transactions_owe";
    	$this->insert($_arr);
    }
    
	function updateMoneyTransfer($data)
    {       	
    	$percent =  (empty($data['percent']))? 0 : $data['percent'];
    	$send_date = $data['send_date'].' '.date('h:i:s');
    	$exp_date = $data['epx_date'].' '.date('h:i:s'); 	
    	$rec_date = ($data['status'] == 0) ? null :date('Y-m-d h:i:s');
    	$session_user=new Zend_Session_Namespace('auth');
    	$mt_id = $data['id'];
    	$old_data = $this->getTransactionDetailByID($mt_id);
//     	print_r($old_data); exit;
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	try {
    		$agent_id = $data['agent_id'];
    		$_data=array(
    					'sender_name'=>$data['sender'],
						'reciever_name'=>$data['reciever'],
						'reciever_tel'=>$data['reciever_tel'],
						'invoice_no'=>$data['invoice_no'],
						'amount'=>$data['amount'],
						'amount_type'=>$data['type_money'],
						'commission'=>(empty($data['commission'])) ? 0 :$data['commission'],
						'commission_type'=>$data['type_money'],
						'commission_percent'=> $percent,
    					'commission_agent'=>$data['commission_agent'],
						'recieved'=>$data['recieve_money'],
						'recieved_type'=>$data['type_money'],
						'status'=>$data['status'],
    					'status_loan'=>(empty($data['loan'])) ? 0 : $data['loan'],
						'cut_service'=>(empty($data['minus'])) ? 0 : $data['minus'],
						'agent_id'=>$agent_id,	
    					'subagent_id'=>(empty($data['sub_agent_id'])) ? 0 : $data['sub_agent_id'],					
						'send_date'=>$send_date,
						'expire_date'=>$exp_date,
    					'recieved_date'=>$rec_date,
						'transfer_money'=>$data['gave'],
						'total_money'=>$data['total'],
						'return_money'=>$data['return_money'],
    					'tran_type'=>$data['tran_type'],
    					'is_kbank'=>(!empty($data['is_kbank']))?0 : $data['is_kbank'],
						'user_id'=>$session_user->user_id,
    							
    	           );   	 
	    	$where=$this->getAdapter()->quoteInto('id=?', $mt_id); 
	    	$this->update($_data, $where);
	    	
	    	$this->_name="cs_transactions_owe";
	    	$_data['total_money_owe']=$data['total'];
	    	unset($_data['return_money']);
	    	
	    	
	    	
	    	$db_sender = new Application_Model_DbTable_DbKbank();
	    	$sender_id = $db_sender->getSenderIdbyName($data['sender']);
// 	    	$db_rate=new Application_Model_DbTable_DbRate();
// 	    	$rate = $db_rate->getCurrentRate();
	    	
	    	$_data['rate_perday']=$data['rate_perday'];
	    	$_data['total_money_owe']=$data['total'];
	    	$_data['sender_name']=$sender_id;
	    	$_data['exchange_rate_db'] =  $data['BD'];
	    	$_data['exchange_rate_dr'] =  $data['RD'];
	    	$_data['exchange_rate_rb'] =  $data['RB'];
	    	$_data['is_orgdebt'] =  1;
	    	
	    	unset($_data['status_loan']);
	    	unset($_data['return_money']);
	    	
	    	
	    	
// 	    	$this->_name="cs_transactions_owe";
	    	 
// 	    	$_data['money_tran_id']=$tran_no;
// 	    	$_data['rate_perday']=$data['rate_perday'];
// 	    	$money_type = 1;
// 	    	if($data['type_money']==1){
// 	    		$total_debt = $data['total']+$data['debt_dollar'];
	    			
// 	    	}elseif($data['type_money']==2){
// 	    		$total_debt = $data['total']+$data['debt_bath'];
// 	    		$money_type = 2;
// 	    	}else{
// 	    		$total_debt = $data['total']+$data['debt_khmer'];
// 	    		$money_type = 3;
// 	    	}
// 	    	$this->updateStatustoPaid($sender_id,$money_type);//update old tran debt to paid below add new debt
// 	    	$_data['total_money_owe']=$total_debt;
// 	    	unset($_data['return_money']);
// 	    	unset($_data['is_kbank']);
// 	    	$this->insert($_data);
	    	
	    	
	    	
	    	$exist=$this->TransaOweNotexist($mt_id);
	    	if(!empty($exist)){
	    		$where=$this->getAdapter()->quoteInto('money_tran_id=?', $mt_id);
	    		$this->update($_data, $where);
	    	}
	    	else{
	    		if(!empty($data['loan'])){
	    			
	    			$_data['rate_perday']=$data['rate_perday'];
	    			$_data['money_tran_id']=$mt_id;
	    			$this->insert($_data);
	    		}
	    	}
	    	
	    	
    	
	    	$tran_type = $data['tran_type'];
	    	$old_agent = $old_data['agent_id'];
	    	$old_amount_type = $old_data['amount_type'];
	    	$old_amount = $old_data['amount'] + $old_data['commission_agent']; 
	    	$new_agent= $_data['agent_id']; 
	    	$new_amount_type= $_data['amount_type']; 
	    	$new_amount = $_data['amount'] + $_data['commission_agent'];
    		$db_cap = new Application_Model_DbTable_DbCapitalAgent(); 		
    		$db_cap->updateBalanceWithCondiction($tran_type, $old_agent, $old_amount_type, $old_amount, $new_agent, $new_amount_type, $new_amount);
    	
    		return  $db->commit();
    	} catch (Exception $e) {
    		$db->rollBack();
    	}
    }
    public function TransaOweNotexist($trans_id){
    	$db = $this->getAdapter();
    	$sql = "SELECT money_tran_id FROM cs_transactions_owe WHERE money_tran_id=".$trans_id;
    	$sql.=" LIMIT 1";
    	return  $db->fetchOne($sql);
    	
    }
    
	function updateMoneyTransferByStatus($id, $status)
    {    	
    	$_data=array(
    					'status'=>$status,												
						'recieved_date'=>date('Y-m-d h:i:s')
										
    	           );    	    
    	$where=$this->getAdapter()->quoteInto('id=?', $id);    	
    	return  $this->update($_data, $where);
    }
    
	function checkExpired(){    	
    	$_data=array(
    					'status'=>5,												
						'recieved_date'=>date('Y-m-d h:i:s')
										
    	           );    	    
    	$where= "DATEDIFF(`expire_date`, CURRENT_DATE) < 0 AND (`status` = 0 OR `status` = 2)";
    	
    	    	
    	return  $this->update($_data, $where);
    }
    
 	function getTranCounter(){
    	$db = $this->getAdapter();
    	$session_user=new Zend_Session_Namespace('auth');
		$sql = "CALL `getTransactionsCounter`(".$session_user->user_id.")";
		$row = $db->fetchRow($sql);
		return $row;
    }
    
	function setTransactionReportAutoPrint($datas){
		
		$s_where = array();                    
		$where = '';
		foreach ($datas as $key => $data) {
			$s_where[] = "`id` = ".$data;	
		}
		$where .= implode(' OR ',$s_where);
		
		
		$_data=array( 'status'=>1);   	 
    	
    	return  $this->update($_data, $where);
		
	}
	
	function updateStatusAfterPrint($datas){
	
		$s_where = array();
		$where = '';
		foreach ($datas as $key => $data) {
			$s_where[] = "`id` = ".$data;
		}
		$where .= implode(' OR ',$s_where);
		$_data=array( 'status'=>1);
		 
		return  $this->update($_data, $where);
	
	}
	
	
	function getTransactionSummaryReports($user_id, $from_date, $to_date, $showall=false){
		$db = $this->getAdapter();
		
		$orderby = " ORDER BY u.`id`,  mt.tran_type, mt.`amount_type` ";
		$where = "WHERE DATE(mt.`send_date`) BETWEEN DATE('".$from_date . "') AND DATE('". $to_date ."') AND mt.`status_loan` = 0 ";
		
		if($showall){
			$orderby = " ORDER BY mt.`status_loan`,  mt.tran_type, mt.`amount_type` ";
			$where = "WHERE DATE(mt.`send_date`) BETWEEN DATE('".$from_date . "') AND DATE('". $to_date ."') ";
		}
		
		
		
		if($user_id > 0){
			$where .= " AND mt.user_id = ". $user_id;
		}
		
		$where .= " AND (mt.status BETWEEN 0 AND 1 OR mt.status = 3)";
		
		$sql = "SELECT
					 mt.`reciever_tel`
				    , mt.`sender_name`
				    , mt.`reciever_name`				    
				    , mt.`total_money`
				    , mt.`commission_agent`
				    , mt.`status_loan`
				    , cur.`name` AS `currncytype`
					, cur.`symbol` AS `currncysymbol`
					, CONCAT(u.`last_name`,' ',u.`first_name`) AS username
					, mt.tran_type
				FROM
				     `cs_money_transactions`  AS mt 			        
				   	INNER JOIN `cs_currencies` AS cur ON (cur.`id` = mt.`amount_type`)
					INNER JOIN  `cs_users` AS u ON (u.`id`=mt.user_id)";
// 		echo $sql.$where.$orderby; exit();
		$rs = $db->fetchAll($sql.$where.$orderby);
// 		print_r($rs); exit();
		return $rs;
	}
}


