<?php
class Site_IndexController extends Cl_Controller_Action_Index
{
	public function errorAction()
	{
		Bootstrap::$pageTitle = t('error', 1);
	}
	
    public function indexAction()
    {
    	/*
    	If (LANGUAGE == 'vn' && $_SERVER['REQUEST_URI'] == '/')
    	{
    		header("Location: http://" . $_SERVER['HTTP_HOST'] . "/cuoi/index.htm");
    		exit();
    	}
    	*/
        $this->setViewParam('is_widget', $this->getStrippedParam('is_widget'));
        Bootstrap::$pageTitle = t("home_page_title", 1);
        Bootstrap::$pageDesc = t("home_page_desc", 1);
    	$page = $this->getStrippedParam('page',1);
    	if ($page == '')
    		$page = 1;
    	
    	$filter = $this->getStrippedParam('filter','new'); // new = newst, hot => trending, best => best , picks => editorial picks
    	if ($filter == '')
    		$filter = 'new';
    	$storyTypeStr = $this->getStrippedParam('story_type');
    	if ($storyTypeStr == 'clip-hai' )
    		$storyTypeStr = 'video-clip-hai';
    	if ($storyTypeStr == 'do-vui')
    		$storyTypeStr = 'cau-do-vui';

    	 
    	if ($storyTypeStr != '')
    	{
    		$storyTypeInt = node_type_from_slug($storyTypeStr,'');
    	}
    	else 
    		$storyTypeInt = '';
    	
    	if(is_rest() && get_value('type')!="")
    	{
    		$storyTypeInt=get_value("type",array(1,2,3,4)); //(1=> jokes,2 => pics,3 => videos,4 => quiz)
    		
    	}
    	
    	
    	
    	$tag = $this->getStrippedParam('tag','');
    	if($tag != ''){
    	    $filter = 'new';
    	    $this->setViewParam('slug',$tag);
    	}
    	 
    	$uid = $this->getStrippedParam('uiid','');
		
    	$dao = Dao_Node_Story::getInstance();
    	$ret = $dao->getStoryList($filter, $page, $storyTypeInt, $tag, $uid);
    	
    	if (is_rest()) //mobile api
    	{
    	    foreach ($ret['result'] as $i => $t)
    	    {
    	        $ret2[] = $t;
    	    }
    	    
    		$ret['result'] = $this->filterDataForMobileApi($ret2);
    		
    		$file = get_cache_file_fullpath(true);
    		$file = $file . '.json';
    		$json = json_encode($ret);
        	Cl_Utility::getInstance()->saveFile($json, $file);
        	send_json($ret);
    		die();
    	}
    	// update isVote field to $r
    	$this->setViewParam('storyTypeInt',$storyTypeInt);
        $this->setViewParam('list', $ret['result']);
        $total = ceil($ret['total'] / per_page());
        $this->setViewParam('total', $total);
        $this->setViewParam('filter', $filter);
		Zend_Registry::set('active_menu', $filter);
		Zend_Registry::set('page_nr', $page);
		//Zend_Registry::set('canonical', '');
		
		if ($storyTypeStr == '')
		{
			if ($uid != '')
			{
				Zend_Registry::set('base_url', "/thanh-vien/$uid/"); // thanh-vien/12/$page
				Zend_Registry::set('page_type', "user"); 
			}
			elseif ($tag != '')
			{
				Zend_Registry::set('base_url', "/tag/$tag/"); // tag/abc/$page
				Zend_Registry::set('page_type', "tag");
			}
			else 
			{
				Zend_Registry::set('base_url', "/" . $filter . "/"); //   hot/$page
				Zend_Registry::set('page_type', "story");
			}
		}
		else 
		{
			Zend_Registry::set('base_url', "/$filter/$storyTypeStr/"); //  hot/anh-vui/$page
			Zend_Registry::set('page_type', "story-category");
		}
		
		
		
		
		Zend_Registry::set('total', $total);
		if ($tag != '')
			Bootstrap::$pageTitle = "Tag: " . $tag;
		elseif ($storyTypeStr != '')
		{
			$storyType = story_type($storyTypeInt);
			
			Bootstrap::$pageTitle = t( $storyType. '_title', 1);
			Bootstrap::$pageDesc = Bootstrap::$pageTitle;
			if ($storyType == 'flash-game')
			{
				$this->setNoRenderer();
				echo $this->renderScript('widget/grid-game.phtml');
			}
		}    
    }
    public function filterDataForMobileApi($storyList)
    {
    	$ret = array();
    	foreach ($storyList as $i => $row)
    	{
    		unset($row['u']['counter']);unset($row['u']['ts']);unset($row['u']['counter']);
    		unset($row['content_uf']);
    		unset($row['seo']);
    		unset($row['status']);
    		unset($row['wm']);
    		unset($row['next']);
    		unset($row['prev']);
    		unset($row['url']);//not needed link 
    		unset($row['tags']);
    		unset($row['u']);
    		if(is_nan($row['counter']['hn']))
    			$row['counter']['hn']=intval($row['counter']['hn']);
    		if(is_nan($row['counter']['point']))
    			$row['counter']['point']=intval($row['counter']['point']);
    		if (isset($row['images']))
    		{
    		    foreach ($row['images'] as $i => $image)
    		    {
    		        //$image['w'] = 550;
    		        //$image['h'] = 800;
    		        $row['images'][$i] = $image;
    		    }
    		}
    		$ret[] = $row;
    	}
    	return $ret;
    }
    //hardmapping for crawler
    public function hardmappingAction()
    {
    	$key = $this->getStrippedParam('key');
    	$ret = array();
    	if ($key == 'status')
    	{
    		$ret = array(array('value' => 0, 'name' => 'queued'),
    				array('value' => 1, 'name' => 'approved'));
    	}
    	send_json(array('success' => true, 'result' => $ret));
    }
    /*
    public function setVoted(&$r){
      if ($r['success'])
        {
            $ids = array();
            foreach($r['result'] as $k => $row) {
                $ids[] = $row['id'];
           }

            $options = array('subject' => 'user', 'object' => 'story');
            $relationDao = Cl_Dao_Relation::getInstance($options);
            $where = array('s.id' => $this->_u['id'], 'o.id' => array('$in' => $ids));
            $sr = $relationDao->findAll(array('where' => $where));
            if($sr['success'] && $sr['count'] > 0) {
                foreach($r['result'] as $k => $row) {
                	//$r['result'][$k]['isVoteUp']=false;
                	//$r['result'][$k]['isVoteDown']=false;
                    foreach ($sr['result'] as $key => $l) {
                    	
                        if(isset($l['s']['id']) && $row['u']['id'] == $l['s']['id']) {
                        	//v($l['r'][0]["rt"]);die();
                        	if($l['r'][0]['rt']==1)
								$r['result'][$k]['isVoteUp']=true;
							elseif($l['r'][0]['rt']==4)
								$r['result'][$k]['isVoteDown']=true;
                        } 
                        	
                    }
                }
            }
    	}
    }*/
    
    public function adminAction()
    {
        assure_perm('sudo');
        $this->setLayout('admin');
        Bootstrap::$pageTitle = "Admin Panel";
    }
    public function imageSearchAction()
    {
        $this->setLayout('admin');
        Bootstrap::$pageTitle = "Admin Panel";
    }
    // port old db to new mongodb
    public function portAction()
    {
    	assure_perm('sudo');
    	ini_set('max_execution_time', 6000);
    	$this->genericMethod('Site_Form_Port', 'Dao_Node_PortStory', 'portStory');
    	Bootstrap::$pageTitle = "Port Old Mysql Db";
    	$this->setLayout('admin');
    	if ($this->isSubmitted())
    	{
    		$this->setViewParam('msg', 'Job Submited');
    	}
    }
    
    //update Iids to redis
    public function updateIidAction()
    {
    	assure_perm('sudo');
    	$pair = array('user' => 'Dao_User', 'story' => 'Dao_Node_Story', 'tag' => 'Dao_Node_Tag');
    	Dao_User::getInstance()->updateIidsToRedis($pair);
    	Bootstrap::$pageTitle = 'update iids to redis';
    }
    
    //Update chuc nang tim kiem 1.8.2013
 	public function searchAction()
    {
        Bootstrap::$pageTitle = "Search";
    }
    
    /**
     * 	Get list top user activity for day
     * 		- top_user:7
     * 		- top_user:30
     * 		- top_user:all
     */
    public function topuserAction()
    {
       $limit = get_value('number_top_user',10);
       $redis = init_redis(RDB_CACHE_DB);
       
       //Get top user: 7
       $topUser7 = $redis->zRange('top_user:7', 0, -1, true);
       $topUser7 = array_reverse($topUser7,true);
      
       $listTopUser7 = array();
       $count = 0;
       foreach ($topUser7 as $k => $v){
           if($count >= $limit)
               break;
           
           $count ++;    
           $where = array('iid'=>$k);
           $cond['where'] = $where;
           
           $r = Dao_User::getInstance()->find($cond);
           
           $data['avatar'] = $r['result'][0]['avatar'];
           $data['uiid'] = $r['result'][0]['iid'];
           $data['karma'] = (int)($v);
           if($data['karma'] > 1000){
               $pre = $data['karma'] / 1000;
               $las =  $data['karma'] / 100;
               $las = (int)$las % 10;
               $data['karma'] = (int)$pre . '.' .$las . 'K';
           }
           
           if(strlen($r['result'][0]['name']) > 6)
               $data['name'] = substr($r['result'][0]['name'],0,6).'...';
           else 
               $data['name'] = $r['result'][0]['name'];
           
           $listTopUser7[] = $data;
       }
       
       //Get top user: 30
       $topUser30 = $redis->zRange('top_user:30', 0, -1, true);
       $topUser30 = array_reverse($topUser30,true);
      
       $listTopUser30 = array();
       $count = 0;
       foreach ($topUser30 as $k => $v){
            if($count >= $limit)
               break;
               
           $count ++;
           $where = array('iid'=>$k);
           $cond['where'] = $where;
           
           $r = Dao_User::getInstance()->find($cond);
           
           $data['avatar'] = $r['result'][0]['avatar'];
           $data['uiid'] = $r['result'][0]['iid'];
           $data['karma'] = (int)($v);
           if($data['karma'] > 1000){
               $pre = $data['karma'] / 1000;
               $las =  $data['karma'] / 100;
               $las = (int)$las % 10;
               $data['karma'] = (int)$pre . '.' .$las . 'K';
           }
           if(strlen($r['result'][0]['name']) > 6)
               $data['name'] = substr($r['result'][0]['name'],0,6).'...';
           else 
               $data['name'] = $r['result'][0]['name']; 
               
           $listTopUser30[] = $data;
       }   
       
       //Get top user: all time
       $order = array('counter.k' => -1);
	   $cond2['order'] = $order;
       $cond2['limit'] = $limit;
       $u = Dao_User::getInstance()->find($cond2);
       
       $listTopUserAll = array();
       if($u['success']){
           foreach ($u['result'] as $row){
               $data['avatar'] = $row['avatar'];
               $data['uiid'] = $row['iid'];
               $data['karma'] = (int)($row['counter']['k']);
               if($data['karma'] > 1000){
                   $pre = $data['karma'] / 1000;
                   $las =  $data['karma'] / 100;
                   $las = (int)$las % 10;
                   $data['karma'] = (int)$pre . '.' .$las . 'K';
               }
               if(strlen($row['name']) > 6)
                   $data['name'] = substr($row['name'],0,6).'...';
               else 
                   $data['name'] = $row['name'];
                   
               $listTopUserAll[] = $data;
           }
       }
       
       $this->setViewParam('top7', $listTopUser7);
       $this->setViewParam('top30', $listTopUser30);
       $this->setViewParam('topAll', $listTopUserAll);
    }
    
    public function aboutAction()
    {
    	Bootstrap::$pageTitle = t("About", 1);
    }
    
    public function contactAction()
    {
    	Bootstrap::$pageTitle = t("Contact",1);
    }
    
    public function termsAction()
    {
    	//$terms= get_conf("terms","Terms");
    	//$this->setViewParam('terms', $terms);
    	Bootstrap::$pageTitle = t("Terms_of_Use",1);
    }
    
    
    /**************************RAGECOMIC Maker**********************************/
    public function rageTranslationsAction()
    {
        Zend_Layout::getMvcInstance()->disableLayout();        
    }
    
    public function rageMakerAction()
    {
        $this->setLayout('ragelayout');
        $this->setNoRenderer()    ;
        $edit = $this->getStrippedParam('edit');
        $id = $this->getStrippedParam('id');
        if(isset($id) || $id != null || $id != '')
        {
            assure_perm('root');
            $r = Dao_Node_Story::getInstance()->findOne(array('id'=> $id));
            if($r['success'])
            {
               $this->setViewParam('storyRegistered', $r['result']);
               $this->setViewParam('isEdit', true);
            }
        }            
    }

    public function ragePackAction()
    {
        require_once LIBRARY_PATH . '/rage.php';
        $data = rage_pack();
        send_json($data);
//        Zend_Layout::getMvcInstance()->disableLayout();
    }
    
    public function ragePackManifestAction()
    {
        $packName = $this->getStrippedParam('packName');
        require_once PUBLIC_PATH . "/rage/ragecomic/packs/{$packName}/manifest.json";
        die(); 
    }
    
    public function apiAction()
    {
    	Bootstrap::$pageTitle = 'API for mobile';
    }
    public function goodAppAction()
    {
    	Bootstrap::$pageTitle = 'Good App';
    }
    
    public function upanhAction()
    {
    	Bootstrap::$pageTitle = "Fake upanh";
    }
    
    /*
    public function dispatchLoopShutdown()
    {
        if (Zend_Registry::get('page') == 'site/index/rage-pack')
        {
            $data = Zend_Registry::get('data');
            send_json($data);
            die();
        }
        else 
            parent::dispatchLoopShutdown();
    }
    */
}
