<?php 
class Story_Form_Comment extends Cl_Form_CommentNode
{
    public $nodeType = "story";

    /*
    public $attributes = array(
    		'id' => "comment_form",
    		'method' => "POST",
    		'action' => "/story/comment",
    		'class' => "cl_ajax",
            'new_url' => "/story/comment",
            'update_url' => "/story/update-comment"
    );
    */
    
	public function init()
	{
		parent::init();
		if (method_exists($this, "_customFormFieldsConfig"))
			$this->_formFieldsConfig = array_merge($this->_formFieldsConfig(), $this->_customFormFieldsConfig());
	
		$this->fieldList = array('content', 'nid','pid', 'id'/*, 'attachments'*/);
	}
	
	public function setStep($step)
	{
	    if ($step == 'update')
	        $this->fieldList = array('content', 'nid', 'id');
        elseif($step == 'status')
            $this->fieldList = array('status', 'nid', 'id');
        elseif($step == 'is_spam')
            $this->fieldList = array('nid', 'id');
	    parent::setStep($step);
	}
	
    protected function _customFormFieldsConfig()
    {
        return array(
        	'content' => array(
        		'type' => 'Textarea',
        		'options' => array(
                    'required' => true,
        			'class' => 'isEditor',
        		    'label' => t("standard_shortcuts_are_similar_to_Microsoft_Office:_Ctrl_+_B_for_bold_text,etc...)",1),
    	    		'filters' => array('StringTrim', 'StoryPost'), //TODO: filter user input here. use Purifier
        			'prefixPath' => array(
        				"filter" => array (
        					"Filter" => "Filter/"
        				)
        			)
        		),
        	),
            'attachments' =>  array(
        		'type' => 'Hidden',
        		'options' => array(
                    'transformers' => array("tokensJSONStringToArray")
                ) 
        	),
            'status' => array(
                'type' => 'Hidden'
            )
        );
    }
    
     public function customPermissionFilter($data, $currentRow)
     {
         if ($this->step == 'update')
         {
             //check permission for update
             $lu = Zend_Registry::get('user');
             if ($lu['id'] != $currentRow['u']['id'])
                 return array('success' => false, 'err' => "You are not owner of the comment");
         }
         return array('success' => true);
     }
}