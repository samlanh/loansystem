<?php
class Installment_ProductController extends Zend_Controller_Action
{
public function init()
    {
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
    }
    protected function GetuserInfoAction(){
    	$user_info = new Application_Model_DbTable_DbGetUserInfo();
    	$result = $user_info->getUserInfo();
    	return $result;
    }
	function updatecodeAction(){
		$db = new Installment_Model_DbTable_DbProduct();
		$db->getProductCoded();
	}
    public function indexAction()
    {
    	$db = new Installment_Model_DbTable_DbProduct();
		$user_info = new Application_Model_DbTable_DbGetUserInfo();
		$result = $user_info->getUserInfo();
    	if($this->getRequest()->isPost()){
    		$data = $this->getRequest()->getPost();
    	}else{
    		$data = array(
    			'adv_search'=>	'',
    			'branch_id'	=>	-1,
    			'category'	=>	-1,
    			'product_type'=>-1,
    			'start_date'=>	date("Y-m-d"),
    			'end_date'	=>	date("Y-m-d"),
    			'status'	=>  -1
    		);
    	}
    	$columns=array("BRANCH_NAME","ITEM_CODE","ITEM_NAME","TYPE",
    			"PRODUCT_CATEGORY","CURRENT_QTY","COST_PRICE","SOLD_PRICE","BY_USER","STATUS");
    	$rows = $db->getAllProduct($data);
		$link=array(
				'module'=>'installment','controller'=>'product','action'=>'edit',);
	
		$list = new Application_Form_Frmtable();
		$this->view->list=$list->getCheckList(10, $columns, $rows,array('item_name'=>$link,'product_type'=>$link,'item_code'=>$link,'barcode'=>$link,'branch'=>$link));
		
    	$formFilter = new Installment_Form_FrmProduct();
    	$this->view->formFilter = $formFilter->add();
    	Application_Model_Decorator::removeAllDecorator($formFilter);
	}
	public function addAction()
	{
		    $db = new Installment_Model_DbTable_DbProduct();
			if($this->getRequest()->isPost()){ 
				try{
					$post = $this->getRequest()->getPost();
					$db->add($post);
					if(isset($post["save_close"]))
					{
						Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS", '/installment/product');
					}else{
						Application_Form_FrmMessage::message("INSERT_SUCCESS");
					}
				  }catch (Exception $e){
				  	Application_Form_FrmMessage::messageError("INSERT_ERROR",$err = $e->getMessage());
				  }
			}
			$dbc = new Application_Model_GlobalClass();
			$this->view->branch = $dbc->getAllBranchOption();
			
			$formCat = new Installment_Form_FrmCategory();
			$frmCat = $formCat->cat();
			$this->view->frmCat = $frmCat;
			Application_Model_Decorator::removeAllDecorator($frmCat);
			
			$db = new Installment_Model_DbTable_DbProduct();
			$row_cat = $db->getCategory();
			
	        array_unshift($row_cat,array(
	        'id' => -1,
	        'name' => 'បន្ថែមថ្មី',
	        ) );
	        $this->view->rs_cate=$row_cat;
	        
	        $row_protype = $db->getProducttype();
	        array_unshift($row_cat,array(
	        		'id' => -1,
	        		'name' => 'Add New',
	        ) );
	        $this->view->rs_protype=$row_protype;
	}
	public function editAction()
	{
		$id = $this->getRequest()->getParam("id"); 
		$db = new Installment_Model_DbTable_DbProduct();
		if($this->getRequest()->isPost()){ 
				try{
					$post = $this->getRequest()->getPost();
					$post["id"] = $id;
					$db->edit($post);
					Application_Form_FrmMessage::Sucessfull("EDIT_SUCCESS", '/installment/product');
				  }catch (Exception $e){
				  	Application_Form_FrmMessage::messageError("INSERT_ERROR", $e->getMessage());
				  }
		}
		$proLocation = $db->getProductLocation($id);
		if (empty($proLocation)){
			Application_Form_FrmMessage::Sucessfull("NO_RECORD","/installment/product",2);exit();
		}
		$this->view->rs_location = $proLocation;
		
		$this->view->rspro =  $db->getProductById($id);
		
		$dbc = new Application_Model_GlobalClass();
		$this->view->branch = $dbc->getAllBranchOption();
			
		$formCat = new Installment_Form_FrmCategory();
		$frmCat = $formCat->cat();
		$this->view->frmCat = $frmCat;
		Application_Model_Decorator::removeAllDecorator($frmCat);
			
		$db = new Installment_Model_DbTable_DbProduct();
		$row_cat = $db->getCategory();
			
		array_unshift($row_cat,array(
				'id' => -1,
				'name' => 'បន្ថែមថ្មី',
		) );
		$this->view->rs_cate=$row_cat;
		
		$row_protype = $db->getProducttype();
		array_unshift($row_cat,array(
				'id' => -1,
				'name' => 'Add New',
		) );
		$this->view->rs_protype=$row_protype;
	}
	public function copyAction()
	{
		$id = $this->getRequest()->getParam("id");
		$db = new Installment_Model_DbTable_DbProduct();
		if($this->getRequest()->isPost()){
			try{
				$post = $this->getRequest()->getPost();
				$db->add($post);
				if(isset($post["save_close"]))
				{
					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS", '/installment/product');
				}else{
					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS", '/installment/product/add');
				}
			}catch (Exception $e){
				Application_Form_FrmMessage::messageError("INSERT_ERROR", $e->getMessage());
			}
		}
		
		$proLocation = $db->getProductLocation($id);
		if (empty($proLocation)){
			Application_Form_FrmMessage::Sucessfull("NO_RECORD","/installment/product",2);exit();
		}
		
		$this->view->rs_location = $proLocation;
		$this->view->rspro =  $db->getProductById($id);
	
		$dbc = new Application_Model_GlobalClass();
		$this->view->branch = $dbc->getAllBranchOption();
			
		$formCat = new Installment_Form_FrmCategory();
		$frmCat = $formCat->cat();
		$this->view->frmCat = $frmCat;
		Application_Model_Decorator::removeAllDecorator($frmCat);
			
		$db = new Installment_Model_DbTable_DbProduct();
		$row_cat = $db->getCategory();
			
		array_unshift($row_cat,array(
				'id' => -1,
				'name' => 'បន្ថែមថ្មី',
		) );
		$this->view->rs_cate=$row_cat;
	
		$row_protype = $db->getProducttype();
		array_unshift($row_cat,array(
				'id' => -1,
				'name' => 'Add New',
		) );
		$this->view->rs_protype=$row_protype;
		$db = new Application_Model_GlobalClass();
		$this->view->rsbranch = $db->getAllBranchOption();
	}
	function getproductbycateAction(){
		if($this->getRequest()->isPost()){
			$post=$this->getRequest()->getPost();
			$db = new Installment_Model_DbTable_DbProduct();
// 			$result =$db->getallProductbycate($post['category_id']);
			$result =$db->getallProductbycate($post);
			print_r(Zend_Json::encode($result));
			exit();
		}
	}
	function getpriceAction(){
		if($this->getRequest()->isPost()){
			$post=$this->getRequest()->getPost();
			$db = new Installment_Model_DbTable_DbProduct();
			$result =$db->getProductById($post['product_id']);
			print_r(Zend_Json::encode($result));
			exit();
		}
	}
	public function addCategoryAction(){
		if($this->getRequest()->isPost()){
			try {
				$post=$this->getRequest()->getPost();
				$db = new Installment_Model_DbTable_DbCategory();
				$cat_id =$db->addNew($post);
				//$result = array('cat_id'=>$cat_id);
				echo Zend_Json::encode($cat_id);
				exit();
			}catch (Exception $e){
				$result = array('err'=>$e->getMessage());
				echo Zend_Json::encode($result);
				exit();
			}
		}
	}
	function getlocationAction(){
		if($this->getRequest()->isPost()){
			$_data = $this->getRequest()->getPost();
			$db = new Installment_Model_DbTable_DbProduct();
			$Client = $db->getAllBranchOption($_data);
			print_r(Zend_Json::encode($Client));
			exit();
		}
	}
	function getproductbybranchAction(){
		if($this->getRequest()->isPost()){
			$post=$this->getRequest()->getPost();
			$db = new Installment_Model_DbTable_DbProduct();
			// 			$result =$db->getallProductbycate($post['category_id']);
			$result =$db->getallProductbyBranch($post);
			array_unshift($result,array(
					'id' => -1,
					'name' => 'បន្ថែមថ្មី',
			) );
			print_r(Zend_Json::encode($result));
			exit();
		}
	}
}

