<?php

/*
 * Umineko Project update manager
 * Encoding: UTF-8
 *
 * Copyright (c) 2011-2019 Umineko Project
 *
 * This document is considered confidential and proprietary,
 * and may not be reproduced or transmitted in any form 
 * in whole or in part, without the express written permission
 * of Umineko Project.
 */

define('LF', "\n");
define('CRLF', "\r\n");
define('DS', '/' /*DIRECTORY_SEPARATOR*/);
define('TAB', "\t");
define('MAGIC', 'ONS2');
define('VERSION', 110);
define('MAX_IN', 0x10000000);
define('UPDATE_MANAGER', true);
define('PASSWORD', '035646750436634546568555050');

$exclude = [
	'.DS_Store',
	'thumbs.db',
	'Thumbs.db',
	'umi_scr',
	'dlls',
	'onscripter-ru',
	'/en.txt',
	'/ru.txt',
	'/pt.txt',
	'/cn.txt',
	'/test.txt',
	'gmon.out',
	'head.png',
	'ons.cfg',
	'_shine.png',
	'shine.png',
	'script.file',
	'language_pt',
	'language_cn',
	'/en.file',
	'/ru.file',
	'/pt.file',
	'/pt.cfg',
	'/cn.file',
	'/cn.cfg',
	'/chiru.file',
	'/game.hash',
	'/default.cfg'
];

$include = [
];

function err($m) {
	echo $m.PHP_EOL;
	die(0);
}

function getUsage() {
	return 
	
	'Usage options:'.PHP_EOL.
	'	php update-manager.php hash directory hashfile'.PHP_EOL.
	'	php update-manager.php adler directory hashfile'.PHP_EOL.
	'	php update-manager.php size directory hashfile'.PHP_EOL.
	'	php update-manager.php verify old_hash_file new_hashfile [update.file json/ini]'.PHP_EOL.
	'	php update-manager.php dscript script_file scripting_folder locale'.PHP_EOL.
	'	php update-manager.php script script_file new_script last_episode'.PHP_EOL.
	'	php update-manager.php update update_file source_folder new_folder [archive_prefix]'.PHP_EOL;
	
}

function str_replace_first($search, $replace, $subject) {
	return implode($replace, explode($search, $subject, 2));
}

function str_nat_sort($str1, $str2) {
	if (substr($str1,0,4) == substr($str2,0,4)) {
		if (preg_match('/op/',$str1)) return -1;
		else if (preg_match('/op/',$str2)) return 1;
		else if ($str1 == $str2) return 0;
	}
	return strnatcmp($str1, $str2);
}

function extractGuid($bytes) {
	if (!$bytes)
		return NULL;

	$data = bin2hex($bytes);
	$data = str_pad($data, 32, '0', STR_PAD_LEFT);
	return $data;
}

function generateGuid() {
	if (function_exists('random_bytes'))
		$bytes = random_bytes(16);
	else if (function_exists('openssl_random_pseudo_bytes'))
		$bytes = openssl_random_pseudo_bytes(16);
	else
		return 'ea5078d1f71c405887bd54994bfeff24';
	return extractGuid($bytes);
}

function inplaceLines($data_dir, $in_dir, $by_dir) {
	$buffer = '';
	
	if (!file_exists($data_dir) || !file_exists($in_dir) || !file_exists($by_dir)) {
		die('Invalid directory(ies)'.PHP_EOL);
	}

	//Get the scripts_in
	$scripts_in = scandir($in_dir);
	array_shift($scripts_in);
	array_shift($scripts_in);
	for ($i = 0,$sizeof = sizeof($scripts_in); $i < $sizeof; $i++) {
		if (substr($scripts_in[$i],-3) != 'txt') {
			unset($scripts_in[$i]);
		}
	}
	sort($scripts_in);

	//Get the scripts_by
	$scripts_by = scandir($by_dir);
	array_shift($scripts_by);
	array_shift($scripts_by);
	for ($i = 0,$sizeof = sizeof($scripts_by); $i < $sizeof; $i++) {
		if (substr($scripts_by[$i],-3) != 'txt') {
			unset($scripts_by[$i]);
		}
	}
	sort($scripts_by);
	
	//Get the data_in
	$data_in = scandir($data_dir);
	array_shift($data_in);
	array_shift($data_in);
	for ($i = 0,$sizeof = sizeof($data_in); $i < $sizeof; $i++) {
		if (substr($data_in[$i],-3) != 'txt') {
			unset($data_in[$i]);
		}
	}
	$data_in = array_flip($data_in);
	
	if (sizeof($scripts_in) != sizeof($scripts_by)) {
		die('Invalid data in dirs'.PHP_EOL);
	}

	$tmp_guid = generateGuid();

	$scripts = array_combine($scripts_in,$scripts_by);
	unset($scripts_in);
	unset($scripts_by);
	uksort($scripts, 'str_nat_sort');
	foreach ($scripts as $in => $by) {
		$data_in = trim(file_get_contents($in_dir.$in));
		$data_by = trim(file_get_contents($by_dir.$by));
		$text = trim(file_get_contents($data_dir.$in));
		if ($data_in != '' && $data_by != '' && $text != '') {
			$data_in = explode(LF,$data_in);
			$data_by = explode(LF,$data_by);
			for ($i = 0; $i < sizeof($data_in); $i++) {
				if (!isset($data_in[$i])) {
					echo 'Missing data_in of '.$i.' in '.$in.' for '.$data_by[$i].PHP_EOL;
					continue;
				}
				
				if (!isset($data_by[$i])) {
					echo 'Missing data_by of '.$i.' in '.$by.' for '.$data_in[$i].PHP_EOL;
					continue;
				}

				// Fix first and last `s, also spaces, and double replacement.
				$data_by[$i] = trim($data_by[$i]);
				$data_by[$i] = '`' . $tmp_guid . substr($data_by[$i], $data_by[$i][0] == '`');
				if (substr($data_by[$i], -1) != '`')
					$data_by[$i] .= '`';
				
				$text = str_replace_first($data_in[$i],$data_by[$i],$text);
			}
		}

		$text = str_replace($tmp_guid, '', $text);

		$buffer .= $text.LF;
	}
	
	return $buffer;
}

function hashDir($dir, $base, &$map, $type) {
	if (!is_dir($dir)) err('No such directory '.$dir);

	$files = scandir($dir);
	for ($i = 2, $s = sizeof($files); $i < $s; $i++) {
		if (is_dir($dir.DS.$files[$i])) {
			hashDir($dir.DS.$files[$i], $base, $map, $type);
		} else {
			if ($type == 'adler') {
				$hash = hash('adler32', file_get_contents($dir.DS.$files[$i]));
			} else if ($type == 'size') {
				$hash = filesize($dir.DS.$files[$i]);
			} else {
				$hash = md5_file($dir.DS.$files[$i]);
			}
			//echo $hash.TAB.str_replace($base, '', $dir).DS.$files[$i].PHP_EOL;
			$map[str_replace($base, '', $dir).DS.$files[$i]] = $hash;
		}
	}
}

function hasIn($haystack, $needle) {
	if (!is_array($needle)) $needle = [$needle];
	foreach ($needle as $query) {
		if (strstr($haystack, $query) !== false) return true;
	}
	return false;
}

function filteredIniCreate($hashes, $mode) {
	global $exclude;

	$output = '[info]'.CRLF;
	$output .= '"game"="UminekoPS3fication*"'.CRLF;
	$output .= '"hash"="'.$mode.'"'.CRLF;
	$output .= '"ver"="20190109-ru"'.CRLF;
	$output .= '"apiver"="2.2.0"'.CRLF;
	$output .= '"date"="ignore"'.CRLF;
	//$output .= '"date"="'.time().'"'.CRLF;
	$output .= '[data]'.CRLF;

	foreach ($hashes as $file => $hash) {
		if (!hasIn($file, $exclude) && !strstr($file, 'game.hash')) {
			if ($file[0] == '/') $file = substr($file, 1);
			$output .= '"'.$file.'"="'.$hash.'"'.CRLF; 
		}
	}

	return $output;
}

function compareHashes($old, $new) {
	global $exclude, $include;

	$out = [];
	$out['different'] = [];
	$out['delete'] = [];

	foreach ($old as $old_f => $old_h) {
		// Avoid any temporary files
		if (hasIn($old_f, $exclude) && isset($new[$old_f]))
			unset($new[$old_f]);
		
		$force_include = hasIn($old_f, $include);
		
		if (isset($new[$old_f]) || $force_include) {
			// Has change
			if ($new[$old_f] != $old_h || $force_include) {
				$out['different'][preg_replace('#^[\\/]#', '', $old_f)] = $new[$old_f];
			}
			unset($new[$old_f]);
		} else {
			// Redundant file
			$out['delete'][] = preg_replace('#^[\\/]#', '', $old_f);
		}
		unset($old[$old_f]);
	}
	
	foreach ($new as $new_f => $new_h) {
		unset($new[$new_f]);
		if (!hasIn($new_f, $exclude)) {
			$new[preg_replace('#^[\\/]#', '', $new_f)] = $new_h;
		}
	}
	
	$out['insert'] = $new;
	
	return $out;
}

function generateIncomplete($ep) {
	$num = [
		1 => 17,
		2 => 18,
		3 => 18,
		4 => 19,
		5 => 15,
		6 => 18,
		7 => 18,
		8 => 16
	];
	
	$out = '';
	
	for ($i = $ep; $i <= 8; $i++) {
		$out .= '*umi'.$i.'_op'.CRLF.'jskip_s goto *incomplete ~'.CRLF;
		for ($j = 1; $j <= $num[$i]; $j++) {
			$out .= '*umi'.$i.'_'.$j.CRLF.'jskip_s goto *incomplete ~'.CRLF;
		}
		$out .= '*umi'.$i.'_end'.CRLF.'*teatime_'.$i.CRLF.'jskip_s goto *incomplete ~'.CRLF.'*teatime_'.$i.'_end'.CRLF.'*ura_teatime_'.$i.CRLF.'jskip_s goto *incomplete ~'.CRLF.'*ura_'.$i.'_end'.CRLF;
	}
	
	return $out;
}

function filterScript(&$script, $ep) {
	if (!is_numeric($ep) || $ep < 1) err('Invalid episode number');

	$incomplete = generateIncomplete($ep+1);

	$start = strpos($script, '*umi'.($ep+1).'_op');
	if ($start !== false) {
		$len = strlen("ura_8_end\ngoto *end_game");
		$end = strpos($script, "ura_8_end\ngoto *end_game", $start);
		if ($end === false) {
			$end = strpos($script, "ura_8_end\r\ngoto *end_game", $start);
			$len = strlen("ura_8_end\r\ngoto *end_game");
		}

		if ($end !== false)
			$script = substr_replace($script, $incomplete, $start, $end + strlen("ura_8_end\ngoto *end_game") - $start);
	}

	$script = str_replace('.txt', '.file', $script);
}

function localiseScript(&$script, $locale) {
	$lstart = '#locale_import "';
	$lstart_len = strlen($lstart);
	$lend = '"';
	$lend_len = strlen($lend);

	do {
		$start = strpos($script, $lstart);
		if ($start === false)
			break;

		$end = strpos($script, $lend, $start + $lstart_len);
		if ($end === false)
			break;

		$file = substr($script, $start + $lstart_len, $end - $lstart_len - $start);

		if (!preg_match('/^[a-z0-9_]+\.txt$/ui', $file))
			break;

		$dst = str_replace(CRLF, LF, file_get_contents($locale . $file));

		$script = substr_replace($script, $dst, $start, $end + $lend_len - $start);
	} while ($start !== false);
}

function xorData($data, $pass) {
	$key_table = [ 0xc0, 0xbc, 0x86, 0x66, 0x84, 0xf3, 0xbe, 0x90, 0xb0, 0x02, 0x98, 0x5e, 0x0f, 0x9c, 0x7b, 0xf4, 0xd9, 0x91, 0xdb, 0xeb, 0x81, 0x74, 0x3a, 0xe3, 0x76, 0x94, 0x21, 0x93, 0x63, 0x68, 0x0d, 0xa1, 0xba, 0xaa, 0x1b, 0xa0, 0x49, 0x2b, 0xe1, 0xe7, 0x38, 0xa6, 0x25, 0x53, 0x40, 0x4a, 0xec, 0x29, 0x36, 0xbf, 0xf2, 0x9f, 0xac, 0x0c, 0xcb, 0x00, 0x1f, 0xf1, 0x7c, 0x80, 0x4f, 0x60, 0x82, 0x62, 0x14, 0x6d, 0xd8, 0x32, 0x13, 0x2f, 0xe0, 0x99, 0xf7, 0x10, 0xd1, 0x30, 0x64, 0x4e, 0x8c, 0xde, 0xc1, 0x6a, 0xad, 0xa7, 0xb5, 0x95, 0xcf, 0xc6, 0x0b, 0x2d, 0x69, 0x24, 0x5c, 0xc5, 0x03, 0xda, 0xd6, 0x8e, 0xa3, 0x88, 0x31, 0x17, 0x3c, 0xb3, 0xa8, 0xb4, 0x01, 0x0e, 0xfc, 0x37, 0x65, 0x16, 0x6c, 0xbb, 0x50, 0x55, 0x2a, 0xe5, 0x77, 0x97, 0x09, 0xb1, 0x04, 0x67, 0xc7, 0x79, 0x71, 0x7a, 0x43, 0xd0, 0x22, 0x58, 0x0a, 0x57, 0xb7, 0xae, 0x4d, 0xc8, 0xe9, 0x46, 0xd3, 0x5b, 0x96, 0xcc, 0x3f, 0xe6, 0x3e, 0x54, 0x5f, 0x1d, 0xfa, 0xf0, 0x3d, 0x7d, 0x83, 0xa5, 0xfd, 0xef, 0x15, 0x8b, 0x70, 0x6b, 0xe2, 0xff, 0x07, 0xd7, 0x92, 0x41, 0x61, 0x75, 0x6f, 0x7f, 0xc4, 0xd5, 0xf9, 0x05, 0x34, 0xfe, 0x5d, 0xdc, 0xb9, 0xe8, 0xab, 0xca, 0xc3, 0x35, 0x08, 0x3b, 0xa2, 0xbd, 0x8f, 0x7e, 0x2e, 0x44, 0x5a, 0x12, 0xed, 0xe4, 0x11, 0x1e, 0xc2, 0x78, 0xf5, 0xaf, 0xf6, 0x72, 0x28, 0x9d, 0x6e, 0x39, 0xd2, 0xea, 0x45, 0x73, 0x47, 0x9e, 0x26, 0x89, 0x85, 0x52, 0x33, 0xdf, 0xa4, 0x48, 0x23, 0xce, 0x1c, 0x8d, 0x18, 0x27, 0x9a, 0xb6, 0xa9, 0xee, 0xb8, 0xc9, 0x2c, 0xfb, 0x59, 0x56, 0x20, 0x42, 0xcd, 0x51, 0xb2, 0x06, 0x19, 0x4b, 0x9b, 0xd4, 0x8a, 0x4c, 0xf8, 0x87, 0x1a, 0xdd ];
	$data = str_split($data);
	$s = sizeof($data);
	
	if ($s == 0)
		err('Nothing to xor');
	
	for ($i = 0; $i < $s; $i++) {
		$c = ord($data[$i]);
		
		if ($pass != 2) {
			$c = $key_table[$c ^ 0x71] ^ 0x45;
		} else {
			$c = $key_table[$c ^ 0x23] ^ 0x86;
		}
	
		$data[$i] = chr($c);
	}
	
	return implode('', $data);
}

function transformScript($data) {
	$res = '';
	do {
		$res .= xorData(substr($data, 0, 131072), 1);
		$data = substr($data, 131072);
	} while($data != '');
	
	$data = zlib_encode($res, 15);
	
	$res = '';
	do {
		$res .= xorData(substr($data, 0, 131072), 2);
		$data = substr($data, 131072);
	} while($data != '');
	
	return $res;
}

function encodeScript($data) {
	$out_size = strlen($data);

	$data = transformScript($data);
	$hdr = MAGIC;
	$hdr .= pack('LLL', strlen($data), $out_size, VERSION); 

	return $hdr.$data;
}

function main($argc, $argv) {
	if ($argc < 2) err(getUsage());
	
	ini_set('memory_limit','2048M');

	switch ($argv[1]) {
		case 'hash':
		case 'adler':
		case 'size':
			if ($argc < 4) err(getUsage());
			$hashes = [];
			hashDir($argv[2], $argv[2], $hashes, $argv[1]);
			if ($argv[1] == 'hash')
				$out = json_encode($hashes);
			else
				$out = filteredIniCreate($hashes, $argv[1]);
			file_put_contents($argv[3], $out);
			break;
		case 'verify':
			if ($argc < 4) err(getUsage());
			if (!file_exists($argv[2])) err('No such file '.$argv[2]);
			if (!file_exists($argv[3])) err('No such file '.$argv[3]);
			$old_hashes = json_decode(file_get_contents($argv[2]), true);
			$new_hashes = json_decode(file_get_contents($argv[3]), true);
			$modifications = compareHashes($old_hashes, $new_hashes);	
			$fixture = '';
			foreach ($modifications as $sect => $content) {
				$fixture .= CRLF.'['.$sect.']'.CRLF;
				foreach ($content as $key => $value) {
					if (is_numeric($key))
						$fixture .= '"'.$value.'"="DO"'.CRLF;
					else
						$fixture .= '"'.$key.'"="'.$value.'"'.CRLF;
				}
			}
			$fixture .= CRLF.'[update]'.CRLF.'"hash"="'.md5($fixture).'"'.CRLF.CRLF;
			if ($argc > 5)
				file_put_contents($argv[4], $argv[5] == 'json' ? json_encode($modifications) : $fixture);
			else
				echo $fixture;
			break;
		case 'dscript':
			if ($argc < 5) err(getUsage());
			$ver    = '8.2a' . ($argc > 5 ? ' r' . $argv[5] : '');
			$locale = $argv[4];
			$gameid = 'UminekoPS3fication'.ucfirst($locale);
			$scripting = $argv[3];

			$script = file_get_contents($scripting.'/script/umi_hdr.txt').LF;
			$script = str_replace(CRLF, LF, $script);
			$script = str_replace('builder_id', $gameid, $script);
			$script = str_replace('builder_date', time(), $script);
			$script = str_replace('builder_localisation', $locale, $script);
			$script = str_replace('builder_version', $ver, $script);
			
			for ($i = 1; $i <= 8; $i++) {
				$tldir = $scripting.'/story/ep'.$i.'/'.$locale.'/';
				if (!is_dir($tldir))
					$tldir = $scripting.'/story/ep'.$i.'/en/';
				$script .= inplaceLines($scripting.'/game/main/', $scripting.'/story/ep'.$i.'/jp/', $tldir);
			}
			$script .= inplaceLines($scripting.'/game/omake/', $scripting.'/story/omake/jp/',
				$scripting.'/story/omake/'.$locale.'/');

			$footer = file_get_contents($scripting.'/script/umi_ftr.txt');
			$script .= str_replace(CRLF, LF, $footer);

			localiseScript($script, $scripting.'/script/'.$locale.'/');

			file_put_contents($argv[2], $script);
			break;
		case 'script':
			if ($argc < 5) err(getUsage());
			if (!file_exists($argv[2])) err('No such file '.$argv[2]);
			$script = file_get_contents($argv[2]);
			filterScript($script, $argv[4]);
			file_put_contents($argv[3], encodeScript($script));
			break;
		case 'update':
			if ($argc < 5) err(getUsage());
			$update =  json_decode(file_get_contents($argv[2]), true);
			if (!is_dir($argv[3])) err('No source dir '.$argv[3]);
			if (!is_dir($argv[4])) mkdir($argv[4], 0755, true);
			
			foreach ($update as $sect => $content) {
				if ($sect != 'insert' && $sect != 'different') continue;
				foreach ($content as $file => $hash) {
					$dir = $argv[4].DS.dirname($file);
					if (!is_dir($dir)) mkdir($dir, 0755, true);
					copy($argv[3].DS.$file, $argv[4].DS.$file);
				}
			}
			
			if ($argc > 5) {
				$archive = $argv[5] . '_' . date('d.m.y') . '.7z';
				$folder = $argv[4] . DS . '*';
				system('7z a '.$archive.' -t7z -m0=lzma2 -mx=9 -mfb=64 -md=128m -ms=on -mhe -v1023m -p'.PASSWORD.' '.$folder);
				system('rm -rf '.$argv[4]);
			}
					
			break;
		default:
			 err(getUsage());
			break;
	}
	
	
}

main($argc, $argv);
