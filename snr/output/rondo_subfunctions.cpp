/*
 * Reverse-engineered rondo subfunctions.
 * Encoding: UTF-8
 *
 * Copyright (c) 2013 Umineko Project
 *
 * This document is considered confidential and proprietary,
 * and may not be reproduced or transmitted in any form 
 * in whole or in part, without the express written permission
 * of Umineko Project.
 */

void sub_91ca6(reg9,reg10) {
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

//PS3 eye function analogue (broken glass 1)
void sub_92a4f(reg20) {
	spriteCommand(slot=11,property=PAUSE_LAYER,value=1);
	spriteCommand(slot=7,property=PAUSE_LAYER,value=1);
	
	hideDialogueWindow();
	waitFrames(10);
	
	loadPic(slot=28,file=HANA1);
	spriteCommand(slot=28,property=MULTIPLIER_ALPHA,value=150);
	sub_91ca6(reg9=7,reg10=0u);
	
	loadPic(slot=29,file=CINEMA_LOGO);
	spriteCommand(slot=29,property=X_POSITION,value=500);
	spriteCommand(slot=29,property=Y_POSITION,value=-50);
	sub_91ca6(reg9=5,reg10=0u);
	
	waitFrames(60);
	
	loadPic(slot=3,file=BLACK);
	spriteCommand(slot=3,property=PROP_5_MONOCRO,value=reg20);
	loadPic(slot=27,file=BLACK);
	loadPic(slot=28,file=BLACK);
	sub_91ca6(reg9=2,reg10=0u);
	
	loopSFXFade(frames=0);
	musicFade(frames=0);
	removeSlot(slots={1...30});
	
	return;
}

//PS3 eye function analogue (broken glass 2)
void sub_928a3(reg20) {
	spriteCommand(slot=11,property=PAUSE_LAYER,value=1);
	spriteCommand(slot=7,property=PAUSE_LAYER,value=1);
	
	hideDialogueWindow();
	waitFrames(10);
	
	loadPic(slot=27,file=BLACK);
	spriteCommand(slot=27,property=MULTIPLIER_ALPHA,value=150);
	sub_91ca6(reg9=1,reg10=0u);
	
	loadPic(slot=28,file=WARE);
	musicFade(frames=0);
	loopSFXFade(frames=0);
	playSFX(channel=8,file=umise_1006,volume=180,single_play=1); //padding=0?
	sub_91ca6(reg9=1,reg10=0u);
	
	waitFrames(60);
	
	loadPic(slot=29,file=CINEMA_LOGO);
	spriteCommand(slot=29,property=X_POSITION,value=500);
	spriteCommand(slot=29,property=Y_POSITION,value=-50);
	sub_91ca6(reg9=5,reg10=0u);
	
	waitFrames(60);
	
	loadPic(slot=3,file=BLACK);
	spriteCommand(slot=3,property=PROP_5_MONOCRO,value=reg20);
	loadPic(slot=27,file=BLACK);
	loadPic(slot=28,file=BLACK);
	sub_91ca6(reg9=2,reg10=0u);
	
	removeSlot(slots={1...30});
	
	return;
}

//PS3 eye function analogue (clocks)
void sub_9239f(reg28,reg29,reg30,reg31,reg32, reg13,reg14) {
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
	printCommand(130,frames=30);
	
	spriteCommand(slot=18,property=ROTATION_ANGLE,value=r3,frames=24,equation=TYPE_64);
	spriteCommand(slot=19,property=ROTATION_ANGLE,value=r4,frames=2,equation=TYPE_64);
	
	if (reg31==1) {
		playSFX(channel=20,file=umilse_1051,volume=180,single_play=0); //padding=0?
	} else {
		playSFX(channel=20,file=umilse_1050,volume=180,single_play=0); //padding=0?
	}
	
	spriteCommandWait(slot=18,property=11);
	spriteCommandWait(slot=19,property=11);
	
	loopChannelFade(channel=20,frames=0);
	
	if (reg32 != 0) {
		playSFX(channel=2,file=umise_1052,volume=180,single_play=1); //padding=0?
		waitChannel(channel=2);
	}
	
	waitFrames(100);
	
	return;
}

//PS3 style whirl effect
void sub_92bc6() {
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

//Display 'death' counter, reg27 the current counter increased by one every fn call
void sub_924c3(reg27) {
	reg27++;
	int r1 = reg27;
	
	if (reg27 >= 20) {
		picLoad(slot=29,file=2);
		spriteCommand(slot=29,property=X_POSITION,value=700);
		spriteCommand(slot=29,property=Y_POSITION,value=-900);
		spriteCommand(slot=29,property=UNK_PROPERTY_ZERO,value=26);
		r1 = reg27-20;
	} else if (reg27 >= 10) {
		picLoad(slot=29,file=1);
		spriteCommand(slot=29,property=X_POSITION,value=700);
		spriteCommand(slot=29,property=Y_POSITION,value=-900);
		spriteCommand(slot=29,property=UNK_PROPERTY_ZERO,value=26);
		r1 = reg27-10;
	}
	
	playSFX(channel=0,file=umise_070,volume=180,single_play=1); //padding=0?
	
	//r1 = {0...9}
	picLoad(slot=28,file=r1);

	spriteCommand(slot=28,property=X_POSITION,value=800);
	spriteCommand(slot=28,property=Y_POSITION,value=-900);
	spriteCommand(slot=28,property=UNK_PROPERTY_ZERO,value=26);
	sub_91ca6(reg9=1,reg10=0);
	
	return(reg27);
}




