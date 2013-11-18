WordPress : Medias Remove Accents Tools (plugin + CLI)
===============================

Remove accents for existing medias on WordPress.

## 1. Install the plugin or mu-plugin

It allows you to remove accents for new media!

Filename : wordpress-media-remove-accents.php

## 2. You want to remove accents for existing media?

### 2.1 Build replacement list

`php-cli cli/1-build-list.php`

### 2.2 Prepare DBSR config file

`php-cli cli/2-prepare-DBSR-config-file.php`

### 2.2 Rename all files on filesystem

`php-cli cli/2-rename-files.php`

### 2.2 Execution search/replace script on database

`php-cli DBSearchReplace-CLI.php --file ../data/dbsr-config.json`
