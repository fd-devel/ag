<?php

// Informations du paramétrage de l'utilisateur a afficher
    $consultUser = $_SESSION["id_user"];
    // TODO    if($_POST['userToConsul'])
    
    $paramUser = new \Modea\DAO\AgendaDAO();
    $userParams = $paramUser->findByUser($consultUser);

    $debut_journee = $userParams['debut_journee'];
    $fin_journee = $userParams['fin_journee'];
    $semaine_type = $userParams['semaine_type'];
    $planning = $userParams['planning'];
    $precision_planning = $userParams['precision_planning'];