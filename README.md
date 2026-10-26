# Emoji Parse

In 2017, I wanted to find a way to use PHP to read the text files from the Unicode website which list the emojis for each new release.

This was the file I was first looking at: https://unicode.org/Public/emoji/5.0/emoji-test.txt

I found someone had worked out how to do that - they kindly put their code on Github:

https://github.com/lcherone/emoji-parse

I took that as a starting point for my own version (which is no doubt very clunky and basic). The code I have used is probably very inefficient and riddled with glaring mistakes and so on, but it works for my own basic hobbyist purposes. I didn't use any AI to write the code as I can't understand the output anyway, even if it is a lot more efficient.

This repository contains the code I have used to do the following:

- Create emoji database and tables using SQL files
- PHP code to read emoji unicode text file from unicode website and load it into the database
- PHP Code to create javascript files from the database content for use in a range of emoji browsers I have created - which are here
  - https://zzalsjr8.github.io/projects/emoji-browsers/index.html
- Store the output of the above in a range of files (javascript, csv and xlsx)

## Content

The content on this repository is made up of the following:

### SQL files

#### 01-emojis-setup.sql

- Create `emojis` database, and `emojis` and `categories` tables.

#### 02-emoji-data-analysis

- Various queries to analyse the emoji data (for info, you can run this after the database has been populated).

#### 03-emoji-data-full-database.sql

- A full backup of a populated database (as of Emoji Version 18), in case you don't want to mess around populating the database yourself.

### PHP files

#### step-01-parse-emoji.php

- File to read through current unicode emoji text file (https://www.unicode.org/Public/emoji/latest/emoji-test.txt) and load it into the database (assumes you have already created the database and tables as per file `01-emojis-setup.sql`.

#### step-02-setup-create-javascript-data.php

- File to read through emoji database data and create a file called `emojis.js` which is used in all of the [emoji browser](https://zzalsjr8.github.io/projects/emoji-browsers/index.html) pages apart from the treeview version.

#### step-03-setup-create-javascript-quercus-data.php

- File to read through emoji database data and create a file called `emojis-quercus.js` which is used in the [treeview emoji browser](https://zzalsjr8.github.io/projects/emoji-browsers/emoji-browser-treeview.html).

### Data files

- 04-emoji-data.csv
- 05-emoji-data.xlsx
- emojis.js
- emojis-quercus.js
