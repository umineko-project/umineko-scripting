//
//  higu.h
//  AlchemistUnpacker
//

#ifndef AlchemistUnpacker_higu_h
#define AlchemistUnpacker_higu_h

struct pic_header {
	int64_t magic;
	int32_t filesize;
	uint16_t x_centre;
	uint16_t y_cutoff;
	uint16_t w;
	uint16_t h;
	int32_t unk0;
	int32_t palette_off;
	int32_t chunks;
};

struct pic_chunk {
	uint16_t left;
	uint16_t top;
	uint16_t w;
	uint16_t h;
	int32_t off;
	int32_t size;
};

struct bup_header {
	int64_t magic;
	int32_t filesize;
	int32_t char_id;
	uint16_t left;
	uint16_t top;
	uint16_t w;
	uint16_t h;
	int32_t palette_off;
	int32_t image_off;
	int32_t image_size;
	int32_t chunks;
};

struct bup_chunk {
	char title[16]; //could be 0x00
	int32_t chunk_cells; //2/3
    
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

struct lzs_header {
	int32_t magic;
	int32_t compressed;
	int32_t decompressed;
	int32_t unk0;
};

struct wip_header {
	int32_t magic;
	uint16_t w;
	uint16_t h;
	int32_t colours;
	int32_t size;
};

struct txa_header {
	int32_t magic;
	uint16_t data_size; //multiply 0x2000
    uint16_t chunks;
	int32_t off;
	int32_t unk1;
};

struct txa_chunk {
	uint16_t size;
	uint16_t index;
    uint16_t w;
    uint16_t h;
	uint16_t unk0;
	uint16_t unk1;
	uint16_t unk2;
    char name[];
};

#define _FILE_OFFSET_BITS 64

int unpack_higu(FILE *rom, off_t offset, char *output); 

struct pck_chunk {
	uint32_t nameoff;
	uint32_t off;
	uint32_t size;
};

#endif
