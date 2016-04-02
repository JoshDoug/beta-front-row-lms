<?php
$errors = [];
if (isset($_POST['register'])) {
    require_once '_includes/pdoConnect.php';
    $expected = ['kNumber', 'fName', 'lName', 'kMail', 'pwd', 'confirm'];
    // Assign $_POST variables to simple variables and check all fields have values
    foreach ($_POST as $key => $value) {
        if (in_array($key, $expected)) {
            // $$ is a variable variable, confusing, but cool, sorry for the unprofessional commentary Ahmed!
            $$key = trim($value);
            if (empty($$key)) {
                $errors[$key] = 'This field requires a value.';
            }
        }
    }
    // Proceed only if there are no errors
    if (!$errors) {
        if ($pwd != $confirm) {
            $errors['nomatch'] = 'Passwords do not match.';
        } else {
            // Check that the kNumber hasn't already been registered
            $sql = 'SELECT COUNT(*) FROM user WHERE kNumber = :kNumber';
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':kNumber', $kNumber);
            $stmt->execute();
            if ($stmt->fetchColumn() != 0) {
                $errors['failed'] = "kNumber is already registered. If this is your kNumber, then please log in, otherwise check you've entered your kNumber correctly.";
            } else {
                try {
                    $sql = 'INSERT INTO user (kNumber, fName, lName, kMail, pwd)
                            VALUES (:kNumber, :fName, :lName, :kMail, :pwd)';
                    $stmt = $db->prepare($sql);
                    $stmt->bindParam(':kNumber', $kNumber);
                    $stmt->bindParam(':fName', $fName);
                    $stmt->bindParam(':lName', $lName);
                    $stmt->bindParam(':kMail', $kMail);
                    // Store an encrypted version of the password
                    $stmt->bindValue(':pwd', password_hash($pwd, PASSWORD_DEFAULT));
                    $stmt->execute();
                } catch (\PDOException $e) {
                    $error = $e->getMessage();
                }
                // The rowCount() method returns 1 if the record is inserted,
                // so redirect the user to the login page
                if ($stmt->rowCount()) {
                    header('Location: login.php');
                    exit;
                }
            }
        }
    }
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Create Account</title>
<!--    <link href="css/styles.css" rel="stylesheet" type="text/css">-->
</head>

<body id="create">
<h1>Create Account</h1>
<!-- shorthand for echo!   -->
<form action="<?= $_SERVER['PHP_SELF']; ?>" method="post">
    <p>
        <label for="kNumber">kNumber:</label>
        <input type="text" name="kNumber" id="kNumber"
        <?php
        if (isset($kNumber) && !isset($errors['kNumber'])) {
            echo 'value="' . htmlentities($kNumber) . '">';
        } else {
            echo '>';
        }
        if (isset($errors['kNumber'])) {
            echo $errors['kNumber'];
        } elseif (isset($errors['failed'])) {
            echo $errors['failed'];
        }
        ?>
<!--  if the text editor shows an error, it's because the closing angle bracket for the input is echoed back by the php             -->
    </p>
<!-- should be required really, will be implemented for beta       -->
    <p>
        <label for="fName">First Name:</label>
        <input type="text" name="fName" id="fName">
         <?php
        if (isset($errors['fName'])) {
            echo $errors['fName'];
        }
        ?>
    </p>
    <p>
        <label for="lName">Last Name:</label>
        <input type="text" name="lName" id="lName">
        <?php
        if (isset($errors['lName'])) {
            echo $errors['lName'];
        }
        ?>
    </p>
    <p>
        <label for="kMail">KU Email:</label>
        <input type="text" name="kMail" id="kMail">
        <?php
        if (isset($errors['kMail'])) {
            echo $errors['kMail'];
        }
        ?>
    </p>
    <p>
        <label for="pwd">Password:</label>
        <input type="password" name="pwd" id="pwd">
        <?php
        if (isset($errors['pwd'])) {
            echo $errors['pwd'];
        }
        ?>
    </p>
    <p>
        <label for="confirm">Confirm Password:</label>
        <input type="password" name="confirm" id="confirm">
        <?php
        if (isset($errors['confirm'])) {
            echo $errors['confirm'];
        } elseif (isset($errors['nomatch'])) {
            echo $errors['nomatch'];
        }
        ?>
    </p>
    <p>
        <input type="submit" name="register" id="register" value="Create Account">
    </p>
</form>
</body>
</html>