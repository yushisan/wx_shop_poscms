<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

$active_group	= 'default';
$query_builder	= TRUE;

$db['default']	= array(
	'dsn'		=> '',
	'hostname'	=> '127.0.0.1',
	'username'	=> 'a3lseGx6',
	'password'	=> 'Fi2BSd5TPnQ6FFbX',
	'port'		=> '3306',
	'database'	=> 'a3lseGx6',
	'dbdriver'	=> 'mysqli',
	'dbprefix'	=> 'imt_',
	'pconnect'	=> FALSE,
	'db_debug'	=> true,
	'cache_on'	=> FALSE,
	'cachedir'	=> 'cache/sql/',
	'char_set'	=> 'utf8',
	'dbcollat'	=> 'utf8_general_ci',
	'swap_pre'	=> '',
	'autoinit'	=> FALSE,
	'encrypt'	=> FALSE,
	'compress'	=> FALSE,
	'stricton'	=> FALSE,
	'failover'	=> array(),
);
