<?php 
class Ad_Form_New extends Cl_Form
{
	public function init()
	{
		parent::init();
		$this->fieldList = array('avatar', 'name', 'content', 'status');
		$this->setCbHelper('Ad_Form_Helper');
		
	}
	//protected $_fieldListConfig; @see Cl_Dao_Foo
	public function setStep($step)
	{
	    if ($step == 'checklist')
    		$this->fieldList = array(
    		);
	    
		parent::setStep($step);
	}
	
    protected $_formFieldsConfig = array(
    	'name' => array(
    		'type' => 'Text',
    		'options' => array(
    			'label' => "Ad name",
    			'required' => true,
	    		'filters' => array('StringTrim', 'StripTags'),
                'validators' => array('NotEmpty'),
    		),
            //'permission' => 'update_task'
    	),
    	'content' => array(
    		'type' => 'Textarea',
    		'options' => array(
    	        'label' => "Ad Content",
    	        'class' => 'isEditor',
	    		'filters' => array('StringTrim', 'StoryPost'),
    			'prefixPath' => array(
    				"filter" => array (
    					"Filter" => "Filter/"
    				)
    			)
    		),
    	),
        'status' => array(
        		'type' => 'Select',
        		'options' => array(
        				'label' => 'Status',
        				'required' => true,
        		),
        		'multiOptionsCallback' => array('getStatus')
        ),
    	'avatar' => array(
    			'type' => 'Hidden',
    			'options' => array(
    					'class' => 'cl_upload',
    					'filters' => array('StringTrim', 'StripTags')
    			),
    	)
        
    	
    );
    
    /**TODO: hook here if needed
    public function customIsValid($data)
    {
        return array('success' => false, 'err' => "If customIsValid exist. You must implement it");
    }
    */
}
