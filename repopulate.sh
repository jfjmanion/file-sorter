#!/bin/bash
rm -fr ./files_to_move
cp -vr ./files_to_move-copy ./files_to_move
chmod -R 0777 ../file-sorter
