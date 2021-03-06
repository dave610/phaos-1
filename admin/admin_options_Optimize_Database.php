<html>
<head>
<title>WoP Admin Panel - Optimize SQL Database</title>
<link href="../styles/phaos.css" rel="stylesheet" type="text/css">
</head>
<body>
<center>
<?php

require_once("../config_settings.php");
include "aup.php";

$sql = array();
$sql['host'] = $game_host;
$sql['user'] = $game_dbuser;
$sql['pass'] = $game_dbpasswd;
$sql['db']   = $game_db;
    
$db = mysqli_connect($sql['host'], $sql['user'], $sql['pass'], $sql['db']) or die ("Can't connect, check the login info!\n");
//mysqli_select_db($sql['db']) or die ("Can't connect to the database, check the login info!\n");

function optimaliseer()
{
    global $db, $sql;
    $tabellen = mysqli_list_tables($sql['db']);
    $totaal = 0;
    
    echo "Size of the Tables:<br /><br />
        <table width=\"600\" border=\"0\" cellspacing=\"2\" cellpadding=\"0\">
        <tr>
            <td width=\"200\"><strong>TABLE</strong></td>
            <td width=\"200\"><strong>BEFORE</STRONG></td>
            <td width=\"200\"><strong>AFTER</strong></td>
        </tr>";
    
    while($tabel_naam = mysqli_fetch_row($tabellen))
    {
        $grootte1 = 0;
        $grootte2 = 0;
        
        $query1 = mysqli_query($db, "SHOW TABLE STATUS FROM ".$sql['db']." LIKE '".$tabel_naam[0]."'") or die(mysqli_error());
        mysqli_query($db, "OPTIMIZE TABLE ".$tabel_naam[0]) or die(mysqli_error());
        $query2 = mysqli_query($db, "SHOW TABLE STATUS FROM ".$sql['db']." LIKE '".$tabel_naam[0]."'") or die(mysqli_error());
        
        while($data1 = mysqli_fetch_assoc($query1))
            $grootte1 = $grootte1 + floatval($data1["Data_length"]) + floatval($data1["Index_length"]);
        
        while($data2 = mysqli_fetch_assoc($query2))
            $grootte2 = $grootte2 + floatval($data2["Data_length"]) + floatval($data2["Index_length"]);
        
        echo "<tr><td>".$tabel_naam[0]."</td>";
        echo "<td>".$grootte1." bytes</td>";
        echo "<td>".$grootte2." bytes</td></tr>";
        
        $totaal += $grootte1 - $grootte2;
        mysqli_free_result($query1);
        mysqli_free_result($query2);
    }
    
    echo "</tr></table><br /><br />";
    echo "Space Saved ".$totaal." bytes.";
}

optimaliseer();
?>
<form><input type='button' onClick="parent.location='index.php'" value='Back To Admin Panel'></form>
</center>
</body>
</html>
