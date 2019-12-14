//
//  snr.cpp
//  AlchemistUnpacker
//

#include <map>
#include <cassert>

#include "snr.h"
#include "main.h"

typedef std::unordered_map<uint8_t, CMD*> OpcodeMap; // {OPCODE: COMMAND}

std::map<SNRVer, std::ios::pos_type> script_offset { // File offset of SCRIPT segment
	{SNRVer::RONDO, 0x91ba0},
	{SNRVer::CHIRU, 0xc6260}
};

static char *allocBuf (int size) {
	char *buf = new char[size];
	memset(buf, '\0', size);
	return buf;
}

inline uint8_t getId (std::string &name, const OpcodeMap &map) {
	for (auto &elem : map) {
		if (elem.second->cmd_name == name) {
			return elem.second->cmd_no;
		}
	}
	return 0;
}

inline OpcodeMap* setupOpcodes(const OpcodeMap& base, const OpcodeMap& diff) {
	auto ret = new OpcodeMap(base);
	for (auto p : diff)
		(*ret)[p.first] = p.second;
	return ret;
}

inline const OpcodeMap setupChiruOpcodes() {
	const OpcodeMap ret {
		{0x42, new CMD_VAR(0x42, "REGISTER_OPERATION_42",{ CMD_ARG(ARG_TYPE::USHORT), CMD_ARG(ARG_TYPE::UCHAR), CMD_ARG(ARG_TYPE::USHORT), CMD_ARG(ARG_TYPE::UCHAR), CMD_ARG(ARG_TYPE::USHORT, 3)})},
		{0x81, new CMD(0x81, "READ_EXTERNAL", {CMD_ARG(ARG_TYPE::REGISTER), CMD_ARG(ARG_TYPE::REGISTER)})},
		{0x82, new CMD(0x82, "SPECIAL_WAIT", {CMD_ARG(ARG_TYPE::USHORT)})},
		{0x84, new CMD(0x84, "CLICK_WAIT", {CMD_ARG(ARG_TYPE::USHORT)})},
		{0xA5, new CMD(0xA5, "SFX_PLAY2", {CMD_ARG(ARG_TYPE::USHORT), CMD_ARG(ARG_TYPE::USHORT), CMD_ARG(ARG_TYPE::USHORT)})},
		{0xB3, new CMD( 0xB3, "CLEARTIMER", {CMD_ARG(ARG_TYPE::USHORT)})},
		{0xCC, new CMD(0xCC, "SYSTEM_MENU_SHOW", {CMD_ARG(ARG_TYPE::USHORT), CMD_ARG(ARG_TYPE::USHORT)})},
		{0xCD, new CMD(0xCD, "GET_RESPONSE_CAKE", {CMD_ARG(ARG_TYPE::USHORT), CMD_ARG(ARG_TYPE::USHORT)})},
		{0xCE, new CMD(0xCE, "GET_RESPONSE_QUIZ", {CMD_ARG(ARG_TYPE::USHORT), CMD_ARG(ARG_TYPE::USHORT)})},
		{0xCF, new CMD(0xCF, "GET_RESPONSE_QUIZ2", {CMD_ARG(ARG_TYPE::USHORT), CMD_ARG(ARG_TYPE::USHORT)})},
		{0xD0, new CMD(0xD0, "GET_RESPONSE_MURDER_STORY", {CMD_ARG(ARG_TYPE::USHORT), CMD_ARG(ARG_TYPE::USHORT)})}
	};
	return ret;
}

// {OPCODE: COMMAND}
OpcodeMap rondo_cmds {
	{0x3D, new CMD( 0x3D, "REGISTER_OPERATION_3D",{ CMD_ARG(ARG_TYPE::USHORT,7) } )},
	{0x41, new CMD_VAR( 0x41, "REGISTER_MODIFY",{ CMD_ARG(ARG_TYPE::UCHAR), CMD_ARG(ARG_TYPE::REGISTER), CMD_ARG(ARG_TYPE::REGISTER) } )},
	{0x42, new CMD( 0x42, "REGISTER_OPERATION_42",{ CMD_ARG(ARG_TYPE::USHORT), CMD_ARG(ARG_TYPE::UCHAR), CMD_ARG(ARG_TYPE::USHORT), CMD_ARG(ARG_TYPE::UCHAR), CMD_ARG(ARG_TYPE::USHORT,4) } )},
	{0x46, new CMD_ADDR( 0x46, "REGISTER_CONDITION",{ CMD_ARG(ARG_TYPE::UCHAR), CMD_ARG(ARG_TYPE::REGISTER), CMD_ARG(ARG_TYPE::USHORT), CMD_ARG(ARG_TYPE::ADDRESS) } )},
	{0x47, new CMD_ADDR( 0x47, "JUMP_TO_ADDRESS", { CMD_ARG(ARG_TYPE::ADDRESS) } )},
	{0x48, new CMD_ADDR( 0x48, "GOSUB",		{ CMD_ARG(ARG_TYPE::ADDRESS) } )},
	{0x4A, new CMD_ADDR( 0x4A, "JUMP_ONVALUE_NBRANCHES", { CMD_ARG(ARG_TYPE::REGISTER), CMD_ARG(ARG_TYPE::USHORT) } )},
	{0x49, new CMD( 0x49, "RETURN_KEYWORD",	{ } )},
	{0x4D, new CMD( 0x4D, "STACK_PUSH",		{ CMD_ARG(ARG_TYPE::UCHAR), CMD_ARG(ARG_TYPE::USHORT,-1) } )},
	{0x4E, new CMD( 0x4E, "STACK_POP",		{ CMD_ARG(ARG_TYPE::UCHAR), CMD_ARG(ARG_TYPE::USHORT,-1) } )},
	{0x80, new CMD( 0x80, "SYSCALL",		{ CMD_ARG(ARG_TYPE::REGISTER) } )},
	{0x83, new CMD( 0x83, "WAIT",			{ CMD_ARG(ARG_TYPE::USHORT) } )},
	{0x85, new CMD( 0x85, "TEXTBOX_COMMAND",{ CMD_ARG(ARG_TYPE::USHORT), CMD_ARG(ARG_TYPE::USHORT) } )},
	{0x86, new CMD( 0x86, "DIALOGUE",		{ CMD_ARG(ARG_TYPE::USHORT), CMD_ARG(ARG_TYPE::UCHAR), CMD_ARG(ARG_TYPE::UCHAR), CMD_ARG(ARG_TYPE::USHORT), CMD_ARG(ARG_TYPE::STRING,-1) } )},
	{0x87, new CMD( 0x87, "PIPE",			{ CMD_ARG(ARG_TYPE::SHORT) } )},
	{0x88, new CMD( 0x88, "PIPE_RETURN",	{ } )},
	{0x89, new CMD( 0x89, "HIDE_DIALOGUE_WINDOW",{ } )},
	{0x8B, new CMD( 0x8B, "HIDDEN_DIALOGUE",{ CMD_ARG(ARG_TYPE::USHORT), CMD_ARG(ARG_TYPE::STRING,-1) } )},
	{0x8C, new CMD( 0x8C, "SWITCH",			{ CMD_ARG(ARG_TYPE::UINTEGER), CMD_ARG(ARG_TYPE::REGISTER), CMD_ARG(ARG_TYPE::USHORT), CMD_ARG(ARG_TYPE::UCHAR), CMD_ARG(ARG_TYPE::STRING,-1), CMD_ARG(ARG_TYPE::UCHAR), CMD_ARG(ARG_TYPE::STRING,-1) } )},
	{0x8D, new CMD_VAR( 0x8D, "PRINT_COMMAND",{ CMD_ARG(ARG_TYPE::UCHAR) } )},
	{0x8E, new CMD( 0x8E, "INSTRUCTION_8E",	{ } )},
	{0x9C, new CMD( 0x9C, "BGM_PLAY",		{ CMD_ARG(ARG_TYPE::USHORT), CMD_ARG(ARG_TYPE::USHORT), CMD_ARG(ARG_TYPE::USHORT), CMD_ARG(ARG_TYPE::USHORT) } )},
	{0x9D, new CMD( 0x9D, "BGM_FADE",		{ CMD_ARG(ARG_TYPE::USHORT) } )},
	{0x9E, new CMD( 0x9E, "BGM_VOLUME",		{ CMD_ARG(ARG_TYPE::USHORT), CMD_ARG(ARG_TYPE::USHORT) } )},
	{0xA0, new CMD( 0xA0, "SFX_PLAY",		{ CMD_ARG(ARG_TYPE::USHORT), CMD_ARG(ARG_TYPE::USHORT), CMD_ARG(ARG_TYPE::USHORT), CMD_ARG(ARG_TYPE::USHORT), CMD_ARG(ARG_TYPE::USHORT) } )},
	{0xA1, new CMD( 0xA1, "CHANNEL_FADE",	{ CMD_ARG(ARG_TYPE::USHORT), CMD_ARG(ARG_TYPE::USHORT) } )},
	{0xA2, new CMD( 0xA2, "MIX_CHANNEL_FADE",{ CMD_ARG(ARG_TYPE::USHORT) } )},
	{0xA3, new CMD( 0xA3, "CHANNEL_VOLUME",	{ CMD_ARG(ARG_TYPE::USHORT), CMD_ARG(ARG_TYPE::USHORT), CMD_ARG(ARG_TYPE::USHORT) } )},
	{0xA4, new CMD( 0xA4, "CHANNEL_WAIT",	{ CMD_ARG(ARG_TYPE::USHORT), CMD_ARG(ARG_TYPE::USHORT) } )},
	{0xA6, new CMD( 0xA6, "RUMBLE",			{ CMD_ARG(ARG_TYPE::USHORT), CMD_ARG(ARG_TYPE::USHORT) } )},
	{0xB0, new CMD( 0xB0, "SECTION_START",	{ CMD_ARG(ARG_TYPE::USHORT), CMD_ARG(ARG_TYPE::UCHAR), CMD_ARG(ARG_TYPE::STRING,-1) } )},
	{0xB1, new CMD( 0xB1, "MOVIE_PLAY",		{ CMD_ARG(ARG_TYPE::USHORT) } )},
	{0xB2, new CMD( 0xB2, "WAITTIMER",		{ CMD_ARG(ARG_TYPE::USHORT) } )},
	{0xB3, new CMD( 0xB3, "CLEARTIMER",		{ } )},
	{0xB4, new CMD( 0xB4, "UNSETTIMER",		{ } )},
	{0xB6, new CMD( 0xB6, "SECTION_END",	{ } )},
	{0xB9, new CMD( 0xB9, "VOICE_PLAY",		{ CMD_ARG(ARG_TYPE::UCHAR), CMD_ARG(ARG_TYPE::STRING,-1) } )},
	{0xBD, new CMD( 0xBD, "TIP_ENTRY_UNLOCK",{ CMD_ARG(ARG_TYPE::UCHAR), CMD_ARG(ARG_TYPE::USHORT,-1) } )},
	{0xBA, new CMD( 0xBA, "INSTRUCTION_BA",	{ CMD_ARG(ARG_TYPE::USHORT) } )},
	{0xBE, new CMD( 0xBE, "OPEN_TROPHY",	{ CMD_ARG(ARG_TYPE::USHORT) } )},
	{0xBF, new CMD( 0xBF, "CHARACTER_ENTRY_UNLOCK",	{ CMD_ARG(ARG_TYPE::USHORT), CMD_ARG(ARG_TYPE::USHORT) } )},
	{0xC0, new CMD( 0xC0, "SPRITESET_CLEAR",{ } )},
	{0xC1, new CMD_VAR( 0xC1, "RESOURCE_COMMAND", { CMD_ARG(ARG_TYPE::USHORT), CMD_ARG(ARG_TYPE::USHORT) } )},
	{0xC2, new CMD_VAR( 0xC2, "SPRITE_PROPERTY", { CMD_ARG(ARG_TYPE::USHORT), CMD_ARG(ARG_TYPE::USHORT), CMD_ARG(ARG_TYPE::UCHAR) } )},
	{0xC3, new CMD( 0xC3, "SPRITE_COMMAND_WAIT_FOR_END",{ CMD_ARG(ARG_TYPE::USHORT), CMD_ARG(ARG_TYPE::USHORT) } )},
	{0xC4, new CMD( 0xC4, "MASK_COMMAND",	{ CMD_ARG(ARG_TYPE::USHORT), CMD_ARG(ARG_TYPE::USHORT) } )},
	{0xC5, new CMD( 0xC5, "SELECT_SPRITESET",{ CMD_ARG(ARG_TYPE::USHORT) } )},
	{0xC6, new CMD( 0xC6, "SPRITESET_INITIALIZE",	{ } )},
	{0xC7, new CMD_VAR( 0xC7, "SPRITESET_PROPERTY", { CMD_ARG(ARG_TYPE::USHORT), CMD_ARG(ARG_TYPE::UCHAR) } )},
	{0xC8, new CMD( 0xC8, "SPRITESET_COMMAND_WAIT_FOR_END",{ CMD_ARG(ARG_TYPE::USHORT) } )},
	{0xC9, new CMD( 0xC9, "SECTION_MARKER",	{ } )},
	{0xCA, new CMD_VAR( 0xCA, "GLOBAL_DISPLAY_COMMAND", { CMD_ARG(ARG_TYPE::USHORT), CMD_ARG(ARG_TYPE::UCHAR) } )},
	{0xCB, new CMD( 0xCB, "GLOBAL_DISPLAY_COMMAND_WAIT_FOR_END",{ CMD_ARG(ARG_TYPE::USHORT) } )},
	// label hack
	{0xFF, new CMD_ADDR( 0xFF, "LABEL",{ CMD_ARG(ARG_TYPE::USHORT) } )}
};

// {OPCODE: CMD}
OpcodeMap* chiru_cmds;

// {GAME_VER: {OPCODE: COMMAND}}
std::map<SNRVer, OpcodeMap *> cmds {
	{SNRVer::RONDO, &rondo_cmds},
	{SNRVer::CHIRU, chiru_cmds}
};

//external, check header for explanation
std::unordered_map<uint32_t, uint32_t> labels;
std::unordered_map<uint32_t, label_enc> labels_enc;
uint32_t label_count {1};

int process_snr_dec(char *snr, SNRVer mode, char *output) {

	std::ifstream fin(snr, std::ios::binary);
	
	if (!fin) return 1;
	if (mode == SNRVer::UNKNOWN) {
		std::cerr << "Unknown snr version" << std::endl;
		return 1;
	}
	
	if (mode == SNRVer::CHIRU) {
		chiru_cmds = setupOpcodes(rondo_cmds, setupChiruOpcodes());
		cmds[SNRVer::CHIRU] = chiru_cmds;
	}
	
	OpcodeMap* codes = cmds[mode];
	
	std::ofstream fout(output);
	//std::ostream &fout = std::cout;
	if (!fout) return 1;
	
	fout << "DECODE_MODE " << static_cast<int>(mode);
	
	for (int i = 0; i < 2; i++) {
		fin.seekg(script_offset[mode]);
			
		while (!fin.eof()) {
			if (i && labels[static_cast<uint32_t>(fin.tellg())]) {
				fout << "\nLABEL " << labels[static_cast<uint32_t>(fin.tellg())];
			}
		
			unsigned char cmd = fin.get();
			if ((*codes)[cmd] == nullptr) {
				if (cmd == 0x00) {
					std::cerr << "Pass " << i << " complete" << std::endl;
					break;
				}
				std::cerr << "Undefined command 0x" << std::hex << (int)cmd << " at 0x" << fin.tellg() - std::ios::pos_type(1) << std::endl;
				std::cerr.unsetf(std::ios::showbase);
				break;
			}
//			printf("Read 0x%x at 0x%x\n", cmd, static_cast<uint32_t>(fin.tellg() - std::ios::pos_type(1)));
			(*codes)[cmd]->decodeCmd(fin, fout, mode, i);
		}
	}
	
	return 0;
}

int process_snr_enc(char *txt, char* basesnr, char *outsnr) {

	std::ifstream fin(txt);
	if (!fin) return 1;
	
	std::ifstream fbase(basesnr, std::ios::binary);
	if (!fbase) return 1;
	
	std::ofstream fout(outsnr, std::ios::binary);
	if (!fout) return 1;
	
	std::string cmd;
	uint8_t id;
	char temp[4096];
	
	fin >> cmd;
	if (cmd != "DECODE_MODE") return 1;
	
	int modeIn;
	fin >> modeIn;
	
	SNRVer mode = static_cast<SNRVer>(modeIn);
	if (mode == SNRVer::UNKNOWN) {
		std::cerr << "Unknown snr version" << std::endl;
		return 1;
	}
	
	if (mode == SNRVer::CHIRU) {
		chiru_cmds = setupOpcodes(rondo_cmds, setupChiruOpcodes());
		cmds[SNRVer::CHIRU] = chiru_cmds;
	}
	
	char* data = nullptr;
	try {
	  data = new char[script_offset.at(mode)];
	} catch (std::out_of_range) {
		std::cerr << "No encode handler for mode " << static_cast<int>(mode) << std::endl;
		exit(1);
	}
	
	fbase.read(data, script_offset[mode]);
	fout.write(data, script_offset[mode]);
	delete [] data;
	
	fout.seekp(script_offset[mode]);
	
	while (!fin.eof()) {
		fin >> cmd;
		if (cmd[0] == ';') {
			fin.getline(temp,4096);
			continue;
		}
		id = getId(cmd, *cmds[mode]);
//		std::cerr << std::hex << cmd << ": 0x" << (int)id << std::endl;

		if (id == 0) {
			std::cerr << "Undefined command 0x" << std::hex << (int)id << " at 0x" << fin.tellg() - std::ios::pos_type(1) << std::endl;
			std::cerr.unsetf(std::ios::showbase);
			return 0;
		}
		(*cmds[mode])[id]->encodeCmd(fin, fout, mode);
	}
	
	auto cur = fout.tellp();
	for (int i = 0; i < 16 - (cur % 16); i++) fout.put(0x00);
	
	for (auto &lbl : labels_enc) {
		uint32_t &val = lbl.second.addr;
		for (auto &addr : lbl.second.toAdd) {
			fout.seekp(addr);
			fout.write((char*)&val,4);
		}
	}
	
	std::cerr << "Reached the end" << std::endl;
	
	return 0;
}

// For basic ones

void CMD::decodeCmd (std::istream &input, std::ostream &output, SNRVer ver, int output_data) {
	
	//std::cerr << "CMD::decodeCmd " << args.size() << " " << getSize(args[0],0) << std::endl;
	
	if (output_data) output << '\n' << cmd_name;
	temps t;
	
	for (int i = 0; i < args.size(); i++) {
		auto &arg = args[i];
		
		switch (arg.type) {
			case ARG_TYPE::STRING:
			{
				if (output_data) output << '\t';
				int size = getSize(arg,i);
				if (size != 0) {
					char *str = new char[size];
					input.read(str, size);
					if (output_data) output.write(str,size-1);
					delete [] str;
				} else {
					t.str = "";
					std::getline( input, t.str, '\0' );
					if (output_data) output << t.str;
				}
			}
				break;
			case ARG_TYPE::ADDRESS:
				for (int j = 0; j < getSize(arg,i); j++) {
					arg.value = emit<uint32_t>(input, output, 0);
				}
				break;
			case ARG_TYPE::UCHAR:
				for (int j = 0; j < getSize(arg,i); j++) {
					arg.value = emit_as<uint8_t, int>(input, output, output_data);
				}
				break;
			case ARG_TYPE::SHORT:
				for (int j = 0; j < getSize(arg,i); j++) {
					arg.value = emit<int16_t>(input, output, output_data);
				}
				break;
			case ARG_TYPE::USHORT:
				for (int j = 0; j < getSize(arg,i); j++) {
					arg.value = emit<uint16_t>(input, output, output_data);
				}
				break;
			case ARG_TYPE::UINTEGER:
				for (int j = 0; j < getSize(arg,i); j++) {
					arg.value = emit<uint32_t>(input, output, output_data);
				}
				break;
			case ARG_TYPE::REGISTER:
				//FIXME
				for (int j = 0; j < getSize(arg,i); j++) {
					arg.value = emit<uint16_t>(input, output, output_data);
				}
				break;
			default:
				break;
		}
	}
}

void CMD::encodeCmd (std::istream &input, std::ostream &output, SNRVer ver, int output_data) {
	if (output_data) output << cmd_no;
	temps t;
	
	for (int i = 0; i < args.size(); i++) {
		auto &arg = args[i];
		while (input.peek() == '\t') input.get(); //skip \t
		
		switch (arg.type) {
			case ARG_TYPE::STRING:
			{
				int size = getSize(arg,i);
				if (size != 0) {
					char *str = allocBuf(size);
					input.read(str, size-1);
					if (output_data) { output.write (str, size-1); output.put(0x00); }
					delete [] str;
				} else {
					t.str = "";
					std::getline( input, t.str, '\t' );
					if (output_data) output.write(t.str.c_str(), t.str.length()+1);
				}
			}
				break;
			case ARG_TYPE::ADDRESS:
				for (int j = 0; j < getSize(arg,i); j++) {
					arg.value = consume<uint32_t>(input, output, 0);
				}
				break;
			case ARG_TYPE::UCHAR:
				for (int j = 0; j < getSize(arg,i); j++) {
					arg.value = consume_as<uint16_t, unsigned char>(input, output, output_data); //due to interpretation reasons
				}
				break;
			case ARG_TYPE::SHORT:
				for (int j = 0; j < getSize(arg,i); j++) {
					arg.value = consume<int16_t>(input, output, output_data);
				}
				break;
			case ARG_TYPE::USHORT:
				for (int j = 0; j < getSize(arg,i); j++) {
					arg.value = consume<uint16_t>(input, output, output_data);
				}
				break;
			case ARG_TYPE::UINTEGER:
				for (int j = 0; j < getSize(arg,i); j++) {
					arg.value = consume<uint32_t>(input, output, output_data);
				}
				break;
			case ARG_TYPE::REGISTER:
				for (int j = 0; j < getSize(arg,i); j++) {
					arg.value = consume<uint16_t>(input, output, output_data);
				}
				break;
			default:
				break;
				
		}
	}
}

// For the ones dealing with addresses

void CMD_ADDR::decodeCmd (std::istream &input, std::ostream &output, SNRVer ver, int output_data) {
	CMD::decodeCmd (input, output, ver, output_data);
	if (cmd_no == 0x46 || cmd_no == 0x47 || cmd_no == 0x48) { // REGISTER_CONDITION or JUMP_TO_ADDRESS or GOSUB
		if (!labels[static_cast<uint32_t>(args[args.size()-1].value)]) {
			labels[static_cast<uint32_t>(args[args.size()-1].value)] = label_count;
			if (output_data) output << '\t' << label_count;
			label_count++;
		} else if (output_data) {
			output << '\t' << labels[static_cast<uint32_t>(args[args.size()-1].value)];
		}
	} else if (cmd_no == 0x4A) { // JUMP_ONVALUE_NBRANCHES
		uint32_t temp32;
		for (int i = 0; i < args[1].value; i++) {
			input.read((char*)&temp32,4);
			if (!labels[temp32]) {
				labels[temp32] = label_count;
				if (output_data) output << '\t' << label_count;
				label_count++;
			} else if (output_data) {
				output << '\t' << labels[temp32];
			}
		}
	}
}

void CMD_ADDR::encodeCmd (std::istream &input, std::ostream &output, SNRVer ver, int output_data) {
	int32_t lbl;
	if (cmd_no == 0x46 || cmd_no == 0x47 || cmd_no == 0x48) { // REGISTER_CONDITION or JUMP_TO_ADDRESS or GOSUB
		CMD::encodeCmd (input, output, ver);
		lbl = static_cast<int32_t>(args[args.size()-1].value);
		labels_enc[lbl].toAdd.push_back(static_cast<uint32_t>(output.tellp()));
		output.write((char *)&labels_enc[lbl].addr,4);
	} else if (cmd_no == 0xFF) { // LABEL
		CMD::encodeCmd (input, output, ver, 0);
		lbl = static_cast<int32_t>(args[args.size()-1].value);
		labels_enc[lbl].addr = static_cast<uint32_t>(output.tellp());
	} else if (cmd_no == 0x4A) { // JUMP_ONVALUE_NBRANCHES
		CMD::encodeCmd (input, output, ver);
		
		for (int i = 0; i < args[1].value; i++) {
			input >> lbl;
			labels_enc[lbl].toAdd.push_back(static_cast<uint32_t>(output.tellp()));
			output.write((char *)&labels_enc[lbl].addr,4);
		}
	}
}

// For the variable ones

void CMD_VAR::decodeCmd (std::istream &input, std::ostream &output, SNRVer ver, int output_data) {
	CMD::decodeCmd (input, output, ver, output_data);
	temps t;
	if (cmd_no == 0x41) { // REGISTER_MODIFY
		int64_t mode = args[0].value;
		switch(mode) {
			case 0x82:
				emit<uint16_t>(input, output, output_data); // value2
				emit_as<uint8_t, int>(input, output, output_data); // mode2
				emit<uint16_t>(input, output, output_data); // value3
				emit<uint16_t>(input, output, output_data); // value4
				break; // Otherwise, we get an extraneous read
			case 0x83:
				emit<uint16_t>(input, output, output_data); // value2
				break;
			default:
				break;
		}
	} else if (cmd_no == 0x42) { // REGISTER_OPERATION_42
		uint16_t register4 = emit<uint16_t>(input, output, output_data);
		if (register4 == 512) {
			emit<uint16_t>(input, output, output_data); // register5
			emit<uint16_t>(input, output, output_data); // register6
		}
	} else if (cmd_no == 0x8D) { // PRINT_COMMAND
		int64_t flags;
		int64_t mode = 0;
		
		switch (ver) {
			case SNRVer::RONDO:
				if (args.size() != 1)
					throw std::invalid_argument("Rondo PRINT_COMMAND expects 1 arg");
				flags = args[0].value;
				break;
			case SNRVer::CHIRU:
				if (args.size() != 1)
					throw std::invalid_argument("Chiru PRINT_COMMAND expects 1 arg");
				mode = args[0].value;
				break;
			default:
				throw std::invalid_argument("No handler for this ver of PRINT_COMMAND");
		}
		
		if (mode == 1) { // Chiru glass smash
			uint8_t extra = emit<uint8_t>(input, output, output_data);

			if (extra & 1)
				emit<uint16_t>(input, output, output_data); // speed
				
			return;
		}
		
		if (ver == SNRVer::CHIRU) // Fall back to Rondo variant
			flags = emit_as<uint8_t, int>(input, output, output_data);
		
		if (flags & PRINT_PROPERTY::MASK_PROVIDED)
			emit<uint16_t>(input, output, output_data);
		if (flags & PRINT_PROPERTY::DURATION_PROVIDED)
			emit<uint16_t>(input, output, output_data);
		if (flags & PRINT_PROPERTY::UNKNOWN_PROVIDED)
			emit<uint16_t>(input, output, output_data);
		if (flags & PRINT_PROPERTY::ONS_EFFECT_PROVIDED)
			emit<uint16_t>(input, output, output_data);
		
	} else if (cmd_no == 0xC1) { // RESOURCE_COMMAND
		int64_t cmd;
		switch (ver) {
			default:
				if (args.size() != 2)
					throw std::invalid_argument("RESOURCE_COMMAND expects 2 args");
				break;
		}
		cmd = args[1].value;
		switch (cmd) {
			case RESOURCE_COMMAND::REMOVE_SLOT: {
				emit_as<uint8_t, int>(input, output, output_data); // unk1
				break;
			}
			case RESOURCE_COMMAND::PIC_LOAD: {
				uint8_t bg_provided = emit_as<uint8_t, int>(input, output, output_data);
				
				if (bg_provided)
					emit<uint16_t>(input, output, output_data); // bg_index
				break;
			}
			case RESOURCE_COMMAND::RAIN_LOAD: {
				uint8_t show = emit_as<uint8_t, int>(input, output, output_data);
				
				if (show)
					emit<uint16_t>(input, output, output_data); // unk_2
				break;
			}
			case RESOURCE_COMMAND::SPRITE_LOAD: {
				uint8_t unk1 = emit_as<uint8_t, int>(input, output, output_data);
				emit<uint16_t>(input, output, output_data); // sprite_index
				
				if (unk1 > 1)
					emit<uint16_t>(input, output, output_data); // unk_2
				break;
			}
			case RESOURCE_COMMAND::ANIME_LOAD: {
				uint8_t flags = emit_as<uint8_t, int>(input, output, output_data);
				
				if (flags & ANIME_PROPERTY::INDEX_PROVIDED)
					emit<uint16_t>(input, output, output_data); // anime_index
				if (flags & ANIME_PROPERTY::SINGLE_PLAY_PROVIDED)
					emit<uint16_t>(input, output, output_data); // single_play
				break;
			}
			case RESOURCE_COMMAND::LOAD_SIMPLE: {
				emit_as<uint8_t, int>(input, output, output_data); // type
				emit<uint16_t>(input, output, output_data); // colour
				emit<uint16_t>(input, output, output_data); // width
				emit<uint16_t>(input, output, output_data); // height
				emit<uint16_t>(input, output, output_data); // unk1
				break;
			}
			// Chiru:
			case RESOURCE_COMMAND::QUIZ: {
				uint8_t unk_1 = emit<uint8_t>(input, output, output_data);
				assert(unk_1 == 1);
				emit<int16_t>(input, output, output_data); // button_mask
				break;
			}
			case RESOURCE_COMMAND::CAKE: {
				uint8_t unk_1 = emit<uint8_t>(input, output, output_data);
				assert(unk_1 == 0);
				break;
			}
			case RESOURCE_COMMAND::QUIZ2: {
				uint8_t unk_1 = emit<uint8_t>(input, output, output_data);
				assert(unk_1 == 0);
				break;
			}
			case RESOURCE_COMMAND::MURDER_STORY: {
				uint8_t unk_1 = emit<uint8_t>(input, output, output_data);
				assert(unk_1 == 0);
				break;
			}
			default:
				throw std::runtime_error("No handler for RESOURCE_COMMAND " + std::to_string(cmd));
				break;
		}
	} else if (cmd_no == 0xC2 || cmd_no == 0xC7 || cmd_no == 0xCA) { // SPRITE_COMMAND or SPRITESET_COMMAND or GLOBAL_DISPLAY_COMMAND
		int64_t property = args[args.size() - 1].value;
		if (property & SPRITE_COMMAND::VALUE_PROVIDED)
			emit<uint16_t>(input, output, output_data);
		if (property & SPRITE_COMMAND::FRAMES_PROVIDED)
			emit<uint16_t>(input, output, output_data);
		if (property & SPRITE_COMMAND::EQUATION_PROVIDED)
			emit<uint16_t>(input, output, output_data);
	}
}

void CMD_VAR::encodeCmd (std::istream &input, std::ostream &output, SNRVer ver, int output_data) {
	CMD::encodeCmd (input, output, ver);
	temps t;
	if (cmd_no == 0x41) { // REGISTER_MODIFY
		int64_t mode = args[0].value;
		switch(mode) {
			case 0x82:
				consume<uint16_t>(input, output, output_data); // value2
				consume_as<uint16_t, uint8_t>(input, output, output_data); // mode2
				consume<uint16_t>(input, output, output_data); // value3
				consume<uint16_t>(input, output, output_data); // value4
				break;
			case 0x83:
				consume<uint16_t>(input, output, output_data); // value2
				break;
			default:
				break;
		}
	} else if (cmd_no == 0x42) { // REGISTER_OPERATION_42
			uint16_t register4 = consume<uint16_t>(input, output, output_data);
			if (register4 == 512) {
				consume<uint16_t>(input, output, output_data); // register5
				consume<uint16_t>(input, output, output_data); // register6
			}
	} else if (cmd_no == 0x8D) { // PRINT_COMMAND
		int64_t flags;
		int64_t mode = 0;
		
		switch (ver) {
			case SNRVer::RONDO:
				if (args.size() != 1)
					throw std::invalid_argument("Rondo PRINT_COMMAND expects 1 arg");
				flags = args[0].value;
				break;
			case SNRVer::CHIRU:
				if (args.size() != 1)
					throw std::invalid_argument("Chiru PRINT_COMMAND expects 1 arg");
				mode = args[0].value;
				break;
			default:
				throw std::invalid_argument("No handler for this ver of PRINT_COMMAND");
		}
		
		if (mode == 1) { // Chiru glass smash
			uint8_t extra = consume<uint8_t>(input, output, output_data);
			if (extra & 0x1)
				consume<int16_t>(input, output, output_data); // speed
			
			return;
		}
		
		if (ver == SNRVer::CHIRU) { // Fall back to Rondo variant
			flags = consume_as<uint16_t, uint8_t>(input, output, output_data);
		}
		
		if (flags & PRINT_PROPERTY::MASK_PROVIDED)
			consume<uint16_t>(input, output, output_data);
		if (flags & PRINT_PROPERTY::DURATION_PROVIDED)
			consume<uint16_t>(input, output, output_data);
		if (flags & PRINT_PROPERTY::UNKNOWN_PROVIDED)
			consume<uint16_t>(input, output, output_data);
		if (flags & PRINT_PROPERTY::ONS_EFFECT_PROVIDED)
			consume<uint16_t>(input, output, output_data);
	} else if (cmd_no == 0xC1) { // RESOURCE_COMMAND
		int64_t cmd;
		switch (ver) {
			default:
				if (args.size() != 2)
					throw std::invalid_argument("RESOURCE_COMMAND expects 2 args");
				break;
		}
		cmd = args[1].value;
		switch (cmd) {
			case RESOURCE_COMMAND::REMOVE_SLOT: {
				consume_as<uint16_t, uint8_t>(input, output, output_data); // unk1
				break;
			}
			case RESOURCE_COMMAND::PIC_LOAD: {
				uint16_t bg_provided = consume_as<uint16_t, uint8_t>(input, output, output_data);
				
				if (bg_provided)
					consume<uint16_t>(input, output, output_data); // bg_index
				break;
			}
			case RESOURCE_COMMAND::RAIN_LOAD: {
				uint16_t show = consume_as<uint16_t, uint8_t>(input, output, output_data);
				
				if (show)
					consume<uint16_t>(input, output, output_data); // unk_2
				break;
			}
			case RESOURCE_COMMAND::SPRITE_LOAD: {
				uint16_t unk1 = consume_as<uint16_t, uint8_t>(input, output, output_data);
				consume<uint16_t>(input, output, output_data); // sprite_index
				
				if (unk1 > 1)
					consume<uint16_t>(input, output, output_data); // unk_2
				break;
			}
			case RESOURCE_COMMAND::ANIME_LOAD: {
				uint16_t flags = consume_as<uint16_t, uint8_t>(input, output, output_data);
				
				if (flags & ANIME_PROPERTY::INDEX_PROVIDED)
					consume<uint16_t>(input, output, output_data); // anime_index
				if (flags & ANIME_PROPERTY::SINGLE_PLAY_PROVIDED)
					consume<uint16_t>(input, output, output_data); // single_play
				break;
			}
			case RESOURCE_COMMAND::LOAD_SIMPLE: {
				consume_as<uint16_t, uint8_t>(input, output, output_data); // type
				consume<uint16_t>(input, output, output_data); // colour
				consume<uint16_t>(input, output, output_data); // width
				consume<uint16_t>(input, output, output_data); // height
				consume<uint16_t>(input, output, output_data); // unk1
				break;
			}
			// Chiru:
			case RESOURCE_COMMAND::QUIZ: {
				uint8_t unk_1 = consume<uint8_t>(input, output, output_data);
				assert(unk_1 == 1);
				consume<int16_t>(input, output, output_data); // button_mask
				break;
			}
			case RESOURCE_COMMAND::CAKE: {
				uint8_t unk_1 = consume<uint8_t>(input, output, output_data);
				assert(unk_1 == 0);
				break;
			}
			case RESOURCE_COMMAND::QUIZ2: {
				uint8_t unk_1 = consume<uint8_t>(input, output, output_data);
				assert(unk_1 == 0);
				break;
			}
			case RESOURCE_COMMAND::MURDER_STORY: {
				uint8_t unk_1 = consume<uint8_t>(input, output, output_data);
				assert(unk_1 == 0);
				break;
			}
			default:
				throw std::runtime_error("No handler for RESOURCE_COMMAND " + std::to_string(cmd));
				break;
			}
		} else if (cmd_no == 0xC2 || cmd_no == 0xC7 || cmd_no == 0xCA) { // SPRITE_COMMAND or SPRITESET_COMMAND or GLOBAL_DISPLAY_COMMAND
			int64_t property = args[args.size() - 1].value;
			if (property & SPRITE_COMMAND::VALUE_PROVIDED)
				consume<uint16_t>(input, output, output_data);
			if (property & SPRITE_COMMAND::FRAMES_PROVIDED)
				consume<uint16_t>(input, output, output_data);
			if (property & SPRITE_COMMAND::EQUATION_PROVIDED)
				consume<uint16_t>(input, output, output_data);
		}
}
