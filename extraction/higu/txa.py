import os

from bitstring import ConstBitStream
from common import *
from decompress import decompress
from tools import *

################################################################################

def convert_txa(filename, out_dir):
  data = ConstBitStream(filename = filename)
  
  out_template = os.path.join(out_dir, os.path.splitext(os.path.basename(filename))[0])
  try:
    os.makedirs(out_template)
  except:
    pass
  
  magic    = data.read("bytes:4")
  size     = data.read("uintle:32")
  indexed  = data.read("uintle:32")
  chunks   = data.read("uintle:32")
  dec_size = data.read("uintle:32")
  unk2     = data.read("uintle:32")
  unk3     = data.read("uintle:32")
  unk4     = data.read("uintle:32")
  
  print size, chunks, indexed
  
  for i in range(chunks):
    hdr_len   = data.read("uintle:16")
    index     = data.read("uintle:16")
    w         = data.read("uintle:16")
    h         = data.read("uintle:16")
    entry_off = data.read("uintle:32")
    entry_len = data.read("uintle:32")
    name      = data.read("bytes:%d" % (hdr_len - 16)).strip("\0")
    
    print name, index, w, h, entry_off, entry_len
    
    temp_pos  = data.bytepos
    data.bytepos = entry_off
    
    # If we are zero size, we're not compressed.
    if entry_len == 0:
      if indexed:
        size = 1024 + (w * h)
        chunk = bytearray(data.read(size * 8).bytes)
      else:
        print "???"
    
    else:
      chunk = data.read(entry_len * 8)
      chunk = decompress(chunk)
    
    data.bytepos = temp_pos
    
    w = adjust_w(w)
    if indexed:
      chunk, masked = get_indexed(chunk, w, h, crop = False)
    else:
      chunk = get_rgba(chunk, w, h, crop = False)
    chunk.save("%s/%s.png" % (out_template, name))
    # dump_to_file(chunk, "temp%d.dat" % index)

################################################################################