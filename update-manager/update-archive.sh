#!/bin/bash

#
# Umineko Project differential update archive generator
# Encoding: UTF-8
#
# Copyright (c) 2011-2019 Umineko Project
#
# This document is considered confidential and proprietary,
# and may not be reproduced or transmitted in any form 
# in whole or in part, without the express written permission
# of Umineko Project.
#

if [ "$#" != "3" ];then
	echo "Usage: ./update-archive.sh files.txt src dst"
	exit 1
fi

files="$1"
src="$2"
dst="$3"

while IFS='' read -r file || [[ -n "$file" ]]; do
    dstdir=$(dirname "$file")
    mkdir -p "$dst/$dstdir"
    cp "$src/$file" "$dst/$file"
done < "$files"

pushd "$dst"
zip -qry ../update.zip *
popd

exit 0
