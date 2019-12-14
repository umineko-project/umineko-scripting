//
//  main.h
//  AlchemistUnpacker
//

#ifndef AlchemistUnpacker_main_h
#define AlchemistUnpacker_main_h

#include <cstdlib>
#include <cstring>

#include <sys/types.h>
#include <sys/stat.h>

#ifdef __APPLE__
	#include <mach-o/dyld.h>
#endif

#include "SDL.h"
#include <png.h>
#include <zlib.h>
#include <iconv.h>

#define IMG_COMPRESS_OFF 0
#define IMG_COMPRESS_MAX 9
#define IMG_COMPRESS_DEFAULT -1

#define IMAGE_COMPRESS_LEVEL IMG_COMPRESS_OFF

#ifndef png_voidp
#define png_voidp voidp
#endif

#ifndef SCANLINE
#define SCANLINE(w) (4*(((w)+3)&0xfffc))
#endif

DECLSPEC int SDLCALL IMG_SavePNG(const char  *file, SDL_Surface *surf, int compression);
DECLSPEC int SDLCALL IMG_SavePNG_RW(SDL_RWops *src, SDL_Surface *surf, int compression);

int usage(int argc,char **argv);
char decode_palette(uint8_t *buffer, uint8_t *result, short alpha);
size_t decompress_lzss(uint8_t *buffer, size_t size, uint8_t *result);
size_t decompress_lz10(uint8_t *buffer, size_t size, uint8_t *result);
size_t decompress_lz10_sui(uint8_t *buffer, size_t size, uint8_t *result);
void dpcm(unsigned char *src,unsigned char *dst,int w,int h,int scanline);
void putpixel(SDL_Surface *surface, int x, int y, Uint32 pixel);
void blit(unsigned char *src,int w,int h,int scanline,unsigned char *dst,int dx,int dy,int dstscanline);
void blend(unsigned char *src,int w,int h,int scanline,unsigned char *dst,int dx,int dy,int dstscanline);

void makedir(const char *dir);
char *sjisToUnicode(char* sjis, size_t sjis_len);

int process_pic_higu(FILE *pic, char *output);
int process_bup_higu(FILE *bup, char *output);
int process_lzs_higu(FILE *lzs, char *output);
int process_wip_higu(FILE *wip, char *output);
int process_txa_higu(FILE *txa, char *output);
int process_rom_higu(FILE *rom, char *output);

int process_msk_umi(FILE *msk, char *output);
int process_bup_umi(FILE *bup, char *output);
int process_pic_umi(FILE *pic, char *output);
int process_txa_umi(FILE *txa, char *output);
int process_rom_umi(FILE *rom, char *output);

int process_bup_sui(FILE *bup, char *output);
int process_pic_sui(FILE *pic, char *output);
int process_txa_sui(FILE *txa, char *output);

int process_rom_hou(FILE *rom, char *output);

#endif
