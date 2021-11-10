<?php
//send object json for principal page in  the site
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include('get_data1.php');
include('group.php');
$host =  'localhost';
$user = 'root';
$password = '';
$dbname = 'cslab';
$dsn = 'mysql:host='. $host .';dbname='. $dbname;
try{
$pdo = new PDO($dsn, $user, $password);
$data=[];
$_POST['user_token']="T58efs52857fes5es";
$_POST['user_id']=3;
if(isset($_POST['user_token'])&&isset($_POST['user_id']))
{

    $sql="SELECT users.user_profilepicture,users.user_name,users.user_lastjoin from users where users.user_id=? and users.user_token=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_POST['user_id'],$_POST['user_token']]);
    $tab1=$stmt->fetch(PDO::FETCH_ASSOC);

   $data['users']=  $tab1;

}
$filters1=filters_start();
$filters=filters_start();
$filters['select']=['languages.language_name','books_languages.upload_date','categories.categorie_id','categories.categorie_name','books_languages.download_link'];
$filters['joins']['books_languages']['join']['book_id']=['rel' => '=', 'col' =>'books.book_id'];
  $filters['joins']['languages']['join']['language_id']=['rel' => '=', 'col' =>'books_languages.language_id'];
  $filters['joins']['books_categories']['join']['book_id']=['rel' => '=', 'col' =>'books.book_id'];
    $filters['joins']['categories']['join']['categorie_id']=['rel' => '=', 'col' =>'books_categories.categorie_id'];
    $filters['orders']['books']['book_downloads']="DESC";
    $filters1['orders']['books']['book_downloads']="DESC";
  $sql='('.get_all('books','books',5,0,$filters1)['sql'].') as books';
$sql=get_all('books',$sql,-1,-1,$filters)['sql'];
$stmt = $pdo->prepare($sql);
$stmt->execute();
$tab=$stmt->fetchAll(PDO::FETCH_ASSOC);
$tab= organiz($tab,'book_id',['languages'=>['language_name','upload_date','download_link'],'categories'=>['categorie_name','categorie_id']]);
$data['section']['most_popular']=$tab;
$filters=filters_start();
$filters['select']=['languages.language_name','books_languages.upload_date','books_languages.id','books_languages.download_link'];
$filters['joins']['books_languages']['join']['book_id']=['rel' => '=', 'col' =>'books.book_id'];
  $filters['joins']['languages']['join']['language_id']=['rel' => '=', 'col' =>'books_languages.language_id'];
  $filters['orders']['books_languages']['upload_date']='DESC';
  $sql=get_all('books','books',5,1,$filters)['sql'];
  $filters=filters_start();
$filters['select']=['categories.categorie_id','categories.categorie_name'];
$filters['joins']['books_categories']['join']['book_id']=['rel' => '=', 'col' =>'books.book_id'];
  $filters['joins']['categories']['join']['categorie_id']=['rel' => '=', 'col' =>'books_categories.categorie_id'];
$filters['orders']['books']['upload_date']='DESC';
$sql='('.$sql.') as books ';
$sql=get_all(' DISTINCT books',$sql,-1,-1,$filters)['sql']; //echo $sql;
$stmt = $pdo->prepare($sql);
$stmt->execute();
$tab=$stmt->fetchAll(PDO::FETCH_ASSOC); //print_r($tab);
$tab= organiz($tab,'id',['categories'=>['categorie_id','categorie_name']]);

foreach($tab as $ro=>$e) unset($tab[$ro]['id']);
$data['section']['last_uploaded']=$tab;
$filters=filters_start();
  $filters['select']=['(select ROUND(AVG(books_ratings.user_rating),2) from books_ratings where books_ratings.book_id=books.book_id) as rating'];
$filters['orders']['order']['(select ROUND(AVG(books_ratings.user_rating),2) from books_ratings where books_ratings.book_id=books.book_id)']='DESC';
    $sql=get_all('books','books',5,1,$filters)['sql'];

  $sql='('.$sql.') as books ';
  $filters=filters_start();
    $filters['select']=['languages.language_name','books_languages.upload_date','books_languages.download_link','categories.categorie_id','categories.categorie_name'];
    $filters['joins']['books_languages']['join']['book_id']=['rel' => '=', 'col' =>'books.book_id'];
      $filters['joins']['languages']['join']['language_id']=['rel' => '=', 'col' =>'books_languages.language_id'];
      $filters['joins']['books_categories']['join']['book_id']=['rel' => '=', 'col' =>'books.book_id'];
        $filters['joins']['categories']['join']['categorie_id']=['rel' => '=', 'col' =>'books_categories.categorie_id'];

$filters['orders']['books']['rating']='DESC';

        $sql=get_all('books',$sql,-1,-1,$filters)['sql']; //echo $sql;
        $stmt = $pdo->prepare($sql);
  $stmt->execute();
    $tab=$stmt->fetchAll(PDO::FETCH_ASSOC);
$tab= organiz($tab,'book_id',['languages'=>['language_name','upload_date','download_link'],'categories'=>['categorie_name','categorie_id']]);

$data['section']['most_rated']=$tab;
//echo $sql;
echo json_encode($data,JSON_PRETTY_PRINT);
}catch(PDOException $e){
  echo $e->getMessage();
}
 ?>
