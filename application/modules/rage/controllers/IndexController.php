<?php
class Rage_IndexController extends Cl_Controller_Action_NodeIndex {
	
    public function init() {
		//parent::init ();
	}
	
	public function indexAction()
	{
	    
	}
	
	public function memeAction()
	{
	     
	}
	
	public function uploadMemeAction()
	{
	    $img = $this->getStrippedParam('imageData');
	    $name = $name?$this->getStrippedParam('name'):"meme";
	    $img = str_replace('data:image/png;base64,', '', $img);
	    $img = str_replace(' ', '+', $img);
	    $data = base64_decode($img);
	    $imgName = uniqid();
	    $file = STATIC_PATH . '/rage/'.$imgName.'.png';
	    $success = file_put_contents($file, $data);
	    if($success)
	    {
	        $r = array('success' => true, 'imgUrl' => STATIC_CDN."/rage/".$imgName.'.png' ,'name' => $name);
	        echo json_encode($r);
	    }
	    exit();
	}
	
	public function downloadAction()
	{
	    $img = $this->getStrippedParam('imageData');
	    $name = $this->getStrippedParam('name');
	    $img = str_replace('data:image/png;base64,', '', $img);
	    $img = str_replace(' ', '+', $img);
	    $data = base64_decode($img);
	    $imgName = uniqid();
	    $file = STATIC_PATH . '/rage/'.$imgName.'.png';
	    $success = file_put_contents($file, $data);
	    
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$name");
        header("Content-Type: image/png");
        header('Content-Disposition: attachment; filename='.basename($file));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: public');
        header('Pragma: public');
        
        readfile($file);
        //include('../watermark.php');
        exit;
	}
	
}

