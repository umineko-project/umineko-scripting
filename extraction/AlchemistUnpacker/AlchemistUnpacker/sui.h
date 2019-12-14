//
//  sui.h
//  AlchemistUnpacker
//

#ifndef __AlchemistUnpacker__sui__
#define __AlchemistUnpacker__sui__

struct pic_header {
	int32_t magic;
	int32_t filesize;
	uint16_t left;
	uint16_t top;
	uint16_t w;
	uint16_t h;
	int32_t calc; // always 1, calculate chunk dimensions and sizes
	int32_t chunks;
};

struct pic_chunk {
	int16_t left;
	int16_t top;
	uint32_t off;
};


struct sui_pack {
	uint16_t type;
	uint16_t unk1;
	uint32_t decompressed_size; // can be bigger
	uint16_t x;
	uint16_t y;
	uint16_t w; //normally bigger than w by 2
	uint16_t h; //normally bigger than h by 2
	uint32_t compressed_size;
	uint32_t unk3;
	uint16_t o_w;
	uint16_t o_h;
	uint32_t unk4;
};

struct bup_header {
	int32_t magic;
	int32_t filesize;
	uint16_t left;
	uint16_t top;
	uint16_t w;
	uint16_t h;
	uint32_t unk; //always = 1
	int32_t off_num;
	int32_t chunks;
	int32_t unk2; //char id??
};

struct bup_off {
	int32_t off;
};

struct bup_chunk {
	char title[16];
	int32_t emo_off;
	int32_t unk[3]; // always 0
	int32_t lips_off[3];
};

struct txa_header {
	int32_t magic;
	int32_t filesize;
	int32_t off;
	int32_t chunks;
	int32_t decsize;
	int32_t unk0;
	int32_t unk1;
	int32_t unk2;
};

struct txa_header_hou {
	int32_t magic;
	int32_t unk;
	int32_t filesize;
	int32_t off;
	int32_t chunks;
	int32_t decsize;
	int32_t unk0;
	int32_t unk1;
};

struct txa_chunk {
	uint16_t length;
	uint16_t index;
	uint16_t w;
	uint16_t h;
	uint32_t off;
	uint32_t size;
	char name[];
};

struct txa_chunk_hou {
	uint16_t length;
	uint16_t index;
	uint16_t w;
	uint16_t h;
	uint32_t off;
	uint32_t size;
	uint32_t decsize;
	char name[];
};

union txa_header_any {
	txa_header sui;
	txa_header_hou hou;
};

#endif
