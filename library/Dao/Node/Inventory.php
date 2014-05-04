<?php
class Dao_Node_Inventory extends Cl_Dao_Node
{
    public $hotnessRanking = false;
    
    public $nodeType = 'inventory';
    
    public $cSchema = array('id' => 'string', 'iid' => 'string', 'name' => 'string', 'avatar' => 'string'
    //add other stuff u want
        );
    
    protected function _configs()
    {
        //$user = Dao_User::getInstance()->cSchema;
        $user = array(
            'id' => 'string',
            'name' => 'name'
        );
        return array(
            'collectionName' => 'inventory',
            'documentSchemaArray' => array(
                'iid' => 'int', //incremental sql-like uniq id
                'SupplierName' => 'string',
                'EbayItemNo' => 'longint',
                'Model' => 'string',
                'Condition' => 'enum',
                'COO' => 'string',
                'SerialNumber' => 'string',
                'SmartNet' => 'string',
                'Location' => 'string',
                'ReceivedDate' => 'int', // unix timestamp , at 00:00:00 of that date    
                'SoldDate' => 'int', // unix timestamp , at 00:00:00 of that date
                'StockStatus' => 'string', // 0 NOTINStock, 1 => InStock, 2 => Missing
                'Note' => 'array',
                'images' => array(
                    array(
                        'id' => 'string',
                        'ext' => 'string',
                        'url' => 'string',
                        'path' => 'string'
                    )
                ),
                'weight' => 'float'
            ),
            'indexes' => array(
                array(
                    array(
                        'iid' => 1
                    ),
                    array(
                        "unique" => true,
                        "dropDups" => true
                    )
                ),
                array(
                    array(
                        'SerialNumber' => 1
                    ),
                    array(
                        "unique" => true,
                        "dropDups" => true
                    )
                )
            )
        );
    }
    
    
    /**
     * Add new node
     * @param post data Array $data
     */
    public function beforeInsertNode($data)
    {
        
        //TODO: check serialnumber length > 5 ,check is EbayItemNo enough 12 digits ?
        //TODO: check has Model end with V0X ? 
        
        if (!empty($data['EbayItemNo'])) {
            if (strlen($data['EbayItemNo']) !== 12) {
                return array(
                    'success' => false,
                    'err' => "EbayItemNo: {$data['EbayItemNo']} <div style='color :red'>must be enough 12 digits</div>"
                );
            }
        }
        
        if (!empty($data['COO'])) {
            if (strlen($data['COO']) !== 2) {
                return array(
                        'success' => false,
                        'err' => "COO: {$data['COO']} <div style='color :red'>has more than 2 chars.</div>"
                );
            }
        }
        
        if (preg_match('/V[0-9]{2}$/', $data['Model'])) {
            if (!empty($data['Note'])) {
                array_push($data['Note'], '| Model Note:' . substr($data['Model'], -3) . '|');
            }
            else 
            {
                $data['Note'] = array('Model Note:' . substr($data['Model'], -3));
            }
            
            $data['Model'] = preg_replace('/V[0-9]{2}$/', '', $data['Model']);
        }
        //remove leading 'S' if SerialNumber has 12 chars
        if (strlen($data['SerialNumber']) >= 12)
            $data['SerialNumber']=preg_replace('/^S/', '', $data['SerialNumber']);
        
        $where = array(
            'SerialNumber' => $data['SerialNumber']
        );
        foreach ($where as $index => $value) {
            if (strlen($value) >= 5 && strlen($value)<=30) {
                $r = $this->findOne($where);
                if ($r['success'] && $r['count'] = 1) {
                    //Serial already exists
                    return array(
                        'success' => false,
                        'err' => "SerialNumber {$data['SerialNumber']} already exists"
                    );
                }
                $data['StockStatus'] = 1;
                if (isset($data['SoldDate'])) {
                    //if = 01/01/2000 
                    //$data['InStock'] = 1;
                    //
                }
                return array(
                    'success' => true,
                    'result' => $data
                );
                
                
            } else
                return array(
                    'success' => false,
                    'err' => "SerialNumber {$data['SerialNumber']} over 30chars",
                    'result' => $data
                );
        }
    }
    
    public function afterInsertNode($data, $row)
    {
        //$data = array('SerialNumber' => '7');
        parent::afterInsertNode($data, $row);
        return array(
            'success' => true,
            'result' => $row
        );
    }
    
    /******************************UPDATE****************************/
    public function beforeUpdateNode($where, $data, $currentRow)
    {
        if (!empty($data['$set']['Model'])) {
            // v($data['$set']['Model']);
            if (preg_match('/V0[A-Z0-9]$/', $data['$set']['Model'])) {
                array_push($data['$set']['Note'], ' | Model Note:' . substr($data['$set']['Model'], -3) . '|');
                $data['$set']['Model'] = preg_replace('/V0[A-Z0-9]$/', '', $data['$set']['Model']);
            }
        }
        if (!isset($data['$set']['_cl_multiple']) || $data['$set']['_cl_multiple'] == '') //updating single item
            {

                if (!empty($data['$set']['EbayItemNo'])) {
                    if (strlen($data['$set']['EbayItemNo']) !== 12) {
                        return array(
                                'success' => false,
                                'err' => "EbayItemNo: {$data['$set']['EbayItemNo']} <div style='color :red'>is not enough 12 digits</div>"
                        );
                    }
                }
                
                if (!empty($data['$set']['COO'])) {
                    if (strlen($data['$set']['COO']) !== 2) {
                        return array(
                                'success' => false,
                                'err' => "COO: {$data['$set']['COO']} <div style='color :red'>has more than 2 chars.</div>"
                        );
                    }
                }
                
                if (!empty($data['$set']['Note'])) {
                    if (preg_match('/V0[A-Z0-9]$/', $data['$set']['Model'])) {
                        array_push($data['$set']['Note'], '| Model Note:' . substr($data['$set']['Model'], -3) . '|');
                        $data['$set']['Model'] = preg_replace('/V0[A-Z0-9]$/', '', $data['$set']['Model']);
                    }
                }
                
                else {
                    if (preg_match('/V0[A-Z0-9]$/', $data['$set']['Model'])) {
                        $data['$set']['Note'][0] = 'Model Note:' . substr($data['$set']['Model'], -3);
                        $data['$set']['Model']   = preg_replace('/V0[A-Z0-9]$/', '', $data['$set']['Model']);
                    }
                }
                
                
                if ($currentRow['SerialNumber'] !== $data['$set']['SerialNumber']) {
                    //remove leading 'S' if SerialNumber has 12 chars
                    if (strlen($data['$set']['SerialNumber']) >= 12)
                    	$data['$set']['SerialNumber']=preg_replace('/^S/', '', $data['$set']['SerialNumber']);
                    //$data['$set']['SerialNumber']=preg_replace('/^S/', '', $data['$set']['SerialNumber']);
                    $where = array(
                            'SerialNumber' => $data['$set']['SerialNumber']
                    );
                    foreach ($where as $index => $value) {
                        if (strlen($value) >= 5 && strlen($value)<=30) {
                            $r = $this->findOne($where);
                            if ($r['success'] && $r['count'] = 1) {
                                //Serial already exists
                                return array(
                                        'success' => false,
                                        'err' => "SerialNumber {$data['$set']['SerialNumber']} already exists"
                                );
                            }
                            $data['StockStatus'] = 1;
                            if (isset($data['$set']['SoldDate'])) {
                                //if = 01/01/2000
                                //$data['InStock'] = 1;
                                //
                            }
                            return array(
                                    'success' => true,
                                    'result' => $data
                            );
                    
                    
                        } else
                            return array(
                                    'success' => false,
                                    'err' => "SerialNumber {$data['$set']['SerialNumber']} over 30chars",
                                    'result' => $data
                        );
                    }
                }  
                
        } else { //update multiple
            //we must $concat Note in stead of $set
            if (!empty($data['$set']['Note'])) {
                $note = $data['$set']['Note'];
                unset($data['$set']['Note']);
                if ($note != '' || isset($note)) {
                    //change $set to $concat
                    $data['$push'] = array(
                        'Note' => $note[0]
                    );
                }
            }
            
        }
        
        return array(
            'success' => true,
            'result' => $data
        );
        
    }
    
    public function afterUpdateNode($where, $data, $currentRow)
    {
        return array(
            'success' => true,
            'result' => $data
        );
    }
    
    public function deleteStaticCache($row)
    {
        if (is_string($row)) {
            $where = array(
                'id' => $row
            );
            $t     = $this->findOne($where);
            if ($t['success'])
                $row = $t['result'];
            else
                return false;
        }
        //Delete cache if exists
        $dir      = realpath(PUBLIC_PATH . '/cache/'); //add folder caches
        $filename = $dir . node_link('inventory', $row);
        if (file_exists($filename)) {
            unlink($filename);
        }
    }
    
    
    /******************************INSERT_COMMENT****************************/
    /**
     * You have $node = $data['_node'];
     */
    public function beforeInsertComment($data)
    {
        $node = $data['_node'];
        
        $data['node'] = array(
            'id' => $data['nid']
        );
        
        $data['status'] = 'queued';
        if (in_array('root', $data['_u']['roles']) || in_array('admin', $data['_u']['roles'])) {
            $data['status'] = 'approved';
        }
        if (isset($node['name']) && !empty($node['name']))
            $data['node']['name'] = $node['name'];
        else if (isset($node['content']))
            $data['node']['name'] = word_breadcumb($node['content'], CACHED_POST_TITLE_WORD_LENGTH);
        
        if (isset($data['attachments']) && (is_null($data['attachments']) || $data['attachments'] == ''))
            unset($data['attachments']);
        
        return array(
            'success' => true,
            'result' => $data
        );
    }
    
    /**
     * Delete a single node by its Id
     * @param MongoID $nodeId
     */
    public function afterDeleteNode($row)
    {
        //Delete cache if exists
        $this->deleteStaticCache($row);
        return array(
            'success' => true,
            'result' => $row
        );
    }
    
    // set karma when delete comment
    /**
     * Prepare data for new node insert
     * @param Array $dataArray
     */
    public function prepareFormDataForDaoInsert($dataArray = array(), $formName = "Inventory_Form_Add")
    {
        return $dataArray;
    }
    
    public function prepareCommentFormDataForDao($dataArray = array())
    {
        return $dataArray;
    }
    
    /******************************RELATION*********************************/
    public function beforeInsertRelation($data)
    {
        return array(
            'success' => true,
            'result' => $data
        );
    }
    
    
    public function afterInsertRelation($data, $newRelations, $currentRow)
    {
        //Delete cache if exists
        $this->deleteStaticCache($currentRow['o']['id']);
        
        parent::afterInsertRelation($data, $newRelations, $currentRow);
        
        // check cache
        $this->incActivityCountAndCheckHotnessCache(array(
            'ts' => time()
        ));
        
        return array(
            'success' => true,
            'result' => $data
        );
    }
    
    
    public function afterDeleteRelation($currentRow, $rt, $newRelations = array())
    {
        
        $dir      = realpath(APPLICATION_PATH . '/../public/' . CODENAME . '/cache/'); //add folder caches
        $filename = $dir . "/inventory/" . $currentRow['o']['id'];
        if (file_exists($filename)) {
            unlink($filename);
        }
        
        $this->incActivityCountAndCheckHotnessCache(array(
            'ts' => time()
        ));
        
        return array(
            'success' => true
        );
    }
    
    
    public function filterNewObjectForAjax($ret, $formData)
    {
        if (isset($formData['_cl_multiple']) && $formData['_cl_multiple'] != '') {
            //inserting multiple)
            //$OBJ multiple results
            $str       = '';
            $count     = 0;
            $failCount = 0;
            $failedMsg = '';
            $model     = '';
            foreach ($ret as $obj) {
                if ($obj['success']) {
                    $model = $obj['result']['Model'];
                    $str   = $str . $obj['result']['SerialNumber'] . ',';
                    $count++;
                } else {
                    $failCount++;
                    if (isset($obj['err'])) {
                        $failedMsg = $failedMsg . $obj['err'] . ',';
                    }
                }
            }
            return array(
                'count' => $count,
                'fail' => $failCount,
                'message' => $str,
                'failedMessage' => $failedMsg,
                'Model' => $model
            );
        } else //inserting single item at a time
            {
            return array(
                'Model' => $ret['Model'],
                'SerialNumber' => $ret['SerialNumber']
                //'slug' => $obj['slug'] , 
                //'type' => $obj['type'], 
                //'iid' => $obj['iid']
            );
        }
    }
    
    
    public function filterUpdatedObjectForAjax($currentRow, $step, $data, $returnedData)
    {
        $keys = array(
            'id'
        );
        
        if (!isset($data['_cl_multiple']) || $data['_cl_multiple'] == '') {
            foreach ($keys as $key)
                $ret[$key] = $currentRow[$key];
        } else {
            foreach ($currentRow as $row) {
                $r = array();
                foreach ($keys as $key) {
                    $r[$key] = $row[$key];
                }
                $ret[] = $r;
            }
        }
        return $ret;
    }
    
    
    public function uploadAndResizeImage($img, $remote = false, $server = 'local')
    {
        $absoluteFile = PUBLIC_FILES_UPLOAD_PATH . '/' . $img['id'] . '.' . $img['ext'];
        // add bg resize
        $options      = array(
            'resize' => true,
            'absolute_file' => $absoluteFile,
            'file_id' => $img['id'],
            'type' => 'image',
            'local_destination_folder' => PUBLIC_FILES_UPLOAD_PATH . '/',
            'remote' => $remote,
            'extension' => $img['ext'],
            'server' => $server
        );
        //TODO:
        Cl_File::getInstance($options)->upload();
        //Cl_File::getInstance($options)->runBackground('upload', array());
        return true;
    }
    /*************************** Calculate Hotness score*****************************************************/
    //TODO: Use doBatchJobs();
    public function calculateHotness($data)
    {
        $time = $data['ts'];
        $r    = $this->findAll();
        foreach ($r['result'] as $k => $row) {
            $row['counter']['hn']   = $row['counter']['point'] / pow((($time - $row['ts']) / 3600 + 2), 1.5);
            $r['result'][$k]['mau'] = pow((($time - $row['ts']) / 3600 + 2), 1.5);
            $this->update(array(
                'id' => $row['id']
            ), $row);
        }
        return array(
            'success' => true,
            'result' => $this->findAll()
        );
    }
    
    
    public function hasTag($tagName, $node)
    {
        foreach ($node['tags'] as $tag) {
            if ($tag['name'] == $tagName) {
                return true;
            }
        }
        return false;
    }
    
    public function isFeatured($node)
    {
        return $this->hasTag(featured_tag());
    }
    
    /******************************** Cache homepage **************************/
    /**
     * 
     */
    public function incActivityCountAndCheckHotnessCache($data)
    {
        $current_ts      = $data['ts'];
        // increase activities counter & check hotness cache
        $redis           = init_redis(RDB_CACHE_DB);
        $hotness_act     = $redis->get("hotness:activites");
        $hotness_last_ts = $redis->get("hotness:last_ts");
        
        // get config keys
        $hotness_refresh_rate         = get_conf('hotness_refresh_rate');
        $last_ts_rendering_home_cache = $redis->get('last_ts_rendering_home_cache');
        
        if (($current_ts - $last_ts_rendering_home_cache) < $hotness_refresh_rate || $hotness_act > get_conf('hotness_refresh_activites_count')) {
            $this->refreshHotness(array(
                'ts' => time()
            ));
        } else {
            // increate hotness activities counter
            $redis->incr("hotness:activites");
        }
    }
    
    public function refreshHotness($data)
    {
        // reset hotness:activites
        $redis = init_redis(RDB_CACHE_DB);
        $redis->set("hotness:activites", 0);
        
        // set hotness last time reculated
        $redis->set('last_ts_rendering_home_cache', time());
        
        $keys = $redis->keys('hotness*');
        foreach ($keys as $key)
            $this->deleteRedisCache($key);
        
        // reculate hotness
        $this->runBackground("calculateHotness", array(
            $data
        ));
        return array(
            'success' => true
        );
    }
    
    /**
     * Recalculate all hotness score & cache top 200 into redis cache key "hotness:top_iids"
     * @param unknown_type $data
     * @return multitype:boolean
     */
    public function refreshHotnessAll($data)
    {
        // reset hotness:activites
        $redis = init_redis(RDB_CACHE_DB);
        $redis->set("hotness:activites", 0);
        
        // set hotness last time reculated
        $redis->set('last_ts_rendering_home_cache', time());
        
        $keys = $redis->keys('hotness*');
        foreach ($keys as $key)
            $this->deleteRedisCache($key);
        
        // reculate hotness
        $this->runBackground("calculateHotness", array(
            $data
        ));
        
        //cache top 200 into redis cache key "hotness:top_iids"
        $nrOfTop = get_conf('nr_hotness_cache_top', 200);
        $where   = array(
            'status' => 'approved'
        );
        $order   = array(
            'counter.hn' => -1
        );
        $limit   = $nrOfTop;
        
        $cond['where'] = $where;
        $cond['order'] = $order;
        $cond['limit'] = $limit;
        $r             = $this->findAll($cond);
        if ($r['success']) {
            foreach ($r['result'] as $inventory) {
                $redis->sAdd("hotness:top_iids", $inventory['iid']);
            }
        }
        
        return array(
            'success' => true
        );
    }
    
    
    public function refreshBest()
    {
        $redis = init_redis(RDB_CACHE_DB);
        $keys  = $redis->keys('best*');
        foreach ($keys as $key)
            $this->deleteRedisCache($key);
    }
    
    
    public function refreshUpcoming()
    {
        $redis = init_redis(RDB_CACHE_DB);
        $keys  = $redis->keys('upcomming*');
        foreach ($keys as $key)
            $this->deleteRedisCache($key);
    }
    
    
    public function setVoted($r)
    {
        if ($r['success'] && $r['count'] > 0) {
            $ids = array();
            foreach ($r['result'] as $k => $row) {
                $ids[] = $row['id'];
            }
            $lu          = Zend_Registry::get('user');
            $options     = array(
                'subject' => 'user',
                'object' => 'inventory'
            );
            $relationDao = Cl_Dao_Relation::getInstance($options);
            $where       = array(
                's.id' => $lu['id'],
                'o.id' => array(
                    '$in' => $ids
                )
            );
            $sr          = $relationDao->findAll(array(
                'where' => $where
            ));
            if ($sr['success'] && $sr['count'] > 0) {
                foreach ($r['result'] as $k => $row) {
                    foreach ($sr['result'] as $key => $l) {
                        //$r['result'][$k]['isVoteUp']=false;
                        //$r['result'][$k]['isVoteDown']=false;
                        if (isset($l['s']['id']) && $row['u']['id'] == $l['s']['id'] && $row['id'] == $l['o']['id']) {
                            if ($l['r'][0]['rt'] == 1) {
                                $r['result'][$k]['isVoteUp'] = true;
                            } elseif ($l['r'][0]['rt'] == 4) {
                                $r['result'][$k]['isVoteDown'] = true;
                            }
                            break;
                        }
                    }
                }
            }
        }
        return $r;
    }
    
    public function getInventoryList($filter, $page = 1, $inventoryTypeInt = '', $tag = '', $uid = '')
    {
        $redis = init_redis(RDB_CACHE_DB);
        
        if ($filter == 'vote') {
            // find up comming inventory
            $where = array(
                'status' => 'queued'
            );
            $order = array(
                'counter.c' => -1,
                'ts' => -1
            );
        } elseif ($filter == 'picks') {
            //find featured inventory
            $where = array(
                'tags' => array(
                    '$elemMatch' => array(
                        'name' => featured_tag()
                    )
                )
            );
            $order = array(
                'ts' => -1
            );
        } elseif ($filter == 'hot') {
            //array of top 100 hot iids
            $currentHotIids = $redis->sMembers('hotness:top_iids');
            if (count($currentHotIids) == 0) {
                //TODO: generate top 100 hotness Iids
                //recalculate all hotness
                $this->refreshHotnessAll(array(
                    'ts' => time()
                ));
                $currentHotIids = $redis->sMembers('hotness:top_iids');
            }
            
            $where = array(
                'status' => 'approved',
                'iid' => array(
                    '$in' => $currentHotIids
                )
            );
            $order = array(
                'counter.hn' => -1
            );
        } elseif ($filter == 'best') //order by point
            {
            $where = array(
                'status' => 'approved'
            );
            $order = array(
                'counter.point' => -1
            );
        } elseif ($filter == 'new') //order by point
            {
            $where = array(
                'status' => 'approved'
            );
            $order = array(
                'ts' => -1
            );
        }
        
        if ($inventoryTypeInt == '')
            $cond['where'] = $where;
        else {
            $typeWhere     = array(
                'type' => $inventoryTypeInt
            );
            $cond['where'] = array(
                '$and' => array(
                    $where,
                    $typeWhere
                )
            );
        }
        
        if ($tag != '') {
            $tagWhere      = array(
                'tags.slug' => $tag
            );
            $cond['where'] = array(
                '$and' => array(
                    $where,
                    $tagWhere
                )
            );
        }
        
        if ($uid != '') {
            $uWhere        = array(
                'u.iid' => $uid
            );
            $cond['where'] = array(
                '$and' => array(
                    $where,
                    $uWhere
                )
            );
        }
        
        $cond['limit'] = per_page();
        $cond['order'] = $order;
        $cond['page']  = $page;
        $cond['total'] = 1; //do count total
        
        //cache if $page is < 10;
        /*
        v($cond);
        if ($page < 10)
        {
        $r = $this->getRedisCache("find", "$filter:$page", 600, array($cond)); //300 seconds?
        }	
        else 
        $r = $this->find($cond);
        */
        
        $r = $this->find($cond);
        return $r;
    }
    
    //inc view counter of inventory
    function incViewCounter($id)
    {
        $where  = array(
            'id' => $id
        );
        $update = array(
            '$inc' => array(
                'counter.v' => 1
            )
        );
        Dao_Node_Inventory::getInstance()->update($where, $update);
        $result = Dao_Node_Inventory::getInstance()->find(array(
            'where' => $where
        ));
        if (isset($result['result'][0]['counter']['v'])) {
            return $result['result'][0]['counter']['v'];
        } else {
            return 0;
        }
    }
}
