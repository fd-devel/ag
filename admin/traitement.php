<?php
//  autoload des classes.
function chargerClasse($classname)
{
  require '../../../data/modeles/'.$classname.'.class.php'; 
}
spl_autoload_register('chargerClasse');
require '../src/DAO/DAO.php';
require '../src/DAO/CategorieDAO.php';
require '../src/Domain/categorie.class.php'; 

//  ---- Traitement supression, page admin ----
if (isset($_POST["cat_id"]) && !empty($_POST["cat_id"]))
{

    $dao = new Modea\DAO\CategorieDAO();
    $dao->delete($_POST['cat_id']);

    echo "Success";
}