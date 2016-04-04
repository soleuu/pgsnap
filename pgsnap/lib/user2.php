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

$buffer = $navigate_globalobjects.'
<div id="pgContentWrap">

<h1>Users space allocated</h1>
';

$query = "SELECT pg_get_userbyid(relowner) AS rolname,
  CASE WHEN relkind='r' THEN 'table'
       WHEN relkind='i' THEN 'index'
       WHEN relkind='S' THEN 'sequence'
       WHEN relkind='m' THEN 'materialized view'
       WHEN relkind='t' THEN 'TOAST table'
       ELSE '<unkown>' END AS kind,";
if ($g_version > 80) {
  $query .= '
  pg_size_pretty(SUM(pg_relation_size(pg_class.oid))::int8) AS size';
} else {
  $query .= '
  sum(relpages)*8192 AS size';
}
$query .= "
FROM pg_class
WHERE relkind IN ('r', 't', 'i', 'S')";
if ($g_withoutsysobjects) {
  $query .= "
  AND relnamespace NOT IN
  (SELECT oid FROM pg_namespace
   WHERE nspname <> 'pg_catalog'
     AND nspname <> 'information_schema'
     AND nspname !~ '^pg_toast')";
}
$query .= "
GROUP BY 1, 2
ORDER BY 1, 2";

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

$buffer .= '<div class="tblBasic">

<table id="myTable" border="0" cellpadding="0" cellspacing="0" class="tblBasicGrey">
<thead>
<tr>
  <th class="colFirst" width="40%">Owner</th>
  <th class="colMid" width="40%">Object\'s type</th>';
if ($g_version > 80) {
  $buffer .= '
  <th class="colLast" width="200">Size</th>';
} else {
  $buffer .= '
  <th class="colLast" width="200">Estimated Size</th>';
}
$buffer .= '
</tr>
</thead>
<tbody>
';

while ($row = pg_fetch_array($rows)) {
$buffer .= tr().'
  <td title="'.$comments['roles'][$row['rolname']].'">'.$row['rolname'].'</td>
  <td>'.ucfirst($row['kind']).'</td>';
if ($g_version > 80) {
$buffer .= '
  <td>'.$row['size'].'</td>';
} else {
$buffer .= '
  <td>'.pretty_size($row['size']).'</td>';
}
$buffer .= '
</tr>';
}
$buffer .= '</tbody>
</table>
</div>
';

$buffer .= '<button id="showthesource">Show SQL commands!</button>
<div id="source">
<p>'.$query.'</p>
</div>';

$filename = $outputdir.'/user2.html';
include 'lib/fileoperations.php';

?>
