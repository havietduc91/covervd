<?php 
class Category_Form_New extends Cl_Form
{
	public function init()
	{
		parent::init();
		$this->fieldList = array('avatar', 'name', 'content', 'status', /*'parent_category',*/ 'slug');
		$this->setCbHelper('Category_Form_Helper');
		
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
        			'label' => "Tên chuyên mục",
        			'required' => true,
    	    		'filters' => array('StringTrim', 'StripTags'),
                    'validators' => array('NotEmpty'),
        		),
                //'permission' => 'update_task'
        	),
        	'slug' => array(
        			'type' => 'Text',
       				'options' => array(
       						'label' => "Slug eg: dien-lanh",
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
        	'level' => array(
        			'type' => 'Select',
       				'options' => array(
       						'label' => 'Level',
       						'required' => true,
       				),
        			'multiOptionsCallback' => array('getLevel')
       		),
        	'avatar' => array(
        			'type' => 'Hidden',
        			'options' => array(
        					'class' => 'cl_upload',
        					'filters' => array('StringTrim', 'StripTags')
        			),
        	),
        	'parent_category' => array(
        			'type' => 'Select',
       				'options' => array(
       						'label' => 'Parent Category',
       						//'required' => true,
       				),
       				'multiOptionsCallback' => array('getParentCategory')
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
