<?php

/*
 * Copyright (c) 2008-2016 Guillaume Lelarge <guillaume@lelarge.info>
 *
 * Permission to use, copy, modify, and distribute this software for any
 * purpose with or without fee is hereby granted, provided that the above
 * copyright notice and this permission notice appear in all copies.
 *
 * THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES
 * WITH REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR
 * ANY SPECIAL, DIRECT, INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES
 * WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN
 * ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF
 * OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE.
 */


if (strlen($outputdir) > 0 && $g_alldatabases) {
  $outputdir = $outputdir.'/'.$PGDATABASE.'_snap_'.date('Ymd');
}
else if (strlen($outputdir) == 0 || $g_alldatabases) {
  $outputdir = $PGDATABASE.'_snap_'.date('Ymd');
}

// if the directory doesn't exist, we create it
if (file_exists($outputdir)) {
  if ($g_deleteifexists) {
    // removing js folder if it exists
    if (file_exists($outputdir.'/js')) {
      $handle = opendir($outputdir.'/js');
      for (;false !== ($file = readdir($handle));)
        if($file != "." && $file != "..")
          unlink($outputdir.'/js/'.$file);
      closedir($handle);
      rmdir($outputdir.'/js');
    }
    // removing root folder contents
    $handle = opendir($outputdir);
    for (;false !== ($file = readdir($handle));)
      if($file != "." && $file != "..")
        unlink($outputdir.'/'.$file);
    closedir($handle);
  } else {
    $files = scandir($outputdir);
    if (count($files) > 2) {
      die ("Directory $outputdir already here and not empty!\n");
    }
  }
} else {
  mkdir($outputdir, octdec($outputdirmode), true);
}

echo "Connecting to $PGDATABASE database...\n";
include 'lib/connect.php';

$query = "SHOW server_version";
$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}
if ($row = pg_fetch_array($rows)) {
  $tmp = explode(".", $row['server_version']);
  $g_version = $tmp[0].$tmp[1];
}

$query = "SHOW data_directory";
$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}
if ($row = pg_fetch_array($rows)) {
  $g_datadirectory = $row['data_directory'];
} else {
  $g_datadirectory = $PG_DATA;
}

$query = 'SELECT rolsuper FROM pg_roles WHERE rolname=current_user';
$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}
if ($row = pg_fetch_array($rows)) {
  $g_superuser = $row['rolsuper'] == 't';
}

if (!$queriesinlogs && $g_superuser) {
  if ($g_version == 74) {
    $query = "SET log_statement TO off; SET log_duration TO off; SET log_min_duration_statement TO -1;";
  } else {
    $query = "SET log_statement TO 'none'; SET log_duration TO off; SET log_min_duration_statement TO -1;";
  }
  pg_query($connection, $query);
}

echo "Adding some HTML files...\n";
// javascript
copy($PGSNAP_ROOT_PATH.'external/jquery-1.2.3.js',
     $outputdir.'/jquery-1.2.3.js');
copy($PGSNAP_ROOT_PATH.'external/jquery.tablesorter.js',
     $outputdir.'/jquery.tablesorter.js');
copy($PGSNAP_ROOT_PATH.'external/flotr2.min.js',
     $outputdir.'/flotr2.js');
// css
copy($PGSNAP_ROOT_PATH.'template/fixed.css', $outputdir.'/fixed.css');
copy($PGSNAP_ROOT_PATH.'template/global.css', $outputdir.'/global.css');
copy($PGSNAP_ROOT_PATH.'template/graphs.css', $outputdir.'/graphs.css');
copy($PGSNAP_ROOT_PATH.'template/layout.css', $outputdir.'/layout.css');
copy($PGSNAP_ROOT_PATH.'template/text.css', $outputdir.'/text.css');
copy($PGSNAP_ROOT_PATH.'template/navigation.css', $outputdir.'/navigation.css');
copy($PGSNAP_ROOT_PATH.'template/table.css', $outputdir.'/table.css');
copy($PGSNAP_ROOT_PATH.'template/iefixes.css', $outputdir.'/iefixes.css');
// png
copy($PGSNAP_ROOT_PATH.'images/check-off.png', $outputdir.'/check-off.png');
copy($PGSNAP_ROOT_PATH.'images/check-on.png', $outputdir.'/check-on.png');
copy($PGSNAP_ROOT_PATH.'images/danger.png', $outputdir.'/danger.png');
copy($PGSNAP_ROOT_PATH.'images/important.png', $outputdir.'/important.png');
copy($PGSNAP_ROOT_PATH.'images/tip.png', $outputdir.'/tip.png');
copy($PGSNAP_ROOT_PATH.'images/warning.png', $outputdir.'/warning.png');

copy($PGSNAP_ROOT_PATH.'images/hdr_left.png', $outputdir.'/hdr_left.png');
copy($PGSNAP_ROOT_PATH.'images/hdr_right.png', $outputdir.'/hdr_right.png');
copy($PGSNAP_ROOT_PATH.'images/bg_hdr.png', $outputdir.'/bg_hdr.png');
copy($PGSNAP_ROOT_PATH.'images/blt_blu_arrow.png', $outputdir.'/blt_blu_arrow.png');
copy($PGSNAP_ROOT_PATH.'images/blt_gry_arrow.png', $outputdir.'/blt_gry_arrow.png');
copy($PGSNAP_ROOT_PATH.'images/box_bottom.gif', $outputdir.'/box_bottom.gif');
copy($PGSNAP_ROOT_PATH.'images/box_top.gif', $outputdir.'/box_top.gif');
copy($PGSNAP_ROOT_PATH.'images/feature_bl.gif', $outputdir.'/feature_bl.gif');
copy($PGSNAP_ROOT_PATH.'images/feature_br.gif', $outputdir.'/feature_br.gif');
copy($PGSNAP_ROOT_PATH.'images/feature_tl.gif', $outputdir.'/feature_tl.gif');
copy($PGSNAP_ROOT_PATH.'images/feature_tr.gif', $outputdir.'/feature_tr.gif');
copy($PGSNAP_ROOT_PATH.'images/hdr_fill.png', $outputdir.'/hdr_fill.png');
copy($PGSNAP_ROOT_PATH.'images/nav_fill.png', $outputdir.'/nav_fill.png');
copy($PGSNAP_ROOT_PATH.'images/nav_tbl_btm_lft.png', $outputdir.'/nav_tbl_btm_lft.png');
copy($PGSNAP_ROOT_PATH.'images/nav_tbl_btm.png', $outputdir.'/nav_tbl_btm.png');
copy($PGSNAP_ROOT_PATH.'images/nav_tbl_btm_rgt.png', $outputdir.'/nav_tbl_btm_rgt.png');
copy($PGSNAP_ROOT_PATH.'images/nav_tbl_top_lft.png', $outputdir.'/nav_tbl_top_lft.png');
copy($PGSNAP_ROOT_PATH.'images/nav_tbl_top.png', $outputdir.'/nav_tbl_top.png');
copy($PGSNAP_ROOT_PATH.'images/nav_tbl_top_rgt.png', $outputdir.'/nav_tbl_top_rgt.png');
copy($PGSNAP_ROOT_PATH.'images/usr_tbl_btm.png', $outputdir.'/usr_tbl_btm.png');
copy($PGSNAP_ROOT_PATH.'images/usr_tbl_top.png', $outputdir.'/usr_tbl_top.png');
copy($PGSNAP_ROOT_PATH.'images/nav_lft.png', $outputdir.'/nav_lft.png');
copy($PGSNAP_ROOT_PATH.'images/nav_rgt.png', $outputdir.'/nav_rgt.png');
// image variables
$image['f'] = '<img src="check-off.png" title="Off" alt="Off"/>';
$image['t'] = '<img src="check-on.png" title="On" alt="On"/>';
$image['off'] = '<img src="check-off.png" title="Off" alt="Off"/>';
$image['on'] = '<img src="check-on.png" title="On" alt="On"/>';
// kind variables
$kind['r'] = 'HEAP Table';
$kind['t'] = 'TOAST Table';
$kind['i'] = 'Index';
$kind['S'] = 'Sequence';
// replident variables
$replident['d'] = 'default';
$replident['n'] = 'nothing';
$replident['f'] = 'all';
$replident['i'] = 'index';

echo "Getting Misc informations...\n";
include 'lib/getmodules.php';
include 'lib/getcomments.php';
include 'lib/navigate.php';
include 'lib/links.php';

echo "Getting General informations...\n";
include 'lib/ver.php';
include 'lib/pgconfig.php';
include 'lib/pgcontroldata.php';
include 'lib/param.php';
include 'lib/nondefaultparam.php';
if ($g_version > '80' && $g_version < '84') {
  include 'lib/paramautovac.php';
}
if ($g_files['pg_hba.conf']) {
  include 'lib/contents_pghba.php';
}
if ($g_files['pg_ident.conf']) {
  include 'lib/contents_pgident.php';
}
if ($g_files['recovery.conf']) {
  include 'lib/contents_recovery.php';
}
if ($g_superuser && $g_version > '74'
   && (! $g_withoutlastlog)
   && (!strcmp($g_settings['log_destination'], 'stderr')
         || !strcmp($g_settings['log_destination'], 'csvlog'))
   && ((array_key_exists('redirect_stderr', $g_settings)
          && !strcmp($g_settings['redirect_stderr'], 'on'))
         || (array_key_exists('logging_collector', $g_settings)
          && !strcmp($g_settings['logging_collector'], 'on'))) ) {
  include 'lib/lastlogfile.php';
}

echo "Getting Global Informations...\n";
include 'lib/bases.php';
if ($g_version > '80') {
  include 'lib/graph_dbsize.php';
}
if ($g_pgbuffercache) {
  include 'lib/databasesincache.php';
} else {
  echo "  pg_buffercache unavailable!\n";
}
if ($g_version > '80') {
  include 'lib/roles.php';
} else {
  include 'lib/users.php';
}
include 'lib/user1.php';
include 'lib/user2.php';
if ($g_version >= '90') {
  include 'lib/dbrolesettings.php';
}
if ($g_version > '74') {
  include 'lib/tablespaces.php';
  if ($g_superuser && $g_version > '80') {
    include 'lib/graph_tblspcsize.php';
  }
  include 'lib/tblspc1.php';
}
if ($g_superuser && $g_version >= '90') {
  include 'lib/largeobjects.php';
} elseif ($g_version < '90') {
  include 'lib/largeobjects.php';
}
if ($g_superuser && $g_version > '80') {
  include 'lib/xlog.php';
}
if ($g_version >= '94') {
  include 'lib/replslots.php';
}

echo "Getting Database Informations...\n";
include 'lib/schemas.php';
if ($g_version >= '90') {
  include 'lib/defaultacl.php';
}
include 'lib/tables.php';
if ($g_version > '80') {
  include 'lib/graph_tablesize.php';
}
if ($g_pgbuffercache) {
  include 'lib/tablesincache.php';
} else {
  echo "  pg_buffercache unavailable!\n";
}
if ($g_pgstattuple) {
  include 'lib/fragmentedtables.php';
} else {
  echo "  pgstattuple unavailable!\n";
}
include 'lib/tableswithoutpkey.php';
include 'lib/tableswith5+indexes.php';
include 'lib/clusteredtables.php';
include 'lib/fkconstraints.php';
include 'lib/fkwithoutindexes.php';
include 'lib/views.php';
if ($g_version > '92') {
  include 'lib/materializedviews.php';
}
include 'lib/sequences.php';
include 'lib/sequences2.php';
include 'lib/indexes.php';
if ($g_version > '80') {
  include 'lib/graph_indexsize.php';
}
if ($g_version > '82') {
  include 'lib/uselessindexes.php';
}
if ($g_version > '80') {
  include 'lib/indexesbiggerthantables.php';
}
if ($g_pgstatindex) {
  include 'lib/fragmentedindexes.php';
} else {
  echo "  pgstattuple on indexes unavailable!\n";
}
if ($g_version > '80') {
  include 'lib/relationsbloat.php';
  include 'lib/graph_relsize.php';
}
include 'lib/languages.php';
include 'lib/functions.php';
if ($g_version >= '91') {
  include 'lib/extensions.php';
}

echo "Getting Current Activities Informations...\n";
include 'lib/sessionsinfo.php';
include 'lib/activities.php';
include 'lib/nonidleprocesses.php';
if ($g_version >= '91') {
  include 'lib/replication.php';
}
if ($g_version >= '82') {
  include 'lib/cursors.php';
  include 'lib/preparedstatements.php';
  include 'lib/preparedxacts.php';
}
include 'lib/locks.php';
if ($g_version > '80') {
  include 'lib/exclusivelocks.php';
}
if ($g_version < '90') {
  include 'lib/listeners.php';
}

echo "Getting Statistical Informations...\n";
if ($g_version >= '83') {
  include 'lib/bgwriter.php';
}
if ((array_key_exists('stats_block_level', $g_settings)
     && !strcmp($g_settings['stats_block_level'], 'on'))
   ||
   (array_key_exists('track_counts', $g_settings)
     && !strcmp($g_settings['track_counts'], 'on'))) {
  include 'lib/cachehitratio.php';
}
include 'lib/stat_databases.php';
if ($g_version >= '91') {
  include 'lib/stat_databases_conflicts.php';
}
include 'lib/stat_tables.php';
include 'lib/statio_tables.php';
if ($g_version >= '82') {
  include 'lib/lastvacuumtables.php';
  include 'lib/lastanalyzetables.php';
}
if ($g_fsmrelations) {
  include 'lib/fsmrelations.php';
}
if ($g_fsmpages) {
  include 'lib/fsmpages.php';
}
include 'lib/stat_indexes.php';
include 'lib/statio_indexes.php';
include 'lib/statio_sequences.php';
if ($g_version >= '84') {
  include 'lib/stat_functions.php';
}
if ($g_version >= '94') {
  include 'lib/stat_archiver.php';
}

echo "Getting Tools Informations...\n";
if ($g_pgpool) {
  include 'lib/pgpool.php';
} else {
  echo "  pgPool unavailable!\n";
}

pg_close($connection);
?>
