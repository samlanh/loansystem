<?php

class Home_Model_DbTable_DbDashboard extends Zend_Db_Table_Abstract
{

//     protected $_name = 'ln_properties';
//     public function getUserId(){
//     	$session_user=new Zend_Session_Namespace(SYSTEM_SES);
//     	return $session_user->user_id;
//     }
	
	function CountAllClient(){
		$db = $this->getAdapter();
		$sql="SELECT COUNT(p.`client_id`) AS total
			FROM `ln_client` AS p 
			WHERE p.`status` =1 ";
		return $db->fetchOne($sql);
	}
	
	function CountAllAgency(){
		$db = $this->getAdapter();
		$sql="SELECT COUNT(p.`co_id`) AS total
		FROM `ln_co` AS p 
		WHERE p.`status` =1 ";
		return $db->fetchOne($sql);
	}
	
	function countAllLoan(){
		$db = $this->getAdapter();
		$sql="
			SELECT 
				COUNT(l.`id`) AS countLoan
			FROM `ln_loan` AS l 
			WHERE l.`status`=1 
		";
		
		$dbGB = new Application_Model_DbTable_DbGlobal();
		$userInfo = $dbGB->getUserInfo();
		if(!empty($userInfo)){
			$userLevel = empty($userInfo["level"]) ? 0: $userInfo["level"];
			$userId = empty($userInfo["user_id"]) ? 0: $userInfo["user_id"];
			if($userLevel!=1){
				$sql.=" AND l.user_id=$userId ";
			}
		}
		
		return $db->fetchOne($sql);
	}
	
	function countAllLoanComplete(){
		$db = $this->getAdapter();
		$sql="
		SELECT 
			COUNT(l.`id`) AS countLoan
			, SUM(l.`loan_amount`) AS total
			,l.`currency_type`
		FROM `ln_loan` AS l 
		WHERE l.`status`=1 AND l.`is_completed`=1 ";
		
		$dbGB = new Application_Model_DbTable_DbGlobal();
		$userInfo = $dbGB->getUserInfo();
		if(!empty($userInfo)){
			$userLevel = empty($userInfo["level"]) ? 0: $userInfo["level"];
			$userId = empty($userInfo["user_id"]) ? 0: $userInfo["user_id"];
			if($userLevel!=1){
				$sql.=" AND l.user_id=$userId ";
			}
		}
		
		return $db->fetchOne($sql);
	}
	
	function countAllBadLoan(){
		$db = $this->getAdapter();
		$sql="
			SELECT 
				COUNT(l.`id`) AS countLoan
			FROM `ln_loan` AS l 
			WHERE 
				l.`status`=1 AND l.`is_badloan`=1 
			";
		
		$dbGB = new Application_Model_DbTable_DbGlobal();
		$userInfo = $dbGB->getUserInfo();
		if(!empty($userInfo)){
			$userLevel = empty($userInfo["level"]) ? 0: $userInfo["level"];
			$userId = empty($userInfo["user_id"]) ? 0: $userInfo["user_id"];
			if($userLevel!=1){
				$sql.=" AND l.user_id=$userId ";
			}
		}
		return $db->fetchOne($sql);
	}
	
	function TotalExpense($data = array()){
		$db = $this->getAdapter();
		$sql="SELECT SUM(p.`total_amount`) AS totalAmount,p.`curr_type`
			FROM `ln_income_expense` AS p 
			WHERE p.`status` =1
			 ";
		if(!empty($data['currentMonthData'])){
			$thisMonth = date("Y-m");
			$sql.=" AND DATE_FORMAT(p.`date`, '%Y-%m')='$thisMonth' ";
		}

		$dbGB = new Application_Model_DbTable_DbGlobal();
		$userInfo = $dbGB->getUserInfo();
		if(!empty($userInfo)){
			$userLevel = empty($userInfo["level"]) ? 0: $userInfo["level"];
			$userId = empty($userInfo["user_id"]) ? 0: $userInfo["user_id"];
			if($userLevel!=1){
				$sql.=" AND p.user_id=$userId ";
			}
		}
		
		$sql.=" GROUP BY p.`curr_type` ";
		return $db->fetchAll($sql);
	}
	function getTotalOtherIncome($data = array()){
		$db = $this->getAdapter();
		$sql="
		SELECT 
			SUM(i.`total_amount`) AS total,i.`curr_type`
		FROM ln_income AS i
		WHERE i.`status`=1  ";
		if(!empty($data['currentMonthData'])){
			$thisMonth = date("Y-m");
			$sql.=" AND DATE_FORMAT(i.`date`, '%Y-%m')='$thisMonth' ";
		}
		
		$dbGB = new Application_Model_DbTable_DbGlobal();
		$userInfo = $dbGB->getUserInfo();
		if(!empty($userInfo)){
			$userLevel = empty($userInfo["level"]) ? 0: $userInfo["level"];
			$userId = empty($userInfo["user_id"]) ? 0: $userInfo["user_id"];
			if($userLevel!=1){
				$sql.=" AND i.user_id=$userId ";
			}
		}
		
		$sql.=" GROUP BY i.`curr_type` ";
		return $db->fetchAll($sql);
	}
	
	function getTotalLoanCollectIncome($data = array()){
		$db = $this->getAdapter();
		$sql="SELECT 
				SUM(crm.`recieve_amount`) AS total, crm.`currency_type`
			FROM ln_client_receipt_money AS crm WHERE crm.status=1 
		 ";
		 if(!empty($data['currentMonthData'])){
			$thisMonth = date("Y-m");
			$sql.=" AND DATE_FORMAT(crm.`date_pay`, '%Y-%m')='$thisMonth' ";
		}
		
		$dbGB = new Application_Model_DbTable_DbGlobal();
		$userInfo = $dbGB->getUserInfo();
		if(!empty($userInfo)){
			$userLevel = empty($userInfo["level"]) ? 0: $userInfo["level"];
			$userId = empty($userInfo["user_id"]) ? 0: $userInfo["user_id"];
			if($userLevel!=1){
				$sql.=" AND crm.user_id=$userId ";
			}
		}
		
		$sql.=" GROUP BY crm.`currency_type` ";
		
		return $db->fetchAll($sql);
	}
	function getTotalFeeByCurrency($data = array()){
		$db = $this->getAdapter();
		$sql="SELECT 
				SUM(COALESCE(l.`admin_fee`,0)+COALESCE(l.`other_fee`,0)) AS total, l.`currency_type`
			FROM ln_loan AS l WHERE l.status=1 AND (l.admin_fee> 0 OR l.other_fee>0 )
		 ";
		 if(!empty($data['currentMonthData'])){
			$thisMonth = date("Y-m");
			$sql.=" AND DATE_FORMAT(l.`date_release`, '%Y-%m')='$thisMonth' ";
		}
		$dbGB = new Application_Model_DbTable_DbGlobal();
		$userInfo = $dbGB->getUserInfo();
		if(!empty($userInfo)){
			$userLevel = empty($userInfo["level"]) ? 0: $userInfo["level"];
			$userId = empty($userInfo["user_id"]) ? 0: $userInfo["user_id"];
			if($userLevel!=1){
				$sql.=" AND l.user_id=$userId ";
			}
		}
		
		$sql.=" GROUP BY l.`currency_type` ";
		
		return $db->fetchAll($sql);
	}
	
	
	function getLoanDisbursPerMonth($yearMonth){
		$db = $this->getAdapter();
		$sql="
		SELECT 
			COUNT(l.`id`) AS countLoan
			, SUM(l.`loan_amount`) AS total
			,l.`currency_type`
		FROM `ln_loan` AS l 
		WHERE l.`status`=1 AND
		DATE_FORMAT(l.`date_release`, '%Y-%m')='$yearMonth'
		";
		
		$dbGB = new Application_Model_DbTable_DbGlobal();
		$userInfo = $dbGB->getUserInfo();
		if(!empty($userInfo)){
			$userLevel = empty($userInfo["level"]) ? 0: $userInfo["level"];
			$userId = empty($userInfo["user_id"]) ? 0: $userInfo["user_id"];
			if($userLevel!=1){
				$sql.=" AND l.user_id=$userId ";
			}
		}
		$sql.=" GROUP BY l.`currency_type`";
		$row = $db->fetchAll($sql);
		$reilsLoan = 0;
		$dollarLoan = 0;
		$bahtLoan = 0;
		if (!empty($row)) foreach ($row as $rs){
			if ($rs['currency_type']==1){
				$reilsLoan = $rs['countLoan'];
			}else if ($rs['currency_type']==2){
				$dollarLoan = $rs['countLoan'];
			}else if ($rs['currency_type']==3){
				$bahtLoan = $rs['countLoan'];
			}
		}
		$array = array('riels'=>$reilsLoan,'dollar'=>$dollarLoan,'bath'=>$bahtLoan);
		return $array;
	}
	function getCountPawnShop($completed=null,$dach=null){
		$db = $this->getAdapter();
		$sql="SELECT COUNT(p.`id`) AS countPawnShop 
		FROM ln_pawnshop AS p
		WHERE p.`status`=1";
		if (!empty($completed)){
			$sql.=" AND p.`is_completed`=1";
		}
		if (!empty($dach)){
			$sql.=" AND p.`is_dach`=1 ";
		}
		
		$dbGB = new Application_Model_DbTable_DbGlobal();
		$userInfo = $dbGB->getUserInfo();
		if(!empty($userInfo)){
			$userLevel = empty($userInfo["level"]) ? 0: $userInfo["level"];
			$userId = empty($userInfo["user_id"]) ? 0: $userInfo["user_id"];
			if($userLevel!=1){
				$sql.=" AND p.user_id=$userId ";
			}
		}
		
		$sql.=" LIMIT 1";
		return $db->fetchOne($sql);
	}
	
	function getCountSaleInstallment($data=array()){
		$db = $this->getAdapter();
		$completed=empty($data["isCompleted"]) ? null : $data["isCompleted"];
		$saleType=empty($data["saleType"]) ? null : $data["saleType"];
		
		$sql="
		SELECT 
			COUNT(s.`id`) AS countSale 
		FROM ln_ins_sales_install AS s
		WHERE s.`status`=1 ";
		if (!empty($completed)){
			$sql.=" AND s.`is_completed`=1 ";
		}
		if (!empty($saleType)){
			$sql.=" AND s.selling_type=2 ";
		}
		
		$dbGB = new Application_Model_DbTable_DbGlobal();
		$userInfo = $dbGB->getUserInfo();
		if(!empty($userInfo)){
			$userLevel = empty($userInfo["level"]) ? 0: $userInfo["level"];
			$userId = empty($userInfo["user_id"]) ? 0: $userInfo["user_id"];
			if($userLevel!=1){
				$sql.=" AND s.user_id=$userId ";
			}
		}
		
		$sql.=" LIMIT 1";
		return $db->fetchOne($sql);
	}
	
	function compareLoanVsPreviousMonth(){
		$db = $this->getAdapter();
		
		$previousMonth = date("Y-m",strtotime(" -1 month"));
		$thisMonth = date("Y-m");
		
		
		$sql="SELECT 
				COUNT(l.`id`) AS countLoan
			FROM 
				`ln_loan` AS l 
			WHERE l.`status`=1 
		";
		$sql.=" AND DATE_FORMAT(l.`date_release`, '%Y-%m')='$previousMonth' ";
		
		$dbGB = new Application_Model_DbTable_DbGlobal();
		$userInfo = $dbGB->getUserInfo();
		if(!empty($userInfo)){
			$userLevel = empty($userInfo["level"]) ? 0: $userInfo["level"];
			$userId = empty($userInfo["user_id"]) ? 0: $userInfo["user_id"];
			if($userLevel!=1){
				$sql.=" AND l.user_id=$userId ";
			}
		}
		
		$previousMonthLoan = $db->fetchOne($sql);
		$previousMonthLoan = empty($previousMonthLoan) ? 0 : $previousMonthLoan;
		
		$sql="SELECT 
				COUNT(l.`id`) AS countLoan
			FROM 
				`ln_loan` AS l 
			WHERE l.`status`=1 
		";
		$sql.=" AND DATE_FORMAT(l.`date_release`, '%Y-%m')='$thisMonth' ";
		if(!empty($userInfo)){
			$userLevel = empty($userInfo["level"]) ? 0: $userInfo["level"];
			$userId = empty($userInfo["user_id"]) ? 0: $userInfo["user_id"];
			if($userLevel!=1){
				$sql.=" AND l.user_id=$userId ";
			}
		}
		$thisMonthLoan = $db->fetchOne($sql);
		$thisMonthLoan = empty($thisMonthLoan) ? 0 : $thisMonthLoan;
		
		$different = $thisMonthLoan - $previousMonthLoan;
		$percentage = 100;
		if($thisMonthLoan<=0){
			$percentage = 0;
		}
		if($previousMonthLoan>0){
			$percentage = (($previousMonthLoan - $thisMonthLoan) / $previousMonthLoan) * 100;
		}
		
		$arr = array(
			"different"=>$different,
			"percentage"=>$percentage,
		);
		
		return $arr;
	}
	
}