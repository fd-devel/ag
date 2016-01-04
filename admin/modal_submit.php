<div class="modal fade" id="confirm-submit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                Confirmation
            </div>
            <div class="modal-body">
                Voulez vous valider ces choix?
                <table class="table">
                    <tr>
                        <td id="params"></td>
                    </tr>
                    <tr>
                        <td id="grp_users"></td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                <a href="#" id="submit" class="btn btn-success success">Valider</a>
            </div>
        </div>
    </div>
</div>

<script>

// Verifie si des choix spécifiques sont selectionnés
function verifSel(theForm) {
      var _checked = false;
      for (var i=0; i<theForm.elements.length; i++) {
        if (theForm.elements[i].name.substr(0,3)==="sel")
          if (theForm.elements[i].checked==true)
            _checked = true;
      }
      return _checked;
    }
    
$('#submitBtn').click(function() {
    var _checked = verifSel(document.paramGenAgenda);
    var _Sel_G = document.getElementById('groupesSelect').length;
    var _Sel_U = document.getElementById('utilisateursSelect').length;
    var params, grpEtUsers;
    
    if(_checked && (_Sel_G || _Sel_U)){
        params = "Seules les options sélectionnées seront appliquées.";
        grpEtUsers = "Des groupes ( "+_Sel_G+" ) ou utilisateurs ( "+_Sel_U+" ) sont sélectionnés. Ils seront les seuls affectés";
    }
    else if(_checked && !(_Sel_G || _Sel_U)){
        params = "Seules les options sélectionnées seront appliquées.";
        grpEtUsers = "Aucun groupe ou utilisateur sont sélectionné. Les options seront appliqués à tous les utilisateurs.";
    }
    else if(!_checked && (_Sel_G || _Sel_U)){
        params = "Aucune option selectionnée. Elles seront toutes appliquées";
        grpEtUsers = "Des groupes ( "+_Sel_G+" ) ou utilisateurs ( "+_Sel_U+" ) sont sélectionnés. Ils seront les seuls affectés.";
    }
    else if(!_checked && !(_Sel_G || _Sel_U)){
        params = "Vous n'avez sélectionné aucune option ni aucun utilisateur.";
        grpEtUsers = "Toutes les options seront appliquées aux nouveaux utilisateurs uniquement.";
    }
    else{
        params = "??...";
        grpEtUsers ="";
    }
   
     $('#params').html(params);
     $('#grp_users').html(grpEtUsers);
});

$('#submit').click(function(){
    document.getElementById('paramGenAgenda_Go').value="1";
    document.getElementById('GrpSel').value = recupSelection('groupesSelect');
    document.getElementById('UsersSel').value = recupSelection('utilisateursSelect');
    $('#paramGenAgenda').submit();
});
</script>
