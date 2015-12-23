

$(".btn-minimize").click(function(){
    $(this).toggleClass('btn-plus');
    $("#body-liste_radios").slideToggle();
  });

// Info Alerte ajout radio
if(info_alert==1) message("AJOUT RADIO", "Votre radio a bien été ajoutée", "success");
if(info_alert==2) message("AJOUT RADIO", "Votre radio a bien été ajoutée. Mais il n'y a pas de logo", "warning");
if(info_alert==3) message("AJOUT RADIO", "Votre radio a bien été ajoutée.<br />Mais pas le logo, le fichier n'est pas une image valide", "warning");
if(info_alert==4) message("AJOUT RADIO", "Votre radio a bien été ajoutée.<br />Mais pas le logo, le fichier dépasse les 500Ko!", "warning");
if(info_alert==5) message("AJOUT RADIO", "Votre radio a bien été ajoutée.<br />Mais pas le logo, Une image avec le même nom existe déjà!", "warning");
if(info_alert==6) message("AJOUT RADIO", "Votre radio n'a pas été ajoutée.<br />Il n'est pas possible d'écrire dans le fichier Xml.", "error");
if(info_alert==7) message("AJOUT RADIO", "Votre radio n'a pas été ajoutée.<br />Le fichier Xml contenant la liste des radios n'existe pas", "error");
if(info_alert==8) message("AJOUT RADIO", "Votre radio n'a pas été ajoutée.<br />L'adresse n'est pas valide.", "error");


function valider_suppression(id,elmt)
{
    if( confirm('Voulez vous vraiment supprimer cette catégorie??')){
            while(elmt.firstChild){
                    elmt.removeChild(elmt.firstChild);
            };
    elmt.className ="fa fa-spinner fa-2x fa-spin";
    $.ajax({
        url: 'admin/traitement.php',
        type: 'POST',
        data: { cat_id: id },
        success: function(response) {
            var cpt_rendu_tite = cpt_rendu_corps ='';
            var coul='success';

            if (response == 'Success') {
                cpt_rendu_tite = "Suppression";
                var parent_node = elmt.parentNode;
                parent_node.className += "anim" ;
                jQuery(parent_node).fadeOut( 3000, function() {
                    while(parent_node.firstChild){
                        parent_node.removeChild(parent_node.firstChild);
                    }
                 });
                 cpt_rendu_corps = "La catégorie à bien été supprimée";
             }
            message(cpt_rendu_tite, cpt_rendu_corps, coul);
        }
    })
    }
}


function message(titre,message,coul)
{
	toastr.options = {
		"closeButton": true,
		"positionClass": "toast-top-right",
		"onclick": null,
		"showDuration": "300",
		"hideDuration": "1000",
		"timeOut": "6000",
		"extendedTimeOut": "1000",
		"showEasing": "swing",
		"hideEasing": "linear",
		"showMethod": "fadeIn",
		"hideMethod": "fadeOut"
	}
	toastr[coul](message,titre);
}
