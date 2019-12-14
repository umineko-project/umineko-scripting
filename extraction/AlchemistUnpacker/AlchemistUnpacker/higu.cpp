//
//  higu.cpp
//  AlchemistUnpacker
//

#include "main.h"
#include "higu.h"
#include "higu_header.h"

int process_pic_higu(FILE *pic, char *output)
{
	SDL_Surface *surface;
	pic_header header;
	pic_chunk *chunks;
	uint8_t pal_data[1024];
	uint8_t *buffer;
	uint8_t *result;
	Uint32 palette[256];
	
	int i, k;
	unsigned int x,y;
	
	fread(&header, sizeof(header), 1, pic);
	
	surface = SDL_CreateRGBSurface(SDL_SWSURFACE, header.w, header.h, 32, 0x000000FF, 0x0000FF00, 0x00FF0000, 0xFF000000);
	SDL_LockSurface(surface);
	
	fprintf(stderr,"%s width %d height %d x_centre %d y_cutoff %d\n",
			output, header.w, header.h, header.x_centre, header.y_cutoff);
	
	chunks = (pic_chunk *)calloc(header.chunks, sizeof(pic_chunk));
	fread(chunks, sizeof(pic_chunk), header.chunks, pic);
	
	fseek(pic, header.palette_off, SEEK_SET);
	fread(&pal_data, 1024, 1, pic);
	
	decode_palette(pal_data,(uint8_t*)palette,768);
	
	for(i = 0; i < header.chunks; i++){
		pic_chunk *chunk=chunks+i;
		int size = chunk->size;
		
		if (chunk->w*chunk->h <= 0) continue;
		if (size <= 0) continue;
		
		buffer = (uint8_t *)malloc(size);
		result = (uint8_t *)malloc(chunk->w*chunk->h);
		
		fseek(pic, chunk->off, SEEK_SET);
		fread(buffer, size, 1, pic);
		decompress_lz10(buffer, size, result);
		
		k = 0;
		
		for (y = chunk->top; y < chunk->top+chunk->h; y++) {
			for (x = chunk->left; x < chunk->left+chunk->w; x++) {
				putpixel(surface, x, y, palette[*(result+k)]);
				k++;
			}
		}
		
		free(buffer);
		free(result);
	}
	
	SDL_UnlockSurface(surface);
	
	IMG_SavePNG(output, surface, IMAGE_COMPRESS_LEVEL);
	SDL_FreeSurface(surface);
	
	return 0;
	
}

int process_bup_higu(FILE *bup, char *output)
{
	SDL_Surface *surface, *back, *subchunk;
	bup_header header;
	bup_chunk *chunks;
	char str[256];
	uint8_t pal_data[1024];
	Uint32 palette[256];
	
	int i, k, size;
	unsigned int x,y;
	uint8_t *buffer, *result;
	
	SDL_Rect rect;
	
	fread(&header, sizeof(header), 1, bup);
	
	back = SDL_CreateRGBSurface(SDL_SWSURFACE, header.w, header.h, 32, 0x000000FF, 0x0000FF00, 0x00FF0000, 0xFF000000);
	SDL_LockSurface(back);
	
	chunks = (bup_chunk *)calloc(header.chunks, sizeof(bup_chunk));
	fread(chunks, sizeof(bup_chunk), header.chunks, bup);
	
	fseek(bup, header.palette_off, SEEK_SET);
	fread(&pal_data, 1024, 1, bup);
	
	decode_palette(pal_data,(uint8_t*)palette,768);
	
	buffer = (uint8_t *)malloc(header.image_size);
	result = (uint8_t *)malloc(header.w*header.h);
	
	fseek(bup, header.image_off, SEEK_SET);
	fread(buffer, header.image_size, 1, bup);
	decompress_lz10(buffer, header.image_size, result);
	
	k = 0;
	
	for (y = 0; y < header.h; y++) {
		for (x = 0; x < header.w; x++) {
			putpixel(back, x, y, palette[*(result+k)]);
			k++;
		}
	}
	
	free(buffer);
	free(result);
	
	SDL_UnlockSurface(back);
	
	fprintf( stderr, "%s width %d height %d left %d top %d:\n",output, header.w, header.h, header.left, header.top);
	
	for(i = 0; i < header.chunks; i++) {	
		bup_chunk *chunk=chunks+i;
		
		size = chunk->pic[0].size;
		
		surface = SDL_ConvertSurface(back, back->format, SDL_SWSURFACE);
		
		if (chunk->pic[0].w*chunk->pic[0].h <= 0) goto build_second_chunk;
		SDL_LockSurface(surface);
		//Face time;
		//If it exists it is stored in two cells one above another
		//First cell is a mask actually
		buffer = (uint8_t *)malloc(size);
		result = (uint8_t *)malloc(chunk->pic[0].w*chunk->pic[0].h*2);
		
		fseek(bup, chunk->pic[0].off, SEEK_SET);
		fread(buffer, size, 1, bup);
		decompress_lz10(buffer, size, result);
		
		subchunk = SDL_CreateRGBSurface(SDL_SWSURFACE, chunk->pic[0].w, chunk->pic[0].h, 32, 0x000000FF, 0x0000FF00, 0x00FF0000, 0xFF000000);
		SDL_LockSurface(subchunk);
		
		//Skip the mask
		k = chunk->pic[0].w*chunk->pic[0].h;
		
		for (y = 0; y < chunk->pic[0].h; y++) {
			for (x = 0; x < chunk->pic[0].w; x++) {
				putpixel(subchunk, x, y, palette[*(result+k)]);
				k++;
			}
		}
		
		free(buffer);
		free(result);
		
		SDL_UnlockSurface(subchunk);
		SDL_UnlockSurface(surface);
		
		rect.w = chunk->pic[0].w;
		rect.h = chunk->pic[0].h;
		rect.x = chunk->pic[0].left;
		rect.y = chunk->pic[0].top;
		
		SDL_BlitSurface(subchunk, NULL, surface, &rect);
		
	build_second_chunk:
			
		if (header.chunks == 1) {
			sprintf(str, "%s.png", output);
		} else {
			sprintf(str, "%s_%s.png", output, chunk->title);
		}
		
		IMG_SavePNG(str, surface, IMAGE_COMPRESS_LEVEL);
		SDL_FreeSurface(surface);
		
		if (header.chunks == 1) {
			SDL_FreeSurface(back);
			return 0;
		}
		
		//Lips time;
		//If they exist, they are stored in four cells from the top to the bottom
		//First cell is a mask of the following ones
		
		size = chunk->pic[1].size;
		
		if (chunk->pic[1].w*chunk->pic[1].h <= 0) {
			continue;
		}
		
		subchunk = SDL_CreateRGBSurface(SDL_SWSURFACE, chunk->pic[1].w*4, chunk->pic[1].h, 32, 0x000000FF, 0x0000FF00, 0x00FF0000, 0xFF000000);
		SDL_FillRect(subchunk, NULL, 0x00000000);
		
		buffer = (uint8_t *)malloc(size);
		result = (uint8_t *)malloc(chunk->pic[1].w*chunk->pic[1].h*4);
		
		fseek(bup, chunk->pic[1].off, SEEK_SET);
		fread(buffer, size, 1, bup);
		decompress_lz10(buffer, size, result);
		
		//Skip the mask
		k = chunk->pic[1].h*chunk->pic[1].w;
		
		
		for (int cell = 0; cell < 3; cell++) {
			for (y = 0; y < chunk->pic[1].h; y++) {
				for (x = chunk->pic[1].w*cell; x < chunk->pic[1].w*(cell+1); x++) {
					putpixel(subchunk, x, y, palette[*(result+k)]);
					k++;
				}
			}
		}
		
		free(buffer);
		free(result);
			
		fprintf( stderr,"	%s lips: width %d height %d left %d top %d\n", chunk->title, chunk->pic[1].w*4, chunk->pic[1].h, chunk->pic[1].left, chunk->pic[1].top);
		sprintf(str, "%s_%s-M.png", output, chunk->title);
		
		IMG_SavePNG(str, subchunk, IMAGE_COMPRESS_LEVEL);
		SDL_FreeSurface(subchunk);
	}
	
	SDL_FreeSurface(back);
	
	return 0;
}

int process_txa_higu(FILE *txa, char *output)
{
	txa_header header;
	fread(&header, sizeof(header), 1, txa);

	fseek(txa, 0, SEEK_END);
	long size = ftell(txa);
	fprintf( stderr, "unk0 0x%X size 0x%lX\n", header.unk1, size);
	
	return 1;
}

int process_lzs_higu(FILE *lzs, char *output)
{
	lzs_header header;   
	uint8_t *buffer, *result;
	
	fread(&header, sizeof(header), 1, lzs);
	
	buffer = (uint8_t *)malloc(header.compressed);
	result = (uint8_t *)malloc(header.decompressed);
	
	fread(buffer, header.compressed, 1, lzs);
	decompress_lz10(buffer, header.compressed, result);
	
	free(buffer);
	
	FILE *out = fopen(output,"wb");
	fwrite(result, header.decompressed, 1, out);
	fclose(out);
	
	free(result);
	
	return 0;
}

int process_wip_higu(FILE *wip, char *output)
{
	wip_header header;
	uint8_t *buffer, *result;
	
	fread(&header, sizeof(header), 1, wip);
	
	buffer = (uint8_t *)malloc(header.size);
	result = (uint8_t *)malloc(header.w*header.h);
	
	fread(buffer, header.size, 1, wip);
	decompress_lz10(buffer, header.size, result);
	
	free(buffer);
	
	SDL_Surface *surface = SDL_CreateRGBSurface(SDL_SWSURFACE, header.w, header.h, 8, 0xFF, 0xFF, 0xFF, 0);
	SDL_LockSurface(surface);
	
	int x,y,i=0;
	
	for (y = 0; y < header.h; y++) {
		for (x = 0; x < header.w; x++) {
			putpixel(surface, x, y, result[i]);
			i++;
		}
	}
	
	free(result);
	
	SDL_UnlockSurface(surface);
	
	fprintf( stderr,"%s width %d height %d\n", output, header.w, header.h);
	IMG_SavePNG(output, surface, IMAGE_COMPRESS_LEVEL);
	SDL_FreeSurface(surface);
	
	return 0;
}

int process_rom_higu(FILE *rom, char *output)
{
	if (sizeof(off_t) <= 4) {
		fprintf(stderr,"64-bit seeking is not supported\n");
		return 1;
	}
	
	makedir(output);
	unpack_higu(rom, 0x10, output);
	
	return 0;
}

int unpack_higu(FILE *rom, off_t offset, char *output)
{
	unsigned int count;
	memcpy(&count,&higu_header[offset],sizeof(count));
	pck_chunk *chunks = new pck_chunk[count];
	memcpy(chunks,&higu_header[offset+4],sizeof(pck_chunk)*count);
	
	for(int i = 0; i < count; i++) {
		char s[0x400],name[0x100];
		int isdir;
		off_t off;
		int size;
		
		pck_chunk *chunk = &chunks[i];
		size = chunk->size;
		off = chunk->off;
		
		isdir = chunk->nameoff & 0x80000000;
		chunk->nameoff &= ~0x80000000;
		off <<= isdir ? 4:11;
		
		memcpy(name,&higu_header[offset+chunk->nameoff],0x100);
		
		if((name[0]=='.' && name[1]=='\0') || (name[0]=='.' && name[1]=='.' && name[2]=='\0'))
			continue;
		
		sprintf(s,"%s/%s",output,name);
		
		if(!isdir) off += 0x222E0000;
		
		//fprintf(log,"%s isdir %d size %d off %I64d\n",s,isdir?1:0,isdir?0:size,isdir?0:off);
		
		if(isdir){
			makedir(s);
			if (unpack_higu(rom,off,s)) {
				return 1;
			}
		} else{
			FILE *f = fopen(s,"wb");
			
			if (!f) {
				fprintf(stderr,"Couldn't open file %s for writing.\n",s);
				return 1;
			}
			
			fseeko(rom,off,SEEK_SET);
			
			while(size>0){
				char buffer[0x400];
				int c = size > 0x400 ? 0x400 : size;
				
				fread(buffer,c,1,rom);
				fwrite(buffer,c,1,f);
				
				size -= c;
			}
			
			fclose(f);
			
			//patch data start
			f = fopen(s,"rb+");
			
			if (!f) {
				fprintf(stderr,"Couldn't open file %s for patching.\n",s);
				return 1;
			}
			
			unsigned int ptr; //global pointer
			unsigned int ptr2; //local pointer
			uint8_t cr_buffer[16];
			int rom_hdr = 0xF7161099; //taken from ROM
			int rom_off = (int)((off - 0x222E0000) >> 11);
			int key, key2;
			
			for (ptr = 0; ptr < chunk->size; ptr += 0x800) {
				fseek(f,ptr,SEEK_SET);
				fread(cr_buffer, 16, 1, f);
				
				key = rom_off + rom_hdr + (ptr >> 11);
				key2 = 0x343FD * key + 0x269EC3;
				
				for (ptr2 = 0; ptr2 < 16; ptr2++) {
					key2 = 0x343FD * key2 + 0x269EC3;
					cr_buffer[ptr2] ^= key2 ^ (uint8_t)(key2 >>16);
				}
				
				fseek(f,ptr,SEEK_SET);
				fwrite(cr_buffer, 16, 1, f);
			}
			
			fclose(f);
			//patch data end
		}
	}
	
	return 0;
}
