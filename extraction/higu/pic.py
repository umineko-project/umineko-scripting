from bitstring import ConstBitStream
from common import *
from decompress import decompress_umi

from PyQt4 import QtGui
from PyQt4.QtGui import QImage, QPainter
from PyQt4.QtCore import QRectF

################################################################################

def convert_pic(filename, out_file):
  data = ConstBitStream(filename = filename)
  
  magic   = data.read("bytes:4")
  size    = data.read("uintle:32")
  ew      = data.read("uintle:16")
  eh      = data.read("uintle:16")
  width   = data.read("uintle:16")
  height  = data.read("uintle:16")
  
  unk1    = data.read("uintle:32")
  chunks  = data.read("uintle:32")
  
  print "EW, EH:    ", ew, eh
  print "Width:     ", width
  print "Height:    ", height
  print "Unk:       ", unk1
  print
  
  image = QImage(width, height, QImage.Format_ARGB32)
  image.fill(0)
  painter = QPainter(image)
  
  for i in range(chunks):
    x = data.read("uintle:16")
    y = data.read("uintle:16")
    offset = data.read("uintle:32")
    
    # if not i == chunks - 1:
      # continue
    
    print "X, Y:      ", i, x, y
    # print
    chunk, shift_x, shift_y, masked = process_chunk(data, offset)
    if not chunk:
      continue
    
    painter.drawImage(x, y, chunk)
    
  painter.end()
  image.save(out_file)

################################################################################

def convert_pic_umi(filename, out_file):
  data = ConstBitStream(filename = filename)
  
  magic   = data.read("bytes:4")
  size    = data.read("uintle:32")
  ew      = data.read("uintle:16")
  eh      = data.read("uintle:16")
  width   = data.read("uintle:16")
  height  = data.read("uintle:16")
  
  unk1    = data.read("uintle:32")
  chunks  = data.read("uintle:32")
  
  image = QImage(width, height, QImage.Format_ARGB32)
  image.fill(0)
  painter = QPainter(image)
  
  for i in range(chunks):
    version = data.read("uintle:32")
    x = data.read("uintle:16")
    y = data.read("uintle:16")
    w = data.read("uintle:16")
    h = data.read("uintle:16")
    
    offset = data.read("uintle:32")
    size = data.read("uintle:32")
    
    # if not i == 1:
      # continue
  
    print w, h, size, offset
    
    temp_pos = data.bytepos
    data.bytepos = offset
    chunk = data.read(size * 8)
    data.bytepos = temp_pos
    
    chunk = decompress_umi(chunk)
    chunk = adjust_scanline(chunk, w, h)
    dump_to_file(chunk)
    chunk = QImage(chunk, w, h, QImage.Format_ARGB32)
    chunk.fill(0)
    # chunk.save("test.bmp")
    painter.drawImage(QRectF(x, y, w, h), chunk, QRectF((chunk.rect())))
    # break
    
  painter.end()
  image.save(out_file)

################################################################################