<?php
//functio to group data geted from the database
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
//function to order  the joins
function  gg($result,$join,$nom){
  foreach($result as $q=>$p)
  {
    if (is_array($result[$q][$join[0]])){
    foreach($result[$q][$join[0]] as $col=>$el)
    {

    /*if(!empty($result[$q][$join[0]][$col]))*/ $result[$q][$nom][$col]=array($join[0]=>$el);
foreach($join as $a=>$e){
  if($a!=0)
  //if(!empty($result[$q][$e][$col]))
  $result[$q][$nom][$col][$e]=$result[$q][$e][$col];}
}
   }else{
    foreach($join as $e){
    $result[$q][$nom][0][$e]=$result[$q][$e];
    }
}
foreach($result[$q][$nom] as $co=>$arr){
   foreach($result[$q][$nom] as $co1=>$arr1){
  if($co==$co1) break;
if($result[$q][$nom][$co]==$result[$q][$nom][$co1]) {unset($result[$q][$nom][$co]); break;}
      }
   }
   $result[$q][$nom]=array_values($result[$q][$nom]);
}
return $result;
}
//using gg function to organiz data geted from the data base

function organiz($table,$specify,$joins)
{
 $result=[];
 foreach($table as $row){
  $first=true;
  foreach($result as $p=>$row1){
    if($result[$p][$specify]==$row[$specify]){
       $first=false;
       foreach($result[$p] as $col=>$el){
         $exist=false;
           foreach($joins as $ex) if(in_array($col,$ex)){ $exist=true; break;}
           if($exist) {
                if(is_array($result[$p][$col])){  $result[$p][$col][]=$row[$col];
                }else
                      $result[$p][$col]=[$result[$p][$col],$row[$col]];

           }else    {
                 if(is_array($result[$p][$col])){ $i=true; foreach($result[$p][$col] as $el) {
                  if($row[$col]==$el) { $i=false; break;}
               }
                  if($i) $result[$p][$col][]=$row[$col];
                }else if($result[$p][$col]!=$row[$col])
                  $result[$p][$col]=[$result[$p][$col],$row[$col]];
       }
     }
       break;
   }
  }
     if($first) $result[]=$row;
}
   if(!empty($joins))  foreach($joins as $a=>$tab){
  $result=gg($result,$tab,$a);
}
foreach($result as $aa=>$bb) foreach($joins as $cc) foreach($cc as $dd) unset($result[$aa][$dd]);
return $result;
}

$table=[
  ['title'=>' book of java','isbn'=>'125455','categories'=>'programmation','luangage'=>'english','upload'=>'2016','user'=>'hichem','size'=>200],
  ['title'=>'equation differentiellle','isbn'=>'4555','categories'=>'math','luangage'=>'francais','upload'=>'2015','user'=>'abdou','size'=>300],
  ['title'=>'c','isbn'=>'725458','categories'=>'programmation','luangage'=>'english','upload'=>'2014','user'=>'ert','size'=>300],
  ['title'=>'livre de java','isbn'=>'125455','categories'=>'programmation','luangage'=>'francais','upload'=>'2016','user'=>'sadik','size'=>200],
  ['title'=>'AI','isbn'=>'1258855','categories'=>'informatique','luangage'=>'francais','upload'=>'1440','user'=>'rev','size'=>400],
  ['title'=>'c','isbn'=>'725458','categories'=>'informatique','luangage'=>'english','upload'=>'2014','user'=>'klklk','size'=>300]
];
//the function get the table geted by query sql and the specific thing in the objects and the table of tables of
//joins data
$r= organiz($table,'isbn',['book_added'=>['luangage','upload','user']]);
//print_r($r);
//echo json_encode($r);
 ?>
