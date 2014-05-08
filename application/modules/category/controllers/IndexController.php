<?php
/**
 * Remember you have    
 *  public $dao;
 *  public $node, $nodeUC; //node name : foo|post|item...

 * @author tran
 */ 
class Category_IndexController extends Cl_Controller_Action_NodeIndex 
{
    public function init()
    {
        //$this->daoClass = "Cl_Dao_Node_Category";
        //$this->commentDaoClass = "Cl_Dao_Comment_Category";
        
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
        $this->genericNew("Category_Form_New", "Dao_Node_Category", "Node");
        
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
                	$this->ajaxData['data'] = array('url' => node_link('category' , $this->ajaxData['result']));
                }
            }
        }
        Bootstrap::$pageTitle = 'Tạo chuyên mục mới';
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
        $this->genericUpdate("Category_Form_Update", $this->daoClass ,"", "Node");
        Bootstrap::$pageTitle = 'Cập nhật chuyên mục';
    }

    public function searchAction()
    {
    	assure_perm('sudo');
    	$this->setLayout("admin");
        $this->genericSearch("Category_Form_Search", $this->daoClass, "Node");
        Bootstrap::$pageTitle = 'Quản lý chuyên mục';        
    }
    
    public function searchCommentAction()
    {
        assure_perm("search_category");//by default
        $commentClass =$this->commentDaoClass;
        $this->genericSearch("Category_Form_SearchComment", $commentClass, "");
        Bootstrap::$pageTitle = "Search " . $this->nodeUC . " Comments";        
    }
    
    public function viewAction()
    {
   		 /***
    	 * Check category is level one?
    	 * If category level 1
    	 * 	  Show child categories
    	 * Else
    	 * 	  Show all product of category 
    	 */

    	$iid = $this->getStrippedParam('iid');
    	$slug = $this->getStrippedParam('slug');
    	$where = array('iid' => $iid);
    	$r = Dao_Node_Category::getInstance()->findOne($where);
    	if($r['success']){
    	   $cate_name = $r['result']['name'];
    		$cate = $r['result'];
			if(!isset($cate['level']))
    		    $cate['level'] = 1;
    		if($cate['level'] == 2){
    			//Show all product of category
    			$where = array('parent_category_iid' => $cate['iid']);
    			//$where = array();
    			$cond['where'] = $where;
    			$r = Dao_Node_Product::getInstance()->findAll($cond);
    			if($r['success']){
    				$products = $r['result'];
    				$this->setViewParam('products', $products);
    				$this->setViewParam('row', $cate);
    			}
    			
    			$this->setViewParam('is_level', 2);
    		}elseif($cate['level'] == 1){
    			//show child categories
    			$child_cate = isset($cate['child_category']) ? $cate['child_category'] : array();
    			$categories = array();
    			if(count($child_cate) > 0){
    				foreach ($child_cate as $ca){
    					$where = array('parent_category_iid' => $ca['iid']);
    					//$where = array();
    					$cond['where'] = $where;
    					$cond['limit'] = 3;
    					$r = Dao_Node_Product::getInstance()->findAll($cond);
    					if($r['success'])
    					   $cateNew['products'] = $r['result'];
    					$cateNew['detail'] = $ca;
    					$categories[] = $cateNew;
    				}
    			}
    			
    			$iid = get_conf('home_page_product_iid', 1);
    			$hp_product = Dao_Node_Product::getInstance()->getHomePageProduct($iid);
    			 
    			$this->setViewParam('hp_product', $hp_product);
    			
    			//TODO:: Get product was recommended
    			$this->setViewParam('categories', $categories);
    			$this->setViewParam('row', $cate);
    		    $this->_helper->viewRenderer->setNoRender(true);
    			echo $this->view->render('index/categories.phtml');
    			$this->setViewParam('is_level', 1);
    		}
    	}

        //TODO Your permission here
        //parent::viewAction();//no permission yet
        Bootstrap::$pageTitle = 'Chuyên mục - ' . $cate_name;
    }
    
    public function deleteNodePermissionCheck($row)
    {
        if (has_perm("delete_category"))
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
    	return has_perm("new_category_comment");
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
    	assure_role('admin_category');
    	$ids = $this->getStrippedParam('ids');
    	$in = explode(',', $ids);
    	$where = array('id' => array('$in' => $in));
    	Dao_Node_Category::getInstance()->delete($where);
    	$r = array('success' => true);
    	$this->handleAjaxOrMaster($r);
    }
    
}

