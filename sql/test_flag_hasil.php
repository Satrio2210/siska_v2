<?php
include __DIR__ . '/../inc/lab_filter_rujukan.php';
$tests = array(
  array('11', '12,0 - 16,0', '*11'),
  array('18', '12,0 - 16,0', '*18'),
  array('14', '12,0 - 16,0', '14'),
  array('13,5', '12,0 - 16,0', '13,5'),
  array('3500', '4000 - 10000', '*3500'),
  array('A', 'A/B/O', 'A'),
  array('12,0', '12,0 - 16,0', '12,0'),
  array('16,0', '12,0 - 16,0', '16,0'),
);
$ok = true;
foreach ($tests as $t) {
  $got = format_lab_hasil_flag($t[0], $t[1]);
  $pass = ($got === $t[2]);
  echo ($pass ? 'OK' : 'FAIL') . " in={$t[0]} ref={$t[1]} expect={$t[2]} got={$got}\n";
  if (!$pass) $ok = false;
}
echo $ok ? "ALL FLAG TESTS PASSED\n" : "SOME FAILED\n";
