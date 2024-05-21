<?php

class KontaktController extends Controller
{
        public static $menuTitle = "Kontakt";
        public function __construct()
        {
                $this->loginRequire = false;
        }
        public function zpracuj($parametry)
        {
                $this->hlavicka = array(
                        'titulek' => 'Kontaktní formulář',
                        'klicova_slova' => 'kontakt, email, formulář',
                        'popis' => 'Kontaktní formulář našeho webu.'
                );
                $this->data['zpravaRok'] = "";
                $this->pohled = 'kontakt';

                if (isset($_POST["email"])) 
                {
                        if ($_POST['rok'] == date("Y")) {
                                        EmailSender::odesli("jakub.charvot@centrum.cz", "Email z webu", $_POST['zprava'], $_POST['email']);
                                        $this->data['zprava'] = "Formulář byl úspěšně odeslán, děkujeme.";
                                        $this->pohled = 'kontaktDone'; //presmerovani na jiny pohled
                                } else
                                $this->data['zpravaRok'] =  "Zadali jste špatný rok! <br/>";
                }
}
}

