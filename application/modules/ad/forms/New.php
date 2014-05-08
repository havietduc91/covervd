<?php 
class Ad_Form_New extends Cl_Form
{
	public function init()
	{
		parent::init();
		$this->fieldList = array('avatar', 'name', 'content', 'status');
		$this->setCbHelper('Ad_Form_Helper');
		
	}
	public function setStep($step, $currentRow = null)
	{
		parent::setStep($step, $currentRow);
	}
	
    protected function _formFieldsConfigCallback()
    {
        $ret = array(
        	'name' => array(
        		'type' => 'Text',
        		'options' => array(
        			'label' => "Tên quảng cáo",
        			'required' => true,
    	    		'filters' => array('StringTrim', 'StripTags'),
                    'validators' => array('NotEmpty'),
        		),
                //'permission' => 'update_task'
        	),
        	'content' => array(
        		'type' => 'Textarea',
        		'options' => array(
        	        'label' => "Nội dung",
        	        'class' => 'isEditor',
    	    		'filters' => array('StringTrim', 'NodePost'),
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
            				'label' => 'Trạng thái',
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
        return $ret;
    }
    /**TODO: hook here if needed
    public function customIsValid($data)
    {
        return array('success' => false, 'err' => "If customIsValid exist. You must implement it");
    }
    */
}
