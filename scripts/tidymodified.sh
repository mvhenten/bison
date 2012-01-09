#!/bin/sh
THIS_DIRNAME=`dirname $0`;
for f in `git status -s | grep -E '^.M.+\.php' | sed -e s/^.M//g`;
    do echo "php ${THIS_DIRNAME}/phptidy.php replace $f" && OK=`php ${THIS_DIRNAME}/phptidy.php replace $f`;
done;
