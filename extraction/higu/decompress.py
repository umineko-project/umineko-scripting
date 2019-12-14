from bitstring import ConstBitStream
from tools import *

################################################################################

def decompress_umi(data):
  marker = 1
  res = bytearray()
  
  while data.pos < data.len:
    # dump_to_file(res)
    # 0b00001 111 00000000
    if marker == 1:
      print "Marker: 0b%s 0x%s" % (data.peek(8).bin, data.peek(8).hex)
      marker = 0x100 | data.read("uintle:8")
    
    if marker & 1:
      print "     V: 0b" + data.peek(16).bin
      count  = data.read("uint:5")
      offset = data.read("uint:11")
      
      if count & 0b10000:
        data.bytepos -= 2
        flag = data.read("uint:1")
        count = data.read("uint:10")
        offset = data.read("uint:5")
      else:
        offset += 32
      
      count = count + 3
      
      # pos = len(res) - (offset + 1)
      pos = len(res) - (offset + 1) * 4
      print "%6d:" % data.bytepos, "%6d" % len(res), count, offset
      for i in range(count):
        res.append(res[pos])
        pos += 1
    
    else:
      res.append(data.read("uintle:8"))
      print "  Byte: 0x%02X" % res[-1]
    
    marker >>= 1
    # print "Marker:", bin(marker)
  
  return res

################################################################################
# Basically LZ10?
def decompress_higu(data):
  data    = bytearray(data.bytes)
  marker  = 1
  res     = bytearray()
  p       = 0
  
  while p < len(data):
    if marker == 1:
      # print "Marker:", bin(data[p]), hex(data[p])
      marker = 0x100 | data[p]
      p += 1
    
    if p >= len(data):
      break
    
    if marker & 1:
      # print "     V:", bin((data[p] << 8) | data[p + 1])
      b1 = data[p]
      b2 = data[p + 1]
      p += 2
      
      count  = (b1 & 0b00001111) + 3
      offset = ((b1 & 0b11110000) << 4) | b2
      
      for i in range(count):
        res.append(res[-(offset + 1)])
    
    else:
      # print "  Byte: 0x%02X" % data[p]
      res.append(data[p])
      p += 1
    
    marker >>= 1
  
  return res

################################################################################

def decompress(data):
  return decompress_higu(data)

################################################################################