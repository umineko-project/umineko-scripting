#!/bin/sh

#
# Umineko Project update manager invocator
# Encoding: UTF-8
#
# Copyright (c) 2011-2019 Umineko Project
#
# This document is considered confidential and proprietary,
# and may not be reproduced or transmitted in any form 
# in whole or in part, without the express written permission
# of Umineko Project.
#

if [ "$UMINEKO_BASE" = "" ]; then
  echo "Missing UMINEKO_BASE!"
  exit 1
fi

if [ "$UMINEKO_SCRIPTING" = "" ]; then
  echo "Missing UMINEKO_SCRIPTING!"
  exit 1
fi

cd "$UMINEKO_SCRIPTING/update-manager"

export UMINEKO_FILES="$UMINEKO_BASE/arcs"
export UMINEKO_UPDATE="$UMINEKO_BASE/full_update"

export ARCHIVE_PREFIX="$UMINEKO_UPDATE/extra"
export TEMP_UPDATE_FOLDER=/tmp/umineko
export OUT_DIR=~/Desktop

export LAST_EPISODE=$(cat "$UMINEKO_SCRIPTING/current/last")
export LAST_EPISODE_PT=$(cat "$UMINEKO_SCRIPTING/current/last_pt")
export LAST_EPISODE_CN=$(cat "$UMINEKO_SCRIPTING/current/last_cn")
export LAST_EPISODE_IDN=$(cat "$UMINEKO_SCRIPTING/current/last_idn")

# 1. To generate the latest verification file run:

php update-manager.php size "$UMINEKO_FILES" "$UMINEKO_SCRIPTING/misc/game.hash"
php update-manager.php size "$UMINEKO_FILES" "$UMINEKO_FILES/game.hash"

# 2. To make an extra update run:

php update-manager.php hash "$UMINEKO_FILES" Current.hash 
php update-manager.php verify Base.hash Current.hash Update.info json
php update-manager.php update Update.info "$UMINEKO_FILES" "$TEMP_UPDATE_FOLDER" "$ARCHIVE_PREFIX"

# 3. To get the script for developers run:

php update-manager.php dscript "$OUT_DIR/en.txt" "$UMINEKO_SCRIPTING" en
php update-manager.php dscript "$OUT_DIR/ru.txt" "$UMINEKO_SCRIPTING" ru
php update-manager.php dscript "$OUT_DIR/pt.txt" "$UMINEKO_SCRIPTING" pt
php update-manager.php dscript "$OUT_DIR/cn.txt" "$UMINEKO_SCRIPTING" cn

# 4. To get the scripts for testers run:

php update-manager.php script "$OUT_DIR/en.txt" "$OUT_DIR/en.file" $LAST_EPISODE
php update-manager.php script "$OUT_DIR/ru.txt" "$OUT_DIR/ru.file" $LAST_EPISODE
php update-manager.php script "$OUT_DIR/pt.txt" "$OUT_DIR/pt.file" $LAST_EPISODE_PT
php update-manager.php script "$OUT_DIR/cn.txt" "$OUT_DIR/cn.file" $LAST_EPISODE_CN
