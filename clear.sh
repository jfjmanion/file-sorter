#!/bin/bash
rm -fr ./series/
mkdir ./series/
rm -fr ./movies/
mkdir ./movies/
rm -fr ./music/
mkdir ./music/
rm -fr ./manual_sort/
mkdir ./manual_sort/
rm -fr ./files_to_move
cp -vr ./files_to_move-copy ./files_to_move

chmod -R 0777 ../file-sorter

