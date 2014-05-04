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
         * Chances to check fr permission here if you like
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
                	$this->ajaxData['data'] = array('url' => '/ad/' . $this->ajaxData['result']['id']);
                	//OR go to search : $this->ajaxData['data'] = array('url' => '/ad/search');
                }
            }
        }
        Bootstrap::$pageTitle = "New " . $this->nodeUC;
    }

    public function updateAction()
    {
        /**
         * Permission to update a node is done in 
         * $Node_Form_Update form->customPermissionFilter()
         * Do not do it here
         * @NOTE: object is already filtered in Index.php, done in Cl_Dao_Node::filterUpdatedObjectForAjax()
         */
        $this->genericUpdate("Ad_Form_Update", $this->daoClass ,"", "Node");
        Bootstrap::$pageTitle = "Update " . $this->nodeUC;
    }

    public function searchAction()
    {
        assure_perm("search_ad");//by default
        $this->genericSearch("Ad_Form_Search", $this->daoClass, "Node");
        $this->setLayout("admin");
        Bootstrap::$pageTitle = "Search " . $this->nodeUC;        
    }
    
    public function searchCommentAction()
    {
        assure_perm("search_ad");//by default
        $commentClass =$this->commentDaoClass;
        $this->genericSearch("Ad_Form_SearchComment", $commentClass, "");
        $this->setLayout("admin");
        Bootstrap::$pageTitle = "Search " . $this->nodeUC . " Comments";        
    }
    
    public function viewAction()
    {
        //TODO Your permission here
        parent::viewAction();//no permission yet
        if ($row = $this->getViewParam('row'))
        {
            $id = $this->getStrippedParam('id');
            $where = array('node.id' => $id);
            $commentClass =$this->commentDaoClass;
            $r = $commentClass::getInstance()->findAll(array('where' => $where));
            if ($r['success'] && $r['count'] > 0)
            {
                $comments = $this->dao->generateCommentTree($r['result'], 0);
                //Construct comment trees here
                $this->setViewParam('comments', $comments);
            }
            if(is_rest()) {
                if ($r['success'] && $r['count'] > 0)
                {
                    $row['comments'] = $comments;
    	            $r = array('success' => true, 'result' => $row);
                }
    	        $this->handleAjaxOrMaster($r);
            }
        }        
        Bootstrap::$pageTitle = "View " . $this->nodeUC;
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
    
    //====================================ATTACHMENTS=============================
    public function downloadAttachmentAction()
    {
		assure_login(true);
		$tid = $this->getStrippedParam('tid');
		$id = $this->getStrippedParam('id'); //file id
		$all = $this->getStrippedParam('all',null);
		$r = $this->dao->findOne(array('id' => $tid));
		if($r['success'] && $r['count'] > 0) {
		    $task = $r['result'];
		    //check if in participants list
		    $allowed = false;
		    foreach ($task['participants'] as $pp)
		    {
		        if ($pp['id'] == $this->_u['id'])
		        {
		            $allowed = true;
		            break;
		        }
		    }
		    if (!$allowed)
		    {
		        go_to_error("You are not participating in the task");
		    }
		}	
		else 
		{
			go_to_error(_("task_not_found"));
		}
			
		if($all) {
			$fileName = "task_" . $tid . "_attachments.zip";
			$fullPath = FILES_UPLOAD_PATH . $task['co']['id']. '/' . $tid . ".zip";
			$ext = 'zip';
		}
		else {
			$r = Cl_Dao_File::getInstance()->findOne(array('id' => $id));
			if($r['success'] && $r['count'] > 0) {
				$fileObj = $r['result'];
				$fileName = $fileObj['name'];
				$ext = strtolower($fileObj['ext']);
				$fullPath = $fileObj['path'] . $fileObj['id'] . '.' . $fileObj['ext']; 
			}
			else 
				go_to_error("file $id not found"); //$r = array('success' => false, 'err' => _('not_found'));
		}
		
		download_file($fullPath, $fileName, $ext);
		exit();
    }
    
    public function deleteAttachmentAction()
    {
		assure_login(true);
		$tid = $this->getStrippedParam('tid');
		$cid = $this->getStrippedParam('cid');
		$id = $this->getStrippedParam('fileId'); //file id
	    $attachmentType = $this->getStrippedParam('attachmenttype');
		
	    //now check for file ID. Only owner is allowed to delete file
	    $t = Cl_Dao_File::getInstance()->findOne(array('id' => $id));
	    if ($t['success'])
	    {
	        if ($t['result']['u']['id'] == $this->_u['id'])
	            $allowed = true;
	        else 
	            $allowed = false;
    	    if (!$allowed)
    	    {
    	        go_to_error("You are not the owner of the file");
    	    }
	    }
	    /** TODO: this is bad, we allow removing some shit that's not existing,
	     * this only happens when some files have been deleted without updating cache
	    else 
	        go_to_error("file not found");
    	*/
	    
		$r = $this->dao->findOne(array('id' => $tid));
		if($r['success'] && $r['count'] > 0) {
		    $task = $r['result'];
		    //check if in participants list
		    $allowed = false;
		    foreach ($task['participants'] as $pp)
		    {
		        if ($pp['id'] == $this->_u['id'])
		        {
		            $allowed = true;
		            break;
		        }
		    }
		    if (!$allowed)
		    {
		        go_to_error("You are not participating in the task");
		    }
		    
		    //All OK now. Let's delete
		    if ($attachmentType == 'task')
		        $r = $this->dao->deleteAttachment($tid, $id);
		    else 
		    {
		        $commentClass =$this->commentDaoClass;
		        $r = $commentClass::getInstance()->deleteAttachment($cid, $id, $tid);
		    }
		    $this->handleAjaxOrMaster($r);
		}	
		else 
		{
			go_to_error(_("task_not_found"));
		}        
		
    }    
}

