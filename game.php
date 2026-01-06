<?php
include("./include/header.php");

if(iseet($_GET['category'])) {
    $category_id = $_GET['catergory'];
    $posts = $db->prepare('SELECT * FROM posts WHERE category_id = :id LIMIT 4'); 
    $posts->execute(params: ['id' => $category_id]);
    $products = $db->prepare(query: 'SELECT *FROM product WHERE categori_ID = :id LIMIT 6');
    $products->execute(params:['id'=> $category_id]);

} else {
    $posts = $db->query( "SELECT * FROM posts LIMIT 4");
    $products = $db->query(query: "SELECT * FROM products LIMIT 6");
}
?>

<section class="hero">
    <div class="container">
       <div class="hero-container">
         <div class="hero-content">
            
         </div>
       </div> 
    </div>
</section>