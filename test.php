<html>
<head>
    <meta charset="UTF-8"/>
	<title>calendrier</title>
	
	<style type="text/css">
	table th{
		padding : 5px;
		border: 2px groove black;
		width: 75px;
		height: 25px;
	}
	table td{
		padding : 5px;
		text-align : center;
	}
	#today{
		color: blue;
	}
	.dimanche{
		color:red;
	}
	</style>
</head>
<body>
<?php
error_reporting(E_ALL);

define("GET_DATE_FORMAT","Y-m-d");
if(isset($_GET['firstday'])){
	//on vérifie le format de l'entrée GET : n'oubliez pas qu'il ne faut jamais faire confiance aux entrées utilisateur!
	try{
		
		$firstDay = DateTime::createFromFormat(GET_DATE_FORMAT,$_GET['firstday']);//si la donnée est au mauvais format, une exception est lancée
		
		$firstDay->modify("first day of this month");//pour être vraiment sûr que les données sont cohérentes
	}catch(Excpetion $e){
		
		$firstDay = new DateTime("first day of this month");
	}
}else{
	$firstDay = new DateTime("first day of this month");
}



$formatter_semaine = new IntlDateFormatter("fr_FR",IntlDateFormatter::FULL,IntlDateFormatter::FULL,'Europe/Paris',
                IntlDateFormatter::GREGORIAN,"EEEE" );
$formatter_semaine->setPattern("EEEE");
$formatter_mois = new IntlDateFormatter("fr_FR",IntlDateFormatter::FULL,IntlDateFormatter::NONE,'Europe/Paris',
                IntlDateFormatter::GREGORIAN,"MMMM" );
$formatter_mois->setPattern("MMMM");
?>
<h1><?php echo $formatter_mois->format($firstDay);?></h1>
<?php
$lastDay = clone $firstDay;
$lastDay->modify("last day of this month");// le dernier jour du mois
$offset_depart = $firstDay->format("w");// le nombre de jour qu'il faut passer au début du calendrier
$offset_fin = 6 - $lastDay->format("w");// le nombre de jour qu'il reste dans la dernière semaine du calendrier
$firstDay->modify("-$offset_depart days" );
$lastDay->modify("+$offset_fin days");
$dateInitWeek = clone $firstDay;
$endInitWeek = clone $dateInitWeek;
$endInitWeek->modify("+7 days");
$intervalInitWeek = new DateInterval("P1DT0S");
$aujourdhui = new DateTime("today");
?>
<table>
	<tbody>
		<tr>
			<?php 
			foreach(new DatePeriod($dateInitWeek,$intervalInitWeek,$endInitWeek) as $jour){
				 if($jour->format("w")==0){
					echo "<th class=\"dimanche\">",$formatter_semaine->format($jour),"</th>";
				}else{
					echo "<th>",$formatter_semaine->format($jour),"</th>";
				}
				
			}?>
		</tr>

<?php

$lastDay->modify("+1 day");//c'est une astuce pour utiliser DatePeriod 
//qui ne sait pas prendre en compte le dernier jour si on ne fait pas ça
$intervale_iteration = new DateInterval("P1DT0S");
$iterateur = new DatePeriod($firstDay,$intervale_iteration,$lastDay);
$i = 0;
foreach($iterateur as $jour){
	if($i == 0){
		echo "<tr>";
	}
	if($jour == $aujourdhui){
					echo "<td id=\"today\">",$jour->format("d"),"</td>";
				}else if($jour->format("w")==0){
					echo "<td class=\"dimanche\">",$jour->format("d"),"</td>";
				}else{
					echo "<td>",$jour->format("d"),"</td>";
				}
	$i++;
	$i %=7;
	if($i == 0){
		echo "<tr/>";
	}
}
?>
	</tbody>
</table>
<?php

setlocale(LC_TIME, "fr_FR");
echo strftime(" in French %B %A and")
?>

</body>
</html>
