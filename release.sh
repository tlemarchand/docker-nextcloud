#!/bin/bash
git checkout master
curl -s https://api.github.com/repos/nextcloud/server/releases/latest | jq -j -r '.name'| cut -c 2- > version
git add -A
git commit -m "Version update"
git push origin master
git tag `cat version` -f
git push origin `cat version` -f
