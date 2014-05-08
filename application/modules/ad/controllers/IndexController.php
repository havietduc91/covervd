<?php
/**
 * Remember you have    
 *  public $dao;
 *  public $node, $nodeUC; //node name : foo|post|item...

 * @author tran
 */ 
class Ad_IndexController extends Cl_Controller_Action_NodeIndex 
{
    public function init()
    {
        //$this->daoClass = "Cl_Dao_Node_Ad";
        //$this->commentDaoClass = "Cl_Dao_Comment_Ad";
        
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

    public function newAction()
    {
    	assure_perm('sudo');
    	$this->setLayout("admin");
        $this->genericNew("Ad_Form_New", "Dao_Node_Ad", "Node");
        
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
                	$this->ajaxData['data'] = array('url' => node_link('ad' , $this->ajaxData['result']));
                }
            }
        }
        Bootstrap::$pageTitle = 'Tạo quảng cáo mới';
    }

    public function updateAction()
    {
        /**
         * Permission to update a node is done in 
         * $Node_Form_Update form->customPermissionFilter()
         * Do not do it here
         * @NOTE: object is already filtered in Index.php, done in Cl_Dao_Node::filterUpdatedObjectForAjax()
         */
    	assure_perm('sudo');
    	$this->setLayout("admin");
        $this->genericUpdate("Ad_Form_Update", $this->daoClass ,"", "Node");
        Bootstrap::$pageTitle = 'Cập nhật quảng cáo';
    }

    public function searchAction()
    {
        assure_perm('sudo');
    	$this->setLayout("admin");
        $this->genericSearch("Ad_Form_Search", $this->daoClass, "Node");
        Bootstrap::$pageTitle = 'Quản lý quảng cáo';        
    }
    
    public function searchCommentAction()
    {
        assure_perm("search_ad");//by default
        $commentClass =$this->commentDaoClass;
        $this->genericSearch("Ad_Form_SearchComment", $commentClass, "");
        Bootstrap::$pageTitle = "Search " . $this->nodeUC . " Comments";        
    }
    
    public function viewAction()
    {
    	assure_perm('sudo');
    	$this->setLayout("admin");
        //TODO Your permission here
        parent::viewAction();//no permission yet

        Bootstrap::$pageTitle = 'Chi tiết quảng cáo';
    }
    
    public function deleteNodePermissionCheck($row)
    {
        if (has_perm("delete_ad"))
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
    	return has_perm("new_ad_comment");
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
    	assure_role('admin_ad');
    	$ids = $this->getStrippedParam('ids');
    	$in = explode(',', $ids);
    	$where = array('id' => array('$in' => $in));
    	Dao_Node_Ad::getInstance()->delete($where);
    	$r = array('success' => true);
    	$this->handleAjaxOrMaster($r);
    }
    
}

