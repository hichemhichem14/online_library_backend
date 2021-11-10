<?php
/*this file is for getting data*/
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include('get_data1.php');
include('group.php');
$host =  'localhost';
$user = 'root';
$password = '';
$dbname = 'cslab';
$dsn = 'mysql:host='. $host .';dbname='. $dbname;
$filters=filters_start();
try{
$pdo = new PDO($dsn, $user, $password);
if(isset($_POST['main_object'])){
$object=$_POST['main_object'];
  if(isset($_POST[$object.'_id']))   //search by id
{
  $object_id=$_POST[$object.'_id']; $filters['wheres']['and'][$object.'s'][$object.'_id'][]=["rel"=>"=","value"=>$object_id];
if($object=='author'){

  $filters['select']=['books.*'];
  $filters['joins']['books_authors']['join']['author_id']=['rel' => '=', 'col' =>'authors.author_id'];
    $filters['joins']['books']['join']['book_id']=['rel' => '=', 'col' =>'books_authors.book_id'];
              $sql=get_all($object.'s',$object.'s',-1,-1,$filters)['sql'];
$data=get_all($object.'s',$object.'s',-1,-1,$filters)['data'];
$stmt = $pdo->prepare($sql);
$stmt->execute($data);
    $tab1=$stmt->fetchAll(PDO::FETCH_ASSOC);
    $tab1= organiz($tab1,'author_id',['books'=>['book_id','book_name','book_pages','book_size','book_description','book_downloads','book_quote','book_picture']]);
echo json_encode($tab1,JSON_PRETTY_PRINT);
}
else if($object=='book')
{

$filters["select"]=['categories.categorie_id,categories.categorie_name','languages.language_name','authors.author_name','authors.author_id','books_languages.upload_date','books_languages.download_link','(select ROUND(AVG(books_ratings.user_rating),2) from books_ratings where books_ratings.book_id=books.book_id) as rating'];
$filters['joins']['books_authors']['join']['book_id']=['rel' => '=', 'col' =>'books.book_id'];
  $filters['joins']['authors']['join']['author_id']=['rel' => '=', 'col' =>'books_authors.author_id'];

  $filters['joins']['books_categories']['join']['book_id']=['rel' => '=', 'col' =>'books.book_id'];
    $filters['joins']['categories']['join']['categorie_id']=['rel' => '=', 'col' =>'books_categories.categorie_id'];

    $filters['joins']['books_languages']['join']['book_id']=['rel' => '=', 'col' =>'books.book_id'];
      $filters['joins']['languages']['join']['language_id']=['rel' => '=', 'col' =>'books_languages.language_id'];
$sql=get_all($object.'s',$object.'s',-1,-1,$filters)['sql'];
$data=get_all($object.'s',$object.'s',-1,-1,$filters)['data'];
$stmt = $pdo->prepare($sql);
$stmt->execute($data);
  $tab1=$stmt->fetchAll(PDO::FETCH_ASSOC);
  $tab1=organiz($tab1,'book_id',['authors'=>['author_name','author_id'],'book_added'=>['language_name','upload_date'],'categories'=>['categorie_name','categorie_id']]);
  $tab1=$tab1[0];
$sql1="SELECT  DISTINCT books.* FROM  books join (select books_categories.* from books_categories join (select categorie_id from books_categories where book_id=? limit 5) as b on books_categories.categorie_id=b.categorie_id  where books_categories.book_id!=? limit 5) as c on c.book_id=books.book_id";
$stmt = $pdo->prepare($sql1);
$stmt->execute([$object_id,$object_id]);
  $tab2=$stmt->fetchAll(PDO::FETCH_ASSOC);
  $tab2=organiz($tab2,'book_id',[]);
$tab1['similars_books']=$tab2;
echo json_encode($tab1,JSON_PRETTY_PRINT);
}else  if($object=='categorie')
{
  $filters['select']=['(SELECT COUNT(*) from books_categories where books_categories.categorie_id=categories.categorie_id)as nbr_books'];
  $sql=get_all($object.'s',$object.'s',-1,-1,$filters)['sql'];
  $data=get_all($object.'s',$object.'s',-1,-1,$filters)['data'];
  $stmt = $pdo->prepare($sql);
  $stmt->execute($data);
  $tab1=$stmt->fetchAll(PDO::FETCH_ASSOC);
  $tab1=organiz($tab1,'categorie_id',[]);
  echo json_encode($tab1,JSON_PRETTY_PRINT);
}else if($object=='user'){
  if(isset($_POST['user_token']))
  {
    $sql="SELECT COUNT(*) as c from users where user_id=? and user_token=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$object_id,$_POST['user_token']]);
    $tab1=$stmt->fetch(PDO::FETCH_ASSOC);
    if($tab1['c']!=1) { echo json_encode("error of authentification"); exit();}
  }
if(isset($_POST['object'])){
  $o=$_POST['object'];

  if($o=='comments'){
    $filters['select']=['user_comments.user_comment','user_comments.comment_id','user_comments.comment_date','books.*'];
$filters['orders']['user_comments']['comment_date']="DESC";
    $filters['joins']['user_comments']['join']['user_id']=['rel'=>'=','col'=>'users.user_id'];
    $filters['joins']['user_comments']['conditions']['and']['user_id'][]=['rel'=>'=','value'=>$object_id];
    $filters['joins']['books']['join']['book_id']=['rel'=>'=','col'=>'user_comments.book_id'];
    $sql=get_all($object.'s',$object.'s',-1,-1,$filters)['sql'];
    $data=get_all($object.'s',$object.'s',-1,-1,$filters)['data'];
    $stmt = $pdo->prepare($sql);
    $stmt->execute($data);
    $tab1=$stmt->fetchAll(PDO::FETCH_ASSOC);
    $tab1=organiz($tab1,'book_id',['comments'=>['user_comment','comment_id','comment_date']]);
echo json_encode($tab1,JSON_PRETTY_PRINT);
}else if($o=='ratings'){
  $filters['orders']['books_ratings']['user_rating']="DESC";
  $filters['select']=['books_ratings.rating_id','books_ratings.user_rating','books.*'];
  $filters['joins']['books_ratings']['join']['user_id']=['rel'=>'=','col'=>'users.user_id'];
  $filters['joins']['books_ratings']['conditions']['and']['user_id'][]=['rel'=>'=','value'=>$object_id];
  $filters['joins']['books']['join']['book_id']=['rel'=>'=','col'=>'books_ratings.book_id'];
  $sql=get_all($object.'s',$object.'s',-1,-1,$filters)['sql'];
  $data=get_all($object.'s',$object.'s',-1,-1,$filters)['data'];
  $stmt = $pdo->prepare($sql);
  $stmt->execute($data);
  $tab1=$stmt->fetchAll(PDO::FETCH_ASSOC);
  $tab1=organiz($tab1,'book_id',[]);
echo json_encode($tab1,JSON_PRETTY_PRINT);
}else if($o=='favorites'){
  $filters['select']=['user_favorites.date','books.*'];
$filters['orders']['user_favorites']['date']="DESC";
  $filters['joins']['user_favorites']['join']['user_id']=['rel'=>'=','col'=>'users.user_id'];
  $filters['joins']['user_favorites']['conditions']['and']['user_id'][]=['rel'=>'=','value'=>$object_id];
  $filters['joins']['books']['join']['book_id']=['rel'=>'=','col'=>'user_favorites.book_id'];
  $sql=get_all($object.'s',$object.'s',-1,-1,$filters)['sql'];
  $data=get_all($object.'s',$object.'s',-1,-1,$filters)['data'];
  $stmt = $pdo->prepare($sql);
  $stmt->execute($data);
  $tab1=$stmt->fetchAll(PDO::FETCH_ASSOC);
  $tab1=organiz($tab1,'book_id',[]);
echo json_encode($tab1,JSON_PRETTY_PRINT);
}
}
  else{
  $filter1=$filters;
   $filter2=$filters;
$filters['select']=['users.user_id','users.user_name','users.user_email','users.user_token','users.user_profilepicture','users.user_joindate','users.user_lastjoin'];
$sql=get_all($object.'s',$object.'s',-1,-1,$filters)['sql'];
$data=get_all($object.'s',$object.'s',-1,-1,$filters)['data'];
$stmt = $pdo->prepare($sql);
$stmt->execute($data);
$tab1=$stmt->fetch(PDO::FETCH_ASSOC);
$filter1['orders']['user_comments']['comment_date']="DESC";
$filter1['wheres']['and']=[];
$filter1['select']=['user_comments.user_comment','user_comments.comment_id','user_comments.comment_date'];
$filter1['joins']['user_comments']['join']['book_id']=['rel'=>'=','col'=>'books.book_id'];
$filter1['joins']['user_comments']['conditions']['and']['user_id'][]=['rel'=>'=','value'=>$object_id];
$filter1['group']="books.book_id";
$sql=get_all('books','books',5,1,$filter1)['sql'];// echo $sql;
$data=get_all('books','books',5,1,$filter1)['data'];//print_r($data);
$stmt=$pdo->prepare($sql);
$stmt->execute($data);
$t1=$stmt->fetchAll(PDO::FETCH_ASSOC);
$tab1['some_book_comments']=$t1;
$filter2['orders']['books_ratings']['user_rating']="DESC";
$filter2['wheres']['and']=[];
$filter2['select']=['books_ratings.* '];
$filter2['joins']['books_ratings']['join']['book_id']=['rel'=>'=','col'=>'books.book_id'];
$filter2['joins']['books_ratings']['conditions']['and']['user_id'][]=['rel'=>'=','value'=>$object_id];
$sql=get_all('books','books',5,1,$filter2)['sql'];// echo $sql;
$data=get_all('books','books',5,1,$filter2)['data'];
$stmt = $pdo->prepare($sql);
$stmt->execute($data);
$t1=$stmt->fetchAll(PDO::FETCH_ASSOC);
$tab1['some_book_ratings']=$t1;
echo json_encode($tab1,JSON_PRETTY_PRINT);
   }
}

  }
  //recherecher des livers avec filtrage
else if(isset($_POST['search'])&&isset($_POST['object'])&& $object='book' && isset($_POST['pages']))

{
$filters['orders']['books']['book_downloads']="DESC";
$filters['select']=['(select ROUND(AVG(books_ratings.user_rating),2) from books_ratings where books_ratings.book_id=books.book_id) as rating'];
  $search=$_POST['search'];
  $search= preg_replace('# {2,}#', ' ', $search);//remove the uncessary spaces
  $search= ltrim($search,' '); //remove the space at the beginig
  $search=trim($search,' ');//remove the last space if it existe
  $o=$_POST['object'];
  //$filters['object']=$_POST['object'];
  if($search!=''){
  $search='%'.$search.'%';
  $search=str_replace(' ','% %',$search); //replae spaces with % %
  if($o!='book'){$ser=$search; goto one;}
   $ser=explode(" ",$search); //make the string array whrn ever you find space
  $familiar=['%the%','%of%','%by%','%and%'];
  if(!empty(array_diff($ser,$familiar))){ // to remove the familliare word to find the key words if there's none use the familliare word that he wrote
    $ser=array_diff($ser,$familiar);
  }
  one:
  if($o=='book')
  foreach ($ser as  $value) {
$filters['wheres']['and']['books']['book_name'][]=['rel'=>' LIKE ','value'=>$value];
  }

else {
$filters['joins']['books_'.$o.'s']['join']['book_id']=['rel'=>'=','col'=>'books.book_id'];
  $filters['joins'][$o.'s']['join'][$o.'_id']=['rel'=>'=','col'=>'books_'.$o.'s.'.$o.'_id'];
$filters['joins'][$o.'s']['conditions']['and'][$o.'_name'][]=['rel'=>' LIKE ','value'=>'%'.$ser.'%'];         }
}
if(isset($_POST['categories'])){
  $filters['joins']['books_categories']['join']['book_id']=['rel' => '=', 'col' =>'books.book_id'];
    $filters['joins']['categories']['join']['categorie_id']=['rel' => '=', 'col' =>'books_categories.categorie_id'];
foreach ($_POST['categories'] as $key => $value) {
  if($key=='categorie_name') $op=' LIKE ';
  else $op='=';
  foreach ($value as $ke => $val) {
    if($op==' LIKE ')
    $sub=['rel'=>$op,'value'=>'%'.$val.'%'];
    else   $sub=['rel'=>$op,'value'=>$val];

  $filters['joins']['categories']['conditions']['and'][$key][]=$sub;
      }
   }

 }
   if(isset($_POST['languages'])){
     $filters['select'][]='books_languages.upload_date';
      $filters['select'][]='books_languages.download_link';
     $filters['joins']['books_languages']['join']['book_id']=['rel' => '=', 'col' =>'books.book_id'];
       $filters['joins']['languages']['join']['language_id']=['rel' => '=', 'col' =>'books_languages.language_id'];
    foreach ($_POST['languages'] as $key => $value) {
      if($key=='language_name') $op='LIKE';
      else $op='=';
      foreach ($value as $ke => $val) {
        $sub=['rel'=>$op,'value'=>$val];
        $filters['joins']['languages']['conditions']['and'][$key][]=$sub;
      }
    }
  }
    if(isset($_POST['book_downloads']))
     $filters['wheres']['and']['books']['book_downloads'][]=['rel'=>'>','value'=>$_POST['book_downloads']];
     if(isset($_POST['book_ratings']))

    $filters['wheres']['and']['where']['(select ROUND(AVG(books_ratings.user_rating),2) from books_ratings where books_ratings.book_id=books.book_id)'][]=['rel'=>'>=','value'=>$_POST['book_ratings']];
    $sql=get_all('DISTINCT books','books',5,$_POST['pages'],$filters)['sql']; //echo $sql;
    $data=get_all('DISTINCT books','books',5,$_POST['pages'],$filters)['data'];
    $stmt = $pdo->prepare($sql);
    $stmt->execute($data);
    $tab1=$stmt->fetchAll(PDO::FETCH_ASSOC);
    $tab1=organiz($tab1,'book_id',[]);
    echo json_encode($tab1,JSON_PRETTY_PRINT);
    //print_r($filters);
}

}

}catch(PDOException $e){
  echo $e->getMessage();
}
 ?>
