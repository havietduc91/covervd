<?php
class Snapshot_IndexController extends Cl_Controller_Action_NodeIndex {
	
    public function init() {
        assure_perm('admin_story');
		parent::init ();
	}
	
	public function fakeSnapshotAction() {
	    //Duyet toan bo cac userid sau do se thuc hien insert toan bo vao bang snapshot
	    $u = Dao_User::getInstance()->findAll(array('return_cursor' => true));
	    $listUser = array();
	    $data = array();
	   
	    if($u['success']){
    	    foreach ($u['result'] as $row){
    	        //for ($i = 1; $i ++ ; $i < 14)
    	        //{
        	        $data['uiid'] = $row['iid'];
        	        //$data['ts'] = time() -(14 - $i) * 86400;
        	        //$data['karma'] = $i;
        	        $data['ts'] = time() - 1 * 86400;
        	        $data['karma'] = 15;
        	        Dao_Node_Snapshot::getInstance()->insert($data);
    	        //}
    	        //$listUser[] = $data;
    	    }    
	    }
	   
		Bootstrap::$pageTitle = "Add new fake Snapshot";
		
	}
	
	public function fakeBaseKarmaAction() {
        $u = Dao_User::getInstance()->findAll(array('return_cursor' => true));
	    $listUser = array();
	    $data = array();
	   
	    if($u['success']){
    	    foreach ($u['result'] as $row){
    	        $data['uiid'] = $row['iid'];
    	        $data['ts'] = time() - 14 * 86400;;
    	        $data['karma'] = 0;
    	        
    	        Dao_Node_BaseKarma::getInstance()->insert($data);
    	        $listUser[] = $data;
    	    }    
	    }
    	       
	    Bootstrap::$pageTitle = "Fake base karma";
	}
	
	public function rebaseKarmaAction(){
	    Dao_Node_Story::getInstance()->rebaseKarma();
	    
	    Bootstrap::$pageTitle = "Re Base Karma";
	    die('OK');
	}
	
    public function takeSnapshotAction(){
        
	    Dao_Node_Story::getInstance()->takeSnapshot(time());
	    
	    Bootstrap::$pageTitle = "Take Snap Shot";
	    die('OK');
	}
	
    public function topscorekarmaAction(){
	    Dao_Node_Story::getInstance()->topScoreKarma();
	    
	    Bootstrap::$pageTitle = "Top Score Karma";
	    die('OK');
	}
	
     public function guidetestAction(){
       
	    Bootstrap::$pageTitle = "Guide Test Top Score Karma";
	}
}

