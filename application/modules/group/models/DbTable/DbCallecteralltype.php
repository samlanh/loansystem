<?php

class Group_Model_DbTable_DbCallecteralltype extends Zend_Db_Table_Abstract
{
    protected $_name = 'ln_callecteral_type';
    function addcallecterall($data){
    	$db = $this->getAdapter();
    	$arr = array(
    			'title_en'=>$data['title_kh'],
    			'title_kh'=>$data['title_kh'],
    			'date'=>$data['date'],
    			'status'=>1,
    			
//     			'title_en'=>$data['title_en'],
    			//'displayby'=>$data['display_by'],
//     			'key_code'=>$numer_record+1,
//     			'type'=>13,
    			);
         $id=$this->insert($arr);
     
    }
    
    public function addCallteralAjax($data){
    	$arr = array(
    			'title_en'=>$data['title_kh'],
    			'title_kh'	=>$data['title_kh'],
    			'date'		=>date("Y-m-d"),
    			'status'	=>$data['status'],
//     			'title_en'	=>$data['title_en'],
    			);
         return $this->insert($arr);
    }
    
    function updatcallecterall($data){
    	$arr = array(
    			'title_en'=>$data['title_kh'],
    			'title_kh'=>$data['title_kh'],
    			'date'=>$data['date'],
    			'status'=>$data['status'],
//     			'title_en'	=>$data['title_en'],
    			//'displayby'=>$data['display_by'],
    			);
    	$where=" id = ".$data['id'];
    	$this->update($arr, $where);
    }
    function getcallecterallbyid($id){
    	$db = $this->getAdapter();
    	$sql="SELECT id,title_en,title_kh,display_by,date,status FROM $this->_name where id=$id ";
    	return $db->fetchRow($sql);
    }
    function geteAllid($search=null){
    	$db = $this->getAdapter();
    	$sql=" SELECT id,title_en,title_kh,date,status FROM $this->_name where id ORDER BY id DESC";
    	return $db->fetchAll($sql);
    	
    }
}

