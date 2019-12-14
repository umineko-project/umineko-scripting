<?php

/*
 * SNRParser.bt output optimiser
 * Encoding: UTF-8
 * Currently compatible with Umineko RONDO and CHIRU.
 *
 * Copyright (c) 2011-2018 Umineko Project
 *
 * This document is considered confidential and proprietary,
 * and may not be reproduced or transmitted in any form
 * in whole or in part, without the express written permission
 * of Umineko Project.
 */

require_once('lib/XMLParser.php');

define('LF', "\n");
define('CHIRU', true);
define('PRINT_USED', false);
if ($argc < 2)
	define('BASE_FOLDER', realpath(dirname(__FILE__).'/../../').'/scripting');
else
	define('BASE_FOLDER', $argv[1]);

if (CHIRU) {
	define('EP_DEF', 4);
	define('START', 5684);
} else {
	define('EP_DEF', 0);
	define('START', 626);
}

$g = [
	'log'		=> BASE_FOLDER.'/game/main/',
	'input' 	=> BASE_FOLDER.'/snr/output/script_'.(CHIRU == true ? 'chiru' : 'rondo').'.xml'
];

global $g;

function strsplit( $str ) {
	return preg_split('/(?<!^)(?!$)/u', $str);
}

function logstr( $str ) {
	global $g;

	$append = NULL;

	foreach ($g['modifications'] as $id => $lines) {
		if ($id == $g['dlg_id']) {
			foreach ($lines as $line => $command) {
				if ($line == $str) {
					if (isset($g['modifications'][$id][$line]['skip'])) {
						$g['modifications'][$id][$line]['skip']--;
						if ($g['modifications'][$id][$line]['skip'] < 0)
							$append = $command;
					} else {
						$append = $command;
					}
					if ($append && isset($g['modifications'][$id][$line]['count'])) {
						$g['modifications'][$id][$line]['count']--;
						if ($g['modifications'][$id][$line]['count'] == 0) {
							unset($g['modifications'][$id][$line]);
						}
					}
					break;
				}
			}
			break;
		}
	}

	if (isset($append['before']))
		$g['out'][$g['curr_chapter']] .= $append['before'].LF;

	if (!isset($append['ignore']) && !$g['todevnull'])
		$g['out'][$g['curr_chapter']] .= $str.LF;

	if (isset($append['after']))
		$g['out'][$g['curr_chapter']] .= $append['after'].LF;

	if (isset($append['increment']) && $append['increment'])
		$g['dlg_new']++;

	if (isset($append['todevnull']))
		$g['todevnull'] = $append['todevnull'];
}

function logend() {
	global $g;

	foreach($g['out'] as $filename => $data) {
		$file = fopen($g['log'].'/'.$filename,'wb');
		fwrite($file,$g['out'][$filename]);
		fclose($file);
	}
}

function err( $str ) {
	global $g;
	logstr($str);
	logend();
	echo 'Exception: '.$str.LF;
	die();
}


function initGlobals() {
	global $g;

	mb_regex_encoding('UTF-8');
	mb_internal_encoding("UTF-8");

	$g['todevnull'] = false;

	$g['names'] = [
		'南條　輝正'				=>	'nan',
		'右代宮　金蔵'			=>	'kin',
		'呂ノ上　源次'			=>	'gen',
		'右代宮　戦人'			=>	'but',
		'右代宮　絵羽'			=>	'eva',
		'右代宮　秀吉'			=>	'hid',
		'右代宮　霧江'			=>	'kir',
		'右代宮　留弗夫'			=>	'rud',
		'右代宮　真里亞'			=>	'mar',
		'右代宮　楼座'			=>	'ros',
		'右代宮　譲治'			=>	'geo' ,
		'右代宮　朱志香'			=>	'jes',
		'熊沢　チヨ'				=>	'kum',
		'郷田　俊朗'				=>	'goh',
		'右代宮　夏妃'			=>	'nat',
		'右代宮　蔵臼'			=>	'cla',
		'右代宮　縁寿'			=>	'enj',
		'須磨寺　霞'				=>	'kas',
		'紗音'					=>	'sha',
		'嘉音'					=>	'kan',
		'エヴァ・ベアトリーチェ'	=>	'ev2',
		'ベアトリーチェ'			=>	'bea',
		'ロノウェ'				=>	'ron',
		'アスモデウス'			=>	'rg7',
		'レヴィアタン'			=>	'rg2',
		'ワルギリア'				=>	'wal',
		'ベルンカステル'			=>	'ber',
		'ラムダデルタ'			=>	'lam',
		'ベルゼブブ'				=>	'rg6',
		'さくたろう'				=>	'sak',
		'マモン'					=>	'rg5',
		'サタン'					=>	'rg3',
		'ベルフェゴール'			=>	'rg4',
		'ルシファー'				=>	'rg1',
		'山羊の従者'				=>	'goa',
		'シエスタ４１０'			=>	's41',
		'シエスタ４５'			=>	's45',
		'小此木　鉄郎'			=>	'oko',
		'天草　十三'				=>	'ama',
		'大月教授'				=>	'pro',
		'シエスタ００'			=>	's00',
		'ガァプ'					=>	'gap',
		'南條　雅行'				=>	'na2',
		'熊沢　鯖吉'				=>	'ku2',
		'川畑船長'				=>	'kaw',
		//chiru
		'古戸　ヱリカ'			=>	'eri', //46
		'ドラノール'				=>	'dla', //47
		'ガートルード'			=>	'ger', //48
		'コーネリア'				=>	'cor', //49
		'フェザリーヌ'			=>	'fea', //50
		'ゼパル'					=>	'zep', //51
		'フルフル'				=>	'fur', //52
		'右代宮　理御'			=>	'rio', //53
		'ウィラード・Ｈ・ライト'	=>	'wil', //54
		'クレル'					=>	'cur', //55
		'八城　幾子'				=>	'fe2', //56
		'八城　十八'				=>	'bu3', //57
		//'右代宮　金蔵'			=>	'ki2', //58
		//59 for extra bea
		'姉ベアトリーチェ'			=>	'be3', //60
		//11 for witchy enj
		'エンジェ・ベアトリーチェ'	=>	'en2', //11
		'寿ゆかり'				=>	'en3', //11
	];

	$g['emot'] = [
		'デフォルト' => 'default',
		'汗' => 'ase',
		'痛い' => 'itai',
		'後編' => 'kohen',
		'ありがとう' => 'arigatou',
		'放心' => 'hoshin',
		'★★' => 'starstar',
		'★' => 'star',
		'挑発' =>'chohatsu',
		'焦り' =>'aseri',
		'えっと' =>'etto',
		'超泣く'=>'chonaku',
		'泣き笑い'=>'nakiwarai',
		'てれ笑い'=>'terewarai',
		'大笑い'=>'owarai',
		'愛想笑い'=>'aisowarai',
		'おだやか'=>'odayaka',
		'思案'=>'shian',
		'やれやれ'=>'yareyare',
		'我慢'=>'gaman',
		'悔しがる'=>'kuyasigaru',
		'エロ'=>'ero',
		'余裕'=>'yoou',
		'かしこまり'=>'kashikomari',
		'まじめ'=>'majime',
		'呆れ'=>'akire',
		'半ベソ'=>'hanbeso',
		'睨む'=>'niramu',
		'お任せ'=>'omakase',
		'言い訳'=>'iiwake',
		'ぶぅ'=>'buu',
		'あちゃー'=>'atya',
		'照れる'=>'tereru',
		'とほほ'=>'tohoho',
		'うー'=>'uuu',
		'泣き'=>'naki',
		'ふむ'=>'fumu',
		'不敵'=>'futeki',
		'おや？'=>'oyaa',
		'おや'=>'oya',
		'ニヤリ'=>'niyari',
		'不機嫌'=>'fukigen',
		'叫ぶ'=>'sakebu',
		'疲れ'=>'tukare',
		'ヒス'=>'hisu',
		'頭痛'=>'zutuu',
		'微笑みクール' => 'hohoemicool',
		'微笑み'=>'hohoemi',
		'焦る'=>'aseru',
		'驚愕'=>'kyogaku',
		'悩む'=>'nayamu',
		'得意'=>'tokui',
		'泣く'=>'naku',
		'恥じらい'=>'hajirai',
		'不満'=>'fuman',
		'デフォ'=>'defo',
		'困る'=>'komaru',
		'真面目'=>'majime',
		'悪笑い'=>'akuwarai',
		'怒り'=>'ikari',
		'笑い'=>'warai',
		'驚き'=>'odoroki',
		'散' => 'chiru',
		'頭正面' => 'aoshoumen',
		'Ｋ'=>'k',
		'ｒ'=>'r',
		'ａ'=>'a',
		'ｂ'=>'b',
		'ｃ'=>'c',
		'ｄ'=>'d',
		'ｅ'=>'e',
		'ｆ'=>'f',
		'ｇ'=>'g',
		'ｈ'=>'h',
		'後'=>'go',
		'１１'=>'11',
		'１'=>'1',
		'２'=>'2',
		'３'=>'3',
		'４'=>'4',
		'５'=>'5',
		'６'=>'6',
		'７'=>'7',
		'８'=>'8',
		'９'=>'9',
		'０'=>'0',
		' '=>'',
		'、'=>'' //BUT_A21_、まじめ１ typo
	];

	$hwk 	= strsplit('｢｣ｧｨｩｪｫｬｭｮｱｲｳｴｵｶｷｸｹｺｻｼｽｾｿﾀﾁﾂﾃﾄﾅﾆﾇﾈﾉﾊﾋﾌﾍﾎﾏﾐﾑﾒﾓﾔﾕﾖﾗﾘﾙﾚﾛﾜｦﾝｰｯ､ﾟﾞ･?｡');
	$hira 	= strsplit('「」ぁぃぅぇぉゃゅょあいうえおかきくけこさしすせそたちつてとなにぬねのはひふへほまみむめもやゆよらりるれろわをんーっ、？！…　。');
	$g['sym'] = array_combine($hwk,$hira);

	//************************************************************
	//Game storables (registers, flags etc.)
	//************************************************************

	//convert_dialogue temp
	$g['text_colour']			= '';

	//Game status
	$g['episode_num']			= EP_DEF;
	$g['chapter_num']			= -1;

	//Text processing
	$g['textbox_mode']			= true;
	$g['current_char']			= 'non';
	//$g['need_textoff'] 			= false;
	$g['dlg_id']				= 0;
	$g['dlg_new']				= 0;

	//Registers with default values
	$g['def_regs'] = [
		 0 		=>	0,
		 1 		=>	0,
		 2 		=>	0,
		 3 		=>	0,
		 4 		=>	0,
		 5 		=>	0,
		 6 		=>	0,
		 7 		=>	0,
		 8 		=>	0,
		 9 		=>	0,
		10 		=>	0,
		11 		=>	0,
		12 		=>	0,
		13 		=>	0,
		14 		=>	0,
		15 		=>	0,
		16 		=>	255,	//A
		17 		=>	255,	//R
		18 		=>	255,	//G
		19 		=>	255,	//B
		20 		=>	0,		//MONOCRO
		21 		=>	0,		//ADD_BLEND
		22 		=>	0,
		23 		=>	0,
		24 		=>	0,
		25 		=>	0,
		26 		=>	0,
		27 		=>	0,
		28 		=>	0,
		29 		=>	0,
		30 		=>	0,
		31		=>	0,
		32		=>	0,
		33		=>	0,
		34		=>	0,
		35		=>	0,
		36		=>	0,
		37		=>	0,
		38		=>	0,
		39		=>	0
	];
	$g['regs'] = $g['def_regs'];

	$g['stack'] = [];

	//Sprites and sets
	$g['current_set']			= '0';
	$g['spriteset_state']		= [ true, false, false, false ];
	$g['sprite_def'] = [
		'xpos'			=> '0',
		'ypos'			=> '0',
		'z_order'		=> 'SAME',
		'alpha'			=> 255,
		'darken_r'		=> 255,
		'darken_g'		=> 255,
		'darken_b'		=> 255,
		'monocro'		=> 0,
		'add'			=> 0,
		'centrex'		=> '0',
		'centrey'		=> '0',
		'scalex'		=> 100,
		'scaley'		=> 100,
		'rot'			=> '0',
		'rain'			=> false,
		'rain_spd'		=> '800',
		'rain_obj'		=> '500',
		'rain_wind'		=> '0',
		'rain_orbs'		=> false,
		'rain_pause'	=> false,
		'blur'			=> '0',
		'flip'			=> '0'
	];

	reset_sprites();

	//Tip and Char compatibility hacks, yay!
	$g['tips_c'] = -1;
	$g['chars_c'] = -1;
	// These may be used for verification
	if (CHIRU==true) {
		$g['labeladdrs'] = [
			'0x5a1c5a' => false,
			'0x5a2e7e' => false,
			'0x5a54b8' => false,
			'0x5a7cc7' => false,
			'0x5a815f' => false,
			'0x5a8828' => false,
			'0x5a9d42' => false,
			'0x5aab1f' => false,
			'0x5ad9f6' => false,
			'0x5ae699' => false,
			'0x5ae76f' => false,
			'0x5aeb5f' => false,
			'0x5af3dc' => false,
			'0x5afaef' => false,
			'0x5b001e' => false,
			'0x5b00f4' => false,
			'0x5b0527' => false,
			'0x5b10d5' => false,
			'0x5b3ca9' => false,
			'0x5b4558' => false,
			'0x5b4632' => false,
			'0x5b4c58' => false,
			'0x5b5823' => false,
			'0x5b603a' => false,
			'0x5b6893' => false,
			'0x5b696d' => false,
			'0x5b71d4' => false,
			'0x5b7c62' => false,
			'0x5bac09' => false,
			'0x5bbad5' => false,
			'0x5bbbaf' => false,
			'0x5bc242' => false,
			'0x5bccf9' => false,
			'0x5bd911' => false,
			'0x5be0c3' => false,
			'0x5be19d' => false,
			'0x5bea20' => false,
			'0x5bf0ea' => false,
			'0x5bf9db' => false,
			'0x5c1c58' => false,
			'0x5c2b3b' => false,
			'0x5c2c15' => false,
			'0x5c3928' => false,
			'0x5c426b' => false,
			'0x5c4d07' => false,
			'0x5c5350' => false,
			'0x5c542a' => false,
			'0x5c665d' => false,
			'0x5c71ca' => false,
			'0x5c8502' => false,
			'0x5ca9ed' => false,
			'0x5cb1ea' => false,
			'0x5cb2c4' => false,
			'0x5cb6d4' => false,
			'0x5cbb74' => false,
			'0x5cc36f' => false,
			'0x5cd2f4' => false,
			'0x5cd300' => false,
			'0x5cd3da' => false,
			'0x5cdfe0' => false,
			'0x5ce711' => false,
			'0x5cfa98' => false,
			'0x5d1cc7' => false,
			'0x5d2543' => false,
			'0x5d2615' => false,
			'0x5d30b7' => false,
			'0x5d35e8' => false,
			'0x5d405e' => false,
			'0x5d406f' => false,
			'0x5d414d' => false,
			'0x5d4b93' => false,
			'0x5d5163' => false,
			'0x5d5836' => false,
			'0x5d7ecd' => false,
			'0x5da6db' => false,
			'0x5da7ad' => false,
			'0x5db51d' => false,
			'0x5dc2d5' => false,
			'0x5dd7e3' => false,
			'0x5e5cfe' => false,
			'0x5e5d2e' => false,
			'0x5e5d5e' => false,
			'0x5e5d8e' => false,
			'0x5e5dbe' => false,
			'0x5e5dee' => false,
			'0x5e5e1e' => false,
			'0x5e5e4e' => false,
			'0x5e5e7e' => false,
			'0x5e5eae' => false,
			'0x5e5ede' => false,
			'0x5e5f10' => false,
			'0x5e5f42' => false,
			'0x5e5f74' => false,
			'0x5e5fa6' => false,
			'0x5e5fd8' => false,
			'0x5e600a' => false,
			'0x5e603c' => false,
			'0x5e6069' => false,
			'0x5e6751' => false,
			'0x5e67bc' => false,
			'0x5e6825' => false,
			'0x5e689d' => false,
			'0x5e6917' => false,
			'0x5e6987' => false,
			'0x5e69f0' => false,
			'0x5e6a69' => false,
			'0x5e6ae1' => false,
			'0x5e6b53' => false,
			'0x5e6bc6' => false,
			'0x5e6c3a' => false,
			'0x5e6cb0' => false,
			'0x5e6d27' => false,
			'0x5e6d9f' => false,
			'0x5e6e14' => false,
			'0x5e6e90' => false,
			'0x5e6f00' => false,
			'0x5e6f74' => false,
			'0x61551d' => false,
			'0x61564d' => false,
			'0x6183b3' => false,
			'0x61c14b' => false,
			'0x61da35' => false,
			'0x61ee2b' => false,
			'0x61fa6f' => false,
			'0x620d86' => false,
			'0x621d2c' => false,
			'0x6226c0' => false,
			'0x623179' => false,
			'0x624406' => false,
			'0x625351' => false,
			'0x6268bc' => false,
			'0x627403' => false,
			'0x62852f' => false,
			'0x6292e3' => false,
			'0x62a1c6' => false,
			'0x62af80' => false,
			'0x62b8a8' => false,
			'0x62c61e' => false,
			'0x62d2fb' => false,
			'0x62e0c7' => false,
			'0x62efd9' => false,
			'0x62f8bc' => false,
			'0x62ff77' => false,
			'0x63079d' => false,
			'0x630d78' => false,
			'0x63163b' => false,
			'0x72c4f3' => false,
			'0x72c6ae' => false,
			'0x72c6c4' => false,
			'0x72c6d5' => false,
			'0x72c820' => false,
			'0x72c976' => false,
			'0x72cacf' => false,
			'0x72cb19' => false,
			'0x72cbfd' => false,
			'0x72ccce' => false,
			'0x72ce1c' => false,
			'0x72ce24' => false,
			'0x72cf84' => false,
			'0x72d0ae' => false,
			'0x72d1cb' => false,
			'0x72d296' => false,
			'0x72d4ec' => false,
			'0x72d745' => false,
			'0x72d96f' => false,
			'0x72dabc' => false,
			'0x72dc0c' => false,
			'0x72e028' => false,
			'0x72e0cc' => false,
			'0x72e16e' => false,
			'0x72e21f' => false,
			'0x72e2d2' => false,
			'0x72e37b' => false,
			'0x72e41d' => false,
			'0x72e4cf' => false,
			'0x72e580' => false,
			'0x72e62b' => false,
			'0x72e6d7' => false,
			'0x72e784' => false,
			'0x72e833' => false,
			'0x72e8e3' => false,
			'0x72e994' => false,
			'0x72ea42' => false,
			'0x72eaf7' => false,
			'0x72eba0' => false,
			'0x72ec4d' => false,
			'0x72ed55' => false,
			'0x72ee2b' => false,
			'0x72ef04' => false,
			'0x72ef89' => false,
			'0x72f132' => false,
			'0x72f2e0' => false,
			'0x73cb90' => false,
			'0x73d8d2' => false,
			'0x73d976' => false,
			'0x73da18' => false,
			'0x73dac9' => false,
			'0x73db7c' => false,
			'0x73dc25' => false,
			'0x73dcc7' => false,
			'0x73dd79' => false,
			'0x73de2a' => false,
			'0x73ded5' => false,
			'0x73df81' => false,
			'0x73e02e' => false,
			'0x73e0dd' => false,
			'0x73e18d' => false,
			'0x73e23e' => false,
			'0x73e2ec' => false,
			'0x73e3a1' => false,
			'0x73e44a' => false,
			'0x73e4fc' => false,
			'0x80d92d' => false,
		];
		$g['tips'] = [];
		$g['chars'] = [];
	} else {
		$g['labeladdrs'] = [];
		$g['tips'] = [
			'0',
			'1',
			'2',
			'3',
			'7',
			'8',
			'9',
			'10',
			'11',
			'14',
			'15',
			'0, 1, 2, 3, 4',
			'7, 8, 9, 10, 11',
			'14, 15',
			'0, 1, 2, 3, 4',
			'7, 8, 9, 10, 11',
			'14, 15'
		];
		$g['chars'] = [
			['id' => '4', 'mode' => '0'],
			['id' => '5', 'mode' => '0'],
			['id' => '6', 'mode' => '0'],
			['id' => '7', 'mode' => '0'],
			['id' => '8', 'mode' => '0'],
			['id' => '9', 'mode' => '0'],
			['id' => '10', 'mode' => '0'],
			['id' => '11', 'mode' => '0'],
			['id' => '12', 'mode' => '0'],
			['id' => '13', 'mode' => '0'],
			['id' => '14', 'mode' => '0'],
			['id' => '15', 'mode' => '0'],
			['id' => '16', 'mode' => '0'],
			['id' => '17', 'mode' => '0'],
			['id' => '18', 'mode' => '0'],
			['id' => '19', 'mode' => '0'],
			['id' => '20', 'mode' => '0'],
			['id' => '21', 'mode' => '0'],
			['id' => '22', 'mode' => '0'],
			['id' => '23', 'mode' => '0'],
			['id' => '24', 'mode' => '0'],
			['id' => '25', 'mode' => '0'],
			['id' => '26', 'mode' => '0'],
			['id' => '27', 'mode' => '0'],
			['id' => '28', 'mode' => '0'],
			['id' => '29', 'mode' => '0'],
			['id' => '30', 'mode' => '0'],
			['id' => '35', 'mode' => '0'],
			['id' => '36', 'mode' => '0'],
			['id' => '36', 'mode' => '0'],
			['id' => '36', 'mode' => '0'],
			['id' => '36', 'mode' => '0'],
			['id' => '37', 'mode' => '0'],
			['id' => '37', 'mode' => '0'],
			['id' => '37', 'mode' => '0'],
			['id' => '37', 'mode' => '0'],
			['id' => '37', 'mode' => '0'],
			['id' => '38', 'mode' => '0'],
			['id' => '37', 'mode' => '0'],
			['id' => '37', 'mode' => '0'],
			['id' => '38', 'mode' => '0'],
			['id' => '39', 'mode' => '0'],
			['id' => '37', 'mode' => '0'],
			['id' => '37', 'mode' => '0'],
			['id' => '37', 'mode' => '0'],
			['id' => '37', 'mode' => '0'],
			['id' => '37', 'mode' => '0'],
			['id' => '37', 'mode' => '0'],
			['id' => '37', 'mode' => '0'],
			['id' => '37', 'mode' => '0'],
			['id' => '37', 'mode' => '0'],
			['id' => '37', 'mode' => '0'],
			['id' => '37', 'mode' => '0'],
			['id' => '37', 'mode' => '0'],
			['id' => '37', 'mode' => '0'],
			['id' => '37', 'mode' => '0'],
			['id' => '37', 'mode' => '0'],
			['id' => '37', 'mode' => '0'],
			['id' => '37', 'mode' => '0'],
			['id' => '40', 'mode' => '0'],
			['id' => '40', 'mode' => '0'],
			['id' => '40', 'mode' => '0'],
			['id' => '40', 'mode' => '0'],
			['id' => '40', 'mode' => '0'],
			['id' => '40', 'mode' => '0'],
			['id' => '40', 'mode' => '0'],
			['id' => '40', 'mode' => '0'],
			['id' => '40', 'mode' => '0'],
			['id' => '40', 'mode' => '0'],
			['id' => '40', 'mode' => '0'],
			['id' => '40', 'mode' => '0'],
			['id' => '40', 'mode' => '0'],
			['id' => '40', 'mode' => '0'],
			['id' => '40', 'mode' => '0'],
			['id' => '40', 'mode' => '0'],
			['id' => '40', 'mode' => '0'],
			['id' => '40', 'mode' => '0'],
			['id' => '40', 'mode' => '0'],
			['id' => '41', 'mode' => '0'],
			['id' => '41', 'mode' => '0'],
			['id' => '41', 'mode' => '0'],
			['id' => '41', 'mode' => '0'],
			['id' => '41', 'mode' => '0'],
			['id' => '41', 'mode' => '0'],
			['id' => '41', 'mode' => '0'],
			['id' => '41', 'mode' => '0'],
			['id' => '41', 'mode' => '0'],
			['id' => '42', 'mode' => '0'],
			['id' => '43', 'mode' => '0'],
			['id' => '43', 'mode' => '0'],
			['id' => '43', 'mode' => '0'],
			['id' => '43', 'mode' => '0'],
			['id' => '43', 'mode' => '0'],
			['id' => '43', 'mode' => '0'],
			['id' => '43', 'mode' => '0'],
			['id' => '43', 'mode' => '0'],
			['id' => '43', 'mode' => '0'],
			['id' => '43', 'mode' => '0'],
			['id' => '43', 'mode' => '0'],
			['id' => '43', 'mode' => '0'],
			['id' => '43', 'mode' => '0'],
			['id' => '43', 'mode' => '0'],
			['id' => '44', 'mode' => '0'],
			['id' => '44', 'mode' => '0'],
			['id' => '44', 'mode' => '0'],
			['id' => '45', 'mode' => '0'],
			['id' => '45', 'mode' => '0'],
			['id' => '45', 'mode' => '0'],
			['id' => '45', 'mode' => '0'],
			['id' => '45', 'mode' => '0'],
			['id' => '46', 'mode' => '0'],
			['id' => '47', 'mode' => '0'],
			['id' => '47', 'mode' => '0'],
			['id' => '47', 'mode' => '0'],
			['id' => '47', 'mode' => '0'],
			['id' => '52', 'mode' => '0'],
			['id' => '52', 'mode' => '1'],
			['id' => '52', 'mode' => '0'],
			['id' => '52', 'mode' => '0'],
			['id' => '52', 'mode' => '1'],
			['id' => '52', 'mode' => '0'],
			['id' => '53', 'mode' => '0'],
			['id' => '53', 'mode' => '1'],
			['id' => '54', 'mode' => '1'],
			['id' => '54', 'mode' => '0'],
			['id' => '54', 'mode' => '0'],
			['id' => '54', 'mode' => '1'],
			['id' => '54', 'mode' => '0'],
			['id' => '54', 'mode' => '0'],
			['id' => '54', 'mode' => '1'],
			['id' => '54', 'mode' => '0'],
			['id' => '54', 'mode' => '1'],
			['id' => '54', 'mode' => '0'],
			['id' => '54', 'mode' => '1'],
			['id' => '54', 'mode' => '0'],
			['id' => '54', 'mode' => '0'],
			['id' => '54', 'mode' => '1'],
			['id' => '54', 'mode' => '0'],
			['id' => '54', 'mode' => '0'],
			['id' => '54', 'mode' => '1'],
			['id' => '54', 'mode' => '0'],
			['id' => '54', 'mode' => '1'],
			['id' => '54', 'mode' => '0'],
			['id' => '54', 'mode' => '1'],
			['id' => '55', 'mode' => '1'],
			['id' => '55', 'mode' => '0'],
			['id' => '56', 'mode' => '0'],
			['id' => '56', 'mode' => '0'],
			['id' => '56', 'mode' => '1'],
			['id' => '56', 'mode' => '0'],
			['id' => '56', 'mode' => '1'],
			['id' => '56', 'mode' => '0'],
			['id' => '56', 'mode' => '0'],
			['id' => '56', 'mode' => '1'],
			['id' => '57', 'mode' => '1'],
			['id' => '58', 'mode' => '1'],
			['id' => '59', 'mode' => '1'],
			['id' => '60', 'mode' => '1'],
			['id' => '60', 'mode' => '0'],
			['id' => '60', 'mode' => '0'],
			['id' => '60', 'mode' => '1'],
			['id' => '60', 'mode' => '0'],
			['id' => '60', 'mode' => '1'],
			['id' => '60', 'mode' => '0'],
			['id' => '61', 'mode' => '1'],
			['id' => '61', 'mode' => '0'],
			['id' => '61', 'mode' => '0'],
			['id' => '61', 'mode' => '0'],
			['id' => '61', 'mode' => '1'],
			['id' => '61', 'mode' => '0'],
			['id' => '61', 'mode' => '0'],
			['id' => '61', 'mode' => '0'],
			['id' => '61', 'mode' => '1'],
			['id' => '61', 'mode' => '1'],
			['id' => '62', 'mode' => '1'],
			['id' => '62', 'mode' => '1'],
			['id' => '62', 'mode' => '0'],
			['id' => '62', 'mode' => '0'],
			['id' => '62', 'mode' => '1'],
			['id' => '63', 'mode' => '1'],
			['id' => '63', 'mode' => '0'],
			['id' => '63', 'mode' => '1'],
			['id' => '63', 'mode' => '0'],
			['id' => '63', 'mode' => '1'],
			['id' => '63', 'mode' => '0'],
			['id' => '63', 'mode' => '0'],
			['id' => '63', 'mode' => '1'],
			['id' => '63', 'mode' => '0'],
			['id' => '63', 'mode' => '0'],
			['id' => '63', 'mode' => '1'],
			['id' => '63', 'mode' => '0'],
			['id' => '63', 'mode' => '0'],
			['id' => '64', 'mode' => '0'],
			['id' => '64', 'mode' => '0'],
			['id' => '65', 'mode' => '0'],
			['id' => '65', 'mode' => '0'],
			['id' => '66', 'mode' => '0'],
			['id' => '66', 'mode' => '0'],
			['id' => '66', 'mode' => '1'],
			['id' => '66', 'mode' => '0'],
			['id' => '66', 'mode' => '0'],
			['id' => '67', 'mode' => '0'],
			['id' => '68', 'mode' => '0'],
			['id' => '75', 'mode' => '0'],
			['id' => '76', 'mode' => '1'],
			['id' => '77', 'mode' => '1'],
			['id' => '77', 'mode' => '1'],
			['id' => '77', 'mode' => '0'],
			['id' => '77', 'mode' => '0'],
			['id' => '77', 'mode' => '0'],
			['id' => '78', 'mode' => '2'],
			['id' => '79', 'mode' => '2'],
			['id' => '80', 'mode' => '2'],
			['id' => '81', 'mode' => '2'],
			['id' => '81', 'mode' => '2'],
			['id' => '82', 'mode' => '2'],
			['id' => '83', 'mode' => '0'],
			['id' => '83', 'mode' => '1'],
			['id' => '83', 'mode' => '0'],
			['id' => '84', 'mode' => '1'],
			['id' => '84', 'mode' => '0'],
			['id' => '84', 'mode' => '1'],
			['id' => '84', 'mode' => '1'],
			['id' => '84', 'mode' => '0'],
			['id' => '84', 'mode' => '1'],
			['id' => '84', 'mode' => '0'],
			['id' => '84', 'mode' => '1'],
			['id' => '84', 'mode' => '0'],
			['id' => '84', 'mode' => '1'],
			['id' => '84', 'mode' => '1'],
			['id' => '84', 'mode' => '1'],
			['id' => '85', 'mode' => '1'],
			['id' => '86', 'mode' => '1'],
			['id' => '86', 'mode' => '2'],
			['id' => '87', 'mode' => '2'],
			['id' => '88', 'mode' => '2'],
			['id' => '89', 'mode' => '2'],
			['id' => '89', 'mode' => '2'],
			['id' => '89', 'mode' => '1'],
			['id' => '90', 'mode' => '1'],
			['id' => '91', 'mode' => '2'],
			['id' => '91', 'mode' => '2'],
			['id' => '91', 'mode' => '0'],
			['id' => '91', 'mode' => '0'],
			['id' => '92', 'mode' => '1'],
			['id' => '92', 'mode' => '2'],
			['id' => '93', 'mode' => '2'],
			['id' => '93', 'mode' => '0'],
			['id' => '93', 'mode' => '1'],
			['id' => '93', 'mode' => '0'],
			['id' => '93', 'mode' => '0'],
			['id' => '93', 'mode' => '0'],
			['id' => '94', 'mode' => '1'],
			['id' => '95', 'mode' => '1'],
			['id' => '96', 'mode' => '1'],
			['id' => '97', 'mode' => '1'],
			['id' => '98', 'mode' => '1'],
			['id' => '98', 'mode' => '1'],
			['id' => '98', 'mode' => '0'],
			['id' => '98', 'mode' => '0'],
			['id' => '98', 'mode' => '0'],
			['id' => '98', 'mode' => '0'],
			['id' => '98', 'mode' => '0'],
			['id' => '98', 'mode' => '0'],
			['id' => '98', 'mode' => '2'],
			['id' => '99', 'mode' => '2'],
			['id' => '99', 'mode' => '2'],
			['id' => '100', 'mode' => '2'],
			['id' => '101', 'mode' => '2'],
			['id' => '101', 'mode' => '0'],
			['id' => '101', 'mode' => '0'],
			['id' => '101', 'mode' => '0'],
			['id' => '101', 'mode' => '0'],
			['id' => '101', 'mode' => '0'],
			['id' => '101', 'mode' => '0'],
			['id' => '101', 'mode' => '0'],
			['id' => '101', 'mode' => '0'],
			['id' => '101', 'mode' => '0'],
			['id' => '101', 'mode' => '0'],
			['id' => '101', 'mode' => '0'],
			['id' => '101', 'mode' => '0'],
			['id' => '101', 'mode' => '0'],
			['id' => '101', 'mode' => '0'],
			['id' => '101', 'mode' => '0'],
			['id' => '101', 'mode' => '0'],
			['id' => '101', 'mode' => '0'],
			['id' => '101', 'mode' => '1'],
			['id' => '101', 'mode' => '0'],
			['id' => '102', 'mode' => '0'],
			['id' => '102', 'mode' => '1'],
			['id' => '103', 'mode' => '0'],
			['id' => '104', 'mode' => '1'],
			['id' => '104', 'mode' => '2'],
			['id' => '105', 'mode' => '2'],
			['id' => '106', 'mode' => '1'],
			['id' => '107', 'mode' => '1'],
			['id' => '108', 'mode' => '1'],
			['id' => '109', 'mode' => '1'],
			['id' => '110', 'mode' => '1'],
			['id' => '31', 'mode' => '0'],
			['id' => '32', 'mode' => '0'],
			['id' => '48', 'mode' => '0'],
			['id' => '49', 'mode' => '0'],
			['id' => '69', 'mode' => '1'],
			['id' => '70', 'mode' => '1'],
			['id' => '111', 'mode' => '0'],
			['id' => '112', 'mode' => '0'],
			['id' => '113', 'mode' => '0'],
			['id' => '114', 'mode' => '0'],
			['id' => '115', 'mode' => '0'],
			['id' => '116', 'mode' => '0'],
			['id' => '117', 'mode' => '0'],
			['id' => '118', 'mode' => '0'],
			['id' => '119', 'mode' => '0'],
			['id' => '120', 'mode' => '0'],
			['id' => '121', 'mode' => '0'],
			['id' => '33', 'mode' => '0'],
			['id' => '34', 'mode' => '0'],
			['id' => '50', 'mode' => '0'],
			['id' => '50', 'mode' => '0'],
			['id' => '51', 'mode' => '0'],
			['id' => '71', 'mode' => '0'],
			['id' => '72', 'mode' => '0'],
			['id' => '73', 'mode' => '1'],
			['id' => '74', 'mode' => '1'],
			['id' => '122', 'mode' => '1']
		];
	}

	//Effects
	$g['eff'] = [
		2	=>	'10,:DELAY:',
		22	=>	'10,:DELAY:',
		42	=>	'10,:DELAY:',
		62	=>	'10,:DELAY:',
		80	=>	'10,:DELAY:',
		3	=>	'18,:DELAY:,msk_left',
		23	=>	'18,:DELAY:,msk_left',
		43	=>	'18,:DELAY:,msk_left',
		63	=>	'18,:DELAY:,msk_left',
		83	=>	'18,:DELAY:,msk_left',
		4	=>	'18,:DELAY:,msk_right',
		24	=>	'18,:DELAY:,msk_right',
		44	=>	'18,:DELAY:,msk_right',
		64	=>	'18,:DELAY:,msk_right',
		84	=>	'18,:DELAY:,msk_right',
		5	=>	'18,:DELAY:,msk_down',
		25	=>	'18,:DELAY:,msk_down',
		45	=>	'18,:DELAY:,msk_down',
		65	=>	'18,:DELAY:,msk_down',
		85	=>	'18,:DELAY:,msk_down',
		6	=>	'18,:DELAY:,msk_up',
		26	=>	'18,:DELAY:,msk_up',
		46	=>	'18,:DELAY:,msk_up',
		66	=>	'18,:DELAY:,msk_up',
		86	=>	'18,:DELAY:,msk_up',
		7	=>	'18,:DELAY:,msk_x',
		27	=>	'18,:DELAY:,msk_x',
		47	=>	'18,:DELAY:,msk_x',
		67	=>	'18,:DELAY:,msk_x',
		8	=>	'18,:DELAY:,msk_c',
		28	=>	'18,:DELAY:,msk_c',
		48	=>	'18,:DELAY:,msk_c',
		68	=>	'18,:DELAY:,msk_c',
		88	=>	'18,:DELAY:,msk_c',
		9	=>	'18,:DELAY:,msk_m1',
		29	=>	'18,:DELAY:,msk_m1',
		49	=>	'18,:DELAY:,msk_m1',
		69	=>	'18,:DELAY:,msk_m1',
		10	=>	'18,:DELAY:,msk_1',
		30	=>	'18,:DELAY:,msk_1',
		50	=>	'18,:DELAY:,msk_1',
		11	=>	'18,:DELAY:,msk_2',
		31	=>	'18,:DELAY:,msk_2'
	];

	$g['msk'] = [
		0	=>	'1',
		1	=>	'2',
		2	=>	'C',
		3	=>	'DOWN',
		4	=>	'LEFT',
		5	=>	'M1',
		6	=>	'RIGHT',
		7	=>	'UP',
		8	=>	'X',
		9	=>	'BLOOD',
		10	=>	'cut',
		11	=>	'test',
		12	=>	'1920x1080',
		13	=>	'cutA',
		14	=>	'cutB',
		15	=>	'cutC',
		16	=>	'cutD',
		17	=>	'cutZ',
		18	=>	'cutA2',
		19	=>	'cutUL',
		20	=>	'cutDR',
		21	=>	'cutUR',
		22	=>	'cutDL',
		23	=>	'cutYR',
		24	=>	'cutYL',
		25	=>	'cut_up',
		26	=>	'cut_down',
		27	=>	'test2'
	];

	$g['msk_st'] = [
		'msk1' => -1,
		'msk2' => -1,
		'msk3' => -1,
	];

	$g['missing_voices'] = [
		'"16"*"10600020"' => true,
		'"10"*"20101265"' => true,
		'"01"*"31500075"' => true,
		'"01"*"31500079"' => true,
		'"01"*"31500080"' => true,
		'"01"*"31500093"' => true,
		'"10"*"30100414"' => true,
		'"10"*"30100657"' => true,
		'"10"*"30101271"' => true,
		'"10"*"30101469"' => true,
		'"10"*"30101613"' => true,
		'"11"*"32000001"' => true,
		'"11"*"42000026"' => true,
		'"11"*"42000045"' => true,
		'"11"*"42000117"' => true,
		'"11"*"42000277"' => true,
		'"11"*"420009512"' => true,
		'"11"*"42000972"' => true,
		'"11"*"42001053"' => true,
		'"13"*"40401069"' => true,
		'"11"*"42001401"' => true,
		'"11"*"42001463"' => true,
		'"10"*"40100727"' => true,
		'"11"*"42001671"' => true,
		'"11"*"42001693"' => true,
		'"11"*"42001698"' => true,
		'"11"*"42001746"' => true,
		'"10"*"90100412"' => true,
		'"05"*"91000003"' => true,
		'"11"*"92000029"' => true
	];

	$g['curr_chapter'] = 'umi'.(EP_DEF + 1).'_1.txt';
	$g['out'] = [];
	$g['out'][$g['curr_chapter']] = '';

	$g['rain_bak'] = [];
	$g['rain_style'] = 0;

	$g['volume_level'] = [
		'bgm'	=>	0,
		'me1'	=>	0,
		'me2'	=>	0,
		'me3'	=>	0,
		'me4'	=>	0,
		'me5'	=>	0,
		'me6'	=>	0,
		'me7'	=>	0,
		'se1'	=>	0,
		'se2'	=>	0,
		'se3'	=>	0,
		'se4'	=>	0,
		'se5'	=>	0,
		'se6'	=>	0,
		'se7'	=>	0,
		'se8'	=>	0,
		'se9'	=>	0,
		'se10'	=>	0,
		'se11'	=>	0,
		'se12'	=>	0,
		'se13'	=>	0,
		'se14'	=>	0,
		'se15'	=>	0,
		'se16'	=>	0
	];
	$g['volume_enabled'] = [
		'bgm'	=>	false,
		'me1'	=>	false,
		'me2'	=>	false,
		'me3'	=>	false,
		'me4'	=>	false,
		'me5'	=>	false,
		'me6'	=>	false,
		'me7'	=>	false,
		'se1'	=>	false,
		'se2'	=>	false,
		'se3'	=>	false,
		'se4'	=>	false,
		'se5'	=>	false,
		'se6'	=>	false,
		'se7'	=>	false,
		'se8'	=>	false,
		'se9'	=>	false,
		'se10'	=>	false,
		'se11'	=>	false,
		'se12'	=>	false,
		'se13'	=>	false,
		'se14'	=>	false,
		'se15'	=>	false,
		'se16'	=>	false
	];

	$g['big_images'] = [
		'm_door4l',
		'wclo_1l',
		'eve_last1',
		'c_e0305_a',
		'c_e0305_c',
		'c_e0305_d',
		'end_5a',
		'end_5c',
		'end_6a',
		'end_6b',
		'end_7a',
		'end_7c',
		'end_8a',
		'end_8a_t1',
		'end_8a_t2',
		'end_8b',
		'end_8c',
		'reend_1c',
		'reend_1c'
	];

	$g['stretched_videos'] = [
		'door_rev',
		'door',
		'no01a',
		'no0004',
		'no0004a',
		'no0004b',
		'no0004c',
		'no0004d',
		'no0004e',
		'no0004f',
		'no0004g',
		'no0006',
		'no0008a',
		'no0008b',
		'no0014c',
		'no0015',
		'no0020a',
		'no38b',
		'no39b',
		'no40b',
		'no54',
		'no55',
		'no57',
		'no59',
		'no63',
		'ship1a',
		'ship1b',
		'ship2',
		'ship3'
	];

	$g['align'] = 0;

	$g['used_bgs'] = [];
	$g['used_sps'] = [];
	$g['used_anim'] = [];
	$g['used_mov'] = [];
	$g['used_voice'] = [];

	$g['committed'] = true;

	$g['propwaits'] = [
		'sprite' => [],
		'set' => [],
		'global' => []
	];

	$g['modifications'] = [
		// umi5_op broken bern effect
		35484 => [
			'flush 1' => [
				'before'	=> 'aspt2 breakup,s0_8,1000'.LF.'aspt2 alpha,s0_8,0',
				'after'	=> 'aspt2 breakup,s0_8,0,2000'.LF.'aspt2 alpha,s0_8,255,2000'.LF.'sptwait2 breakup,s0_8'
			]
		],
		// umi5_12, missing bern's phrase
		40330 => [
			'meplay 1,18,50' => [
				'after' => 'msgwnd_ber'.LF.'*d40331'.LF.'d [lv 0*"28"*"52100478"]`「あらそう？　ありがとう。`[@][lv 0*"28"*"52100479"]`......そして失望だわ、ヱリカ。`[@][lv 0*"28"*"52100480"]`退屈だわ、ヱリカ。`[@][lv 0*"28"*"52100481"]`この私の駒にして分身のあなたにもお手上げなんて、本当に情けなくて笑えるわ。`[@][lv 0*"28"*"52100482"]`わからないなら、いつまでも沈黙してて頂戴。`[@][lv 0*"28"*"52100483"]`そしてわかった時だけ口を開いて頂戴。」`[\\]',
				'increment' => true
			]
		],
		// cas_ep5 broken effect
		42257 => [
			'flush 10,17' => [
				'ignore'	=> true
			]
		],
		// ep8 snow
		58285 => [
			'lbg s0_3,"cit_2a"' => [
				'before'	=> 'snow s0_4'
			]
		],
		58286 => [
			'lbg s0_3,"black"' => [
				'before'	=> 'csp_slot s0_4'
			],
			'flush 22'			=> [
				'after'		=> 'snow -1'
			]
		],
		58297 => [
			'lbg s0_3,"cit_2a"' => [
				'before'	=> 'snow s0_4'
			]
		],
		58300 => [
			'lbg s0_3,"black"' => [
				'before'	=> 'csp_slot s0_4',
				'count'		=> 1
			],
			'flush 22'			=> [
				'after'		=> 'snow -1',
				'count'		=> 1
			]
		],
		// end_8a scroll
		65490 => [
			'lbg s0_3,"white"' => [
				'before'	=> 'moreram 2048'
			],
			'vol_bgm 60' => [
				'after'		=> 'systemcall sync'
			],
			'lbg2 s0_1,"end_8a"' => [
				'before'	=> 'lbg2 s0_1,end_8a_current',
				'ignore'	=> true
			],
			'drop_cache "end_8a"' => [
				'before'	=> 'drop_cache end_8a_current',
				'ignore'	=> true,
				'after'		=> 'aspt2 scaley,s0_1,end_8a_scaley'
			]
		],
		// ep5 subtitles
		68517 => [
			'bgmplay 134,100,1' => [
				'before'	=> 'bgmplay2 134,100,1'.LF.'lsp s0_29,"*8"',
				'ignore'	=> true
			],
			'csp_slot s0_28' => [
				'after'	=> 'csp_slot s0_29'
			],
		],
		// ep6 subtitles
		50495 => [
			'bgmplay 160,100,1' => [
				'before'	=> 'bgmplay2 160,100,1'.LF.'lsp s0_29,"*8"',
				'ignore'	=> true
			],
		],
		50501 => [
			'vol_bgm -1,1000' => [
				'after'	=> 'csp_slot s0_29',
			],
		],
		// ep7 subtitles
		68717 => [
			'bgmplay 186,100,1' => [
				'before'	=> 'bgmplay2 186,100,1'.LF.'lsp s0_29,"*8"',
				'ignore'	=> true
			],
			// end_7c double load
			'lbg2 s0_28,"end_7c"' => [
				'ignore'	=> true,
				'count'		=> 1
			],
			'drop_cache "end_7c"' => [
				'ignore'	=> true,
				'count'		=> 1
			]
		],
		68721 => [
			'vol_bgm -1,1000' => [
				'after'	=> 'csp_slot s0_29',
			],
		],
		// ep5 shutters
		40649 => [
			'msgwnd_non' => [
				'before'	=> 'textoff'.LF.'waits 167'.LF.'seplay 1,25,50'.LF.'lbg s0_1,"m2f_r4ans_bg"'.LF.'lbg s0_3,"m2f_r4an"'.LF.'lbg s0_26,"view_efes"'.LF.'flush 23'
			]
		],
		40672 => [
			'lbg s0_1,"m2f_r4ao_bg"' => [
				'before'	=> 'lbg s0_1,"m2f_r4ans_bg"',
				'ignore'	=> true
			],
			'lbg s0_3,"m2f_r4ao"' => [
				'before'	=> 'lbg s0_3,"m2f_r4an"',
				'ignore'	=> true
			],
			'lbg s0_26,"view_efe"' => [
				'before'	=> 'lbg s0_26,"view_efes"',
				'ignore'	=> true
			]
		],
		40696 => [
			'lbg s0_1,"m2f_r4ao_bg"' => [
				'before'	=> 'lbg s0_1,"m2f_r4ans_bg"',
				'ignore'	=> true
			],
			'lbg s0_3,"m2f_r4ao"' => [
				'before'	=> 'lbg s0_3,"m2f_r4an"',
				'ignore'	=> true
			],
			'lbg s0_26,"view_efe"' => [
				'before'	=> 'lbg s0_26,"view_efes"',
				'ignore'	=> true
			],
			'aspt2 darken_r,s0_8,186' => [
				'before'	=> 'aspt2 darken_r,s0_8,156',
				'ignore'	=> true
			],
			'aspt2 darken_g,s0_8,186' => [
				'before'	=> 'aspt2 darken_g,s0_8,156',
				'ignore'	=> true
			],
			'aspt2 darken_b,s0_8,190' => [
				'before'	=> 'aspt2 darken_b,s0_8,160',
				'ignore'	=> true
			]
		],
		40738 => [
			'lbg s0_1,"m2f_r4ao_bg"' => [
				'before'	=> 'lbg s0_1,"m2f_r4ans_bg"',
				'ignore'	=> true
			],
			'lbg s0_3,"m2f_r4ao"' => [
				'before'	=> 'lbg s0_3,"m2f_r4an"',
				'ignore'	=> true
			]
		],
		40746 => [
			'lbg s0_1,"m2f_r4ao_bg"' => [
				'before'	=> 'lbg s0_1,"m2f_r4ars_bg"',
				'ignore'	=> true
			],
			'lbg s0_3,"m2f_r4ao"' => [
				'before'	=> 'lbg s0_3,"m2f_r4ar"'.LF.'z_order_override s0_11,s0_0',
				'ignore'	=> true
			]
		],
		40749 => [
			'lbg s0_1,"m2f_r4ao_bg"' => [
				'before'	=> 'lbg s0_1,"m2f_r4ars_bg"',
				'ignore'	=> true
			],
			'lbg s0_3,"m2f_r4ao"' => [
				'before'	=> 'lbg s0_3,"m2f_r4ar"',
				'ignore'	=> true
			]
		],
		40752 => [
			'lbg s0_1,"m2f_r4ao_bg"' => [
				'before'	=> 'lbg s0_1,"m2f_r4ars_bg"',
				'ignore'	=> true
			],
			'lbg s0_3,"m2f_r4ao"' => [
				'before'	=> 'lbg s0_3,"m2f_r4ar"',
				'ignore'	=> true
			]
		],
		40764 => [
			'lbg s0_1,"m2f_r4ao_bg"' => [
				'before'	=> 'lbg s0_1,"m2f_r4ars_bg"',
				'ignore'	=> true
			],
			'lbg s0_3,"m2f_r4ao"' => [
				'before'	=> 'lbg s0_3,"m2f_r4ar"',
				'ignore'	=> true
			]
		],
		40765 => [
			'lbg s0_1,"m2f_r4ao_bg"' => [
				'before'	=> 'lbg s0_1,"m2f_r4ars_bg"',
				'ignore'	=> true
			],
			'lbg s0_3,"m2f_r4ao"' => [
				'before'	=> 'lbg s0_3,"m2f_r4ar"',
				'ignore'	=> true
			]
		],
		40767 => [
			'lbg s0_1,"m2f_r4ao_bg"' => [
				'before'	=> 'lbg s0_1,"m2f_r4ars_bg"',
				'ignore'	=> true
			],
			'lbg s0_3,"m2f_r4ao"' => [
				'before'	=> 'lbg s0_3,"m2f_r4ar"',
				'ignore'	=> true
			]
		],
		40771 => [
			'lbg s1_1,"m2f_r4ao_bg"' => [
				'before'	=> 'lbg s1_1,"m2f_r4ars_bg"',
				'ignore'	=> true
			],
			'lbg s1_3,"m2f_r4ao"' => [
				'before'	=> 'lbg s1_3,"m2f_r4ar"',
				'ignore'	=> true
			]
		],
		40772 => [
			'lbg s2_1,"m2f_r4ao_bg"' => [
				'before'	=> 'lbg s2_1,"m2f_r4ars_bg"',
				'ignore'	=> true
			],
			'lbg s2_3,"m2f_r4ao"' => [
				'before'	=> 'lbg s2_3,"m2f_r4ar"',
				'ignore'	=> true
			]
		],
		40773 => [
			'lbg s0_1,"m2f_r4ao_bg"' => [
				'before'	=> 'lbg s0_1,"m2f_r4ars_bg"',
				'ignore'	=> true
			],
			'lbg s0_3,"m2f_r4ao"' => [
				'before'	=> 'lbg s0_3,"m2f_r4ar"',
				'ignore'	=> true
			]
		],
		40775 => [
			'lbg s0_3,"m1f_p1d"' => [
				'before'	=> 'lbg s0_3,"m1f_p1d"'.LF.'z_order_override s0_11,s0_1',
				'ignore'	=> true
			]
		],
		40788 => [
			'lbg s0_1,"m2f_r4ao_bg"' => [
				'before'	=> 'lbg s0_1,"m2f_r4ars_bg"',
				'ignore'	=> true
			],
			'lbg s0_3,"m2f_r4ao"' => [
				'before'	=> 'lbg s0_3,"m2f_r4ar"'.LF.'z_order_override s0_11,s0_0',
				'ignore'	=> true
			]
		],
		40794 => [
			'lbg s0_3,"m2f_p1b"' => [
				'before'	=> 'lbg s0_3,"m2f_p1b"'.LF.'z_order_override s0_11,s0_1',
				'ignore'	=> true
			]
		],
		40807 => [
			'lbg s0_1,"m2f_r4ao_bg"' => [
				'before'	=> 'lbg s0_1,"m2f_r4ars_bg"',
				'ignore'	=> true
			],
			'lbg s0_3,"m2f_r4ao"' => [
				'before'	=> 'lbg s0_3,"m2f_r4ar"'.LF.'z_order_override s0_11,s0_0',
				'ignore'	=> true
			]
		],
		40811 => [
			'lbg s0_3,"m2f_p1b"' => [
				'before'	=> 'lbg s0_3,"m2f_p1b"'.LF.'z_order_override s0_11,s0_1',
				'ignore'	=> true
			]
		],
		40821 => [
			'lbg s0_1,"m2f_r4ao_bg"' => [
				'before'	=> 'lbg s0_1,"m2f_r4ars_bg"',
				'ignore'	=> true
			],
			'lbg s0_3,"m2f_r4ao"' => [
				'before'	=> 'lbg s0_3,"m2f_r4ar"'.LF.'z_order_override s0_11,s0_0',
				'ignore'	=> true
			]
		],
		40829 => [
			'lbg s0_1,"m2f_r4ao_bg"' => [
				'before'	=> 'lbg s0_1,"m2f_r4ars_bg"',
				'ignore'	=> true
			],
			'lbg s0_3,"m2f_r4ao"' => [
				'before'	=> 'lbg s0_3,"m2f_r4ar"',
				'ignore'	=> true
			]
		],
		40834 => [
			'lbg s0_1,"m2f_r4ao_bg"' => [
				'before'	=> 'lbg s0_1,"m2f_r4ars_bg"',
				'ignore'	=> true
			],
			'lbg s0_3,"m2f_r4ao"' => [
				'before'	=> 'lbg s0_3,"m2f_r4ar"',
				'ignore'	=> true
			]
		],
		40838 => [
			'lbg s0_1,"m2f_r4ao_bg"' => [
				'before'	=> 'lbg s0_1,"m2f_r4ars_bg"',
				'ignore'	=> true
			],
			'lbg s0_3,"m2f_r4ao"' => [
				'before'	=> 'lbg s0_3,"m2f_r4ar"',
				'ignore'	=> true
			],
			'lbg s0_3,"m1f_p1d"' => [
				'before'	=> 'lbg s0_3,"m1f_p1d"'.LF.'z_order_override s0_11,s0_1',
				'ignore'	=> true
			]
		],
		42166 => [
			'lbg s0_1,"m2f_r4ao_bg"' => [
				'before'	=> 'lbg s0_1,"m2f_r4ans_bg"',
				'ignore'	=> true
			],
			'lbg s0_3,"m2f_r4ao"' => [
				'before'	=> 'lbg s0_3,"m2f_r4an"',
				'ignore'	=> true
			],
			'lbg s0_26,"view_efe"' => [
				'before'	=> 'lbg s0_26,"view_efes"',
				'ignore'	=> true
			]
		],
		// ep5 borked background with wrong portrait
		38328 => [
			'lbg s0_1,"mlib_1a_bg"' => [
				'before'	=> 'lbg s0_1,"mlib_1ao_bg"',
				'ignore'	=> true
			],
			'lbg s0_3,"mlib_1a"' => [
				'before'	=> 'lbg s0_3,"mlib_1ao"',
				'ignore'	=> true
			]
		],
		38329 => [
			'lbg s0_1,"mlib_1c_bg"' => [
				'before'	=> 'lbg s0_1,"mlib_1co_bg"',
				'ignore'	=> true
			],
			'lbg s0_3,"mlib_1c"' => [
				'before'	=> 'lbg s0_3,"mlib_1co"',
				'ignore'	=> true
			]
		],
		38349 => [
			'lbg s0_1,"mlib_1a_bg"' => [
				'before'	=> 'lbg s0_1,"mlib_1ao_bg"',
				'ignore'	=> true
			],
			'lbg s0_3,"mlib_1a"' => [
				'before'	=> 'lbg s0_3,"mlib_1ao"',
				'ignore'	=> true
			]
		],
		38350 => [
			'lbg s0_1,"mlib_1a_bg"' => [
				'before'	=> 'lbg s0_1,"mlib_1ao_bg"',
				'ignore'	=> true
			],
			'lbg s0_3,"mlib_1a"' => [
				'before'	=> 'lbg s0_3,"mlib_1ao"',
				'ignore'	=> true
			]
		],
		38372 => [
			'lbg s0_1,"mlib_1c_bg"' => [
				'before'	=> 'lbg s0_1,"mlib_1co_bg"',
				'ignore'	=> true
			],
			'lbg s0_3,"mlib_1c"' => [
				'before'	=> 'lbg s0_3,"mlib_1co"',
				'ignore'	=> true
			]
		],
		38377 => [
			'lbg s0_1,"mlib_1a_bg"' => [
				'before'	=> 'lbg s0_1,"mlib_1ao_bg"',
				'ignore'	=> true
			],
			'lbg s0_3,"mlib_1a"' => [
				'before'	=> 'lbg s0_3,"mlib_1ao"',
				'ignore'	=> true
			]
		],
		38433 => [
			'lbg s0_1,"mlib_1c_bg"' => [
				'before'	=> 'lbg s0_1,"mlib_1co_bg"',
				'ignore'	=> true
			],
			'lbg s0_3,"mlib_1c"' => [
				'before'	=> 'lbg s0_3,"mlib_1co"',
				'ignore'	=> true
			]
		],
		38451 => [
			'lbg s0_1,"mlib_1a_bg"' => [
				'before'	=> 'lbg s0_1,"mlib_1ao_bg"',
				'ignore'	=> true
			],
			'lbg s0_3,"mlib_1a"' => [
				'before'	=> 'lbg s0_3,"mlib_1ao"',
				'ignore'	=> true
			]
		],
		38453 => [
			'lbg s0_1,"mlib_1c_bg"' => [
				'before'	=> 'lbg s0_1,"mlib_1co_bg"',
				'ignore'	=> true
			],
			'lbg s0_3,"mlib_1c"' => [
				'before'	=> 'lbg s0_3,"mlib_1co"',
				'ignore'	=> true
			]
		],
		// ep5 early removed rain
		42163 => [
			'csp_slot s0_11' => [
				'before'	=> ';csp_slot s0_11',
				'ignore'	=> true
			],
			'lbg s0_3,"g_o1a"' => [
				'after'	=> 'z_order_override s0_11,s0_11'
			]
		],
		42164 => [
			'lbg s0_3,"black"' => [
				'before'	=> 'csp_slot s0_11'
			]
		],
		// ep5 grimoire
		36915 => [
			'*d36915'.LF.'d [lv 0*"46"*"54500019"]`「ドンブリに盛ればいいんですっ。`[@][lv 0*"46"*"54500020"]`牛丼をスプーンで食べる人いますか？`[@][lv 0*"46"*"54500021"]`　いませんよねっ。`[@][lv 0*"46"*"54500022"]`日本人なら断じて絶対徹頭徹尾、お箸ですっ！`[@][lv 0*"46"*"54500023"]`　というわけで郷田さん。`[@][lv 0*"46"*"54500024"]`すみませんがお箸をお願いします。`[@][lv 0*"46"*"54500025"]`私が日本人として恥ずかしくない食べ方をご覧に入れて見せましょう！」`[\\]' => [
				'before'	=> '*d36915'.LF.'d [lv 0*"46"*"54500019"]`「ドンブリに盛ればいいんですっ。`[gstg 1][@][lv 0*"46"*"54500020"]`牛丼をスプーンで食べる人いますか？`[@][lv 0*"46"*"54500021"]`　いませんよねっ。`[@][lv 0*"46"*"54500022"]`日本人なら断じて絶対徹頭徹尾、お箸ですっ！`[@][lv 0*"46"*"54500023"]`　というわけで郷田さん。`[@][lv 0*"46"*"54500024"]`すみませんがお箸をお願いします。`[@][lv 0*"46"*"54500025"]`私が日本人として恥ずかしくない食べ方をご覧に入れて見せましょう！」`[\\]',
				'ignore'	=> true
			]
		],
		39063 => [
			'*d39063'.LF.'d2 [lv 0*"46"*"54500327"]`「この事件のハンニン、`[@][lv 0*"46"*"54500328"]`……全世界全歴史のミステリーを塗り替えかねない、とんでもない挑戦をしてるんですよ？`[@][lv 0*"46"*"54500329"]`　……もちろんそんなの皆様方は、とっくに気付いてましたよねェ……？`[@][lv 0*"46"*"54500330"]`　推理好きならミステリの女王の作品は必読。`[@][lv 0*"46"*"54500330b"]`さらに日本人なら、新本格ミステリーくらいは読破してて当然ですから。`[@][#][*][lv 0*"46"*"54500331"]`……皆さん、ホントに読書、足りてますゥ？`[@][lv 0*"46"*"54500332"]`　くすくすくすくす、くっくくくくくくくくく………」`[\\]' => [
				'before'	=> '*d39063'.LF.'d2 [lv 0*"46"*"54500327"]`「この事件のハンニン、`[@][lv 0*"46"*"54500328"]`……全世界全歴史のミステリーを塗り替えかねない、とんでもない挑戦をしてるんですよ？`[@][lv 0*"46"*"54500329"]`　……もちろんそんなの皆様方は、とっくに気付いてましたよねェ……？`[@][lv 0*"46"*"54500330"]`　推理好きならミステリの女王の作品は必読。`[@][lv 0*"46"*"54500330b"]`さらに日本人なら、新本格ミステリーくらいは読破してて当然ですから。`[gstg 2][@][#][*][lv 0*"46"*"54500331"]`……皆さん、ホントに読書、足りてますゥ？`[@][lv 0*"46"*"54500332"]`　くすくすくすくす、くっくくくくくくくくく………」`[\\]',
				'ignore'	=> true
			]
		],
		39125 => [
			'*d39125'.LF.'d2 [lv 0*"09"*"51300100"]`「なんだかんだ死なないから大丈夫よ。`[@][#][*][lv 0*"09"*"51300101"]`……それより、鎧戸が下りてなくて幸運だったわ。`[@][lv 0*"09"*"51300102"]`お父様の書斎では夜、鎧戸を下ろす習慣はないの？」`[\\]' => [
				'before'	=> '*d39125'.LF.'d2 [lv 0*"09"*"51300100"]`「なんだかんだ死なないから大丈夫よ。`[gstg 3][@][#][*][lv 0*"09"*"51300101"]`……それより、鎧戸が下りてなくて幸運だったわ。`[@][lv 0*"09"*"51300102"]`お父様の書斎では夜、鎧戸を下ろす習慣はないの？」`[\\]',
				'ignore'	=> true
			]
		],
		65782 => [
			'*d65782'.LF.'d [lv 0*"28"*"52100840_b"]`「ニンゲン狩りの伯爵よ」`[\\]' => [
				'before'	=> '*d65782'.LF.'d [lv 0*"28"*"52100840_b"]`「ニンゲン狩りの伯爵よ」`[gstg 4][\\]',
				'ignore'	=> true
			]
		],
		68341 => [
			'*d68341'.LF.'d2 [lv 0*"32"*"53700193"]`「足元がお留守だわ、お嬢ちゃん。`[@][#][*][lv 0*"32"*"53700194"]`{p:1:………全てのゲーム開始時に、右代宮金蔵は死んでいる！}」`[\\]' => [
				'before'	=> '*d68341'.LF.'d2 [lv 0*"32"*"53700193"]`「足元がお留守だわ、お嬢ちゃん。`[gstg 5][@][#][*][lv 0*"32"*"53700194"]`{p:1:………全てのゲーム開始時に、右代宮金蔵は死んでいる！}」`[\\]',
				'ignore'	=> true
			]
		],
		// ep5 ura dialogue
		68158 => [
			'*d68158'.LF.'d2 `{a:c:`[ak][text_speed_t 5][*][*][!w333]`そ`[*][!w333]`し`[*][!w333]`て`[*][!w333]`、`[*][!w333]`…`[*][!w333]`…`[*][!w333]`…`[*][!w333]`俺`[*][!w333]`は`[*][!w333]`、`[*][!w333]`…`[*][!w333]`…`[*][!w333]`知`[*][!w333]`る`[!w333]`。`[*]`}`[\\]' => [
				'before'	=> '*d68158'.LF.'d2 [ak][#][text_speed_t -222][text_fade_t 833]`{a:c:そして、………俺は、……知る。}`[\\]',
				'ignore'	=> true
			],
			'd_continue' => [
				'before'	=> 'waits 333',
				'ignore'	=> true
			]
		],
		45594 => [
			'bgmplay 100,71,0' => [
				'before'	=> 'mov %bgm_title_delay,2000',
				'after'		=> 'mov %bgm_title_delay,0'.LF.'jskip',
			],
			'gptwait onionalpha' => [
				'after'		=> '~',
			],
		],
		61952 => [
			'bgmplay 133,71,0' => [
				'before'	=> 'mov %bgm_title_delay,2000',
				'after'		=> 'mov %bgm_title_delay,0'.LF.'jskip',
			],
			'gptwait onionalpha' => [
				'after'		=> '~',
			],
		],
		46309 => [
			'csp_slot s0_24' => [
				'skip'		=> 1,
				'ignore'	=> true,
			],
			'csp_slot s0_23' => [
				'skip'		=> 1,
				'ignore'	=> true
			],
		],
		46312 => [
			'lbg s0_22,"white"' => [
				'before'	=> 'csp_slot s0_23'.LF.'csp_slot s0_24',
				'count'		=> 1,
			]
		],
		// ep6 voices
		48184 => [
			'*d48184'.LF.'d2 [lv 0*"46"*"64501114"]`「右代宮、戦人。`[#][*][|][lv 0*"46"*"64501115_o"]`…`[!w83]`…`[!w83]`…`[!w83]`…`[!w83]`…。`[#][*][!w83]`って`[!w500]`言`[!w500]`う`[!w500]`ン`[!w500]`です`[!w1167]`よォおおおぉおおおおおおぉおおおおおおおぉおおおおおおおぁあああああぁあああぁあああぁあぁああぁぁ、`[|][lv 0*"46"*"64501125"]`ロジックエラー動議を申請ッ！！」`[\\]' => [
				'before'	=> '*d48184'.LF.'d2 [lv 0*"46"*"64501114"]`「右代宮、戦人。`[#][*][|][lv 0*"46"*"64501115"][!w83]`…`[!w83]`…`[!w83]`…`[!w83]`…`[!w83]`…。`[#][*][!w83][d_setvoicewait 0][lv 0*"46"*"64501116"]`って`[|][lv 0*"46"*"64501117"]`言`[|][lv 0*"46"*"64501118"]`う`[|][lv 0*"46"*"64501119"]`ン`[|][lv 0*"46"*"64501120"]`です`[|][d_setvoicewait 500][lv 0*"46"*"64501121"]`よォおおおぉおおおおおおぉおおおおおおおぉおおおおおおおぁあああああぁあああぁあああぁあぁああぁぁ、`[|][lv 0*"46"*"64501125"]`ロジックエラー動議を申請ッ！！」`[\\]',
				'ignore'	=> true
			],
		],
		// ep6 burning scene optimisation & save fixes
		46095 => [
			'lbg s0_3,"m_door2h"' => [
				'ignore'	=> true,
				'after'		=> 'lbg s0_2,"m_door2h"'.LF.'z_order_override s0_3,s0_18'.LF.'flush 1'.LF.'csp_slot s0_3'.LF.'flush 22'.LF.'set_saving off'
			]
		],
		46096 => [
			'*d46096'.LF.'d [lv 0*"09"*"61300270"]`「ぐ、`[|][lv 0*"09"*"61300271"]`………ぎゃ、`[|][lv 0*"09"*"61300272"]`…………が…、`[|][lv 0*"09"*"61300273"]`…………、」`[\\]' => [
				'before'	=> '*d46096'.LF.'d [nj][lv 0*"09"*"61300270"]`「ぐ、`[|][lv 0*"09"*"61300271"]`………ぎゃ、`[|][lv 0*"09"*"61300272"]`…………が…、`[|][lv 0*"09"*"61300273"]`…………、」`[\\]',
				'ignore'	=> true
			],
			'csp_slot s0_17'	=>	[
				'before'	=> 'csp_slot s0_2'.LF.'set_saving on'
			]
		],
		// ep6 grimoire
		42598 => [
			'*d42598'.LF.'d2 [lv 0*"50"*"65000053"]`「…これよりそなた右代宮縁寿を、`[@][#][*][lv 0*"50"*"65000054"]`観劇の魔女、フェザリーヌ・アウグストゥス・アウローラの巫女にして朗読者に任ずる。`[@][lv 0*"50"*"65000055"]`そなたが朗読を終えるまで。`[@][lv 0*"50"*"65000056"]`そなたの朗読を妨げようとする全ての者に災いを与えよう」`[\\]' => [
				'before'	=> '*d42598'.LF.'d2 [lv 0*"50"*"65000053"]`「…これよりそなた右代宮縁寿を、`[@][#][*][lv 0*"50"*"65000054"]`観劇の魔女、フェザリーヌ・アウグストゥス・アウローラの巫女にして朗読者に任ずる。`[gstg 1][@][lv 0*"50"*"65000055"]`そなたが朗読を終えるまで。`[@][lv 0*"50"*"65000056"]`そなたの朗読を妨げようとする全ての者に災いを与えよう」`[\\]',
				'ignore'	=> true
			]
		],
		43751 => [
			'*d43751'.LF.'d [lv 0*"60"*"60700120"]`「………どうして、妾と同じ顔を……？」`[\\]' => [
				'before'	=> '*d43751'.LF.'d [lv 0*"60"*"60700120"]`「………どうして、妾と同じ顔を……？」`[gstg 2][\\]',
				'ignore'	=> true
			]
		],
		49453 => [
			'*d49453'.LF.'d2 [lv 0*"17"*"61700024"]`「そ、それが…！！`[@][#][*][lv 0*"17"*"61700025"]`　き、傷がないのに、`[|][lv 0*"17"*"61700026"]`……血、`[|][lv 0*"17"*"61700027"]`……血が止まらない…！」`[\\]' => [
				'before'	=> '*d49453'.LF.'d2 [lv 0*"17"*"61700024"]`「そ、それが…！！`[@][#][*][lv 0*"17"*"61700025"]`　き、傷がないのに、`[|][lv 0*"17"*"61700026"]`……血、`[waitvoice 2][lv 0*"17"*"61700027"]`……血が止まらない…！」`[\\]',
				'ignore'	=> true
			],
		],
		// ep6 geo bgm reappear
		49466 => [
			'vol_bgm -1,3000'	=> [
				'before'	=> 'vol_bgm 0,3000',
				'ignore'	=> true
			],
			'bgmplay 145,0,0'	=> [
				'ignore'	=> true
			]
		],
		50382 => [
			'lss s0_9,"eri","a11_akuwarai2" ;1' => [
				'before'	=> 'color_mod2 s0_3,#39C6FF ;#0000FF'.LF.'color_mod2 s0_9,#39C6FF ;#0000FF'.LF.'flush 99,2000,"breakup.dll/lrp"'
			],
			'seplay 3,43,71' => [
				'after'		=> 'flush 22'
			]
		],
		// ep6 ending subtitles
		68643 => [
			'bgmplay 161,100,1' => [
				'ignore'	=> true,
				'after'		=> 'bgmplay2 161,100,1'.LF.'lsp 2,"*8"'.LF.'flush 1',
			]
		],
		// ep6 cut ep2_text & ending subtitles
		68658 => [
			'vol_mix_fade 1000"' => [
				'before'	=> '_csp 2'
			],
			'lbg s0_3,"ep2_text"' => [
				'before'	=> 'bg #4a1010,0'
			],
			'flush 22' => [
				'before'	=> 'bg black,0'
			]
		],
		// ep6 broken kan animation
		46279 => [
			'lss s0_10,"kan","a12_ikari1" ;1' => [
				'before'	=> 'lss s0_10,"kan","a12_odoroki1"',
				'ignore'	=> true
			],
			'aspt2 breakup,s0_23,0' => [
				'after'		=> 'flush 1'
			],
			'flush 1 ;flush2: NO frames' => [
				'before'	=> 'lss s0_10,"kan","a12_ikari1" ;1'.LF.'aspt2 xpos,s0_10,370'.LF.'flush 65',
				'ignore'	=> true
			]
		],
		// ep7 missing voice command
		54331 => [
			'*d54331'.LF.'d `「……ベアトリーチェは夜の屋敷を徘徊する時、白い人影の姿をすることが多い」`[\\]' => [
				'before'	=> '*d54331'.LF.'d [lv 0*"55"*"75500376"]`「……ベアトリーチェは夜の屋敷を徘徊する時、白い人影の姿をすることが多い」`[\\]',
				'ignore'	=> true
			]
		],
		// ep7 killed gen sprite
		56144 => [
			'csp_slot s0_10' => [
				'ignore'	=> true
			]
		],
		56145 => [
			'lbg s0_3,"different_spiral_1a"' => [
				'before'	=> 'csp_slot s0_10'
			]
		],
		39662 => [
			'*d39662'.LF.'d `「……………………………」`[\\]' => [
				'before'	=> '*d39662'.LF.'d [lv 0*"10"*"50100841"]`「……………………………」`[\\]',
				'ignore'	=> true
			]
		],
		67632 => [
			'*d67632'.LF.'d `「……………………………。`[@][lv 0*"53"*"75300816"]`……ごめん、………ウィル………。`[@][lv 0*"53"*"75300817"]`……もう、……体が…………」`[\\]' => [
				'before'	=> '*d67632'.LF.'d [lv 0*"53"*"75300815"]`「……………………………。`[@][lv 0*"53"*"75300816"]`……ごめん、………ウィル………。`[@][lv 0*"53"*"75300817"]`……もう、……体が…………」`[\\]',
				'ignore'	=> true
			]
		],
		66816 => [
			';flush 1' => [
				'before'	=> 'flush 24',
				'ignore'	=> true
			]
		],
		64609 => [
			'csp_slot s0_5' => [
				'ignore'	=> true
			]
		],
		64612 => [
			'lbg s0_3,"black"' => [
				'before'	=> 'csp_slot s0_5'
			],
			'csp_slot s0_5' => [
				'ignore'	=> true
			],
		],
		64618 => [
			'lbg s0_3,"black"' => [
				'before'	=> 'csp_slot s0_5'
			],
			'csp_slot s0_5' => [
				'ignore'	=> true
			],
		],
		64622 => [
			'lbg s0_3,"black"' => [
				'before'	=> 'csp_slot s0_5'
			],
			'csp_slot s0_5' => [
				'ignore'	=> true
			],
		],
		64624 => [
			'csp_slot s0_17' => [
				'before'	=> 'csp_slot s0_5'
			],
			'csp_slot s0_5' => [
				'ignore'	=> true,
			]
		],
		64627 => [
			'csp_slot s0_10' => [
				'before'	=> 'csp_slot s0_5'
			],
			'csp_slot s0_5' => [
				'ignore'	=> true,
			]
		],
		64630 => [
			'lbg s0_3,"black"' => [
				'before'	=> 'csp_slot s0_5'
			],
		],
		46253 => [
			'*d46253'.LF.'d `「………………………………、」`[\\]' => [
				'before'	=> '*d46253'.LF.'d [lv 0*"12"*"61400094"]`「………………………………、」`[\\]',
				'ignore'	=> true
			]
		],
		58290 => [
			'rain_load s0_11'	=> [
				'before'	=> 'if %bgm_current_id != 187 bgmplay 187,71,0'
			],
		],
		60178 => [
			'mov $name_chapter_save, "8_09"' => [
				'after'		=> 'mov %bern_game_mode,0'
			],
			'bgmplay 144,71,0'	=> [
				'before'	=> '*bern_session_start'
			]
		],
		60220 => [
			'gstc 130'	=> [
				'before'	=> 'if %bern_game_mode != 0 jumpf'
			],
			'flush 22'	=> [
				'after'	=> '~'.LF.'if %bern_game_mode != 1 jumpf'.LF.'goto *uu_uu_0x61551d'.LF.'*uu_uu_0x6183b3'.LF.'vol_mix_fade 1000'.LF.'wait 1000'.LF.'gstc 136'.LF.'~',
				'count'	=> 1
			]
		],
		60272 => [
			'seplay 1,61,71'	=> [
				'before'	=> 'if %bern_game_mode != 0 jumpf'
			],
			'bgmplay 96,71,0'	=> [
				'before'	=> '~'.LF.'if %bern_game_mode != 1 jumpf'.LF.'goto *uu_uu_0x61551d'.LF.'*uu_uu_0x61c14b'.LF.'vol_mix_fade 1000'.LF.'wait 1000'.LF.'~',
			],
		],
		60296 => [
			'gstc 131'	=> [
				'before'	=> 'if %bern_game_mode != 0 jumpf'
			],
			'textoff'	=> [
				'skip'		=> 4,
				'count'		=> 1,
				'before'	=> '~'.LF.'if %bern_game_mode != 1 jumpf'.LF.'goto *uu_uu_0x61551d'.LF.'*uu_uu_0x61da35'.LF.'vol_mix_fade 1000'.LF.'wait 1000'.LF.'gstc 137'.LF.'~',
			]
		],
		60318 => [
			'gstc 132'	=> [
				'before'	=> 'if %bern_game_mode != 0 jumpf'
			],
			'bgmplay 142,71,0'	=> [
				'before'	=> '~'.LF.'if %bern_game_mode != 1 jumpf'.LF.'goto *uu_uu_0x61551d'.LF.'*uu_uu_0x61ee2b'.LF.'vol_mix_fade 1000'.LF.'wait 1000'.LF.'gstc 138'.LF.'~',
			],
		],
		60334 => [
			'gstc 133'	=> [
				'before'	=> 'if %bern_game_mode != 0 jumpf'
			],
			'bgmplay 147,71,0'	=> [
				'before'	=> '~'.LF.'if %bern_game_mode != 1 jumpf'.LF.'goto *uu_uu_0x61551d'.LF.'*uu_uu_0x61fa6f'.LF.'vol_mix_fade 1000'.LF.'wait 1000'.LF.'gstc 139'.LF.'~',
			],
		],
		60351 => [
			'bgmplay 97,71,0' => [
				'after'	=> 'mov %bern_game_mode,1'
			],
			'wait 1000'	=> [
				'skip'  => 1,
				'count'	=> 1,
				'after'	=> 'if %bern_game_mode = 1 goto *bern_session_end'
			],
		],
		60395 => [
			'bgmplay 144,71,0' => [
				'before'	=> 'goto *bern_session_start',
				'todevnull'	=> true,
				'ignore'	=> true
			],
			// Set msgwnd correctly after quiz reset
			'mov %branch_tmp,%get_response_value' => [
				'after'	=> 'if %branch_tmp < 6 msgwnd_non'
			]
		],
		60568 => [
			'wait 1000'	=> [
				'skip'  => 1,
				'count'	=> 1,
				'after'	=> '*bern_session_end',
				'todevnull'	=> false
			],
		],
		52150 => [
			'msgwnd_non' => [
				'ignore'	=> true
			],
			'*d52150'.LF.'d `彼女に金蔵とは血の繋がらない忘れ形見がいたことを南條が知るのは、その後しばらく経ってからのことである。`[\\]' => [
				'ignore'	=> true
			]
		],
		52152 => [
			'msgwnd_non' => [
				'ignore'	=> true
			],
			'*d52152'.LF.'d `だが南條がそれを知った時には、ベアトリーチェは既にひどく身体を患い、今際の際にあったらしい。残された娘は金蔵に託され、そして……。`[\\]' => [
				'ignore'	=> true
			]
		],
		53489 => [
			'msgwnd_non' => [
				'ignore'	=> true
			],
			'*d53489'.LF.'d `もちろん、金蔵とて最初に彼女を目にした時から亡きベアトリーチェの面影を映し、かつて遂げられなかった想いを強引に果たそうなどと思っていたわけではあるまい。`[\\]' => [
				'ignore'	=> true
			]
		],
		51013 => [
			'msgwnd_non' => [
				'ignore' => true,
				'before' => 'msgwnd_kir'
			],
			'*d51013'.LF.'d `ありえるわ、と霧江も秀吉の意見に小さくうなづく。`[\\]' => [
				'ignore' => true,
				'before' => '*d51013'.LF.'d [lv 0*"09"*"71300007"]`「愛人の娘……。`[@][lv 0*"09"*"71300008"]`ありえるわ。`[@][lv 0*"09"*"71300009"]`黄金を授けたベアトリーチェとの間に、お父さんは隠し子を儲けた……とか。」`[\\]'
			]
		],
		51041 => [
			'*d51041'.LF.'d [lv 0*"06"*"71100012"]`「ふぅぅむ……。`[@][lv 0*"06"*"71100014"]`その葬式もまともに挙げられんかったんなら、そら、お父さんも心苦しかったやろな……」`[\\]' => [
				'ignore' => true,
				'before' => '*d51041'.LF.'d [lv 0*"06"*"71100012"]`「ふぅぅむ……。`[@][lv 0*"06"*"71100013"]`隠し子っちゅうたって、血をわけた子供や。`[@][lv 0*"06"*"71100014"]`その葬式もまともに挙げられんかったんなら、そら、お父さんも心苦しかったやろな……」`[\\]'
			]
		],
		51266 => [
			'*d51266'.LF.'d [lv 0*"54"*"75400401b"]`「聞いたことがない……か、ある意味で金蔵の腹心らしい、模範的な返答と言える。`[@][lv 0*"54"*"75400402b"]`……何しろ、金蔵にとってその娘は、生まれ変わった{i:ベアトリーチェ}そのものなんだからな」`[\\]' => [
				'ignore' => true,
				'before' => '*d51266'.LF.'d [lv 0*"54"*"75400401b"]`「聞いたことがない……か、ある意味で金蔵の腹心らしい、模範的な返答と言える。`[@][lv 0*"54"*"75400402"]`……何しろ、金蔵にとってその娘は、生まれ変わりであって、`[@][lv 0*"54"*"75400403"]`{i:娘ではない}んだからな。」`[\\]'
			]
		],
		51282 => [
			'*d51282'.LF.'d [lv 0*"14"*"71600014"]`「…………ウィラードさま。`[@][lv 0*"14"*"71600015b"]`お館様は偉大なる魔法の儀式により、亡きベアトリーチェさまの生まれ変わりとなる器を得たのでございます。`[@][lv 0*"14"*"71600016"]`それを否定することは出来ません」`[\\]' => [
				'ignore' => true,
				'before' => '*d51282'.LF.'d [lv 0*"14"*"71600014"]`「…………ウィラードさま。`[@][lv 0*"14"*"71600015"]`お館様は偉大なる魔法の儀式により、亡きベアトリーチェさまの生まれ変わりの赤子を得たのでございます。`[@][lv 0*"14"*"71600016"]`それを否定することは出来ません」`[\\]'
			]
		],
		51283 => [
			'*d51283'.LF.'d [lv 0*"54"*"75400427b"]`「忘れられない想いも、慈しむ感情も否定はしねェ。`[@][lv 0*"54"*"75400428"]`そして金蔵がどれだけ、ベアトリーチェという女を深く愛していたかも、疑う気はねェ。`[@][lv 0*"54"*"75400429"]`………忘れ形見にどういう愛情を注ぐかは人の勝手だ。`[@][lv 0*"54"*"75400430"]`育つほどに似ていくその姿に、金蔵が愛ゆえに特別な感情を持ったとしても、`[@][lv 0*"54"*"75400431"]`俺はそれを人間らしい感情だと思うぜ」`[\\]' => [
				'ignore' => true,
				'before' => '*d51283'.LF.'d [lv 0*"54"*"75400427"]`「コウノトリを否定はしねェ。`[@][lv 0*"54"*"75400428"]`そして金蔵がどれだけ、ベアトリーチェという女を深く愛していたかも、疑う気はねェ。`[@][lv 0*"54"*"75400429"]`………忘れ形見にどういう愛情を注ぐかは人の勝手だ。`[@][lv 0*"54"*"75400430"]`育つほどに似ていくその姿に、金蔵が愛ゆえに特別な感情を持ったとしても、`[@][lv 0*"54"*"75400431"]`俺はそれを人間らしい感情だと思うぜ」`[\\]'
			]
		],
		52149 => [
			'*d52149'.LF.'d [lv 0*"19"*"71900025b"]`「平穏に、慎ましやかに暮らしたと聞きます」`[\\]' => [
				'ignore' => true,
				'before' => '*d52149'.LF.'d [lv 0*"19"*"71900025b"]`「平穏に、慎ましやかに暮らしたと聞きます。`[@][lv 0*"19"*"71900026_UP_MODIFIED"]`……それから数年の後に、彼女に子供が生まれましてな。」`[\\]'
			]
		],
		52151 => [
			'*d52151'.LF.'d [lv 0*"54"*"75400507b"]`「その娘こそ……九羽鳥庵のベアトリーチェ、か」`[\\]' => [
				'ignore' => true,
				'before' => '*d52151'.LF.'d [lv 0*"54"*"75400507"]`「……九羽鳥庵のベアトリーチェの誕生、か。」`[\\]'
			],
			'lbg s0_3,"black"' => [
				'ignore'	=> true,
			],
			'flush 25' => [
				'ignore'	=> true,
			]
		],
		52153 => [
			'*d52153'.LF.'d [lv 0*"19"*"71900027b"]`「金蔵さんの前では気丈に振舞われていたそうですが……。`[@][lv 0*"19"*"71900028"]`お気の毒なことです」`[\\]' => [
				'ignore' => true,
				'before' => '*d52153'.LF.'d [lv 0*"19"*"71900027"]`「出産が、うまく行かなかったそうで……。`[@][lv 0*"19"*"71900028"]`お気の毒なことです」`[\\]'
			]
		],
		52155 => [
			'*d52155'.LF.'d [lv 0*"54"*"75400508"]`「驚くな。`[@][lv 0*"54"*"75400509"]`金蔵はその娘を、生まれ変わりと信じたんだぞ。`[@][lv 0*"54"*"75400510"]`……心を、蔑ろにするんじゃねェ。推理は可能だ」`[\\]' => [
				'ignore' => true,
				'before' => '*d52155'.LF.'d [lv 0*"54"*"75400508"]`「驚くな。`[@][lv 0*"54"*"75400509_UP_MODIFIED"]`金蔵は娘を、生まれ変わりと信じたんだぞ。`[@][lv 0*"54"*"75400510"]`……心を、蔑ろにするんじゃねェ。推理は可能だ」`[\\]'
			]
		],
		52159 => [
			'*d52159'.LF.'d [lv 0*"54"*"75400511"]`「そして、その娘のために九羽鳥庵を作ったというわけか……」`[\\]' => [
				'ignore' => true,
				'before' => '*d52159'.LF.'d [lv 0*"54"*"75400511_UP_MODIFIED"]`「そして、娘のために九羽鳥庵を作ったというわけか……」`[\\]'
			]
		],
		53140 => [
			'*d53140'.LF.'d [lv 0*"54"*"75400846"]`「九羽鳥庵のベアトリーチェは、潜水艦で来たベアトリーチェの娘だ。`[@][lv 0*"54"*"75400847"]`……しかし金蔵は彼女をベアトリーチェの娘と思わなかった。`[|][lv 0*"54"*"75400848"]`生き写しの、生まれ変わりと信じた。`[@][lv 0*"54"*"75400849"]`……その生まれ変わりが、かつての年齢にまで成長し、まさに生き写しとなった時。`[@][lv 0*"54"*"75400850"]`金蔵は悲劇を犯したんだ」`[\\]' => [
				'ignore' => true,
				'before' => '*d53140'.LF.'d [lv 0*"54"*"75400846"]`「九羽鳥庵のベアトリーチェは、潜水艦で来たベアトリーチェの娘だ。`[@][lv 0*"54"*"75400847_UP_MODIFIED"]`……しかし金蔵は娘と思わなかった。`[|][lv 0*"54"*"75400848"]`生き写しの、生まれ変わりと信じた。`[@][lv 0*"54"*"75400849"]`……その生まれ変わりが、かつての年齢にまで成長し、まさに生き写しとなった時。`[@][lv 0*"54"*"75400850_UP_MODIFIED"]`金蔵は過ちを犯したんだ」`[\\]'
			]
		],
		53142 => [
			'*d53142'.LF.'d [lv 0*"28"*"72100099"]`「そういうこと。`[@][lv 0*"28"*"72100100"]`金蔵はベアトリーチェの娘をベアトリーチェとし、`[@][lv 0*"28"*"72100101"]`さらにそのベアトリーチェにあんたを生ませたの。`[@][lv 0*"28"*"72100102"]`……あんたは金蔵の子であると同時に、ベアトリーチェの孫、ということね」`[\\]' => [
				'ignore' => true,
				'before' => '*d53142'.LF.'d [lv 0*"28"*"72100099"]`「そういうこと。`[@][lv 0*"28"*"72100100_UP_MODIFIED"]`金蔵はベアトリーチェにベアトリーチェを生ませ、`[@][lv 0*"28"*"72100101"]`さらにそのベアトリーチェにあんたを生ませたの。`[@][lv 0*"28"*"72100102"]`……あんたは金蔵の子であると同時に、ベアトリーチェの孫、ということね」`[\\]'
			]
		],
		53484 => [
			'*d53484'.LF.'d [lv 0*"55"*"75500059"]`「源次は九羽鳥庵のベアトリーチェにもずっと仕えていた。`[@][lv 0*"55"*"75500060"]`その胸中についても詳しかった。`[@][lv 0*"55"*"75500061"]`彼女が金蔵の寵愛を一身に受け、父のように敬愛していたことも」`[\\]' => [
				'ignore' => true,
				'before' => '*d53484'.LF.'d [lv 0*"55"*"75500059"]`「源次は九羽鳥庵のベアトリーチェにもずっと仕えていた。`[@][lv 0*"55"*"75500060"]`その胸中についても詳しかった。`[@][lv 0*"55"*"75500061_UP_MODIFIED"]`彼女が金蔵の寵愛を一身に受け、父として敬愛していたことも」`[\\]'
			]
		],
		53485 => [
			'*d53485'.LF.'d [lv 0*"55"*"75500062"]`「しかしそれは金蔵のそれとは異なった。`[@][lv 0*"55"*"75500063"]`金蔵は彼女を、ベアトリーチェの生まれ変わりと思い、`[@][lv 0*"55"*"75500064"]`亡きベアトリーチェへの万感の思いの慰み者にしようとした。`[@][lv 0*"55"*"75500065"]`その思いを、九羽鳥庵のベアトリーチェが受け入れきれるわけもない。`[@][lv 0*"55"*"75500066"]`彼女は父のように思い、尊敬してきた金蔵自らに、`[@][lv 0*"55"*"75500067"]`その想いを踏みにじられることになるのである……」`[\\]' => [
				'ignore' => true,
				'before' => '*d53485'.LF.'d [lv 0*"55"*"75500062"]`「しかしそれは金蔵のそれとは異なった。`[@][lv 0*"55"*"75500063"]`金蔵は彼女を、ベアトリーチェの生まれ変わりと思い、`[@][lv 0*"55"*"75500064"]`亡きベアトリーチェへの万感の思いの慰み者にしようとした。`[@][lv 0*"55"*"75500065"]`その思いを、九羽鳥庵のベアトリーチェが受け入れきれるわけもない。`[@][lv 0*"55"*"75500066_UP_MODIFIED"]`彼女は父と思い、尊敬してきた金蔵自らに、`[@][lv 0*"55"*"75500067_UP_MODIFIED"]`純潔を奪われることになるのである……」`[\\]'
			]
		],
		53486 => [
			'*d53486'.LF.'d [lv 0*"51"*"75100039"]`「自分は父のように思ってたのに、向こうは妻と思ってた！」`[\\]' => [
				'ignore' => true,
				'before' => '*d53486'.LF.'d [lv 0*"51"*"75100039_UP_MODIFIED"]`「自分は父と思ってたのに、向こうは妻と思ってた！」`[\\]'
			]
		],
		53487 => [
			'*d53487'.LF.'d [lv 0*"52"*"75200041b"]`「あぁ、それは想いのすれ違いが生んだ悲しい恋！」`[\\]' => [
				'ignore' => true,
				'before' => '*d53487'.LF.'d [lv 0*"52"*"75200041"]`「あぁ、それは両立出来ぬ禁じられた恋！」`[\\]'
			]
		],
		53488 => [
			'*d53488'.LF.'d [lv 0*"14"*"71600037"]`「………金蔵さまのお気持ちも、わからぬわけではありません」`[\\]' => [
				'ignore' => true,
				'before' => '*d53488'.LF.'d [lv 0*"14"*"71600037"]`「………金蔵さまのお気持ちも、わからぬわけではありません。`[@][lv 0*"14"*"71600038"]`金蔵さまとて、彼女が生まれた時から、亡きベアトリーチェさまの面影を映して慰み者にしようなどと、`[@][lv 0*"14"*"71600039"]`思っていたわけではありません。」`[\\]'
			]
		],
		53492 => [
			'*d53492'.LF.'d [lv 0*"52"*"75200042b"]`「歳を重ねる毎に、抑えられなくなっていく胸の昂りを？」`[\\]' => [
				'ignore' => true,
				'before' => '*d53492'.LF.'d [lv 0*"52"*"75200042"]`「歳を重ねる毎に、抑えられぬ禁じられた感情を？」`[\\]'
			]
		],
		53493 => [
			'*d53493'.LF.'d [lv 0*"14"*"71600040b"]`「……悲劇を、`[|][lv 0*"14"*"71600041b"]`……引き起こしてしまうのではないか。`[@][lv 0*"14"*"71600042"]`……私はそう、思いました」`[\\]' => [
				'ignore' => true,
				'before' => '*d53493'.LF.'d [lv 0*"14"*"71600040"]`「……悲劇が、`[|][lv 0*"14"*"71600041"]`……繰り返されるのではないか。`[@][lv 0*"14"*"71600042"]`……私はそう、思いました」`[\\]'
			]
		],
		53497 => [
			'*d53497'.LF.'d [lv 0*"52"*"75200043"]`「父のように敬愛する人が、あなたを死んだ母の代用品にしようと思ってるなんて！」`[\\]' => [
				'ignore' => true,
				'before' => '*d53497'.LF.'d [lv 0*"52"*"75200043_UP_MODIFIED"]`「父と敬愛する人が、あなたを死んだ母の代用品にしようと思ってるなんて！」`[\\]'
			]
		],
		53498 => [
			'*d53498'.LF.'d2 [lv 0*"19"*"71900086"]`「……一応、金蔵さんの名誉のために申し上げる。`[@][lv 0*"19"*"71900087"]`金蔵さんも苦しんでおられた。`[@][lv 0*"19"*"71900088"]`生き写しになるその娘が、娘と思えなくなると苦しんでおられた。`[@][#][*][lv 0*"19"*"71900089"]`……私は、父として愛しなさいと助言した。`[@][lv 0*"19"*"71900090"]`金蔵さんも、理性と良心に従い、抗われたと思う…」`[\\]' => [
				'ignore' => true,
				'before' => '*d53498'.LF.'d2 [lv 0*"19"*"71900086"]`「……一応、金蔵さんの名誉のために申し上げる。`[@][lv 0*"19"*"71900087"]`金蔵さんも苦しんでおられた。`[@][lv 0*"19"*"71900088_UP_MODIFIED"]`生き写しになる娘が、娘と思えなくなると苦しんでおられた。`[@][#][*][lv 0*"19"*"71900089"]`……私は、父として愛しなさいと助言した。`[@][lv 0*"19"*"71900090"]`金蔵さんも、理性と良心に従い、抗われたと思う…」`[\\]'
			]
		],
		53500 => [
			'*d53500'.LF.'d [lv 0*"55"*"75500072a"]`「あぁ、金蔵の罪はベアトリーチェを喪った時より始まっているのだ。`[@][lv 0*"55"*"75500073a"]`彼女と引き換えのように死んだベアトリーチェ。`[@][lv 0*"55"*"75500074a"]`……その名で、そのまま彼女を呼んだ。`[@][lv 0*"55"*"75500075a"]`金蔵はその瞬間から、彼女をベアトリーチェの生まれ変わりだと決めていたのである…」`[\\]' => [
				'ignore' => true,
				'before' => '*d53500'.LF.'d [lv 0*"55"*"75500072"]`「あぁ、金蔵の罪は彼女が生を受けた時より始まっているのだ。`[@][lv 0*"55"*"75500073"]`彼女を出産し、その引き換えのように死んだベアトリーチェ。`[@][lv 0*"55"*"75500074"]`……その名を、そのまま娘に与えた。`[@][lv 0*"55"*"75500075"]`金蔵は、彼女が生まれたその瞬間から、彼女をベアトリーチェの生まれ変わりだと決めていたのである…」`[\\]'
			]
		],
		53502 => [
			'*d53502'.LF.'d [lv 0*"28"*"72100164"]`「……そういうことよ。`[@][lv 0*"28"*"72100165"]`他のカケラの話をするとおしりを抓られそうだけれど。`[@][lv 0*"28"*"72100166"]`……理御が迎え入れられながら、`[|][lv 0*"28"*"72100167"]`……金蔵が悲劇を犯すカケラ、`[|][lv 0*"28"*"72100168"]`なんてのもあったりして。`[@][lv 0*"28"*"72100169"]`……そのカケラも探してみる？`[@][lv 0*"28"*"72100170"]`　くすくすくすくす………」`[\\]' => [
				'ignore' => true,
				'before' => '*d53502'.LF.'d [lv 0*"28"*"72100164"]`「……そういうことよ。`[@][lv 0*"28"*"72100165"]`他のカケラの話をするとおしりを抓られそうだけれど。`[@][lv 0*"28"*"72100166"]`……理御が迎え入れられながら、`[|][lv 0*"28"*"72100167_UP_MODIFIED"]`……再び金蔵が過ちを犯すカケラ、`[|][lv 0*"28"*"72100168"]`なんてのもあったりして。`[@][lv 0*"28"*"72100169"]`……そのカケラも探してみる？`[@][lv 0*"28"*"72100170"]`　くすくすくすくす………」`[\\]'
			]
		],
		53507 => [
			'*d53507'.LF.'d [lv 0*"28"*"72100173"]`「そうよ。`[@][lv 0*"28"*"72100174"]`かつて惚れた女の娘に手をかけて、ほとぼりも冷めない男よ。`[@][lv 0*"28"*"72100175"]`ベアトリーチェの気持ちを知り同情していた源次は、金蔵のことを容易く信じることが出来なかったの」`[\\]' => [
				'ignore' => true,
				'before' => '*d53507'.LF.'d [lv 0*"28"*"72100173"]`「そうよ。`[@][lv 0*"28"*"72100174_UP_MODIFIED"]`実の娘に手をかけて、ほとぼりも冷めない男よ。`[@][lv 0*"28"*"72100175"]`ベアトリーチェの気持ちを知り同情していた源次は、金蔵のことを容易く信じることが出来なかったの」`[\\]'
			]
		],
		53509 => [
			'*d53509'.LF.'d [lv 0*"28"*"72100176"]`「源次は公私をしっかりと使い分けられる男よ。`[@][lv 0*"28"*"72100177"]`金蔵に忠誠を尽くす一方で、彼女に対して犯した過ちは、容易に許されるべきではないと思っていたわ」`[\\]' => [
				'ignore' => true,
				'before' => '*d53509'.LF.'d [lv 0*"28"*"72100176"]`「源次は公私をしっかりと使い分けられる男よ。`[@][lv 0*"28"*"72100177_UP_MODIFIED"]`金蔵に忠誠を尽くす一方で、娘に対して犯した過ちは、容易に許されるべきではないと思っていたわ」`[\\]'
			]
		],
		67522 => [
			'*d67522'.LF.'d2 [lv 0*"55"*"75501001"][ak][text_speed_t 5]`「どうして…！！　`[#][*][|][lv 0*"55"*"75501002"]`どうしてあなたたちは私を助けたんですか？！`[#][*][|][lv 0*"55"*"75501003"]`　どうして死なせてくれなかったんですか？！　`[#][*][|][lv 0*"55"*"75501004"]`私はあの時の大怪我で、……こんな体で生きさせられている！！　`[#][*][|][lv 0*"55"*"75501005"]`こんな体で、生きていたくなんかなかった！！`[#][*]`」`[\\]' => [
				'ignore' => true,
				'before' => '*d67522'.LF.'d2 [lv 0*"55"*"75501001"][ak][text_speed_t 5]`「どうして…！！　`[#][*][|][lv 0*"55"*"75501002"]`どうしてあなたたちは私を助けたんですか？！`[#][*][|][lv 0*"55"*"75501003"]`　どうして死なせてくれなかったんですか？！　`[#][*][|][lv 0*"55"*"75501004"]`私はあの時の大怪我で、……こんな体で生きさせられている！！　`[#][*][|][lv 0*"55"*"75501005"]`こんな体で、生きていたくなんかなかった！！」`[#][*][\\]'
			]
		],
		66969 => [
			'lbg s0_3,"sub_clock1c"' => [
				'ignore' => true,
				'before' => 'lbg s0_3,"sub_clock1c_r"'
			]
		],
		55198 => [
			'*d55198'.LF.'d [lv 0*"55"*"75500729"]`「織姫と彦星のようにな…！」`[\\]' => [
				'ignore' => true,
				'before' => 'd [lv 0*"55"*"75500729"]`「織姫と彦星のようにな…！」`[gstg 1][\\]'
			]
		],
		67587 => [
			'*d67587'.LF.'d [lv 0*"54"*"75401229"]`「……へっ。`[@][lv 0*"54"*"75401230"]`何がミステリーだ。`[@][lv 0*"54"*"75401231"]`心のねぇミステリーなんざ、俺が認めると思うか。`[@][lv 0*"54"*"75401232"]`……三味線になりてぇ猫から掛かってきな」`[\\]' => [
				'ignore' => true,
				'before' => '*d67587'.LF.'d [lv 0*"54"*"75401229"]`「……へっ。`[@][lv 0*"54"*"75401230"]`何がミステリーだ。`[@][lv 0*"54"*"75401231"]`心のねぇミステリーなんざ、俺が認めると思うか。`[@][lv 0*"54"*"75401232"]`……三味線になりてぇ猫から掛かってきな」`[gstg 2][\\]'
			]
		],
		58942 => [
			'lss s0_10,"en2","a11_defo1" ;1' => [
				'todevnull'	=> true,
				'ignore'	=> true
			]
		],
		58944 => [
			'*d58944'.LF.'d [lv 0*"15"*"80500037"]`「ローマ字がわかれば充分ですよ。`[@][lv 0*"15"*"80500038"]`英語はわからなくて大丈夫です。`[@][lv 0*"15"*"80500039"]`絵羽さまと協力すれば、きっと解けますよ」`[\\]'	=> [
				'ignore'  => true,
				'todevnull'	=> false
			],
		],
		65249 => [
			'mov %chosen_ending,1' => [
				'todevnull'	=> true,
				'ignore'	=> true
			],
			'*uu_uu_0x72c4f3' => [
				'todevnull'	=> false,
				'ignore'	=> true
			]
		],
		65253 => [
			'csp_slot s0_10' => [
				'ignore'	=> true,
				'after'	=> 'csp_slot s0_12'.LF.'csp_slot s0_13'.LF.'csp_slot s0_14'.LF.'csp_slot s0_15'
			]
		],
		65261 => [
			'csp_slot s0_24' => [
				'ignore'	=> true,
				'after'	=> 'csp_slot s0_9'
			]
		],
		65264 => [
			'mov %branch_tmp,%total_coin_count' => [
				'before'	=> 'msgwnd_non',
			]
		],
		65283 => [
			'csp_slot s0_10' => [
				'ignore'	=> true,
				'after'	=> 'csp_slot s0_8'
			]
		],
		65285 => [
			'csp_slot s0_9' => [
				'ignore'	=> true,
				'after'	=> 'csp_slot s0_10'
			]
		],
		65286 => [
			'*d65286'.LF.'d [lv 0*"11"*"82001582"]`「……もうちょい真面目にやってれば良かったわ」`[\\]' => [
				'before' => 'msgwnd_enj'
			]
		],
		65590 => [
			'mov %branch_tmp,%total_coin_count' => [
				'before'	=> 'textoff'.LF.'msgwnd_non',
			]
		],
		59416 => [
			'lss s0_8,"jes","a11_odoroki1" ;1' => [
				'before'	=> 'lss s0_8,"jes","a11_komaru3" ;1',
				'ignore'	=> true
			]
		],
		57476 => [
			'*d57476'.LF.'d2 `そんな調子で、`[@]`上、下、左、右、また左と、`[#][*]`まるであっち向いてホイでもされてるような感じになる。`[\\]' => [
				'before'	=> '*d57476'.LF.'d2 `そんな調子で、`[@]`上、下、左、右、また左と、`[#][*]`まるであっち向いてホイでもされてるような感じになる。`[gstg 1][\\]',
				'ignore'	=> true
			]
		],
		58392 => [
			'*d58392'.LF.'d [lv 0*"06"*"81100020"]`「わしのはな、なぞなぞやで。`[@][lv 0*"06"*"81100021"]`えぇか、よく聞いてや。`[@][lv 0*"06"*"81100022"]`桃太郎の話は知ってるやろ？」`[\\]' => [
				'before'	=> '*d58392'.LF.'d [lv 0*"06"*"81100020"]`「わしのはな、なぞなぞやで。`[@][lv 0*"06"*"81100021"]`えぇか、よく聞いてや。`[@][lv 0*"06"*"81100022"]`桃太郎の話は知ってるやろ？」`[gstg 2][\\]',
				'ignore'	=> true
			]
		],
		58816 => [
			'*d58816'.LF.'d [lv 0*"11"*"82000586"]`「………わかんない」`[\\]' => [
				'before'	=> 'msgwnd_enj'.LF.'*d58816'.LF.'d [lv 0*"11"*"82000586"]`「………わかんない」`[\\]',
				'ignore'	=> true
			],
		],
		// ep8 burning scene 1 save fixes
		64584 => [
			'agpt quakeycycle,167' => [
				'before'	=> 'set_saving off'
			]
		],
		64591 => [
			'csp_slot s0_17'	=>	[
				'before'	=> 'set_saving on'
			]
		],
		// ep8 burning scene 2 save fixes
		64622 => [
			'csp_slot s0_5' => [
				'before'	=> 'set_saving off'
			]
		],
		64624 => [
			'csp_slot s0_17'	=>	[
				'before'	=> 'set_saving on'
			]
		]
	];

	$g['voice_remap'] = [
		(!CHIRU) => [
			'"05"*"410001376"' => [
				'replace'	=> '"05"*"41000136"'
			]
		],
		CHIRU => [
			// Echo effect
			'"34"*"33300121_1"' => [
				'replace'	=> '"34"*"33300121_1_fb"'
			],
			'"34"*"33300121"' => [
				'replace'	=> '"34"*"33300121_fb"'
			],
			'"34"*"33300122"' => [
				'replace'	=> '"34"*"33300122_fb"'
			],
			'"34"*"33300123_1"' => [
				'replace'	=> '"34"*"33300123_1_fb"'
			],
			'"34"*"33300123"' => [
				'replace'	=> '"34"*"33300123_fb"'
			],
			'"34"*"33300124"' => [
				'replace'	=> '"34"*"33300124_fb"'
			],

			'"05"*"51000231"' => [
				'skip' 		=> 1,
				'replace'	=> '"05"*"51000243"'
			],
			'"05"*"51000232"' => [
				'skip' 		=> 1,
				'replace'	=> '"05"*"51000244"'
			],
			'"05"*"51000233"' => [
				'skip' 		=> 1,
				'replace'	=> '"05"*"51000245"'
			],
			'"03"*"50900762"' => [
				'skip' 		=> 1,
				'replace'	=> '"03"*"50900802"'
			],
			'"03"*"50900763"' => [
				'skip' 		=> 1,
				'replace'	=> '"03"*"50900803"'
			],

			'"03"*"50900766"' => [
				'skip'		=> 1,
				'replace'	=> '"03"*"50900805"'
			],
			'"19"*"51900059"' => [
				'skip'		=> 1,
				'replace'	=> '"19"*"51900067"'
			],
			'"19"*"51900060"' => [
				'skip'		=> 1,
				'replace'	=> '"19"*"51900068"'
			],
			'"19"*"51900061"' => [
				'skip'		=> 1,
				'replace'	=> '"19"*"51900069"'
			],
			'"18"*"51800050"' => [
				'skip'		=> 1,
				'replace'	=> '"18"*"51800061"'
			],
			'"18"*"51800047"' => [
				'skip'		=> 1,
				'replace'	=> [ '"18"*"51800051"', '"18"*"51800062"' ]
			],
			'"18"*"51800052"' => [
				'skip'		=> 1,
				'replace'	=> '"18"*"51800063"'
			],
			'"18"*"51800053"' => [
				'skip'		=> 1,
				'replace'	=> '"18"*"51800064"'
			],

			'"46"*"54500569"' => [
				'skip'		=> 1,
				'replace'	=> '"46"*"54500718"'
			],
			'"46"*"54500570"' => [
				'skip'		=> 1,
				'replace'	=> '"46"*"54500719"'
			],

			'"13"*"50400004"' => [
				'skip'		=> 1,
				'replace'	=> '"13"*"50400010"'
			],
			'"13"*"50400001"' => [
				'skip'		=> 1,
				'replace'	=> '"13"*"50400012"'
			],
			'"13"*"50400004"' => [
				'skip'		=> 1,
				'replace'	=> '"13"*"50400018"'
			],
			'"13"*"50400014"' => [
				'skip'		=> 1,
				'replace'	=> '"13"*"50400028"'
			],
			'"13"*"70400154"' => [
				'skip'		=> 1,
				'replace'	=> '"13"*"70400282"'
			],

			'"27"*"50700168"' => [
				'skip'		=> 1,
				'replace'	=> '"27"*"50700167"'
			],
			'"28"*"52100250"' => [
				'skip'		=> 1,
				'replace'	=> '"28"*"52100266"'
			],
			'"28"*"52100200"' => [
				'skip'		=> 1,
				'replace'	=> '"28"*"52100462"'
			],
			'"28"*"52100552"' => [
				'skip'		=> 1,
				'replace'	=> '"28"*"52100555"'
			],
			'"28"*"52100583"' => [
				'skip'		=> 1,
				'replace'	=> '"28"*"52100770"'
			],
			'"29"*"52200095"' => [
				'skip'		=> 1,
				'replace'	=> '"29"*"52200256"'
			],
			'"30"*"53200043"' => [
				'skip'		=> 1,
				'replace'	=> '"30"*"53200058"'
			],
			'"31"*"53100043"' => [
				'skip'		=> 1,
				'replace'	=> '"31"*"53100152"'
			],
			'"46"*"54500256"' => [
				'skip'		=> 1,
				'replace'	=> '"46"*"54500510"'
			],
			'"46"*"54501281"' => [
				'skip'		=> 1,
				'replace'	=> '"46"*"54501310"'
			],
			'"49"*"54700001"' => [
				'skip'		=> 1,
				'replace'	=> '"49"*"54700045"'
			],
			'"49"*"54700006"' => [
				'skip'		=> 1,
				'replace'	=> '"49"*"54700048"'
			],
			'"49"*"54700001"' => [
				'skip'		=> 1,
				'replace'	=> '"49"*"54700051"'
			],
			'"10"*"50100002"' => [
				'skip'		=> 1,
				'replace'	=> '"10"*"50100008"'
			],
			'"10"*"50100004"' => [
				'skip'		=> 1,
				'replace'	=> '"10"*"50100010"'
			],
			'"10"*"50100011"' => [
				'skip'		=> 1,
				'replace'	=> '"10"*"50100067"'
			],
			'"10"*"50100083"' => [
				'skip'		=> 1,
				'replace'	=> '"10"*"50100105"'
			],
			'"10"*"50100022"' => [
				'skip'		=> 1,
				'replace'	=> '"10"*"50100303"'
			],
			'"10"*"50100248"' => [
				'skip'		=> 1,
				'replace'	=> '"10"*"50100386"'
			],
			'"10"*"50100835"' => [
				'skip'		=> 1,
				'replace'	=> '"10"*"50101249"'
			],
			'"10"*"50101373"' => [
				'skip'		=> 1,
				'replace'	=> '"10"*"50101378"'
			],
			'"10"*"60100322"' => [
				'skip'		=> 1,
				'replace'	=> '"10"*"60100332"'
			],
			'"10"*"60100435"' => [
				'skip'		=> 1,
				'replace'	=> '"10"*"60100642"'
			],
			'"10"*"60100510"' => [
				'skip'		=> 1,
				'replace'	=> '"10"*"60100849"'
			],
			'"10"*"80101001"' => [
				'skip'		=> 1,
				'replace'	=> '"10"*"80101125"'
			],
			'"02"*"50800006"' => [
				'skip'		=> 1,
				'replace'	=> '"02"*"50800010"'
			],
			'"03"*"50900217"' => [
				'skip'		=> 1,
				'replace'	=> '"03"*"50900363"'
			],
			'"03"*"50900027"' => [
				'skip'		=> 1,
				'replace'	=> '"03"*"50900716"'
			],
			'"03"*"50900788"' => [
				'skip'		=> 1,
				'replace'	=> '"03"*"50900797"'
			],
			'"03"*"50900199"' => [
				'skip'		=> 1,
				'replace'	=> '"03"*"50900816"'
			],
			'"03"*"50900584"' => [
				'skip'		=> 1,
				'replace'	=> '"03"*"50901055"'
			],
			'"05"*"51000314"' => [
				'skip'		=> 1,
				'replace'	=> '"05"*"51000321"'
			],
			'"17"*"51700090"' => [
				'skip'		=> 1,
				'replace'	=> '"17"*"51700094"'
			],
			'"10"*"60101016"' => [
				'replace'	=> '"10"*"60101016_b"'
			],
			'"07"*"60300049"' => [
				'skip'		=> 1,
				'replace'	=> '"07"*"60300387"'
			],
			'"15"*"60500200"' => [
				'skip'		=> 1,
				'replace'	=> '"15"*"60500217"'
			],
			'"16"*"60600126"' => [
				'skip'		=> 1,
				'replace'	=> '"16"*"60600204"'
			],
			'"60"*"60700584"' => [
				'skip'		=> 1,
				'replace'	=> '"60"*"60700596"'
			],
			'"27"*"60700034"' => [
				'skip'		=> 1,
				'replace'	=> '"27"*"60700660"'
			],
			'"27"*"60701131"' => [
				'skip'		=> 1,
				'replace'	=> '"27"*"60701133"'
			],
			'"28"*"62100174"' => [
				'skip'		=> 1,
				'replace'	=> '"28"*"62100255"'
			],
			'"46"*"64500085"' => [
				'skip'		=> 1,
				'replace'	=> '"46"*"64500295"'
			],
			'"46"*"64501545"' => [
				'skip'		=> 1,
				'replace'	=> ['"46"*"64501550"', '"46"*"64501559"']
			],
			'"27"*"60701164"' => [
				'skip'		=> 1,
				'replace'	=> ['"27"*"60701167"', '"27"*"60701169"', '"27"*"60701164"']
			],
			'"00"*"awase6001"' => [
				'replace'	=> '"00"*"awase6001b"'
			],
			'"00"*"awase6002"' => [
				'replace'	=> '"00"*"awase6002_o"'
			],
			'"00"*"awase6003"' => [
				'replace'	=> '"00"*"awase6003_o"'
			]
		]
	];
}

function getVolume($val) {
	$vol = round(($val/255.0)*100);

	// That's ryuu's fuckup which remained through the conversion.
	if ($vol == 200 || $vol == 1000)
		return 100;

	if ($vol > 100)
		err('ERROR: Volume too high: '.$vol);

	return $vol;
}

function getRealVal($str) {
	global $g;

	if ($str == 'NO') {
		return 0;
	} else if (substr($str,0,1) == 'r') {
		$str = intval(substr($str,3));
		return $g['regs'][$str];
	} else {
		return intval($str);
	}
}

function aluCalculate($str) {
	$cmds = explode(';', $str);
	$stack = [];

	for ($i = 0, $sz = sizeof($cmds); $i < $sz; $i++) {
		$cmd = explode(' ', trim($cmds[$i]));
		if ($cmd[0] == 'end') {
			return $stack[0];
		}  else if ($cmd[0] == 'push') {
			$stack[] = intval($cmd[1]);
		} else {
			$b = array_pop($stack);
			$a = array_pop($stack);

			if ($cmd[0] == 'add') $stack[] = $a + $b;
			else if ($cmd[0] == 'sub') $stack[] = $a - $b;
			else if ($cmd[0] == 'mul') $stack[] = $a * $b;
			else if ($cmd[0] == 'div') $stack[] = $a / $b;
		}
	}

	err('ERROR: Invalid stack');
}

function parse_voice($symbols, &$out, &$i) {
	global $g;

	$q = 1; //counter
	$n = 0; //array_entry
	$v = []; //array
	$v[0] = '"';

	while(1) {
		$c = $symbols[$i+$q];
		$q++;

		if ($c == '/') {
			$v[$n] .= '"*"';
		} else if ($c == '|') {
			$v[$n] .= '"';
			$n++;
			$v[$n] = '"';
		} else if ($c == '.') {
			$v[$n] .= '"';
			break;
		} else {
			$v[$n] .= $c;
		}
	}

	$i += ($q-1);

	for ($q = 0; $q <= $n; $q++) {
		$ch   = ($n-$q);
		$voice = strtolower($v[$q]);

		if (isset($g['voice_remap'][CHIRU][$voice])) {
			if (isset($g['voice_remap'][CHIRU][$voice]['skip']) &&
				$g['voice_remap'][CHIRU][$voice]['skip'] > 0)
				$g['voice_remap'][CHIRU][$voice]['skip']--;
			else
				if (is_array($g['voice_remap'][CHIRU][$voice]['replace']))
					$voice = array_shift($g['voice_remap'][CHIRU][$voice]['replace']);
				else
					$voice = $g['voice_remap'][CHIRU][$voice]['replace'];
		}

		if (!isset($g['missing_voices'][$voice])) {
			$out .= '[lv '.$ch.'*'.$voice.']';
			$g['used_voice'][$voice] = 1;
		}
	}
}

function parse_hint( $symbols, &$out, &$i) {
	$q = 1; //counter
	$kanji = false;

	while(1) {
		$c = $symbols[$i+$q];
		$q++;

		if ($c == '<') {
			$kanji = true;
		} else if ($c == '>') {
			break;
		} else {
			if ($kanji && $c != '!')	$out .= $c;
		}
	}

	$i += $q;
}

function parse_colour( $symbols, &$out, &$i) {
	global $g;

	if ($symbols[$i+1] == '.') {
		$g['text_colour'] = '';
		$i += 1;
	} else {
		$g['text_colour'] = $symbols[$i+1].$symbols[$i+2].$symbols[$i+3];
		$i += 4;
	}

}

function parse_wait( $symbols, &$out, &$i, $skip) {
	$q = 1; //counter
	$frames = '';

	while(1) {
		$c = $symbols[$i+$q];
		$q++;

		if ($c == '.') {
			break;
		} else {
			$frames .= $c;
		}
	}

	if (!$skip) {
		$frames = round((1000/60.0)*$frames);

		$out .= '[!w'.$frames.']';
	}

	$i += ($q-1);
}

function parse_fontsize( $symbols, &$out, &$i, &$customSize) {
	$q = 1; //counter
	$size = '';

	if ($customSize) $out .= '}';

	//70|80|90|100|120|150|170|200
	// in %

	while(1) {
		$c = $symbols[$i+$q];
		$q++;

		if ($c == '.') {
			break;
		} else {
			$size .= $c;
		}
	}

	$size = str_pad((string)$size, 3, '0', STR_PAD_LEFT);

	$out .= '{e:'.$size.':';

	$i += ($q-1);

	$customSize = true;
}

function parse_fade( $symbols, &$out, &$i) {
	$q = 1;

	$out .= '[text_fade_t ';

	$num = '';

	while ($symbols[$i+$q] != '.') {
		$num .= $symbols[$i+$q];
		$q++;
	}

	$out .= round(intval($num)*(1000/60));

	$out .= ']';

	$i += ($q);
}

function dialogue_convertor( &$inst, &$doc, $doc_i, $hidden ) {
	global $g;

	if (!$hidden) {
		$g['dlg_id'] = $g['dlg_new'] + $inst->tagAttrs['num'] + (CHIRU ? 35000 : 0);
	} else {
		$g['dlg_id'] = 'HIDDEN'; //add an error if we try to make a pipe with that?
	}

	//Firstly... Convert names
	$isName = (mb_substr($inst->tagAttrs['data'],0,1) == 'r') ? false : true;
	if ($isName) {
		preg_match('/^(.+?)r/u', $inst->tagAttrs['data'], $name);
		if (isset($name)) $name['1'] = trim($name['1']); // Fixes an extra space after 古戸　ヱリカ
		if (isset($g['names'][$name['1']])) {
			$inst->tagAttrs['data'] = preg_replace('/^(.+?)r/u','',$inst->tagAttrs['data']);
			$inst->tagAttrs['person'] = $g['names'][$name['1']];
		} else if (isset($name)) {
			err('Unknown name '.$name['1']);
		} else {
			$inst->tagAttrs['person'] = 'non';
		}
	} else {
		$inst->tagAttrs['data'] = substr($inst->tagAttrs['data'],1);
		$inst->tagAttrs['person'] = 'non';
	}

	if (/*$g['textbox_mode'] && */$g['current_char'] != $inst->tagAttrs['person'] && !$hidden) {
		$charname = $inst->tagAttrs['person'];
		// Biche name should be Biche not Beatoriche!
		if ($charname == 'bea' && $g['episode_num'] == 7 && ($g['chapter_num'] == 4 || $g['chapter_num'] == 5))
			$charname = 'be4';
		logstr('msgwnd_'.$charname);
		$g['current_char'] = $inst->tagAttrs['person'];
		//Sigh, honestly, ps3 textoffs are far from optimised, but we cannot detect them properly
		//$g['need_textoff'] = true;
	}

	$out	 = '';

	$plain = !isset($inst->tagAttrs['dlgtype']) || $inst->tagAttrs['dlgtype'] == '1';

	if ($g['dlg_id'] != 'HIDDEN') {
		$out .= '*d'.$g['dlg_id'].LF.($plain ? 'd ' : 'd2 ');
	}

	$data = $inst->tagAttrs['data'];
	$data = str_replace('/53200153','v30/53200153',$data);
	$data = str_replace('/73700010','v32/73700010',$data);


	$symbols = strsplit($data);
	$inText	 = false;
	$addSpace = false;
	$notacommand = false;
	$clickWait = true; //Start with true
	$customSize = false;
	$hasPipe = false;

	if ($g['align'] == 2) {
		$out .= '`{a:c:';
		$inText = true;
	}

	$prev_voice = 0;

	for ($i = 0; $i < sizeof($symbols); $i++) {
		$c = $symbols[$i];

		if ($prev_voice > 0)
			$prev_voice--;

		switch($c) {
			case 'a':
				if ($notacommand) { $notacommand = false; $out .= $c; break; }
				if ($inText) { $out .= '`'; $inText = false; }
				parse_fade($symbols, $out, $i);
				break;
			case 'b':
				if ($notacommand) { $notacommand = false; $out .= $c; break; }
				if (!$inText) { $out .= '`'; $inText = true; }
				if ($addSpace) { $out .= '　'; $addSpace = false; }
				parse_hint($symbols, $out, $i);
				break;
			case 'c':
				if ($notacommand) { $notacommand = false; $out .= $c; break; }
				$had_colour = $g['text_colour'] != '';
				parse_colour($symbols, $out, $i);

				if (!$inText) { $out .= '`'; $inText = true; $clickWait = false; }
				if ($addSpace) { $out .= '　'; $addSpace = false; }

				if ($g['text_colour'] == '' && $had_colour) {
					$out .= '}';
				} else if ($g['text_colour'] != '') {
					if ($symbols[$i + 1] == 'c' && $symbols[$i + 2] != '.') {
						$i++;
						parse_colour($symbols, $out, $i);
					}
					if ($g['text_colour'] == 900)		$out .= '{p:1:'; //red truth
					else if ($g['text_colour'] == 279) 	$out .= '{p:2:'; //blue
					else if ($g['text_colour'] == 960)	$out .= '{p:41:'; //gold
					else if ($g['text_colour'] == 649)	$out .= '{p:42:'; //purple
				}

				break;
			case 'e': // automatic click
				if ($notacommand) { $notacommand = false; $out .= $c; break; }
				if ($inText) { $out .= '`'; $inText = false; }
				$out .= '[ak]';
				break;
			case 'k':
				if ($notacommand) { $notacommand = false; $out .= $c; break; }
				if ($inText) { $out .= '`'; $inText = false; }
				$out .= '[@]';
				$clickWait = true;
				break;
			case 'o': // voice volume
				if ($notacommand) { $notacommand = false; $out .= $c; break; }
				if ($inText) { $out .= '`'; $inText = false; }
				$out .= '[vv '.$symbols[$i+1].$symbols[$i+2].']';
				$i += 3;
				break;
			case 'r': // new line
				if ($notacommand) { $notacommand = false; $out .= $c; break; }
				//if ($inText) { $out .= '`'.LF; $inText = false; break; } //simply add a new line in this case
				//$out .= 'br:';
				if (!$inText) { $out .= '`'; $inText = true; }
				if ($addSpace) { $out .= '　'; $addSpace = false; }
				$out .= '{n}';
				break;
			case 's': // text draw speed
				if ($notacommand) { $notacommand = false; $out .= $c; break; }
				//s5. and s0. in chiru, actual speed override according to config
				if ($inText) { $out .= '`'; $inText = false; }
				$out .= '[text_speed_t '.$symbols[$i+1].']';
				$i += 2;
				break;
			case 't': // simultaneous text draw
				if ($notacommand) { $notacommand = false; $out .= $c; break; }
				if (!$inText) { $out .= '`'; $inText = true; }
				if ($addSpace) { $out .= '　'; $addSpace = false; }
				$out .= '{t}';
				break;
			case 'v':
				if ($notacommand) { $notacommand = false; $out .= $c; break; }
				//Does that mean pseudodelay should be inside lv command?
				if ($inText) {
					if (substr($out,-7) != ' `{a:c:') $out .= '`[|]'; // optimisation hack
					else $out .= '`';
					$inText = false;
				}
				else if(!$clickWait) { $out .= '[|]'; }
				parse_voice($symbols, $out, $i);
				$prev_voice = 2;
				break;
			case 'w':
				if ($notacommand) { $notacommand = false; $out .= $c; break; }
				if ($inText) { $out .= '`'; $inText = false; }
				parse_wait($symbols, $out, $i, $prev_voice > 0);
				break;
			case 'y':
			case 'Y':
				if ($notacommand) { $notacommand = false; $out .= $c; break; }
				if ($inText) { $out .= '`'; $inText = false; }

				if ($g['dlg_id'] == 'HIDDEN') err('pipe y in a hidden dialogue');
				if ($plain) err('pipe y in a plain dialogue');

				$out .= '[*]';
				$hasPipe = true;

				break;
			case 'z':
				if ($notacommand) { $notacommand = false; $out .= $c; break; }
				if (!$inText) { $out .= '`'; $inText = true; }
				parse_fontsize($symbols, $out, $i, $customSize);
				break;
			case '�':
			case '&':
				if (!$inText) $addSpace = true;
				else $out .= '　';
				break;
			case ',':
				if ($notacommand) { $notacommand = false; $out .= $c; break; }
				break;
			case '|':
				if ($inText) { $out .= '`'; $inText = false; }

				if ($g['dlg_id'] == 'HIDDEN') err('pipe | in a hidden dialogue');
				if ($plain) err('pipe | in a plain dialogue');

				$out .= '[#]';
				$hasPipe = true;

				break;
			case '{':
				if (!$inText) { $out .= '`'; $inText = true; $clickWait = false; }
				if ($addSpace) { $out .= '　'; $addSpace = false; }
				$out .= '{i:';
				break;
			//case '}':
			//	$out .= '}':
			//	break;
			case '[':
			case ']':
			case '\\':
				// Conversion bugs
				break;
			case '.':
				if ($notacommand) { $notacommand = false; $out .= $c; break; }
				// This is some sync-like control symbol that is normally attached to a prev command.
				break;
			default:
				if (!$inText) { $out .= '`'; $inText = true; $clickWait = false; }
				if ($addSpace) { $out .= '　'; $addSpace = false; }
				if ($notacommand) $notacommand = false;
				if ($c == '!') {
					$notacommand = true;
					break;
				}
				if (isset($g['sym'][$c])) 	$out .= $g['sym'][$c];
				else 						$out .= $c;
				//if ($i+1 == sizeof($symbols)) $out .= '`';
				break;
		}
	}

	if ($customSize) {
		if ($inText) {
			$out .= '}';
		} else {
			$out .= '`}';
			$inText = true;
		}
	}

	if ($g['align'] == 2 && $inText) {
		$out .= '}`[\\]';
	} else if ($g['align'] == 2) {
		$out .= '`}`[\\]';
	} else if ($inText) {
		$out .= '`[\\]';
	} else {
		$out .= '[\\]';
	}

	if (!$plain && !$hasPipe) {
		//if ($doc->ins[$doc_i+1]->tagAttrs['type'] == 'DIALOGUE' ||
		//	$doc->ins[$doc_i+1]->tagAttrs['type'] == 'HIDE_DIALOGUE_WINDOW' ||
		//	$doc->ins[$doc_i+1]->tagAttrs['type'] == 'TEXTBOX_COMMAND') {
			$out = str_replace('d2 ', 'd ', $out);
			echo 'WARN: Attempt to fix non d2 dialogue'.LF;
		//}
	}

	if (!$hidden)	logstr($out);
	else			logstr(';HIDDEN: '.$out);
}

function load_rain_back($load=false) {
	global $g;

	if ($load) { //restore
		if (empty($g['rain_bak'])) return;

		for ($i = 0; $i < sizeof($g['rain_bak']); $i++) {
			$g['current_set'] = $g['rain_bak'][$i]['set'];
			$slot = $g['rain_bak'][$i]['slot'];

			setSprop($slot,'on',true);
			setSprop($slot,'lsp2','');
			setSprop($slot,'rain',true);
			setSprop($slot,'rain_spd',$g['rain_bak'][$i]['rain_spd']); //800
			setSprop($slot,'rain_obj',$g['rain_bak'][$i]['rain_obj']); //500
			setSprop($slot,'rain_wind',$g['rain_bak'][$i]['rain_wind']); //0
			setSprop($slot,'rain_pause',$g['rain_bak'][$i]['rain_pause']); //0
			setSprop($slot,'rain_orbs',$g['rain_bak'][$i]['rain_orbs']); //0
			setSprop($slot,'z_order',$g['rain_bak'][$i]['z_order']); //self

			logstr('rain_load s'.$g['current_set'].'_'.$slot.($g['rain_bak'][$i]['rain_orbs'] ? ',1' : ''));
			if ($g['rain_bak'][$i]['rain_spd'] != 800 || $g['rain_bak'][$i]['rain_obj'] != 500 || $g['rain_bak'][$i]['rain_wind'] != 0)
				logstr('rain_params '.getSalias($slot).','.getSprop($slot,'rain_obj').','.getSprop($slot,'rain_spd').','.getSprop($slot,'rain_wind').'');
			if (getSalias($slot) != getSprop($slot,'z_order'))
				logstr('z_order_override '.getSalias($slot).','.getSprop($slot,'z_order'));
			if ($g['rain_bak'][$i]['rain_pause'])
				logstr('rain_pause '.getSalias($slot));
		}

		$g['rain_bak'] = [];
		$g['current_set'] = '0';
	} else { //backup
		$g['rain_bak'] = [];

		for ($i = 0; $i < 4; $i++) {
			$g['current_set'] = $i;
			for ($i2 = 0; $i2 < 31; $i2++) {
				if (getSprop($i2,'rain') && getSprop($i2,'on')) {
					$g['rain_bak'][] = [
						'set'			=>	(string)$i,
						'slot'			=>	(string)$i2,
						'z_order'		=>	getSprop($i2,'z_order'),
						'rain_spd'		=>	getSprop($i2,'rain_spd'),
						'rain_obj'		=>	getSprop($i2,'rain_obj'),
						'rain_wind'		=>	getSprop($i2,'rain_wind'),
						'rain_pause'	=>	getSprop($i2,'rain_pause'),
						'rain_orbs'		=>	getSprop($i2,'rain_orbs')
					];
					logstr('csp_slot '.getSalias($i2));
					setSprop($i2,'on',false);
				}
			}
		}

		$g['current_set'] = '0';

		if (!empty($g['rain_bak'])) {
			logstr('flush 1');
			$g['committed'] = true;
		}
	}
}

function finish_section($type='') {
	global $g;

	$matches = [];
	preg_match('/^umi([0-9])_([0-9op]{1,2})\.txt$/',$g['curr_chapter'],$matches);
	$add = '';

	if ($g['rain_style'] != 0) {
		$g['rain_style'] = 0;
		logstr('rain_style 0');
	}

	if (($type == 'tea' && $matches[1] != 4 && $matches[1] != 8) || ($type == 'ura' && ($matches[1] == 4 || $matches[1] == 8))) {
		logstr('ch_done flag_scenario_'.$matches[1].'_tea');
		$add = '*teatime_'.$matches[1].'_end' . LF . 'goto *end_game';
		$g['regs'] = $g['def_regs'];
	} else if ($type == 'ura') {
		logstr('ch_done flag_scenario_'.$matches[1].'_ura');
		$add = '*ura_'.$matches[1].'_end' . LF . 'goto *end_game' . LF;
		$g['regs'] = $g['def_regs'];
	} else {
		if ($matches[1] == 5 && $matches[2] == 'op') {
			// Return to Rokkenjima
			logstr('trophy_open 1');
		}
		logstr('ch_done flag_scenario_'.$matches[1].'_'.$matches[2]);
		if (($matches[1] == 1 && $matches[2] == 17) ||
			($matches[1] == 2 && $matches[2] == 18) ||
			($matches[1] == 3 && $matches[2] == 18) ||
			($matches[1] == 4 && $matches[2] == 19) ||
			($matches[1] == 5 && $matches[2] == 15) ||
			($matches[1] == 6 && $matches[2] == 18) ||
			($matches[1] == 7 && $matches[2] == 18) ||
			($matches[1] == 8 && $matches[2] == 16)) {
			$add = '*umi'.$matches[1].'_end' . LF . 'goto *end_game';
			$g['regs'] = $g['def_regs'];
		}
	}

	load_rain_back(false);

	if ($add != '') {
		logstr($add);
	}
}

function section_convertor( &$inst ) {
	global $g;

	//reset_sprites();
	$g['textbox_mode']			= true;
	$g['current_char']			= 'non';
	//$g['need_textoff'] 			= false;

	$sectionType = $inst->tagAttrs['section_type'];

	if ($sectionType == 'EPISODE') {
		if ($g['episode_num'] == EP_DEF+4) {
			$g['episode_num'] = EP_DEF+1;
		} else {
			$g['episode_num']++;
		}
		$g['chapter_num'] = -1;
	} else {
		$sectionName = $inst->tagAttrs['section'];

		if ($sectionName == 'Tea party') {
			finish_section('tea');
			//tea/ura need to become chapters
			$id = 0;
			$matches = [];
			foreach ($g['out'] as $file => $data) {
				if (preg_match('/umi'.$g['episode_num'].'_([0-9]{1,2})\.txt/', $file, $matches))
					if ($matches[1] > $id) $id = $matches[1];
			}
			$g['curr_chapter'] = 'umi'.$g['episode_num'].'_'.($id+1).'.txt';
			$g['out'][$g['curr_chapter']] = '';

			logstr('*teatime_'.$g['episode_num']);
			logstr('log_reset');
			logstr('jskip_s');
			logstr('mov $name_chapter_r_click,r_click_chapters_'.$g['episode_num'].'_tea');
			logstr('mov $name_chapter_save, "'.$g['episode_num'].'_tea"');
			logstr('~');
			logstr('new_tea '.$g['episode_num']);
		} else if ($sectionName == '????') {
			finish_section('ura');
			//tea/ura need to become chapters
			$id = 0;
			$matches = [];
			foreach ($g['out'] as $file => $data) {
				if (preg_match('/umi'.$g['episode_num'].'_([0-9]{1,2})\.txt/', $file, $matches))
					if ($matches[1] > $id) $id = $matches[1];
			}
			$g['curr_chapter'] = 'umi'.$g['episode_num'].'_'.($id+1).'.txt';
			$g['out'][$g['curr_chapter']] = '';

			logstr('*ura_teatime_'.$g['episode_num']);
			logstr('log_reset');
			logstr('jskip_s');
			logstr('mov $name_chapter_r_click,r_click_chapters_'.$g['episode_num'].'_ura');
			logstr('mov $name_chapter_save, "'.$g['episode_num'].'_ura"');
			logstr('~');
			logstr('new_ura '.$g['episode_num']);
		} else {
			finish_section();
			$g['chapter_num']++;
			if ($g['chapter_num'] == 0) $id = 'op';
			else $id = $g['chapter_num'];

			$g['curr_chapter'] = 'umi'.$g['episode_num'].'_'.$id.'.txt';
			$g['out'][$g['curr_chapter']] = '';

			logstr('*umi'.$g['episode_num'].'_'.$id);
			logstr(';'.$sectionName);
			logstr('log_reset');
			logstr('jskip_s');
			logstr('mov $name_chapter_r_click,r_click_chapters_'.$g['episode_num'].'_'.$id);
			logstr('mov $name_chapter_save, "'.$g['episode_num'].'_'.str_pad($id,2,'0',STR_PAD_LEFT).'"');
			logstr('~');

			if ($id != 'op') {
				logstr('new_chapter date_scenario_'.$g['episode_num'].'_'.$id.',scenario_'.$g['episode_num'].'_'.$id);
				load_rain_back(true);
			} else {
				logstr('new_episode '.$g['episode_num']);
			}

			if ($g['episode_num'] == 1 && $id == 2) {
				logstr('trophy_open 1'); //Welcome to Rokkenjima
			}
		}
	}
}

function bgm_convertor(&$inst) {
	global $g;

	$attr = $inst->tagAttrs;

	if ($attr['bgm_file'] == 'BGM_FINAL') {
		// They reused a longer version of BGM_FINAL for chiru
		$id = CHIRU ? 1013 : 1010;
	} else {
		$id = intval(preg_replace('/^umib_([0-9]{3,4})$/', '\1', $attr['bgm_file']));
	}

	// Chiru has fixed stupefaction/suspicion by using correct ids
	// We actually swapped the files for rondo
	if (CHIRU == true) {
		if ($id == 27) $id = 20;
		else if ($id == 20) $id = 27;
	}

	if ($id == 0) {
		err('Failed to get the bgm id');
	}

	$vol = getVolume($attr['volume']);

	$g['volume_level']['bgm'] = $vol;
	$g['volume_enabled']['bgm'] = true;

	logstr('bgmplay '.$id.','.$vol.','.$attr['single_play']);
}

function sfx_convertor(&$inst) {
	global $g;

	$attr = $inst->tagAttrs;

	// SFX_PLAY2 seems to have no channel, redirect it to channel 0 for now
	if (!isset($attr['sfx_channel'])) $attr['sfx_channel'] = 0;

	if ($attr['single_play'] == 1 && $attr['sfx_channel'] < 16) { //SE
		if ($attr['sfx_file'] == 'umilse_022') {
			$id = 29;
		} else if ($attr['sfx_file'] == 'umise_ex01') {
			$id = 1064;
		} else if ($attr['sfx_file'] == 'umise_ex02') {
			$id = 1065;
		} else if ($attr['sfx_file'] == 'umise_ex03') {
			$id = 1066;
		} else {
			$id = intval(preg_replace('/^umise_([0-9]{3,4})$/', '\1', $attr['sfx_file']));
		}
		$vol = getVolume($attr['volume']);
		$ch = $attr['sfx_channel']+1; //ps3 uses 0-15, we use 1-16
		$g['volume_level']['se'.$ch] = $vol;
		$g['volume_enabled']['se'.$ch] = true;
		logstr('seplay '.$ch.','.$id.','.$vol);
	} else {
		if ($attr['sfx_file'] == 'MARIA_V') {
			$id = 1054;
		} else if ($attr['sfx_file'] == 'MARIA_V2') {
			$id = 1055;
		} else if ($attr['sfx_file'] == 'umise_037') {
			$id = 1137;
		} else if ($attr['sfx_file'] == 'umise_055') {
			$id = 1155;
		} else if ($attr['sfx_file'] == 'umise_066') {
			$id = 1166;
		} else {
			$id = intval(preg_replace('/^umilse_([0-9]{3,4})$/', '\1', $attr['sfx_file']));
		}
		$vol = getVolume($attr['volume']);
		$ch = $attr['sfx_channel']-15; //ps3 uses 16+, we use 1+
		$g['volume_level']['me'.$ch] = $vol;
		$g['volume_enabled']['me'.$ch] = true;
		logstr('meplay '.$ch.','.$id.','.$vol);
	}
}

function sanitiseCheck($prop, $frames, $type, ...$path) {
	global $g;

	$time = intval(str_replace(',', '', $frames));

	switch ($type) {
		case 'sprite':
		case 'set':
			$slot = $path[0];

			if ($time > 0) {
				if (!isset($g['propwaits'][$type][$slot]))
					$g['propwaits'][$type][$slot] = [];
				$g['propwaits'][$type][$slot][$prop] = $time;
			} else if (isset($g['propwaits'][$type][$slot][$prop])) {
				logstr(';WARN: Possible animated property override');
				sanitiseWait($prop, $type, $slot);
			}

			break;
		case 'global':

			if ($time > 0) {
				$g['propwaits']['global'][$prop] = $time;
			} else if (isset($g['propwaits']['global'][$prop])) {
				logstr(';WARN: Possible animated property override');
				sanitiseWait($prop, $type);
			}

			break;
	}
}

function sanitiseWait($prop, $type, ...$path) {
	global $g;

	switch ($type) {
		case 'sprite':
			$slot = $path[0];
			$time = $g['propwaits']['sprite'][$slot][$prop] ?? 0;
			break;
		case 'global':
			$time = $g['propwaits']['global'][$prop] ?? 0;
			break;
		case 'set':
			$set = $path[0];
			$time = $g['propwaits']['set'][$set][$prop] ?? 0;
			break;
	}

	foreach ($g['propwaits'] as $type => $data) {
		foreach ($data as $path => $specific) {
			if ($type == 'sprite' || $type == 'set') {
				// $path is set number or slot number
				foreach ($specific as $prop => $value) {
					$g['propwaits'][$type][$path][$prop] -= $time;
					if ($g['propwaits'][$type][$path][$prop] <= 0)
						unset($g['propwaits'][$type][$path][$prop]);
				}
			} else {
				// $path is prop name
				$g['propwaits'][$type][$path] -= $time;
				if ($g['propwaits'][$type][$path] <= 0)
					unset($g['propwaits'][$type][$path]);
			}
		}
	}
}

function sanitiseProperties() {
	global $g;

	do {
		$again = false;
		foreach ($g['propwaits']['sprite'] as $slot => $propMap) {
			foreach ($propMap as $prop => $time) {
				sanitiseWait($prop, 'sprite', $slot);
				logstr('sptwait2 '.$prop.','.$slot.' ;missed '.$time);
				$again = true;
				break;
			}
			if ($again) break;
		}
	} while ($again);

	do {
		$again = false;
		foreach ($g['propwaits']['set'] as $set => $propMap) {
			foreach ($propMap as $prop => $time) {
				sanitiseWait($prop, 'set', $set);
				logstr('spritesetptwait '.$prop.','.$set.' ;missed '.$time);
				$again = true;
				break;
			}
			if ($again) break;
		}
	} while ($again);

	do {
		$again = false;
		foreach ($g['propwaits']['global'] as $prop => $time) {
			sanitiseWait($prop, 'global');
			logstr('gptwait '.$prop.' ;missed '.$time);
			$again = true;
			break;
		}
	} while ($again);
}

function sprite_load_convertor(&$inst) {
	global $g;

	$key = strtolower(substr($inst->tagAttrs['file'],0,3));
	$emo = substr($inst->tagAttrs['file'],4);

	$slot = $inst->tagAttrs['slot'];

	foreach ($g['emot'] as $jp => $lt) {
		$emo = str_replace($jp,$lt,$emo);
	}

	$emo = strtolower($emo);

	//Looks like they are far from fixed in psp too :(
	if (($emo == 'b11_hohoemicool' && $key == 'rg4') ||
		($emo == 'b11_1kaoshoumen' && $key == 'rg5') ||
		($emo == 'b11_warai2' && $key == 'mar')) {
		$emo = 'b11_akuwarai1';
	}

	if ($emo == 'in_nakua1') { // t/1KIN_NAKUA1
		$key = 'kin';
		$emo = 'a11_naku2';
	}

	if ($emo == 'id_majimea2g') { // t/1HID_MAJIMEA2G
		$key = 'hid';
		$emo = 'a12_majime1';
	}

	if ($key == 'dla') {
		if ($emo == 'b21_akuwarai1r') $emo = 'b21_akuwarai1';
		if ($emo == 'b21b_akuwarai1r') $emo = 'b21b_akuwarai1';
		if ($emo == 'b22_akuwarai1r') $emo = 'b22_akuwarai1';
		if ($emo == 'b22b_akuwarai1r') $emo = 'b22b_akuwarai1';
		if ($emo == 'b23b_akuwarai1r') $emo = 'b23b_akuwarai1';
		if ($emo == 'bm21_akuwarai1r') $emo = 'bm21_akuwarai1';
		if ($emo == 'bm21b_akuwarai1r') $emo = 'bm21b_akuwarai1';
	}

	if ($key == 'geo' && $emo == 'b12_warai2k') $emo = 'b12k_warai2k';
	if ($key == 'rud' && $emo == 'a21_majime1') $emo = 'a21_majime2';

	if (!isset($g['used_sps'][$key])) $g['used_sps'][$key] = [];
	$g['used_sps'][$key][$emo] = 1;

	logstr('lss s'.$g['current_set'].'_'.$inst->tagAttrs['slot'].',"'.$key.'","'.$emo.'" ;'.$inst->tagAttrs['unk_1']);
	if (getSprop($inst->tagAttrs['slot'],'on') && $inst->tagAttrs['unk_1'] == 3) {
		printModifiedSprop($inst->tagAttrs['slot']);
	} else {
		if ($inst->tagAttrs['unk_1'] == 3)
			logstr(';Failed to apply removed sprite properties');
		setSprop($inst->tagAttrs['slot'],'on',true);
	}

	setSprop($inst->tagAttrs['slot'],'char_id',$key);
	setSprop($inst->tagAttrs['slot'],'lips_anim',true);
}

function reset_sprites() {
	global $g;

	$g['sprites'] = [];

	for ($i = 0; $i < 4; $i++) {
		$g['sprites'][$i] = [];
		$g['current_set'] = $i;
		for ($i2 = 0; $i2 < 31; $i2++) {
			$g['sprites'][$i][$i2] = [];
			setSprop($i2,'on',false);
		}
	}

	$g['current_set'] = '0';
	$g['spriteset_state']		= [ true, false, false, false ];
}

function getSprop($slot, $prop, $set = -1) {
	global $g;

	if ($set < 0) {
		$set = $g['current_set'];
	}

	if (!isset($g['sprites'][$set][$slot][$prop])) {
		return -1; //-1 for breakup and warp_amp
	}

	return $g['sprites'][$set][$slot][$prop];
}

function getSalias($slot, $set = -1) {
	global $g;

	if ($set < 0) {
		$set = $g['current_set'];
	}

	if ($slot == 0) $slot = '0';

	return 's'.$set.'_'.$slot;
}

function setSprop($slot, $prop, $val) {
	global $g;

	if ($prop == 'on') {
		$g['sprites'][$g['current_set']][$slot][$prop] = $val;
		$g['sprites'][$g['current_set']][$slot]['z_order'] 	= getSalias($slot);
		$g['sprites'][$g['current_set']][$slot]['lips_anim']= true;
		$g['sprites'][$g['current_set']][$slot]['char_id']	= 'non';
		$g['sprites'][$g['current_set']][$slot]['lsp2']		= '2';
		$g['sprites'][$g['current_set']][$slot]['video']	= false;

		foreach ($g['sprite_def'] as $prop => $value)
			if ($prop != 'z_order')
				$g['sprites'][$g['current_set']][$slot][$prop] = $value;
	} else {
		$g['sprites'][$g['current_set']][$slot][$prop] = $val;
	}
}

function printModifiedSprop($slot) {
	global $g;

	foreach ($g['sprite_def'] as $prop => $value) {
		$val = $g['sprites'][$g['current_set']][$slot][$prop];
		$str = '';

		if (($prop == 'z_order' && $val != getSalias($slot)) || ($prop != 'z_order' && $val != $value)) {
			if ($prop == 'monocro') {
				$str = 'color_mod'.getSprop($slot,'lsp2').' ';
				if ($val == 1) $str .= 'sepia,'.getSalias($slot);
				else if ($val == 2) $str .= 'nega1,'.getSalias($slot);
				else if ($val == 4) $str .= getSalias($slot).',#ffffff';
				else err('Restore: Unknown color_mod '.$val);
			} else if ($prop == 'add') {
				if ($val == '0') $str = 'blend_mode'.getSprop($slot,'lsp2').' nor,'.getSalias($slot);
				else			 $str = 'blend_mode'.getSprop($slot,'lsp2').' add,'.getSalias($slot);
			} else if ($prop == 'centrex') {
				$str = 'set_scale_center '.getSalias($slot).','.getSprop($slot,'centrex').','.getSprop($slot,'centrey');
			} else if ($prop == 'centrey') {
				continue;
			} else if ($prop == 'z_order') {
				if (getSprop($slot,'z_order') != 's0_99')
					$str = 'z_order_override'.getSprop($slot,'lsp2').' '.getSalias($slot).','.getSprop($slot,'z_order');
			} else if ($prop == 'rain') {
				err('Restore: Invalid rain');
			} else if ($prop == 'rain_spd') {
				err('Restore: Invalid rain');
			} else if ($prop == 'rain_wind') {
				err('Restore: Invalid rain');
			} else if ($prop == 'rain_pause') {
				err('Restore: Invalid rain');
			} else {
				$str = 'aspt'.getSprop($slot,'lsp2').' '. $prop.','.getSalias($slot).','.$val;
			}
			if ($str != '') logstr($str);
		}
	}

	// 'lips_anim'
	// 'char_id'
	// 'video'
}

function convProp1(&$inst) {
	global $g;

	$slot = $inst->tagAttrs['slot'];

	$prop = '';
	switch ($inst->tagAttrs['property']) {
		case 'MULTIPLIER_ALPHA': $prop = 'alpha'; break;
		case 'MULTIPLIER_RED': $prop = 'darken_r'; break;
		case 'MULTIPLIER_GREEN': $prop = 'darken_g'; break;
		case 'MULTIPLIER_BLUE': $prop = 'darken_b'; break;
		case 'X_POSITION': $prop = 'xpos'; break;
		case 'Y_POSITION': $prop = 'ypos'; break;
		case 'RESIZE_FACTOR_X': $prop = 'scalex'; break;
		case 'RESIZE_FACTOR_Y': $prop = 'scaley'; break;
		case 'ROTATION_ANGLE': $prop = 'rot'; break;
		case 'BLUR_AMOUNT': $prop = 'blur'; break;
		case 'QUAKE_SPRITE_X_AMPLITUDE': $prop = 'quakexamp'; break;
		case 'QUAKE_SPRITE_X_CYCLEFRAMES': $prop = 'quakexcycle'; break;
		case 'QUAKE_SPRITE_Y_AMPLITUDE': $prop = 'quakeyamp'; break;
		case 'QUAKE_SPRITE_Y_CYCLEFRAMES': $prop = 'quakeycycle'; break;
		case 'BREAKUP_VALUE': $prop = 'breakup'; break;
		case 'BREAKUP_DIRECTION': $prop = 'breakupdir'; break;
		case 'WARP_AMP': $prop  = 'warp_amp'; break;
		case 'WARP_SPEED': $prop  = 'warp_spd'; break;
		case 'FLIP_MODE': $prop = 'flip'; break;
		default: err('Unknown property '.$inst->tagAttrs['property']);
	}

	if (isset($inst->tagAttrs['value'])) $val = getRealVal($inst->tagAttrs['value']);
	else $val = '0';

	if ($prop == 'scalex' || $prop == 'scaley') $val = round($val/10);
	if ($prop == 'rot') $val = -round($val*360/1000);

	if (isset($inst->tagAttrs['frames']) && $inst->tagAttrs['frames'] != '0') {
		$frames = ','.round(getRealVal($inst->tagAttrs['frames'])*(1000/60.0));
	}
	else
		$frames = '';

	if ($prop == 'quakexcycle' || $prop == 'quakeycycle')  {
		$val = round($val*(1000/60.0));
		$frames = ''; //probably not needed
	}

	if ($frames != '' && isset($inst->tagAttrs['equation'])) {
		if ($inst->tagAttrs['equation'] == 'GRADUAL_SLOWDOWN') $eq = ',1';
		else if ($inst->tagAttrs['equation'] == 'GRADUAL_SPEEDUP') $eq = ',2';
		else if ($inst->tagAttrs['equation'] == 'SMOOTH_START_STOP') $eq = ',3';
		else {
			$eq = '';
			echo 'Unsupported equation '.$inst->tagAttrs['equation'].LF;
		}
	} else {
		$eq = '';
	}

	if ($val != getSprop($slot,$prop) && getSprop($slot,'on')) {
		sanitiseCheck($prop, $frames, 'sprite', getSalias($slot));
		$pref = '';
		if (getSprop($slot,'lsp2') == '' && ($prop == 'scalex' || $prop == 'scaley')) {
			logstr(';WARN: skipping unsupported effect');
			$pref = ';';
		}
		logstr($pref.'aspt'.getSprop($slot,'lsp2').' '.$prop.','.getSalias($slot).','.$val.$frames.$eq);
		if ($prop != 'breakup' && $prop != 'breakupdir' && $prop != 'warp_amp' && $prop != 'warp_spd')
			setSprop($slot,$prop,$val);
	}

}

function convProp2(&$inst) {
	global $g;

	$prop = '';
	switch ($inst->tagAttrs['property']) {
		case 'QUAKE_X_AMPLITUDE': $prop = 'quakexamp'; break;
		case 'QUAKE_Y_AMPLITUDE': $prop = 'quakeyamp'; break;
		case 'QUAKE_X_CYCLEFRAMES': $prop = 'quakexcycle'; break;
		case 'QUAKE_Y_CYCLEFRAMES': $prop = 'quakeycycle'; break;
		case 'DISPLAY_ONION_ALPHA_OPACITY': $prop = 'onionalpha'; break;
		case 'DISPLAY_ONION_ALPHA_SCALE_FACTOR': $prop = 'onionscale'; break;
		case 'BLUR_AMOUNT_DISPLAY_PROPERTY': $prop = 'blur'; break;
		case 'DISPLAY_X_POSITION': $prop = 'xpos'; break;
		case 'DISPLAY_Y_POSITION': $prop = 'ypos'; break;
		case 'DISPLAY_CENTER_OFFSET_X': $prop = 'centrex'; break;
		case 'DISPLAY_CENTER_OFFSET_Y': $prop = 'centrey'; break;
		default: err('Unknown property '.$inst->tagAttrs['property']);
	}

	if (isset($inst->tagAttrs['value'])) $val = getRealVal($inst->tagAttrs['value']);
	else if ($prop == 'onionscale') $val = '1000';
	else $val = '0';

	if ($prop == 'quakexcycle' || $prop == 'quakeycycle') $val = round($val*(1000/60.0));

	if (isset($inst->tagAttrs['frames']) && $inst->tagAttrs['frames'] != '0')
		$frames = ','.round(getRealVal($inst->tagAttrs['frames'])*(1000/60.0));
	else
		$frames = '';

	if ($frames != '' && isset($inst->tagAttrs['equation'])) {
		if ($inst->tagAttrs['equation'] == 'GRADUAL_SLOWDOWN') $eq = ',1';
		else if ($inst->tagAttrs['equation'] == 'GRADUAL_SPEEDUP') $eq = ',2';
		else if ($inst->tagAttrs['equation'] == 'SMOOTH_START_STOP') $eq = ',3';
		else {
			$eq = '';
			echo 'Unsupported equation '.$inst->tagAttrs['equation'].LF;
		}
	} else {
		$eq = '';
	}

	if ($prop != 'quakexcycle' && $prop != 'quakeycycle') {
		sanitiseCheck($prop, $frames, 'global');
		logstr('agpt '.$prop.','.$val.$frames.$eq);
	} else {
		logstr('agpt '.$prop.','.$val); //ignore unknown frame(?) values
	}
}

function property_convertor(&$inst) {
	global $g;

	$slot = $inst->tagAttrs['slot'];

	// don't apply properties on empty sprites
	if (!getSprop($slot,'on'))  {
		if (getSalias($slot) == 's0_1' && $inst->tagAttrs['property'] == 'Z_ORDER') {
			echo 'WARN: Failed to z_override s0_1 sprite'.LF;
			if (isset($inst->tagAttrs['value'])) $val = getSalias(getRealVal($inst->tagAttrs['value']));
			else $val = getSalias('0');
			logstr(';z_order_override'.getSprop($slot,'lsp2').' '.getSalias($slot).','.$val);
		}
		return;
	}

	switch ($inst->tagAttrs['property']) {
		case 'Z_ORDER':
			if (isset($inst->tagAttrs['value'])) $val = getSalias(getRealVal($inst->tagAttrs['value']));
			else $val = getSalias('0');

			if ($val != getSprop($slot,'z_order')) {
				//WARN: hack
				logstr(($val == 's0_99' ? ';' : '').'z_order_override'.getSprop($slot,'lsp2').' '.getSalias($slot).','.$val);
				setSprop($slot,'z_order',$val);
			}
			break;

		case 'MONOCRO':
			if (isset($inst->tagAttrs['value'])) $val = getRealVal($inst->tagAttrs['value']);
			else $val = '0';

			if ($val != getSprop($slot,'monocro')) {
				$str = 'color_mod'.getSprop($slot,'lsp2').' ';
				if ($val == 0) $str .= 'off,'.getSalias($slot);
				else if ($val == 1) $str .= 'sepia,'.getSalias($slot);
				else if ($val == 2) $str .= 'nega1,'.getSalias($slot);
				else if ($val == 4) $str .= getSalias($slot).',#ffffff';
				else err('Unknown color_mod '.$val);
				setSprop($slot,'monocro',$val);
				logstr($str);
			}
			break;

		case 'ADD_BLEND':
			if (isset($inst->tagAttrs['value'])) $val = getRealVal($inst->tagAttrs['value']);
			else $val = '0';

			//Rain cannot have other blending modes
			if ($val != getSprop($slot,'add') && getSprop($slot,'rain') == false) {
				if ($val == '0')	logstr('blend_mode'.getSprop($slot,'lsp2').' nor,'.getSalias($slot));
				else				logstr('blend_mode'.getSprop($slot,'lsp2').' add,'.getSalias($slot));
				setSprop($slot,'add',$val);
			}

			break;

		case 'PAUSE_LAYER':
			if (isset($inst->tagAttrs['value'])) $val = getRealVal($inst->tagAttrs['value']);
			else $val = false;

			if ($val != getSprop($slot,'rain_pause')) {
				setSprop($slot,'rain_pause',$val);
				if ($val == false) logstr('rain_resume '.getSalias($slot));
				else logstr('rain_pause '.getSalias($slot));
			}

			break;

		case 'LIPS_ANIM':
			if (isset($inst->tagAttrs['value'])) $val = true;
			else $val = false;

			if (getSprop($slot,'lips_anim') != $val && !$val && getSprop($slot,'char_id') != 'non') {
				setSprop($slot,'lips_anim',$val);
				logstr('lips_sprite '.getSalias($slot).'_b');
			} else if (getSprop($slot,'lips_anim') != $val && getSprop($slot,'char_id') != 'non') {
				setSprop($slot,'lips_anim',$val);
				logstr('lips_sprite '.getSalias($slot).'_b'.',"'.getSprop($slot,'char_id').'"');
			}

			break;
		default:
			convProp1($inst);
			break;
	}
}

function wait_property_convertor(&$inst) {
	global $g;

	$prop = '';
	switch ($inst->tagAttrs['property']) {
		case 'ALPHA': $prop = 'alpha'; break;
		case 'POSITION_X': $prop = 'xpos'; break;
		case 'POSITION_CY': //this looks like a ps3 bug, making it pure y
		case 'POSITION_Y': $prop = 'ypos'; break;
		case 'SCALE_X': $prop = 'scalex'; break;
		case 'SCALE_Y': $prop = 'scaley'; break;
		case 'ROTATION': $prop = 'rot'; break;
		case 'BLUR': $prop = 'blur'; break;
		case 'BREAKUP': $prop = 'breakup'; break;
		case 'QUAKE_X': $prop = 'quakexamp'; break;
		case 'QUAKE_Y': $prop = 'quakeyamp'; break;
		case 'WARP': 	$prop = 'warp_amp'; break;
		case 'ANIM': break;
		default: err('Unknown property '.$inst->tagAttrs['property']);
	}

	if ($prop != '') {
		//Okay, Japs are completely drunk and sometimes wait on something miraculous
		if (getSprop($inst->tagAttrs['slot'],'on')) {
			$slot = $inst->tagAttrs['slot'];
			sanitiseWait($prop, 'sprite', getSalias($slot));
			logstr('sptwait'.getSprop($slot,'lsp2').' '.$prop.','.getSalias($slot));
		}
	} else if ($inst->tagAttrs['property'] == 'ANIM') {
		logstr('waitvideo');
	}

}

function global_property_convertor(&$inst) {
	global $g;

	$type = $inst->tagAttrs['property'];

	switch ($type) {
		case 'QUAKE_SINGLE_AXIS': break; // hopefully not needed
		default:
			convProp2($inst);
			break;
	}
}

function wait_global_property_convertor(&$inst) {
	global $g;

	$type = $inst->tagAttrs['property'];

	switch ($type) {
		case 'GLOBAL_QUAKE_X':
			sanitiseWait('quakexamp', 'global');
			logstr('gptwait quakexamp');
			break;
		case 'GLOBAL_QUAKE_Y':
			sanitiseWait('quakeyamp', 'global');
			logstr('gptwait quakeyamp');
			break;
		case 'GLOBAL_QUAKE_XY':
			sanitiseWait('quakexamp', 'global');
			sanitiseWait('quakeyamp', 'global');
			logstr('gptwait quakexamp');
			logstr('gptwait quakeyamp');
			break;
		case 'GLOBAL_ONION_ALPHA':
			sanitiseWait('onionalpha', 'global');
			logstr('gptwait onionalpha');
			break;
		case 'GLOBAL_BLUR_AMOUNT':
			sanitiseWait('blur', 'global');
			logstr('gptwait blur');
			break;
		case 'GLOBAL_POSITION_X':
			sanitiseWait('xpos', 'global');
			logstr('gptwait xpos');
			break;
		case 'GLOBAL_POSITION_Y':
			sanitiseWait('ypos', 'global');
			logstr('gptwait ypos');
			break;
		default:
			err('Unknown property '.$inst->tagAttrs['property']);
	}
}

function spriteset_property_convertor(&$inst) {
	global $g;

	$prop = '';
	switch ($inst->tagAttrs['property']) {
		case 'SPRITESET_ALPHA': $prop = 'alpha'; break;
		case 'SPRITESET_X_POSITION': $prop = 'xpos'; break;
		case 'SPRITESET_Y_POSITION': $prop = 'ypos'; break;
		case 'SPRITESET_BLUR_AMOUNT': $prop = 'blur'; break;
		case 'SPRITESET_BREAKUP_VALUE': $prop = 'breakup'; break;
		case 'SPRITESET_BREAKUP_DIRECTION': $prop = 'breakupdir'; break;
		case 'SPRITESET_PIXELATED_NOISE': $prop = 'pixelate'; break;
		case 'SPRITESET_WARP_AMP': $prop  = 'warp_amp'; break;
		case 'SPRITESET_WARP_SPEED': $prop  = 'warp_spd'; break;
		case 'SPRITESET_CENTER_OFFSET_X': $prop = 'centrex'; break;
		case 'SPRITESET_CENTER_OFFSET_Y': $prop = 'centrey'; break;
		case 'SPRITESET_RESIZE_FACTOR_X': $prop = 'scalex'; break;
		case 'SPRITESET_RESIZE_FACTOR_Y': $prop = 'scaley'; break;
		case 'SPRITESET_ROTATION_ANGLE': $prop = 'rot'; break;
		case 'SPRITESET_FLIP_MODE': $prop = 'flip'; break;
		default: err('Unknown property '.$inst->tagAttrs['property']);
	}

	if (isset($inst->tagAttrs['value'])) $val = getRealVal($inst->tagAttrs['value']);
	else $val = '0';

	if ($prop == 'scalex' || $prop == 'scaley') $val = round($val/10);
	if ($prop == 'rot') $val = -round($val*360/1000);

	if (isset($inst->tagAttrs['frames']) && $inst->tagAttrs['frames'] != '0')
		$frames = ','.round(getRealVal($inst->tagAttrs['frames'])*(1000/60.0));
	else
		$frames = '';

	if ($frames != '' && isset($inst->tagAttrs['equation'])) {
		if ($inst->tagAttrs['equation'] == 'GRADUAL_SLOWDOWN') $eq = ',1';
		else if ($inst->tagAttrs['equation'] == 'GRADUAL_SPEEDUP') $eq = ',2';
		else if ($inst->tagAttrs['equation'] == 'SMOOTH_START_STOP') $eq = ',3';
		else {
			$eq = '';
			echo 'Unsupported equation '.$inst->tagAttrs['equation'].LF;
		}
	} else {
		$eq = '';
	}

	if ($prop != 'breakupdir') {
		// Santise the call
		sanitiseCheck($prop, $frames, 'set', $g['current_set']);

		logstr('aspritesetpt '.$prop.','.$g['current_set'].','.$val.$frames.$eq);
	} else {
		logstr('aspritesetpt '.$prop.','.$g['current_set'].','.$val);
	}
}

function wait_spriteset_property_convertor(&$inst) {
	global $g;

	$type = $inst->tagAttrs['property'];

	switch ($type) {
		case 'SPRITESET_WAIT_ALPHA':
			sanitiseWait('alpha', 'set', $g['current_set']);
			logstr('spritesetptwait alpha,'.$g['current_set']);
			break;
		case 'SPRITESET_WAIT_X_POSITION':
			sanitiseWait('xpos', 'set', $g['current_set']);
			logstr('spritesetptwait xpos,'.$g['current_set']);
			break;
		case 'SPRITESET_WAIT_Y_POSITION':
			sanitiseWait('ypos', 'set', $g['current_set']);
			logstr('spritesetptwait ypos,'.$g['current_set']);
			break;
		case 'SPRITESET_WAIT_BREAKUP':
			sanitiseWait('breakup', 'set', $g['current_set']);
			logstr('spritesetptwait breakup,'.$g['current_set']);
			break;
		case 'SPRITESET_WAIT_PIXELATED_NOISE':
			sanitiseWait('pixelate', 'set', $g['current_set']);
			logstr('spritesetptwait pixelate,'.$g['current_set']);
			break;
		case 'SPRITESET_WAIT_WARP':
			sanitiseWait('warp_amp', 'set', $g['current_set']);
			logstr('spritesetptwait warp_amp,'.$g['current_set']);
			break;
		case 'SPRITESET_WAIT_ROTATION_ANGLE':
			sanitiseWait('rot', 'set', $g['current_set']);
			logstr('spritesetptwait rot,'.$g['current_set']);
			break;
		default:
			err('Unknown property '.$inst->tagAttrs['property']);
	}
}

function parse_effect($effect, $dur, $ons=false) {
	global $g;

	$g['committed'] = true;

	//almost proper effect and duration
	if ($ons) {
		//some are crossfade, some simple flushes (simplify them when possible)
		if (($effect == -1 || $effect == 0 || $effect == 1) || !isset($g['eff'][$effect])) {
			if ($dur == 0)	logstr('flush 1'); //zero is called instant
			else 			logstr('flush 10,'.$dur);
		} else if ($dur == 0) {
			logstr('flush '.$effect);
		} else {
			$effect = str_replace(':DELAY:',$dur,$g['eff'][$effect]);
			logstr('flush '.$effect);
		}
	} else if (isset($effect['glass'])) {
		logstr('show_glass '.($effect['speed'] == 'NO' ? '0' : $effect['speed']));
	} else {

		if ($effect['block'] == 1) {
			$cmd = 'flush';
		} else {
			$cmd = 'flush2';
		}

		if (getRealVal($effect['duration']) == 0 || getRealVal($effect['duration']) == 1) {
			logstr('flush 1'.($cmd == 'flush2'?(' ;flush2: '.$effect['duration'].' frames'):'')); //optimise
		} else if ($effect['ons_ef'] != 'NO') { //no mask possible
			$eff = '';
			$dur = round($effect['duration']*(1000/60.0));
			switch($effect['ons_ef']) {
				case 22:
					$eff = str_replace(':DELAY:',$dur,$g['eff'][22]);
				break;
				case 15:
				case 23:
					$eff = str_replace(':DELAY:',$dur,$g['eff'][23]);
				break;
				case 20:
				case 24:
					$eff = str_replace(':DELAY:',$dur,$g['eff'][24]);
				break;
				case 1:
				case 10:
				case 30:
					$eff = str_replace(':DELAY:',$dur,$g['eff'][80]);
				break;
				default:
					err('unknown effect '.$effect['ons_ef']);
				break;
			}
			logstr($cmd.' '.$eff);
		} else if ($effect['mask'] != 'NO') {
			$mask = '":c;graphics\\system\\fade\\'.strtolower($g['msk'][getRealVal($effect['mask'])].'.png"');
			$dur = round($effect['duration']*(1000/60.0));
			logstr($cmd.' 18,'.$dur.','.$mask);
		} else {
			$dur = round($effect['duration']*(1000/60.0));
			logstr($cmd.' 10,'.$dur);
		}
	}

}

function removeSet($disable) {
	global $g;

	if (!$g['spriteset_state'][$g['current_set']])
		//err('What to remove a disabled spriteset for?');
		return;

	$clear = false;
	$killvid = false;
	for ($k = 0; $k < 31; $k++) {
		if (getSprop($k,'on')) {
			$clear = true;
			if (getSprop($k,'video')) {
				$killvid = true;
				setSprop($k,'video',false);
			}
			setSprop($k,'on',false);
		}
	}
	if ($clear && !$disable) {
		logstr('reset_set '.$g['current_set']);
	} else if ($disable) {
		$num = -($g['current_set']+10);
		logstr('csp '.$num);
	}
	for ($mask_id = 1, $mask_num = sizeof($g['msk_st']); $mask_id <= $mask_num; $mask_id++) {
		if ($g['msk_st']['msk'.$mask_id] == $g['current_set']) {
			$g['msk_st']['msk'.$mask_id] = -1;
			logstr('csp msk'.$mask_id);
			break;
		}
	}

	if ($disable) {
		if ($g['current_set'] == '0')
			//err('Cannot clear spriteset 0');
			return;

		$g['spriteset_state'][$g['current_set']] = false;
	}
}

function main() {
	global $g;

	initGlobals();

	ini_set('memory_limit','2048M');

	echo 'Welcome to Alchemist Script Parser 2.0'.LF.'This will require a lot of RAM ^________^'.LF;

	$start = microtime(true);

	if (!file_exists($g['input'])) {
		echo 'Check your files, nothing to parse!'.LF;
		die();
	}

	$scr = new XMLParser();
	$scr->Parse($g['input']);

	echo 'XML parsed: '.round(microtime(true)-$start, 2).' seconds'.LF;

	$i = START;

	$start = microtime(true);

	while (isset($scr->document->ins[$i])) {
		$type  = $scr->document->ins[$i]->tagAttrs['type'];
		$iaddr = $scr->document->ins[$i]->tagAttrs['iaddr'];

		if (isset($g['labeladdrs'][$iaddr])) {
			logstr('*uu_uu_' . $iaddr);
			$g['labeladdrs'][$iaddr] = true;
		}

		switch($type) {

			case 'BGM_PLAY':
				bgm_convertor($scr->document->ins[$i]);
				break;

			case 'SFX_PLAY':
			case 'SFX_PLAY2':
				sfx_convertor($scr->document->ins[$i]);
				break;

			case 'DIALOGUE':
				if (!$g['committed']) {
					logstr(';flush 1');
					$g['committed'] = true;
				}
				sanitiseProperties();
				dialogue_convertor($scr->document->ins[$i],$scr->document,$i,false);
				break;

			case 'HIDDEN_DIALOGUE':
				sanitiseProperties();
				dialogue_convertor($scr->document->ins[$i],$scr->document,$i,true);
				break;

			case 'DIALOGUE_PIPE_WAIT':
				$idx = $scr->document->ins[$i]->tagAttrs['idx'];
				logstr('wait_on_d '.$idx);
				break;

			case 'DIALOGUE_CONTINUE_SIGNAL':
				logstr('d_continue');
				break;

			case 'SECTION_START':
				section_convertor($scr->document->ins[$i]);
				break;

			case 'SPRITE_LOAD':
				$g['committed'] = false;
				sprite_load_convertor($scr->document->ins[$i]);
				break;

			case 'PIC_LOAD':
			case 'LOAD_SIMPLE':
				if (($scr->document->ins[$i]->tagAttrs['file'] == 'BLACK' || $scr->document->ins[$i]->tagAttrs['file'] == 'WHITE' /*|| $scr->document->ins[$i]->tagAttrs['file'] == 'RED'*/) &&
					$g['current_set'] == '0' && $scr->document->ins[$i]->tagAttrs['slot'] == '1') {
					if (getSprop($scr->document->ins[$i]->tagAttrs['slot'],'on')) {
						$g['committed'] = false;
						logstr('csp_slot s'.$g['current_set'].'_'.$scr->document->ins[$i]->tagAttrs['slot']);
						setSprop($scr->document->ins[$i]->tagAttrs['slot'],'on',false);
					}
				} else {
					$file = strtolower($scr->document->ins[$i]->tagAttrs['file']);
					$file = str_replace('t/', '', $file);
					$g['used_bgs'][$file] = true;
					if (substr($file,0,1) == '1') $file = 'cg_'.$file;
					if ($file == 'cha_i1e_bg' || $file == 'cha_i1er_bg' || $file == 'cha_i1en_bg' || $file == 'cha_i1ef_bg') {
						if (getSprop($scr->document->ins[$i]->tagAttrs['slot'],'on')) {
							$g['committed'] = false;
							logstr('csp_slot s'.$g['current_set'].'_'.$scr->document->ins[$i]->tagAttrs['slot']);
							setSprop($scr->document->ins[$i]->tagAttrs['slot'],'on',false);
						}
						break;
					}
					$g['committed'] = false;
					$cmd = in_array($file, $g['big_images']) ? 'lbg2' : 'lbg';
					logstr($cmd.' s'.$g['current_set'].'_'.$scr->document->ins[$i]->tagAttrs['slot'].',"'.$file.'"');
					if ($cmd == 'lbg2') logstr('drop_cache '.'"'.$file.'"');

					/*if (getSprop($scr->document->ins[$i]->tagAttrs['slot'],'on')) {
						printModifiedSprop($scr->document->ins[$i]->tagAttrs['slot']);
					} else {*/
						setSprop($scr->document->ins[$i]->tagAttrs['slot'], 'on', true);
					//}
				}
				break;

			case 'REMOVE_SLOT':
				if (getSprop($scr->document->ins[$i]->tagAttrs['slot'],'on')) {
					$g['committed'] = false;
					logstr('csp_slot s'.$g['current_set'].'_'.$scr->document->ins[$i]->tagAttrs['slot']);
					if (getSprop($scr->document->ins[$i]->tagAttrs['slot'],'video')) {
						setSprop($scr->document->ins[$i]->tagAttrs['slot'],'video',false);
					}
					setSprop($scr->document->ins[$i]->tagAttrs['slot'],'on',false);
				}
				break;

			case 'ANIME_LOAD':
				$g['committed'] = false;
				setSprop($scr->document->ins[$i]->tagAttrs['slot'],'on',true);
				setSprop($scr->document->ins[$i]->tagAttrs['slot'],'lsp2','2');
				setSprop($scr->document->ins[$i]->tagAttrs['slot'],'video',true);
				$file = strtolower($scr->document->ins[$i]->tagAttrs['file']);
				$stretched = in_array($file,$g['stretched_videos']) ? ',1' : '';
				$g['used_anim'][$file] = 1;
				logstr('anim2 s'.$g['current_set'].'_'.$scr->document->ins[$i]->tagAttrs['slot'].',"'.
								$file.'",'.$scr->document->ins[$i]->tagAttrs['single_play'].$stretched);
				break;

			case 'RAIN_LOAD':
				$g['committed'] = false;
				$slot = $scr->document->ins[$i]->tagAttrs['slot'];
				if (getSprop($slot,'z_order') != getSalias($slot)) {
					//not doing this here any more but inside rain_load (to avoid uncommitted z_order changes)
					//logstr('z_order_override'.getSprop($slot,'lsp2').' '.getSalias($slot).','.getSalias($slot));
					setSprop($slot,'z_order',getSalias($slot));
				}
				if (getSprop($slot,'on')) {
					// We cannot allow previous rains to be not removed
					logstr('csp_slot s'.$g['current_set'].'_'.$slot);
				}

				setSprop($slot,'on',true);
				setSprop($slot,'lsp2','');
				setSprop($slot,'rain',true);
				setSprop($slot,'rain_spd',800);
				setSprop($slot,'rain_obj',500);
				setSprop($slot,'rain_wind',0);
				$orbs = isset($scr->document->ins[$i]->tagAttrs['show']) && $scr->document->ins[$i]->tagAttrs['show'] == 1;
				setSprop($slot,'rain_orbs',$orbs);
				if ($g['rain_style'] != $orbs) {
					$g['rain_style'] = $orbs;
					logstr('rain_style '.($orbs ? '2' : '0'));
				}
				logstr('rain_load s'.$g['current_set'].'_'.$slot.($orbs ? ',1' : ''));
				break;

			case 'SPRITE_COMMAND':
				if ($scr->document->ins[$i]->tagAttrs['property'] == 'RAIN_SPEED' ||
					$scr->document->ins[$i]->tagAttrs['property'] == 'RAIN_DROPS' ||
					$scr->document->ins[$i]->tagAttrs['property'] == 'RAIN_WIND_FACTOR') {

					$e = 0;
					$slots = [];
					$rainframes = '';

					while ($scr->document->ins[$i+$e]->tagAttrs['type'] == 'SPRITE_COMMAND' &&
							($scr->document->ins[$i+$e]->tagAttrs['property'] == 'RAIN_SPEED' ||
							$scr->document->ins[$i+$e]->tagAttrs['property'] == 'RAIN_DROPS' ||
							$scr->document->ins[$i+$e]->tagAttrs['property'] == 'RAIN_WIND_FACTOR')) {

						$raininst = $scr->document->ins[$i+$e];

						$slot = $raininst->tagAttrs['slot'];
						$slots[$slot] = true;

						switch ($raininst->tagAttrs['property']) {
							case 'RAIN_SPEED':
								if (isset($raininst->tagAttrs['value'])) $val = getRealVal($raininst->tagAttrs['value']);
								else $val = '0';
								setSprop($slot,'rain_spd',$val);
								break;

							case 'RAIN_DROPS':
								if (isset($raininst->tagAttrs['value'])) $val = getRealVal($raininst->tagAttrs['value']);
								else $val = '0';
								if (isset($raininst->tagAttrs['frames']) && $raininst->tagAttrs['frames'] != '0')
									$rainframes = ','.round(getRealVal($raininst->tagAttrs['frames'])*(1000/60.0));
								setSprop($slot,'rain_obj',$val);
								break;

							case 'RAIN_WIND_FACTOR':
								if (isset($raininst->tagAttrs['value'])) $val = getRealVal($raininst->tagAttrs['value']);
								else $val = '0';
								setSprop($slot,'rain_wind',$val);
								break;
						}
						$e++;
					}

					foreach ($slots as $slot => $flg) {
						logstr('rain_params '.getSalias($slot).','.getSprop($slot,'rain_obj').','.getSprop($slot,'rain_spd').','.getSprop($slot,'rain_wind').$rainframes);
					}

					$i += $e-1;
				} else if ($scr->document->ins[$i]->tagAttrs['property'] == 'SPRITE_CENTER_OFFSET_X' ||
							$scr->document->ins[$i]->tagAttrs['property'] == 'SPRITE_CENTER_OFFSET_Y') {
					$slot = $scr->document->ins[$i]->tagAttrs['slot'];
					if ($scr->document->ins[$i]->tagAttrs['property'] == 'SPRITE_CENTER_OFFSET_X') {
						if (isset($scr->document->ins[$i]->tagAttrs['value'])) $val_x = getRealVal($scr->document->ins[$i]->tagAttrs['value']);
						else $val_x = '0';

						setSprop($slot,'centrex',$val_x);

						if (isset($scr->document->ins[$i+1]->tagAttrs['property']) && $scr->document->ins[$i+1]->tagAttrs['property'] == 'SPRITE_CENTER_OFFSET_Y') {
							if (isset($scr->document->ins[$i+1]->tagAttrs['value'])) $val_y = getRealVal($scr->document->ins[$i+1]->tagAttrs['value']);
							else $val_y = '0';
							setSprop($slot,'centrey',$val_y);
							$i++;
						}

						logstr('set_scale_center '.getSalias($slot).','.getSprop($slot,'centrex').','.getSprop($slot,'centrey'));
					} else { //'SPRITE_CENTER_OFFSET_Y':
						if (isset($scr->document->ins[$i]->tagAttrs['value'])) $val_y = getRealVal($scr->document->ins[$i]->tagAttrs['value']);
						else $val_y = '0';

						setSprop($slot,'centrey',$val_y);

						if (isset($scr->document->ins[$i+1]->tagAttrs['property']) && $scr->document->ins[$i+1]->tagAttrs['property'] == 'SPRITE_CENTER_OFFSET_X') {
							if (isset($scr->document->ins[$i+1]->tagAttrs['value'])) $val_x = getRealVal($scr->document->ins[$i+1]->tagAttrs['value']);
							else $val_x = '0';
							setSprop($slot,'centrex',$val_x);
							$i++;
						}

						logstr('set_scale_center '.getSalias($slot).','.getSprop($slot,'centrex').','.getSprop($slot,'centrey'));
					}
				} else {
					property_convertor($scr->document->ins[$i]);
				}
				break;

			case 'SPRITE_COMMAND_WAIT_FOR_END':
				wait_property_convertor($scr->document->ins[$i]);
				break;

			case 'SPRITESET_COMMAND':
				spriteset_property_convertor($scr->document->ins[$i]);
				break;

			case 'SPRITESET_COMMAND_WAIT_FOR_END':
				wait_spriteset_property_convertor($scr->document->ins[$i]);
				break;

			case 'SELECT_SPRITESET':
				$new_sptset = intval($scr->document->ins[$i]->tagAttrs['num']);
				if ($g['current_set'] != (string)$new_sptset) {
					$g['current_set'] = (string)$new_sptset;
				}
				if (!$g['spriteset_state'][$new_sptset]) {
					$g['spriteset_state'][$new_sptset] = true;
					logstr('spriteset_enable '.$g['current_set']);
				}
				break;

			case 'SPRITESET_CLEAR':
				removeSet(true);
				break;

			case 'SPRITESET_INITIALIZE':
				removeSet(false);
				break;

			case 'GLOBAL_DISPLAY_COMMAND':
				global_property_convertor($scr->document->ins[$i]);
				break;

			case 'GLOBAL_DISPLAY_COMMAND_WAIT_FOR_END':
				wait_global_property_convertor($scr->document->ins[$i]);
				break;

			case 'MASK_COMMAND':
				$mask = 'msk_'.strtolower($scr->document->ins[$i]->tagAttrs['mask']);

				//mode==1 is horizontal flip
				if ($scr->document->ins[$i]->tagAttrs['mode'] == '1' && $mask == 'msk_cutb') {
					$mask = 'msk_cutc';
				} else if ($scr->document->ins[$i]->tagAttrs['mode'] == '1' && $mask == 'msk_cut_up') {
					$mask = 'msk_cut_up_flip';
				} else if ($scr->document->ins[$i]->tagAttrs['mode'] == '1' && $mask == 'msk_cut_down') {
					$mask = 'msk_cut_down_flip';
				//mode==3 is 180 degree rotate
				} else if ($scr->document->ins[$i]->tagAttrs['mode'] == '3' && $mask == 'msk_cutb') {
					$mask = 'msk_cuta';
				} else if ($scr->document->ins[$i]->tagAttrs['mode'] == '3' && $mask == 'msk_cuta') {
					$mask = 'msk_cutb';
				} else if ($scr->document->ins[$i]->tagAttrs['mode'] != '0') {
					err('Incompatible mode');
				}

				// Find a mask slot:
				$found_mask = false;
				for ($mask_id = 1, $mask_num = sizeof($g['msk_st']); $mask_id <= $mask_num; $mask_id++) {
					// Make sure there is no mask in use
					$try_this = true;
					for ($omask_id = 1; $omask_id <= $mask_num; $omask_id++) {
						if ($omask_id == $mask_id) continue;
						if ($g['msk_st']['msk'.$omask_id] == $g['current_set']) {
							$try_this = false;
							break;
						}
					}
					if ($try_this && ($g['msk_st']['msk'.$mask_id] == $g['current_set'] || $g['msk_st']['msk'.$mask_id] == -1)) {
						$g['msk_st']['msk'.$mask_id] = $g['current_set'];
						logstr('mask_set msk'.$mask_id.','.$mask.','.$g['current_set']);
						$found_mask = true;
						break;
					}
				}

				if (!$found_mask) err('Need more masks');

				break;

			case 'HIDE_DIALOGUE_WINDOW':
				//if ($g['need_textoff']) {
					logstr('textoff');
				//	$g['need_textoff'] = false;
				//}
				break;

			case 'WAIT':
				$val = round($scr->document->ins[$i]->tagAttrs['duration']*(1000/60.0));
				// Let's say 167 is a mgic value we may likely skip entirely, if we need
				logstr(($val == 167 ? 'waits ' : 'wait ').$val);
				break;

			case 'WAITTIMER':
				logstr('waittimer '.round($scr->document->ins[$i]->tagAttrs['duration']*(1000/60.0)));
				break;

			case 'CLEARTIMER':
				logstr('skip_off');
				break;

			case 'UNSETTIMER':
				logstr('skip_on'); //enable skip
				break;

			case 'STACK_PUSH':
				$data = $scr->document->ins[$i]->tagAttrs['data'];
				$data = substr($data,6);
				$regs = [];
				while (preg_match('/^reg([0-9]{1,2})/',$data,$regs)) {
					$g['stack'][] = $g['regs'][$regs[1]];
					if ($regs[1] < 10) {
						$data = substr($data,5);
					} else {
						$data = substr($data,6);
					}
				}
				break;

			case 'STACK_POP':
				$data = $scr->document->ins[$i]->tagAttrs['data'];
				$regs = [];
				while (preg_match('/^reg([0-9]{1,2})/',$data,$regs)) {
					$g['regs'][$regs[1]] = array_pop($g['stack']);
					if ($regs[1] < 10) {
						$data = substr($data,5);
					} else {
						$data = substr($data,6);
					}
				}
				break;

			case 'REGISTER_MODIFY':
				$data = $scr->document->ins[$i]->tagAttrs['data'];
				$matches = [];

				if (!preg_match('/^reg([0-9]{1,2})(=|\+=|\?=|\&=)(reg[0-9]{1,2}|[-]{0,1}[0-9]{1,5})(u|)(|\-)(|reg[0-9]{1,2}|[-]{0,1}[0-9]{1,5})(u|);$/',$data,$matches)) {
					err('REGISTER_MODIFY ('.$data.') unrecognised command');
				}

				if ($matches[5] == '-') {
					$g['regs'][$matches[1]] = getRealVal($matches[3])-getRealVal($matches[6]);
				} else if($matches[2] == '=') {
					$g['regs'][$matches[1]] = getRealVal($matches[3]);
					if($matches[1]==34) {
						$quizmask = intval($g['regs'][$matches[1]]);
						logstr('mov %eva_button_enabled,' . (($quizmask & 0x40) ? '1' : '0'));
						logstr('mov %quiz_answer_count,' . (string)substr_count(decbin($quizmask & 0x3F), '1'));
					} elseif($matches[1]==37) {
						logstr('mov %pair_answers_counter,'.$matches[3]);
					} elseif($matches[1]==38) {
						logstr('mov %eva_hint_counter,'.$matches[3]);
					} elseif($matches[1]==39) {
						logstr('mov %chosen_ending,'.$matches[3]);
					}
				} else if($matches[2] == '+=') {
					$g['regs'][$matches[1]] += getRealVal($matches[3]);
					if($matches[1]==37) {
						logstr('add %pair_answers_counter,'.$matches[3]);
					} elseif($matches[1]==38) {
						logstr('add %eva_hint_counter,'.$matches[3]);
					}
				} else if($matches[2] == '&=') {
					$g['regs'][$matches[1]] &= getRealVal($matches[3]);
					if($matches[1]==34) {
						logstr('mov %eva_button_enabled,0');
					}
				} else {
					err('REGISTER_MODIFY ('.$data.') unsupported command');
				}

				if ($matches[1] == 27) {
					logstr('mov %flg_number_o,'.$g['regs'][$matches[1]]);
				}

				break;

			case 'TIP_ENTRY_UNLOCK':
				$g['tips_c']++;
				/*if ($g['tips'][$g['tips_c']] == $scr->document->ins[$i]->tagAttrs['data'])*/ {
					logstr('gstt '.$g['tips_c']);
				} /*else {
					err('ERROR: '.$scr->document->ins[$i]->tagAttrs['data']);
				}*/
				break;

			case 'CHARACTER_ENTRY_UNLOCK':
				$g['chars_c']++;
				/*if ($g['chars'][$g['chars_c']]['id'] == $scr->document->ins[$i]->tagAttrs['character'] &&
					$g['chars'][$g['chars_c']]['mode'] == $scr->document->ins[$i]->tagAttrs['mode'])*/ {
					logstr('gstc '.(string)$g['chars_c']);
				}/* else {
					err('ERROR: '.$scr->document->ins[$i]->tagAttrs['character']);
				}*/
				break;

			case 'TEXTBOX_COMMAND':
				if ($scr->document->ins[$i]->tagAttrs['textbox_disable'] == '1') {
					$g['textbox_mode']			= false;
					//$g['current_char']			= 'non';
					logstr('set_window_simple 80');
				} else {
					$g['textbox_mode']			= true;
					$g['current_char']			= 'non';
					logstr('set_name_window_non'); //Fixme... don't update the screen?
				}
				$g['align'] = $scr->document->ins[$i]->tagAttrs['align'];
				break;

			case 'BGM_FADE':
				if (!$g['volume_enabled']['bgm'] && $g['volume_level']['bgm'] == 0) {
					break;
				} else if (!$g['volume_enabled']['bgm']) {
					$g['volume_level']['bgm'] = 0;
					break;
				}

				$inst = $scr->document->ins[$i];
				$dur = round($inst->tagAttrs['duration']*(1000/60.0));
				logstr('vol_bgm '.(($dur != 0) ? '-1,'.$dur : '-1'));
				$g['volume_level']['bgm'] = 0;
				$g['volume_enabled']['bgm'] = false;
				break;

			case 'CHANNEL_FADE':
				$inst = $scr->document->ins[$i];
				$dur = round($inst->tagAttrs['duration']*(1000/60.0));
				if ($inst->tagAttrs['channel'] < 16) { //SE
					$ch = $inst->tagAttrs['channel']+1;

					//Avoid unnecessary (not working) calls
					if (!$g['volume_enabled']['se'.$ch] && $g['volume_level']['se'.$ch] == 0) {
						break;
					} else if (!$g['volume_enabled']['se'.$ch]) {
						$g['volume_level']['se'.$ch] = 0;
						break;
					}

					logstr('vol_se '.$ch.(($dur != 0) ? ',-1,'.$dur : ',-1'));
					$g['volume_level']['se'.$ch] = 0;
					$g['volume_enabled']['se'.$ch] = false;
				} else if ($inst->tagAttrs['channel'] < 23) {
					$ch = $inst->tagAttrs['channel']-15;

					//Avoid unnecessary (not working) calls
					if (!$g['volume_enabled']['me'.$ch] && $g['volume_level']['me'.$ch] == 0) {
						break;
					} else if (!$g['volume_enabled']['me'.$ch]) {
						$g['volume_level']['me'.$ch] = 0;
						break;
					}

					logstr('vol_me '.$ch.(($dur != 0) ? ',-1,'.$dur : ',-1'));
					$g['volume_level']['me'.$ch] = 0;
					$g['volume_enabled']['me'.$ch] = false;
				}
				break;

			case 'MIX_CHANNEL_FADE':
				$inst = $scr->document->ins[$i];
				$dur = round($inst->tagAttrs['duration']*(1000/60.0));
				foreach ($g['volume_level'] as $key => $val)
					if ($key != 'bgm') {
						$g['volume_level'][$key] = 0;
						$g['volume_enabled'][$key] = false;
					}
				logstr('vol_mix_fade '.(string)$dur);
				break;

			case 'BGM_VOLUME':
				$inst = $scr->document->ins[$i];
				$dur = round($inst->tagAttrs['duration']*(1000/60.0));
				$vol = getVolume($inst->tagAttrs['volume']);

				//Avoid unnecessary (not working) calls
				if (!$g['volume_enabled']['bgm'] || $g['volume_level']['bgm'] == $vol) {
					break;
				}

				logstr('vol_bgm '.(string)$vol.(($dur != 0) ? ','.$dur : ''));
				//Unfortunately PS3 code is far from ideal. These not working instructions are cut by the above check now
				//if ($g['volume_level']['bgm'] == 0 && $vol != 0 && !$g['volume_enabled']['bgm']) logstr(';Error, trying to increase disabled bgm volume');
				$g['volume_level']['bgm'] = $vol;
				break;

			case 'CHANNEL_VOLUME':
				$inst = $scr->document->ins[$i];
				$dur = round($inst->tagAttrs['duration']*(1000/60.0));
				$vol = getVolume($inst->tagAttrs['volume']);
				if ($inst->tagAttrs['channel'] < 16) { //SE
					$ch = $inst->tagAttrs['channel']+1;

					//Avoid unnecessary (not working) calls
					if (!$g['volume_enabled']['se'.$ch] || $g['volume_level']['se'.$ch] == $vol) {
						break;
					}

					logstr('vol_se '.$ch.','.(string)$vol.(($dur != 0) ? ','.$dur : ''));
					//Unfortunately PS3 code is far from ideal. These not working instructions are cut by the above check now
					//if ($g['volume_level']['se'.$ch] == 0 && $vol != 0 && !$g['volume_enabled']['se'.$ch]) logstr(';Error, trying to increase disabled se volume');
					$g['volume_level']['se'.$ch] = $vol;
				} else if ($inst->tagAttrs['channel'] < 23) {
					$ch = $inst->tagAttrs['channel']-15;

					//Avoid unnecessary (not working) calls
					if (!$g['volume_enabled']['me'.$ch] || $g['volume_level']['me'.$ch] == $vol) {
						break;
					}

					logstr('vol_me '.$ch.','.(string)$vol.(($dur != 0) ? ','.$dur : ''));
					//Unfortunately PS3 code is far from ideal. These not working instructions are cut by the above check now
					//if ($g['volume_level']['me'.$ch] == 0 && $vol != 0 && !$g['volume_enabled']['me'.$ch]) logstr(';Error, trying to increase disabled me volume');
					$g['volume_level']['me'.$ch] = $vol;
				}
				break;

			 case 'RUMBLE':
				$dur = round($scr->document->ins[$i]->tagAttrs['duration']*(1000/60.0));
				$str = round(($scr->document->ins[$i]->tagAttrs['strength']/255.0)*100);
				logstr('rumble '.$str.','.$dur);
				break;

			 case 'MOVIE_PLAY':
				//TODO: change sprite(?)
				$g['used_mov'][$scr->document->ins[$i]->tagAttrs['file']] = 1;
				$g['committed'] = true;
				logstr('pam 749,"'.$scr->document->ins[$i]->tagAttrs['file'].'",0');
				break;

			 case 'VOICE_PLAY':
				$matches = [];
				preg_match('/^(.+?)\/(.+?)$/',$scr->document->ins[$i]->tagAttrs['voice'],$matches);
				$g['used_voice']['"'.$matches[1].'"*"'.strtolower($matches[2]).'"'] = 1;
				logstr('lv 0,"'.$matches[1].'","'.strtolower($matches[2]).'"');
				break;

			 case 'VOICE_WAIT_FOR_END':
				logstr('waitvoice');
				break;

			 case 'OPEN_TROPHY':
				if ($scr->document->ins[$i]->tagAttrs['id'] != 1) //not welcome
					logstr('trophy_open '.$scr->document->ins[$i]->tagAttrs['id']);
				break;

			 case 'GOSUB':
				$id = [];
				if (!preg_match('/^sub_([0-9a-fA-F]{1,10})\(/',$scr->document->ins[$i]->tagAttrs['data'],$id)) {
					err('ERROR: '.$scr->document->ins[$i]->tagAttrs['data']);
				}
				$id = strtolower($id[1]);

				if ($id == '91ca6' || $id == 'c6366') {
					$dur = round(getRealVal('reg10')*(1000/60.0));
					$ef = getRealVal('reg9');
					parse_effect($ef,$dur,true);
				} else if ($id == '92bc6' || $id == 'cfbfb') {
					$g['committed'] = true;
					logstr('show_whirl');
				} else if ($id == '924c3') {
					logstr('flg_p');
				} else if ($id == '92a4f') {
					$g['committed'] = true;
					logstr('break_glass');
					reset_sprites();
				} else if ($id == '928a3') {
					$g['committed'] = true;
					logstr('break_glass2');
					reset_sprites();
				} else if ($id == 'cfa84') {
					$g['committed'] = true;
					logstr('break_glass3');
					reset_sprites();
				} else if ($id == 'cf8d5') {
					$g['committed'] = true;
					logstr('break_glass4');
					reset_sprites();
				} else if ($id == '9239f') {
					$g['committed'] = true;
					//%clock_xpos,%clock_ypos,%clock_scale,%start_min,%end_min,%clock_sfx,%clock_sfx2
					logstr('display_clock '.(960+$g['regs'][28]).','.(1080+$g['regs'][29]).','.round($g['regs'][30]/10).','.$g['regs'][13].','.$g['regs'][14].','.$g['regs'][31].','.$g['regs'][32]);
					reset_sprites();
				} else if ($id == 'ce623') {
					logstr('show_coin_counter %total_coin_count');
				} else if ($id == 'ce904') {
					logstr('show_coin_counter2 %total_coin_count');
				} else if ($id == 'cf0db') {
					logstr('show_coin_counter3 %total_coin_count');
				} else if ($id == 'cfc2d') {
					logstr('all_presents_discarded '.$g['regs'][13]);
				} else {
					echo 'WARN: Unknown gosub function '.$scr->document->ins[$i]->tagAttrs['data'].LF;
					logstr(';'.$scr->document->ins[$i]->tagAttrs['data']);
				}
				break;

			 case 'REGISTER_CALC':
			 	$reg = intval(substr($scr->document->ins[$i]->tagAttrs['output'], 3));
				$g['regs'][$reg] = aluCalculate($scr->document->ins[$i]->tagAttrs['command']);
				break;

			 case 'REGISTER_CONDITION':
			 	$matches = [];
			 	if (preg_match('/if\(reg([0-9]{1,2})\=\=([0-9]{1,2})\) { goto (0x[0-9a-f]+); }/',$scr->document->ins[$i]->tagAttrs['data'],$matches)) {
			 		if($matches[1]==39) {
			 			logstr('if %chosen_ending=='.$matches[2].' goto *uu_uu_'.$matches[3]);
					 	if (!isset($g['labeladdrs'][$matches[3]])) {
				 			echo 'WARN: Unknown label to conditionally jump to: *uu_uu_' . $matches[3] . LF;
				 			logstr(';WARN: Unknown label to conditionally jump to: *uu_uu_' . $matches[3]);
				 		}
				 		break;
				 	}
			 	}
				if ($scr->document->ins[$i+1]->tagAttrs['type'] == 'REGISTER_CONDITION') {
					$matches = [];
					if (!preg_match('/if\(([0-9]{1,2})\!\=([0-9]{1,2})\)/',$scr->document->ins[$i]->tagAttrs['data'],$matches)) break;
					$match = $matches[1] == 24;
					if (!preg_match('/if\(([0-9]{1,2})\!\=([0-9]{1,2})\)/',$scr->document->ins[$i+1]->tagAttrs['data'],$matches)) break;
					if ($match)
						$match = $matches[1] == 00;
					$i+=2;
					if ($match) $g['regs'][32] = '1';
				}
				break;

			 case 'PRINT':
				parse_effect($scr->document->ins[$i]->tagAttrs,0,false);
				break;
			 case 'CLICK_WAIT':
				logstr('click');
				break;
			 case 'CAKE':
			 case 'QUIZ':
			 case 'QUIZ2':
			 case 'MURDER_STORY':
				logstr(strtolower($scr->document->ins[$i]->tagAttrs['type']));
			 	break;
			 case 'GET_RESPONSE_CAKE':
			 case 'GET_RESPONSE_QUIZ':
			 case 'GET_RESPONSE_QUIZ2':
			 case 'GET_RESPONSE_MURDER_STORY':
				logstr('*get_response_'.$scr->document->ins[$i]->tagAttrs['iaddr']);
				logstr(strtolower($scr->document->ins[$i]->tagAttrs['type']));
				break;
			 case 'SECTION_MARKER':
			 case 'SECTION_END':
				//echo 'INFO: ignoring command: '.$type.LF;
				break;
			 case 'SYSCALL':
			 	if ($g['curr_chapter'] == 'umi8_16.txt') {
			 		logstr('gosub *chiru_ending' . LF . 'goto *umi8_end');
			 	}
			 	break;
			 case 'JUMP_ONVALUE_NBRANCHES':
			 	$regvarname = $scr->document->ins[$i]->tagAttrs['register'];
			 	switch($regvarname) {
			 		case 'reg0':
			 			logstr('mov %branch_tmp,%get_response_value');
						break;
					case 'reg36':
						logstr('mov %branch_tmp,%total_coin_count');
						break;
					case 'reg37':
						logstr('mov %branch_tmp,%pair_answers_counter');
						break;
					default:
						logstr('mov %branch_tmp,%' . $regvarname);
						break;
			 	}
			 	// This could be rewritten using tablegoto.
			 	for ($branch = 0; $branch < getRealVal($scr->document->ins[$i]->tagAttrs['count']); $branch++) {
			 		$aaa = '0x' . dechex(getRealVal($scr->document->ins[$i]->tagAttrs['addr' . (string)$branch]));
			 		logstr('if %branch_tmp = '.(string)$branch.' goto *uu_uu_' . $aaa);
				 	if (!isset($g['labeladdrs'][$aaa])) {
				 		echo 'WARN: Unknown label to case to: *uu_uu_' . $aaa . LF;
				 		logstr(';WARN: Unknown label to case to: *uu_uu_' . $aaa);
				 	}
			 	}
			 	break;
			 case 'JUMP_TO_ADDRESS':
			 	$aaa = str_replace(';', '', str_replace('goto ', '', $scr->document->ins[$i]->tagAttrs['data']));
			 	logstr('goto *uu_uu_' . $aaa);
			 	if (!isset($g['labeladdrs'][$aaa])) {
			 		echo 'WARN: Unknown label to jump to: *uu_uu_' . $aaa . LF;
			 		logstr(';WARN: Unknown label to jump to: *uu_uu_' . $aaa);
			 	}
			 	break;
			 case 'SWITCH':
				logstr(';'.$scr->document->ins[$i]->tagAttrs['data']);
				// Ugly hack to fix invalid darkening near d60569.
				$g['regs'] = $g['def_regs'];
				break;
			 case 'SYSTEM_MENU_SHOW':
			 	if ($scr->document->ins[$i]->tagAttrs['id'] == '5') {
			 		logstr('show_characters');
			 	} else {
			 		err('Unsupported SYSTEM_MENU_SHOW argument '.$scr->document->ins[$i]->tagAttrs['id']);
			 	}
			 	break;
			 default:
				err('I do not know what to do to this command, sorry :( '.$type);
		}

		$i++;
	}

	foreach ($g['labeladdrs'] as $a => $s) {
		if (!$s) {
			echo 'WARN: Failed to find label to jump to: *uu_uu_' . $a . LF;
			logstr(';WARN: Failed to find label to jump to: *uu_uu_' . $a);
		}
	}

	logstr('ch_done flag_scenario_'.(EP_DEF+4).'_ura');
	logstr('*ura_'.(EP_DEF+4).'_end');
	logstr('goto *end_game');
	logend();

	echo 'Parsing done: '.round(microtime(true)-$start, 2).' seconds'.LF;

	if (PRINT_USED) {
		echo LF.'Used bgs:'.LF;
		foreach ($g['used_bgs'] as $bg => $f) echo $bg.LF;
		echo LF.'Used sps:'.LF;
		foreach ($g['used_sps'] as $key => $sps)
			foreach ($sps as $emo => $f)
				echo $key.'_'.$emo.LF;
		echo LF.'Used anims:'.LF;
		foreach ($g['used_anim'] as $v => $f) echo $v.LF;
		echo LF.'Used movies:'.LF;
		foreach ($g['used_mov'] as $v => $f) echo $v.LF;
		echo LF.'Used voices:'.LF;
		foreach ($g['used_voice'] as $v => $f) echo $v.LF;
	}
}

main();
