<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bio-data</title>
    <style>
        form{
            border: 4px solid black;
            border-radius: 20px;
            padding: 34px;
            width: 834px;
            margin: auto;
            margin-top: 20px;
        }
        h1{
            text-align: center;
            border: 2px solid black;
        }

        .pd, .eb, .empr, .char, .extra{
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            margin-left: 110px;
        }
        .pd_left,.pd_right{
            width: 200px;
        }
        .pd_bottom, .eb_buttom{
            display: block;
            margin-left: 110px;
        }

        textarea{
            width: 600.5px;
            height: 16px;
            resize: none;
        }
        p{
            text-align: center;
            font-size: 22px;
            border-top: 2px solid black;
            padding-top: 25px;
        }
        input[type=submit]{
            font-size: 17px;
        }
    </style>
</head>
<body>
    <form method="post" action="bio-data_output.php" enctype="multipart/form-data">

        <h1>Bio-Data Fill-up Form</h1>

        <div class="pfp">
            Select image to upload as your profile: <br>
            <input type="file" name="myfile" id="myfile"><br><br>
            <strong>Notice:</strong> max of 2MB in size and only JPG, JPEG, PNG, GIF & PDF files are allowed.
        </div>

        <br>
        <p><b>PERSONAL DATA</b></p>
        <div class="pd">
            <div class="pd_left">
                <?php
                echo 'Position Desired:<br>
                <input type="text" name="position" placeholder="Your desired position.">
                <span class="error"></span>
                <br>';

                echo 'Name:<br>
                <input type="text" name="name" placeholder="Your full name.">
                <span class="error"></span>
                <br>';

                echo 'City Address:<br>
                <textarea name="cityAdd" placeholder="Your city address."></textarea>
                <span class="error"></span>
                <br>';

                echo 'Provincial Address:<br>
                <textarea name="provAdd" placeholder="Your provincial address."></textarea>
                <span class="error"></span>
                <br>';

                echo 'Telephone:<br>
                <input type="text" name="telephone" placeholder="Your telephone number.">
                <span class="error"></span>
                <br>';

                echo 'E-mail Address:<br>
                <input type="text" name="email" placeholder="Your e-mail address.">
                <span class="error"></span>
                <br>';

                echo 'Date of Birth:<br>
                <input type="text" name="dob" placeholder="Your birhtday (e.g. M/D/Y)">
                <span class="error"></span>
                <br><br>';

                echo 'Civil Status:
                    <select name="status">
                        <option value="">Select</option>
                        <option value="single">Single</option>
                        <option value="married">Married</option>
                        <option value="other">Other</option>
                    </select>
                    <span class="error"></span>
                    <br><br>';

                echo 'Height:<br>
                <input type="text" name="height" placeholder="Specify if in ft or cm">
                <span class="error"></span>
                <br>';

                echo 'Religion:<br>
                <input type="text" name="religion" placeholder="Your religion.">
                <span class="error"></span>
                <br>';

                echo 'Spouse:<br>
                <input type="text" name="spouse" placeholder="Your spouse`s name.">
                <span class="error"></span>
                <br>';

                echo 'Name of Children:<br>
                <input type="text" name="child1" placeholder="First child name."><span class="error"></span><br>
                <input type="text" name="child2" placeholder="Second child name."><span class="error"></span><br>
                <input type="text" name="child3" placeholder="Third child name."><span class="error"></span><br>';

                echo "Father's Name:<br>
                <input type='text' name='father' placeholder='Your father`s name.'>
                <span class='error'></span>
                <br>";

                echo "Mother's Name:<br>
                <input type='text' name='mother' placeholder='Your mother`s name.'>
                <span class='error'></span>
                <br>";
                ?>
            </div>
            <div class="pd_right">
                <?php
                
                echo '&nbsp&nbsp&nbsp&nbsp
                Date:<br>
                    &nbsp&nbsp&nbsp&nbsp
                <input type="text" name="date" placeholder="M/D/Y">
                <span class="error"></span>
                <br><br>';

                echo '&nbsp&nbsp&nbsp&nbsp
                Gender:
                    <select name="gender">
                        <option value="">Select</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                    <span class="error"></span>
                    <br>';

                echo '<br><br><br><br><br><br><br>';

                echo '&nbsp&nbsp&nbsp&nbsp
                Cellphone:<br>
                    &nbsp&nbsp&nbsp&nbsp
                <input type="text" name="cellphone1" placeholder="Your cellphone number."><span class="error"></span><br>
                    &nbsp&nbsp&nbsp&nbsp
                <input type="text" name="cellphone2" placeholder="e.g. +63 9123456789"><span class="error"></span><br>';

                echo '<br>';

                echo '&nbsp&nbsp&nbsp&nbsp
                Birth of Place:<br>
                    &nbsp&nbsp&nbsp&nbsp
                <input type="text" name="bop" placeholder="City and Province.">
                <span class="error"></span>
                <br>';

                echo '&nbsp&nbsp&nbsp&nbsp
                Citizenship:<br>
                    &nbsp&nbsp&nbsp&nbsp
                <input type="text" name="citizenship" placeholder="e.g. Filipino">
                <span class="error"></span>
                <br><br>';

                echo '&nbsp&nbsp&nbsp&nbsp
                Weight:<br>
                    &nbsp&nbsp&nbsp&nbsp
                <input type="text" name="weight" placeholder="Specify if in lb or kg.">
                <span class="error"></span>
                <br>';

                echo '<br><br>';

                echo '&nbsp&nbsp&nbsp&nbsp
                Occupation:<br>
                    &nbsp&nbsp&nbsp&nbsp
                <input type="text" name="spouseOccu" placeholder="Spouse`s occupation">
                <span class="error"></span>
                <br>';

                echo '&nbsp&nbsp&nbsp&nbsp
                Date of Birth:<br>
                    &nbsp&nbsp&nbsp&nbsp
                <input type="text" name="dob1" placeholder="M/D/Y"><span class="error"></span><br>
                    &nbsp&nbsp&nbsp&nbsp
                <input type="text" name="dob2" placeholder="M/D/Y"><span class="error"></span><br>
                    &nbsp&nbsp&nbsp&nbsp
                <input type="text" name="dob3" placeholder="M/D/Y"><span class="error"></span><br>';

                echo '&nbsp&nbsp&nbsp&nbsp
                Occupation:<br>
                    &nbsp&nbsp&nbsp&nbsp
                <input type="text" name="fatherOccu" placeholder="Your father`s occupation.">
                <span class="error"></span>
                <br>';

                echo '&nbsp&nbsp&nbsp&nbsp
                Occupation:<br>
                    &nbsp&nbsp&nbsp&nbsp
                <input type="text" name="motherOccu" placeholder="Your mother`s occupation.">
                <span class="error"></span>
                <br>';
                ?>
            </div>

        </div>

        <br>

        <div class="pd_bottom">
        <?php
            echo 'Language or dialect spoken and written:<br>
            <textarea name="language" placeholder="Type here."></textarea>
            <span class="error"></span>
            <br>';

            echo 'Person to be contacted in case of emergency:<br>
            <textarea name="contactPerson" placeholder="Type here."></textarea>
            <span class="error"></span>
            <br>';

            echo 'His or her address and telephone:<br>
            <textarea name="contactInfo" placeholder="Type here."></textarea>
            <span class="error"></span>
            <br>';
        ?>
        </div>

        <br>
        <p><b>EDUCATIONAL BACKGROUND</b></p>
        <div class="eb">
            <div class="eb_left">
                <?php
                echo 'Elementary:<br>
                <input type="text" name="elem" placeholder="Name of school.">
                <span class="error"></span>
                <br>';

                echo 'High School:<br>
                <input type="text" name="hsh" placeholder="Name of highschool.">
                <span class="error"></span>
                <br>';

                echo 'College:<br>
                <input type="text" name="college" placeholder="Name of college.">
                <span class="error"></span>
                <br>';
                ?>
            </div>
            <div class="eb_right">
                <?php
                echo '&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                Year Graduated:<br>
                    &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                <input type="text" name="elemGrad" placeholder="Type year here.">
                <span class="error"></span>
                <br>';

                echo '&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                Year Graduated:<br>
                    &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                <input type="text" name="hshGrad" placeholder="Type year here.">
                <span class="error"></span>
                <br>';

                echo '&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                Year Graduated:<br>
                    &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                <input type="text" name="collegeGrad" placeholder="Type year here.">
                <span class="error"></span>
                <br>';
                ?>
            </div>
        </div>
            <div class="eb_buttom">
            <?php
            echo 'Degree Received:<br>
            <textarea name="degree" placeholder="Type here."></textarea>
            <span class="error"></span>
            <br>';

            echo 'Special Skills:<br>
            <textarea name="skills" placeholder="Type here."></textarea>
            <span class="error"></span>
            <br>';
            ?>
            </div>

        <br>
        <p><b>EMPLOYMENT RECORD</b></p>
        <div class="empr">
            <div class="empr_left">
                <?php
                echo 'Company Name:<br>
                <textarea name="empCompany1" placeholder="Previous company name employed at."></textarea>
                <span class="error"></span>
                <br>';
                echo 'Postion:<br>
                <input type="text" name="empPost1" placeholder="Previous company position.">
                <span class="error"></span>';
                echo ' From:
                <input type="text" name="from1" placeholder="Month and Year">
                <span class="error"></span>';
                echo ' To:
                <input type="text" name="to1" placeholder="Month and Year"><br>
                <span class="error"></span>';

                echo 'Company Name:<br>
                <textarea name="empCompany2" placeholder="Previous company name employed at."></textarea>
                <span class="error"></span>
                <br>';
                echo 'Position:<br>
                <input type="text" name="empPost2" placeholder="Previous company position.">
                <span class="error"></span>';
                echo ' From:
                <input type="text" name="from2" placeholder="Month and Year">
                <span class="error"></span>';
                echo ' To:
                <input type="text" name="to2" placeholder="Month and Year"><br>
                <span class="error"></span>';
                ?>
            </div>
        </div>

        <br>
        <p><b>CHARACTER REFERENCE</b></p>
        <div class="char">
            <div class="char_left">
                <?php
                echo 'Name:<br>
                <input type="text" name="charName1" placeholder="Name of Contact person.">
                <span class="error"></span>
                <br>';
                echo 'Position:<br>
                <input type="text" name="charPos1" placeholder="Contact person`s position.">
                <span class="error"></span>
                <br><br>';

                echo 'Name:<br>
                <input type="text" name="charName2" placeholder="Name of contact person.">
                <span class="error"></span>
                <br>';
                echo 'Position:<br>
                <input type="text" name="charPos2" placeholder="Contact person`s position.">
                <span class="error"></span>
                <br>';
                ?>
            </div>
            <div class="char_right">
                <?php
                echo '&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                Company:<br>
                    &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                <input type="text" name="charCompany1" placeholder="Contact person`s workplace.">
                <span class="error"></span>
                <br>';

                echo '&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                Contact No.:<br>
                    &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                <input type="text" name="charContact1" placeholder="e.g. +63 9123456789">
                <span class="error"></span>
                <br><br>';

                echo '&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                Company:<br>
                    &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                <input type="text" name="charCompany2" placeholder="Contact person`s workplace.">
                <span class="error"></span>
                <br>';

                echo '&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                Contact No.:<br>
                    &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                <input type="text" name="charContact2" placeholder="e.g. +63 9123456789">
                <span class="error"></span>
                <br>';
                ?>
            </div>
        </div>

        <br>
        <p><b>ADDITIONAL INFORMATION</b></p>
        <div class="extra">
            <div class="extra_left">
                <?php
                echo 'Res. Cert. No.:<br>
                <input type="text" name="resCertNo" placeholder="Type here.">
                <span class="error"></span>
                <br>';

                echo 'Issued at:<br>
                <input type="text" name="issAt" placeholder="Type here.">
                <span class="error"></span>
                <br>';

                echo 'Issued on:<br>
                <input type="text" name="issOn" placeholder="Type here.">
                <span class="error"></span>
                <br>';

                echo 'SSS:<br>
                <input type="text" name="sss" placeholder="Type here.">
                <span class="error"></span>
                <br>';

                echo 'TIN:<br>
                <input type="text" name="tin" placeholder="Type here.">
                <span class="error"></span>
                <br>';

                echo 'NBI No.:<br>
                <input type="text" name="nbi" placeholder="Type here.">
                <span class="error"></span>
                <br>';

                echo 'Passport No.:<br>
                <input type="text" name="passport" placeholder="Type here.">
                <span class="error"></span>
                <br>';
                ?>
            </div>
            <div class="extra_right">
                <?php
                // echo 'I here certify that the above information is true and correct to the best of my knowledge and belief. I also understand that any misinterpretation will be considered reason for withdrawal of an offer or subsequent dismissal if employed.';

                // echo '<br>';
                // echo '<br>';

                // echo '____________________________________________________________';
                // echo '<br>';
                // echo "Applicant's Signature";
                ?>
            </div>
        </div>

        <br>
        <p><input type="submit" value="Submit" name="submit"></p>

    </form>
</body>
</html>