
    /* ******************************************************
    **     Controle si une date respecte le calendrier     **
    ****************************************************** */ 
    function DateIsValid_val(jj, mm, aaaa) {
      if (jj < 1 || jj > 31 || mm < 1 || mm > 12)
        return false;
      if (mm == 2) {
        if (jj == 30 || jj == 31)
          return false;
        if (jj == 29)
          return ((aaaa % 4 == 0 && aaaa % 100 != 0) || aaaa % 400 == 0);
      } else if (jj == 31)
        return !( mm == 4 || mm == 6 || mm == 9 || mm == 11 );
      return true;
    }

    function chk_date_format(jj_mm_aaaa) {
      //Declarations
      var delim_char;
      var tab_jma;
      var msg1;
      var msg2;
      var ctrlOK;

      //Corps de la fonction
      //Initialisations
      delim_char = '/';
//      msg1 = "<?php echo trad('CHECKDATE_MSG1'); ?>";
//      msg2 = "<?php echo trad('CHECKDATE_MSG2'); ?>";
      msg1 = "CHECKDATE_MSG1";
      msg2 = "CHECKDATE_MSG2";

      //Verification de la longueur du param
      if (jj_mm_aaaa.length !== 10 ) {
          return false;
      } else {
        //Decoupage de la date en jj, mm, aaaa
        tab_jma = jj_mm_aaaa.split(delim_char);
        //Verification de la longueur du tableau (3 cases) :
        // [jj][mm][aaaa]
        if (tab_jma.length !== 3) {
          return false;
        } else {

          //Verification de la validite des chaines de caracteres
          //jj, mm, aaaa
          if ((tab_jma[0].length !== 2) || (tab_jma[1].length !== 2) || (tab_jma[2].length !== 4) ||
            (isNaN(tab_jma[0])) || (isNaN(tab_jma[1])) || (isNaN(tab_jma[2])) || (tab_jma[2] <= 0)) {
            return false;
          } else {
            //Verification de la date dans le calendrier
            ctrlOK = DateIsValid_val(tab_jma[0], tab_jma[1], tab_jma[2]);
            if (! ctrlOK) {
              return false;
            }
          }
        }
      }
      return true;
    }

    function end_sup_start(_Hdebut, _Hfin) {
		a= parseInt(_Hdebut);
		b= parseInt(_Hfin);
		if(a>b) { return false;}
		return true;
    }

    function evalDate(_date) {
      //Declarations
      var delim_char;
      var tab_jma;

      //Corps de la fonction
      //Initialisations
      delim_char = '/';
      //Decoupage de la date en jj, mm, aaaa
      tab_jma = _date.split(delim_char);
      // Pas besoin de verif, car la fonction precedente l'a deja faite
      return (tab_jma[2]+tab_jma[1]+tab_jma[0]);
    }

    //N'autorise que [0-9] et / comme saisie
    function onlyChar(ev) {
      ev || (ev=window.event);
      if ((ev.keyCode < 47) || (ev.keyCode > 57)) {
        ev.returnValue=false;
      }
      if ((ev.which < 47) || (ev.which > 57)) {
        return (false);
      }
      return (true);
    }
  //-->

