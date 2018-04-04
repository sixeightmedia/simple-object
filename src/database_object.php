<?php
namespace Sixeightmedia;
	
abstract class DatabaseObject {
	
	public static function create($data) {
		global $db;
		$db->insert(static::$table,$data);
		$o = static::get($db->id());
		return $o;
	}
	
	public static function get($id) {
		global $db;
		$o = new static;
		
		if(is_array($id)) {
  		$criteria = $id;
			$data = $db->get(static::$table, "*", $criteria);
		} else {
			$data = $db->get(static::$table, "*", [static::$key=>$id]);
		}
		
		//Set properties
		if ($id != 0) {
  		if(is_array($data) > 0) {
  			foreach($data as $col => $val) {
  				if($val != '') {
  					$o->$col = $val;
  				}
  			}
  		}
			return $o;
		} else {
			return false;
		}
	}
	
	public function set($property,$value='') {
  	
  	if(is_array($property)) {
  		foreach($property as $p => $v) {
    		$this->set($p,$v);
  		}
		}
  	
  	if($this->propertyIsAssignable($property)) {
    	//Update database
  		global $db;
   		$db->update(static::$table,[$property => $value],[static::$key=>$this->getID()]);
  		
  		//Update object
      $this->$property = $value;
      return true;
    } else {
      return false;
    }
	}
	
	public static function delete() {
		global $db;
		$db->delete(static::$table,[static::$key=>$this->getID()]);
	}
	
	public function getID() {
		$key = static::$key;
		return $this->$key;
	}
	
	public static function search($criteria,$orderBy='',$order='') {
		global $db;
		$records = $db->select(static::$table,'*',$criteria);
		
		$objects = array();
		
		if(count($records) > 0) {
			foreach($records as $record) {
				$o = static::get($record[static::$key]);
				$objects[] = $o;
			}
		}
		return $objects;
	}
	
	public function all() {
  	return static::search();
	}
	
	public function propertyIsAssignable($property) {
		foreach(static::$assignableProperties as $ap) {
			if($ap == $property) {
				return true;
			}	
		}
		return false;
	}
	
}