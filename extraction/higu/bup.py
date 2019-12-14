import os

from bitstring import ConstBitStream
from common import process_chunk

from PyQt4 import QtGui
from PyQt4.QtGui import QImage, qRgba, qAlpha, qRed, qGreen, qBlue

# A color we can draw over no matter what, distinct from 0x00000000.
# I haven't checked to see if this is actually used by any images, but hot pink seems unlikely.
TRANSPARENT_COLOR = 0x00FF0080

################################################################################

def blit(src, dst, x, y, masked):
  w = src.width()
  h = src.height()
  
  out = dst.copy()
  for i in range(w):
    for j in range(h):
      dst_pixel = dst.pixel(x + i, y + j)
      src_pixel = src.pixel(i, j)
      
      # If src has transparency data, we use it, otherwise, we borrow from dst.
      # This logic doesn't quite work for bustup/l/si4?
      if masked or dst_pixel == TRANSPARENT_COLOR:
        out_pixel = src_pixel
      else:
        out_pixel = qRgba(qRed(src_pixel), qGreen(src_pixel), qBlue(src_pixel), qAlpha(dst_pixel))
      
      out.setPixel(x + i, y + j, out_pixel)
  
  return out

################################################################################

def convert_bup(filename, out_dir):
  data = ConstBitStream(filename = filename)
  
  out_template = os.path.join(out_dir, os.path.splitext(os.path.basename(filename))[0])
  
  magic   = data.read("bytes:4")
  size    = data.read("uintle:32")
  ew      = data.read("uintle:16")
  eh      = data.read("uintle:16")
  width   = data.read("uintle:16")
  height  = data.read("uintle:16")
  
  tbl1    = data.read("uintle:32")
  base_chunks = data.read("uintle:32")
  exp_chunks  = data.read("uintle:32")
  
  print width, height
  
  # Dunno what this is for.
  for i in range(tbl1):
    data.read(32)
  
  base = QImage(width, height, QImage.Format_ARGB32)
  # base.fill(0)
  base.fill(TRANSPARENT_COLOR)
  
  for i in range(base_chunks):
    offset = data.read("uintle:32")
    chunk, x, y, masked = process_chunk(data, offset)
    base = blit(chunk, base, x, y, masked)
  
  # base.save("%s.png" % (out_template))
  # return
  
  for i in range(exp_chunks):
    name       = data.read("bytes:16").strip("\0").decode("CP932")
    face_off   = data.read("uintle:32")
    unk1       = data.read("uintle:32")
    unk2       = data.read("uintle:32")
    unk3       = data.read("uintle:32")
    mouth1_off = data.read("uintle:32")
    mouth2_off = data.read("uintle:32")
    mouth3_off = data.read("uintle:32")
    
    if not face_off:
      base.save("%s_%s.png" % (out_template, name))
      # base.save("%s.png" % (out_template))
      # return
      continue
    
    face, x, y, masked = process_chunk(data, face_off)
    exp_base = blit(face, base, x, y, masked)
    
    # exp_base.save("test/%s.png" % name)
    # exp_base.save("%s_%s.png" % (out_template, name))
    # face.save("%s_%sf.png" % (out_template, name))
    # break
    
    for j, mouth_off in enumerate([mouth1_off, mouth2_off, mouth3_off]):
      if not mouth_off:
        continue
      
      mouth, x, y, masked = process_chunk(data, mouth_off)
      
      if not mouth:
        continue
      
      exp = blit(mouth, exp_base, x, y, masked)
      
      exp.save("%s_%s_%d.png" % (out_template, name, j))

################################################################################
