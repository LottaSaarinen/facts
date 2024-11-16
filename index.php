<?php
include 'yla.php';

$dsn = "pgsql:host=localhost;dbname=lsaarinen";
$user = "db_lsaarinen";
$pass = getenv('DB_PASSWORD');
$options = [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION];

try {
    $yht = new PDO($dsn, $user, $pass, $options);
    if (!$yht) { 
        echo "Database connection refused.";
        die ();
    }
} catch (PDOException $e) { 
echo $e->getMessage(); 
die();
}

?>
<h><a href='vastaukset.php'>VASTAUKSET</a></h><br><br><br>

<h2>Kirjoita kysely</h2>
<form method=post action=index.php>
    <textarea rows=15 cols=100 name=kys></textarea>
    <br><br>
    <input type=submit value='Suorita kysely'>
</form>
    

<?php
$kys = $_POST['kys'];

// validointi
$fail = 0;

$forbidden = array('CREATE','DELETE','DROP','TRUNCATE','TRUNC','INSERT','UPDATE','COPY','GRANT','REVOKE','PROCEDURE','FUNCTION','RETURNS');

$kys = trim($kys);

if (strtoupper(substr ($kys, 0, 6)) !='SELECT') {
    $fail = 1;
}

foreach ($forbidden as $sana) {
    if ( strpos( strtoupper($kys), trim (strtoupper($sana)) )) { 
 $fail = 2;
}
}

if ($fail and strlen ($kys)) {
    echo "Query too complex!";
    die();
}

$lask = 0; //monesko rivi tulostuksessa menossa
$colnum = 0; // sarakkeiden lukumäärä
$tulos = $yht->query($kys);

echo "<table>";
try {
    while ($rivi = $tulos->fetch (PDO::FETCH_ASSOC)){
        //otsikot
        if ($lask == 0)
        {

            echo"<tr>";
            $colhead = array_keys($rivi); // sarakeotsikot
            $colnum = count($colhead);
            for($x=0; $x<$colnum; $x++) {
                echo "<th>" . $colhead[$x] . "</th>";    
            
        }
    }
    echo "</tr><tr>";
        for ($x=0; $x<$colnum; $x++) {
        echo "<td>" . $rivi[$colhead[$x]] . "</td>";
    }
    echo "</tr>";
    $lask++;
}

echo "</table>";
 

} catch (PDOException $e) { 
    echo $e->getMessage(); 
    die();
}
?>
