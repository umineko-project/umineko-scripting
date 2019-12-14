//
//  routine.c
//  AlchemistUnpacker
//

#include "main.h"

void putpixel(SDL_Surface *surface, int x, int y, Uint32 pixel)
{
	int bpp = surface->format->BytesPerPixel;
	Uint8 *p = (Uint8 *)surface->pixels + y * surface->pitch + x * bpp;
	
	switch(bpp) {
		case 1:
			*p = pixel;
			break;
			
		case 2:
			*(Uint16 *)p = pixel;
			break;
			
		case 3:
			if(SDL_BYTEORDER == SDL_BIG_ENDIAN) {
				p[0] = (pixel >> 16) & 0xff;
				p[1] = (pixel >> 8) & 0xff;
				p[2] = pixel & 0xff;
			} else {
				p[0] = pixel & 0xff;
				p[1] = (pixel >> 8) & 0xff;
				p[2] = (pixel >> 16) & 0xff;
			}
			break;
			
		case 4:
			*(Uint32 *)p = pixel;
			break;
	}
}

int IMG_SavePNG(const char *file, SDL_Surface *surf,int compression){
	SDL_RWops *fp;
	int ret;
	
	fp=SDL_RWFromFile(file,"wb");
    
	if( fp == NULL ) {
		return (-1);
	}
    
	ret=IMG_SavePNG_RW(fp,surf,compression);
	SDL_RWclose(fp);
	return ret;
}

static void png_write_data(png_structp png_ptr,png_bytep data, png_size_t length){
	SDL_RWops *rp = (SDL_RWops*) png_get_io_ptr(png_ptr);
	SDL_RWwrite(rp,data,1,(int)length);
}

int IMG_SavePNG_RW(SDL_RWops *src, SDL_Surface *surf,int compression){
	png_structp png_ptr;
	png_infop info_ptr;
	SDL_PixelFormat *fmt=NULL;
	SDL_Surface *tempsurf=NULL;
	int ret,funky_format,used_alpha;
	unsigned int i,temp_alpha;
	png_colorp palette;
	Uint8 *palette_alpha=NULL;
	png_byte **row_pointers=NULL;
	png_ptr=NULL;info_ptr=NULL;palette=NULL;ret=-1;
	funky_format=0;
	
	if( !src || !surf) {
		goto savedone; /* Nothing to do. */
	}
    
	row_pointers=(png_byte **)malloc(surf->h * sizeof(png_byte*));
	if (!row_pointers) { 
		SDL_SetError("Couldn't allocate memory for rowpointers");
		goto savedone;
	}
	
	png_ptr=png_create_write_struct(PNG_LIBPNG_VER_STRING, NULL,NULL,NULL);
	if (!png_ptr){
		SDL_SetError("Couldn't allocate memory for PNG file");
		goto savedone;
	}
	info_ptr= png_create_info_struct(png_ptr);
	if (!info_ptr){
		SDL_SetError("Couldn't allocate image information for PNG file");
		goto savedone;
	}
	/* setup custom writer functions */
	png_set_write_fn(png_ptr,(png_voidp)src,png_write_data,NULL);
    
	if (setjmp(png_jmpbuf(png_ptr))){
		SDL_SetError("Unknown error writing PNG");
		goto savedone;
	}
    
	if(compression>Z_BEST_COMPRESSION)
		compression=Z_BEST_COMPRESSION;
    
	if(compression == Z_NO_COMPRESSION) // No compression
	{
		png_set_filter(png_ptr,0,PNG_FILTER_NONE);
		png_set_compression_level(png_ptr,Z_NO_COMPRESSION);
	}
    else if(compression<0) // Default compression
		png_set_compression_level(png_ptr,Z_DEFAULT_COMPRESSION);
    else
		png_set_compression_level(png_ptr,compression);
    
	fmt=surf->format;
	if(fmt->BitsPerPixel==8){ /* Paletted */
		png_set_IHDR(png_ptr,info_ptr,
                     surf->w,surf->h,8,PNG_COLOR_TYPE_PALETTE,
                     PNG_INTERLACE_NONE,PNG_COMPRESSION_TYPE_DEFAULT,
                     PNG_FILTER_TYPE_DEFAULT);
		palette=(png_colorp) malloc(fmt->palette->ncolors * sizeof(png_color));
		if (!palette) {
			SDL_SetError("Couldn't create memory for palette");
			goto savedone;
		}
		for (i=0;i<fmt->palette->ncolors;i++) {
			palette[i].red=fmt->palette->colors[i].r;
			palette[i].green=fmt->palette->colors[i].g;
			palette[i].blue=fmt->palette->colors[i].b;
		}
		png_set_PLTE(png_ptr,info_ptr,palette,fmt->palette->ncolors);
		if (surf->flags&SDL_SRCCOLORKEY) {
			palette_alpha=(Uint8 *)malloc((fmt->colorkey+1)*sizeof(Uint8));
			if (!palette_alpha) {
				SDL_SetError("Couldn't create memory for palette transparency");
				goto savedone;
			}
			/* FIXME: memset? */
			for (i=0;i<(fmt->colorkey+1);i++) {
				palette_alpha[i]=255;
			}
			palette_alpha[fmt->colorkey]=0;
			png_set_tRNS(png_ptr,info_ptr,palette_alpha,fmt->colorkey+1,NULL);
		}
	}else{ /* Truecolor */
		if (fmt->Amask) {
			png_set_IHDR(png_ptr,info_ptr,
                         surf->w,surf->h,8,PNG_COLOR_TYPE_RGB_ALPHA,
                         PNG_INTERLACE_NONE,PNG_COMPRESSION_TYPE_DEFAULT,
                         PNG_FILTER_TYPE_DEFAULT);
		} else {
			png_set_IHDR(png_ptr,info_ptr,
                         surf->w,surf->h,8,PNG_COLOR_TYPE_RGB,
                         PNG_INTERLACE_NONE,PNG_COMPRESSION_TYPE_DEFAULT,
                         PNG_FILTER_TYPE_DEFAULT);
		}
	}
	png_write_info(png_ptr, info_ptr);
    
	if (fmt->BitsPerPixel==8) { /* Paletted */
		for(i=0;i<surf->h;i++){
			row_pointers[i]= ((png_byte*)surf->pixels) + i*surf->pitch;
		}
		if(SDL_MUSTLOCK(surf)){
			SDL_LockSurface(surf);
		}
		png_write_image(png_ptr, row_pointers);
		if(SDL_MUSTLOCK(surf)){
			SDL_UnlockSurface(surf);
		}
	}else{ /* Truecolor */
		if(fmt->BytesPerPixel==3){
			if(fmt->Amask){ /* check for 24 bit with alpha */
				funky_format=1;
			}else{
				/* Check for RGB/BGR/GBR/RBG/etc surfaces.*/
#if SDL_BYTEORDER == SDL_BIG_ENDIAN
				if(fmt->Rmask!=0xFF0000 
                   || fmt->Gmask!=0x00FF00
                   || fmt->Bmask!=0x0000FF){
#else
                    if(fmt->Rmask!=0x0000FF 
                       || fmt->Gmask!=0x00FF00
                       || fmt->Bmask!=0xFF0000){
#endif
                        funky_format=1;
                    }
                }
            }else if (fmt->BytesPerPixel==4){
                if (!fmt->Amask) { /* check for 32bit but no alpha */
                    funky_format=1; 
                }else{
                    /* Check for ARGB/ABGR/GBAR/RABG/etc surfaces.*/
#if SDL_BYTEORDER == SDL_BIG_ENDIAN
                    if(fmt->Rmask!=0xFF000000
                       || fmt->Gmask!=0x00FF0000
                       || fmt->Bmask!=0x0000FF00
                       || fmt->Amask!=0x000000FF){
#else
                        if(fmt->Rmask!=0x000000FF
                           || fmt->Gmask!=0x0000FF00
                           || fmt->Bmask!=0x00FF0000
                           || fmt->Amask!=0xFF000000){
#endif
                            funky_format=1;
                        }
                    }
                }else{ /* 555 or 565 16 bit color */
                    funky_format=1;
                }
                if (funky_format) {
                    /* Allocate non-funky format, and copy pixeldata in*/
                    if(fmt->Amask){
#if SDL_BYTEORDER == SDL_BIG_ENDIAN
                        tempsurf = SDL_CreateRGBSurface(SDL_SWSURFACE, surf->w, surf->h, 24,
                                                        0xff000000, 0x00ff0000, 0x0000ff00, 0x000000ff);
#else
                        tempsurf = SDL_CreateRGBSurface(SDL_SWSURFACE, surf->w, surf->h, 24,
                                                        0x000000ff, 0x0000ff00, 0x00ff0000, 0xff000000);
#endif
                    }else{
#if SDL_BYTEORDER == SDL_BIG_ENDIAN
                        tempsurf = SDL_CreateRGBSurface(SDL_SWSURFACE, surf->w, surf->h, 24,
                                                        0xff0000, 0x00ff00, 0x0000ff, 0x00000000);
#else
                        tempsurf = SDL_CreateRGBSurface(SDL_SWSURFACE, surf->w, surf->h, 24,
                                                        0x000000ff, 0x0000ff00, 0x00ff0000, 0x00000000);
#endif
                    }
                    if(!tempsurf){
                        SDL_SetError("Couldn't allocate temp surface");
                        goto savedone;
                    }
                    if(surf->flags&SDL_SRCALPHA){
                        temp_alpha=fmt->alpha;
                        used_alpha=1;
                        SDL_SetAlpha(surf,0,255); /* Set for an opaque blit */
                    }else{
                        used_alpha=0;
                    }
                    if(SDL_BlitSurface(surf,NULL,tempsurf,NULL)!=0){
                        SDL_SetError("Couldn't blit surface to temp surface");
                        SDL_FreeSurface(tempsurf);
                        goto savedone;
                    }
                    if (used_alpha) {
                        SDL_SetAlpha(surf,SDL_SRCALPHA,(Uint8)temp_alpha); /* Restore alpha settings*/
                    }
                    for(i=0;i<tempsurf->h;i++){
                        row_pointers[i]= ((png_byte*)tempsurf->pixels) + i*tempsurf->pitch;
                    }
                    if(SDL_MUSTLOCK(tempsurf)){
                        SDL_LockSurface(tempsurf);
                    }
                    png_write_image(png_ptr, row_pointers);
                    if(SDL_MUSTLOCK(tempsurf)){
                        SDL_UnlockSurface(tempsurf);
                    }
                    SDL_FreeSurface(tempsurf);
                } else {
                    for(i=0;i<surf->h;i++){
                        row_pointers[i]= ((png_byte*)surf->pixels) + i*surf->pitch;
                    }
                    if(SDL_MUSTLOCK(surf)){
                        SDL_LockSurface(surf);
                    }
                    png_write_image(png_ptr, row_pointers);
                    if(SDL_MUSTLOCK(surf)){
                        SDL_UnlockSurface(surf);
                    }
                }
            }
            
            png_write_end(png_ptr, NULL);
            ret=0; /* got here, so nothing went wrong. YAY! */
            
        savedone: /* clean up and return */
            png_destroy_write_struct(&png_ptr,&info_ptr);
            if (palette) {
                free(palette);
            }
            if (palette_alpha) {
                free(palette_alpha);
            }
            if (row_pointers) {
                free(row_pointers);
            }
            return ret;
        }