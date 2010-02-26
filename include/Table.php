<?php
class Table {
  public $table;
  public $cells = array();
  function __construct($parentElement,$num_rows,$num_cols,$class=NULL,$id=NULL) {
    $this->table = new HTMLTable($parentElement,$class,$id);
    for ($i=0;$i<$num_rows;$i++) {
      if (empty($class) and empty($id)) {
        $row = new HTMLTr($this->table);
      } else {
        $row = new HTMLTr($this->table,$class.'_row',$class.'_row_'.$i);
      }
      for ($j=0;$j<$num_cols;$j++) {
        if (empty($class) and empty($id)) {
          $this->cells[$i][$j] = new HTMLTd($row);
        } else {
          $this->cells[$i][$j] = new HTMLTd($row,$class.'_col',$class.'_col_'.$i.'_'.$j);
        }
      }
    }
  }
}
?>