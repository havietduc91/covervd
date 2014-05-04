<?php
/**API stuff**/
class Story_ApiController extends Cl_Controller_Action_NodeIndex
{
	public function indexAction()
	{
	    //TODO: get the avatar url and set shit
	    $avatar = $this->getStrippedParam('avatar');
	    if (strpos($avatar, 'youtube.com'))
	    {
	        $storyType = 'video';
	        $tmp = explode('/', $avatar);
	        
	        //http://img.youtube.com/vi/wrZypHVXVt4/0.jpg
	        $len = count($tmp);
	        $youtubeId = $tmp[$len - 2];
	        $this->_request->setParam('url', "http://www.youtube.com/watch?v=$youtubeId");
	        
	        $this->_request->setParam('ytid', $youtubeId);
	        $type = 3;
	    }
	    else
	    { 
	        $storyType = 'image';
	        $this->_request->setParam('url', $avatar);
	        $type = 2;
	    }
	    
	    $this->_request->setParam('_cl_step', $storyType);
	    $this->_request->setParam('type', $type);
	    //$this->_request->setParam('_cl_step', $storyType);
        $this->api('Dao_Node_Story', 'Story_Form_New', 'Story_Form_Update');                
	}
}
