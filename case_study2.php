<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Case Study 2</title>
    <style>
        .out_des{
            border: 2px solid black;
            padding: 10px;
            width: 332px;
            height: flex;
            margin-left: 40%;
            margin-top: 2%;
            font-size: 20px;
        }

        .design {
            border: 2px solid black;
            padding: 8px;
            width: 310px;
            height: flex;
            background-color: aliceblue;
        }
    </style>
</head>
<body>
    <?php
        $stu_name = $_GET['stu_name'];
        $stu_score = $_GET['stu_score'];

        if($stu_score >=95 && $stu_score <=100){
            $stu_grade = "Excellent";
            $stu_remark = "A - Outstanding Performance!";
        } else
        if($stu_score >=90 && $stu_score <=94){
            $stu_grade = "Very Good";
            $stu_remark = "B - Great Job!";
        } else
        if($stu_score >=85 && $stu_score <=89){
            $stu_grade = "Good";
            $stu_remark = "C - Good effort, keep it up!";
        } else
        if($stu_score >=75 && $stu_score <=84){
            $stu_grade = "Needs Improvement";
            $stu_remark = "D - Work harder next time.";
        } else {
            $stu_grade = "Failed";
            $stu_remark = "F - You need to improve.";
        }

        echo "<div class=out_des>".
        "<b>Student Grade Info</b><br><br>".
        "<div class=design>";

            echo "Student Name: "."$stu_name";
            echo "<br>";
            echo "Score: "."$stu_score";

            echo "<br>_______________________________<br><br>";

            echo "Grade: "."$stu_score"." ($stu_grade)";
            echo "<br>";
            echo "Remark: "."$stu_remark";

        echo "</div></div>";
    ?>
</body>
</html>