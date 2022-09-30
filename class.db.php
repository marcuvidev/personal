<?php

  class db {

    public $error; 
    private $link; 
    private $resultado; 
    public $nrows = 0;
    public $sql; 
    public $insertid; 
    public $queryCount;
    public $queries = array();

    public function __construct($host,$db,$user,$pass) {
      $this->conecta($host,$db,$user,$pass);
    }

    /* Funci�n encargada de crear el objeto en caso de no estar ya instanciado */ 

    public function table_column_exists($dbname,$table,$column) {
      $sql = 'SELECT *
              FROM information_schema.COLUMNS
              WHERE TABLE_SCHEMA = "'.$dbname.'"
              AND TABLE_NAME = "'.$table.'"
              AND COLUMN_NAME = "'.$column.'"';
      $res = $this->query($sql);
      return ($this->nrows==1);
    }

    public function conecta($host,$db,$user,$pass){    
      $this->link=@mysql_connect($host,$user,$pass) or die('Error de conexion a la base de datos');
      @mysql_select_db($db,$this->link) or die ('Error de acceso a la base de datos');
    }

    public function select_db($db) {
      @mysql_select_db($db,$this->link) or die ('Error de acceso a la base de datos');
    }

    public function query($sql) {
      if ($_SESSION['config']['config']['dev_mode']==1)
        $start = $this->getTime();
      $this->sql=$sql;      
      $this->resultado=@mysql_query($sql,$this->link);

      if ($_SESSION['config']['config']['dev_mode']==1) {
        $this->queryCount += 1;
        $this->logQuery($sql, $start);
      }

      if (mysql_errno()!=0)
        $this->error=$sql." - ".mysql_error();

      $this->nrows=@mysql_num_rows($this->resultado);

      $this->insertid=@mysql_insert_id($this->link);

      return $this->resultado;
    }
   
    private function logQuery($sql, $start) {
      $query = array(
          'sql' => $sql,
          'time' => ($this->getTime() - $start)*1000
        );
      array_push($this->queries, $query);
    }    

    private function getTime() {
      $time = microtime();
      $time = explode(' ', $time);
      $time = $time[1] + $time[0];
      $start = $time;
      return $start;
    }

    public function next($r=null) {
      if($r) $resultado=$r;
      else $resultado=$this->resultado;
      return @mysql_fetch_assoc($resultado);
    }

    public function realscape($cad) {
      return @mysql_real_escape_string($cad);
    }
    
    public function query_hash($sql,$indice="",$utf8="") {
      $res = $this->query($sql);
      $out = array();

      while ($fila = $this->next($res))
        if ($indice!="") {
          if ($utf8!="")
            $out[$fila[$indice]] = utf8_encode($fila);
          else
            $out[$fila[$indice]] = $fila;
        } else {
          if ($utf8!="")
            $out[] = utf8_encode($fila);
          else
            $out[] = $fila;
        }

      return $out;

    }
   
    public function cerrar() {
     @mysql_close();
    }

    function __destruct() {
      @mysql_free_result($this->resultado);
      @mysql_close();
    }
  }
?>
