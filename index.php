<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Terminator</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="grid">
        <div class="grid-item"></div>
        <div class="grid-item">
            <form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="POST">
                <p>
                    <input type="submit" value="Terminate" class="btn-submit" name="submit"> 
                </p>
            </form>
            <?php
            if(isset($_POST['submit'])){
                require("pids.php");
                $server = "localhost:3306";
                $username = "root";
                $password = "Admin123";
                $database = "openmrs";
                $con=new mysqli($server,$username,$password,$database);
                if(!$con)
                {
                    die("Failed to Connect to MYSQL Database Server <br>");
                }
                $counter = 0;
                $clientFacility = 53;
                $visitDate = "2023-03-30";
                $facilityTransferred = "Ameke Oduma Health Center";
                
                foreach ($pids as $key => $getIds) {
                        $con -> query("SET FOREIGN_KEY_CHECKS = 0");
                         #insert into visit table
                        $insert1 ="INSERT INTO `visit`(`patient_id`,`visit_type_id`,`date_started`, `date_stopped`,`location_id`,`creator`,`date_created`,`uuid`) 
                                    VALUES('$getIds','1','$visitDate', '$visitDate','$clientFacility','1',NOW(), UUID())";
                        $con->query($insert1);

                        #select last visit_id
                        $sql2 = $con->query("select max(visit_id) from `visit`");
                        
                        $getVisit = $sql2 -> fetch_row()[0];

                        #insert into encounter table
                        $insert2 ="INSERT INTO `encounter`(`encounter_type`,`patient_id`,`location_id`,`form_id`,`encounter_datetime`,`creator`,`date_created`,`visit_id`, `uuid`) VALUES
                                    ('15', '$getIds','$clientFacility','13','$visitDate','1',NOW(), '$getVisit', UUID())";
                        $con->query($insert2);

                        #select last encounter_id1
                        $sql3 = $con->query("select max(encounter_id) from `encounter` where encounter_type = '15'");
                        $getEncounter = $sql3 -> fetch_row()[0];

                        
                        #insert into encounter_provider table
                        $insert4 ="INSERT INTO `encounter_provider`(`encounter_id`,`provider_id`,`encounter_role_id`,`creator`,`date_created`,`uuid`) 
                                            VALUES('$getEncounter', '1','2','1',NOW(), UUID())";
                        $con->query($insert4);


                        $insertObs ="INSERT INTO `obs`
                            (`person_id`,`concept_id`,`encounter_id`,`obs_datetime`,`location_id`, `value_coded`, `value_datetime`, `value_text`,`creator`,`date_created`,`uuid`) VALUES
                            ('$getIds', '165586', '$getEncounter','$visitDate','$clientFacility', '1065', NULL,NULL,'1',NOW(), UUID()),
                            ('$getIds', '165469', '$getEncounter','$visitDate','$clientFacility', NULL, '$visitDate',NULL,'1',NOW(), UUID()),
                            ('$getIds', '165470', '$getEncounter','$visitDate','$clientFacility', '159492', NULL,NULL,'1',NOW(), UUID()),
                            ('$getIds', '159495', '$getEncounter','$visitDate','$clientFacility', NULL, NULL,'$facilityTransferred','1',NOW(), UUID()),
                            ('$getIds', '165459', '$getEncounter','$visitDate','$clientFacility', NULL, NULL,'Grace Okoli','1',NOW(), UUID()),
                            ('$getIds', '165777', '$getEncounter','$visitDate','$clientFacility', NULL, '$visitDate',NULL,'1',NOW(), UUID())
                            ";
                            $con -> query($insertObs);

                            $con -> query("SET FOREIGN_KEY_CHECKS = 1");
                        $counter++;     
                }
                
                echo "<h3>".$counter." Patients Have been Successfully Terminated</h3>";
            }
            
            
            ?>
        </div>
        <div class="grid-item"></div>
    </div>
</body>
</html>