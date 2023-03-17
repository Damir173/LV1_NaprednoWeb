<?php

include('./dom_template.php');
include ('database/db.php');

interface iRadovi {
    public function create($naziv, $tekst, $link, $oib);
    public function save();
    public function read();
}

class DiplomskiRadovi implements iRadovi {
    private $naziv_rada = NULL;
    private $tekst_rada = NULL;
    private $link_rada = NULL;
    private $oib_tvrtke = NULL;

    function __construct($naziv, $tekst, $link, $oib) {
        $this->naziv_rada = $naziv;
        $this->tekst_rada = $tekst;
        $this->link_rada = $link;
        $this->oib_tvrtke = $oib;
    }

    function create($naziv, $tekst, $link, $oib) {
        self::__construct($naziv, $tekst, $link, $oib);
    }
    
    function read() {
        $conn = dbConn();
        $sql = "SELECT * FROM diplomski_radovi";
        $radovi = $conn->query($sql);
        if($radovi->num_rows > 0){
            while($rad = $radovi->fetch_assoc()){
                echo "<div>";
                echo "<strong>Naziv: ${rad['naziv_rada']}</strong><br>";
                echo "Tekst: ${rad['tekst_rada']}<br>";
                echo "Link: ${rad['link_rada']}<br>";
                echo "OIB: ${rad['oib_tvrtke']}";
                echo "</div><br>";
            }
        }
        $conn->close();    
    } 

    function save() {
        $conn = dbConn();
        $nazivRada = $this->naziv_rada;
        $tekstRada = $this->tekst_rada;
        $linkRada = $this->link_rada;
        $oib = $this->oib_tvrtke;
        $sql = "INSERT INTO diplomski_radovi(naziv_rada, tekst_rada, link_rada, oib_tvrtke) VALUES ('$nazivRada', '$tekstRada', '$linkRada', '$oib')";
        if($conn->query($sql) === true) {
            $this->read();
        }
        else {
            echo $conn->error;
        };
        $conn->close();
    }
}

function fetch_content($URL){
    $c = curl_init();
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($c, CURLOPT_URL, $URL);
    $data = curl_exec($c);
    curl_close($c);
    return $data;
}



$url = 'https://stup.ferit.hr/index.php/zavrsni-radovi/page/2';
$page = fetch_content($url);
$new_page = $page;
$lengthArticle = 1;
$lengthPage = strlen($page);

while(true){
    $pos1 = strpos($new_page, "<article id=");
    $pos2 = strpos($new_page, "</article>");
    $lengthArticle = $pos2-$pos1;
    if($lengthArticle > 0){
        $article = substr($new_page, $pos1, $lengthArticle);
        $oib_tvrtke = substr($article, strpos($article, ".png")-13, 13);
        $link_start = strpos($article, '<a class="fusion-rollover-link" href=')+38;
        $link_end = strpos($article, '/">')+1;
        $link_rada = substr($article, $link_start, $link_end - $link_start);
        $tema_start = strpos($article, '<div class="fusion-post-content-container"><p>')+46;
        $tema_end = strpos($article, "Opis:");
        $naziv_rada = substr($article, $tema_start, $tema_end - $tema_start);
        $tema_page = fetch_content($link_rada);
        $tekst_start = strpos($tema_page, "<p><strong>${naziv_rada}")+strlen($naziv_rada)+48;
        $tekst_end = strpos($tema_page, "<p><strong>Mentor:")-17;
        $tekst_rada = substr($tema_page, $tekst_start, $tekst_end - $tekst_start);
        $rad = new DiplomskiRadovi($naziv_rada, $tekst_rada, $link_rada, $oib_tvrtke);
        $rad->save();
        $lengthPage = $lengthPage - $pos2; 
        $new_page = substr($new_page, $pos2, $lengthPage);
    }
    else {
        break;
    }
}

?>
