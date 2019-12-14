//
//  sui.cpp
//  AlchemistUnpacker
//

#include "main.h"
#include "sui.h"

int process_bup_sui(FILE *bup, char *output)
{
	bup_header header;
	sui_pack pack_hdr;
	
	fread(&header, sizeof(bup_header), 1, bup);
	
	bup_off *offsets = (bup_off *)calloc(header.off_num, sizeof(bup_off));
	fseek(bup, sizeof(bup_header), SEEK_SET);
	fread(offsets, sizeof(bup_off), header.off_num, bup);
	
	bup_chunk *chunks = (bup_chunk *)calloc(header.chunks, sizeof(bup_chunk));
	fseek(bup, sizeof(bup_header)+sizeof(bup_off)*header.off_num, SEEK_SET);
	fread(chunks, sizeof(bup_chunk), header.chunks, bup);
	
	for (int i = 0; i < header.off_num; i++) {
		fseek(bup,offsets[i].off,0);
		fread(&pack_hdr,sizeof(sui_pack),1,bup);
		
		//printf("size: %4Xh, width: %4d (%4d), height: %4d (%4d), estimated: %d, unks %.8X %.8X %.8X\n",
		//	   pack_hdr.compressed_size,
		//	   pack_hdr.w, pack_hdr.width_m, pack_hdr.h, pack_hdr.height_m, pack_hdr.decompressed_size,
		//	   pack_hdr.unk1, pack_hdr.unk2, pack_hdr.unk3);
	}
	
	/*int w = 1280;
	int h = 128;
	size_t len = offsets[i+1].off - offsets[i].off;
	fseek(bup, offsets[i].off, SEEK_SET);
	
	uint8_t *buffer = (uint8_t*)malloc(len);
	uint8_t *result = (uint8_t*)malloc(w*h*4);
	bzero(result, w*h*4);
	fread(buffer, len, 1, bup);
	
	SDL_Surface *surface = SDL_CreateRGBSurface(SDL_SWSURFACE, w, h, 32, 0x000000FF, 0x0000FF00, 0x00FF0000, 0xFF000000);
	SDL_LockSurface(surface);
	
	size_t sz = decompress_lz10(buffer, len, result);
	//dpcm(result, result, w, h, SCANLINE(w));
	blit(result, w, h, SCANLINE(w), (uint8_t*)surface->pixels, 0, 0, surface->pitch);
	
	SDL_UnlockSurface(surface);
	
	IMG_SavePNG(output, surface, IMAGE_COMPRESS_LEVEL);
	
	
	SDL_FreeSurface(surface);*/

	
	/*char name[PATH_MAX];
	
	int h = 1900;
	//int w = 1280;
	
	int m = 1024;
	for (int w = 150; w > 100; w -= 1) {
		SDL_Surface *surface = SDL_CreateRGBSurface(SDL_SWSURFACE, w, h, 32, 0x000000FF, 0x0000FF00, 0x00FF0000, 0xFF000000);
		
		uint8_t *p = (uint8_t *)surface->pixels;
		SDL_LockSurface(surface);
		
		size_t x = 0;
		for (int i = 0; i < header.off_num-1; i++) {
			
			
			size_t len = offsets[i+1].off - offsets[i].off;
			fseek(bup, offsets[i].off, SEEK_SET);
			
			uint8_t *buffer = (uint8_t*)malloc(len);
			uint8_t *result = (uint8_t*)malloc(w*h*4);
			bzero(result, w*h*4);
			fread(buffer, len, 1, bup);
			for (size_t k = len-1; buffer[k] == 0; len--, k--);
			size_t sz = decompress_lz10(buffer, len, result);
			free(buffer);
			//sz = (sz + 15) & ~0xf;
			
			//if (surface->w * 4 != surface->pitch)
			//	throw 1;
			
			for (int j = 0; j < sz-m; j++) {
				p[x*4+0] = result[j+m];
				p[x*4+1] = result[j+m];
				p[x*4+2] = result[j+m];
				p[x*4+3] = 255;
				x++;
				
				if (x == w) {
					p += surface->pitch;
					x ^= x;
				}
			}
			if (x != 0) {
				p += surface->pitch;
				x ^= x;
			}
			
			free(result);
			//printf("%d\n", sz);
			//dpcm(result, result, w, h, SCANLINE(w));
			//blit(result, w, h, SCANLINE(w), (uint8_t*)surface->pixels, 0, 0, surface->pitch);

		}
		
		SDL_UnlockSurface(surface);
		sprintf(name, "%s_%d_%d.png", output, w, m);
		
		IMG_SavePNG(name, surface, IMAGE_COMPRESS_LEVEL);
		
		
		SDL_FreeSurface(surface);
	}*/
	
	/*for (int w = 120; w < 125; w+=1)// 484 l/ri7.bup
	for (int i = 0; i < header.chunks; i++) {
		SDL_Surface *surface = SDL_CreateRGBSurface(SDL_SWSURFACE, w, h, 32, 0x000000FF, 0x0000FF00, 0x00FF0000, 0xFF000000);
	 
		size_t len = chunks[i].lips_off[0] - chunks[i].emo_off;
		fseek(bup, chunks[i].emo_off, SEEK_SET);
		
		uint8_t *buffer = (uint8_t*)malloc(len);
		uint8_t *result = (uint8_t*)malloc(1280*720*4);
		fread(buffer, len, 1, bup);
		int sz = decompress_lz10(buffer, len, result);
		free(buffer);
		
		
		for (int j = 0; j < 1; j+=4) {
			SDL_LockSurface(surface);
			//dpcm(result, result, w, h, SCANLINE(w));
			blit(result+j, w, h, SCANLINE(w), (uint8_t*)surface->pixels, 0, 0, surface->pitch);
			SDL_UnlockSurface(surface);
			sprintf(name, "%s_%s_%d_%d.png", output, chunks[i].title, w, j);
			
			IMG_SavePNG(name, surface, IMAGE_COMPRESS_LEVEL);
		}
		free(result);
		
		printf("%s %d\n", name, sz);
		
		SDL_FreeSurface(surface);
	}*/
	
	
	free(chunks);
	free(offsets);
	
	return 0;
}

int process_pic_sui(FILE *pic, char *output)
{
	pic_header header;
	pic_chunk *chunks;
	sui_pack pack_hdr;
	
	fread(&header,sizeof(header),1,pic);
	
	fprintf(stderr,"%s left %d top %d width %d height %d calc %d\n", output, header.left, header.top, header.w, header.h, header.calc);
	
	chunks=(pic_chunk *)malloc(header.chunks*sizeof(pic_chunk));
	fread(chunks,sizeof(pic_chunk),header.chunks,pic);
	
	SDL_Surface *surface = SDL_CreateRGBSurface(SDL_SWSURFACE, header.w, header.h, 32, 0x000000FF, 0x0000FF00, 0x00FF0000, 0xFF000000);
	SDL_LockSurface(surface);
	
	for(int i=0;i<header.chunks;i++){
		fseek(pic,chunks[i].off,0);
		fread(&pack_hdr,sizeof(sui_pack),1,pic);
		
		uint8_t *buffer=(uint8_t *)malloc(pack_hdr.compressed_size);
		uint8_t *result=(uint8_t *)malloc(pack_hdr.decompressed_size);
		memset(result, 0, pack_hdr.decompressed_size);
		
		fseek(pic,chunks[i].off+sizeof(sui_pack),0);
		fread(buffer,pack_hdr.compressed_size,1,pic);
		
		
		if (pack_hdr.compressed_size == 0) {
			pack_hdr.compressed_size = chunks[i+1].off - chunks[i].off - sizeof(sui_pack);
		}
		
		size_t sz = decompress_lz10(buffer,pack_hdr.compressed_size,result);

		
		//printf("size: %4Xh, left: %4d, top: %4d, width: %4d (%4d), height: %4d (%4d), estimated: %d, dec: %lu\n",
		//	   pack_hdr.compressed_size, chunks[i].left, chunks[i].top,
		//	   pack_hdr.w, pack_hdr.width_m, pack_hdr.h, pack_hdr.height_m, pack_hdr.decompressed_size, sz);
		
		char oo[1024];
		sprintf(oo, "/Users/vit9696/Desktop/oo/oo_%d.bin", i);
		FILE *fh = fopen(oo, "wb");
		fwrite(result, sz, 1, fh);
		fclose(fh);
		
		//dpcm(result,result,pack_hdr.w,pack_hdr.h,SCANLINE(pack_hdr.w));
		blit(result,pack_hdr.w,pack_hdr.h,SCANLINE(pack_hdr.w),(uint8_t *)surface->pixels,chunks[i].left,chunks[i].top,surface->pitch);
		
		free(buffer);
		free(result);
		
		//break;
	}
	
	SDL_UnlockSurface(surface);
	IMG_SavePNG(output, surface, IMAGE_COMPRESS_LEVEL);
	SDL_FreeSurface(surface);
	

	return 0;
}

int process_txa_sui(FILE *txa, char *output)
{
	txa_header_any header;
	uint8_t *metadata,*p;
	uint8_t *data;
	uint8_t *buffer;
	int i;
	char str[0x100];
	SDL_Surface *surface;
	
	fread(&header,sizeof(header),1,txa);

	fseeko(txa, 0, SEEK_END);
	off_t filesize = ftello(txa);
	fseeko(txa, sizeof(header), SEEK_SET);

	if (header.hou.filesize == filesize) {
		txa_chunk_hou **chunks;
		size_t size = 0;
		int16_t curr_size = 0;
		for (i=0; i < header.hou.chunks; i++) {
			fseek(txa,sizeof(header)+size,SEEK_SET);
			fread(&curr_size,sizeof(int16_t),1,txa);
			size += curr_size;
		}

		metadata=(uint8_t *)malloc(size);
		chunks=(txa_chunk_hou **)malloc(header.hou.chunks*sizeof(txa_chunk_hou **));
		fseek(txa,sizeof(header),SEEK_SET);
		fread(metadata,size,1,txa);

		for(p=metadata,i=0;i<header.hou.chunks;i++){
			chunks[i]=(txa_chunk_hou *)p;
			p+=chunks[i]->length;
		}

		for(i=0;i<header.hou.chunks;i++){
			data=(uint8_t *)malloc(chunks[i]->decsize);
			buffer=(uint8_t *)malloc(chunks[i]->size);
			fseek(txa,chunks[i]->off,SEEK_SET);
			fread(buffer,chunks[i]->size,1,txa);
			decompress_lz10_sui(buffer,chunks[i]->size,data);
			free(buffer);

			uint32_t *palette = (uint32_t *)data;
			uint8_t *pixels = data + 256*4;

			sprintf(str,"%s_%s.png",output,chunks[i]->name);
			surface = SDL_CreateRGBSurface(SDL_SWSURFACE, chunks[i]->w,chunks[i]->h, 32, 0x000000FF, 0x0000FF00, 0x00FF0000, 0xFF000000);
			SDL_LockSurface(surface);

			uint32_t k = 0;
			for (uint32_t y = 0; y < chunks[i]->h; y++) {
				for (uint32_t x = 0; x < chunks[i]->w; x++) {
					putpixel(surface, x, y, palette[*(pixels+k)]);
					k++;
				}
			}

			SDL_UnlockSurface(surface);
			IMG_SavePNG(str, surface, IMAGE_COMPRESS_LEVEL);
			SDL_FreeSurface(surface);

			free(data);
		}
		free(metadata);
		free(chunks);
	} else if (header.sui.filesize == filesize) {
		txa_chunk **chunks;
		size_t size = 0;
		int16_t curr_size = 0;
		for (i=0; i < header.sui.chunks; i++) {
			fseek(txa,sizeof(header)+size,SEEK_SET);
			fread(&curr_size,sizeof(int16_t),1,txa);
			size += curr_size;
		}

		metadata=(uint8_t *)malloc(size);
		chunks=(txa_chunk **)malloc(header.sui.chunks*sizeof(txa_chunk **));
		fseek(txa,sizeof(header),SEEK_SET);
		fread(metadata,size,1,txa);

		for(p=metadata,i=0;i<header.sui.chunks;i++){
			chunks[i]=(txa_chunk *)p;
			p+=chunks[i]->length;
		}

		data=(uint8_t *)malloc(header.sui.decsize);

		for(i=0;i<header.sui.chunks;i++){
			buffer=(uint8_t *)malloc(chunks[i]->size);
			fseek(txa,chunks[i]->off,SEEK_SET);
			fread(buffer,chunks[i]->size,1,txa);
			decompress_lz10(buffer,chunks[i]->size,data);
			free(buffer);

			sprintf(str,"%s_%s.png",output,chunks[i]->name);
			surface = SDL_CreateRGBSurface(SDL_SWSURFACE, chunks[i]->w,chunks[i]->h, 32, 0x000000FF, 0x0000FF00, 0x00FF0000, 0xFF000000);
			SDL_LockSurface(surface);
			dpcm(data,data,chunks[i]->w,chunks[i]->h,SCANLINE(chunks[i]->w));
			blit(data,chunks[i]->w,chunks[i]->h,SCANLINE(chunks[i]->w),(uint8_t *)surface->pixels,0,0,surface->pitch);

			SDL_UnlockSurface(surface);
			IMG_SavePNG(str, surface, IMAGE_COMPRESS_LEVEL);
			SDL_FreeSurface(surface);
		}

		free(data);
		free(metadata);
		free(chunks);
	} else {
		return -1;
	}

	return 0;
}
