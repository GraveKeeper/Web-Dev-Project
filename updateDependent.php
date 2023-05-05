<?php
	session_start();	
	require_once "config.php";
 
$Dependent_name =  $Bdate  = $Sex = $Relationship = "";
$Dependent_name_err = $Bdate_err = $Sex_err = $Relationship_err = "";

if(isset($_GET["Dependent_name"]) && !empty(trim($_GET["Dependent_name"]))){
	$_SESSION["Dependent_name"] = $_GET["Dependent_name"];

    $sql1 = "SELECT * FROM DEPENDENT WHERE Essn = ? AND Dependent_name = ?";
  
    if($stmt1 = mysqli_prepare($link, $sql1)){
        mysqli_stmt_bind_param($stmt1, "is", $param_Essn, $param_Dependent_name);      
       $param_Essn = $_SESSION["Ssn"];
       $param_Dependent_name = $_SESSION["Dependent_name"];

        if(mysqli_stmt_execute($stmt1)){
            $result1 = mysqli_stmt_get_result($stmt1);
			if(mysqli_num_rows($result1) > 0){

				$row = mysqli_fetch_array($result1);

                
				$Dependent_name = $row['Dependent_name'];
                $Essn = $row['Essn'];
				$Relationship = $row['Relationship'];
				$Sex = $row['Sex'];
				$Bdate = $row['Bdate'];
			}
		}
	}
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $Essn = $_SESSION["Ssn"];
    $Old_Dependent_name = $_SESSION["Dependent_name"];
    

    $Dependent_name = trim($_POST["Dependent_name"]);
    if(empty($Dependent_name)){
        $Dependent_name_err = "Please enter a dependent name.";
    } elseif(!filter_var($Dependent_name, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $Dependent_name_err = "Please enter a valid dependent name.";
    } 

    $Relationship = trim($_POST["Relationship"]);
    if(empty($Relationship)){
        $Relationship_err = "Please enter a Relationship.";
    } elseif(!filter_var($Relationship, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $Relationship_err = "Please enter a valid Relationship.";
    } 

    $Sex = trim($_POST["Sex"]);
    if(empty($Sex)){
        $Sex_err = "Please enter Sex.";     
    }

    $Bdate = trim($_POST["Bdate"]);
    if(empty($Bdate)){
        $Bdate_err = "Please enter birthdate.";     
    }  

    if(empty($Dependent_name_err) && empty($Sex_err) && empty($Bdate_err) && empty($Relationship_err)){  
        $sql = "UPDATE DEPENDENT SET Dependent_name=?, Relationship=?, Sex=?, Bdate=? WHERE Essn=? AND Dependent_name=?";
    
        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "ssssis", $param_Dependent_name, $param_Relationship, $param_Sex, $param_Bdate, 
                $param_Essn, $param_Old_Dependent_name);

            $param_Essn = $Essn;
            $param_Dependent_name = $Dependent_name;
            $param_Relationship = $Relationship;
            $param_Sex = $Sex;
            $param_Bdate = $Bdate;
            $param_Old_Dependent_name = $Old_Dependent_name;
            
            if(mysqli_stmt_execute($stmt)){
                header("location: index.php");
                exit();
            } else{
                echo "<center><h2>Error when updating</center></h2>";
            }
        }        
        mysqli_stmt_close($stmt);
    }

    mysqli_close($link);
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>College DB</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        .wrapper{
            width: 500px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header">
                        <h3>Update Record for SSN =  <?php echo $_GET["Ssn"]; ?> </H3>
                    </div>
                    <p>Please edit the input values and submit.
                    <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">

                        <div class="form-group <?php echo (!empty($Dependent_name_err)) ? 'has-error' : ''; ?>">
                            <label>Dependent Name</label>
                            <input type="text" name="Dependent_name" class="form-control" value="<?php echo $param_Dependent_name; ?>">
                            <span class="help-block"><?php echo $Dependent_name_err;?></span>
                        </div>

                        <div class="form-group <?php echo (!empty($Relationship_err)) ? 'has-error' : ''; ?>">
                            <label>Relationship</label>
                            <input type="text" name="Relationship" class="form-control" value="<?php echo $Relationship; ?>">
                            <span class="help-block"><?php echo $Relationship_err;?></span>
                        </div>


                        <div class="form-group <?php echo (!empty($Sex_err)) ? 'has-error' : ''; ?>">
                            <label>Sex</label>
                            <input type="text" name="Sex" class="form-control" value="<?php echo $Sex; ?>">
                            <span class="help-block"><?php echo $Sex_err;?></span>
                        </div>
                                          
                        <div class="form-group <?php echo (!empty($Bdate_err)) ? 'has-error' : ''; ?>">
                            <label>Birth date</label>
                            <input type="date" name="Bdate" class="form-control" value="<?php echo $Bdate; ?>">
                            <span class="help-block"><?php echo $Bdate_err;?></span>
                        </div>	

                        <input type="hidden" name="Ssn" value="<?php echo $Essn; ?>"/>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="index.php" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>