<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
//$post_books=$_POST['books'];
$host =  'localhost';
$user = 'root';
$password = '';
$dbname = 'cslab';
$dsn = 'mysql:host='. $host .';dbname='. $dbname;
try{
$pdo = new PDO($dsn, $user, $password);
if(isset($_POST['user_id'])&&isset($_POST['user_token'])&&isset($_POST['object']))
{
  $object=$_POST['object'];
  $sql="SELECT count(*) as c from users where users.user_token=? and users.user_id=?";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([$_POST['user_token'],$_POST['user_id']]);
  $tab=$stmt->fetch(PDO::FETCH_ASSOC);
  if($tab['c']!=1) {echo json_encode("error of authentication"); exit();}

  if($object=='comment') {
if(isset($_POST['book_id'])&&isset($_POST['user_comment']))
{
    $sql="INSERT INTO user_comments(book_id,user_id,user_comment,comment_date) values(?,?,?,?)"; $stmt = $pdo->prepare($sql);
    $stmt->execute([$_POST['book_id'],$_POST['user_id'],$_POST['user_comment'],date("Y-m-d H:i:s")]);
    echo json_encode("true");

}else if(isset($_POST['comment_id'])){
  if(isset($_POST['user_comment'])){

$sql="UPDATE user_comments SET user_comment=? where comment_id=?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$_POST['user_comment'],$_POST['comment_id']]);
echo json_encode("true");
}
else {

$sql="DELETE from user_comments where comment_id=?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$_POST['comment_id']]);
echo json_encode("true");


}
  }

}
 else if($object=="rating"){
  if(isset($_POST["book_id"])&&issset($_POST["user_rating"])){

    $sql="INSERT INTO books_ratings(book_id,user_id,user_rating) values(?,?,?,)"; $stmt = $pdo->prepare($sql);
    $stmt->execute([$_POST['book_id'],$_POST['user_id'],$_POST['user_rating']]);
    echo json_encode("true");}


 else if(isset($_POST['rating_id'])){
   if(isset($_POST['user_rating'])){

       $sql="UPDATE books_ratings SET user_rating=? where rating_id=?";
       $stmt = $pdo->prepare($sql);
       $stmt->execute([$_POST['user_rating'],$_POST['rating_id']]);
       echo json_encode("true");


   }else{

       $sql="DELETE FROM books_ratings where rating_id=?";
       $stmt = $pdo->prepare($sql);
       $stmt->execute([$_POST['rating_id']]);
       echo json_encode("true");


   }
 }
}
else if($object=='favorite')
{
  if(isset($_POST['book_id']))
  {

$sql='INSERT INTO users_favorites(user_id,book_id,favorite_date) values(?,?,?)';
$stmt = $pdo->prepare($sql);
$stmt->execute([$_POST['user_id'],$_POST['book_id'],date("Y-m-d H:i:s")]);
       echo json_encode("true");


}else  if(isset($_POST['favorites_id'])){

$sql='DELETE FROM users_favorites where favorites_id=?';
$stmt = $pdo->prepare($sql);
$stmt->execute([$_POST['favorites_id']]);
       echo json_encode("true");
}
}
else if($object=='history'){
  if(isset($_POST['book_id']))
  {

$sql='INSERT INTO users_history(user_id,book_id,download_date) values(?,?,?)';
$stmt = $pdo->prepare($sql);
$stmt->execute([$_POST['user_id'],$_POST['book_id'],date("Y-m-d H:i:s")]);
       echo json_encode("true");


}else  if(isset($_POST['history_id'])){

$sql='DELETE FROM users_history where history_id=?';
$stmt = $pdo->prepare($sql);
$stmt->execute([$_POST['history_id']]);
       echo json_encode("true");
}
}}
}catch(PDOException $e){
  echo $e->getMessage();
}

 ?>
