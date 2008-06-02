<?php

/*
 * Copyright (c) 2008 Guillaume Lelarge <guillaume@lelarge.info>
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

$buffer = $navigate_stats.'
<div id="pgContentWrap">

<h1>Statistical tables list</h1>
';

$query = "SELECT
  schemaname,
  relname,
  seq_scan,
  seq_tup_read,
  idx_scan,
  idx_tup_fetch,
  n_tup_ins,
  n_tup_upd,
  n_tup_del,
  n_tup_hot_upd,
  n_live_tup,
  n_dead_tup,
  last_vacuum,
  last_autovacuum,
  last_analyze,
  last_autoanalyze
FROM pg_stat_all_tables
ORDER BY schemaname, relname";

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

$buffer .= '<div class="tblBasic">

<table border="0" cellpadding="0" cellspacing="0" class="tblBasicGrey">
<tr>
  <th class="colFirst">Schema name</th>
  <th class="colMid">Table name</th>
  <th class="colMid">seq_scan</th>
  <th class="colMid">seq_tup_read</th>
  <th class="colMid">idx_scan</th>
  <th class="colMid">idx_tup_fetch</th>
  <th class="colMid">n_tup_ins</th>
  <th class="colMid">n_tup_upd</th>
  <th class="colMid">n_tup_del</th>
  <th class="colMid">n_tup_hot_upd</th>
  <th class="colMid">n_live_tup</th>
  <th class="colMid">n_dead_tup</th>
  <th class="colMid">last_vacuum</th>
  <th class="colMid">last_autovacuum</th>
  <th class="colMid">last_analyze</th>
  <th class="colLast">last_autoanalyze</th>
</tr>
';

while ($row = pg_fetch_array($rows)) {
$buffer .= tr()."
  <td>".$row['schemaname']."</td>
  <td>".$row['relname']."</td>
  <td>".$row['seq_scan']."</td>
  <td>".$row['seq_tup_read']."</td>
  <td>".$row['idx_scan']."</td>
  <td>".$row['idx_tup_fetch']."</td>
  <td>".$row['n_tup_ins']."</td>
  <td>".$row['n_tup_upd']."</td>
  <td>".$row['n_tup_del']."</td>
  <td>".$row['n_tup_hot_upd']."</td>
  <td>".$row['n_live_tup']."</td>
  <td>".$row['n_dead_tup']."</td>
  <td>".$row['last_vacuum']."</td>
  <td>".$row['last_autovacuum']."</td>
  <td>".$row['last_analyze']."</td>
  <td>".$row['last_autoanalyze']."</td>
</tr>";
}

$buffer .= '</table>
</div>
';

$buffer .= '<button id="showthesource">Show SQL commands!</button>
<div id="source">
<p>'.$query.'</p>
</div>';

$filename = $outputdir.'/stat_tables.html';
include 'lib/fileoperations.php';

?>
