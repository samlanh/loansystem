<?php

class Application_Form_FrmPopupGlobal extends Zend_Dojo_Form
{
	public function init()
	{
		
	}
// 	public function getArrCsType(){
// 		$arr_type = array(
// 				"fi_cs_type" => "CS-TYPE",
// 				"fi_cs_zone" => "CS-ZONE",
// 				"fi_cs_items" => "CS-ITEMS",
// 				"fi_cs_subject" => "CS-SUBJECT",
// 				"fi_cs_living_situation" => "CS-LIVING",
// 				"fi_cs_ass_provided" => "CS-ASS-PROVIDED",
// 				"fi_cs_ass_requested" => "CS-ASS-REQUESTED",
// 				"fi_cs_current_condition" => "CS-CONDITION",
// 				"fi_cs_referal" => "CS-REFERAL",
// 				"fi_cs_type_caller" => "CS-TYPE-CALLER",
// 				"fi_cs_type_case" => "CS-TYPE-CASE",
// 				"fi_cs_ar_category" => "CS-AR-CATEGORY",
				 
// 				"cs-refresh" => "CS-REFRESH",
// 				"cs-member" => "CS-MEMBER"
// 		);
// 		return $arr_type;
// 	}
// 	public function getForm($action, $method, $url_cancel, $elements, $legend = null, $hidenvalues = null, $type=null, $page=null, $tableadd=null,$id_popup){
// 		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
// 		if($type!=null && $page!=null){
// 			$arr_type = $this->getArrCsType();
// 		}
		 
// 		$script= '<script type="text/javascript">
// 		jQuery(document).ready(function(){
// 		//binds form submission and fields to the validation engine
// 		jQuery("#frm").validationEngine();
// 	});
// 	</script>';
// 		$form="<div class='dijitHidden'>
// 				<div data-dojo-type='dijit.Dialog'  id='".$id_popup."' >";
// 		$form.= "<form  id=\"frm\" method='". $method ."' action='". $action ."' accept-charset=\"utf-8\" enctype=\"multipart/form-data\" style=\"position:relative;\"> ";
		 
// 		$form .= '<div class="btn" align="right">
// 		<button type="submit" class="positive">
// 		<img src="'.BASE_URL.'/images/icon/apply2.png" alt=""/>
// 		Save
// 		</button>
// 		<a href="'. $url_cancel .'" class="negative">
// 		<img src="'.BASE_URL.'/images/icon/cross.png" alt=""/>
// 		Cancel
// 		</a>
// 		</div>
// 		<fieldset>
// 		<legend>'.$tr->translate($legend).'</legend>
// 		<table>';
	
// 		foreach ($elements as $lbl => $element){
// 			$form .= '<tr>
// 			<td class="field">'. $tr->translate($lbl) .'</td>
// 			<td class="add-edit">'. $element .'</td>
// 			</tr>';
// 		}
// 		$form .= '</table>';
// 		if($tableadd!=null){
// 			$form .= $tableadd;
// 		}
// 		$form .= '</fieldset>';
// 		if(!empty($hidenvalues)){
// 			foreach ($hidenvalues as $i =>$h){
// 				$form .= $h;
// 			}
// 		}
		 
// 		$form .= "</form></div></div>";
// 		return $form ;
// 	}
	public function frmPopupClient(){
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$frmco = new Group_Form_FrmClient();
		$frm = $frmco->FrmAddClient();
		Application_Model_Decorator::removeAllDecorator($frm);
		$str='<div class="dijitHidden">
					<div data-dojo-type="dijit.Dialog"  id="frm_client" >
					<form id="form_client" name="form_client" />';
				$str.='<table style="margin: 0 auto; width: 100%;" cellspacing="7">
							<tr>
							       <td>Is Group</td>
									<td>'.$frm->getElement('is_group').'</td>
									<td>Client N</td>
									<td>'.$frm->getElement('client_no').'</td>
								</tr>
								<tr>
									<td>Name Khmer</td>
									<td>'.$frm->getElement('name_kh').'</td>
									<td>Name Eng</td>
									<td>'.$frm->getElement('name_en').'</td>
								</tr>
								<tr>
									<td>Sex</td>
									<td>'.$frm->getElement('sex').'</td>
									<td>Status</td>
									<td>'.$frm->getElement('situ_status').'</td>
								</tr>
								<tr>
									<td>Province</td>
									<td>'.$frm->getElement('province').'</td>
									<td>District</td>
									<td>'.$frm->getElement('district').'</td>
								</tr>
								<tr>
									<td>Commune</td>
									<td>'.$frm->getElement('commune').'</td>
									<td>'.$tr->translate("Village").'</td>
									<td>'.$frm->getElement('village').'</td>
								</tr>
								<tr>
									<td>Street</td>
									<td>'.$frm->getElement('street').'</td>
									<td>'.$tr->translate("House N.").'</td>
									<td>'.$frm->getElement('house').'</td>
									
								</tr>
								<tr>
									<td>ID Type</td>
									<td>'.$frm->getElement('id_type').'</td>
									<td>'.$tr->translate("ID Card").'</td>
									<td>'.$frm->getElement('id_no').'</td>
								</tr>
								<tr>
									<td>'.$tr->translate("Phone").'</td>
									<td>'.$frm->getElement('phone').'</td>
									<td>'.$tr->translate("Spouse Name").'</td>
									<td>'.$frm->getElement('spouse').'</td>
								</tr>
								<tr>
									<td>'.$tr->translate("Status").'</td>
									<td>'.$frm->getElement('status').'</td>
									<td>'.$tr->translate("Note").'</td>
									<td>'.$frm->getElement('desc').'</td>
								</tr>
								<tr>
									<td colspan="4" align="center">
									<input type="button" value="Save" label="Save" dojoType="dijit.form.Button" 
										 iconClass="dijitEditorIcon dijitEditorIconSave" onclick="addNewClient();"/>
									</td>
								</tr>
							</table>';	
							
		$str.='	</form>	</div>
				</div>';
		return $str;
	}
	public function frmPopupCO(){
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$frmclient = new Other_Form_FrmCO();
		$frm = $frmclient->FrmAddCO();
		Application_Model_Decorator::removeAllDecorator($frm);
		$str='<div class="dijitHidden">
				<div data-dojo-type="dijit.Dialog"  id="frm_co" >
					<form id="form_co" name="form_co"  dojoType="dijit.form.Form" method="post" enctype="application/x-www-form-urlencoded">
					<script type="dojo/method" event="onSubmit">
						if(this.validate()) {
							return true;
						}else {
							return false;
						}
			      </script>';
			$str.='<table style="margin: 0 auto; width: 100%;" cellspacing="7">
					<tr>
						<td></td>
						<td>'.$frm->getElement('namsdfse_kh').'</td>
					</tr>
					<tr>
						<td>'.$tr->translate("LOAN_NUMBER").'</td>
						<td>'.$frm->getElement('co_code').'</td>
					</tr>
					<tr>
						<td>'.$tr->translate("NAME_KH").'</td>
						<td>'.$frm->getElement('last_name').'</td>
					</tr>
					<tr>
						<td>'.$tr->translate("NAME_EN").'</td>
						<td>'.$frm->getElement('first_name').'</td>
					</tr>
					<tr>
						<td>'.$tr->translate("SEX").'</td>
						<td>'.$frm->getElement('co_sex').'</td>
					</tr>
					<tr>
						<td>'.$tr->translate("PHONE").'</td>
						<td>'.$frm->getElement('tel').'</td>
					</tr>
					<tr>
						<td>'.$tr->translate("EMAIL").'</td>
						<td>'.$frm->getElement('email').'</td>
					</tr>
					<tr>
						<td>'.$tr->translate("ADDRESS").'</td>
						<td>'.$frm->getElement('address').'</td>
					</tr>
					<tr>
						<td colspan="4" align="center">
						<input type="button" value="Save" label="'.$tr->translate("SAVECLOSE").'" dojoType="dijit.form.Button"
						iconClass="dijitEditorIcon dijitEditorIconSave" onclick="AddConew();"/>
						</td>
					</tr>						
		       </table>';
		$str.='</form>	</div>
		  </div>';
		return $str;								
	}
	public function frmPopupZone(){
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$frmzone = new Other_Form_FrmZone();
		$frm = $frmzone->FrmAddZone();
		Application_Model_Decorator::removeAllDecorator($frm);
		$str='<div class="dijitHidden">
				<div data-dojo-type="dijit.Dialog"  id="frm_zone" >
			<form id="form_zone" dojoType="dijit.form.Form" method="post" enctype="application/x-www-form-urlencoded">
				<script type="dojo/method" event="onSubmit">
					if(this.validate()) {
						return true;
					}else {
						return false;
					}
		      </script>';
			$str.='<table style="margin: 0 auto; width: 100%;" cellspacing="7">
					<tr>
						<td>'.$tr->translate("ZONE_NAME").'</td>
						<td>'.$frm->getElement('zone_name').'</td>
					</tr>
					<tr>
						<td>'.$tr->translate("ZONE_NUMBER").'</td>
						<td>'.$frm->getElement('zone_number').'</td>
					</tr>
					<tr>
						<td colspan="4" align="center">
						<input type="button" value="Save" label="'.$tr->translate("SAVECLOSE").'" dojoType="dijit.form.Button"
						iconClass="dijitEditorIcon dijitEditorIconSave" onclick="addNewZone();"/>
						</td>
					</tr>
				</table>';
		$str.='</form>		</div>
		</div>';
		return $str;
	}
	public function frmPopupDistrict(){
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$frm = new Other_Form_FrmDistrict();
		$frm = $frm->FrmAddDistrict();
		Application_Model_Decorator::removeAllDecorator($frm);
		$str='<div class="dijitHidden">
				<div data-dojo-type="dijit.Dialog"  id="frm_district" >
				<form id="form_district" >';
		$str.='<table style="margin: 0 auto; width: 100%;" cellspacing="7">
					<tr>
						<td>'.$tr->translate("DISTRICT_NAME").'</td>
						<td>'.$frm->getElement('pop_district_namekh').'</td>
					</tr>
					<tr>
						<td>'.$tr->translate("PROVINCE_NAME").'</td>
						<td>'.$frm->getElement('province_names').'</td>
					</tr>
					
					<tr>
						<td colspan="2" align="center">
						<input type="button" value="Save" label="'.$tr->translate("SAVECLOSE").'" dojoType="dijit.form.Button"
						iconClass="dijitEditorIcon dijitEditorIconSave" onclick="addNewDistrict();"/>
						</td>
				    </tr>
				</table>';
		$str.='</form></div>
		</div>';
		return $str;
	}
	public function frmPopupCommune(){
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$str='<div class="dijitHidden">
				<div data-dojo-type="dijit.Dialog"  id="frm_commune" >
					<form id="form_commune" >';
			$str.='<table style="margin: 0 auto; width: 100%;" cellspacing="7">
					<tr>
						<td>'.$tr->translate("COMMUNE_NAME").'</td>
						<td>'.'<input dojoType="dijit.form.ValidationTextBox" required="true" class="fullside" id="commune_namekh" name="commune_namekh" value="" type="text">'.'</td>
					</tr>
					<tr>
						<td></td>
						<td>'.'<input dojoType="dijit.form.TextBox" required="true" class="fullside" id="district_nameen" name="district_nameen" value="" type="hidden">'.'</td>
					</tr>
					<tr>
						<td colspan="2" align="center">
						<input type="button" value="Save" label="'.$tr->translate("SAVECLOSE").'" dojoType="dijit.form.Button"
						iconClass="dijitEditorIcon dijitEditorIconSave" onclick="addNewCommune();"/>
						</td>
					</tr>
				</table>';
		$str.='</form></div>
			</div>';
		return $str;
	}
	public function frmPopupVillage(){
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$str='<div class="dijitHidden">
				<div data-dojo-type="dijit.Dialog"  id="frm_village" >
					<form id="form_village" dojoType="dijit.form.Form" method="post" enctype="application/x-www-form-urlencoded">
		 <script type="dojo/method" event="onSubmit">			
			if(this.validate()) {
				return true;
			} else {
				return false;
			}
        </script>
		';
		$str.='<table style="margin: 0 auto; width: 95%;" cellspacing="10">
					    <tr>
							<td>'.$tr->translate("VILLAGE").'</td>
							<td>'.'<input dojoType="dijit.form.ValidationTextBox" required="true" missingMessage="Invalid Module!" class="fullside" id="village_namekh" name="village_namekh" value="" type="text">'.'</td>
						</tr>
						<tr>
							<td>'.'<input dojoType="dijit.form.TextBox" class="fullside" id="province_name" name="province_name" value="" type="hidden">
								<input dojoType="dijit.form.TextBox" id="district_name" name="district_name" value="" type="hidden">
							'.'</td>
							<td>'.'<input dojoType="dijit.form.TextBox" id="commune_name" name="commune_name" value="" type="hidden">'.'</td>
						</tr>
						<tr>
							<td colspan="2" align="center">
											<input type="reset" value="ážŸáŸ†áž¢áž¶áž�" label='.$tr->translate('CLEAR').' dojoType="dijit.form.Button" iconClass="dijitIconClear"/>
											<input type="button" value="save_close" name="save_close" label="'. $tr->translate('SAVECLOSE').'" dojoType="dijit.form.Button" 
												iconClass="dijitEditorIcon dijitEditorIconSave" Onclick="addVillage();"  />
							</td>
						</tr>
					</table>';
		$str.='</form></div>
		</div>';
		return $str;
	}
	
	public function frmPopupclienttype(){
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$frm = new Group_Form_FrmClient();
		$frm = $frm->FrmAddClient();
		Application_Model_Decorator::removeAllDecorator($frm);
		$str='<div class="dijitHidden">
				<div data-dojo-type="dijit.Dialog"  id="frm_clienttype" >
					<form id="form_clienttype" >';
		$str.='<table style="margin: 0 auto; width: 100%;" cellspacing="7">
					<tr>
						<td>'.$tr->translate("DOCUMENT_TYPE").'</td>
						<td>'.$frm->getElement('clienttype_namekh').'</td>
					</tr>
					<tr>
						<td colspan="2" align="center">
						<input type="button" value="Save" label="'.$tr->translate("SAVECLOSE").'" dojoType="dijit.form.Button"
						iconClass="dijitEditorIcon dijitEditorIconSave" onclick="addNewDocumentType();"/>
						</td>
					</tr>
				</table>';
		$str.='</form></div>
			</div>';
		return $str;
	}
	public function frmPopupLoanTye(){
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
	
		$fm = new Other_Form_FrmVeiwType();
		$frm = $fm->FrmViewType();
		Application_Model_Decorator::removeAllDecorator($frm);
	
		$str='<div class="dijitHidden">
		<div data-dojo-type="dijit.Dialog"  id="frm_loantype" >
		<form id="form_loantype" dojoType="dijit.form.Form" method="post" enctype="application/x-www-form-urlencoded">
		<script type="dojo/method" event="onSubmit">
		if(this.validate()) {
		return true;
	} else {
	return false;
	}
	</script>
	';
		$str.='<table style="margin: 0 auto; width: 95%;" cellspacing="10">
		<tr>
		<td nowrap>'.$tr->translate("TITLE").'</td>
		<td>'.$frm->getElement('title_kh').'</td>
		</tr>
		<tr>
		<td colspan="2" align="center">
		<input type="reset" value="'.$tr->translate('CLEAR').'" label='.$tr->translate('CLEAR').' dojoType="dijit.form.Button" iconClass="dijitIconClear"/>
		<input type="button" value="save_close" name="save_close" label="'. $tr->translate('SAVECLOSE').'" dojoType="dijit.form.Button"
		iconClass="dijitEditorIcon dijitEditorIconSave" Onclick="addNewloanType();"  />
		</td>
		</tr>
		</table>';
		$str.='</form></div>
		</div>';
		return $str;
	}
	public function frmPopupindividualclient(){
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$fm = new Group_Form_FrmClient();
		$frms = $fm->FrmAddClient();
		Application_Model_Decorator::removeAllDecorator($frms);
		
		$str="<div class='dijitHidden'>
				<div data-dojo-type='dijit.Dialog'   id='frmpop_client' >
					<form id='addclient' dojoType='dijit.form.Form' method='post' enctype='application/x-www-form-urlencoded'>
		<script type='dojo/method' event='onSubmit'>
		if(this.validate()) {
		  return true;
	    } else {
	    return false;
	    }
	   </script>";
		$str.='<table style="margin: 0 auto; width: 100%;" cellspacing="7">
					<tr>
						<td>'.$tr->translate("NAME_KHMER").'</td>
						<td>'.$frms->getElement('name_kh').'</td>
						<td>'.$tr->translate("NAME_ENG").'</td>
						<td>'.$frms->getElement('name_en').'</td>		
						<td>'.$tr->translate("SEX").'</td>
						<td>'.$frms->getElement('sex').'</td>
					</tr>
					<tr>
						<td>'.$tr->translate("SITU_STATUS").'</td>
						<td>'.$frms->getElement('situ_status').'</td>	
						<td>'.$tr->translate("NATIONAL_ID").'</td>
						<td>'.$frms->getElement('client_d_type').'</td>	
						<td>'.$tr->translate("NUMBER").'</td>
						<td>'.$frms->getElement('national_id').'</td>	
					</tr>
					<tr>
						<td>'.$tr->translate("JOB_TYPE").'</td>
						<td>'.$frms->getElement('job').'</td>	
						<td>'.$tr->translate("PHONE").'</td>
						<td>'.$frms->getElement('phone').'</td>	
						<td>'.$tr->translate("DOB").'</td>
						<td>'.$frms->getElement('dob_client').'</td>	
					</tr>
					<tr>
						<td>'.$tr->translate("PROVINCE").'</td>
						<td>'.$frms->getElement('province').'</td>	
						<td>'.$tr->translate("DISTRICT").'</td>
						<td>'.$frms->getElement('district').'</td>
						<td>'.$frms->getElement('COMMUNE').'</td>
						<td>'.$frms->getElement('commune').'</td>		
					</tr>
					<tr>
						<td>'.$tr->translate('VILLAGE').'</td>
						<td>'.$frms->getElement('village').'</td>	
						<td>'.$tr->translate('STREET').'</td>
						<td>'.$frms->getElement('street').'</td>	
						<td>'.$tr->translate('HOUSE').'</td>
						<td>'.$frms->getElement('house').'</td>	
					</tr>
					<tr>
						<td colspan="6" align="center" colspan="3">
						<input type="button" value="Save" label="Save" dojoType="dijit.form.Button"
						iconClass="dijitEditorIcon dijitEditorIconSave" onclick="addNewindividual();"/>
						</td>
					</tr>
				</table>';
		$str.='</form></div>
			</div>';
		return $str;
	}
	
	public function frmPopupDepartment(){
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$frm = new Payroll_Form_FrmDepartment();
		$frm = $frm->FrmAddDepartment();
		Application_Model_Decorator::removeAllDecorator($frm);
		$str='<div class="dijitHidden">
				<div data-dojo-type="dijit.Dialog"  id="department" >
					<form id="frm_department" >';
		$str.='<table style="margin: 0 auto; width: 100%;" cellspacing="7">
					
					<tr>
						<td>'.$tr->translate("DEPARTMENT").'</td>
						<td>'.$frm->getElement('department_kh').'</td>
					</tr>
					
					<tr>
						<td colspan="2" align="center">
						<input type="button" value="Save" label="Save" dojoType="dijit.form.Button"
						iconClass="dijitEditorIcon dijitEditorIconSave" onclick="addNewDepartment();"/>
						</td>
					</tr>
				</table>';
		$str.='</form></div>
			</div>';
		return $str;
	}
	
	public function frmPopupCallecterallType(){
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$fm = new Callecterall_Form_Frmcallecterall();
		$frm = $fm->Frmcallecterall();
		Application_Model_Decorator::removeAllDecorator($frm);
		$str='<div class="dijitHidden">
				<div data-dojo-type="dijit.Dialog"  id="call_pop" >
					<form id="frm_call_add" dojoType="dijit.form.Form" method="post" enctype="application/x-www-form-urlencoded">
			 ';
		$str.='<table style="margin: 0 auto; width: 100%;" cellspacing="7">
		<tr>
		<td>'.$tr->translate("NAME_KH").'</td>
		<td>'.$frm->getElement('title_kh').'</td>
		</tr>
		<tr>
		<td>'.$tr->translate("NAME_ENG").'</td>
		<td><input type="hidden" id="row_value" value=""/>'.$frm->getElement('title_en').'</td>
		</tr>
		<tr>
			<td>'.$tr->translate("STATUS").'</td>
			<td>'.$frm->getElement('status').'</td>
		</tr>
		<tr>
		<td colspan="2" align="center">
		<input type="button" value="Save" id="save_call" label="'.$tr->translate("SAVECLOSE").'" dojoType="dijit.form.Button"
		iconClass="dijitEditorIcon dijitEditorIconSave" onclick="addCallteral();"/>
		</td>
		</tr>
		</table>';
		$str.='</form></div>
		</div>';
		return $str;
	}
	
	function getFooterReport(){
		$key = new Application_Model_DbTable_DbKeycode();
		$data=$key->getKeyCodeMiniInv(TRUE);
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$str='<table align="center" width="100%">
				<tr style="font-size: 12px;">
					<td style="width:20%;text-align:center;  font-family:'."'Times New Roman'".','."'Khmer OS Muol Light'".'">'.$tr->translate('APPROVED BY').'</td>
					<td></td>
					<td style="width:20%;text-align:center; font-family:'."'Times New Roman'".','."'Khmer OS Muol Light'".'">'.$tr->translate('VERIFYED BY').'</td>
					<td></td>
					<td style="width:20%;text-align:center; font-family:'."'Times New Roman'".','."'Khmer OS Muol Light'".'">'.$tr->translate('PREPARE BY').'</td>
				</tr>';
		$str.='</table>';
		return $str;
	}
	function getOfficailReceiptLoan(){//general
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$key = new Application_Model_DbTable_DbKeycode();
		$data=$key->getKeyCodeMiniInv(TRUE);
		
		$session_user=new Zend_Session_Namespace(SYSTEM_SES);
		$last_name=$session_user->last_name;
		$username = $session_user->first_name;
		$user_id = $session_user->user_id;
		
		$baseurl=Zend_Controller_Front::getInstance()->getBaseUrl();
		$str='
			<style type="text/css">
				
				td.bgBorderColor {
				    height: 5px;
				    border-top: solid 1px #133b84;
				}
				span.lbtitle {
				    min-width: 100px;
				    display: inline-block;
				}
				span.value{ line-height:12px; min-width:80px; display: inline-block; text-align: center; padding: 0 2px;}
				span.border_bt.value {
				    border-bottom: dashed 1px #000; padding: 0 10px;
				}
				span.borderCo.value {
				    border: 1px solid #133b84;
				    min-height: 40px;
				    width: 100%;
				}
				td.lbtitlev {
				    padding-left: 14px;
				}
				span.borderCo.value label {
				    line-height: 40px;
					margin-bottom: 0;
				}
				table tr td{
					color: #133b84 !important;
					padding: 1px;
				}
		         table table tr td ul li{text-align: center;list-style: none;line-height: 20px; }
				 #projectlogo img{
					 max-height:90px;
				 }
			</style>
			    		<table  cellspacing="0" style=" color: #133b84 !important; width:25cm; max-height:14.85cm; white-space:nowrap; font-family:'."'Times New Roman'".','."'Khmer OS Battambang'".'; font-size: 10px; margin: 0 auto;">
							 <tr valign="top">
								<td align="center" colspan="6" valign="top" height="80">
									<table  cellpadding="0" cellspacing="0" style="font-family: :'."'Times New Roman'".','."'Khmer OS Battambang'".';padding:0; width:100%;white-space:nowrap;">
								   		<tr style="line-height:10px;">
								   			<td width="28%" id="projectlogo"><img src="'.$baseurl.'/images/logo.jpg" style="padding-left: 8px; "></td>
								   			<td valign="top">
									   			<ul style="padding-top: 5px; ">
						                			<li style=" line-height: 25px; text-align:center; font-size:18px !important; font-family:'."'Times New Roman'".','."'Khmer OS Muol Light'".'">'.$data['client_company_name'].'</li>
						                			<li style=" line-height: 25px; text-align:center; font-size:17px !important; font-family:'."'Times New Roman'".','."'Khmer OS Muol Light'".'">'.$tr->translate("PAYMENT_RECEIPT").'</li>
						                			<li style=" line-height: 25px; text-align:center; font-size:17px !important;font-family:'."'Times New Roman'".'; font-weight: bold;">PAYMENT RECEIPT</li>
						                		</ul>
							                </td>
							                <td width="28%" valign="bottom">
							                	<ul style="padding-top: 5px;">
						                			<li style="text-align:left; font-size:14px !important; font-family:'."'Times New Roman'".','."'Khmer OS Muol Light'".'"><span class="lbtitle">កាលបរិច្ឆេទ</span> : <span class=" value"><strong class="fonttel" style="font-size:12px;">'.date('d/m/Y H:i:s').'</strong></span></li>
						                			<li style="text-align:left; font-size:14px !important; font-family:'."'Times New Roman'".','."'Khmer OS Muol Light'".'"><span class="lbtitle">បង្កាន់ដៃលេខ</span> : <span class=" value"><label style="color:#f90909" id="lbl_reciept_no"></label></span></li>
						                		</ul>
							                </td>
										</tr>
										<tr>
			                				<td colspan="3">
					                    	</td>
					                   </tr>  
									</table>
								</td>
			    			</tr>
			    			<tr>
				    			<td align="center" colspan="6" valign="top" height="80">
				    				<table  cellpadding="2" style=" line-height: 30px;  font-family: '."'Times New Roman'".','."'Khmer OS Battambang'".' ;font-size:16px !important;white-space:nowrap; width:24cm; margin: 0 auto;">
										<tr class="fontsize">
											<td class="lbtitlev" valign="top" width="17%">សាខា </td>
											<td ><span class="borderCo value" ><label id="lbl_branch_id"></label></span></td>
											<td class="lbtitlev">ប្រាក់ដើមមុនបង់ </td>
											<td >
												<span class="borderCo value" >
													<label style="color:red" id="lbl_priciple_amount"></label>
												</span>
											</td>
											<td class="lbtitlev">ប្រាក់ដើមបានបង់</td>
											<td >
												<span class="borderCo value" >
													<label style="color:red" id="lbl_os_amount"></label>
												</span>
											</td>
					                   </tr>
					                   <tr>
											<td class="lbtitlev">លេខឥណទាន</td>
											<td >
												<span class="borderCo value">
													<strong class="fonttel" ><label  id="lbl_loan_number"></label></strong>
												</span>
											</td>
											<td class="lbtitlev">ចំនួនប្រាក់ត្រូវបង់សរុប</td>
											<td >
												<span class="borderCo value">
													<label style="color:red" id="lbl_total_payment"></label>
												</span>
											</td>
											<td class="lbtitlev">ការប្រាក់បានបង់</td>
											<td >
												<span class="borderCo value">
													<label style="color:red" id="lbl_interest_paid"></label>
												</span>
											</td>
									  </tr>
									  <tr>
											<td class="lbtitlev">ឈ្មោះអតិថិជន</td>
											<td>
												<span class="borderCo value" >
													<label id="lbl_group_member"></label>
												</span>
											</td>
											<td class="lbtitlev">បង់លើកទី</td>
											<td>
												<span class="borderCo value" >
													<label id="lbl_paidTime"></label>
												</span>
											</td>
											
											<td class="lbtitlev">ប្រាក់ពិន័យបានបង់</td>
											<td>
												<span class="borderCo value">
													<label style="color:red" id="lbl_penalty_amount"></label>
												</span>
											</td>
									</tr>
									<tr class="fontsize">
											<td class="lbtitlev">ឈ្មោះមន្រ្តីឥណទាន</td>
											<td >
												<span class="borderCo value" >
													<label id="lbl_co_id"></label>
												</span>
											</td>
											<td class="lbtitlev">ថ្ងៃបានបង់ប្រាក់</td>
											<td >
												<span class="borderCo value">
													<label style="color:red" id="lbl_collect_date"></label>
												</span>
											</td>
											<td class="lbtitlev" style="min-width: 100px;">ប្រាក់បានបង់សរុប </td>
											<td >
												<span class="borderCo value" style="min-width: 100px;font-weight: bold;">
													<label style="color:red" id="lbl_totalpaid"></label>
												</span>
											</td>
																
									</tr>
									<tr>
											<td class="lbtitlev">សម្គាល់</td>
											<td colspan="5">
												<span class="borderCo value" style="  min-height: 85px;">
													<label id="lbl_note"></label>
												</span>
											</td>
									</tr>
									<tr class="fontsize">
										<td colspan="2"></td>
										<td colspan="4" align="center" >
											<table  width="100%" style="text-align:center; font-family: '."'Times New Roman'".','."'Khmer OS Muol Light'".'" >
												<tr>
													<td width="50%">បេឡាករ</td>
													<td width="50%">ហត្ថលេខា និងឈ្មោះអតិថិជន</td>
												</tr>
												<tr>
													<td colspan="2" style="height: 100px;"></td>
												</tr>
												<tr>
													<td><span class=" value" style="min-width: 100px;" id="CurrentUserLBL">'.$last_name.' '.$username.'</span></td>
													<td><span class=" value" style="min-width: 100px;" id="ClientLbl"></span></td>
												</tr>
											</table>
										</td>
									</tr>
									<tr>
										<td colspan="8" class="bgBorderColor"></td>
									</tr>
									<tr style="line-height: 30px;font-size: 16px;">
											<td valign="top">
												<span style="">អាសយដ្ឋាន</span>
											</td>
											<td colspan="5">
												<span style="white-space: pre-line;font-size: 16px;" id="br_address">
												</span>
											</td>
										</tr>
										<tr style="line-height: 30px;font-size: 16px;">
											<td>
												<span >លេខទំនាក់ទំនង</span>
											</td>
											<td colspan="5">
												<span id="branch_tel" style="white-space: pre-line;font-size: 16px;"></span>
											</td>
										</tr>  
				    			</td>
			    			</tr>
			    		</table>
			    	</td>
			    </tr>
			</table>
		';
		
		return $str;
	}
	
	function getOfficailReceiptPawn(){
		
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$key = new Application_Model_DbTable_DbKeycode();
		$data=$key->getKeyCodeMiniInv(TRUE);
		
		$session_user=new Zend_Session_Namespace(SYSTEM_SES);
		$last_name=$session_user->last_name;
		$username = $session_user->first_name;
		$user_id = $session_user->user_id;
		
		$baseurl=Zend_Controller_Front::getInstance()->getBaseUrl();
		
		$str='
			<style type="text/css">
				
				td.bgBorderColor {
				    height: 5px;
				    border-top: solid 1px #133b84;
				}
				span.lbtitle {
				    min-width: 100px;
				    display: inline-block;
				}
				span.value{ line-height:12px; min-width:80px; display: inline-block; text-align: center; padding: 0 2px;}
				span.border_bt.value {
				    border-bottom: dashed 1px #000; padding: 0 10px;
				}
				span.borderCo.value {
				    border: 1px solid #133b84;
				    min-height: 35px;
				    width: 100%;
				}
				td.lbtitlev {
				    padding-left: 14px;
				}
				span.borderCo.value label {
				    line-height: 34px;
				}
				table tr td{
					color: #133b84 !important;    padding: 1px;
				}
		         table table tr td ul li{text-align: center;list-style: none;line-height: 20px;}
			</style>
			<table width="100%">
				<tr>
			    	<td align="center" valign="top">
			    		<table  cellspacing="10" style=" color: #133b84 !important; width:25cm; max-height:14.85cm; white-space:nowrap; font-family:'."'Times New Roman'".','."'Khmer OS Battambang'".'; font-size: 10px; ">
							 <tr valign="top">
								<td align="center" colspan="6" valign="top" height="80">
									<table  cellpadding="0" cellspacing="0" style="font-family: '."'Times New Roman'".','."'Khmer OS Battambang'".';padding:0; width:100%;white-space:nowrap;">
									   		<tr style="line-height:10px;">
									   			<td width="28%" id="projectlogo"><img src="'.$baseurl.'/images/logo.jpg" style="padding-left: 8px;height: 90px;"></td>
									   			<td valign="top">
										   			<ul style="padding-top: 5px; ">
							                			<li style=" line-height: 25px; text-align:center; font-size:18px !important; font-family:'."'Times New Roman'".','."'Khmer OS Muol Light'".'">'.$data['client_company_name'].'</li>
							                			<li style=" line-height: 25px; text-align:center; font-size:17px !important; font-family:'."'Times New Roman'".','."'Khmer OS Muol Light'".'">'.$tr->translate("PAYMENT_RECEIPT").'</li>
							                			<li style=" line-height: 25px; text-align:center; font-size:17px !important;font-family:'."'Times New Roman'".','."'Khmer OS Battambang'".'; font-weight: bold;">PAYMENT RECEIPT</li>
							                		</ul>
								                </td>
								                <td width="28%" valign="bottom">
								                	<ul style="padding-top: 5px;">
							                			<li style="text-align:left; font-size:14px !important; font-family:'."'Times New Roman'".','."'Khmer OS Battambang'".'"><span class="lbtitle">កាលបរិច្ឆេទ</span> : <span class=" value"><strong class="fonttel" style="font-size:12px;">'.date('d/m/Y H:i:s').'</strong></span></li>
							                			<li style="text-align:left; font-size:14px !important; font-family:'."'Times New Roman'".','."'Khmer OS Battambang'".'"><span class="lbtitle">បង្កាន់ដៃលេខ</span> : <span class=" value"><label style="color:#f90909" id="lbl_reciept_no"></label></span></li>
							                		</ul>
								                </td>
											</tr>
											<tr>
				                				<td colspan="3">
						                    	</td>
						                   </tr>  
									</table>
								</td>
							</tr>
							<tr>
					    		<td align="center" colspan="6" valign="top" >
					    			<table  cellpadding="2" style=" line-height: 30px;  font-family: '."'Times New Roman'".','."'Khmer OS Battambang'".' ;font-size:16px !important;white-space:nowrap; width:24cm; margin: 0 auto;">
										<tr class="fontsize">
											<td class="lbtitlev" valign="top" width="17%">សាខា </td>
											<td ><span class="borderCo value" ><label id="lbl_branch_id"></label></span></td>
											<td class="lbtitlev">ចំនួនប្រាក់ត្រូវបង់សរុប</td>
											<td >
												<span class="borderCo value">
													<label style="color:red" id="lbl_total_payment"></label>
												</span>
											</td>
											<td class="lbtitlev">ការប្រាក់បានបង់</td>
											<td >
												<span class="borderCo value">
													<label style="color:red" id="lbl_interest_paid"></label>
												</span>
											</td>
										</tr>
										<tr>
											<td class="lbtitlev">លេខបញ្ចាំ</td>
											<td >
												<span class="borderCo value">
													<strong class="fonttel" ><label  id="lbl_loan_number"></label></strong>
												</span>
											</td>
											<td class="lbtitlev">បង់លើកទី</td>
											<td >
												<span class="borderCo value" >
													<label id="lbl_paidTime"></label>
												</span>
											</td>
											
											<td class="lbtitlev">ប្រាក់ពិន័យបានបង់</td>
											<td >
												<span class="borderCo value">
													<label style="color:red" id="lbl_penalty_amount"></label>
												</span>
											</td>
										</tr>
										<tr>
												<td class="lbtitlev">ឈ្មោះអតិថិជន</td>
												<td >
													<span class="borderCo value" >
														<label id="lbl_group_member"></label>
													</span>
												</td>
												<td class="lbtitlev">ថ្ងៃបានបង់ប្រាក់</td>
												<td >
													<span class="borderCo value">
														<label style="color:red" id="lbl_collect_date"></label>
													</span>
												</td>
												<td class="lbtitlev" style="min-width: 100px;">ប្រាក់បានបង់សរុប </td>
												<td >
													<span class="borderCo value" style="min-width: 100px;font-weight: bold;">
														<label style="color:red" id="lbl_amount_return"></label>
													</span>
												</td>
												
										</tr>
										<tr class="fontsize">
											<td class="lbtitlev">ប្រភេទ​​ទ្រព្យដាក់បញ្ចាំ</td>
											<td colspan="5">
												<span class="borderCo value" >
													<label id="lbl_colleral"></label>
												</span>
											</td>
										</tr>
										<tr >
												<td class="lbtitlev">សម្គាល់</td>
												<td colspan="5">
													<span class="borderCo value">
														<label id="lbl_note"></label>
													</span>
												</td>
												
										</tr >
										<tr class="fontsize">
											<td colspan="2"></td>
											<td colspan="4" align="center" >
												<table  width="100%" style="text-align:center; font-family: '."'Times New Roman'".','."'Khmer OS Muol Light'".'" >
													<tr>
														<td width="50%">បេឡាករ</td>
														<td width="50%">ហត្ថលេខា និងឈ្មោះអតិថិជន</td>
													</tr>
													<tr>
														<td colspan="2" style="height: 110px;"></td>
													</tr>
													<tr>
														<td><span class=" value" style="min-width: 100px;" id="CurrentUserLBL">'.$last_name.' '.$username.'</span></td>
														<td><span class=" value" style="min-width: 100px;" id="ClientLbl"></span></td>
													</tr>
												</table>
											</td>
										</tr>
										<tr>
											<td colspan="8" class="bgBorderColor"></td>
										</tr>
										<tr style="line-height: 30px;font-size: 16px;">
											<td valign="top">
												<span style="">អាសយដ្ឋាន</span>
											</td>
											<td colspan="5">
												<span style="white-space: pre-line;font-size: 16px;" id="br_address">
												</span>
											</td>
										</tr>
										<tr style="line-height: 30px;font-size: 16px;">
											<td>
												<span >លេខទំនាក់ទំនង</span>
											</td>
											<td colspan="5">
												<span style="white-space: pre-line;font-size: 16px;" id="branch_tel"></span>
											</td>
										</tr>
									</table>
					    		</td>
							</tr>
						</table>
			    	</td>
			    </tr>
			</table>
		
		';
		
		return $str;
	}
	
	function getOfficailReceiptSaleInstall(){
	
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$key = new Application_Model_DbTable_DbKeycode();
		$data=$key->getKeyCodeMiniInv(TRUE);
	
		$session_user=new Zend_Session_Namespace(SYSTEM_SES);
		$last_name=$session_user->last_name;
		$username = $session_user->first_name;
		$user_id = $session_user->user_id;
	
		$baseurl=Zend_Controller_Front::getInstance()->getBaseUrl();
	
		$str='
			<style type="text/css">
				
				td.bgBorderColor {
				    height: 5px;
				    border-top: solid 1px #133b84;
				}
				span.lbtitle {
				    min-width: 100px;
				    display: inline-block;
				}
				span.value{ line-height:12px; min-width:80px; display: inline-block; text-align: center; padding: 0 2px;}
				span.border_bt.value {
				    border-bottom: dashed 1px #000; padding: 0 10px;
				}
				span.borderCo.value {
				    border: 1px solid #133b84;
				    min-height: 35px;
				    width: 100%;
				}
				td.lbtitlev {
				    padding-left: 14px;
				}
				span.borderCo.value label {
				    line-height: 34px;
				}
				table tr td{
					color: #133b84 !important; padding: 1px;
				}
		         table table tr td ul li{text-align: center;list-style: none;line-height: 20px; }
			</style>
			<table width="100%">
				<tr>
			    	<td align="center" valign="top">
			    		<table  cellspacing="10" style=" color: #133b84 !important; width:25cm; max-height:14.85cm; white-space:nowrap; font-family:'."'Times New Roman'".','."'Khmer OS Battambang'".'; font-size: 16px; ">
							 <tr valign="top">
								<td align="center" colspan="6" valign="top" height="80">
									<table  cellpadding="0" cellspacing="0" style="font-family: '."'Times New Roman'".','."'Khmer OS Battambang'".';padding:0; width:100%;white-space:nowrap;">
								   		<tr style="line-height:10px;">
								   			<td width="28%" id="projectlogo"><img src="'.$baseurl.'/images/logo.jpg" style="padding-left: 8px;height: 90px;"></td>
								   			<td valign="top">
									   			<ul style="padding-top: 5px; ">
						                			<li style=" line-height: 30px; text-align:center; font-size:18px !important; font-family:'."'Times New Roman'".','."'Khmer OS Muol Light'".'" id="branch_title_company">'.$data['client_company_name'].'</li>
						                			<li style=" line-height: 30px; text-align:center; font-size:17px !important; font-family:'."'Times New Roman'".','."'Khmer OS Muol Light'".'">'.$tr->translate("PAYMENT_RECEIPT").'</li>
						                			<li style=" line-height: 30px; text-align:center; font-size:17px !important;font-family:'."'Times New Roman'".','."'Khmer OS Battambang'".'; font-weight: bold;">PAYMENT RECEIPT</li>
						                		</ul>
							                </td>
							                <td width="28%" valign="bottom">
							                	<ul style="padding-top: 5px;">
						                			<li style="text-align:left; font-size:14px !important; font-family:'."'Times New Roman'".','."'Khmer OS Battambang'".'"><span class="lbtitle">កាលបរិច្ឆេទ</span> : <span class=" value"><strong class="fonttel" style="font-size:12px;">'.date('d/m/Y H:i:s').'</strong></span></li>
						                			<li style="text-align:left; font-size:14px !important; font-family:'."'Times New Roman'".','."'Khmer OS Battambang'".'"><span class="lbtitle">បង្កាន់ដៃលេខ</span> : <span class=" value"><label style="color:#f90909" id="lbl_reciept_no"></label></span></li>
						                		</ul>
							                </td>
										</tr>
										<tr>
			                				<td colspan="3">
					                    	</td>
					                   </tr>  
									</table>
								</td>
						    </tr>
						    <tr>
				    			<td align="center" colspan="6" valign="top">
				    				<table  cellpadding="2" style=" line-height: 30px;  font-family: '."'Times New Roman'".','."'Khmer OS Battambang'".' ;font-size:18px !important;white-space:nowrap; width:24cm; margin: 0 auto;">
										<tr class="fontsize">
											<td class="lbtitlev" valign="top" width="17%">សាខា </td>
											<td ><span class="borderCo value" ><label id="lbl_branch_id"></label></span></td>
											
											<td class="lbtitlev">ចំនួនប្រាក់ត្រូវបង់សរុប</td>
											<td >
												<span class="borderCo value">
													<label style="color:red" id="lbl_total_payment"></label>
												</span>
											</td>
											<td class="lbtitlev">ការប្រាក់បានបង់</td>
											<td >
												<span class="borderCo value">
													<label style="color:red" id="lbl_interest_paid"></label>
												</span>
											</td>
										</tr>
										<tr>
											<td class="lbtitlev">លេខរំលស់</td>
											<td >
												<span class="borderCo value">
													<strong class="fonttel" ><label  id="lbl_loan_number"></label></strong>
												</span>
											</td>
											<td class="lbtitlev">បង់លើកទី</td>
											<td >
												<span class="borderCo value" >
													<label id="lbl_paidTime"></label>
												</span>
											</td>
											<td class="lbtitlev">ប្រាក់ពិន័យបានបង់</td>
											<td >
												<span class="borderCo value">
													<label style="color:red" id="lbl_penalty_amount"></label>
												</span>
											</td>
										</tr>
										<tr>
											<td class="lbtitlev">ឈ្មោះអតិថិជន</td>
											<td >
												<span class="borderCo value" >
													<label id="lbl_group_member"></label>
												</span>
											</td>
											<td class="lbtitlev">ថ្ងៃបានបង់ប្រាក់</td>
											<td >
												<span class="borderCo value">
													<label style="color:red" id="lbl_collect_date"></label>
												</span>
											</td>
											<td class="lbtitlev" style="min-width: 100px;font-weight: bold;">ប្រាក់បានបង់សរុប </td>
											<td >
												<span class="borderCo value" style="min-width: 100px;font-weight: bold;">
													<label style="color:red" id="lbl_total_paymentpaid"></label>
												</span>
											</td>
										</tr>
										<tr >
										<td class="lbtitlev">សម្គាល់</td>
											<td colspan="5" >
												<span class="borderCo value" style="min-height: 70px;">
													<label id="lbl_note"></label>
												</span>
											</td>
											
										</tr>
										<tr class="fontsize">
											<td colspan="2"></td>
											<td colspan="4" align="center" >
												<table  width="100%" style="text-align:center; font-family: '."'Times New Roman'".','."'Khmer OS Muol Light'".'" >
													<tr>
														<td width="50%">បេឡាករ</td>
														<td width="50%">ហត្ថលេខា និងឈ្មោះអតិថិជន</td>
													</tr>
													<tr>
														<td colspan="2" style="height: 100px;"></td>
													</tr>
													<tr>
														<td><span class=" value" style="min-width: 100px;" id="CurrentUserLBL">'.$last_name.' '.$username.'</span></td>
														<td><span class=" value" style="min-width: 100px;" id="ClientLbl"></span></td>
													</tr>
												</table>
											</td>
										</tr>
										<tr>
											<td colspan="8" class=""></td>
										</tr>
										<tr style="line-height: 20px;font-size: 14px;">
											<td valign="top">
												<span style="text-decoration:underline;font-size: 14px;"></span>
											</td>
											<td colspan="5">
												<span style="white-space: pre-line;font-size: 14px;"></span>
											</td>
										</tr>
										<tr style="line-height: 30px;font-size: 16px;">
											<td colspan="6" style="border-top:1px solid #000;">
											</td>
										</tr>
										<tr style="line-height: 30px;font-size: 16px;">
											<td valign="top">
												<span style="">អាសយដ្ឋាន</span>
											</td>
											<td colspan="5">
												<span style="white-space: pre-line;font-size: 16px;" id="br_address">
												</span>
											</td>
										</tr>
										<tr style="line-height: 30px;font-size: 16px;">
											<td>
												<span >លេខទំនាក់ទំនង</span>
											</td>
											<td colspan="5">
												<span style="white-space: pre-line;font-size: 16px;" id="branch_tel"></span>
											</td>
										</tr>
									</table>
				    			</td>
						    </tr>
						</table> 
			    	</td>
			    </tr>
			</table> 
		';
		
		return $str;
	}
	
}

