//
//  snr.h
//  AlchemistUnpacker
//

#ifndef AlchemistUnpacker_snr_cmd_h
#define AlchemistUnpacker_snr_cmd_h

#include <unordered_map>
#include <iostream>
#include <fstream>
#include <vector>
#include <initializer_list>
#include <cstdint>
#include <cstdlib>
#include <string>

#include "main.h"

// address buffers for GOTO/GOSUB and similar functions
// on decoding step we use [LABEL_ADDRESS] = LABEL_NUMBER (to add a LABEL NUM line later)
// since we have to jump backwards we have to parse script twice on decoding to catch all the labels first
extern std::unordered_map<uint32_t, uint32_t> labels;
// on encoding step we use [LABEL_NUMBER] = { LABEL_ADDRESS, DST_ADDRESSES } (to add an ADDRESS to the CALLER once a label is hit)
struct label_enc {
	uint32_t addr {0};
	std::vector<uint32_t> toAdd;
};
extern std::unordered_map<uint32_t, label_enc> labels_enc;
extern uint32_t label_count;

struct temps {
	uint8_t t8 {0};
	uint16_t t16 {0};
	uint32_t t32 {0};
	int8_t t8s {0};
	int16_t t16s {0};
	int32_t t32s {0};
	std::string str;
};

enum class ARG_TYPE {
	STRING,
	ADDRESS,
	UCHAR,
	USHORT,
	UINTEGER,
	CHAR,
	SHORT,
	INTEGER,
	REGISTER
};

enum class SNRVer {
	RONDO,
	CHIRU,
	UNKNOWN = -1
};

class CMD_ARG {
public:
	CMD_ARG(ARG_TYPE t, int s=0) {
		type = t;
		size = s;
	}
	int size;
	int64_t value;
	ARG_TYPE type;
};

class CMD {
public:
	std::vector<CMD_ARG> args;
	unsigned char cmd_no {0};
	std::string cmd_name;
	
	CMD () {
		throw "Undefined command";
	}
	
	CMD (char no, std::string name, std::initializer_list<CMD_ARG> list) {
		cmd_no = no;
		cmd_name = name;
		args = list;
	}
	
	inline int getSize (CMD_ARG &arg, int i) {
		if (arg.size > 0) {
			return arg.size;
		} else if (arg.size == 0 && arg.type != ARG_TYPE::STRING) {
			return 1;
		} else if (arg.size < 0) {
			return static_cast<int>(args[i+arg.size].value);
		} else {
			return 0;
		}
	}
	
	virtual void decodeCmd (std::istream &input, std::ostream &output, SNRVer ver, int output_data=1);
	virtual void encodeCmd (std::istream &input, std::ostream &output, SNRVer ver, int output_data=1);
	
protected:
	template <typename T>
	T emit(std::istream& input, std::ostream& output, int output_data) {
		return emit_as<T, T>(input, output, output_data);
	}
	
	// Read sizeof(T) bytes of assembly, emit to disassembly formatted as S
	template <typename T, typename S>
	T emit_as(std::istream& input, std::ostream& output, int output_data) {
		T tmp;
		input.read(reinterpret_cast<char *>(&tmp), sizeof(tmp));
		if (output_data)
			output << '\t' << (S)tmp;
		return tmp;
	}
	
	template <typename T>
	T consume(std::istream& input, std::ostream& output, int output_data) {
		return consume_as<T, T>(input, output, output_data);
	}
	
	// Consume input formatted as S from disassembly, write sizeof(T) bytes
	template <typename T, typename S>
	T consume_as(std::istream& input, std::ostream& output, int output_data) {
		T tmp = 0;
		input >> tmp;
		auto as = static_cast<S>(tmp);
		if (output_data)
			output.write(reinterpret_cast<char *>(&as), sizeof(as));
		return tmp;
	}
};

class CMD_ADDR : public CMD {
public:
	CMD_ADDR (char no, std::string name, std::initializer_list<CMD_ARG> list) : CMD (no, name, list) {
		
	}
	
	void decodeCmd (std::istream &input, std::ostream &output, SNRVer ver, int output_data=1);
	void encodeCmd (std::istream &input, std::ostream &output, SNRVer ver, int output_data=1);
};

class CMD_VAR : public CMD {
public:
	CMD_VAR (char no, std::string name, std::initializer_list<CMD_ARG> list) : CMD (no, name, list) {
		
	}
	
	void decodeCmd (std::istream &input, std::ostream &output, SNRVer ver, int output_data=1);
	void encodeCmd (std::istream &input, std::ostream &output, SNRVer ver, int output_data=1);
	
private:
	enum PRINT_PROPERTY : uint8_t {
		MASK_PROVIDED = 1,
		DURATION_PROVIDED = 1 << 1,
		UNKNOWN_PROVIDED = 1 << 2,
		ONS_EFFECT_PROVIDED = 1 << 3,
		BLOCKING_PROVIDED = 1 << 7 // Never written
	};
	
	enum RESOURCE_COMMAND : uint8_t {
		REMOVE_SLOT = 0,
		LOAD_SIMPLE,
		PIC_LOAD,
		SPRITE_LOAD,
		ANIME_LOAD,
		RAIN_LOAD,
		UNUSED1,
		QUIZ,
		CAKE,
		QUIZ2,
		MURDER_STORY
	};
	
	enum ANIME_PROPERTY : uint8_t {
		INDEX_PROVIDED = 1,
		SINGLE_PLAY_PROVIDED = 1 << 2
	};
	
	enum SPRITE_COMMAND : uint8_t {
		VALUE_PROVIDED = 1,
		FRAMES_PROVIDED = 1 << 1,
		EQUATION_PROVIDED = 1 << 2
	};
};


int process_snr_dec(char *snr, SNRVer mode, char *output);
int process_snr_enc(char *txt, char* basesnr, char *outsnr);

#endif
