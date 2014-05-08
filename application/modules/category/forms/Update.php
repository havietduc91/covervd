<?php 
class Category_Form_Update extends Category_Form_New
{
    public function init()
    {
        parent::init();
    }
    public function setStep($step, $currentRow = null)
    {
        if ($step == '' )
        {
			$this->fieldList = array('avatar', 'name', 'content', 'status', 'parent_category', 'slug', 'level');
        }
        elseif ($step == 'status')
        {
            $this->fieldList = array('status');
        }
        elseif ($step == 'is_level')
        {
        	$this->fieldList = array('level');
        }
        $this->setCbHelper('Category_Form_Helper');
	    parent::setStep($step, $currentRow);
    }
    
    /**
     * TODO: lots of checking here
     * Update a node can be divided into multiple step,each step updating certain fields
     * Depending on the step $this->step, we can check for the permission here
     * @param $data :Form data . For example $data['marker__id'] for field "marker.id" 
     * @see Cl_Form::customPermissionFilter()
     */
    public function customPermissionFilter($data, $currentRow)
    {
        $lu = Zend_Registry::get('user');
        if ($this->step == '') //a full update
        {
            assure_perm('sudo');
            return array('success' => true, 'err' => "Permission failed in Category");
        }
        
        return array('success' => true);
    }
}