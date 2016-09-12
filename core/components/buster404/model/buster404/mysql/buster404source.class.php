<?php
/**
 * @package buster404
 */
require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\', '/') . '/buster404source.class.php');
class Buster404Source_mysql extends Buster404Source {}
?>