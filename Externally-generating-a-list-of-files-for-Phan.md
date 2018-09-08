Some uses of Phan may involve passing it a list of files instead of using `.phan/config.php`

You can pass the files to be analyzed to Phan on the command-line, but with a large code base, you'll want to create a file that lists all files and filters out junk to make your life easier.

One way to generate this file list would be to create a file `.phan/bin/mkfilelist` (like [Phan's](https://github.com/phan/phan/blob/master/.phan/bin/mkfilelist)) with the following contents.

```sh
#!/bin/bash

if [[ -z $WORKSPACE ]]
then
    export WORKSPACE=~/path/to/code/src
fi

cd $WORKSPACE

JUNK=/var/tmp/junk.txt

for dir in \
    src \
    vendor/path/to/project
do
    if [ -d "$dir" ]; then
        find $dir -name '*.php' >> $JUNK
    fi
done

cat $JUNK | \
    grep -v "junk_file.php" | \
    grep -v "junk/directory.php" | \
    awk '!x[$0]++'

rm $JUNK
```

You can then run `./.phan/bin/mkfilelist > files`. Take a look at [Phan's file list generator](https://github.com/phan/phan/blob/master/.phan/bin/mkfilelist) to see an example.

With this, you can now run `phan -f files` to run an analysis of your code base.
(If `.phan/config.php` exists in the working directory, `phan -f files` will use configuration settings from that file, but the files from that list)