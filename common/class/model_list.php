<?php
class model_list implements iterator, arrayaccess, countable {
	public $nr = 0;
	public $count_all = -1;
	public $list = array();
	
	function __construct($anc){
		if(! ($class = @$this->class))
			$class = substr(get_class($this),0,-1);
		$tmp = new $class();
		$table = $tmp->table;
		
		$sql = "SELECT a.* FROM $table AS a ";
		
		if(isset($anc['filters']) && $anc['filters']){
			$filters = array();
			foreach ($anc['filters'] as $k => $v){ 
				//$v = addslashes($v);
				if(is_numeric($k)){
					$filters[] = $v;
				} else {
					$filters[] = " a.$k = '$v' ";
				}
			}
			$sql .= ' WHERE ( ' . implode(' ) AND ( ', $filters) . ' ) ';
		}
		if(isset($anc['order'])){
			$sql .= ' ORDER BY ' . $anc['order'];
		}
		if(isset($anc['limit'])){
			$sql_count_all = str_replace("SELECT a.* FROM", "SELECT count(*) AS nr FROM", $sql);
			$row = dbRow($sql_count_all);
			$this->count_all = $row['nr'];
			$offset = (@$anc['offset']) ? $anc['offset'] : 0;
			$sql .= " LIMIT $offset, " . $anc['limit'];
		} else {
			$this->count_all = -1;
		}
		
		$result = dbRows($sql);
		$this->nr = count($result);
		if($this->count_all < 0) $this->count_all = $this->nr;
		foreach($result as $v){
			$item = new $class();
			$item->exists = 1;
			foreach($v as $key => $value){
				$item->c[$key] = $value;
				$item->m[$key] = $value;
			}
			$this->list[] = $item;
		}
		//echoprex($sql);
	}

//implements iterator
	function rewind() {
		$next = reset($this->list);
		
		while ($this->keep_nexting($next)) {
			$next = next($this->list);
		}
	}
	
	function current() {
		return current($this->list);
	}
	
	function key() {
		return key($this->list);
	}
	
	function next() {
		$next = next($this->list);
		
		while ($this->keep_nexting($next)) {
			$next = next($this->list);
		}
	}
	
	function valid() {
		return !!current($this->list);
	}
	
	function keep_nexting($next) {
		if (empty($this->check_existence_of)) { //optionally defined in child classes
			//simple check
			return ($next) && (!$next->exists);
		}
		
		if (!$next) {
			//end of array, don't next
			return false;
		}
		
		if (!$next->exists) {
			//element in array, but inexistent model
			return true;
		}
		
		return true;
	}
	
	//implements arrayaccess
	public function offsetSet($offset, $value) {
		throw new Exception('You cannot add values to the model_list class');
	}
	
	public function offsetExists($offset) {
		return isset($this->list[$offset]);
	}
	
	public function offsetUnset($offset) {
		throw new Exception('You cannot unset values of model_list class');
	}
	
	public function offsetGet($offset) {
		return $this->list[$offset];
	}
	
	//implements countable
	public function count() {
		return $this->count_all;
	}

	
}
?>