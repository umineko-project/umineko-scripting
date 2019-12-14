def dump_to_file(data, filename = "temp.dat"):
  with open(filename, "wb") as f:
    f.write(data)

def dump_bs_to_file(data, filename = "temp.dat"):
  with open(filename, "wb") as f:
    data.tofile(f)