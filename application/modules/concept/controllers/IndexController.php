<?php
class Concept_IndexController extends Cl_Controller_Action_NodeIndex {
	
    public function init() {
        assure_perm('admin_story');
		parent::init ();
	}
	
    public function paserAction(){
	    Dao_Node_Concept::getInstance()->paserDicFileInsertToConcept();
	    
	    Bootstrap::$pageTitle = "paser Dic File Insert To Concept";
	    die('OK');
	}
}

