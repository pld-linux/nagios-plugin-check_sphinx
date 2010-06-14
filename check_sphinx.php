#!/usr/bin/php
<?php
/* vim: set encoding=utf-8: */
/*
 *  Nagios plugin to check Sphinx search engine status.
 *  Copyright (C) 2010  Elan Ruusamäe <glen@delfi.ee>
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @(#) $Id$
 */

define('PROGRAM', basename(array_shift($argv)));

// loads from same dir as program
require_once 'utils.php';

// No pecl class, try php version
if (!class_exists('SphinxClient')) {
	// loads from php include_path
	require_once 'sphinxapi.php';
}

function usage() {
	echo PROGRAM,
" [-H HOSTNAME] [-p PORT] [-s SEARCHSTRING] [-i INDEXNAME] [-t TIMEOUT]
";
	exit(STATE_UNKNOWN);
}

$default_opt = array(
	'H' => 'localhost',
	'p' => 9312,
	'i' => '*',
	't' => 10,
	'd' => null,
);
$opt = array_merge($default_opt, getopt("H:p:s:i:t:d"));

if (empty($opt['H']) || empty($opt['s'])) {
	usage();
}

$sphinx = new SphinxClient();
$sphinx->SetServer($opt['H'], $opt['p']);
#$sphinx->setMatchMode(SPH_MATCH_ANY);
$sphinx->SetConnectTimeout($opt['t']);
$sphinx->setMaxQueryTime($opt['t']);
$res = $sphinx->Query($opt['s'], $opt['i']);

if ($msg = $sphinx->GetLastWarning()) {
	echo "WARINNG: ", $msg, "\n";
	exit(STATE_WARNING);
}
if ($msg = $sphinx->GetLastError()) {
	echo "ERROR: ", $msg, "\n";
	exit(STATE_CRITICAL);
}

if (isset($opt['d'])) {
	print_r($res);
}

if ($res['total']) {
	printf("OK: Found %d documents in %.3f secs\n", $res['total'], $res['time']);
	exit(STATE_OK);
} else {
	printf("WARNING: Found %d documents in %.3f secs\n", $res['total'], $res['time']);
	exit(STATE_WARNING);
}
