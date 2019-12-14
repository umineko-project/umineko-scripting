//
//  umi.cpp
//  AlchemistUnpacker
//

#include "main.h"
#include "umi.h"

int process_msk_umi(FILE *msk, char *output)
{
	msk_header header;
	uint8_t *buffer, *result;
	
	fread(&header, sizeof(msk_header), 1, msk);
	
	buffer = (uint8_t *)malloc(header.size);
	result = (uint8_t *)malloc(header.w*header.h*2); //FIXME: size needs to be calculated carefully.
	
	fread(buffer, header.size, 1, msk);
	size_t out_size = decompress_lz10(buffer, header.size, result);
	free(buffer);

	size_t src_w = out_size / header.h;

	SDL_Surface *surface = SDL_CreateRGBSurface(SDL_SWSURFACE, header.w, header.h, 8, 0xFF, 0xFF, 0xFF, 0);
	SDL_LockSurface(surface);
	
	for (int y = 0; y < header.h; y++) {
		for (int x = 0; x < header.w; x++) {
			putpixel(surface, x, y, result[y*src_w+x]);
		}
	}
	
	free(result);
	
	SDL_UnlockSurface(surface);

	fprintf( stderr,"%s width %d height %d\n", output, header.w, header.h);
	IMG_SavePNG(output, surface, IMAGE_COMPRESS_LEVEL);
	SDL_FreeSurface(surface);
	
	return 0;
}

int process_bup_umi(FILE *bup, char *output)
{
	bup_header header;
	bup_chunk *chunks;
	uint8_t *data;
	uint8_t *buffer;
	int i;
	SDL_Surface *surface, *base_surface;
	
	fread(&header,sizeof(header),1 ,bup);
		
	chunks=(bup_chunk *)calloc(header.chunks,sizeof(bup_chunk));
	fread(chunks,sizeof(bup_chunk),header.chunks ,bup);
	
	data=(uint8_t *)calloc(1,SCANLINE(header.w)*header.h*4);
	
	buffer=(uint8_t *)malloc(header.size);
	fseek(bup,header.off,SEEK_SET);
	fread(buffer,header.size,1 ,bup);
	
	decompress_lzss(buffer,header.size,data);
	dpcm(data,data,header.w,header.h,SCANLINE(header.w));
	
	free(buffer);
	
	base_surface = SDL_CreateRGBSurface(SDL_SWSURFACE, header.w, header.h, 32, 0x000000FF, 0x0000FF00, 0x00FF0000, 0xFF000000);
	
	SDL_LockSurface(base_surface);
	blend(data,header.w,header.h,SCANLINE(header.w),(uint8_t *)base_surface->pixels,0,0,base_surface->pitch);
	SDL_UnlockSurface(base_surface);
	free(data);
	
	for(i=0;i<header.chunks;i++){
		bup_chunk *chunk=chunks+i;
		char name[PATH_MAX];
		char *utfname = sjisToUnicode(chunk->title,16);
		uint8_t *xdata;
		
		if(chunk->pic[1].w>0) {
			surface = SDL_CreateRGBSurface(SDL_SWSURFACE, chunk->pic[1].w, chunk->pic[1].h, 32, 0x000000FF, 0x0000FF00, 0x00FF0000, 0xFF000000);
			SDL_LockSurface(surface);
			xdata=(uint8_t *)malloc(SCANLINE(chunk->pic[1].w)*chunk->pic[1].h);
			buffer=(uint8_t *)malloc(chunk->pic[1].size);
			
			fseek(bup,chunk->pic[1].off,SEEK_SET);
			fread(buffer,chunk->pic[1].size,1 ,bup);
			
			decompress_lzss(buffer,chunk->pic[1].size,xdata);
			dpcm(xdata,xdata,chunk->pic[1].w,chunk->pic[1].h,SCANLINE(chunk->pic[1].w));
			
			
			sprintf(name,"%s%s-M.png",output,utfname);
			
			blend(xdata,chunk->pic[1].w,chunk->pic[1].h,SCANLINE(chunk->pic[1].w),(uint8_t *)surface->pixels,0,0,surface->pitch);
			
			SDL_UnlockSurface(surface);
			
			//Build proper lips
			SDL_Surface *surface2 = SDL_CreateRGBSurface(SDL_SWSURFACE, chunk->pic[1].w*4, chunk->pic[1].h/3, 32, 0x000000FF, 0x0000FF00, 0x00FF0000, 0xFF000000);
			SDL_SetAlpha( surface, 0, SDL_ALPHA_OPAQUE );
			SDL_BlitSurface(surface, NULL, surface2, NULL);

			SDL_FillRect(surface2, nullptr, 0x00000000);
			SDL_Rect src_rect = { 0, 0, chunk->pic[1].w, (Uint16)(chunk->pic[1].h/3) };
			SDL_Rect dst_rect = { 0, 0, 0, 0 };
			
			SDL_BlitSurface(surface, &src_rect, surface2, &dst_rect);
			src_rect.y = chunk->pic[1].h/3;
			dst_rect = (SDL_Rect){ (short)chunk->pic[1].w, 0, chunk->pic[1].w, (Uint16)(chunk->pic[1].h/3) };
			SDL_BlitSurface(surface, &src_rect, surface2, &dst_rect);
			src_rect.y = (chunk->pic[1].h/3)*2;
			dst_rect = (SDL_Rect){ (short)(chunk->pic[1].w*2), 0, chunk->pic[1].w, (Uint16)(chunk->pic[1].h/3) };
			SDL_BlitSurface(surface, &src_rect, surface2, &dst_rect);
			
			IMG_SavePNG(name, surface2, IMAGE_COMPRESS_LEVEL);
			
			SDL_FreeSurface(surface);
			SDL_FreeSurface(surface2);
			free(buffer);
			free(xdata);
		}
		
		surface = SDL_ConvertSurface(base_surface, base_surface->format, SDL_SWSURFACE);
		SDL_LockSurface(surface);
		
		if(chunk->pic[0].w>0) { 
			xdata=(uint8_t *)malloc(SCANLINE(chunk->pic[0].w)*chunk->pic[0].h);
			buffer=(uint8_t *)malloc(chunk->pic[0].size);
			
			fseek(bup,chunk->pic[0].off,SEEK_SET);
			fread(buffer,chunk->pic[0].size,1,bup);
			
			decompress_lzss(buffer,chunk->pic[0].size,xdata);
			dpcm(xdata,xdata,chunk->pic[0].w,chunk->pic[0].h,SCANLINE(chunk->pic[0].w));
			blend(xdata,chunk->pic[0].w,chunk->pic[0].h,SCANLINE(chunk->pic[0].w),(uint8_t *)surface->pixels,chunk->pic[0].left,chunk->pic[0].top,surface->pitch);
			
			free(buffer);
			free(xdata);
		}
		
		sprintf(name,"%s%s.png",output,utfname);
		
		SDL_UnlockSurface(surface);
		IMG_SavePNG(name, surface, IMAGE_COMPRESS_LEVEL);
		if(chunk->pic[1].w>0) {
			fprintf(stderr,"%s left %d top %d width %d height %d char %d lips_x %d lips_y %d\n",
							name,header.left,header.top,header.w, header.h, header.char_id, chunk->pic[1].left, chunk->pic[1].top);
		} else {
			fprintf(stderr,"%s left %d top %d width %d height %d char %d\n",name,header.left,header.top,header.w, header.h, header.char_id);
		}
		SDL_FreeSurface(surface);
	}
	
	return 0;
}

int process_pic_umi(FILE *pic, char *output)
{
	pic_header header;
	pic_chunk *chunks;
	int i;
	
	fread(&header,sizeof(header),1,pic);
	
	fprintf(stderr,"%s left %d top %d width %d height %d\n", output, header.left, header.top, header.w, header.h);
	
	chunks=(pic_chunk *)calloc(header.chunks,sizeof(pic_chunk));
	fread(chunks,sizeof(pic_chunk),header.chunks,pic);
	
	SDL_Surface *surface = SDL_CreateRGBSurface(SDL_SWSURFACE, header.w, header.h, 32, 0x000000FF, 0x0000FF00, 0x00FF0000, 0xFF000000);
	SDL_LockSurface(surface);
	
	for(i=0;i<header.chunks;i++){
		pic_chunk *chunk=chunks+i;
		
		int size=chunk->size;
		uint8_t *result=(uint8_t *)malloc(chunk->h*SCANLINE(chunk->w));
		
		fseek(pic,chunk->off,SEEK_SET);
		
		if (size!=0) {
			uint8_t *buffer=(uint8_t *)malloc(size);
			fread(buffer,size,1,pic);
			decompress_lzss(buffer,size,result);
			dpcm(result,result,chunk->w,chunk->h,SCANLINE(chunk->w));
			blit(result,chunk->w,chunk->h,SCANLINE(chunk->w),(uint8_t *)surface->pixels,chunk->left,chunk->top,surface->pitch);
			free(buffer);
		} else {
			size = chunk->h*SCANLINE(chunk->w);
			fread(result,size,1,pic);
			dpcm(result,result,chunk->w,chunk->h,SCANLINE(chunk->w));
			blit(result,chunk->w,chunk->h,SCANLINE(chunk->w),(uint8_t *)surface->pixels,chunk->left,chunk->top,surface->pitch);
		}
		
		free(result);
	}
	
	SDL_UnlockSurface(surface);
	IMG_SavePNG(output, surface, IMAGE_COMPRESS_LEVEL);
	SDL_FreeSurface(surface);
	
	return 0;
}

int process_txa_umi(FILE *txa,char *output)
{
	txa_header header;
	txa_chunk **chunks;
	uint8_t *metadata,*p;
	uint8_t *data;
	uint8_t *buffer;
	int i;
	char str[PATH_MAX];
	SDL_Surface *surface;
	
	fread(&header,sizeof(header),1,txa);
	
	metadata=(uint8_t *)malloc(header.off-sizeof(header));
	chunks=(txa_chunk **)malloc(header.chunks*sizeof(txa_chunk **));
	fread(metadata,header.off-sizeof(header),1,txa);
	
	for(p=metadata,i=0;i<header.chunks;i++){
		chunks[i]=(txa_chunk *)p;
		p+=chunks[i]->length;
	}
	
	data=(uint8_t *)malloc(header.decsize);
	buffer=(uint8_t *)malloc(header.encsize);
	fseek(txa,header.off,SEEK_SET);
	fread(buffer,header.encsize,1,txa);
	decompress_lzss(buffer,header.encsize,data);
	free(buffer);
	
	for(i=0;i<header.chunks;i++){
		sprintf(str,"%s_%s.png",output,chunks[i]->name);
		surface = SDL_CreateRGBSurface(SDL_SWSURFACE, chunks[i]->w,chunks[i]->h, 32, 0x000000FF, 0x0000FF00, 0x00FF0000, 0xFF000000);
		SDL_LockSurface(surface);
		blit(data+chunks[i]->off,chunks[i]->w,chunks[i]->h,chunks[i]->scanline,(uint8_t *)surface->pixels,0,0,surface->pitch);
		SDL_UnlockSurface(surface);
		IMG_SavePNG(str, surface, IMAGE_COMPRESS_LEVEL);
		SDL_FreeSurface(surface);
	}
	
	free(data);
	free(metadata);
	free(chunks);
	
	return 0;
}

int process_rom_umi(FILE *rom, char *output)
{
	if (sizeof(off_t) <= 4) {
		fprintf(stderr,"64-bit seeking is not supported\n");
		return 1;
	}
	
	makedir(output);
	unpack_umi(rom, 0x10, output);
	
	return 0;
}

int unpack_umi(FILE *rom, off_t offset, char *output, int add, int offfile)
{
	pck_chunk *chunks;
	unsigned int count,i;
	
	fseeko(rom,offset,SEEK_SET);
	fread(&count,sizeof(count),1,rom);
	
	chunks=(pck_chunk *)malloc(count*sizeof(pck_chunk));
	fread(chunks,sizeof(pck_chunk),count,rom);
	
	for(i=0;i<count;i++){
		char s[0x400],name[0x100];
		int isdir;
		off_t off;
		int size;
		
		pck_chunk *chunk=&chunks[i];
		size=chunk->size;
		off=chunk->off;
		
		isdir=chunk->nameoff&0x80000000;
		chunk->nameoff&=~0x80000000;
		off<<=isdir?4:offfile;
		if(isdir)off+=add;
		
		fseeko(rom,offset+chunk->nameoff,SEEK_SET);
		fread(name,1,0x100,rom);
		
		if((name[0]=='.' && name[1]=='\0') || (name[0]=='.' && name[1]=='.' && name[2]=='\0'))
			continue;
		
		sprintf(s,"%s/%s",output,name);
		
		if(isdir){
			makedir(s);
			unpack_umi(rom,off,s,add,offfile);
		} else{
			FILE *f = fopen(s,"wb");
			
			if (!f) {
				fprintf(stderr,"Couldn't open file %s for writing.\n",s);
				return 1;
			}
			
			fseeko(rom,off,SEEK_SET);
			
			while(size>0){
				char buffer[0x400];
				int c=size>0x400?0x400:size;
				
				fread(buffer,c,1,rom);
				fwrite(buffer,c,1,f);
				
				size-=c;
			}
			
			fclose(f);
		}
	}
	
	return 0;
}
