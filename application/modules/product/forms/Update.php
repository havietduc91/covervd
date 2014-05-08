<?php 
class Product_Form_Update extends Product_Form_New
{
    public function init()
    {
        parent::init();
    }
    public function setStep($step, $currentRow = null)
    {
        $this->setCbHelper('Product_Form_Helper');
	    parent::setStep($step, $currentRow);
	    if($step = '')
	    {
           $this->fieldList = array(
                'supplierName','name', 'model','serialNumber', 'receivedDate',
                'images', 'images_deleted','img_canvas','upload_img',
                'note','description','saledate_start','saledate_end',
                'deal_price','origin_price','parent_category_id',
    			'status',
    		);
	    }
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
        assure_perm('root');
        /*
        if ($this->step == '') //a full update
        {
            assure_perm('update_own_product');
            return array('success' => true, 'err' => "Permission failed in Product");
        }
        */
        return array('success' => true);
    }
}