#
# ps3snr instruction manual
#
# Copyright (c) 2011-2018 Umineko Project
#
# This document is considered confidential and proprietary,
# and may not be reproduced or transmitted in any form 
# in whole or in part, without the express written permission
# of Umineko Project.
#

===============================
snr.bt
SNR script disassembler
===============================


Requirements:


To use this file you have to install SweetScape 010 Editor. 
Homepage: https://www.sweetscape.com

There exists an opensource project to parse bt scripts, which is under active
development. However, currently it is impossible to use it with SNRTemplate.bt.
See issues #11, #12, #13, #14, there also are several others.
Homepage: https://github.com/d0c-s4vage/py010parser

The only tested game scripts are as follows:

rondo.snr
Game: Umineko no Naku Koro ni: Rondo
Internal name: RONDO
SHA-256: 15fd5ffebce2a743cbbbc696876e1c8348a71afef7e5fcd32d3039f9b045b0ea

chiru.snr
Game: Umineko no Naku Koro ni: Chiru
Internal name: NOCTURNE
SHA-256: 55c604b83f45d6452df54039d0dd969b2db5b952d7bd02e56caec0aa079fe3ae

sui.snr (WIP)
Game: Higurashi no Naku Koro ni: Sui
Internal name: SUI
SHA-256: bca3e5f6b500c7f9d2a7c7235be9dba085b2f9c766c9823bee9ff4867be1792b


Usage:


In order to use snr.bt open the relevant game script and snr.bt
in 010 Editor.

- For the snr file set the encoding to Japanese:
View -> Character Set -> International -> Japanese
- Uncomment the macro with the loaded internal game name and comment the rest.

Run the template against the loaded game script.
If you receive a warning that says the the template is defining a large number
of variables, press Continue.

Afterwards you can browse the whole file and compare the hex code with the
decoded instructions.

In order to dump the disassembly to a set of xml files, you should uncomment
the PRINT macro in snr.bt. Please ensure that BASENAME points to an
existing path on your disk.

The produced xml files should be sequentially concatenated.


===============================
snrparser.php
Optimising decompiler for SNR disassembly
===============================


Requirements:


PHP interpreter 5.4. 7.0 or higher is recommended.
RONDO or NOCTURNE disassembly by snr.bt.


Usage:


Save the snr disassembly as script_rondo.xml or script_chiru.xml for
NOCTURNE.

Set whether the decompiled game is CHIRU in parser.php by setting CHIRU
define value to true or false.

Run in terminal:
php /path/to/parser.php /path/to/folder/with/script/xml/
