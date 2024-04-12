<?php
require(__DIR__. "/partials/nav.php");
?>

<h2>Cars</h2>

<table>
<tr>
    <th>Make</th>
    <th>Model</th>
    <th>Year</th>
</tr>

<?php
 $db = getDB();
 $stmt = $db->prepare("SELECT make, model, year from Cars");

 try{
    $r = $stmt->execute();
    if($r){
        $cars = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        //echo var_export($cars);
        foreach($cars as $car){
            echo '<tr>';
            //echo var_export($car);
            echo '<td>' . $car['make'] . '</td>';
            //echo "\n";
            echo '<td>' . $car['model'] .' </td>';
            echo '</tr>';
            //echo "<br>";
        }
    }
 } catch (Exception $e){
    echo var_dump($e);
 }
?>
</table>
