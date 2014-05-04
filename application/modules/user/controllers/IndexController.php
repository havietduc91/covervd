<?php 
class User_IndexController extends Cl_Controller_Action_UserIndex
{
	public function init()
	{
		parent::init();
	}
	
	public function loginAction()
	{
	    parent::loginAction();
	    if (Zend_Registry::isRegistered('authentication_happened'))
	    {
	    	//set one more cookie. _cl_is_admin
	    	$user = Zend_Registry::get('user');
	    	if (has_perm('admin_story'))
	    		set_cookie('is_admin', 1);
	    }
	}
	
	public function logoutAction()
	{
        //clear identity & reset cookie
        $adapter = new Cl_Auth_Adapter_PersistentDb();
        $r = $adapter->clearIdentity();
        $r = array('success' => true);

        set_cookie('is_admin','', -3600);
                
        if(is_ajax()) {
            send_json($r);
        }
        else 
        {
            if(!$r['success']){
                // return error;
                $this->setViewParam('err', $r['err']);
            }
            else {
                // redirect to homepage here?
                $this->_redirect("/");
            }
        }
	    
	}
	public function viewAction()
	{
		$lname = $this->getStrippedParam('lname', '');
		
		if ($lname != '')
		{
			$where = array('lname' => $lname);
    		$r = Dao_User::getInstance()->findOne($where);
			
			if ($r['success'] && $r['count'] != 0)
			{
	            //redirect
	            $url = "/user/" . $r['result']['iid'];
	            header("Location: $url");
	            exit();
			}
		}
		else 
			$this->_redirect("/");
	}
	
	public function newAction()
	{
		assure_perm ( 'new_user' );
		$r = $this->genericNew ( "User_Form_New", "Dao_User", "User" );
		
		if($r['success'] && !isset($r['code']))
		{
			$this->ajaxData ['callback'] = 'alert';
			$this->ajaxData['duration'] = 500;
		}
		else {
			$r = array('success' => false, 'err' => 'User name or Email already exits', 'display_err' => 1);
			$this->handleAjaxOrMaster($r);
			return;
		}
		Bootstrap::$pageTitle = t('add_new_user', 2);
	}
	
	public function anyOtherRequestAction()
	{
		
	}    
	
	public function relateAction()
	{
		if(is_guest()){
			$this->_redirect('/user/login');
		}
		parent::relateAction();
	}
	
	public function searchRolesAction()
	{
		parent::searchRolesAction(); //which checks for permission
		$this->setLayout('admin');
	}
	
	public function newRoleAction()
	{
		parent::newRoleAction(); //which checks for permission
		$this->setLayout('admin');
	}
	
	public function registerAction()
	{
		parent::registerAction();
		if (!is_ajax() || is_modal_ajax())
		{
			//$this->setViewParam('')
			echo $this->render('login');
		}
		Bootstrap::$pageTitle = t('login_signup', 2);
	}
	public function updateFakeAction()
	{
		assure_perm('admin_user');
		$r = Dao_User::getInstance()-> UpdateFakeFiledToAllUsers();
		if($r['success'])
			die('OK');
		else
			die('error while updating DB');
	}
}
