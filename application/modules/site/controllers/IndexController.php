<?php
class Site_IndexController extends Cl_Controller_Action_Index
{
    public function indexAction()
    {
    	/**Get categories*/
    	$categories = Dao_Node_Category::getInstance()->getCategoryLevelOne();
    	$this->setViewParam('categories', $categories);

    	/***Get hompage products***
    	 * Sản phẩm được SEO gợi ý đưa lên top 1
    	 * Sản phẩm này sẽ được thay đổi mỗi ngày
    	 * */
    	
    	$iid = get_conf('home_page_product_iid', 17);
    	$hp_product = Dao_Node_Product::getInstance()->getHomePageProduct($iid);
    	$this->setViewParam('hp_product', $hp_product);
    	
    	/****Get products recommend**************
    	 * Cac san pham goi y se duoc admin config
    	 * recommend_products
    	 * */
    
    	$recommend_products_iid = get_conf('recommend_products_iid', '13,14,15,12');
    	$recommend_products = Dao_Node_Product::getInstance()->getRecommendProduct($recommend_products_iid);
    	 
    	$this->setViewParam('recommend_products', $recommend_products);
    	
    	/**LẤY DANH SÁCH SẢN PHẨM THEO IID ĐƯỢC CONFIG CỦA DANH SÁCH CHUYÊN MỤC :: STYLE 1**
  				Name_cofig:: category_iid_style1
  		*/
  		$category_iid_style1 = get_conf('category_iid_style1','11');
  		$style1_products = Dao_Node_Product::getInstance()->getProductsByCategoryIid($category_iid_style1);
  		
  		$this->setViewParam('style1_products', $style1_products);
  		
  		
  		/**LẤY DANH SÁCH SẢN PHẨM THEO IID ĐƯỢC CONFIG CỦA DANH SÁCH CÁC CHUYÊN MỤC :: LOẠI 1**/
  		/**	
  			Name_cofig:: products_categories_iids1
  		 **/
  		
  		$category_iids_style1 = get_conf('products_categories_iids1','11,12,13');
  		$categories = Dao_Node_Product::getInstance()->getProductsByCategorysIids($category_iids_style1, '11');
  		
  		$this->setViewParam('categories_iids1', $categories);
  		
  		/**LẤY DANH SÁCH SẢN PHẨM THEO IID ĐƯỢC CONFIG CỦA DANH SÁCH CHUYÊN MỤC :: STYLE 1**
  		 Name_cofig:: category_iid_style1
  		*/
  		$category_iid_style2 = get_conf('category_iid_style2','11');
  		$style2_products = Dao_Node_Product::getInstance()->getProductsByCategoryIid($category_iid_style2);
  		
  		$this->setViewParam('style2_products', $style2_products);
  		
  		/**TODO::LẤY DANH SÁCH CHUYÊN MỤC PHỔ BIẾN**/
  		
  		
  		/**LẤY DANH SÁCH SẢN PHẨM THEO IID ĐƯỢC CONFIG CỦA DANH SÁCH CÁC CHUYÊN MỤC :: LOẠI 2**/
  		/**
  		 	Name_cofig:: products_categories_iids2
  		 **/
  		$category_iids_style2 = get_conf('products_categories_iids2','1,2,11');
  		$categories = Dao_Node_Product::getInstance()->getProductsByCategorysIids($category_iids_style2, '2');
  		
  		$this->setViewParam('categories_iids2', $categories);
  		
        Bootstrap::$pageTitle = "Trang chủ - Giá Tốt";
    }
	public function errorAction()
	{
		
	}
    //==========================ADMIN==================================
    public function installAction()
    {
    	assure_perm('sudo');
    	$this->setLayout("admin");
    	if ($this->isSubmitted())
    	{
    		Cl_Dao_Util::getUserDao()->installSite();
    	}
    }
    
    public function adminAction()
    {
        assure_perm('sudo');
        $this->setLayout("admin");
        Bootstrap::$pageTitle = "Admin Panel";
    }
}
