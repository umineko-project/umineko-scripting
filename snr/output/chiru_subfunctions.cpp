/*
 * Reverse-engineered chiru subfunctions.
 * Encoding: UTF-8
 *
 * Copyright (c) 2013 Umineko Project
 *
 * This document is considered confidential and proprietary,
 * and may not be reproduced or transmitted in any form 
 * in whole or in part, without the express written permission
 * of Umineko Project.
 */

void sub_c6366(reg9,reg10) {
	if(reg9==1) {
		if (!reg10) reg10=1;
		printCommand(130);
		doTransitionEffect(duration=reg10);
	} else if(reg9==2) {
		if (!reg10) reg10=60;
		printCommand(130);
		doTransitionEffect(duration=reg10);
	} else if(reg9==22) {
		if (!reg10) reg10=18;
		printCommand(130);
		doTransitionEffect(duration=reg10);
	} else if(reg9==42) {
		if (!reg10) reg10=180;
		printCommand(130);
		doTransitionEffect(duration=reg10);
	} else if(reg9==62) {
		if (!reg10) reg10=12;
		printCommand(130);
		doTransitionEffect(duration=reg10);
	} else if(reg9==3) {
		if (!reg10) reg10=60;
		printCommand(131);
		doTransitionEffect(transitionMask=LEFT, duration=reg10);
	} else if(reg9==23) {
		if (!reg10) reg10=18;
		printCommand(131);
		doTransitionEffect(transitionMask=LEFT, duration=reg10);
	} else if(reg9==43) {
		if (!reg10) reg10=180;
		printCommand(131);
		doTransitionEffect(transitionMask=LEFT, duration=reg10);
	} else if(reg9==63) {
		if (!reg10) reg10=12;
		printCommand(131);
		doTransitionEffect(transitionMask=LEFT, duration=reg10);
	} else if(reg9==83) {
		if (!reg10) reg10=3;
		printCommand(131);
		doTransitionEffect(transitionMask=LEFT, duration=reg10);
	} else if(reg9==4) {
		if (!reg10) reg10=60;
		printCommand(131);
		doTransitionEffect(transitionMask=RIGHT, duration=reg10);
	} else if(reg9==24) {
		if (!reg10) reg10=18;
		printCommand(131);
		doTransitionEffect(transitionMask=RIGHT, duration=reg10);
	} else if(reg9==44) {
		if (!reg10) reg10=180;
		printCommand(131);
		doTransitionEffect(transitionMask=RIGHT, duration=reg10);
	} else if(reg9==64) {
		if (!reg10) reg10=12;
		printCommand(131);
		doTransitionEffect(transitionMask=RIGHT, duration=reg10);
	} else if(reg9==84) {
		if (!reg10) reg10=3;
		printCommand(131);
		doTransitionEffect(transitionMask=RIGHT, duration=reg10);
	} else if(reg9==5) {
		if (!reg10) reg10=60;
		printCommand(131);
		doTransitionEffect(transitionMask=DOWN, duration=reg10);
	} else if(reg9==25) {
		if (!reg10) reg10=18;
		printCommand(131);
		doTransitionEffect(transitionMask=DOWN, duration=reg10);
	} else if(reg9==45) {
		if (!reg10) reg10=180;
		printCommand(131);
		doTransitionEffect(transitionMask=DOWN, duration=reg10);
	} else if(reg9==65) {
		if (!reg10) reg10=12;
		printCommand(131);
		doTransitionEffect(transitionMask=DOWN, duration=reg10);
	} else if(reg9==85) {
		if (!reg10) reg10=3;
		printCommand(131);
		doTransitionEffect(transitionMask=DOWN, duration=reg10);
	} else if(reg9==6) {
		if (!reg10) reg10=60;
		printCommand(131);
		doTransitionEffect(transitionMask=UP, duration=reg10);
	} else if(reg9==26) {
		if (!reg10) reg10=18;
		printCommand(131);
		doTransitionEffect(transitionMask=UP, duration=reg10);
	} else if(reg9==46) {
		if (!reg10) reg10=180;
		printCommand(131);
		doTransitionEffect(transitionMask=UP, duration=reg10);
	} else if(reg9==66) {
		if (!reg10) reg10=12;
		printCommand(131);
		doTransitionEffect(transitionMask=UP, duration=reg10)
	} else if(reg9==86) {
		if (!reg10) reg10=3;
		printCommand(131);
		doTransitionEffect(transitionMask=UP, duration=reg10)
	} else if(reg9==7) {
		if (!reg10) reg10=60;
		printCommand(131);
		doTransitionEffect(transitionMask=X, duration=reg10)
	} else if(reg9==27) {
		if (!reg10) reg10=18;
		printCommand(131);
		doTransitionEffect(transitionMask=X, duration=reg10)
	} else if(reg9==47) {
		if (!reg10) reg10=180;
		printCommand(131);
		doTransitionEffect(transitionMask=X, duration=reg10)
	} else if(reg9==67) {
		if (!reg10) reg10=12;
		printCommand(131);
		doTransitionEffect(transitionMask=X, duration=reg10)
	} else if(reg9==8) {
		if (!reg10) reg10=60;
		printCommand(131);
		doTransitionEffect(transitionMask=C, duration=reg10)
	} else if(reg9==28) {
		if (!reg10) reg10=18;
		printCommand(131);
		doTransitionEffect(transitionMask=C, duration=reg10)
	} else if(reg9==48) {
		if (!reg10) reg10=180;
		printCommand(131);
		doTransitionEffect(transitionMask=C, duration=reg10)
	} else if(reg9==68) {
		if (!reg10) reg10=12;
		printCommand(131);
		doTransitionEffect(transitionMask=C, duration=reg10)
	} else if(reg9==88) {
		if (!reg10) reg10=1;
		printCommand(131);
		doTransitionEffect(transitionMask=C, duration=reg10)
	} else if(reg9==9) {
		if (!reg10) reg10=60;
		printCommand(131);
		doTransitionEffect(transitionMask=M1, duration=reg10)
	} else if(reg9==29) {
		if (!reg10) reg10=18;
		printCommand(131);
		doTransitionEffect(transitionMask=M1, duration=reg10)
	} else if(reg9==49) {
		if (!reg10) reg10=180;
		printCommand(131);
		doTransitionEffect(transitionMask=M1, duration=reg10)
	} else if(reg9==69) {
		if (!reg10) reg10=12;
		printCommand(131);
		doTransitionEffect(transitionMask=M1, duration=reg10)
	} else if(reg9==10) {
		if (!reg10) reg10=180;
		printCommand(131);
		doTransitionEffect(transitionMask=1, duration=reg10)
	} else if(reg9==30) {
		if (!reg10) reg10=18;
		printCommand(131);
		doTransitionEffect(transitionMask=1, duration=reg10)
	} else if(reg9==50) {
		if (!reg10) reg10=180;
		printCommand(131);
		doTransitionEffect(transitionMask=1, duration=reg10)
	} else if(reg9==11) {
		if (!reg10) reg10=180;
		printCommand(131);
		doTransitionEffect(transitionMask=2, duration=reg10)
	} else if(reg9==31) {
		if (!reg10) reg10=18;
		printCommand(131);
		doTransitionEffect(transitionMask=2, duration=reg10)
	} else if(reg9==99) {
		if (!reg10) reg10=60;
		printCommand(130);
		doTransitionEffect(duration=reg10)
	} else if(reg10!=0) {
		Print Command(130);
		doTransitionEffect(duration=reg10);
	} else {
		reg10=1;
		printCommand(130);
		doTransitionEffect(duration=reg10);
	}
	return;
}

//PS3 eye function analogue (broken glass 3)
void sub_cfa84(reg20) {
	spriteCommand(slot=11,property=PAUSE_LAYER,value=1);
	spriteCommand(slot=7,property=PAUSE_LAYER,value=1);
	
	hideDialogueWindow();
	waitFrames(10);
	
	loadPic(slot=28,file=HANA1);
	spriteCommand(slot=28,property=MULTIPLIER_ALPHA,value=150);
	sub_c6366(reg9=7,reg10=0u);
	
	loadPic(slot=29,file=CINEMA_LOGO3);
	spriteCommand(slot=29,property=X_POSITION,value=500);
	spriteCommand(slot=29,property=Y_POSITION,value=-50);
	sub_c6366(reg9=5,reg10=200);
	
	waitFrames(90);
	
	loadPic(slot=3,file=BLACK);
	spriteCommand(slot=3,property=PROP_5_MONOCRO,value=reg20);
	loadPic(slot=27,file=BLACK);
	loadPic(slot=28,file=BLACK);

	musicFade(frames=160);
	loopSFXFade(frames=160);

	sub_c6366(reg9=2,reg10=120);
	
	removeSlot(slots={1...30});
	
	return;
}

//PS3 eye function analogue (broken glass 4)
void sub_cf8d5(reg20) {
	spriteCommand(slot=11,property=PAUSE_LAYER,value=1);
	spriteCommand(slot=7,property=PAUSE_LAYER,value=1);
	
	hideDialogueWindow();
	waitFrames(10);
	
	loadPic(slot=27,file=BLACK);
	spriteCommand(slot=27,property=MULTIPLIER_ALPHA,value=150);
	sub_c6366(reg9=1,reg10=0u);

	musicFade(frames=0);
	loopSFXFade(frames=0);
	waitFrames(0);
	playSFX(channel=8,file=umise_1006,volume=180,single_play=1); //padding=0?
	loadPic(slot=28,file=WARE);
	sub_c6366(reg9=1,reg10=0u);
	
	waitFrames(60);
	
	loadPic(slot=29,file=CINEMA_LOGO3);
	spriteCommand(slot=29,property=X_POSITION,value=500);
	spriteCommand(slot=29,property=Y_POSITION,value=-50);
	sub_c6366(reg9=5,reg10=200);
	
	waitFrames(60);
	
	loadPic(slot=3,file=BLACK);
	spriteCommand(slot=3,property=PROP_5_MONOCRO,value=reg20);
	loadPic(slot=27,file=BLACK);
	loadPic(slot=28,file=BLACK);
	sub_c6366(reg9=2,reg10=120);
	
	removeSlot(slots={1...30});
	
	return;
}

//PS3 eye function analogue (clocks)
void sub_cfa84(reg28,reg29,reg30,reg31,reg32,reg33,reg13,reg14,reg7,reg8) {
	loadPic(slot=17,file=clock);
	loadPic(slot=18,file=clock_m);
	loadPic(slot=19,file=clock_h);
	loadPic(slot=20,file=clock_c);
	
	for (int c=17; c<21; c++) {
		spriteCommand(slot=c,property=X_POSITION,value=reg28);
		spriteCommand(slot=c,property=Y_POSITION,value=reg29);
		spriteCommand(slot=c,property=RESIZE_FACTOR_X,value=reg30);
		spriteCommand(slot=c,property=RESIZE_FACTOR_Y,value=reg30);
	}
	
	//First param is a dst register
	int r1,r2,r3,r4;
	op42(r1,reg13,60,-252,0?,0?,1000?,3?);
	op42(r2,reg13,720,-252,0?,0?,1000?,3?);
	op42(r3,reg14,60,-252,0?,0?,1000?,3?);
	op42(r4,reg14,720,-252,0?,0?,1000?,3?);
	
	spriteCommand(slot=18,property=ROTATION_ANGLE,value=r1);
	spriteCommand(slot=19,property=ROTATION_ANGLE,value=r2);
	printCommand(130,noblock,frames=60);
	
	if (reg1 != reg3 || reg2 != reg4) {
		reg7 = reg33;
		reg7 *= 12;
		reg8 = reg33;
		
		spriteCommand(slot=18,property=ROTATION_ANGLE,value=r3,frames=reg7,equation=TYPE_64);
		spriteCommand(slot=19,property=ROTATION_ANGLE,value=r4,frames=reg8,equation=TYPE_64);
		
		if (reg31==1) {
			playSFX(channel=20,file=umilse_1051,volume=180,single_play=0); //padding=0?
		} else {
			playSFX(channel=20,file=umilse_1050,volume=180,single_play=0); //padding=0?
		}
	}
	
	spriteCommandWait(slot=18,property=11);
	spriteCommandWait(slot=19,property=11);
	
	loopChannelFade(channel=20,frames=0);
	
	if (reg32 != 0) {
		playSFX(channel=2,file=umise_1052,volume=180,single_play=1); //padding=0?
		waitChannel(channel=2);
	} else {
		playSFX(channel=8,file=umise_030,volume=180,single_play=1);
		waitChannel(channel=8);
	}
	
	waitFrames(30);
	
	return;
}

//PS3 style whirl effect
void sub_cfbfb() { //equivalent to rondo
	printCommand(2,noblock,frames=120);
	quakeCommand(property=UNKNOWN_QUAKE_PROPERTY_11H,value=200);
	quakeCommand(property=UNKNOWN_QUAKE_PROPERTY_12H,value=25);
	quakeCommand(property=WHIRL_DEGREES,equation=SMOOTH_START_STOP,value=500,frames=60);
	quakeCommand(property=WHIRL_DEGREES,equation=SMOOTH_START_STOP,frames=60);
	quakeCommandWait(property=14);
	instruction8E();
	quakeCommand(property=UNKNOWN_QUAKE_PROPERTY_11H,value=1000);
	quakeCommand(property=UNKNOWN_QUAKE_PROPERTY_12H); //reset?
	
	return;
}

// All presents discarded
// reg13 — present_id {0, 17}
void sub_cfc2d(reg13) {
	reg1 = 20 + reg13; readExternal(reg1) += 1;
	reg1 = 20;
	do {
		reg3 = readExternal(reg1);
		reg2 += reg3;
		reg1 += 1;
	} while (reg1 <= 37);
	if (reg2 < 18) return;
	
	openTrophy(29);
}

// Show coin counter for quiz
void sub_ce623(reg36) {
	loadPic(slot=29,file=QW);
	spriteCommand(slot=29,property=X_POSITION,value=-703);
	spriteCommand(slot=29,property=Y_POSITION,value=-12);
	
	reg1 = reg36;
	
	switch (reg1) {
		case 0:
			loadPic(slot=28,file=Q00);
			spriteCommand(slot=28,property=X_POSITION,value=-615);
			spriteCommand(slot=28,property=Y_POSITION,value=-35);
			break;
		case 1:
			loadPic(slot=28,file=Q01);
			spriteCommand(slot=28,property=X_POSITION,value=-615);
			spriteCommand(slot=28,property=Y_POSITION,value=-35);
			break;
		case 2:
			loadPic(slot=28,file=Q02);
			spriteCommand(slot=28,property=X_POSITION,value=-615);
			spriteCommand(slot=28,property=Y_POSITION,value=-35);
			break;
		case 3:
			loadPic(slot=28,file=Q03);
			spriteCommand(slot=28,property=X_POSITION,value=-615);
			spriteCommand(slot=28,property=Y_POSITION,value=-35);
			break;
		case 4:
			loadPic(slot=28,file=Q04);
			spriteCommand(slot=28,property=X_POSITION,value=-615);
			spriteCommand(slot=28,property=Y_POSITION,value=-35);
			break;
		case 5:
			loadPic(slot=28,file=Q05);
			spriteCommand(slot=28,property=X_POSITION,value=-615);
			spriteCommand(slot=28,property=Y_POSITION,value=-35);
			break;
		case 6:
			loadPic(slot=28,file=Q06);
			spriteCommand(slot=28,property=X_POSITION,value=-615);
			spriteCommand(slot=28,property=Y_POSITION,value=-35);
			break;
		case 7:
			loadPic(slot=28,file=Q07);
			spriteCommand(slot=28,property=X_POSITION,value=-615);
			spriteCommand(slot=28,property=Y_POSITION,value=-35);
			break;
		case 8:
			loadPic(slot=28,file=Q08);
			spriteCommand(slot=28,property=X_POSITION,value=-615);
			spriteCommand(slot=28,property=Y_POSITION,value=-35);
			break;
		case 9:
			loadPic(slot=28,file=Q09);
			spriteCommand(slot=28,property=X_POSITION,value=-615);
			spriteCommand(slot=28,property=Y_POSITION,value=-35);
			break;
		case 10:
			loadPic(slot=28,file=Q10);
			spriteCommand(slot=28,property=X_POSITION,value=-615);
			spriteCommand(slot=28,property=Y_POSITION,value=-35);
			break;
		case 11:
			loadPic(slot=28,file=Q11);
			spriteCommand(slot=28,property=X_POSITION,value=-615);
			spriteCommand(slot=28,property=Y_POSITION,value=-35);
			break;
		case 12:
			loadPic(slot=28,file=Q12);
			spriteCommand(slot=28,property=X_POSITION,value=-615);
			spriteCommand(slot=28,property=Y_POSITION,value=-35);
			break;
		case 13:
			loadPic(slot=28,file=Q13);
			spriteCommand(slot=28,property=X_POSITION,value=-615);
			spriteCommand(slot=28,property=Y_POSITION,value=-35);
			break;
		case 14:
			loadPic(slot=28,file=Q14);
			spriteCommand(slot=28,property=X_POSITION,value=-615);
			spriteCommand(slot=28,property=Y_POSITION,value=-35);
			break;
		case 15:
			loadPic(slot=28,file=Q15);
			spriteCommand(slot=28,property=X_POSITION,value=-615);
			spriteCommand(slot=28,property=Y_POSITION,value=-35);
			break;
		case 16:
			loadPic(slot=28,file=Q16);
			spriteCommand(slot=28,property=X_POSITION,value=-615);
			spriteCommand(slot=28,property=Y_POSITION,value=-35);
			break;
		case 17:
			loadPic(slot=28,file=Q17);
			spriteCommand(slot=28,property=X_POSITION,value=-615);
			spriteCommand(slot=28,property=Y_POSITION,value=-35);
			break;
		case 18:
			loadPic(slot=28,file=Q18);
			spriteCommand(slot=28,property=X_POSITION,value=-615);
			spriteCommand(slot=28,property=Y_POSITION,value=-35);
			break;
		case 19:
			loadPic(slot=28,file=Q19);
			spriteCommand(slot=28,property=X_POSITION,value=-615);
			spriteCommand(slot=28,property=Y_POSITION,value=-35);
			break;
		case 20:
			loadPic(slot=28,file=Q20);
			spriteCommand(slot=28,property=X_POSITION,value=-615);
			spriteCommand(slot=28,property=Y_POSITION,value=-35);
			break;
	}
}

// Show coin counter for quiz and increment it
void sub_ce904(reg36) {
	hideDialogueWindow();
	waitFrames(10);
	
	removeSlot(slots={1, 3-4, 8-10, 27, 25, 24, 23, 12-16});
	loadPic(slot=3,file=WHITE);
	sub_c6366(reg9=2,reg10=0);

	hideDialogueWindow();
	waitFrames(10);
	
	removeSlot(slots={1, 3-4, 8-10, 27, 25, 24, 23, 12-16});
	loadPic(slot=3,file=BLACK);
	
	reg1 = reg36;
	
	spriteCommand(slot=29,property=X_POSITION,value=0);
	spriteCommand(slot=29,property=Y_POSITION,value=-200);
	
	switch (reg1) {
		case 0:
			loadPic(slot=28,file=Q00);
			spriteCommand(slot=28,property=X_POSITION,value=88);
			spriteCommand(slot=28,property=Y_POSITION,value=-223);
			break;
		case 1:
			loadPic(slot=28,file=Q01);
			spriteCommand(slot=28,property=X_POSITION,value=88);
			spriteCommand(slot=28,property=Y_POSITION,value=-223);
			break;
		case 2:
			loadPic(slot=28,file=Q02);
			spriteCommand(slot=28,property=X_POSITION,value=88);
			spriteCommand(slot=28,property=Y_POSITION,value=-223);
			break;
		case 3:
			loadPic(slot=28,file=Q03);
			spriteCommand(slot=28,property=X_POSITION,value=88);
			spriteCommand(slot=28,property=Y_POSITION,value=-223);
			break;
		case 4:
			loadPic(slot=28,file=Q04);
			spriteCommand(slot=28,property=X_POSITION,value=88);
			spriteCommand(slot=28,property=Y_POSITION,value=-223);
			break;
		case 5:
			loadPic(slot=28,file=Q05);
			spriteCommand(slot=28,property=X_POSITION,value=88);
			spriteCommand(slot=28,property=Y_POSITION,value=-223);
			break;
		case 6:
			loadPic(slot=28,file=Q06);
			spriteCommand(slot=28,property=X_POSITION,value=88);
			spriteCommand(slot=28,property=Y_POSITION,value=-223);
			break;
		case 7:
			loadPic(slot=28,file=Q07);
			spriteCommand(slot=28,property=X_POSITION,value=88);
			spriteCommand(slot=28,property=Y_POSITION,value=-223);
			break;
		case 8:
			loadPic(slot=28,file=Q08);
			spriteCommand(slot=28,property=X_POSITION,value=88);
			spriteCommand(slot=28,property=Y_POSITION,value=-223);
			break;
		case 9:
			loadPic(slot=28,file=Q09);
			spriteCommand(slot=28,property=X_POSITION,value=88);
			spriteCommand(slot=28,property=Y_POSITION,value=-223);
			break;
		case 10:
			loadPic(slot=28,file=Q10);
			spriteCommand(slot=28,property=X_POSITION,value=88);
			spriteCommand(slot=28,property=Y_POSITION,value=-223);
			break;
		case 11:
			loadPic(slot=28,file=Q11);
			spriteCommand(slot=28,property=X_POSITION,value=88);
			spriteCommand(slot=28,property=Y_POSITION,value=-223);
			break;
		case 12:
			loadPic(slot=28,file=Q12);
			spriteCommand(slot=28,property=X_POSITION,value=88);
			spriteCommand(slot=28,property=Y_POSITION,value=-223);
			break;
		case 13:
			loadPic(slot=28,file=Q13);
			spriteCommand(slot=28,property=X_POSITION,value=88);
			spriteCommand(slot=28,property=Y_POSITION,value=-223);
			break;
		case 14:
			loadPic(slot=28,file=Q14);
			spriteCommand(slot=28,property=X_POSITION,value=88);
			spriteCommand(slot=28,property=Y_POSITION,value=-223);
			break;
		case 15:
			loadPic(slot=28,file=Q15);
			spriteCommand(slot=28,property=X_POSITION,value=88);
			spriteCommand(slot=28,property=Y_POSITION,value=-223);
			break;
		case 16:
			loadPic(slot=28,file=Q16);
			spriteCommand(slot=28,property=X_POSITION,value=88);
			spriteCommand(slot=28,property=Y_POSITION,value=-223);
			break;
		case 17:
			loadPic(slot=28,file=Q17);
			spriteCommand(slot=28,property=X_POSITION,value=88);
			spriteCommand(slot=28,property=Y_POSITION,value=-223);
			break;
		case 18:
			loadPic(slot=28,file=Q18);
			spriteCommand(slot=28,property=X_POSITION,value=88);
			spriteCommand(slot=28,property=Y_POSITION,value=-223);
			break;
		case 19:
			loadPic(slot=28,file=Q19);
			spriteCommand(slot=28,property=X_POSITION,value=88);
			spriteCommand(slot=28,property=Y_POSITION,value=-223);
			break;
		case 20:
			loadPic(slot=28,file=Q20);
			spriteCommand(slot=28,property=X_POSITION,value=88);
			spriteCommand(slot=28,property=Y_POSITION,value=-223);
			break;
	}
	
	sub_c6366(reg9=2,reg10=0);
	
	waitFrames(120);
	playSFX(channel=0,file=umise_001,volume=180,single_play=1);
	loadPic(slot=27,file=M1);
	
	reg36 += 1;
	reg1 = reg36;

	switch (reg1) {
		case 0:
			loadPic(slot=29,file=Q00);
			spriteCommand(slot=29,property=X_POSITION,value=88);
			spriteCommand(slot=29,property=Y_POSITION,value=-223);
			break;
		case 1:
			loadPic(slot=29,file=Q01);
			spriteCommand(slot=29,property=X_POSITION,value=88);
			spriteCommand(slot=29,property=Y_POSITION,value=-223);
			break;
		case 2:
			loadPic(slot=29,file=Q02);
			spriteCommand(slot=29,property=X_POSITION,value=88);
			spriteCommand(slot=29,property=Y_POSITION,value=-223);
			break;
		case 3:
			loadPic(slot=29,file=Q03);
			spriteCommand(slot=29,property=X_POSITION,value=88);
			spriteCommand(slot=29,property=Y_POSITION,value=-223);
			break;
		case 4:
			loadPic(slot=29,file=Q04);
			spriteCommand(slot=29,property=X_POSITION,value=88);
			spriteCommand(slot=29,property=Y_POSITION,value=-223);
			break;
		case 5:
			loadPic(slot=29,file=Q05);
			spriteCommand(slot=29,property=X_POSITION,value=88);
			spriteCommand(slot=29,property=Y_POSITION,value=-223);
			break;
		case 6:
			loadPic(slot=29,file=Q06);
			spriteCommand(slot=29,property=X_POSITION,value=88);
			spriteCommand(slot=29,property=Y_POSITION,value=-223);
			break;
		case 7:
			loadPic(slot=29,file=Q07);
			spriteCommand(slot=29,property=X_POSITION,value=88);
			spriteCommand(slot=29,property=Y_POSITION,value=-223);
			break;
		case 8:
			loadPic(slot=29,file=Q08);
			spriteCommand(slot=29,property=X_POSITION,value=88);
			spriteCommand(slot=29,property=Y_POSITION,value=-223);
			break;
		case 9:
			loadPic(slot=29,file=Q09);
			spriteCommand(slot=29,property=X_POSITION,value=88);
			spriteCommand(slot=29,property=Y_POSITION,value=-223);
			break;
		case 10:
			loadPic(slot=29,file=Q10);
			spriteCommand(slot=29,property=X_POSITION,value=88);
			spriteCommand(slot=29,property=Y_POSITION,value=-223);
			break;
		case 11:
			loadPic(slot=29,file=Q11);
			spriteCommand(slot=29,property=X_POSITION,value=88);
			spriteCommand(slot=29,property=Y_POSITION,value=-223);
			break;
		case 12:
			loadPic(slot=29,file=Q12);
			spriteCommand(slot=29,property=X_POSITION,value=88);
			spriteCommand(slot=29,property=Y_POSITION,value=-223);
			break;
		case 13:
			loadPic(slot=29,file=Q13);
			spriteCommand(slot=29,property=X_POSITION,value=88);
			spriteCommand(slot=29,property=Y_POSITION,value=-223);
			break;
		case 14:
			loadPic(slot=29,file=Q14);
			spriteCommand(slot=29,property=X_POSITION,value=88);
			spriteCommand(slot=29,property=Y_POSITION,value=-223);
			break;
		case 15:
			loadPic(slot=29,file=Q15);
			spriteCommand(slot=29,property=X_POSITION,value=88);
			spriteCommand(slot=29,property=Y_POSITION,value=-223);
			break;
		case 16:
			loadPic(slot=29,file=Q16);
			spriteCommand(slot=29,property=X_POSITION,value=88);
			spriteCommand(slot=29,property=Y_POSITION,value=-223);
			break;
		case 17:
			loadPic(slot=29,file=Q17);
			spriteCommand(slot=29,property=X_POSITION,value=88);
			spriteCommand(slot=29,property=Y_POSITION,value=-223);
			break;
		case 18:
			loadPic(slot=29,file=Q18);
			spriteCommand(slot=29,property=X_POSITION,value=88);
			spriteCommand(slot=29,property=Y_POSITION,value=-223);
			break;
		case 19:
			loadPic(slot=29,file=Q19);
			spriteCommand(slot=29,property=X_POSITION,value=88);
			spriteCommand(slot=29,property=Y_POSITION,value=-223);
			break;
		case 20:
			loadPic(slot=29,file=Q20);
			spriteCommand(slot=29,property=X_POSITION,value=88);
			spriteCommand(slot=29,property=Y_POSITION,value=-223);
			break;
	}

	sub_c6366(reg9=2,reg10=0);
	waitFrames(200);
	
	removeSlot(slots={27-29});
	hideDialogueWindow();
	waitFrames(10);
	
	removeSlot(slots={1, 3-4, 8-10, 27, 25, 24, 23, 12-16});
	loadPic(slot=3,file=BLACK);
	sub_c6366(reg9=2,reg10=0);
	waitFrames(60);
}

// Show coin counter for quiz and increment it twice
void sub_cf0db(reg36) {
	hideDialogueWindow();
	waitFrames(10);
	
	removeSlot(slots={1, 3-4, 8-10, 27, 25, 24, 23, 12-16});
	loadPic(slot=3,file=WHITE);
	sub_c6366(reg9=2,reg10=0);
	
	hideDialogueWindow();
	waitFrames(10);
	
	removeSlot(slots={1, 3-4, 8-10, 27, 25, 24, 23, 12-16});
	loadPic(slot=3,file=BLACK);
	
	reg1 = reg36;
	
	spriteCommand(slot=29,property=X_POSITION,value=0);
	spriteCommand(slot=29,property=Y_POSITION,value=-200);
	
	switch (reg1) {
		case 0:
			loadPic(slot=28,file=Q00);
			spriteCommand(slot=28,property=X_POSITION,value=88);
			spriteCommand(slot=28,property=Y_POSITION,value=-223);
			break;
		case 1:
			loadPic(slot=28,file=Q01);
			spriteCommand(slot=28,property=X_POSITION,value=88);
			spriteCommand(slot=28,property=Y_POSITION,value=-223);
			break;
		case 2:
			loadPic(slot=28,file=Q02);
			spriteCommand(slot=28,property=X_POSITION,value=88);
			spriteCommand(slot=28,property=Y_POSITION,value=-223);
			break;
		case 3:
			loadPic(slot=28,file=Q03);
			spriteCommand(slot=28,property=X_POSITION,value=88);
			spriteCommand(slot=28,property=Y_POSITION,value=-223);
			break;
		case 4:
			loadPic(slot=28,file=Q04);
			spriteCommand(slot=28,property=X_POSITION,value=88);
			spriteCommand(slot=28,property=Y_POSITION,value=-223);
			break;
		case 5:
			loadPic(slot=28,file=Q05);
			spriteCommand(slot=28,property=X_POSITION,value=88);
			spriteCommand(slot=28,property=Y_POSITION,value=-223);
			break;
		case 6:
			loadPic(slot=28,file=Q06);
			spriteCommand(slot=28,property=X_POSITION,value=88);
			spriteCommand(slot=28,property=Y_POSITION,value=-223);
			break;
		case 7:
			loadPic(slot=28,file=Q07);
			spriteCommand(slot=28,property=X_POSITION,value=88);
			spriteCommand(slot=28,property=Y_POSITION,value=-223);
			break;
		case 8:
			loadPic(slot=28,file=Q08);
			spriteCommand(slot=28,property=X_POSITION,value=88);
			spriteCommand(slot=28,property=Y_POSITION,value=-223);
			break;
		case 9:
			loadPic(slot=28,file=Q09);
			spriteCommand(slot=28,property=X_POSITION,value=88);
			spriteCommand(slot=28,property=Y_POSITION,value=-223);
			break;
		case 10:
			loadPic(slot=28,file=Q10);
			spriteCommand(slot=28,property=X_POSITION,value=88);
			spriteCommand(slot=28,property=Y_POSITION,value=-223);
			break;
		case 11:
			loadPic(slot=28,file=Q11);
			spriteCommand(slot=28,property=X_POSITION,value=88);
			spriteCommand(slot=28,property=Y_POSITION,value=-223);
			break;
		case 12:
			loadPic(slot=28,file=Q12);
			spriteCommand(slot=28,property=X_POSITION,value=88);
			spriteCommand(slot=28,property=Y_POSITION,value=-223);
			break;
		case 13:
			loadPic(slot=28,file=Q13);
			spriteCommand(slot=28,property=X_POSITION,value=88);
			spriteCommand(slot=28,property=Y_POSITION,value=-223);
			break;
		case 14:
			loadPic(slot=28,file=Q14);
			spriteCommand(slot=28,property=X_POSITION,value=88);
			spriteCommand(slot=28,property=Y_POSITION,value=-223);
			break;
		case 15:
			loadPic(slot=28,file=Q15);
			spriteCommand(slot=28,property=X_POSITION,value=88);
			spriteCommand(slot=28,property=Y_POSITION,value=-223);
			break;
		case 16:
			loadPic(slot=28,file=Q16);
			spriteCommand(slot=28,property=X_POSITION,value=88);
			spriteCommand(slot=28,property=Y_POSITION,value=-223);
			break;
		case 17:
			loadPic(slot=28,file=Q17);
			spriteCommand(slot=28,property=X_POSITION,value=88);
			spriteCommand(slot=28,property=Y_POSITION,value=-223);
			break;
		case 18:
			loadPic(slot=28,file=Q18);
			spriteCommand(slot=28,property=X_POSITION,value=88);
			spriteCommand(slot=28,property=Y_POSITION,value=-223);
			break;
		case 19:
			loadPic(slot=28,file=Q19);
			spriteCommand(slot=28,property=X_POSITION,value=88);
			spriteCommand(slot=28,property=Y_POSITION,value=-223);
			break;
		case 20:
			loadPic(slot=28,file=Q20);
			spriteCommand(slot=28,property=X_POSITION,value=88);
			spriteCommand(slot=28,property=Y_POSITION,value=-223);
			break;
	}
	
	sub_c6366(reg9=2,reg10=0);
	
	waitFrames(120);
	playSFX(channel=0,file=umise_001,volume=180,single_play=1);
	loadPic(slot=27,file=M1);
	
	reg36 += 2; //double increment
	reg1 = reg36;
	
	switch (reg1) {
		case 0:
			loadPic(slot=29,file=Q00);
			spriteCommand(slot=29,property=X_POSITION,value=88);
			spriteCommand(slot=29,property=Y_POSITION,value=-223);
			break;
		case 1:
			loadPic(slot=29,file=Q01);
			spriteCommand(slot=29,property=X_POSITION,value=88);
			spriteCommand(slot=29,property=Y_POSITION,value=-223);
			break;
		case 2:
			loadPic(slot=29,file=Q02);
			spriteCommand(slot=29,property=X_POSITION,value=88);
			spriteCommand(slot=29,property=Y_POSITION,value=-223);
			break;
		case 3:
			loadPic(slot=29,file=Q03);
			spriteCommand(slot=29,property=X_POSITION,value=88);
			spriteCommand(slot=29,property=Y_POSITION,value=-223);
			break;
		case 4:
			loadPic(slot=29,file=Q04);
			spriteCommand(slot=29,property=X_POSITION,value=88);
			spriteCommand(slot=29,property=Y_POSITION,value=-223);
			break;
		case 5:
			loadPic(slot=29,file=Q05);
			spriteCommand(slot=29,property=X_POSITION,value=88);
			spriteCommand(slot=29,property=Y_POSITION,value=-223);
			break;
		case 6:
			loadPic(slot=29,file=Q06);
			spriteCommand(slot=29,property=X_POSITION,value=88);
			spriteCommand(slot=29,property=Y_POSITION,value=-223);
			break;
		case 7:
			loadPic(slot=29,file=Q07);
			spriteCommand(slot=29,property=X_POSITION,value=88);
			spriteCommand(slot=29,property=Y_POSITION,value=-223);
			break;
		case 8:
			loadPic(slot=29,file=Q08);
			spriteCommand(slot=29,property=X_POSITION,value=88);
			spriteCommand(slot=29,property=Y_POSITION,value=-223);
			break;
		case 9:
			loadPic(slot=29,file=Q09);
			spriteCommand(slot=29,property=X_POSITION,value=88);
			spriteCommand(slot=29,property=Y_POSITION,value=-223);
			break;
		case 10:
			loadPic(slot=29,file=Q10);
			spriteCommand(slot=29,property=X_POSITION,value=88);
			spriteCommand(slot=29,property=Y_POSITION,value=-223);
			break;
		case 11:
			loadPic(slot=29,file=Q11);
			spriteCommand(slot=29,property=X_POSITION,value=88);
			spriteCommand(slot=29,property=Y_POSITION,value=-223);
			break;
		case 12:
			loadPic(slot=29,file=Q12);
			spriteCommand(slot=29,property=X_POSITION,value=88);
			spriteCommand(slot=29,property=Y_POSITION,value=-223);
			break;
		case 13:
			loadPic(slot=29,file=Q13);
			spriteCommand(slot=29,property=X_POSITION,value=88);
			spriteCommand(slot=29,property=Y_POSITION,value=-223);
			break;
		case 14:
			loadPic(slot=29,file=Q14);
			spriteCommand(slot=29,property=X_POSITION,value=88);
			spriteCommand(slot=29,property=Y_POSITION,value=-223);
			break;
		case 15:
			loadPic(slot=29,file=Q15);
			spriteCommand(slot=29,property=X_POSITION,value=88);
			spriteCommand(slot=29,property=Y_POSITION,value=-223);
			break;
		case 16:
			loadPic(slot=29,file=Q16);
			spriteCommand(slot=29,property=X_POSITION,value=88);
			spriteCommand(slot=29,property=Y_POSITION,value=-223);
			break;
		case 17:
			loadPic(slot=29,file=Q17);
			spriteCommand(slot=29,property=X_POSITION,value=88);
			spriteCommand(slot=29,property=Y_POSITION,value=-223);
			break;
		case 18:
			loadPic(slot=29,file=Q18);
			spriteCommand(slot=29,property=X_POSITION,value=88);
			spriteCommand(slot=29,property=Y_POSITION,value=-223);
			break;
		case 19:
			loadPic(slot=29,file=Q19);
			spriteCommand(slot=29,property=X_POSITION,value=88);
			spriteCommand(slot=29,property=Y_POSITION,value=-223);
			break;
		case 20:
			loadPic(slot=29,file=Q20);
			spriteCommand(slot=29,property=X_POSITION,value=88);
			spriteCommand(slot=29,property=Y_POSITION,value=-223);
			break;
	}
	
	sub_c6366(reg9=2,reg10=0);
	waitFrames(200);
	
	removeSlot(slots={27-29});
	
	reg1 = reg36;
	if (reg1==17) {
		reg1=reg38;
	} else if (reg1==0) {
		openTrophy(28); // No hint
	}
	
	hideDialogueWindow();
	waitFrames(10);
	
	removeSlot(slots={1, 3-4, 8-10, 27, 25, 24, 23, 12-16});
	loadPic(slot=3,file=BLACK);
	sub_c6366(reg9=2,reg10=0);
	waitFrames(60);
}