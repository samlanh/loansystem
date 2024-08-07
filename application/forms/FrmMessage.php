<?php

class Application_Form_FrmMessage extends Zend_Form
{

	
    public function init()
    {
        /* Form Elements & Other Definitions Here ... */
    	
    }
	
	public static function Sucessfull($msg,$url,$typeMessage=1)
	{
		$tr= Application_Form_FrmLanguages::getCurrentlanguage();
		$msg = $tr->translate($msg);
		$msg = preg_replace( "/\r|\n/", "", addslashes(strip_tags(htmlspecialchars($msg))));
		
		$classMessage="success";//1=Success Message,2=Infomation Message
		if($typeMessage!=1){
			$classMessage="info";
		}
		
		$stringScript ="<script language='javascript'>";
		if($typeMessage!=1){
			$signConnect = "?";
			if(strpos($url, "?")){
				$signConnect = "&";
			}
			$stringScript.="
				window.location.href = '".Zend_Controller_Front::getInstance()->getBaseUrl().$url.$signConnect."alertmg=true&messsageTitle=$msg&classMessage=$classMessage';
			";
			//redirect first and show message later
		}else{
			$stringScript.="
				Swal.fire({
				  icon: '".$classMessage."',
				  title: '".$msg."',
				  showConfirmButton: false,
				  timer: 1000,
				  timerProgressBar: true,
				  didOpen: (toast) => {
					toast.addEventListener('mouseenter', Swal.stopTimer)
					toast.addEventListener('mouseleave', Swal.resumeTimer)
				  }
				}).then((result) => {
				  window.location.href = '".Zend_Controller_Front::getInstance()->getBaseUrl().$url."';
				})
			";
		}
		$stringScript.="</script>";
		echo $stringScript;
	}
	
	public static function message($msg)
	{
		$tr= Application_Form_FrmLanguages::getCurrentlanguage();
		$msg = $tr->translate($msg);
		$msg = preg_replace( "/\r|\n/", "", addslashes(strip_tags(htmlspecialchars($msg))));
		$stringScript ="<script language='javascript'>";
		$stringScript.="
				Swal.fire({
				  icon: 'error',
				  title: '".$msg."',
				  showConfirmButton: false,
				  timer: 1000,
				  timerProgressBar: true,
				  didOpen: (toast) => {
					toast.addEventListener('mouseenter', Swal.stopTimer)
					toast.addEventListener('mouseleave', Swal.resumeTimer)
				  }
				})
		";
		$stringScript.="</script>";
		echo $stringScript;
	}
	
	public static function redirectUrl($url)
	{
		echo '<script language="javascript">										           
					window.location = "'.Zend_Controller_Front::getInstance()->getBaseUrl().$url.'";					
			 </script>';
	}
	
	public static function getUrl($url){
		return Zend_Controller_Front::getInstance()->getBaseUrl().$url;
	}
	
	public static function redirectorview($url)
	{
		$baseUrl=Zend_Controller_Front::getInstance()->getBaseUrl();
    	$uri='';
    	foreach($url as $key=>$value)
    		$uri.='/'.$value;
    	return $baseUrl.$uri;
	}
	
	public static function redirector($url){
		$base_url=Zend_Controller_Front::getInstance()->getBaseUrl();
		$siteurl=$base_url.$url;
		echo '<script language="javascript">
		         window.location.href="'.$siteurl.'";
		      </script>';
	}
	
	public static function  getBrowser(){ 
	    $u_agent = $_SERVER['HTTP_USER_AGENT']; 
	    $bname = 'Unknown';
	    $platform = 'Unknown';
	    $version= "";
	
	    //First get the platform?
	    if (preg_match('/linux/i', $u_agent)) {
	        $platform = 'linux';
	    }
	    elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
	        $platform = 'mac';
	    }
	    elseif (preg_match('/windows|win32/i', $u_agent)) {
	        $platform = 'windows';
	    }
	    
	    // Next get the name of the useragent yes seperately and for good reason
	    if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) 
	    { 
	        $bname = 'Internet Explorer'; 
	        $ub = "MSIE"; 
	    } 
	    elseif(preg_match('/Firefox/i',$u_agent)) 
	    { 
	        $bname = 'Mozilla Firefox'; 
	        $ub = "Firefox"; 
	    } 
	    elseif(preg_match('/Chrome/i',$u_agent)) 
	    { 
	        $bname = 'Google Chrome'; 
	        $ub = "Chrome"; 
	    } 
	    elseif(preg_match('/Safari/i',$u_agent)) 
	    { 
	        $bname = 'Apple Safari'; 
	        $ub = "Safari"; 
	    } 
	    elseif(preg_match('/Opera/i',$u_agent)) 
	    { 
	        $bname = 'Opera'; 
	        $ub = "Opera"; 
	    } 
	    elseif(preg_match('/Netscape/i',$u_agent)) 
	    { 
	        $bname = 'Netscape'; 
	        $ub = "Netscape"; 
	    } 
	    
	    // finally get the correct version number
	    $known = array('Version', $ub, 'other');
	    $pattern = '#(?<browser>' . join('|', $known) .
	    ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
	    if (!preg_match_all($pattern, $u_agent, $matches)) {
	        // we have no matching number just continue
	    }
	    
	    // see how many we have
	    $i = count($matches['browser']);
	    if ($i != 1) {
	        //we will have two since we are not using 'other' argument yet
	        //see if version is before or after the name
	        if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
	            $version= $matches['version'][0];
	        }
	        else {
	            $version= $matches['version'][1];
	        }
	    }
	    else {
	        $version= $matches['version'][0];
	    }
	    
	    // check if we have a number
	    if ($version==null || $version=="") {$version="?";}
	    
	    return array(
	        'userAgent' => $u_agent,
	        'name'      => $bname,
	        'version'   => $version,
	        'platform'  => $platform,
	        'pattern'    => $pattern
	    );
	} 
}

