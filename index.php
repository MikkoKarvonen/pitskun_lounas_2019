<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pitskun lounas 2019</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link href="css/toastr.css" rel="stylesheet"/>
    <link rel="stylesheet" type="text/css" href="css/styles.css">
</head>
<body>
    <nav class="navbar navbar-light bg-light center">
        <span class="navbar-brand" href="">
            🍴 Pitskun lounas 2019 🍴
        </span>
    </nav>
    <table class="table">
        <thead class="thead-dark">
            <tr>
                <th scope="col"></th>
                <th scope="col">Lounas</th>
                <th scope="col">Kasvis</th>
                <th scope="col">Deli</th>
                <th scope="col">Jälkiruoka</th>
            </tr>
        </thead>
        <tbody>
            <?php
            include 'credentials.php';
            $conn = new mysqli($servername, $username, $password, $dbname);
            
            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            } 

            $today = new DateTime();
            $weeks = 0;
            $missed = 0;
            $missedMonday = false;
            $days = ['Su', 'Ma', 'Ti', 'Ke', 'To', 'Pe'];
            $curlJson = false;

            echo '<td colspan="5" class="table-secondary text-center">Viikko '.$today->format("W").'</td>';

            $sql = "SELECT date FROM checked WHERE date='".$today->format('Y-m-d')."';";
            $checkedToday = $conn->query($sql);
            
            if ($checkedToday->num_rows == 0) {
                $sql = "INSERT INTO checked (checked, date) VALUES (true, '".$today->format('Y-m-d')."')";
                if ($conn->query($sql) === false) {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }
                $curlJson = true;
            }

            while($weeks < 4){
                if ($today->format('N') < 6){
                    $sql = "SELECT * FROM courses WHERE day='".$today->format('Y-m-d')."';";
                    $result = $conn->query($sql);

                    if ($result->num_rows == 0 && $curlJson) {
                        $url = 'https://www.sodexo.fi/ruokalistat/output/daily_json/27793/'.$today->format('Y/m/d').'/fi';
                        $result = file_get_contents($url);
                        $res = json_decode($result, true);
                        foreach($res['courses'] as $r) {
                            $sql = "INSERT INTO courses (day, category, name, properties)
                            VALUES ('".$today->format('Y-m-d')."', '".$r[category]."', '".$r[title_fi]."', '".$r[properties]."')";
                            if ($conn->query($sql) === false) {
                                echo "Error: " . $sql . "<br>" . $conn->error;
                            }
                        }
                    }
                    $sql = "SELECT * FROM courses WHERE day='".$today->format('Y-m-d')."';";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        if ($missed > 0){
                            $missed = 0;
                        }
                        if (($today->format('N') == 1 || $missedMonday) && $weeks > 0){
                            echo '<td colspan="5" class="table-secondary text-center">Viikko '.$today->format("W").'</td>';
                            if ($missedMonday){
                                $missedMonday = false;
                            }
                        }       
                        $dayContent = '<th scope="row">'.$days[$today->format('w')].'<br>'.$today->format('j.n.').'</th>';
                        while($row = $result->fetch_assoc()) {
                            $dayContent .= '<td>'.$row["name"] .'<br>'. $row["properties"].'</td>';
                        }
                        echo '<tr>'.$dayContent.'</tr>';        
                    } else {
                        $missed++;
                        if ($today->format('N') == 1){
                            $missedMonday = true;
                        }
                    }
                    if ($today->format('N') == 5){
                        $weeks++;
                    }
                    if ($missed >= 3){
                        break;
                    }
                }
                $today->modify( '+1 days' );
            }
            $conn->close();
            ?>
        </tbody>
    </table>
    <div class="footer-copyright text-center py-3">© 2019: 
        <a href="https://github.com/MikkoKarvonen/pitskun_lounas_2019/tree/databaseBranch" target="_blank">Mikko Karvonen</a>
    </div>
    <script src="js/jquery.js"></script>
    <script src="js/toastr.js"></script>
    <script>
        toastr.options = {
            "closeButton": true,
            "positionClass": "toast-bottom-right",
            "timeOut": "5000",
            "extendedTimeOut": "5000",
        }

        Command: toastr["info"]("<a href='https://forms.gle/MXL68yVADLMLGrjC8' target='blank'>Miten sivua voisi parantaa?</a>")
    </script>
</body>
</html>