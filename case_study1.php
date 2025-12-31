<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Case Study 1</title>
    <style>
        .b_bg {
            border: 4px solid black;
            width: 1000px;
            height: 1250px;
            margin: 5%;
            margin-left: 25%;
        }

        .inner {
            background-color:steelblue;
            color:white;
            display: flex;
            margin: 2%;
        }

        img {
            margin: 2%;
            width: 260px;
            height: 250px;
        }

        td {
            border-bottom: 1px solid white;
            border-right: 1px solid white;
        }
    </style>
</head>
<body>
        <?php
            $name = "Steffi May Bauzon";
            $stu = "BSIT Student";
            $phone = "Phone: ";
            $phonenum = "09455233260";
            $em = "Email: ";
            $email = "22ur0632@psu.edu.ph";
            $link = "LinkedIn: ";
            $linkedin = "Steffi May Bauzon";
            $git = "GitLab: ";
            $gitlab = "N/A";
            $addr = "Address: ";
            $address = "Zone 5, Sta. Fe St., Nancamaliran East, Urdaneta City, Pangasinan";
            $bday= "Date of birth: ";
            $birthday = "April 03, 2002";
            $gen = "Gender: ";
            $gender = "Female";
            $nat = "Nationality: ";
            $nationality = "Filipino";

        echo '<div class="b_bg">';
            echo '<div class="inner">';
                echo '<img src="cs_prof.jpg" class="cs_prof" alt="Profile Picture">';
                echo "<h1>$name</h1>";
                echo "<h3>$stu</h3>";

                echo "<table>
                    <tbody>
                        <tr>
                            <td><b>$phone</b>$phonenum</td>
                            <td><b>$addr</b>$address</td>
                        </tr>
                        <tr>
                            <td><b>$em</b>$email</td>
                            <td><b>$bday</b>$birthday</td>
                        </tr>
                        <tr>
                            <td><b>$link</b>$linkedin</td>
                            <td><b>$gen</b>$gender</td>
                        </tr>
                        <tr>
                            <td><b>$git</b>$gitlab</td>
                            <td><b>$nat</b>$nationality</td>
                        </tr>
                    </tbody>
                    </table> <br>";
            echo '</div>';

            echo '<div style="padding:10px; text-align: center;">';
                $intro = "Studying IT in Pangasinan State University. Has gained a fair knowledge about 2D animation. Chosen Major: Web and Mobile Technologies, for the interest of games, animations and overall the creative aspect of the major.";

                echo "<br> $intro <br>";
            echo '</div>';

            echo '<div style="padding:10px;">';
            $educ = "Education";
            $elem_yr = "2008 - 2014";
            $elem_name1 = "Don Felipe Maramba Elementary School";
            $elem_name2 = "Lazaga Elementary School";
            $el2_ach = "Achievement: ";
            $el2_pl = "First Honor";
            $hsh_yr = "2014 - 2020";
            $hsh_name = "International Colleges for Excellence Inc. (ICE)";
            $hsh_act = "Activities: ";
            $hsh_acts = "N/A";
            $col_yr = "2022 - Present";
            $col_cor = "Bachelor of Science in Information Technology";
            $col_name = "Pangasinan State University, Urdaneta City Campus";
            $col_specia = "Specialization: ";
            $col_spec = "N/A";

            //echo "<h3>$educ</h3>";
            echo '<br><div style="border-bottom: 4px solid steelblue">'."<h3>$educ</h3>".'</div>';

            echo '<div style="display: flex;">
                <span style="width: 150px;">'."$elem_yr".'</span>
                <span style="width: 150px;">'."$elem_name1 <br><br>
                $elem_name2 <br><br>$el2_ach<br>
                <li>$el2_pl</li><br>".'</span>
            </div>';

            echo '<div style="display: flex;">
                <span style="width: 150px;">'."$hsh_yr".'</span>
                <span style="width: 150px;">'."$hsh_name <br><br>
                $hsh_act <br>
                <li>$hsh_acts</li><br>".'</span>
            </div>';

            echo '<div style="display: flex;">
                <span style="width: 150px;">'."$col_yr".'</span>
                <span style="width: 150px;">'."<b>$col_cor</b> <br>
                $col_name <br><br> $col_specia<br>
                <li>$col_spec</li>".'</span>
            </div>';

            echo '<div>';
            $exp = "Experience";
            $wut = "N/A";

            //echo "<br> $exp <br>";
            //echo "$wut";
            echo '<br><div style="border-bottom: 4px solid steelblue">'."<h3>$exp</h3>".'</div>';
            echo "$wut";

            echo '<div>';
            $sk = "Skills";
            $ani = "Animation";
            $vis = "Visual Art";
            $ske = "Sketches";

            echo '<br><div style="border-bottom: 4px solid steelblue">'."<h3>$sk</h3>".'</div>';
            echo "<ul>
                <li>$ani</li>
                <li>$vis</li>
                <li>$ske</li>
            </ul>";
            echo '</div>';

        echo '</div>';

        ?>

</body>
</html>