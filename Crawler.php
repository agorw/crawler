<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
class Crawler
{
    private $fileOut;
    function __construct($fileOut = 'HtmlBrut.txt')
    {
        $this->fileOut = $fileOut;
    }

    /**
     * Get the value of fileOut
     */
    public function getFileOut()
    {
        return $this->fileOut;
    }

    /**
     * Set the value of fileOut
     *
     * @return  self
     */
    public function setFileOut(string $fileOut)
    {
        $this->fileOut = $fileOut;

        return $this;
    }
    /**
     * Undocumented function
     *
     * @param string $url
     */
    public function crawler_url(string $url)
    {
        $url = strip_tags(trim($url));
        $ch = curl_init($url);
        if (file_exists($this->getFileOut())) {
            unlink($this->getFileOut());
        }
        $options = array(
            CURLOPT_SSL_VERIFYPEER => false,    // HTTPS AUTORISED IS FALSE
            CURLOPT_RETURNTRANSFER => true,     // return web page
            CURLOPT_HEADER         => false,    // don't return headers
            CURLOPT_FOLLOWLOCATION => true,     // follow redirects
            CURLOPT_ENCODING       => "",       // handle all encodings
            CURLOPT_USERAGENT      => "Agorw.fr", // who am i
            CURLOPT_AUTOREFERER    => true,     // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
            CURLOPT_TIMEOUT        => 120,      // timeout on response
            CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
        );
        $fp_fichier_html_brut = fopen($this->getFileOut(), 'a+');

        // définition des paramètres curl
        // 1 redirection de l'output dans le fichier txt
        curl_setopt_array($ch, $options);
        curl_setopt($ch, CURLOPT_FILE, $fp_fichier_html_brut);

        // 2 on spécifie d'ignorer les headers HTTP
        // curl_setopt($ch, CURLOPT_HEADER, 0);

        // exécution de curl
        curl_exec($ch);

        // fermeture de la session curl
        curl_close($ch);
        // fermeture du fichier texte
        fclose($fp_fichier_html_brut);
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function crawler_Tab_Email()
    {
        $tab_email = [];
        // recup le code source html de la page visiter on le stock dans un variable
        $html_brut = file_get_contents($this->getFileOut());

        // extraction des emails du code source 
        preg_match_all("#[-0-9a-zA-Z.+_]+@[-0-9a-zA-Z.+_]+.[a-zA-Z]{2,4}#", $html_brut, $adresses_mail);

        // on créé une boucle pour placer tous les mails de la page dans un tableau
        foreach ($adresses_mail[0] as $element) {

            // on verif que c'est une email valide
            if (filter_var($element, FILTER_VALIDATE_EMAIL)) {
                // on ajoute au tableau
                array_push($tab_email, $element);
                $result = array_unique($tab_email);
            }
        }

        return $result;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function crawler_Tab_Url()
    {
        $tab_url = [];

        // recup le code source html de la page visiter on le stock dans un variable
        $html_brut = file_get_contents($this->getFileOut());

        // extraction des liens du code source 
        preg_match_all('@(ht|f)(tp)(s?)\:\/\/([a-z0-9A-Z]+\.[a-z0-9A-Z]+\.[a-zA-Z]{2,4})|([a-z0-9A-Z]+\.[a-zA-Z]{2,4})\?([a-zA-Z0-9]+[\&\=\#a-z]+)|#[a-zA-Z]{1,255}@', $html_brut, $url_list);

        // on créé une boucle pour placer toute les url dans un tableau.
        foreach ($url_list[0] as $element) {

            // on ajoute url
            if (filter_var($element, FILTER_VALIDATE_URL) !== false) {
                array_push($tab_url, $element);
                $result = array_unique($tab_url);
            }
        }
        if (!empty($result)) {
            return $result;
        }
    }
}
