<?php

/*

	LOVE DB CLASS
	Author: jsilvestris@x-industries.com
	Version: 3.0

*/

class db {
	public static $connection;
	public static $debug;
	private static $_MYSQL;
	private static $error_query;
	private static $error_query_time;
	var $_QUERY;

	function __construct() {
		//self::connect();
	}
	
	function __destruct() {
		//mysql_close(self::$connection);
	}
	
	function debug() {
		self::$debug = true;
	}
	
	public static function credentials($db, $user, $pass, $host="localhost") {
		self::$_MYSQL = array(
			"host"	=> $host,
			"user"	=> $user,
			"pass"	=> $pass,
			"db"		=> $db
		);
	}

	public static function name() {
		return self::$_MYSQL["db"];
	}
	
	public static function connect() {
		if (!self::$connection) {
			self::$connection = new mysqli(self::$_MYSQL["host"], self::$_MYSQL["user"], self::$_MYSQL["pass"], self::$_MYSQL["db"]);
		}
	}
	
	public static function reconnect() {
		self::$connection = new mysqli(self::$_MYSQL["host"], self::$_MYSQL["user"], self::$_MYSQL["pass"], self::$_MYSQL["db"]);
	}
	
	function an($str) { return preg_replace("/[^A-z0-9]/","_",$str); }
	public static function _cleanse($str) { self::connect(); return self::$connection->real_escape_string($str); }
	
	/*
	
	Major rework here... now takes an array of arguments... can I work this back into the CMS? I like this...
	
	*/
	public static function _select($from, $arguments=false) {
	//public static function _select($from,$when=false,$what="*",$orderby=false,$orderdir="ASC"){
		self::connect();
		
		$whentxt = "";
		if (!isset($arguments["when"]) && isset($arguments["where"])) $arguments["when"] = $arguments["where"];
		if (isset($arguments["when"]) && is_array($arguments["when"])) {
			foreach ($arguments["when"] as $k => $v) {
				$whentxt .= ($whentxt > "" ? " AND" : "");
				if (is_string($v) || is_numeric($v)) {
					$whentxt .= ' `'.$k.'`'.(isset($arguments["like"]) && $arguments["like"] ? ' LIKE \'%'.self::_cleanse($v).'%\'':'=\''.self::_cleanse($v).'\'');
				} elseif (is_array($v)) {
					$ovc = 0;
					$whentxt .= ' ( ';
					foreach ($v as $op_v) {
						if ($ovc > 0) $whentxt .= ' OR ';
						$whentxt .= ' `'.$k.'`'.(isset($arguments["like"]) && $arguments["like"] ? ' LIKE \'%'.self::_cleanse($op_v).'%\'':'=\''.self::_cleanse($op_v).'\'');
						$ovc++;
					}
					$whentxt .= ' ) ';
				} else {
					// not a string, number or array...
				}
			}
			$whentxt = " WHERE".$whentxt;
		} elseif (isset($arguments["when"]) && is_string($arguments["when"])) {
			$whentxt = " WHERE ".$arguments["when"];
		} elseif (isset($arguments["id"]) && is_numeric($arguments["id"])) {
			$whentxt = " WHERE id=".$arguments["id"];
		} elseif (isset($arguments) && is_numeric($arguments)) {
			$whentxt = " WHERE id=".$arguments;
			$arguments = array();
		}
		
		if (isset($arguments["order_direction"]) && strtoupper($arguments["order_direction"]) != "DESC") $arguments["order_direction"] = "ASC";
		if (!isset($arguments["what"])) $arguments["what"] = "*";
		$q = "SELECT ".$arguments["what"]." FROM `".$from."`".$whentxt.(isset($arguments["order_by"]) ? ' ORDER BY `'.$arguments["order_by"].'` '.$arguments["order_direction"] : '');
		if (isset($arguments["limit"]) && is_numeric($arguments["limit"])) $q .= " LIMIT ".$arguments["limit"];
		if (isset($arguments["offset"]) && is_numeric($arguments["offset"])) $q .= " OFFSET ".$arguments["offset"];
		
		if (self::$debug) echo $q;
		
		if ($m = self::$connection->query($q)) {
			$d = array();
			while ($r = $m->fetch_array(MYSQLI_ASSOC)) {
				if (isset($arguments["key"])) {
					$d[($r[$arguments["key"]])] = $r;
				} else $d[]=$r;
			}
			if (isset($arguments["id"]) && is_numeric($arguments["id"]) && isset($d[0]) && !isset($d[1])) return $d[0];
			return $d;
		}
		return self::error($q);
	}
	
	public static function _count($from, $arguments=false) {
		$arguments["what"] = "COUNT(*) as count";
		$result = self::_select($from, $arguments);
		if(isset($result[0]["count"])) return $result[0]["count"];
		return 0;
	}
	
	public static function _insert($to, $insert_array) {
		self::connect();
		$keys = array(); $vals=array();
		if (is_array($insert_array)) {
			foreach ($insert_array as $k=>$v) { $keys[] = self::_cleanse($k); $vals[] = self::_cleanse($v); }
			$q = "INSERT INTO `".$to."` (`_created`, `".implode("`, `",$keys)."`) VALUES(NOW(), '".implode("', '",$vals)."');";
			if (self::$debug) echo $q;
			if ($m = self::$connection->query($q)){
				return self::$connection->insert_id; // should return the last insert id instead... to do
			}
		}
		return self::error($q);
	}
	
	public static function _update($to, $where, $insert_array) {
		self::connect();
		$keys=array(); $vals=array(); $q=""; $w = "";
		if (is_array($insert_array)) {
			foreach ($insert_array as $k => $v) {
				if ($q > "") $q .= ",";
				$q .= "`".self::_cleanse($k)."`='".self::_cleanse($v)."'";
			}
			if (is_numeric($where)) {
				$q = "UPDATE `".$to."` SET ".$q.' WHERE id='.$where.";";
				if (self::$debug) echo $q;
				if ($m = self::$connection->query($q)) return $where;
				
			} elseif (is_array($where)) {
				// handle update with WHERE condition
				foreach ($where as $k => $v) {
					if ($w > "") $w .= " AND ";
					$w .= '`'.$k.'`='.(strtolower($v) == "null" ? "NULL" : '"'.self::_cleanse($v).'"');
				}
				$q = "UPDATE `".$to."` SET ".$q.' WHERE '.$w.';';
				if (self::$debug) echo $q;
				if ($m = self::$connection->query($q)) return $m;
			}
		}
		return self::error($q);
	}
	
	public static function _update_or_insert($to, $uniques, $insert_array) {
		$q = '';
		foreach ($uniques as $key => $value) {
			if ($q != "") $q .= " AND ";
			$q .= '`'.$key.'` = '.(strtolower($value) == "null" ? "NULL" : '"'.self::_cleanse($value).'"');
		}
		$q = 'SELECT id FROM '.$to.' WHERE '.$q;
		//echo $q;
		$result = self::_query($q);
		if (count($result) == 1) {
			return self::_update($to, $result[0]["id"], $insert_array);
		} elseif (count($result) == 0) {
			return self::_insert($to, $insert_array);
		}
		return self::error($q);
	}
	
	// $arg is either numeric 'id' or where match array("key"=>"value") pairs
	public static function _delete($what,$arg=null) {
		self::connect();
		$whentxt = "";
		if (is_array($arg)) {
			foreach ($arg as $k => $v) { $whentxt .= ($whentxt>""?" AND":"").' `'.$k.'`="'.self::_cleanse($v).'"'; }
			$whentxt = " WHERE".$whentxt;
		} else if(is_numeric($arg)) {
			$whentxt = " WHERE id='".$arg."'";
		} else return false;
		$q = "DELETE FROM `".$what."`".$whentxt;
		if (self::$debug) echo $q;
		if (self::$connection->query($q)) return true;
		return null;
	}
	
	public static function _search() {
	
	}
	
	public static function _query($str) {
		self::connect();
		if ($m = self::$connection->query($str)) {
			if ($m === true) return true;
			$d = array();
			while ($r = $m->fetch_array(MYSQLI_ASSOC)) { $d[] = $r; }
			return $d;
		}
		return self::error($str);
	}
	
	private static function error($query) {
		self::$error_query = $query;
		self::$error_query_time = self::timestamp();
		return null;
	}
	
	public static function last_error($as_string=true) {
		return ($as_string ? self::$error_query_time.' - '.self::$error_query : array("time" => self::$error_query_time, "query" => self::$error_query));
	}
	
	public static function _timestamp($time=false) { return self::timestamp($time); }
	public static function timestamp($time=false) { return ($time ? date("Y-m-d H:i:s", strtotime($time)) : date("Y-m-d H:i:s")); }
	public static function datestamp($time=false) { return ($time ? date("Y-m-d", strtotime($time)) : date("Y-m-d")); }
}
?>