<?php
 
require './src/DAO/DAO.php';
require './src/DAO/NoteDAO.php';
require './src/Domain/note.class.php';


if(isset($_POST['do']) && $_POST['do'] === "search"){
    $yep = new Modea\DAO\NoteDAO();
    $noteWithMere = $yep->findByMere($_POST['note_id']);
    
    if(count($noteWithMere)){
        echo 'Mere';
    }  else {
        echo 'notMere';
    }
}
 else {
     if(isset($_POST['param']) && isset($_POST['user']) && isset($_POST['note_id']) ){
    
        /* VALUES */
        $id=$_POST['note_id'];
        $user=$_POST['user'];
        $param = $_POST['param'];   // 4 : Edit - notmere
 

     } 
}
?>