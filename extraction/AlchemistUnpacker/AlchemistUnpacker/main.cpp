/*
 * AlchemistUnpacker
 * Based on Noko's (Andrey Osenenko) ps3umi
 * Uses PIA5PS2 decompressing and decrypting algorithms
 * Uses IMG_savepng taken from RenPy engine
 */

#include "main.h"
#include "snr.h"

#include <bitset>

//FIXME: sdl ugh...
extern "C" __attribute__((weak)) void CGDisplayBeamPosition ();
extern "C" __attribute__((weak)) void CGDisplayBeamPosition () { }

int usage(int argc,char **argv) {
	fprintf(stderr,
			"Usage: %1$s file.pic file.png\n"
			"	Converts ps2 Higurashi and ps3 Umineko picture file.pic to file.png\n"
			"Usage: %1$s file.bup file\n"
			"	Creates multiple png files named file<emotion name>.png\n"
			"	from ps2 Higurashi and ps3 Umineko character sprite file.bup\n"
			"Usage: %1$s file.lzs file.txa\n"
			"	Uncompresses ps2 Higurashi picture collection to file.txa\n"
			"Usage: %1$s file.wip file.png\n"
			"	Converts ps2 Higurashi mask image file.wip to file.png\n"
			"Usage: %1$s file.msk file.png\n"
			"	Converts ps3 Umineko and Higurashi mask image file.msk to file.png\n"
			"Usage: %1$s file.txa file\n"
			"	Creates multiple png files named file_<sub-title>.png\n"
			"	from ps3 Umineko and Higurashi picture collection file.txa\n"
			"Usage: %1$s file.iso folder\n"
			"	Extracts contents of ps2 Higurashi image into folder\n"
			"Usage: %1$s DATA.ROM folder\n"
			"	Extracts contents of ps3 Umineko archive into folder\n"
			"Usage: %1$s snrdec file.snr game output.txt\n"
			"	Decompiles snr file into output.txt\n"
			"	r is for Rondo, c is for Chiru\n"
			"Usage: %1$s snrenc input.txt base.snr output.snr\n"
			"	Compiles txt file into output.snr\n",
			argv[0]
			);
	exit(1);
}

// Reversed from PIA5PS2
// Palette colours are swapped and alpha byte is represented by a half-alpha value
char decode_palette(uint8_t *buffer, uint8_t *result, short alpha) {
	uint8_t *p = result + 3;
	for (uint16_t i = 0; i < 256; i++, p += 4) {
		//4th bit | 2*3rd bit | 2*other bits
		alpha = 2 * ((i & 0x10) | 2 * ((i & 0xFFE7) | 2 * (i & 0x8)));
		*(p - 3) = *(buffer + alpha);			//R
		*(p - 2) = *(buffer + alpha + 1);		//G
		*(p - 1) = *(buffer + alpha + 2);		//B
		*(char*)&alpha = *(buffer + alpha + 3);	//A
		if ( (uint8_t)alpha <= 0x7F ) {
			*((char*)&alpha) = 2 * alpha;
			*p = alpha;
		} else {
			*p = 0xFF;
		}
	}

	return alpha;
}

/* It's a compression scheme similar to LZSS but with data itself
 * instead of 4096 byte circular buffer (which makes a lot of sense
 * to me) and different format of backreferences. */
size_t decompress_lzss(uint8_t *buffer, size_t size, uint8_t *result) {
	unsigned char *res=result;
	int p=0;
	int marker=1;
	int j;
	
	while(p<size){
		if(marker==1) marker=0x100|buffer[p++];
		
		if(marker&1){
			unsigned short v=(buffer[p]<<8)|buffer[p+1];
			int count,offset;
			unsigned char *pos;
			
			// There is something in common with LZM...
			if(v&0x8000){				//highest bit -> mode
				count=((v>>5)&0x3ff)+3; //higher 10 bits -> count
				offset=v&0x1f;			//lower 5 bits -> offset
			} else{
				count=(v>>11)+3;		//higher 4 bits -> count
				offset=(v&0x7ff)+32;	//lower 11 bits -> offset
			}
			
			pos=res-(offset+1)*4;
			
			//printf("0x%0.4X count %X off %X dst %d\n", v, count, offset, (int)(res-result));
			
			for(j=0;j<count;j++)
				*res++=*pos++;
			
			p+=2;
		} else{
			*res++=buffer[p++];
		}
		
		marker>>=1;
	}
	return (int)(res-result);
}

/* lz10 decompression, used in sui
 */
size_t decompress_lz10_sui(uint8_t *buffer, size_t size, uint8_t *result) {
	uint8_t *res = result;
	uint8_t *pos;
	int marker=1, i = 0;
	
	while (i < size) {
		if(marker==1) marker=0x100|buffer[i++];
		
		if (marker & 1) {
			uint16_t v = (buffer[i]<<8)|buffer[i+1];
			int count = (v >> 0xc) + 3;
			int offset = (v & 0xfff);
			i += 2;
			
			pos=res-(offset+1); // 3 for overlay
			for(int k=0;k<count;k++)
				*res++=*pos++;
		} else {
			*res++ = buffer[i++];
		}
		
		marker>>=1;
	}
	return res-result;
}

// Reversed from PIA5PS2
// Seems to be lz10 with some differences
size_t decompress_lz10(uint8_t *in, size_t in_size, uint8_t *out) {
	int out_size = 0;
	uint32_t curr = 0;
	int offset;
	int count;
	uint8_t *p;
	
	for ( int i = 0; i < in_size; i++ ) {
		
		if ( !(curr & 0xFF00) )
			curr = 0xFF00 | in[i++];
		
		if ( curr & 1 ) {
			count = (in[i] & 0xF) + 3;
			offset = out_size - (16 * (in[i] & 0xF0) | in[i+1]) - 1;
			i++;
			if ( count > 0 ) {
				p = &out[offset];
				do {
					out[out_size++] = *p++;
					count--;
				} while ( count );
			}
		} else {
			out[out_size++] = in[i];
		}
		
		curr /= 2;
	}
	return out_size;
}

/* After decompression only the first scanline in pictures is filled bytes
 * representing colors. Remaining scanlines instead have differences from
 * previous scanlines in them. If a pixel at (2,3) is represented by
 * bytes 0x00 0x00 0x00 0x00, then it means that its color is the same as
 * color of pixel at (2,2). This fuctions fixes this and makes all bytes
 * represent colors instead of differences.
 *
 * This function is also horribly mislabeled. */
void dpcm(unsigned char *src,unsigned char *dst,int w,int h,int scanline) {
	int x;
	
	for(x=scanline;x<scanline*h;x++){
		dst[x]+=src[x-scanline];
	}
}

void blit(unsigned char *src,int w,int h,int scanline,unsigned char *dst,int dx,int dy,int dstscanline) {
	int x;
	int y;
	
	for(y=0;y<h;y++){
		for(x=0;x<w;x++){
			int d=(dx+x)*4+(dy+y)*dstscanline;
			int s=x*4+y*scanline;
			
			//Change ARGB to ABGR
			dst[d+0] = src[s+2];
			dst[d+1] = src[s+1];
			dst[d+2] = src[s+0];
			dst[d+3] = src[s+3];
		}
	}
}

/* Not a proper over blend but instead "copy-unless-translaprent" */
void blend(unsigned char *src,int w,int h,int scanline,unsigned char *dst,int dx,int dy,int dstscanline) {
	int x;
	int y;
	
	for(y=0;y<h;y++){
		for(x=0;x<w;x++){
			int d=(dx+x)*4+(dy+y)*dstscanline;
			int s=x*4+y*scanline;
			
			//int da=dst[d+3];
			int sa=src[s+3];
			
			//Change ARGB to ABGR
			if(sa!=0) {
				dst[d+0] = src[s+2];
				dst[d+1] = src[s+1];
				dst[d+2] = src[s+0];
				dst[d+3] = src[s+3];
			}
		}
	}
}

void makedir(const char *dir) {
	char tmp[256];
	char *p = NULL;
	size_t len;
	
	snprintf(tmp, sizeof(tmp),"%s",dir);
	len = strlen(tmp);
	
	if(tmp[len - 1] == '/')
		tmp[len - 1] = 0;
	
	for(p = tmp + 1; *p; p++)
		if(*p == '/') {
			*p = 0;
			mkdir(tmp, S_IRWXU);
			*p = '/';
		}
	mkdir(tmp, S_IRWXU);
}

char *sjisToUnicode(char* sjis, size_t sjis_len) {
	
	size_t utf_len, utf_def_len, iconv_ret;
	
	iconv_t cd = iconv_open("UTF-8", "Shift_JIS");
	if( cd == (iconv_t)(-1) ) {
		fprintf( stderr, "Failed to open iconv\n");
		return nullptr;
	}
	
	utf_len = utf_def_len = sjis_len*3+1;
	char *utf = new char[utf_len];
	memset( utf, 0, utf_len );
	iconv_ret = iconv(cd, &sjis, &sjis_len, &utf, &utf_len);

	if (iconv_ret == (size_t)-1) {
		fprintf( stderr, "Failed to open iconv\n");
		return nullptr;
	}
	
	utf -= (utf_def_len-utf_len);
	
	iconv_close(cd);
	return utf;
}

int main(int argc, char* argv[]) {
	
	if(argc < 3) usage(argc,argv);
	
	if (!strcmp(argv[1],"snrdec")) {
		SNRVer mode;
		switch (argv[3][0]) {
			case 'r':
				mode = SNRVer::RONDO;
				break;
			case 'c':
				mode = SNRVer::CHIRU;
				break;
			default:
				mode = SNRVer::UNKNOWN;
				break;
		}
		process_snr_dec(argv[2], mode, argv[4]);
		return 0;
	} else if (!strcmp(argv[1], "snrenc")) {
		process_snr_enc(argv[2], argv[3], argv[4]);
		return 0;
	}
	
	if (SDL_Init(SDL_INIT_VIDEO) < 0 )
		exit(1);
	
	FILE *in;
	int magic;
	
	if((in = fopen(argv[1],"rb"))==NULL) {
		fprintf(stderr,"Couldn't open file %s for reading.\n",argv[1]);
		exit(1);
	}

	fread(&magic,4,1,in);
	fseek(in,0,0);

	switch(magic){
		case 0x32434950: 
			process_pic_higu(in,argv[2]);
			break;
		case 0x33434950:
			process_pic_umi(in,argv[2]);
			break;
		case 0x34434950:
			process_pic_sui(in,argv[2]);
			break;
		case 0x32505542:
			process_bup_higu(in,argv[2]);
			break;
		case 0x33505542:
			process_bup_umi(in,argv[2]);
			break;
		case 0x34505542:
			process_bup_sui(in,argv[2]);
			break;
		case 0x32535A4C:
			process_lzs_higu(in,argv[2]);
			break;
		case 0x20504957:
			process_wip_higu(in,argv[2]);
			break;
		case 0x334B534D:
			process_msk_umi(in,argv[2]);
			break;
		case 0x32415854:
			process_txa_higu(in,argv[2]);
			break;
		case 0x33415854:
			process_txa_umi(in,argv[2]);
			break;
		case 0x34415854:
			process_txa_sui(in,argv[2]);
			break;
		case 0x0D0D0D0D:
			process_rom_higu(in,argv[2]);
			break;
		case 0x204D4F52:
			process_rom_umi(in,argv[2]);
			break;
		case 0x324d4f52:
			process_rom_hou(in,argv[2]);
			break;
		default:
			fprintf(stderr, "%s: unknown magic: 0x%08x\n",argv[1],magic);
			fclose(in);
			exit(1);
			break;
	}
	
	fclose(in);
	
	SDL_Quit();
	
	return 0;
}
