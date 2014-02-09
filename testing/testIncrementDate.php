<?php
for($i=1;$i<37;$i++)
{
$monthToAdd = $i;

$d1 = DateTime::createFromFormat('Y-m-d', '2011-12-20');

$year = $d1->format('Y');
$month = $d1->format('n');
$day = $d1->format('d');

$year += floor($monthToAdd/12);
$monthToAdd = $monthToAdd%12;
$month += $monthToAdd;
if($month > 12) {
    $year ++;
    $month = $month % 12;
    if($month === 0)
        $month = 12;
}

if(!checkdate($month, $day, $year)) {
    $d2 = DateTime::createFromFormat('Y-n-j', $year.'-'.$month.'-1');
    $d2->modify('last day of');
}else {
    $d2 = DateTime::createFromFormat('Y-n-d', $year.'-'.$month.'-'.$day);
}
$d2->setTime($d1->format('H'), $d1->format('i'), $d1->format('s'));
echo $d2->format('Y-m-d')."<br>";
}