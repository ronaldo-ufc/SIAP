<?php
namespace siap\home\models;

use siap\models\DBSiap;

class Menu
{
  private $menu_codigo;
  private $menu_pai;
  private $menu_nome;
  private $menu_hint;
  private $menu_url; 
  private $menu_hab_pad;
  private $menu_icon;
  private static $filhos = null;
  
  static function bundle($row){
    $u = new Menu();
    $u->setMenu_codigo($row['menu_codigo']);
    $u->setMenu_pai($row['menu_pai']);
    $u->setMenu_nome($row['menu_nome']);
    $u->setMenu_url($row['menu_url']);
    $u->setMenu_hint($row['menu_hint']);
    $u->setMenu_hab_pad($row['menu_hab_pad']);
    $u->setMenu_icon($row['menu_icon']);
    return $u;
  }
  
   /**
     * @param $privilegio integer
     * @return Menu|null
     */
  public static function getPaiByPrivilegio($privilegio) {
    $sql = "select m.* from siap.menu m
            inner join siap.privilegio_menu p on m.menu_codigo = p.menu_codigo 
            where p.privilegio_codigo = ? and p.privilegio_habilitado = 'S' and menu_pai is null order by ordem";

    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array($privilegio));
    $rows = $stmt->fetchAll();
    $result = array();
    foreach ($rows as $row){
      array_push($result, self::bundle($row));
    }
    return $result;
  }
   /**
     * @param $codigo integer
     * @return Menu|null
     */
  public static function getByCodigo($codigo) {
    $sql = "SELECT * FROM siap.menu WHERE menu_codigo = ?";

    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array($codigo));
    $row = $stmt->fetch();
    if ($row == null){
      return false;
    }
    return self::bundle($row);
  }
   /**
     * @param $menu_pai, privilegio integer
     * @return Menu|null
     */
  public static function getFilhosByPaiAndPrivilegio($menu_pai, $privilegio) {
    $sql = "select m.* from siap.menu m
            inner join siap.privilegio_menu p on m.menu_codigo = p.menu_codigo 
            where p.privilegio_codigo = ? and p.privilegio_habilitado = 'S' and m.menu_pai = ? order by ordem";

    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array($privilegio, $menu_pai));
    $rows = $stmt->fetchAll();
    $result = array();
    foreach ($rows as $row){
      array_push($result, self::bundle($row));
    }
    return $result;
  }
  
  public static function getFilhosByPai($menu_pai) {
    $sql = "select * from siap.menu where menu_pai = ?";

    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array($menu_pai));
    $rows = $stmt->fetchAll();
    $result = array();
    foreach ($rows as $row){
      array_push($result, self::bundle($row));
    }
    return $result;
  }
  
  function getFilhos($privilegio){
    if ($this->menu_codigo){
      if ($this->filhos == null){
        $this->filhos = Menu::getFilhosByPaiAndPrivilegio($this->menu_codigo, $privilegio);
      }
      return $this->filhos;
    }else{
      return false;
    }
    
  }
          
  function getMenu_codigo() {
    return $this->menu_codigo;
  }

  function getMenu_pai() {
    return $this->menu_pai;
  }

  function getMenu_nome() {
    return $this->menu_nome;
  }

  function getMenu_hint() {
    return $this->menu_hint;
  }

  function getMenu_url() {
    return $this->menu_url;
  }

  function getMenu_hab_pad() {
    return $this->menu_hab_pad;
  }
  function getMenu_icon() {
    return $this->menu_icon;
  }
  function setMenu_icon($menu_icon) {
    $this->menu_icon = $menu_icon;
  }
  function setMenu_codigo($menu_codigo) {
    $this->menu_codigo = $menu_codigo;
  }

  function setMenu_pai($menu_pai) {
    $this->menu_pai = $menu_pai;
  }

  function setMenu_nome($menu_nome) {
    $this->menu_nome = $menu_nome;
  }

  function setMenu_hint($menu_hint) {
    $this->menu_hint = $menu_hint;
  }

  function setMenu_url($menu_url) {
    $this->menu_url = $menu_url;
  }

  function setMenu_hab_pad($menu_hab_pad) {
    $this->menu_hab_pad = $menu_hab_pad;
  }


}