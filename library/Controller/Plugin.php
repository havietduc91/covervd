<?php
class Controller_Plugin extends  Cl_Controller_Plugin
{
  
	public function dispatchLoopShutdown()
	{
	    $page = Zend_Registry::get('page');
	    //DO NOT CACHE FOR ADMIN
	    if (!has_perm('admin_story'))
	    {
    	    if (is_modal_ajax())
    	    {
    	    	$html = $this->getResponse()->getBody();
    	    	$json = array('success' => true, 'result' => array('title' => Bootstrap::$pageTitle, 'content' => $html));
    	    	//send_json(array('success' => true, 'result' => $json));
    	    	if ($page == 'story/index/view' || $page == 'site/index/index'
    	    	        || $page == 'story/index/widget'
    	    	        || $page == 'site/index/topuser'
    	    	        )
    	    	{
    	    	    $file = get_cache_file_fullpath();
    	    	    $file = $file . '.json';
    	    	    /*
        	    	$json = json_encode($json, JSON_UNESCAPED_UNICODE, "\n"); // now is string
        	    	*/ 
    	    	    $json = json_encode($json);
        	    	Cl_Utility::getInstance()->saveFile($json, $file);
    	    	}
    	    }
    	    else if ($page == 'story/index/view' || $page == 'site/index/index')
    		{
    			$content = $this->getResponse()->getBody();
    			$content = $content . "<span style='display:none;'>cached</span>"; 
    			if($page == 'story/index/view')
    			    Cl_Utility::getInstance()->saveFile($content, get_cache_file_fullpath());
			    if($page == 'site/index/index')
    			    Cl_Utility::getInstance()->saveFile($content, get_cache_file_fullpath().'.html');
    		}
	    }		
		parent::dispatchLoopShutdown();
	}
}