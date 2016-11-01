<?php
/**
 * @package buster404
 */
require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\', '/') . '/buster404url.class.php');
class Buster404Url_mysql extends Buster404Url {}
