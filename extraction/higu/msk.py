from bitstring import ConstBitStream
from common import *
from decompress import decompress
from tools import *

################################################################################

def convert_msk(filename, out_file):
  data = ConstBitStream(filename = filename)
  
  magic    = data.read("bytes:4")
  size     = data.read("uintle:32")
  w        = data.read("uintle:16")
  h        = data.read("uintle:16")
  cmp_size = data.read("uintle:32")
  
  print w, h, cmp_size
  w = adjust_w(w)
  chunk = data.read(cmp_size * 8)
  chunk = decompress(chunk)
  dump_to_file(chunk)
  chunk = get_grayscale(chunk, w, h, crop = False)
  chunk.save(out_file)

################################################################################