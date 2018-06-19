<?php

namespace models;

use \PDOStatement;

class DBStatement extends PDOStatement {

  protected $dbh;

  protected $defaultColumnMappings;

  protected $normalize;

  const STORED_PROPERTY = '_stored';

  protected static $reflection_classes = [];

  protected function __construct($dbh, $defaultColumnMappings = [], $normalize = false) {
    $this->dbh = $dbh;
    $this->defaultColumnMappings = $defaultColumnMappings;
    $this->normalize = $normalize;
  }

  protected function getReflectionClass($class) {
    if(!array_key_exists($class, self::$reflection_classes)) {
      self::$reflection_classes[$class] = new \ReflectionClass($class);
    }
    $rc = self::$reflection_classes[$class];
    return $rc;
  }

  protected function normalize($str) {
    if(strpos($str, '_') === false)
      return $str;
    $s = str_replace('_', '', ucwords($str, '_'));
    $s[0] = strtolower($s[0]);
    return $s;
  }

  public function fetchOneClass($class, $ignore = false, $columnMappings = []) {
    $rc = $this->getReflectionClass($class);
    $row = $this->fetch(\PDO::FETCH_ASSOC);
    if($row === false)
      return null;
    $columnMappings = array_merge($this->defaultColumnMappings, $columnMappings);
    $obj = $rc->newInstanceWithoutConstructor();
    foreach($row as $k => $v) {
      if(array_key_exists($k, $columnMappings)) {
        $k = $columnMappings[$k];
      }
      $k = $this->normalize ? $this->normalize($k) : $k;
      if($ignore && !$rc->hasProperty($k))
        continue;
      $prop = $rc->getProperty($k);
      $prop->setAccessible(true);
      $prop->setValue($obj, $v);
    }
    if($rc->hasProperty(self::STORED_PROPERTY)) {
      $prop = $rc->getProperty(self::STORED_PROPERTY);
      $prop->setAccessible(true);
      $prop->setValue($obj, true); 
    }
    return $obj;
  }

  public function fetchAllClass($class, $ignore = false, $columnMappings = []) {
    $columnMappings = array_merge($this->defaultColumnMappings, $columnMappings);
    $rc = $this->getReflectionClass($class);
    $rows = $this->fetchAll(\PDO::FETCH_ASSOC);
    $result = [];
    foreach($rows as $row) {
      $obj = $rc->newInstanceWithoutConstructor();
      foreach($row as $k => $v) {
        if(array_key_exists($k, $columnMappings)) {
          $k = $columnMappings[$k];
        }
        $k = $this->normalize ? $this->normalize($k) : $k;
        if($ignore && !$rc->hasProperty($k))
          continue;
        $prop = $rc->getProperty($k);
        $prop->setAccessible(true);
        $prop->setValue($obj, $v);
      }
      if($rc->hasProperty(self::STORED_PROPERTY)) {
        $prop = $rc->getProperty(self::STORED_PROPERTY);
        $prop->setAccessible(true);
        $prop->setValue($obj, true);
      }
      $result[] = $obj;
    }
    return $result;
  }

  protected function getParams($queryString) {
    preg_match_all('/:[A-Za-z0-9]*/', $queryString, $params);
    $params = $params[0];
    $params = array_map(function($s) { 
      return str_replace(':', '', $s); 
    }, $params);
    return $params;
  }

  public function executeBind($obj, $columnMappings = []) {
    $columnMappings = array_merge($this->defaultColumnMappings, $columnMappings);
    $rc = $this->getReflectionClass(get_class($obj));
    $params = $this->getParams($this->queryString);
    $binds = [];
    foreach ($params as $param) {
      if(array_key_exists($param, $columnMappings)) {
        $param = $columnMappings[$param];
      }
      $param = $this->normalize ? $this->normalize($param) : $param;
      $prop = $rc->getProperty($param);
      $prop->setAccessible(true);
      $binds[$param] = $prop->getValue($obj);
    }
    return $this->execute($binds);
  }

}