<?php
class Dao_Node_Concept extends Cl_Dao_Node
{
    public $cSchema = array(
    		'name' => 'string',
    		'iid' => 'string',
    		'transcribe' => 'mixed',//phiên âm
    		'avatar' => 'string',
    		//add other stuff u want
    );
        
	protected function _configs(){
    	$user = Dao_User::getInstance()->cSchema;
	    return array(
    		'collectionName' => 'concept',
        	'documentSchemaArray' => array(
        		'iid' => 'int', //incremental sql-like uniq id
        		'name' => 'string', //eg: across 
	    		'vname' => 'string', //eg: bang qua
        		'avatar' => 'string',		
        		'transcribe' => 'mixed',//phiên âm
        		'u' => $user, //who posted this
        		'ts' => 'int',
        		'status' => 'string', //[queued,p,s]
        	    'type_dict' => 'int', // = 1=> anh-viet, 2=> viet-anh
	    		'type' => 'string', //eg: 'dictionary', | 'idiom' | 'sentence'| 
                'tuloai' => 'mixed',
	            /*'tuloai' => array( 
	                array(
	                    'name' => 'string', //eg: danh tu
	                    'meanings' => array(
	                        'name' => 'string',  //eg: qua, ngang, ngang qua
	                        'example' => array(
	                            array(
    	                            'name' => 'string',//eg: a bird is flying across
    	                            'vname' => 'string'// eg: một con chim đang bay ngang qua
	                            )
	                        ),
	                    ),
	                    'idioms' => 'mixed',  
	                ),
	            ),*/
	            
	           /*'idioms' => array(
	                array(
	                    'name' => 'string',//eg: to put it across somebody
	                    'meanings' => array(
    	                    'name' =>  'string', //eg: '(từ lóng) trả thù ai','đánh lừa ai',...  
                            'example' => array(
                                array(
    	                            'name' => 'string',
    	                            'vname' => 'string'
                                )
                            ),
	                    )
	                ),
	            ),*/
        	),
    		'indexes' => array(
    					array(
    							array('iid' => 1),
    							array("unique" => true, "dropDups" => true)
    					)
    			)
    	);
	}
	/**************************paserDicFileInsertToConcept*************************************/
	/**
     * Get name:
     * Là chuỗi đầu tiên sau ký tự @ cho tới ký tự /
     * 
     */ 
    /**
     * Get transcribe
     * Là chuỗi nằm cùng dòng với tên concept nằm giữa hai ký tự // eg: /eibai'ɔdʤinist/
     */

    //Lay ra danh sach tu loai hoac cau truc tu
    /**
     * Get tuloai array
     * 	+ Bat dau bang ky tu *:
     * 	+ Ket thuc bang ky tu * o dau dong(mo dau cho tuloai khac)
     *          hoac bang ky tu ! o dau dong cua cau truc tu
     *          hoac bang ky tu @ o dau dong cua mot concept moi
     *   
     *  + Lay du lieu:
     *  	+ name: 
     *  		La 1 chuoi cac ky tu (trong 1 dong) sau ky tu * o dau dong
     *  	+ meaning:(array)
     *  		- Bat dau bang ky tu - o dau dong
     *  		- Ket thuc bang ky tu - o dau dong (bat dau den 1 detail khac)
     *  			- name: 
     *  					+ la 1 chuoi (trong 1 dong) bat dau bang ky tu - o dau dong
     *  			- example: (array)
     *  				* Bat dau bang ky tu = va ket thuc bang ky tu /n
     *  					+ name: Bat dau bang ky tu = 
     *  							 Ket thuc bang ky tu +
     *  					+ vname: bat dau bang ky tu + 
     *  								 ket thuc bang ky tu /n
     *  		
     */
    
    /**
     * Get idioms array
     * 	+ Bat dau bang ky tu ! o dau dong
     * 	+ ket thuc bang ky tu * o dau dong(mo dau cho tuloai)
     *          hoac bang ky tu ! o dau dong cua cau truc tu khac
     *          hoac bang ky tu @ o dau dong cua mot concept moi
     *      - name: Bat dau bang ky tu ! o dau dong ket thuc bang ky tu /n
     *      - meaning:
     *      		+ Bat dau bang ky tu -
     *      		+ Ket thuc bang ky tu - cua detail khac
     *      		name: 
     *      			+ Bat dau bang ky tu - 
     *      			+ Ket thuc bang ky tu /n
     *      		example: bat dau bang ky tu = ket thuc bang ky tu /n
     *      			 + name: * Bat dau bang ky tu =
     *      					  * Ket thuc bang ky tu +
     *      			 + meanExEnd: * Bat dau bang ky tu +
     *      						  * Ket thuc bang ky tu /n	
     *      				    
     *          
     */
    
    /**
     * Luu y: Co the tuloai va struct_char (cau truc tu) co thu tu
     * xao tron
     * */
	
	public function paserDicFileInsertToConcept(){
	    $file_dict_name = get_conf('file_dict_name','anhviet109K.dict');
	    $file = SITE_URL . '/' . $file_dict_name;
        $file_content = file_get_contents($file, true);	    //concept
	    
	    //GET CONCEPT LIST
	    $pattern = '@';
	    $file_content = substr(trim($file_content),4); // @ tuong duong voi 3 ky tu
	    $conceptList = explode($pattern, $file_content);
	    $newConceptList = array();
	    
	    //GET DETAIL CONCEPT
	    foreach ($conceptList as $c){
	        if(isset($c) && !empty($c)){ //check $c not null
    	        //TACH CONCEPT THANH TUNG DONG
    	        $lines = explode("\n", $c);
    	        $transcribeList = array();
    	        
    	        //GET NAME AND TRANSCRIBE
    	        $nameAndTranscribe = $lines[0];
    	        $list = explode('/', $nameAndTranscribe);
    	        
    	        //Get name
    	        $nameConcept = trim($list[0]);
    	        
    	        //Get transcribe// phien am cua concept
    	        if(count($list) > 1){
    	            for($i = 1;$i < count($list); $i ++){
    	                if($list[$i] != '')
    	                {
    	                    $transcribeList[] = trim($list[$i]);
    	                }
    	            }
    	        }
    	        
	            list($tuloaiList,$tuloai,$charStructList,$charStruct) =  $this->initData();
    	        
    	        //Khoi tao lai du lieu:
	            list($example,$exampleList,$detail,$detailList) =  $this->initData();

	            $type = 0;// if type = 1 => tu loai, type = 2 => cau truc tu
    	        //GET INFO CONCEPT DETAIL
    	        if(count($lines) > 0){
        	        for ($i = 1;$i < count($lines); $i ++){
            	        $line = trim($lines[$i]);
            	        //Lấy ra ký tự đầu tiên của một dòng
            	        $first = $line[0];
            	        if($first == '*'){ //LA MOT TU LOAI MOI
            	            list($exampleList,$detail,$detailList) = $this->addDetailToDetailList($exampleList,$detail,$detailList);
            	            
                            //Them chi tiet vao tuloai hoac cau truc tu
                            list ($tuloai,$charStruct,$type,$detailList,$tuloaiList,$charStructList) =  $this->addDetailToList($tuloai,$charStruct,$type,$detailList,$tuloaiList,$charStructList,1);            	                
            	            
        	                //Khoi tao lai du lieu:
            	            list($example,$exampleList,$detail,$detailList) =  $this->initData();
        	                
            	            //Lay ra ten tu loai
        	                $tuloai['name'] = trim(substr($line, 1));
        	                $type = 1;
            	        }elseif ($first == '!'){ //LA MOT CAU TRUC TU MOI
        	                list($exampleList,$detail,$detailList) = $this->addDetailToDetailList($exampleList,$detail,$detailList);
            	                
                            //Them chi tiet vao tuloai hoac cau truc tu
                            list($tuloai,$charStruct,$type,$detailList,$tuloaiList,$charStructList) =  $this->addDetailToList($tuloai,$charStruct,$type,$detailList,$tuloaiList,$charStructList,2);
            	            
                            //Khoi tao lai du lieu:
            	            list($example,$exampleList,$detail,$detailList) =  $this->initData();

            	            //Lay ra ten cau truc tu
            	            $charStruct['name'] = trim(substr($line, 1));
            	            $type = 2; 
            	        }elseif ($first == '-'){//NGHIA CHO 1 TU LOAI HOAC CAU TRUC TU O TREN
            	            list($exampleList,$detail,$detailList) = $this->addDetailToDetailList($exampleList,$detail,$detailList);
            	            
            	            //Lay ra nghia cua detail lan nay
            	            $detail['name'] = trim(substr($line, 1));
            	        }elseif ($first == '='){//VI DU CHO NGHIA CUA TU LOAI HAY CAU TRUC TU DO
            	            //Lay ra ten vi du , nghia cua vi du do
            	            $exString = trim(substr($line, 1));
            	            $exString = explode('+',$exString);
            	            
            	            //Phan tich ra vi du va nghia cua vi du do
            	            $cInsert['name'] = $example['name'] = trim($exString[0]);
            	            $cInsert['vname'] = $example['vname'] = trim($exString[1]);
            	            $cInsert['type'] = $example['type'] = 'sentence';
            	            $cInsert['ts'] = time();

            	            //Insert into databse
            	            $r = $this->insert($cInsert);
            	            $cInsert = array();
            	            
            	            //GET info from database
            	            if($r['success']){
            	                $example['id'] = $r['result']['id'];
            	            }
            	            
        	                $exampleList[] = $example;
            	        }elseif ($first == ''){ //Bo qua
            	            
            	        }
        	        }
    	        }
    	        
    	        //Them vao concept tu loai hoac cau truc tu cuoi cung
    	        list($exampleList,$detail,$detailList) = $this->addDetailToDetailList($exampleList,$detail,$detailList);
    	        $exampleList = array();
                $detail= array();
	                
	           //Da ket thuc 1 tu loai hoac 1 cau truc tu dua vao trong danh sach
               //Them chi tiet vao tuloaihoac cau truc tu
                list ($tuloai,$charStruct,$type,$detailList,$tuloaiList,$charStructList) =  $this->addDetailToList($tuloai,$charStruct,$type,$detailList,$tuloaiList,$charStructList,1);            	                
            
	            //Khoi tao lai du lieu:
	            list($example,$exampleList,$detail,$detailList) =  $this->initData();
        	                
                //DUA CAC BIEN VAO TRONG CHI TIET CONCEPT
                $concept['type_dict'] = get_conf('type_dict',1);
                if($nameConcept != null)
    	            $concept['name'] = $nameConcept;
	            if($transcribeList != null)
    	            $concept['transcribe'] = $transcribeList;
	            if($tuloaiList != null){
    	            $concept['tuloai'] = $tuloaiList;
        	        //Lay ra duoc vname
        	        /**
        	         * Nghia dau tien cua tu loai dau tien
        	         */
    	            
    	            foreach ($tuloaiList as $t){
    	                if($t['meanings']){
    	                    foreach ($t['meanings'] as $m){
    	                        if ($m['name'])
    	                        {
    	                            $concept['vname'] = $m['name'];
    	                            break;
    	                        }
    	                    }
    	                    break;
    	                }
    	            }
	            }
    	        //if($charStructList != null)
    	            //$concept['idioms'] = $charStructList;
    	        $concept['type'] = 'dictionary';
    	        if($concept != null)
    	            $newConceptList[] = $concept;
    	         
	            //insert concept to database
	            $concept['ts'] = time();
	            $r = $this->insert($concept);
	        }
	    }
	}
	
	/**
	 * @see Cl_Dao_Node::beforeInsertNode()
	 */
	public function beforeInsertNode($data){

	}
	
	/**
	 * @see Cl_Dao_Node::afterInsertNode()
	 */
	public function afterInsertNode($data, $row){
	    
	}
	
	/**
	 * @see Cl_Dao_Node::afterUpdateNode()
	 */
	public function afterUpdateNode($where, $data, $currentRow){
	    
	}
	
	/**
	 * @see Cl_Dao_Node::afterDeleteNode()
	 */
	
	public function afterDeleteNode($row){
	    
	}
	
	public function addDetailToList($tuloai,$charStruct,$type,$detailList,$tuloaiList,$charStructList,$tuloaiOrIdiom){
	    if(!empty($detailList) && $detailList != null && $type == 1){//Dua tu loai vao trong danh sach
            $tuloai['meanings'] = $detailList;
        }elseif (!empty($charStruct) && $charStruct != null && $type == 2){//Dua cau truc tu vao trong danh sach
            $vname = '';
            if(count($detailList) > 0){
                foreach ($detailList as $d){
                    if($d['name'] != ''){
                        $vname = $d['name'];
                        break; 
                    }
                }
            }
            $conceptInsert['name'] = $charStruct['name'];
            $conceptInsert['type'] = $charStruct['type'] = 'idiom';
            $conceptInsert['vname'] = $charStruct['vname'] = $vname;
            $conceptInsert['ts'] = $charStruct['ts'] = time();
            
            //Insert into databse
            $r = $this->insert($conceptInsert);
            $conceptInsert = array();
            
            //GET info from database
            if($r['success']){
                $charStruct['id'] = $r['result']['id'];
            }
            
            //Dua idioms vao trong tu loai
            $charStruct['meanings'] = $detailList; 
            $charStructList[] = $charStruct;

            //Khoi tao lai $charStruct
            $charStruct = array();
        }
        
        if($tuloaiOrIdiom == 1){
            //Them idiom vao tu loai
            if($charStructList != NULL && !empty($charStructList)){
                $tuloai['idioms'] = $charStructList;
            
                //Gan idiom bang rong
                $charStructList = array();
            }
            
            if($tuloai != NULL && !empty($tuloai)){
                $tuloaiList[] = $tuloai;
                
                //Khoi tao lai $tuloai
                $tuloai = array();
            }
        }
        return array ($tuloai,$charStruct,$type,$detailList,$tuloaiList,$charStructList);
	}
	
	public function initData(){
	    return array(array(),array(),array(),array());
	}
	
	public function addDetailToDetailList($exampleList,$detail,$detailList){
	    if(!empty($exampleList) && $exampleList != null){//Lay ra vi du cuoi cung cua $exampleList
            $detail['example'] = $exampleList;     
            $exampleList = array();     	                
        }
            
        if(!empty($detail) && $detail != null) //Lay ra chi tiet cuoi cung cua $detailList
        {
            $detailList[] = $detail;
            $detail = array();
        }
            
        return array($exampleList,$detail,$detailList);
	}
}
?>