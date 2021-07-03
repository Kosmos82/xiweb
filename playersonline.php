<?php
include 'config/database.conf';

$uptime = shell_exec("ps -p $(pidof topaz_game) -o etime=");

header("Refresh: 120");
$jobID = array(
 1 => WAR,
 2 => MNK,
 3 => WHM,
 4 => BLM,
 5 => RDM,
 6 => THF,
 7 => PLD,
 8 => DRK,
 9 => BST,
10 => BRD,
11 => RNG,
12 => SAM,
13 => NIN,
14 => DRG,
15 => SMN,
16 => BLU,
17 => COR,
18 => PUP,
19 => DNC,
20 => SCH
);

$charId = array();
$charName = array();
$charNation = array();
$charZoneId = array();
$charMainJob = array();
$charMainLevel = array();
$charSubJob = array();
$charSubLevel = array();
$charZoneName = array();

// Data for Online Table
try {
    $conn = new PDO("mysql:host=$dbServer;dbname=$dbName", $dbUser, $dbPass);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $getAccountsSessions  = $conn->query('SELECT charid FROM accounts_sessions');
    $i = 0;
    while ($accountsSessionsRow = $getAccountsSessions->fetch())
    {
        $charId[$i] = $accountsSessionsRow['charid'];
        $getChars = $conn->query("SELECT * FROM chars WHERE charid = '$charId[$i]'");
        while ($charsRow = $getChars->fetch())
        {
            $charName[$i] = $charsRow['charname'];
            $charNation[$i] = $charsRow['nation'];
            $charZoneId[$i] = $charsRow['pos_zone'];
        }
        $getCharStats = $conn->query("SELECT * FROM char_stats WHERE charid = '$charId[$i]'");
        while ($charStatsRow = $getCharStats->fetch())
        {
            $charMainJob[$i] = $charStatsRow['mjob'];
            $charMainLevel[$i] = $charStatsRow['mlvl'];
            $charSubJob[$i] = $charStatsRow['sjob'];
            $charSubLevel[$i] = $charStatsRow['slvl'];
        }
        $getZoneSettings = $conn->query("SELECT name FROM zone_settings WHERE zoneid = '$charZoneId[$i]'");
        while ($zoneSettingsRow = $getZoneSettings->fetch())
        {
            $charZoneName[$i] = str_replace("_", " ", $zoneSettingsRow['name']);
        }
        $i = $i + 1;
    }
}

catch(PDOException $e)
    {
    echo "Connection failed: " . $e->getMessage();
    }
?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Shows information of players currently online">
    <title>Canaria - Players Online</title>
</head>

<?php
for ($x = 0; $x < $i ; $x+=1)
{
    if ($x == 0)
    {
        echo "<h3>Characters Online:</h3>";
        echo "<table class='charactersonline'><tr><th>Character Name</th><th>Zone</th><th>Main Job</th><th>Sub Job</th></tr>";
    }
    $mainJob = $jobID[$charMainJob[$x]];
    $subJob = $jobID[$charSubJob[$x]];
    echo "<tr><td>$charName[$x]</td><td>$charZoneName[$x]</td><td>$charMainLevel[$x] $mainJob</td><td>$charSubLevel[$x] $subJob</td></tr>";
//<td>$charSubLevel[$x]  $jobID[$charSubJob[$x]]</td></tr>";
//    echo $charName[$x] . " is @ Zone: " . $charZoneName[$x] . " as a "  .   . "/" .   . "<br>";
}
echo "</table></br></br>";
echo "<h3>Server Last Time Restarted:</h3>";
echo "<p>$uptime</p>";
?>