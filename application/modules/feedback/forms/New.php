<?php 
class Feedback_Form_New extends Cl_Form
{
	public function init()
	{
		parent::init();
		$this->fieldList = array('overview', 'interface', 'used', 'payment', 'uname', 'umail', 'more', 'status');
		$this->setCbHelper('Feedback_Form_Helper');
		
	}
	public function setStep($step, $currentRow = null)
	{
		parent::setStep($step, $currentRow);
	}
	
    protected function _formFieldsConfigCallback()
    {
        $ret = array(
        	'overview' => array(
        		'type' => 'Select',
            		'options' => array(
            				'label' => 'Tổng quan về hài lòng của khách hàng',
            				'required' => true,
            		),
            		'multiOptionsCallback' => array('getStatusFeedback')
                //'permission' => 'update_task'
        	),
        	'interface' => array(
        		'type' => 'Select',
            		'options' => array(
            				'label' => 'Giao diện',
            				'required' => true,
            		),
            		'multiOptionsCallback' => array('getStatusFeedback')
        	),
        	'used' => array(
        		'type' => 'Select',
            		'options' => array(
            				'label' => 'Dễ sử dụng',
            				'required' => true,
            		),
            		'multiOptionsCallback' => array('getStatusFeedback')
        	),
        	'payment' => array(
        		'type' => 'Select',
            		'options' => array(
            				'label' => 'Hình thức thanh toán đa dạng thuận tiện',
            				'required' => true,
            		),
            		'multiOptionsCallback' => array('getStatusFeedback')
        	),
        	'uname' => array(
        		'type' => 'Text',
        		'options' => array(
        			'label' => "Tên",
        			'required' => true,
        			'filters' => array('StringTrim', 'StripTags'),
        			'validators' => array('NotEmpty'),
        		),
        	),
        	'umail' => array(
        		'type' => 'Text',
        		'options' => array(
        			'label' => "Email",
        			'required' => true,
        			'filters' => array('StringTrim', 'StripTags'),
        			'validators' => array('NotEmpty'),
        		),
        	),
        	'more' => array(
        		'type' => 'Textarea',
        		'options' => array(
        	        'label' => "Chú ý thêm",
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
            				'label' => 'Status',
            				'required' => true,
            		),
            		'multiOptionsCallback' => array('getStatus')
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
