<?php
/**
 * Remember you have    
 *  public $dao;
 *  public $node, $nodeUC; //node name : foo|post|item...

 * @author tran
 */ 
class Story_IndexController extends Cl_Controller_Action_NodeIndex 
{
    public function init()
    {
        //$this->daoClass = "Cl_Dao_Node_Story";
        //$this->commentDaoClass = "Cl_Dao_Comment_Story";
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
    
    public function rageMakerAction()
    {
        if (!has_perm('new_story'))
    	    $this->_redirect('/user/login');
        $title = $this->getStrippedParam('title');
        //$tags = $this->getStrippedParam('tags');
        $storyId = $this->getStrippedParam('storyId');
        $imageData = $this->getStrippedParam('image');
        $binary = base64_decode($imageData);
        $id = new MongoId();
        $today = getdate();
        $file = '/'.$today['year'].'/'.$today['month'].'/'.$today['mday'].'/' . $id . '.jpg';
        $filePath = STATIC_PATH . $file;
        $url = STATIC_CDN . $file;
        //WRITE TO STATIC_CDN/2013/10/31/$id.jpg
        Cl_Utility::getInstance()->saveFile($binary, $filePath);
        if($storyId != '' || $storyId != null)
        {
            //TODO : get OLD imgae URL. and copy new file to old folder
            assure_perm('root');
            $r2 = Dao_Node_Story::getInstance()->findOne(array('id'=> $storyId));
            $url2 = PUBLIC_FILES_UPLOAD_PATH . '/1000/'.$r2['id'];
            Cl_Utility::copyFile($filePath, $url2);
            $i = Dao_Node_Story::getInstance()->uploadAndResizeImage($url2);
            die('OK');
        }
        $tmp = Cl_Dao_Util::getUserDao()->getCacheObject(Zend_Registry::get('user'));
        if ($tmp['success'])
            $u = $tmp['result'];
        
        $values['u'] = $u;
        $values['_u'] = Zend_Registry::get('user');
        $values['ts'] = time();
        $values['name'] = $title;
        $values['url'] = $url;
        $values['type'] = 2;
        $r = Dao_Node_Story::getInstance()->insertNode($values);
        if ($r['success'])
        {
            $this->_redirect(node_link('story', $r['result']));
        }
        $this->handleAjaxOrMaster($r);        
    }
    
public function newAction()
    {
    	$lu = Zend_Registry::get('user');
		$crawler = get_conf("crawler","crawler@stuff.com");
		$nrOfRandomUser = get_conf("nrOfRandomUser","100");
		if($lu['mail'] == $crawler)
		{
			$newU = Cl_Dao_Fake::getInstance()->getRandomUser($nrOfRandomUser);
			Zend_Registry::set('user',$newU);
		}
    	if (!has_perm('new_story'))
    	    $this->_redirect('/user/login');
    	
    	$storyTypeInt = get_value('type');
    	$storyType = story_type($storyTypeInt);
    	$this->_request->setParam('_cl_step', $storyType);
    	$nodeUC = $this->nodeUC;//$node = 'story' $nodeUc => 'Story
    	$this->genericNew("Story_Form_New", "Dao_Node_Story", "Node");
    	   
        //parent::newAction();//this action presumes a permission of "new_$node"
        
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
        			$this->ajaxData['data'] = array('url' => node_link('story',$this->ajaxData['result']));
        			//OR go to search : $this->ajaxData['data'] = array('url' => '/samx/search');
        		}
        	}
        }    
        Bootstrap::$pageTitle = t("upload", 1);
    }

    public function updateAction()
    {
        /**
         * Permission to update a node is done in 
         * $Node_Form_Update form->customPermissionFilter()
         * Do not do it here
         * @NOTE: object is already filtered in Index.php, done in Cl_Dao_Node::filterUpdatedObjectForAjax()
         */
        $this->genericUpdate("Story_Form_Update", $this->daoClass ,"", "Node");
        Bootstrap::$pageTitle = "Update " . $this->nodeUC;
    }

    public function searchAction()
    {
        $this->setLayout('admin');
        if (Zend_Registry::get('page') == 'story/index/search')
        	assure_perm("search_story");//by default
        $this->genericSearch("Story_Form_Search", "Dao_Node_Story", "Node");
        $type = $this->getStrippedParam('type');
        $typeStr = story_type($type);
        
        $title = t("search",1);
        $desc =  t("the_most_funny_website.fun_videos,fun_movies",1);
        
        if ($typeStr == 'image')
        {
        	$title = t("fun_pics,funny_pics",1);
			$desc =  t("the_most_funny_website.fun_videos,fun_movies",1);
        }
        if ($typeStr == 'video')
       	{
       		$title = t("fun_videos,funny_videos",1);
       		$desc =  t("the_most_funny_website.fun_videos,fun_movies",1);
       	}
       	if ($typeStr == 'story')
       	{
       		$title = t("fun_stories,funny_stories",1);
       		$desc =  "the_most_funny_website.fun_videos,fun_movies";
       	}	 
       	if ($typeStr == 'quiz')
       	{
       		$title = t("fun_quiz,funny_quiz",1);
       		$desc =  t("the_most_funny_website.fun_videos,fun_movies",1);
       	}
       	if ($typeStr == 'flash-game')
       	{
       		$title = t("fun_flash_games,funny_flash_games",1);
       		$desc =  t("the_most_funny_website.free_to_play_flash_games",1);
       	}
       	if ($typeStr == 'link')
       	{
       		$title = t("top_links",1);
       		$desc =  t("the_most_funny_website.top_links");
       	}
       	if ($typeStr == 'quote')
       	{
       		$title = t('quotes',1);
       		$desc =  t("the_most_funny_website.top_quotes");
       	}
       	
        Bootstrap::$pageTitle = $title;
        Bootstrap::$pageDesc = $desc;
    }
    
    
    public function viewAction()
    {
        $iid = (string) $this->getStrippedParam('iid');
        //$slug = (string) $this->getStrippedParam('slug');
        if ($iid)
        	$where = array('iid' => $iid);
        //elseif ($slug)
        	//$where = array('slug' => $slug);
        	
    	$r = $this->dao->findOne($where, true /*convert id*/);
    	//$r = Dao_Node_Story::getInstance()->findOne($where, true /*convert id*/);
    	if ($r['success'] && $r['count'] > 0)
    	{
    	     $this->setViewParam('row', $r['result']);
    	}   
    	else 
    	{
    		$this->_redirect("/");
    	}
    	
    	//Get list related story 5.9.2013
    	$list = array();
    	$tags = array();
    	
    	if(isset($r['result']['tags'])){
    		foreach ($r['result']['tags'] as $row){
    			$tags[] = $row['id'];
    		}
    		$where = array(
    				'status' => 'approved',
    				'ts' => array('$lte' => $r['result']['ts']),
    				'tags.id' => array('$in' => $tags),
    				'iid' => array('$ne' => $r['result']['iid'])
    		);
    		
    		$cond['where'] = $where;
    		$cond['limit'] = 8;
    		$order= array('ts'=>-1);
    		$cond['order'] = $order;
    		
    		$list = Dao_Node_Story::getInstance()->find($cond);
    		if($list){
    			$this->setViewParam('list', $list);
    		}
    	}
    	
    	/**
    	 * Haduc update next and prev 12.8.2013
    	 * Get next-iid, prev-iid
    	 * if next-iid already exit: Get next-iid, next-slug and set url-next
    	 * else if next-iid already not exit :
    	 * 		- Set next-iid, next-slug and prev-iid, prev-slug
    	 * 		- Set url-next
    	 * 
    	 * Prev same!!!
    	 */
    		
    	if(!isset($r['result']['next']) || !isset($r['result']['prev'])){
    		/**
    		 * Delete cache after updateNext and Prev 
    		 */
    		Dao_Node_Story::getInstance()->updateNextandPrevIidAfterUpload($r['result'],$r['result']);
    		$r = $this->dao->findOne($where, true /*convert id*/);
    	}
    	
    	// Check if result next and prev already exit then set ViewParma
    	if (isset($r['result']['next'])){
    		$this->setViewParam('rowN', $r['result']['next']);
    	}
    	if(isset($r['result']['prev'])){
    		$this->setViewParam('rowP', $r['result']['prev']);
    	}
    	
    	// get cache html file if exists
        if ($row = $this->getViewParam('row'))
        {
            $id = $row['id'];
            $where = array('node.id' => $id);
            
            $comments = isset($row['comments']) ? $row['comments'] : array();
         	$this->setViewParam('comments', $comments);    
           
            //SEO param
            Bootstrap::$pageTitle = 
            	(isset($row['seo']['title']) && $row['seo']['title'] != '') ? $row['seo']['title'] : $row['name'];
            	 
        	Bootstrap::$pageDesc= (isset($row['seo']['desc']) && trim($row['seo']['desc']) != '') ?
            	trim($row['seo']['desc']) : t('click_to_view_and_comment');
            /*	 
            	
            Bootstrap::$pageDesc= (isset($row['seo']['desc']) && trim($row['seo']['desc']) != '') ?
            	trim($row['seo']['desc']) : ((isset($row['content']) && trim(strip_tags($row['content'])) != '' ) ? 
            		trim(strip_tags($row['content'])) : t('click_to_view_and_comment'));
            */            		
            if(isset($row['url'])){
                Zend_Registry::set('img', $row['url']);
            }
            if(isset($row['slug'])){
                Zend_Registry::set('slug',$row['slug']);
            }

            $redis = init_redis(RDB_CACHE_DB);
            $redis->incr("views_" . $row['iid']);
            
            $typeInt = $row['type'];
            if (story_type($typeInt) == 'quiz' || story_type($typeInt) == 'flash-game')
            {
            	Zend_Registry::set('active_menu', story_type($typeInt));
            }
            if (story_type($typeInt) == 'image')
            {
                Zend_Registry::set('og:type', 'photo');
            }
            else 
            {
                Zend_Registry::set('og:type', 'website');
            }
            
            Zend_Registry::set('canonical', SITE_URL . node_link('story', $r['result']));
        }   
        
    }
    
    public function updateCounterViewFromRedisToDbAction()
    {
        //set $limit = -1 for update all StoriesViewsCounter
        //if not $limit = 100 (default) ;
        $limit = '';
        $r = Dao_Node_Story::getInstance()->updateViewCounter($limit);
        if($r['success'])
        {
            $this->removeStaticCacheAction();
        }
    }
    
    public function deleteAction()
    {
        parent::deleteAction();
        if ($this->ajaxData['success'])
        {
            $this->ajaxData['callback'] = 'reload_page';
            $this->ajaxData['data'] = array('msg' => t('successful',1));
        }
    }
    
    public function deleteNodePermissionCheck($row)
    {
        if (has_perm("delete_story"))
            return array('success' => true);
        else 
            return array('success' => false);
    }

    public function searchCommentAction()
    {
        assure_perm("search_story");//by default
        $commentClass =$this->commentDaoClass;
        $this->genericSearch("Story_Form_SearchComment", $commentClass, "");
        $this->setLayout("admin");
        Bootstrap::$pageTitle = "Search " . $this->nodeUC . " Comments";        
    }

    public function commentAction(){
    	//$this->commentScript = "index/one-comment.phtml";
    	parent::commentAction();
    }
    
    //implements parent::newCommentPermissionCheck
    public function newCommentPermissionCheck($row)
    {
    	//TODO: Implement this
    	return has_perm("new_story_comment");
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
    
    
    public function refreshHotnessAction(){
    	assure_perm('admin_story');
    	$r= Dao_Node_Story::getInstance()->calculateHotness(array('ts' => time()));
    	Bootstrap::$pageTitle = "Refresh Hotness Manually";
    }

    public function gamesAction(){ //display games by grid. a different layout
    	$where = array('where'=>array('type'=>4, 'status'=>'approved'));
    	$list_games = Dao_Node_Story::getInstance()->find($where);
    	$this->setViewParam('games', $list_games['result']);
    }
    

    public function removeStaticCacheAction()
    {
    	assure_perm('admin_story');
    	rrmdir(PUBLIC_PATH . '/cache');
    	Bootstrap::$pageTitle = "Remove all static cache manually";
    }
    
    /**
     * @param $type is new|vote|hot
     * Select list new image, hot image or vote image
     * 
     * if type = new
     * Condition:
     * 		+ status: approved
     * 		+ counter.point > 0
     * 		+ limit 3
     * if type = hot
     * Condition:
     * 		+ status => approved
     * 		+ couter.point > 0
     * 		+ order by counter.hn => -1
     * 		+ limit 3
     * if type = vote
     * Condition:
     * 		+ time stamp by random
     * 		+ limit 2
     * 		+ status queue 
     * 
     * @return list new|vote|hot Image type Dao_Node_Story
     */
    public function widgetAction(){
        $type = $this->getStrippedParam('type');
        $types = array(2,3);
       
        //Get type = video or image
        if ($type == 'new')
        {
            $limit = 3;
            $where = array('status' => 'approved','type'=>array('$in'=>$types));
            $order = array('ts' => -1);
            
            $title = t("newest",1);
        }else if ($type == 'hot'){
            $where = array('status' => 'approved','type'=>array('$in'=>$types));
            $order = array('counter.hn' => -1);
		    $limit = 2;
		    
		    $title = t("hot_ness",1);
        }else if($type == 'vote'){
            $where = array('type'=>array('$in'=>$types),'status'=>'queued');
		    $limit = 1;    
		    $order = array('ts' => -1);
		    
		    $title = t('help_%s_vote_this_story',1,DOMAIN);
        }
        
		if(isset($order))
		    $cond['order'] = $order;
		    
		$cond['where'] = $where;
		$cond['limit'] = $limit;
		//v($cond);
		$r = Dao_Node_Story::getInstance()->find($cond);
		//v($r);die();
		$this->setViewParam('list', $r);
		$this->setViewParam('title',$title);
    }
    
    public function uploadImageAction()
    {
    	assure_login();
    	$relativeFolder = $this->getStrippedParam('folder'); //if the client requests storing to a specific folder
    	$fileDao = Cl_Dao_File::getInstance();
    	$access = $this->getStrippedParam('access', 'public');
    	$resize = $this->getStrippedParam('resize', false);
    	$type = $this->getStrippedParam('type'); //avatar | image | attachment
    	$remote = $this->getStrippedParam('remote', false); //remote file or uploaded files
    	$isWM = $this->getStrippedParam('isWM', false);
    	
    	if($access == 'secure') {
    		$root = SECURE_FILES_UPLOAD_PATH; //../hidden/
    		$server = SECURE_FILES_SERVER;
    	}
    	else {
    		$root = PUBLIC_FILES_UPLOAD_PATH; /// ../public/uploads/
    		$server = PUBLIC_FILES_SERVER;
    	}
    	
    	if ($relativeFolder)
    		$localDestinationFolder = $root . "/" . $relativeFolder . "/";
    	else
    	{
   			$localDestinationFolder = $root . '/';
    	}
    	
    	if ($remote)
    	{
    		$files = $this->getStrippedParam('files');
    		$files = explode(",", $files);//array of remote files
    	}
    	else //uploaded from form
    	{
    		$upload = new Zend_File_Transfer();
    			
    		//TODO if $access = 'public' => restrict to non-executable files, no .php files....
    		if($access == 'public')
    		{
    			$upload->addValidator('ExcludeExtension', false, array('php','exe'));
    		}
    		$files = $upload->getFileInfo();
    	}
    	//Process each file
    	if(count($files) > 0) {
    		foreach ($files as $file => $info) {
    			$options = array();
    			//STEP 1. Insert the file object to `files` collections
    			if (!$remote)
    			{
    				//validators
    				if(!$upload->isValid($file)) {
    					$r = array('success' => false, 'err' => $upload->getMessages());
    					echo json_encode($r);
    					exit();
    				}
    				$filename = $upload->getFileName($file);
    				preg_match('#\.([^\.]+)$#',$filename,$matches);
    				$fileExtension  = $matches[1];
    				$ext = $fileExtension;//$this->getFileType($info['type']);
    				if(!$ext)
    				{
    					$r = array('success' => false, 'err' => 'Unknown file type!' . $info['type']);
    					echo json_encode($r);
    					exit();
    				}
    			}
    			else
    			{
    				$absoluteFile = $info;//remote path
    				$client = new Zend_Http_Client();
    				$client->setUri($absoluteFile);
    				$result = $client->request('GET');
    				$ext = $this->getFileType($result->getHeader('Content-Type'));
    				$info['name'] = basename($absoluteFile, ".$ext");
    			}
    				
    			// insert file object to get fileObj id first
    			$fileObjData = array(
    					'u' => get_user_basic(),
    					'name' => $info['name'],
    					'ext' => $ext,
    					'path' => $localDestinationFolder, //dir
    					'server' => $server, //amazon or local
    					'access' => $access,
    			);
    	
    			$r = $fileDao->insert($fileObjData);
    			if(!$r['success']) {
    				echo json_encode($r);
    				exit();
    			}
    			$fileObj = $r['result'];
    	
    			if (!$remote)
    			{
    				$absoluteFile = $localDestinationFolder . $fileObj['id'] . '.' . $fileObj['ext'];
    				//STEP 2: save file to $localDestinationFolder first
    				if(!is_dir($localDestinationFolder))
    				{
    					mkdir($localDestinationFolder, 0777, true);
    				}
    				$upload->addFilter('Rename', array('target' => $absoluteFile, 'overwrite' => true));
    				if(!$upload->receive($file)) {
    					$r = array('success' => false, 'err' => $upload->getMessages());
    					echo json_encode($r);
    					exit();
    				}
    			}
    	
    			//STEP 3: BG resize or upload to amazon
    			$options = array(
    					'resize' => $resize,
    					'access' => $access,
    					'absolute_file' => $absoluteFile,
    					'file_id' => $fileObj['id'],
    					'type' => $type, //avatar|item Image|task attachment
    					'local_destination_folder' => $localDestinationFolder,
    					'relative_folder' => $relativeFolder,
    					'remote' => $remote,
    					'extension' => $ext, //TODO: need this?
    					'server' => $server,
    					'isWM' => $isWM, // add wartermark ?
    			);
    	
    		    //Cl_File::getInstance($options)->upload();
    	
    				
    			$attachment = array(
    					'u' => get_user_basic(),
    					'id' => $fileObj['id'],
    					'ext' => $fileObj['ext'],
    					'name' => $info['name'],
    			);
    	
    			if ($access == 'public' && !$remote)
    			{
    				if (defined('STATIC_CDN'))
    					$attachment['link'] = str_replace(PUBLIC_FILES_UPLOAD_PATH , STATIC_CDN, $absoluteFile);
    				else
    					$attachment['link'] = str_replace(PUBLIC_FILES_UPLOAD_PATH, '', $absoluteFile);
    			}
    			else
    				$attachment['link'] = $absoluteFile;
    	
    			$attachments[] = $attachment;
    		}
    		$ret['attachments'] = $attachments;
    		$r = array('success' => true, 'result' => $ret);
    	}
    	
    	else {
    		$r = array('success' => false,'err' => "No files uploaded");
    	}
    	
    	$redirect = $this->getStrippedParam('redirect', '');
    	if ($redirect != '' & !is_ajax())
    		$this->_redirect($redirect);
    	
    	
    	$uploadWith = $this->getStrippedParam('uploadWith', '');
    	if($uploadWith == 'mce') {
    		if($r['success'])
    			$this->_redirect('/mfm.php?status=1');
    		else
    			$this->_redirect('/mfm.php?status=2');
    	}
    	else {
    		echo json_encode($r);
    		exit();
    	}
    }
    //==============FB comment,like,share counter===========
    
    public function newFbCommentAction()
    {
        // TODO: match this for different url schemes
        $url = preg_replace('/#view$/', '',get_value('url'));
        $id = get_value('id');
        $fbc = fb_counter($url,'comment'); //fb comment
	    $formClass = 'Story_Form_Update';
	    $daoClass = 'Dao_Node_Story';
		$step = '';
		$obj = 'Node';
		$dao = $daoClass::getInstance();
		
		$where = array('id' => $id);
		$r = $dao->findOne($where);
        
		if ($r['success'] && $r['count'] > 0)
		{
		    $row = $r['result'];
		    $c = isset($row['counter']['c']) ? $row['counter']['c'] : 0;
		    $update = array('counter.fbc' => $fbc ,
		            'counter.tc' => $fbc + $c
		    );
		    $update = array('$set' => $update);
		    $r = $dao->update($where, $update);
		    $dao->deleteStaticCache($row);
		}
		$r = array('success' => true, 'result' => 'done!');
        $this->handleAjaxOrMaster($r);
    }
    public function newFbLikeAction()
    {
    	
    	$iid = $this->getStrippedParam('iid');
    	$id = $this->getStrippedParam('id');
    	$rt = $this->getStrippedParam('rt');
    	$uiid=$this->getStrippedParam('uiid');
    	if(is_rest() && $rt==1)
    	{
	    	$options = array(
	    			'subject' => 'user',
	    			'object' => 'story',
	    	);
	    	$relationDao = Cl_Dao_Relation::getInstance($options);
	    	$where=array(
	    				 's.iid'=>(string)$uiid,
	    				 'o.id'=>(string)$id,
	    				 'r.rt'=>$rt);
	    	$cond['where']=$where;
	    	$r=$relationDao->find($cond);
    		if($r['count']>0)
    		{
	    		$r = array('success' => false, 'result' => 'Already like FB');
	    		send_json($r);
    		}
    		else { //new relation
    			$object='story';
    			$data = array(
    					's' => Zend_Registry::get('user'),
    					'o' => array('id' => $id),
    					'r' => array(
    							'rt' => $rt
    					)
    			);
    			$requestParams = array(
    								'object'=>$object,
    								'rt'=>$rt,
    								'id'=>$id
    							);
    			$dao = Cl_Dao_Util::getDaoObject($object); //comment_samx => Cl_Dao_Comment_Samx
    			$r = $dao->insertRelation($data, $options, $requestParams);
    			if (!$r['success'])
    			{
    				$r = array('success' => false, 'result' => 'Error insert database');
	    			send_json($r);
    			}
    			$dao_story=Dao_Node_Story::getInstance();
    			$rs=$dao_story->findOne(array('id'=>$id));
    			$iid=$rs['result']['iid'];
    		}
    	}
    	Dao_Node_Story::getInstance()->fb_like_inc_point($iid,$rt);
    	$r = array('success' => true, 'result' => 'done!');
    	if(is_rest())
    	{
    		send_json($r);
    	}
    	$this->handleAjaxOrMaster($r);
    }
    public function hottagsAction()
    {
    	$popularTagsRedis = get_conf('popular_tags_redis', 'popularTagsRedis');
    	$redis = init_redis(RDB_CACHE_DB);
    	$currentPopularTagsRedis = $redis->zRange($popularTagsRedis, 0, -1, true);
    	$hottag=array();
    	foreach($currentPopularTagsRedis as $key=> $row)
    	{
    		$hottag[]=(string)$key;
    	}
    	$dWhere = array('iid' => array('$in' =>$hottag));
    	$ret = Dao_Node_Tag::getInstance()->find(array('where' => $dWhere));	
    	if (is_rest())
    	{
    		send_json($ret);
    		die();
    	}
    	$this->setViewParam('hottags', $currentPopularTagsRedis);
    	$this->setViewParam('test', $ret);
    	Bootstrap::$pageTitle = "Hot tags";
    }
    public function newStoryMeAction()
    {
    	if(is_guest()){
    		$err=array('success'=>false,'result'=>array('error'=>'Error Login'));
    		send_json($err);
    		die();
    	}
    	$lu = Zend_Registry::get('user');
    	$ts=$lu['ts']!=""?$lu['ts']:$lu['last_login'];
    	$cond['where'] = array(
    				'ts'=>array('$gte'=>$ts),
    				'status' => 'approved'
    	);
    	$cond['order'] = array('ats' => -1);
    	
    	$ret=Dao_Node_Story::getInstance()->find($cond);
    	if($ret['success']==true && count($ret['result']) > 0)
    	{
    		$u=Dao_User::getInstance()->update(array('id'=>$lu['id']),
    											array('$set'=>array('ts'=>time()))
    										);
    		if($u['success']==true)
    		{
    			$lu['ts']=time();
    			Zend_Registry::set('user', $lu);
    		}
    	}
    	$arr=array('success'=>true,'result'=>array('count'=>count($ret['result'])));
		send_json($arr);
    	die();
    	
    }
    
    public function updateSizeImagesAction(){
    	Dao_Node_Story::getInstance()->updateSizeImages();
    	
    	Bootstrap::$pageTitle = 'Update size images';
    }
   	
    
    //==============END=========================

    //==================================DEPRECATED====================================
    /**
     * @deprecated
     */
    /*
     public function viewCategoryAction()
     {
    $type = $this->getStrippedParam('type');
    $typeInt = node_type_from_slug($type);
     
    if (story_type($typeInt) == 'quiz' || story_type($typeInt) == 'flash-game' ||
    		story_type($typeInt) == 'quote'
    )
    {
    Zend_Registry::set('active_menu', story_type($typeInt));
    }
    $this->_request->setParam('type', $typeInt);
    //$iid = $this->getStrippedParam('iid');
    $this->searchAction();
    $this->setLayout('');
    }
    */
}

