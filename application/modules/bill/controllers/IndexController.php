<?php
/**
 * Remember you have    
 *  public $dao;
 *  public $node, $nodeUC; //node name : foo|post|item...

 * @author tran
 */ 
class Bill_IndexController extends Cl_Controller_Action_NodeIndex 
{
    public function init()
    {
        //$this->daoClass = "Cl_Dao_Node_Bill";
        //$this->commentDaoClass = "Cl_Dao_Comment_Bill";
        
        /**
         * Chances to check for permission here if you like
         */
        parent::init();
        /**
         * Chances to change layout if you like
         */
    }

    public function indexAction()
    {

    }
    
    public function buyAction()
    {
    	$productIid = $this->getStrippedParam('iid');
    	$where = array('iid'=>$productIid);
    	$r = Dao_Node_Product::getInstance()->findOne($where);
    	$lu = Zend_Registry::get('user');
    	if($r['success']){
    		$product = $r['result'];
    	}else 
    		$product = array();
    	
    	//TODO: populate value to form
    	$this->setViewParam('product', $product);
    	
    	$form = new Bill_Form_New();
    	//TODO:: populate product'fields into form
    	$data = array(
    			'uname' => $lu['name'],
    	);
    	 
    	$r = $form->build($data);
    	 
    	//$conditions = $form->buildSearchConditions();
    	 
    	$this->genericNew("Bill_Form_New", "Dao_Node_Bill", "Node");
    	
    	if(isset($this->ajaxData)) {
    		//TODO: redirect to my cart
    	}
    	Bootstrap::$pageTitle = 'Tạo hóa đơn mua hàng';
    }

    public function newAction()
    {
    	assure_perm('sudo');
    	$this->setLayout("admin");
        $this->genericNew("Bill_Form_New", "Dao_Node_Bill", "Node");
        
        if(isset($this->ajaxData)) {
            //command the form view to rediect if success
            if (isset($this->ajaxData['result'])) //success
            {
                if (is_preview())
                {
                    $this->setViewParam('row', $this->ajaxData['result']);
                    $this->setViewParam('is_preview', 1);
                    $this->_helper->viewRenderer->setNoRender(true);
                    $ret['data'] = $this->view->render('index/view.phtml');
                    $ret['success'] = true;
                    $ret['callback'] = 'populate_preview';
                    send_json($ret);
                    exit();
                }
                else 
                {
                	$this->ajaxData['callback'] = 'redirect';
                	$this->ajaxData['data'] = array('url' => node_link('bill' , $this->ajaxData['result']));
                }
            }
        }
        Bootstrap::$pageTitle = 'Tạo đơn hàng mới';
    }

    public function updateAction()
    {
    	assure_perm('sudo');
    	$this->setLayout("admin");
        /**
         * Permission to update a node is done in 
         * $Node_Form_Update form->customPermissionFilter()
         * Do not do it here
         * @NOTE: object is already filtered in Index.php, done in Cl_Dao_Node::filterUpdatedObjectForAjax()
         */
        $this->genericUpdate("Bill_Form_Update", $this->daoClass ,"", "Node");
        Bootstrap::$pageTitle = 'Cập nhật đơn hàng';
    }

    public function searchAction()
    {
    	assure_perm('sudo');
    	$this->setLayout("admin");
        //assure_perm("search_bill");//by default
        $this->genericSearch("Bill_Form_Search", $this->daoClass, "Node");
        Bootstrap::$pageTitle = 'Tìm kiếm đơn hàng';        
    }
    
    public function searchCartsAction()
    {
    	assure_perm('sudo');
    	$status = get_value('status','queued');
    	$form = new Bill_Form_Search();
    	 
    	$data = array(
    			'status' => array('$in' => $status),
    	);
    	 
    	$form->build($data);
    	
    	$conditions = $form->buildSearchConditions();
    	$conditions['total'] = 1;
    	 
    	$dao = Dao_Node_Bill::getInstance();
    	$r = $dao->findNode($conditions, true);
    	if($r['success'] && $r['total'] > 0){
    		$products = $r['result'];
    	}else{
    		$products = array();
    	}
    	
    	$this->setViewParam('status', $status);
    	$this->setViewParam('list', $products);
    	
    	Bootstrap::$pageTitle = 'Quản lý sản phẩm';
    }
    
    public function searchCommentAction()
    {
        assure_perm("search_bill");//by default
        $commentClass =$this->commentDaoClass;
        $this->genericSearch("Bill_Form_SearchComment", $commentClass, "");
        Bootstrap::$pageTitle = "Search " . $this->nodeUC . " Comments";        
    }
    
    public function viewAction()
    {
        //TODO Your permission here
        parent::viewAction();//no permission yet
        Bootstrap::$pageTitle = 'Xem đơn hàng';
    }
    
    public function deleteNodePermissionCheck($row)
    {
        if (has_perm("delete_bill"))
            return array('success' => true);
        else 
            return array('success' => false);
    }
    public function commentAction(){
    	//$this->commentScript = "index/one-comment.phtml";
    	parent::commentAction();
    }
    
    //implements parent::newCommentPermissionCheck
    public function newCommentPermissionCheck($row)
    {
    	//TODO: Implement this
    	return has_perm("new_bill_comment");
    }
    public function updateCommentAction()
    {
    	//$this->commentContentScript = "index/one-comment-content.phtml";
    	parent::updateCommentAction();
    }
    
    public function delCommentAction()
    {
    	parent::delCommentAction();
    }
    
    public function bulkDeleteAction()
    {
    	assure_role('admin_bill');
    	$ids = $this->getStrippedParam('ids');
    	$in = explode(',', $ids);
    	$where = array('id' => array('$in' => $in));
    	Dao_Node_Bill::getInstance()->delete($where);
    	$r = array('success' => true);
    	$this->handleAjaxOrMaster($r);
    }
    
}

