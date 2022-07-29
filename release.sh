#!/bin/bash
git checkout master
echo -n '23.0.7' > version
git add -A
git commit -m `cat version`
git push origin master
git tag `cat version` -f
git push origin `cat version` -f