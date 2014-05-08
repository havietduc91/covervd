<?php 
class User_IndexController extends Cl_Controller_Action_UserIndex
{
	public function init()
	{
		parent::init();
	}
	
	public function viewAction()
	{
		parent::viewAction();	
	}
	
	public function anyOtherRequestAction()
	{
		
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
		
		Bootstrap::$pageTitle = 'Đăng nhập';
	}
	
	public function registerAction()
	{
		parent::registerAction();
		Bootstrap::$pageTitle = 'Đăng ký';
	}
	
	public function logoutAction(){
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
	
	public function searchAction(){
		parent::searchAction();
		
		//$this->render('login');
		Bootstrap::$pageTitle = 'Quản lý người dùng';
		//die('oki');
	}
	
	public function cartsAction(){
		//TODO: Lay ra danh sach cac don hang cua nguoi dung
		if(is_guest()){
			//TODO:: redirect login
		}else{
			$lu = Zend_Registry::get('user');
			$where = array('umail' => $lu['mail']);

			$cond['where'] = $where;
			$r = Dao_Node_Bill::getInstance()->findAll($cond);
			if($r['success'] && count($r['total'])){
				$this->setViewParam('list', $r['result']);
			}else{
				$this->setViewParam('list', array());
			}
		}
		
		Bootstrap::$pageTitle = 'Đơn hàng của bạn';
	}
	
	public function updateAction(){
		parent::updateAction();
		Bootstrap::$pageTitle = 'Cập nhật tài khoản';
	}
}
