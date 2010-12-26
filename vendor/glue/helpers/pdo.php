<?php

/*
	Class: PdoHelper
		Pdo helper class. Create and manage db connections.
*/
class PdoHelper {

    protected $_sources     = array();
    protected $_connections = array();
    
    
    public function addSource($name, $config) {
    
        $this->_sources[$name] = $config;
    }
    
    public function src($name = 'default') {
        
        if(!isset($this->_connections[$name])){
            $this->_connections[$name] = new PdoConnectionHelper($this->_sources[$name]);
        }
        
        return $this->_connections[$name];
    }
    
}

/*
	Class: PdoConnectionHelper
		Pdo connection class. Create and manage db connections.
*/
class PdoConnectionHelper {

    public $pdo = null;
    
    public function __construct($config){
      extract($config);
      
      try {
        $this->pdo = new PDO($dns,$user,$password,$options);
      }catch( PDOException $Exception ) {
         trigger_error('PDO Connect failed: '.$Exception->getMessage(),E_USER_ERROR);
      }
      
    }
    
    
    public function create($table,$data){    
      
        $fields = array();
        $values = array();

        foreach($data as $col=>$value){
            $fields[] = $col;
            $values[] = $this->pdo->quote($value);
        }
        
        $fields = implode(',', $fields);
        $values = implode(',', $values);

        $sql = "INSERT INTO {$table} ({$fields}) VALUES ({$values})";

        $this->log['queries'][] = $sql;

        $res = $this->pdo->exec($sql);

        if($res){
            return $this->pdo->lastInsertId();
        }else{
            trigger_error('SQL Error: '.implode(', ',$this->pdo->errorInfo()).":\n".$sql);
            return false;
        }
    }
    
    public function update($table,$data,$conditions=array()){    

      $conditions = $this->buildConditions($conditions);
      
      if(strlen(trim($conditions))>0) $conditions = "WHERE ".$conditions;
      
      $fields = array();
      
      foreach($data as $col=>$value){
        $fields[] = $col.'='.$this->pdo->quote($value);
      }
      
      $fields = implode(',', $fields);
      
      $sql = "UPDATE ".$table." SET {$fields} {$conditions}";
      
      $this->log['queries'][] = $sql;
      
      if($this->pdo->exec($sql)){
      
      }else{
        $errorInfo = $this->pdo->errorInfo();
        if($errorInfo[0]!='00000'){
            trigger_error('SQL Error: '.implode(', ',$errorInfo).":\n".$sql);
            return false;
        }
      }
      
    }
    
    public function read($options = array()) {
        
     $options['limit'] = 1;
     $result =  $this->find($options);
     
     return count($result) ? $result[0]:false;
    }
    
    public function find($options){  
                
        $options = array_merge(array(
          'conditions' => array(), //array of conditions
          'having'     => array(), //array of conditions
          'table'      => array(),
          'joins'      => array(),
          'fields'     => array('*'), //array of field names
          'order'      => array(), //string or array defining order
          'group'      => array(), //fields to GROUP BY
          'limit'      => null, //int
          'page'       => null, //int
          'offset'     => null
        ),$options);
        
        extract($options);

        if(is_array($fields)) $fields = implode(', ', $fields);
        if(is_array($table))  $table  = implode(', ', $table);
        if(is_array($group))  $group  = implode(', ', $group);
        if(is_array($order))  $order  = implode(', ', $order);

        $conditions = $this->buildConditions($conditions);
        $having     = $this->buildConditions($having);
        
        switch(strtolower($this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME))) {
            case 'mysql':
                $mysql_limit = function() use($limit, $offset){
                  if ($limit) {
                    $rt = '';
                    if (!strpos(strtolower($limit), 'limit') || strpos(strtolower($limit), 'limit') === 0) {
                      $rt = ' LIMIT';
                    }
                    if ($offset) { $rt .= ' ' . $offset . ','; }
                    $rt .= ' ' . $limit;
                    
                    return $rt;
                  }
                  return null;
                };
                
                $limit = $mysql_limit();
                break;
                
            case 'sqlite':
            
                $sqlite_limit = function() use($limit, $offset){
                  if ($limit) {
                    $rt = '';
                    if (!strpos(strtolower($limit), 'limit') || strpos(strtolower($limit), 'limit') === 0) {
                      $rt = ' LIMIT';
                    }
                    $rt .= ' ' . $limit;
                    if ($offset) { $rt .= ' OFFSET ' . $offset; }
                    
                    return $rt;
                  }
                  return null;
                };
                
                $limit = $sqlite_limit();
                break;
        }

        //build joins
        
        $_joins     = array();
        
        if(is_string($joins)){
          $_joins = array($joins);
        }else{
          if(count($joins)){
             foreach($joins as $j){
               if(is_string($j)){
                 $_joins = $j;
               }else{
                $_joins[] = strtoupper($j['type']).' JOIN '.$j['table'].' '.$j['alias'].' ON('.implode(' AND ', $j['conditions']).')';
               }
             }
          }
        }
        
        $joins = implode(' ', $_joins);
        
       if(strlen(trim($conditions))>0) $conditions = "WHERE ".$conditions;
       if(strlen(trim($group))>0) $group = "GROUP BY ".$group;
       if(strlen(trim($having))>0) $having = "HAVING ".$conditions;
       if(strlen(trim($fields))==0) $fields = "*";
       if(strlen(trim($order))>0) $order = "ORDER BY ".$order;
        
       $sql = "SELECT {$fields} FROM {$table} {$joins} {$conditions} {$group} {$having} {$order} {$limit}";
       
       $this->log['queries'][] = $sql;
       
       return $this->fetchAll($sql);

    }
    
    public function delete($table,$conditions){    
      
      
      $conditions = $this->buildConditions($conditions);
      
      if(strlen(trim($conditions))>0) $conditions = "WHERE ".$conditions;
      
      $sql = "DELETE FROM {$table} {$conditions}";
      
      $this->log['queries'][] = $sql;
      
      $res = $this->pdo->exec($sql);
      
      if($res || $res===0){
        return true;
      }else{
        trigger_error('SQL Error: '.implode(', ',$this->pdo->errorInfo()).":\n".$sql);
        return false;
      }
    }
    
    public function fetchAll($sql){
      
      $ret_result = array();
      
      if($stmt = $this->pdo->query($sql)){
      
        $meta = array();

        foreach(range(0, $stmt->columnCount() - 1) as $column_index){
          $meta[] = $stmt->getColumnMeta($column_index);
        }
        
        $rows = $stmt->fetchAll(PDO::FETCH_NUM);

        foreach($rows as &$r){  
          $rec = array();
          for($i=0,$max=count($r);$i<$max;$i++){            
            
            $tabeleName = (strlen($meta[$i]['table'])!=0) ? $meta[$i]['table']:0;
            
            $rec[$tabeleName][$meta[$i]['name']] = $r[$i]; 
          }
          $ret_result[] = $rec;
        }
      }else{
         trigger_error('SQL Error: '.implode(', ',$this->pdo->errorInfo()).":\n".$sql,E_USER_ERROR);
      }

      return $ret_result;
    }
  
    protected function buildConditions($conditions){
        
        if(is_string($conditions)) $conditions = array($conditions);
        
        $_conditions = array();
        
        if(count($conditions)){
          
          $_bindParams = array();

          foreach($conditions as $c){
            
            $sql = '';
            
            if(is_array($c)){
              
              $sql = $c[0];
              
              foreach($c[1] as $key=>$value){
                $sql = str_replace(':'.$key,$this->pdo->quote($value),$sql);
              }
            }else{
              $sql= $c;
            }

            if(count($_conditions) > 0  && strtoupper(substr($sql,0,4))!='AND ' && strtoupper(substr($sql,0,3))!='OR '){
              $sql = 'AND '.$sql;
            }
            
            $_conditions[] = $sql;
            
          }
          
        }
        
       $conditions = implode(' ', $_conditions);
       
       return $conditions;
    }  
    
}