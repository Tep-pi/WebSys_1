<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Case Study 3</title>
    <style>
        .form{
            max-width: 1000px;
            margin: auto;
            margin-top: 25px;

            border: 1px solid darkblue;
            border-collapse: collapse;
            border-radius: 20px;
            padding: 20px;

            background: aliceblue;
        }

        h1{
            text-align: center;
        }

        .display{

        }

        table{
            margin: auto;
            border-collapse: collapse;
            max-width: 1000px;
        }

        tr, td{
            border: 1px solid black;
            text-align: center;
            width: 50px;
            height: 25px;
            margin: auto;
        }
    </style>
</head>
<body>

<form method="POST" action="cs3.php" class="form">
    <h1>Multiplication Table Generator</h1><br><br>

    Enter max number of row: <input type="number" name="row" id="row" placeholder="Type row here"><br><br>

    Enter max number of column: <input type="number" name="col" id="col" placeholder="Type column here"><br><br>

    <input type="submit"><br>
</form>

<?php

if (isset ($_POST['row']) && isset($_POST['col']))
{
    $row_num = $_POST['row'];
    $col_num = $_POST['col'];

    echo '<div class="display">';

            echo '<br><br><table>';
                echo '<tr>
                    <td> X </td>';
                    for ($rc = 1 ; $rc <= $col_num ; $rc++){
                        if($rc % 2 != 0){
                            echo '<td style="background-color:yellow;"><b>' .$rc. '</b></td>';
                        }else{
                            echo '<td>' .$rc. '</td>';
                        }
                    }
                echo '</tr>';

                for ($r = 1 ; $r<= $row_num ; $r++){
                    echo '<tr>';
                        for($c = 1 ; $c <= $col_num ; $c++){
                            if($c == 2){
                                $c-1;

                                if($r % 2 == 0){
                                    echo ('<td>' .$r. '</td>');
                                }else if($r % 2 !=0){
                                    echo ('<td style="background-color:yellow;"><b>' .$r. '</b></td>');
                                }
                            }

                            if(($r*$c) % 2 != 0 ){
                                echo ('<td style="background-color:yellow;"><b>' .$r*$c. '</b></td>');
                            }

                            if(($r*$c) % 2 == 0){
                                echo ('<td>' .$r*$c. '</td>');
                            }
                        }
                    }
            echo '</table>';

    echo '</div>';
}

?>

</body>
</html>