//
//  hou.cpp
//  AlchemistUnpacker
//

#include "main.h"
#include "umi.h"

int process_rom_hou(FILE *rom, char *output)
{
	if (sizeof(off_t) <= 4) {
		fprintf(stderr,"64-bit seeking is not supported\n");
		return 1;
	}

	makedir(output);
	unpack_umi(rom, 0x20, output, 0x20, 9);

	return 0;
}

