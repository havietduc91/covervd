<?php
/**
 * See Mail
 * @author steve
 *
 */
class Dao_Mail extends Cl_Mail
{
    /**
     * 
     * @param ActivityType $type
     * @param FromEmail $from
     * @param ToEmail $to
     * @param Object $params : passing to view script
     * @param Language $language
     * @return multitype:boolean
     */
    public function parseAndSendMail($type, $from, $to, $params, $language = '')
    {
    	$view = new Zend_View();
    	//$view->setScriptPath(APPLICATION_PATH .'/views/scripts/');
    	$mailScriptPath = APPLICATION_PATH .'/modules/site/views/scripts/mail/';
    	if ($language != '')
    	{
    		$mailScriptPath = $mailScriptPath . "$language/";
    	}
    	
    	$view->setScriptPath($mailScriptPath);
    	
    	if ($type == 'contact')
    	{
    		$view->from = $from;
    		$view->name = $params['name'];
    		$view->body = $params['body'];
    		$subject = "[" . str_replace("www.", '', DOMAIN) ."][contact] "  . $params['name'];
    	}
    	elseif ($type == 'mail_verification')
    	{
    		$subject = t("email_verification", 1, $language);
    		$view->params = $params;
    		$view->to = $to;
    	}
    	elseif($type == 'mail_activation')
    	{
    		$subject = t("account_activation",1, $language);
    		$view->params = $params;
    		$view->to = $to;
    	}
    	elseif ($type == 'suspend_user')
    	{
    		$subject = "[" . str_replace("www.", '', DOMAIN)  . "][Important] Your account have been suspended";
    		$view->params = $params;
    		$view->to = $to;
    	}
    	elseif($type == 'invitation') {
    		$subject = "Invitation to join a company at eekip.com";
    		$view->params = $params;
    		$view->to = $to;
    	}
    	elseif ($type == 'forgot-password')
    	{
    		$subject = t("forgot_password", 1,$language);//
    		$view->params = $params;
    		$view->to = $to;
    	}
   		 
    	$view->mail_template = $type;
    	try
    	{
    		$body = $view->render("template.phtml");
    	}
    	catch (Exception $e)
    	{
    		v($e->getMessage());
    	}
    	
    	Cl_Mail::getInstance()->sendMail($to, $subject, $body, $from);
    	//$headers = 'Content-type: text/html; charset=utf8' . "\r\n";
    	//mail($to, $subject, $body, $headers);
    	//append_to_file("sned mail to $to"  );
    	return array('success' => true);
    }	
}
