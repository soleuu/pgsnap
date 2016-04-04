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

<h1>Replication slots</h1>';

$query = 'SELECT slot_name,
  plugin,
  slot_type,
  database,
  active,
  xmin,
  catalog_xmin,
  restart_lsn
FROM pg_replication_slots
ORDER BY slot_name';

$rows = pg_query($connection, $query);
if (!$rows) {
  echo "An error occured.\n";
  exit;
}

$buffer .= '<div class="tblBasic">

<table id="myTable" border="0" cellpadding="0" cellspacing="0" class="tblBasicGrey">
<thead>
<tr>
  <th class="colFirst" width="200">Plugin</th>
  <th class="colMid" width="200">Type</th>
  <th class="colMid" width="200">Database</th>
  <th class="colMid" width="200">Active?</th>
  <th class="colMid" width="200">xmin</th>
  <th class="colMid" width="200">Catalog xmin</th>
  <th class="colLast" width="200">Restart LSN</th>
</tr>
</thead>
<tbody>';

while ($row = pg_fetch_array($rows)) {
$buffer .= tr().'
  <td>'.$row['plugin'].'</td>
  <td>'.$row['slot_type'].'</td>
  <td title="'.$comments['databases'][$row['database']].'">'.$row['database'].'</td>
  <td>'.$image[$row['active']].'</td>
  <td>'.$row['xmin'].'</td>
  <td>'.$row['catalog_xmin'].'</td>
  <td>'.$row['restart_lsn'].'</td>';
}
$buffer .= '</tbody>
</table>
</div>
';

$buffer .= '<button id="showthesource">Show SQL commands!</button>
<div id="source">
<p>'.$query.'</p>
</div>';

$filename = $outputdir.'/replslots.html';
include 'lib/fileoperations.php';

?>
