<?php
class Story_Form_Helper extends Cl_Form_NodeHelper
{
    public function getStatus()
    {
    	$ret = array('approved' => 'approved', 'queued' => 'queued', 'scheduled'=>'scheduled', 'spammed'=>'spammed');
    	return array('success' =>true, 'result' => $ret);
    }
    /**return story type**/
    public function getStoryType(){
    	$ret = array('1'=> t('story',1),'2'=> t('img',1),3=> t('video',1), 4 => t('flash-game',1), 5 => t('quiz',1));
    	return array('success'=>true, 'result'=>$ret);
    }
    public function getGameCategory(){
    	$ret = array('action'=> t('action',1), 'casino'=> t('casino',1), 'adventure'=>t('adventure',1), 'chess'=>t('chess',1), 'multiplayer'=> t('multiplayer',1));
    	return array('success'=>true, 'result' => $ret);
    }
    
    /*
    public function getItemsPerPageList($params)
    {
    	$ret = array(
    	    '-1' => "All",
    		'10' => "10/page",
    		'20' => "20/page",
    		'30' => "30/page",	
    		'50' => "50/page");
    	return array('success' => true, 'result' => $ret);
    }
    */
}
