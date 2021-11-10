<?php

function get_all($object,$object1,$number,$pages,$filters){   //function to get sql query
  $query='';
if($object!='users')  $query='SELECT  '.$object.'.*,';
else $query='SELECT ';
  //$query='SELECT  '.$object.'.book_id';
$data=[];
foreach($filters['select'] as $el)
$query.=''.$el.',';
$query=rtrim($query,",");
$query.=' FROM  '.$object1;
  foreach($filters['joins'] as $table=>$criteria){
    $first=true;
  $query.=' JOIN '.$table;
  foreach($criteria['join'] as $col=>$value)
{ $query.= (($first) ? ' ON '.$table.'.'.$col.''.$value['rel'].''.$value['col'] : ' AND '.$table.'.'.$col.''.$value['rel'].''.$value['col']);
  $first=false;}
  if(isset($criteria['conditions'])){
  foreach($criteria['conditions']['and'] as $col=>$value){
  $query.=' AND '; $query.='(';
  $or=true;
  foreach($value as $value1){
 if($or) $query.=''.$table.'.'.$col.''.$value1['rel'].'?';
 else $query.=' OR '.$table.'.'.$col.''.$value1['rel'].'?';
  $data[]=$value1['value'];
   $or=false;
} $query.=') ';
             }
             if(!empty($criteria['conditions']['or']))
             {
               $query.=" and (";
               $or=true;
               foreach($criteria['conditions']['or'] as $col=>$value){
                 foreach($value as $value1){
                   $query.= (($or) ? ''.$table.'.'.$col.''.$value1['rel'].'?' : ' or '.$table.'.'.$col.''.$value1['rel'].'?');
                   $data[]=$value1['value']; $or=false;
                 }
               }
               $query.=")";
             }
           }
      }
      $first=true;
      foreach($filters['wheres']['and'] as $table=>$criteria){


      foreach($criteria as $col=>$value)
      {
$or=true;
$query.=(($first) ? ' WHERE ' :  ' AND ');
$query.='(';
foreach($value as $value1){
if($table!="where")  $query.=(($or) ? ''.$table.'.'.$col.''.$value1['rel'].'?' : ' OR '.$table.'.'.$col.''.$value1['rel'].'?');
else                $query.=(($or) ? ''.$col.''.$value1['rel'].'?' : ' OR '.$col.''.$value1['rel'].'?');
   $data[]=$value1['value'];
   $or=false;
}  $query.=')';
       $first=false;
     }
   }
     if(!empty($filters['wheres']['or'])){
       if($first) $query.=" where("; else $query.=" and(";
       $or=true;
       foreach($filters['wheres']['or'] as $table=>$criteria){
       foreach($criteria as $col=>$value)
       {  foreach($value as $value1){
if($table!="where")   $query.=(($or) ? ''.$table.'.'.$col.''.$value1['rel'].'?' : ' or '.$table.'.'.$col.''.$value1['rel'].'?');
else                      $query.=(($or) ? ''.$col.''.$value1['rel'].'?' : ' OR '.$col.''.$value1['rel'].'?');
        $or=false;
        $data[]=$value1['value'];
         }
      }
    }
    $query.=")";
     }

$first=true;
  foreach($filters['havings']['and'] as $table=>$criteria){
  foreach($criteria as $col=>$value)
  {

    $query.=(($first) ? ' HAVING ' : ' AND ');
$query.='(';
$or=true;
foreach($value as $value1){
  if($table!="having")   $query.=(($or) ? ''.$table.'.'.$col.''.$value1['rel'].'?' : ' or '.$table.'.'.$col.''.$value1['rel'].'?');
  else                      $query.=(($or) ? ''.$col.''.$value1['rel'].'?' : ' OR '.$col.''.$value1['rel'].'?');
$data[]=$value1['value'];
$or=false;
}  $query.=")";
  $first=false;
}
    }
    if(!empty($filters['havings']['or'])){
      if($first) $query.=" having("; else $query.=" and(";
      $or=true;
      foreach($filters['havings']['or'] as $table=>$criteria){
      foreach($criteria as $col=>$value)
      { foreach($value as $value1){
        if($table!="where")   $query.=(($or) ? ''.$table.'.'.$col.''.$value1['rel'].'?' : ' or '.$table.'.'.$col.''.$value1['rel'].'?');
        else                      $query.=(($or) ? ''.$col.''.$value1['rel'].'?' : ' OR '.$col.''.$value1['rel'].'?');
       $or=false;
       $data[]=$value1['value'];
     }
     }
   }
   $query.=")";
    }
    if(isset($filters['group'])) $query.=" GROUP BY ".$filters['group']." ";
    if(isset($filters['havings']['having']))  if($first) $query.=' HAVING '.$filters['havings']['having'];
    else $query.=' AND '.$filters['havings']['having'];
    $first=true;
    foreach($filters['orders'] as $table=>$criteria){
      foreach($criteria as $col=>$value)
    {  if($table!="order") $query.=(($first) ? ' ORDER BY '.$table.'.'.$col.' '.$value : ','.$table.'.'.$col.' '.$value);
      else               $query.=(($first) ? ' ORDER BY '.$col.' '.$value : ','.$col.' '.$value);
      $first=false;}
  }


  if(($pages!=-1)&&($number!=-1)){
  $query.=' LIMIT '.($number*($pages-1)).','.$number;}
return ["sql"=>$query,"data"=>$data];
}

function filters_start(){
  return   [
      'select'=>

      [],
    'wheres'=>[
      'and'=>[

      ],
        'or'=>[

      ]

          ],
       'joins'=>[


      ],


       'havings'=>
       [
         'and'=>[

       ],
       'or'=>[

     ]
     ],
       'orders'=>
       [


    ]
    ];
}

?>
