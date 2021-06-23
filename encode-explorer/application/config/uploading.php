<?php

/*
 * UPLOADING
 */

//
// List of directories where users are allowed to upload.
// For example: $_CONFIG['upload_dirs'] = array("./myuploaddir1/", "./mydir/upload2/");
// The path should be relative to the main directory, start with "./" and end with "/".
// All the directories below the marked ones are automatically included as well.
// If the list is empty (default), all directories are open for uploads, given that the password has been set.
// Default: $_CONFIG['upload_dirs'] = array();
//
$_CONFIG['upload_dirs'] = array();

//
// MIME type that are allowed to be uploaded.
// For example, to only allow uploading of common image types, you could use:
// $_CONFIG['upload_allow_type'] = array("image/png", "image/gif", "image/jpeg");
// Default: $_CONFIG['upload_allow_type'] = array();
//
$_CONFIG['upload_allow_type'] = array();

//
// File extensions that are not allowed for uploading.
// For example: $_CONFIG['upload_reject_extension'] = array("php", "html", "htm");
// Default: $_CONFIG['upload_reject_extension'] = array();
//
$_CONFIG['upload_reject_extension'] = array("php", "php2", "php3", "php4", "php5", "phtml");

//
// By default, apply 0755 permissions to new directories
//
// The mode parameter consists of three octal number components specifying
// access restrictions for the owner, the user group in which the owner is
// in, and to everybody else in this order.
//
// See: https://php.net/manual/en/function.chmod.php
//
// Default: $_CONFIG['new_dir_mode'] = 0755;
//
$_CONFIG['new_dir_mode'] = 0755;

//
// By default, apply 0644 permissions to uploaded files
//
// The mode parameter consists of three octal number components specifying
// access restrictions for the owner, the user group in which the owner is
// in, and to everybody else in this order.
//
// See: https://php.net/manual/en/function.chmod.php
//
// Default: $_CONFIG['upload_file_mode'] = 0644;
//
$_CONFIG['upload_file_mode'] = 0644;

?>