/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/* Fonction de manupulation des listes dans les balises <selec t> */
function genereListe(_liste, _tabTexte, _tabValue, _tailleTab) {
    for (var i = 0; i < _tailleTab; i++)
        _liste.options[i] = new Option(_tabTexte[i], _tabValue[i]);
}

function bubbleSort(_tabText, _tabValue, _tailleTab) {
    var i, s;

    do {
        s = 0;
        for (i = 1; i < _tailleTab; i++)
            if (_tabText[i - 1] > _tabText[i]) {
                y = _tabText[i - 1];
                _tabText[i - 1] = _tabText[i];
                _tabText[i] = y;
                y = _tabValue[i - 1];
                _tabValue[i - 1] = _tabValue[i];
                _tabValue[i] = y;
                s = 1;
            }
    } while (s);
}

function videListe(_liste) {
    var cpt = _liste.options.length;

    for (var i = 0; i < cpt; i++) {
        _liste.options[0] = null;
    }
}

function selectUtil(_listeSource, _listeDest) {
    var i, j;
    var ok = false;
    var tabDestTexte = new Array();
    var tabDestValue = new Array();
    var tailleTabDest = 0;

    for (i = 0; i < _listeDest.options.length; i++) {
        tabDestTexte[tailleTabDest] = _listeDest.options[i].text;
        tabDestValue[tailleTabDest++] = _listeDest.options[i].value;
    }

    for (j = _listeSource.options.length - 1; j >= 0; j--) {
        if (_listeSource.options[j].selected) {
            ok = true;
            tabDestTexte[tailleTabDest] = _listeSource.options[j].text;
            tabDestValue[tailleTabDest++] = _listeSource.options[j].value;
            _listeSource.options[j] = null;
        }
    }

    if (ok) {
        //Trie du tableau
        bubbleSort(tabDestTexte, tabDestValue, tailleTabDest);
        //Vide la liste destination
        videListe(_listeDest);
        //Recree la liste
        genereListe(_listeDest, tabDestTexte, tabDestValue, tailleTabDest);
    }
}

//Fonction pour selectionner tous les utilisateurs d'une liste source et les transferer dans une liste destination
function selectAll(_listeSource, _listeDest) {
    for (var i = 0; i < _listeSource.options.length; i++) {
        _listeSource.options[i].selected = true;
    }
    selectUtil(_listeSource, _listeDest);
}

function recupSelection(_liste) {
    var sel = "";
    for (var i = 0; i < document.getElementById(_liste).length; i++) {
        sel += ((i) ? "+" : "") + document.getElementById(_liste).options[i].value;
    }
    return sel;
}



$(function () {
  $('[data-toggle="popover"]').popover({html : true } )
})

