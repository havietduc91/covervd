<?php 
class Contact_Form_New extends Cl_Form
{
	public function init()
	{
		parent::init();
		$this->fieldList = array('name', 'city', 'status', 'phone', 
					'info', 'company', 'email');
		$this->setCbHelper('Contact_Form_Helper');
		
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
        			'label' => "Họ và tên",
        			'required' => true,
    	    		'filters' => array('StringTrim', 'StripTags'),
                    'validators' => array('NotEmpty'),
        		),
                //'permission' => 'update_task'
        	),
        	'info' => array(
        		'type' => 'Textarea',
        		'options' => array(
        	        'label' => "Thông tin liên hệ",
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
        	'phone' => array(
        		'type' => 'Text',
        		'options' => array(
        			'label' => "Số điện thoại",
        			'required' => true,
        			'filters' => array('StringTrim', 'StripTags'),
        			'validators' => array('NotEmpty'),
        		),
        	),
        	'email' => array(
        		'type' => 'Text',
        		'options' => array(
        			'label' => "Email",
        			'required' => true,
        			'filters' => array('StringTrim', 'StripTags'),
        			'validators' => array('NotEmpty'),
        		),
        	),
        	'company' => array(
        		'type' => 'Text',
        		'options' => array(
        			'label' => "Tên công ty",
        			'required' => true,
        			'filters' => array('StringTrim', 'StripTags'),
        			'validators' => array('NotEmpty'),
        		),
        	),
        	'city' => array(
        		'type' => 'Text',
        		'options' => array(
        			'label' => "Thành phố",
        			'required' => true,
        			'filters' => array('StringTrim', 'StripTags'),
        			'validators' => array('NotEmpty'),
        		),
        	),
        	
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
