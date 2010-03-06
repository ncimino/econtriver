<?php
class Escape {
  
  static function encodeBs($value) { return addcslashes($value,'\\'); }
  static function encodeFs($value) { return addcslashes($value,'\/'); }
  
}