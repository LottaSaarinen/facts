<?php
include 'yla.php';



echo  "<h><a href='index.php'>KYSELYYN</a></h><br><br><br>";

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


$sql="SELECT SUM(pop) as pop_total
from facts";
$tulos=$yht->query($sql);

echo "1. Koko maailman väkiluku?<br><br>";
echo "<table>";
echo "<tr><th>pop_total</th></tr>";
            
while ($row = $tulos->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['pop_total']) . "</td>";
    echo "</tr>";
}
            echo "</table>";
            echo "<br><hr><hr><br>";
 
 $sql="SELECT ROUND(AVG(pop)) as average_pop
from facts";
$tulos=$yht->query($sql);

echo "2. Valtioiden väkilukujen keskiarvo kokonaisluvuksi pyöristettynä?<br><br>";
echo "<table>";
echo "<tr><th>average_pop</th></tr>";
                        
while ($row = $tulos->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['average_pop']) . "</td>";
    echo "</tr>";
}
        echo "</table>";
        echo "<br><hr><hr><br>";


$sql="SELECT country 
FROM facts 
WHERE area = ( SELECT MAX(area)
FROM Facts)";
//$sql="select country from facts order by area desc limit 1";
//select distinct on (area) country from facts order by area desc limit 1;
$tulos=$yht->query($sql);

echo "3. Maailman pinta-alaltaan suurin valtio?<br><br>";
echo "<table>";
echo "<tr><th>country</th></tr>";
            
while ($row = $tulos->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['country']) . "</td>";
    echo "</tr>";
}
            echo "</table>";
            echo "<br><hr><hr><br>";


$sql="SELECT country FROM facts WHERE
pop = (SELECT MAX(pop) FROM facts WHERE continent = 'Europe')";
//"SELECT country FROM facts WHERE continent = 'Europe' ORDER BY pop DESC LIMIT 1";
$tulos=$yht->query($sql);

echo "4. Euroopan väkirikkain valtio?<br><br>";
echo "<table>";
echo "<tr><th>country</th></tr>";

while ($row = $tulos->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['country']) . "</td>";
    echo "</tr>";
}
echo "</table>";
echo "<br><hr><hr><br>";

$sql="SELECT country, pop FROM facts 
WHERE continent = 'Europe' ORDER BY pop DESC LIMIT 3";
//SELECT country, pop from (select country, pop, rank() over (order by pop desc) as rank from facts where continent = 'Europe') as ranked_countries where rank <= 3;
$tulos=$yht->query($sql);

echo "5. Euroopan kolme väkirikkainta valtiota?<br><br>";
echo "<table>";
echo "<tr><th>country</th></tr>";

while ($row = $tulos->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['country']) . "</td>";
    echo "</tr>";
}
echo "</table>";
echo "<br><hr><hr><br>";


$sql="SELECT SUM(pop) AS pop_total FROM facts WHERE continent = 'South America' ";
//SELECT continent, sum(pop) as pop_total from facts GROUP by continent having continent = 'South America';
//SELECT distinct sum(pop) over () as pop_total from facts where continent = 'South America';
$tulos=$yht->query($sql);

echo "6. Etelä-Amerikan väkiluku yhteensä?<br><br>";
echo "<table>";
echo "<tr><th>pop_total</th></tr>";
            
while ($row = $tulos->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['pop_total']) . "</td>";
    echo "</tr>";
 }
    echo "</table>";
    echo "<br><hr><hr><br>";

 $sql="SELECT count(*) as num_country from facts where continent = (select continent from facts where country = 'Canada')";
//$sql="SELECT COUNT(country) AS num_country FROM facts WHERE continent = (SELECT continent FROM facts WHERE country = 'Canada')";
$tulos=$yht->query($sql);

            echo "7. Niiden maiden lukumäärä, jotka kuuluvat samaan maanosaan, kuin Kanada?<br><br>";
            echo "<table>";
            echo "<tr><th>num_country</th></tr>";
                        
            while ($row = $tulos->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['num_country']) . "</td>";
                echo "</tr>";
                }
                echo "</table>";
                echo "<br><hr><hr><br>";


$sql="SELECT country FROM facts
WHERE continent IN (SELECT continent FROM facts
WHERE country IN ('Sweden', 'France', 'Poland'))
ORDER BY country";

$tulos=$yht->query($sql);

echo "8. Niiden maiden nimet, jotka kuuluvat samaan maanosaan, kuin Ruotsi, Ranska ja Puola?<br><br>";
echo "<table>";
echo "<tr><th>country</th><tr>";

while ($row = $tulos->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['country']) . "</td>";
    echo "</tr>";
}
echo "</table>";
echo "<br><hr><hr><br>";


$sql="SELECT country, pop from facts where pop < (select pop from facts where country = 'Gibraltar') order by pop asc";
$tulos=$yht->query($sql);

echo "9.Niiden maiden nimet ja väkiluvut, joiden väkiluku on pienempi kuin Gibraltarin, järjestettynä 
väkiluvun mukaan nousevaan järjestykseen?<br><br>";
echo "<table>";
echo "<tr><th>country</th><th>pop</th></tr>";
while ($row = $tulos->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['country']) . "</td>";
    echo "<td>" . htmlspecialchars($row['pop']) . "</td>";
    echo "</tr>";
}
echo "</table>";
echo "<br><hr><hr><br>";

$sql="SELECT round(sum(pop*gdp_percapita)/1000000) as gdp_millions,
sum(pop) as pop, 'Africa' as political_area
from facts where continent = 'Africa'
union
select round(pop*gdp_percapita/1000000) as gdp_millions,
pop, country as political_area
from facts where country = 'Germany'";
$tulos=$yht->query($sql);

echo "10. Laadi kysely, joka näyttää bruttokansantuotteen miljoonina dollareina, väkiluvun ja poliittisen 
alueen nimen alla olevan mallin mukaan. Vertailtavat kohteet ovat koko Afrikan yhteenlaskettu 
BKT (ei BKT per capita) ja väkiluku sekä samat asiat Saksasta?<br><br>";
echo "<table>";
echo "<tr><th>gpd_millions</th><th>pop</th><th>political_area</th></tr>";

while ($row = $tulos->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['gdp_millions']) . "</td>";
    echo "<td>" . htmlspecialchars($row['pop']) . "</td>";
    echo "<td>" . htmlspecialchars($row['political_area']) . "</td>";
    echo "</tr>";
}
echo "</table>";
echo "<br><hr><hr><br>";

$sql="SELECT country, round(pop/area, 1) as pop_density from facts
where area > 0 and pop/area < (select pop/area from facts
where country = 'Finland')
order by pop_density desc";
$tulos=$yht->query($sql);

echo "11. Maan nimi ja väestötiheys niistä maista,
joiden väestötiheys on pienempi kuin Suomen järjestettynä väestötiheyden mukaan laskevaan järjestykseen?<br><br>";
echo "<table>";
echo "<tr><th>country</th><th>pop_density</th></tr>";

    while ($row = $tulos->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['country']) . "</td>";
        echo "<td>" . htmlspecialchars($row['pop_density']) . "</td>";
        echo "</tr>";
    }
echo "</table>";
echo "<br><hr><hr><br>";

$sql=" SELECT gdp_percapita as gdp, country from facts
where continent = 'South America'
union
select gdp_percapita as gdp, country from facts
where country = 'Finland'
order by gdp desc";
$tulos=$yht->query($sql);
        
echo "12. Niiden Etelä-Amerikan maiden nimet ja BKT per capita,
 joiden pinta-ala on pienempi kuin Suomen siten, että myös Suomi on mukana. Järjestys on BKT per capitan mukaan laskeva<br><br>";
 echo "<table>";
echo "<tr><th>gpd_percapita</th><th>country</th></tr>";

    while ($row = $tulos->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['gdp']) . "</td>";
        echo "<td>" . htmlspecialchars($row['country']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "<br><hr><hr><br>";

$sql="SELECT country, pop, round(pop/area) as pop_density
from facts
where area > 0
order by pop_density desc
limit 5";          
$tulos=$yht->query($sql);

echo "13. Etsi maailman viisi väestötiheydeltään korkeinta maata. Näytä maan nimi, väkiluku, väestötiheys pyöristettynä kokonaisluvuksi
järjestettynä väestötiheyden mukaan laskevaan järjestykseen?<br><br>";
echo "<table>";
echo "<tr><th>country</th><th>pop</th><th>pop_density</th></tr>";

while ($row = $tulos->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['country']) . "</td>";
    echo "<td>" . htmlspecialchars($row['pop']) . "</td>";
    echo "<td>" . htmlspecialchars($row['pop_density']) . "</td>";
    echo "</tr>";
}
echo "</table>";
echo "<br><hr><hr><br>";

$sql="SELECT country from facts where country like '%West%'";          
$tulos=$yht->query($sql);

echo "14. Etsi maat, joiden nimessä esiintyy sana 'West'?<br><br>";
echo "<table>";
echo "<tr><th>country</th></tr>";

while ($row = $tulos->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['country']) . "</td>";
    echo "</tr>";
}
echo "</table>";
echo "<br><hr><hr><br>";

 $sql="SELECT continent, sum(pop) as sum_pop
from facts
group by continent
order by sum_pop desc";          
$tulos=$yht->query($sql);

echo "15. Maanosien nimet ja yhteenlasketut väkiluvut järjestettynä väkiluvun mukaan laskevaan järjestykseen?<br><br>";
echo "<table>";
echo "<tr><th>continent</th><th>sum_pop</th></tr>";

while ($row = $tulos->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['continent']) . "</td>";
    echo "<td>" . htmlspecialchars($row['sum_pop']) . "</td>";
    echo "</tr>";
}
echo "</table>";
echo "<br><hr><hr><br>";



 $sql="SELECT continent, sum(pop) as sum_pop
from facts
group by continent
having sum(pop) >= (select sum(pop) from facts
where continent = 'Europe')
order by sum_pop desc";          
$tulos=$yht->query($sql);

echo "16. Maanosien nimet ja yhteenlasketut väkiluvut järjestettynä väkiluvun mukaan laskevaan järjestykseen.
 Mukaan otetaan vain ne maanosat, joiden väkiluku on korkeampi tai yhtä suuri, kuin Euroopan?<br><br>";
echo "<table>";
echo "<tr><th>continent</th><th>sum_pop</th></tr>";

while ($row = $tulos->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['continent']) . "</td>";
    echo "<td>" . htmlspecialchars($row['sum_pop']) . "</td>";
    echo "</tr>";
}
echo "</table>";
echo "<br><hr><hr><br>";


$sql="SELECT round(sum(pop*gdp_percapita)/1000000000) as gdp_billions,
continent
from facts
where gdp_percapita > 0
group by continent
order by gdp_billions desc";
$tulos=$yht->query($sql);

echo "17. Bruttokansantuote miljardeina dollareina ja maanosan nimi, summattuna maanosittain alla 
olevan mallin mukaan järjestettynä BKT:n mukaan laskevaan järjestykseen. HUOM ei BKT per capita vaan BKT. Ks. teht. 10. Jätä tuloksista pois maanosat, joiden BKT on 0.<br><br>";
echo "<table>";
echo "<tr><th>gdp_billions</th><th>continent</th></tr>";

while ($row = $tulos->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['gdp_billions']) . "</td>";
    echo "<td>" . htmlspecialchars($row['continent']) . "</td>";
    echo "</tr>";
}
echo "</table>";
echo "<br><hr><hr><br>";

}
catch (PDOException $e)
 {
    echo $e->getMessage();
   
}
?>