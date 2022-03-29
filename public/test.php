<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
function abc()
{

$a=array(12,16,3,67,9,28,76);

for($i=0;$i<count($a);$i++)
{
   for($j=$i+1;$j<count($a);$j++)
   {
       if($a[$i]>$a[$j])
       {
         $tem= $a[$i];
         $a[$i]=$a[$j];
         $a[$j]=$tem;
       }

   }


}
for($i=0;$i<count($a);$i++)
{
print($a[$i]."----");

}


}

abc();


?>
