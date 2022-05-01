#!/bin/bash
git checkout master
echo -n '23.0.4' > version
git add -A
git commit -m `cat version`
git push origin master
git tag `cat version`
git push origin `cat version` -f