<?php
class Table {
	public $table;
	public $cells = array();
	function __construct($parentElement,$num_rows,$num_cols,$id=NULL,$class=NULL) {
		$this->table = new HTMLTable($parentElement,$id,$class);
		for ($i=0;$i<$num_rows;$i++) {
			$row = new HTMLTr($this->table);
			if (!empty($class)) { $row->setClass($class.'_row'); }
			if (!empty($id)) { $row->setId($id.'_'.$i); }
			for ($j=0;$j<$num_cols;$j++) {
				$this->cells[$i][$j] = new HTMLTd($row);
				if (!empty($class)) { $this->cells[$i][$j]->setClass($class.'_col'); }
				if (!empty($id)) { $this->cells[$i][$j]->setId($id.'_'.$i.'_'.$j); }
			}
		}
	}
}
?>