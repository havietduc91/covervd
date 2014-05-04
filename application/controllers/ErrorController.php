<?php
class ErrorController extends Cl_Controller_Action_Index
{
    public function errorAction()
    {
        //v(get_class_methods('Zend_View_Exception'));
        if (APPLICATION_ENV == 'development')
        {
            $exceptions = $this->_response->getException();
            foreach ($exceptions as $e) 
                //v($this->_response->getException());
                v($e->getMessage());
	        DIE();
        }
    }
}