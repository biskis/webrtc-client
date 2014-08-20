<?php
class model implements arrayaccess{
	public $c = array();
	public $m = array();
	public $exists = false;
	public $autoincremnet = false;
	
	public $table = 'none';
	public $keys = array('_id');
	
	function __construct($id = null){
		if($id === null)
			return false;
		//suprascriem cu toti parametrii functiei
		$id = func_get_args();
		
		$where_array = array();
		if(!is_array($id)){
			$id = array($id);
		}
		
		if(sizeof($id) == sizeof($this->keys)){
			foreach($this->keys as $k => $v){
				if(isset($id[$k]))
					$where_array[] = " $v = '" . $id[$k] . "' "; 
				else
					throw new Exception("Key $v is not valid on table " . $this->table);
			}
		} else {
			throw new Exception('Key must be: '. print_r($this->keys, true) . " on table " . $this->table);
		}

		if($where_array){
			$sql = "SELECT * FROM $this->table
					WHERE " . implode(' AND ', $where_array) . "
					LIMIT 1";
			//echopre($sql);
			$result = dbRow($sql);
			if($result){
				$this->c = $result;
				$this->m = $result;
				$this->exists = true;
			} else {
				foreach($this->keys as $k => $v){
					$this->m[$v] = $id[$k];
				}
			}			
		}
	}
	
	function save($return_id = false){
		$where_array = array();
		foreach($this->keys as $k => $v){
			$where_array[] = " $v = '" . @$this->m[$v] . "' "; 
		}
			
		$modified = false;
		$for_sql = array();
		foreach($this->m as $k => $v){
			if($v != @$this->c[$k]){
				$for_sql[] = " $k = '". addslashes($v) . "' ";
				$modified = true;
				$this->c[$k] = $this->m[$k];
			}
		}
		
		if($modified){
			$set_sql = " SET " . implode(', ', $for_sql);
			if($this->exists){
				
				$sql = "UPDATE $this->table
						$set_sql
						WHERE " . implode(" AND ", $where_array) . "
						LIMIT 1";
						
				if(dbSql($sql)){
					$this->exists = true;
					return 'update';
				}
			} else {
				
				$sql = "INSERT INTO $this->table
						$set_sql
						";
				//echopre($sql);
				if($return_id || @$this->autoincrement) {
					$this->exists = true;
					$last_id = dbLastId($sql);
					
					if(count($this->keys) == 1){
						$this->c[$this->keys[0]] = $last_id;
						$this->m[$this->keys[0]] = $last_id;
					}
					return $last_id;
				}
					
				if(dbSql($sql)){
					$this->exists = true;
					return 'inserted';	
				}
			}
			return 'error_save';
		}
		return 'nothing';
	}
	
	function delete(){
		foreach($this->keys as $k => $v){
			$where_array[] = " $v = '" . @$this->m[$v] . "' "; 
		}
		
		$sql = "DELETE FROM $this->table 
				WHERE " . implode(' AND ', $where_array) . "
				LIMIT 1 ";;
		dbSql($sql);
		
		return 'deleted';
	}
	
	
	public function offsetSet($offset, $value) {
        /*if ((!isset($this->c[$offset])) || isset($this->m[$offset]) || (@$this->c[$offset] !== $value)) {
			$this->c[$offset] = $value;
			$this->m[$offset] = $value;
		} else {
			unset($this->m[$offset]);
		}*/		
		if (is_null($offset)) {
            $this->c[] = $value;
        } else {
			$this->m[$offset] = $value;
        }
    }
    public function offsetExists($offset) {
        return isset($this->c[$offset]);
    }
    public function offsetUnset($offset) {
        unset($this->c[$offset]);
        unset($this->m[$offset]);
    }
    public function offsetGet($offset) {
        //my method
		if (method_exists($this, $offset)) {
			return $this->$offset();
		}
		
		//my property
		if (array_key_exists($offset, $this->m)) {
			return $this->m[$offset];
		}
		
		return isset($this->c[$offset]) ? $this->c[$offset] : null;
    }

}
?>