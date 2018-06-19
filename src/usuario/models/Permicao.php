<?php
namespace siap\usuario\models;
use siap\models\DBSiap;
use siap\home\models\Menu;
use siap\usuario\models\Privilegio;

class Permicao {
  private $privilegio_codigo;
  private $menu_codigo;
  private $privilegio_habilitado;
  private $menu;
  private $privilegio;
  
  private function bundle($row){
    $u = new Permicao();
    $u->setPrivilegio_codigo($row['privilegio_codigo']);
    $u->setMenu_codigo($row['menu_codigo']);
    $u->setPrivilegio_habilitado($row['privilegio_habilitado']);
    $u->setMenu($row['menu_codigo']);
    $u->setPrivilegio($row['privilegio_codigo']);
    return $u;
  }
  
  static function update($privilegio, $menu, $habilitacao){
    $sql = "UPDATE siap.privilegio_menu SET privilegio_habilitado = ? WHERE menu_codigo = ? and privilegio_codigo = ?";
    
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array($habilitacao, $menu, $privilegio));
  }
  
  static function getAll(){
    $sql = "SELECT * FROM siap.privilegio_menu";
    
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array());
    $rows = $stmt->fetchAll();
    $result = array();
    foreach ($rows as $row){
      array_push($result, self::bundle($row));
    }
    return $result;
  }
  
  static function getMenuPaisByPrivilegio($privilegio){
    $sql = "select pm.* from siap.privilegio_menu pm
              inner join siap.menu m on m.menu_codigo = pm.menu_codigo
              where privilegio_codigo = ? and m.menu_pai is null";
    
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array($privilegio));
    $rows = $stmt->fetchAll();
    $result = array();
    foreach ($rows as $row){
      array_push($result, self::bundle($row));
    }
    return $result;
  }
  
  static function getsubMenusByPrivilegioAndMenu($privilegio, $menu){
    $sql = "select pm.* from siap.privilegio_menu pm
              inner join siap.menu m on m.menu_codigo = pm.menu_codigo
              where privilegio_codigo = ? and m.menu_pai = ?";
    
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array($privilegio, $menu));
    $rows = $stmt->fetchAll();
    $result = array();
    foreach ($rows as $row){
      array_push($result, self::bundle($row));
    }
    return $result;
  }
  
  function getPrivilegio() {
    return $this->privilegio;
  }

  function setPrivilegio($privilegio) {
    $this->privilegio = Privilegio::getByCodigo($privilegio);
  }

  
  function getMenu() {
    return $this->menu;
  }

  function setMenu($menu_codigo) {
    $this->menu = Menu::getByCodigo($menu_codigo);
  }

  function getPrivilegio_codigo() {
    return $this->privilegio_codigo;
  }

  function getMenu_codigo() {
    return $this->menu_codigo;
  }

  function getPrivilegio_habilitado() {
    return $this->privilegio_habilitado;
  }

  function setPrivilegio_codigo($privilegio_codigo) {
    $this->privilegio_codigo = $privilegio_codigo;
  }

  function setMenu_codigo($menu_codigo) {
    $this->menu_codigo = $menu_codigo;
  }

  function setPrivilegio_habilitado($privilegio_habilitado) {
    $this->privilegio_habilitado = $privilegio_habilitado;
  }
  
}
