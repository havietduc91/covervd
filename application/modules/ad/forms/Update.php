<?php 
class Ad_Form_Update extends Ad_Form_New
{
    public function init()
    {
        parent::init();
    }
    public function setStep($step, $currentRow = '')
    {
        if ($step == '' )
        {
            $this->fieldList = array('name', 'content', 'status');
        }
        elseif ($step == 'status')
        {
            $this->fieldList = array('status');
        }
        $this->setCbHelper('Ad_Form_Helper');
	    parent::setStep($step);
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
            assure_perm('update_own_ad');
            return array('success' => true, 'err' => "Permission failed in Ad");
        }
        
        return array('success' => true);
    }
}