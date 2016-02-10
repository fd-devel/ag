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
    
/* VALUES */
$id=$_POST['id_event'];
$user=$_POST['user'];
 

 
}
?>