<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bio-Data</title>
    <style>
        .form{
            border: 4px solid black;
            margin: auto;
            margin-top: 50px;
            margin-bottom: 50px;
            padding: 34px;
            width: 878px;
        }
        .title{
            font-size: 70px;
            text-align: center;
            display: flex;
            margin-left: 280px;
            margin-top: 17px;
            gap: 100px;
        }
        .pd, .eb, .empr, .char, .extra_left{
            border: 3px solid black;
            border-radius: 10px;
            padding: 10px;
        }
        .pd, .eb, .empr, .char, .extra{
            font-size: 19px;
            text-align: left;
        }
        .extra_left{
            width: 364px;
        }
        .extra{
            display: flex;
            gap: 43px;
        }
        .text{
            border: 1px solid white;
            max-width: 364px;
            color: black;
            background: white;
            font-size: 15px;
            text-align: center;
            margin-top: 34px;
        }
        td.from1to{
            width: 50px;
        }
        td{
            width: 200px;
        }
        p{
            border: 3px solid black;
            border-radius: 10px;
            font-size: 20px;
            background: black;
            color: white;
            padding: 7px;
            margin: flex;
            margin-top: 4px;
            margin-bottom: 4px;
        }
        strong{
            margin-top: 43px;
        }
        .img{
            border: 3px solid black;
            height: 150px;
            width: 150px;
            object-fit: cover;
            margin-top: 34px;

        }
    </style>
</head>
<body>
    
    <?php

    if (isset($_POST['submit'])) {
    // Directory where files will be uploaded
    $target_dir = "ws-cs4/";

    // Create the uploads folder if not exists
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

$file_name = basename($_FILES["myfile"]["name"]);
    $target_file = $target_dir . $file_name;
    $uploadOk = 1;

    // Get file extension
    $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Rename file (e.g. with timestamp + random number)
    //$newfileName = strtotime(date());
    $newFileName = uniqid("file_", true) . "." . $fileType;
    
    $target_file = $target_dir . $newFileName;
    // Check if file already exists
    if (file_exists($target_file)) {
        //echo "Sorry, file already exists.<br>";
        $uploadOk = 0;
    }

    // Limit file size (e.g. 2MB)
    if ($_FILES["myfile"]["size"] > 2 * 1024 * 1024) {
        //echo "Sorry, your file is too large (max 2MB).<br>";
        $uploadOk = 0;
    }

    // Allow only specific file formats (e.g. jpg, png, pdf)
    $allowed_types = ["jpg", "jpeg", "png", "gif", "pdf"];
    if (!in_array($fileType, $allowed_types)) {
        //echo "Sorry, only JPG, JPEG, PNG, GIF & PDF files are allowed.<br>";
        $uploadOk = 0;
    }


    // Check if $uploadOk is set to 0
    if ($uploadOk == 0) {
        echo "Your file was not uploaded.<br>";
    } else {
        if (move_uploaded_file($_FILES["myfile"]["tmp_name"], $target_file)) {
            //echo "The file ". htmlspecialchars($file_name). " has been uploaded successfully.<br>";
        } else {
            //echo "Sorry, there was an error uploading your file.<br>";
        }
    }
} else {
    //echo "No file uploaded.";
    }
    ?>

    <?php

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        echo "<div class='form'>";

        //TITLEE
        echo "<div class='title'>";
            echo "<strong>BIO-DATA</strong>";
            echo "<img src='". $target_file. "'class='img'>";
        echo "</div>";
            
        echo "<br>";

        //PER DA
        echo "<p><strong>PERSONAL DATA</strong></p>";
        echo "<div class='pd'>";
            echo "<table class='pd_left'>";
                echo "<tr>";
                    echo "<td>Position Desired</td>";
                        echo "<td style='border-bottom: 1px solid black;'>: ". $position=$_POST['position']. "</td>";
                    echo "<td style='border-bottom: 1px solid black;'>
                        </td><td>&nbsp Date</td>";
                        echo "<td style='border-bottom: 1px solid black;'>: ". $date=$_POST['date']. "</td>";
                echo "</tr>";
                
                echo "<tr>";
                    echo "<td>Name</td>";
                        echo "<td style='border-bottom: 1px solid black;'>: ". $name=$_POST['name']. "</td>";
                    echo "<td style='border-bottom: 1px solid black;'></td><td>&nbsp Gender</td>";
                        echo "<td style='border-bottom: 1px solid black;'>: ". $gender=$_POST['gender']. "</td>";
                echo "</tr>";

                echo "<tr>";
                    echo "<td>City Address</td>";
                        echo "<td style='border-bottom: 1px solid black;'>: ". $cityAdd=$_POST['cityAdd']. "</td>";
                        echo "<td style='border-bottom: 1px solid black;'>"."</td>";
                        echo "<td style='border-bottom: 1px solid black;'>"."</td>";
                        echo "<td style='border-bottom: 1px solid black;'>"."</td>";
                echo "</tr>";

                echo "<tr>";
                    echo "<td>Provincial Address</td>";
                        echo "<td style='border-bottom: 1px solid black;'>: ". $provAdd=$_POST['provAdd']. "</td>";
                        echo "<td style='border-bottom: 1px solid black;'>"."</td>";
                        echo "<td style='border-bottom: 1px solid black;'>"."</td>";
                        echo "<td style='border-bottom: 1px solid black;'>"."</td>";
                echo "</tr>";

                echo "<tr>";
                    echo "<td>Telephone</td>";
                        echo "<td style='border-bottom: 1px solid black;'>: ". $telephone=$_POST['telephone']. "</td>";
                    echo "<td style='border-bottom: 1px solid black;'>"."</td><td>&nbsp Cellphone</td>";
                        echo "<td style='border-bottom: 1px solid black;'>: ". $cellphone1=$_POST['cellphone1'];
                    echo "<tr></tr><td></td><td></td><td></td><td></td>";
                        echo "<td style='border-bottom: 1px solid black;'>: ". $cellphone2=$_POST['cellphone2']. "</td>";
                                //$cellphone2=$_POST['cellphone2']. "</td>";
                echo "</tr>";

                echo "<tr>";
                    echo "<td>E-mail Address</td>";
                        echo "<td style='border-bottom: 1px solid black;'>:&nbsp". $email=$_POST['email']. "</td>";
                        echo "<td style='border-bottom: 1px solid black;'>"."</td>";

                echo "</tr>";

                echo "<tr>";
                    echo "<td>Date of Birth</td>";
                        echo "<td style='border-bottom: 1px solid black;'>: ". $dob=$_POST['dob']. "</td>";
                    echo "<td style='border-bottom: 1px solid black;'>"."</td><td>&nbsp Birth of Place</td>";
                        echo "<td style='border-bottom: 1px solid black;'>: ". $bop=$_POST['bop']. "</td>";
                echo "</tr>";

                echo "<tr>";
                    echo "<td>Civil Status</td>";
                        echo "<td style='border-bottom: 1px solid black;'>: ". $status=$_POST['status']. "</td>";
                    echo "<td style='border-bottom: 1px solid black;'>"."</td><td>&nbsp Citizenship</td>";
                        echo "<td style='border-bottom: 1px solid black;'>: ". $citizenship=$_POST['citizenship']. "</td>";
                echo "</tr>";

                echo "<tr>";
                    echo "<td>Height</td>";
                        echo "<td style='border-bottom: 1px solid black;'>: ". $height=$_POST['height']. "</td>"; 
                    echo "<td style='border-bottom: 1px solid black;'>"."</td><td>&nbsp Weight</td>";
                        echo "<td style='border-bottom: 1px solid black;'>: ". $weight=$_POST['weight']. "</td>";
                echo "</tr>";

                echo "<tr>";
                    echo "<td>Religion</td>";
                        echo "<td style='border-bottom: 1px solid black;'>: ". $religion=$_POST['religion']. "</td>";
                        echo "<td style='border-bottom: 1px solid black;'>"."</td>";
                echo "</tr>";

                echo "<tr>";
                    echo "<td>Spouse</td>";
                        echo "<td style='border-bottom: 1px solid black;'>: ". $spouse=$_POST['spouse']. "</td>";
                    echo "<td style='border-bottom: 1px solid black;'>"."</td><td>&nbsp Occupation</td>";
                        echo "<td style='border-bottom: 1px solid black;'>: ". $spouseOccu=$_POST['spouseOccu']. "</td>";
                echo "</tr>";

                echo "<tr>"; 
                    echo "<td>Name of Children</td>";
                        echo "<td style='border-bottom: 1px solid black;'>: ". $child1=$_POST['child1']. "</td>";
                    echo "<td style='border-bottom: 1px solid black;'>"."</td><td>&nbsp Date of Birth</td>";
                        echo "<td style='border-bottom: 1px solid black;'>: ". $dob1=$_POST['dob1']. "</td>";
                echo "</tr>";

                echo "<tr>";
                    echo "<td></td>";
                    echo "<td style='border-bottom: 1px solid black;'>: ". $child2=$_POST['child2']. "</td>";
                    echo "<td style='border-bottom: 1px solid black;'>"."</td><td></td>";
                    echo "<td style='border-bottom: 1px solid black;'>: ". $dob2=$_POST['dob2']. "</td>";
                echo "</tr>";

                echo "<tr>";
                    echo "<td></td>";
                    echo "<td style='border-bottom: 1px solid black;'>: ". $child3=$_POST['child3']. "</td>";
                    echo "<td style='border-bottom: 1px solid black;'>"."</td><td></td>";
                    echo "<td style='border-bottom: 1px solid black;'>: ". $dob3=$_POST['dob3']. "</td>";
                echo "</tr>";

                echo "<tr>";
                    echo "<td>Father's Name</td>";
                        echo "<td style='border-bottom: 1px solid black;'>: ". $father=$_POST['father']. "</td>";
                    echo "<td style='border-bottom: 1px solid black;'>"."</td><td>&nbsp Occupation</td>";
                        echo "<td style='border-bottom: 1px solid black;'>: ". $fatherOccu=$_POST['fatherOccu']. "</td>";
                echo "</tr>";

                echo "<tr>";
                    echo "<td>Mother's Name</td>";
                        echo "<td style='border-bottom: 1px solid black;'>: ". $mother=$_POST['mother']. "</td>";
                    echo "<td style='border-bottom: 1px solid black;'>"."</td><td>&nbsp Occupation</td>";
                        echo "<td style='border-bottom: 1px solid black;'>: ". $motherOccu=$_POST['motherOccu']. "</td>";
                echo "</tr>";
            echo "</table><br>";

            echo "Language or dialect spoken and written: ".
                "<span style='display: inline-block;
                border-bottom: 1px solid black;
                min-width: 504px;'>". $language=$_POST['language']. "</span><br>";

            echo "Person to be contacted in case of emergency: ".
                "<span style='display: inline-block;
                border-bottom: 1px solid black;
                min-width: 504px;'>".  $contactPerson=$_POST['contactPerson']. "</span><br>";

            echo "His or her address and telephone: ".
                "<span style='display: inline-block;
                border-bottom: 1px solid black;
                min-width: 504px;'>".  $contactInfo=$_POST['contactInfo']."</span><br>";
        echo "</div>";


        //EDU BG
        echo "<p><strong>EDUCATIONAL BACKGROUND</strong></p>";
        echo "<div class='eb'>";
            echo "<table class='eb_left'>";
                echo "<tr>";
                    echo "<td>Elementary</td>";
                        echo "<td style='border-bottom: 1px solid black;'>: ". $elem=$_POST['elem']. "</td>";
                    echo "<td style='border-bottom: 1px solid black;'>"."</td><td>&nbsp Year Graduated</td>";
                        echo "<td style='border-bottom: 1px solid black;'>: ". $elemGrad=$_POST['elemGrad']. "</td>";
                echo "</tr>";

                echo "<tr>";
                    echo "<td>High School</td>";
                        echo "<td style='border-bottom: 1px solid black;'>: ". $hsh=$_POST['hsh']. "</td>";
                    echo "<td style='border-bottom: 1px solid black;'>"."</td><td>&nbsp Year Graduated</td>";
                        echo "<td style='border-bottom: 1px solid black;'>: ". $hshGrad=$_POST['hshGrad']. "</td>";
                echo "</tr>";
                
                echo "<tr>";
                    echo "<td>College</td>";
                        echo "<td style='border-bottom: 1px solid black;'>: ". $college=$_POST['college']. "</td>";
                    echo "<td style='border-bottom: 1px solid black;'>"."</td><td>&nbsp Year Graduated</td>";
                        echo "<td style='border-bottom: 1px solid black;'>: ". $collegeGrad=$_POST['collegeGrad']. "</td>";
                echo "</tr>";
            echo "</table><br>";
            
            echo "Degree Received: ".
                "<span style='display: inline-block;
                border-bottom: 1px solid black;
                min-width: 504px;'>". $degree=$_POST['degree']. "</span><br>";

            echo "Special Skills: ".
                "<span style='display: inline-block;
                border-bottom: 1px solid black;
                min-width: 504px;'>". $skills=$_POST['skills']. "</span><br>";
        echo "</div>";


        //EMP HEERWWE
        echo "<p><strong>EMPLOYMENT RECORD</strong></p>";
        echo "<div class='empr'>";
            echo "<table class='empr_left'";
                echo "<tr>";
                    echo "<td>Company Name</td>";
                        echo "<td style='border-bottom: 1px solid black;'>: ". $empCompany1=$_POST['empCompany1']. "</td>";
                        echo "<td style='border-bottom: 1px solid black;'>"."</td>";
                        echo "<td style='border-bottom: 1px solid black;'>"."</td>";
                        echo "<td style='border-bottom: 1px solid black;'>"."</td>";
                        echo "<td style='border-bottom: 1px solid black;'>"."</td>";
                        echo "<td style='border-bottom: 1px solid black;'>"."</td>";
                echo "</tr>";

                echo "<tr>";
                    echo "<td>Position</td>";
                        echo "<td style='border-bottom: 1px solid black;'>: ". $empPost1=$_POST['empPost1']. "</td>";

                    echo "<td style='border-bottom: 1px solid black;'>"."</td><td>
                        &nbsp
                        From</td>";
                        echo "<td style='border-bottom: 1px solid black;'>: ". $from1=$_POST['from1']. "</td>";
                    echo "<td>&nbsp 
                        To</td>";
                        echo "<td style='border-bottom: 1px solid black;'>: ". $to1=$_POST['to1']. "</td>";
                echo "</tr>";

                echo "<tr><tr><tr><tr></tr></tr></tr></tr>";

                echo "<tr>";
                    echo "<td>Company Name</td>";
                        echo "<td style='border-bottom: 1px solid black;'>: ". $empCompany2=$_POST['empCompany2']. "</td>";
                        echo "<td style='border-bottom: 1px solid black;'>"."</td>";
                        echo "<td style='border-bottom: 1px solid black;'>"."</td>";
                        echo "<td style='border-bottom: 1px solid black;'>"."</td>";
                        echo "<td style='border-bottom: 1px solid black;'>"."</td>";
                        echo "<td style='border-bottom: 1px solid black;'>"."</td>";
                echo "</tr>";

                echo "<tr>";
                    echo "<td>Position</td>";
                        echo "<td style='border-bottom: 1px solid black;'>: ". $empPost2=$_POST['empPost2']. "</td>";

                    echo "<td style='border-bottom: 1px solid black;'>"."</td><td>
                        &nbsp 
                        From</td>";
                        echo "<td style='border-bottom: 1px solid black;'>: ". $from2=$_POST['from2']. "</td>";
                    echo "<td>&nbsp 
                        To</td>";
                        echo "<td style='border-bottom: 1px solid black;'>: ". $to1=$_POST['to2']. "</td>";
                    echo "</div>";
                echo "</tr>";
            echo "</table>";
        echo "</div>";


        //CHAR REF
        echo "<p><strong>CHARACTER REFERENCE</strong></p>";
        echo "<div class='char'>";
            echo "<table class='char_left'>";
                echo "<tr>";
                    echo "<td>Name</td>";
                        echo "<td style='border-bottom: 1px solid black;'>: ". $charName1=$_POST['charName1']. "</td>";
                    echo "<td style='border-bottom: 1px solid black;'>"."</td><td>
                        &nbsp 
                        Company</td>";
                        echo "<td style='border-bottom: 1px solid black;'>: ". $charCompany1=$_POST['charCompany1']. "</td>";
                echo "</tr>";

                echo "<tr>";
                    echo "<td>Position</td>";
                        echo "<td style='border-bottom: 1px solid black;'>: ". $charPos1=$_POST['charPos1']. "</td>";
                    echo "<td style='border-bottom: 1px solid black;'>"."</td><td>
                        &nbsp 
                        Contact No.</td>";
                        echo "<td style='border-bottom: 1px solid black;'>: ". $charContact1=$_POST['charContact1']. "</td>";
                echo "<tr><tr><tr><tr></tr></tr></tr></tr>";

                echo "<tr>";
                    echo "<td>Name</td>";
                        echo "<td style='border-bottom: 1px solid black;'>: ". $charName2=$_POST['charName2']. "</td>";
                    echo "<td style='border-bottom: 1px solid black;'>"."</td><td>
                        &nbsp 
                        Company</td>";
                        echo "<td style='border-bottom: 1px solid black;'>: ". $charCompany2=$_POST['charCompany2']. "</td>";
                echo "</tr>";

                echo "<tr>";
                    echo "<td>Position</td>";
                        echo "<td style='border-bottom: 1px solid black;'>: ". $charPos2=$_POST['charPos2']. "</td>";
                    echo "<td style='border-bottom: 1px solid black;'>"."</td><td>
                        &nbsp 
                        Contact No.</td>";
                        echo "<td style='border-bottom: 1px solid black;'>: ". $charContact2=$_POST['charContact2']. "</td>";
                echo "</tr>";
            echo "</table>";
        echo "</div><br>";


        //ADD INFO
        echo "<div class='extra'>";
            echo "<table class='extra_left'>";
                echo "<tr>";
                    echo "<td>Res. Cert. No.</td>";
                        echo "<td style='border-bottom: 1px solid black;'>: ". $resCertNo=$_POST['resCertNo']. "</td>";
                echo "</tr>";

                echo "<tr>";
                    echo "<td>Issued at</td>";
                        echo "<td style='border-bottom: 1px solid black;'>: ". $issAt=$_POST['issAt']. "</td>";
                echo "</tr>";

                echo "<tr>";
                    echo "<td>Issued on</td>";
                        echo "<td style='border-bottom: 1px solid black;'>: ". $issOn=$_POST['issOn']. "</td>";
                echo "</tr>";

                echo "<tr>";
                    echo "<td>SSS</td>";
                        echo "<td style='border-bottom: 1px solid black;'>: ". $sss=$_POST['sss']. "</td>";
                echo "</tr>";

                echo "<tr>";
                    echo "<td>TIN</td>";
                        echo "<td style='border-bottom: 1px solid black;'>: ". $tin=$_POST['tin']. "</td>";
                echo "</tr>";

                echo "<tr>";
                    echo "<td>NBI No.</td>";
                        echo "<td style='border-bottom: 1px solid black;'>: ". $nbi=$_POST['nbi']. "</td>";
                echo "</tr>";

                echo "<tr>";
                    echo "<td>Passport No.</td>";
                        echo "<td style='border-bottom: 1px solid black;'>: ". $passport=$_POST['passport']. "</td>";
                echo "</tr>";
            echo "</table>";

            echo "<br><div class='extra_right'>";
                echo "<p class='text'>I here certify that the above information is true and correct to the best of my knowledge and belief. I also understand that any misinterpretation will be considered reason for withdrawal of an offer or subsequent dismissal if employed.";

                echo "<br><br>";

                echo "_______________________________________________";
                echo "<br>";
                echo "Applicant's Signature</p>";
            echo "</div>";
        echo "</div>";
    echo "</div>";
    }

?>

</body>
</html>