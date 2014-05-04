<?php 
class Story_Form_Update extends Story_Form_New
{
    public function init()
    {
        parent::init();
    }
    public function setStep($step, $currentRow = '')
    {
        //v($currentRow['type']);
        //die;
        
        if($step==''){
            if ($currentRow['type'] == 1 ) //story
            {
                $step='story';
                $this->fieldList = array('name', 'content','source', 'status');
            }
            elseif ($currentRow['type'] == 2){ //image
                $step='image';
                $this->fieldList= array('name','url','upload_img','content','source','type','status','seo.title','seo.desc','images','images_deleted', 'img_canvas');
            }
            elseif($currentRow['type'] == 3){ //video
                $step='video';
                $this->fieldList=array('name','url','content','source','type','status','ytid','seo.title','seo.desc');
            }
        }
        elseif ($step == 'status')
        {
            $this->fieldList = array('status');
        }
        elseif ($step == 'feature'){
        	//v($step);die();
        	$this->fieldList = array('is_feature');
        };
        $this->setCbHelper('Story_Form_Helper');
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
        
        if ($this->step == 'feature' || $this->step == 'status')
        {
        	if (!has_perm('admin_story'))
        	{
        		return array('success' => false, 'err' => 'you do not have permssion to set feature');
        	}
        	else 
        		return array('success' => true); 
        }
        else
        {
        	if ($currentRow['u']['id'] != $lu['id'])
        		assure_perm('admin_story');
        	else //user la ng post truyen nay
        		assure_perm('update_own_story');
        	 
        	return array('success' => true);
        }
        
        return array('success' => false);
    }
}