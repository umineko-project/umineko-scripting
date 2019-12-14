//
//  umi.h
//  AlchemistUnpacker
//

#ifndef AlchemistUnpacker_umi_h
#define AlchemistUnpacker_umi_h

#define SCANLINE(w) (4*(((w)+3)&0xfffc))

struct msk_header {
	int32_t magic;
	int32_t filesize;
	uint16_t w;
	uint16_t h;
	int32_t size;
};

struct bup_header {
	int32_t magic;
	int32_t filesize;
	int32_t char_id;
	uint16_t left;
	uint16_t top;
	uint16_t w;
	uint16_t h;
	int32_t off;
	int32_t size;
	int32_t chunks;
};

struct bup_chunk {
	char title[16];
	int32_t unk;
	
	struct{
		uint16_t left;
		uint16_t top;
		uint16_t w;
		uint16_t h;
		int32_t off;
		int32_t size;
	} pic[2];
	
	int32_t unka;
	int32_t unkb;
	int32_t unkc;
	int32_t unkd;
	
};

struct pic_header {
	int32_t magic;
	uint32_t filesize;
	uint16_t left;
	uint16_t top;
	uint16_t w;
	uint16_t h;
	int32_t unk0; //always 0
	uint32_t chunks;
};

struct pic_chunk {
	int32_t flag;
	uint16_t left;
	uint16_t top;
	uint16_t w;
	uint16_t h;
	uint32_t off;
	uint32_t size;
};

struct txa_header {
	int32_t magic;
	int32_t filesize;
	int32_t off;
	int32_t encsize;
	int32_t decsize;
	int32_t chunks;
	
	int32_t unk1;
	int32_t unk2;
};

struct txa_chunk {
	uint16_t length;
	uint16_t index;
	uint16_t w;
	uint16_t h;
	uint16_t scanline;
	uint16_t unk1;
	uint32_t off;
	char name[];
};

#define _FILE_OFFSET_BITS 64

int unpack_umi(FILE *rom, off_t offset, char *output, int add=0, int offfile=11); 

struct pck_chunk {
	uint32_t nameoff;
	uint32_t off;
	uint32_t size;
};

#endif
