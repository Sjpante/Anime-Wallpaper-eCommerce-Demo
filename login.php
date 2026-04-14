<?php
session_start(); // Έναρξη συνεδρίας για την αποθήκευση των στοιχείων του χρήστη μετά τη σύνδεση
include 'db.php'; // Σύνδεση με τη βάση δεδομένων

// Συνάρτηση για τον καθαρισμό των δεδομένων εισόδου από ανεπιθύμητα κενά ή σύμβολα
function validate($data) {
    $data = trim($data); // Αφαίρεση κενών από την αρχή και το τέλος
    $data = stripslashes($data); // Αφαίρεση backslashes (\)
    $data = htmlspecialchars($data); // Μετατροπή ειδικών χαρακτήρων σε HTML οντότητες για προστασία
    return $data;
}

// Έλεγχος αν η φόρμα υποβλήθηκε με τη μέθοδο POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = validate($_POST['email']);
    $password = validate($_POST['password']);

    // Αναζήτηση του χρήστη στη βάση δεδομένων με βάση το email του
    $sql = "SELECT * FROM customers WHERE email='$email'";
    $result = mysqli_query($conn, $sql);

    // Έλεγχος αν βρέθηκε ακριβώς ένας χρήστης με αυτό το email
    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        
        // Σύγκριση του κωδικού που έδωσε ο χρήστης με τον κρυπτογραφημένο κωδικό της βάσης
        if (password_verify($password, $row['password'])) {
            // Αν ο κωδικός είναι σωστός, αποθηκεύουμε τα στοιχεία του στη συνεδρία (Session)
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['fullname'];
            // Ανακατεύθυνση στην αρχική σελίδα
            header("Location: index.php");
            exit();
        } else {
            // Μήνυμα σφάλματος αν ο κωδικός είναι λάθος
            $error = "Wrong Password!";
        }
    } else {
        // Μήνυμα σφάλματος αν το email δεν υπάρχει στη βάση
        $error = "Email not found!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Log In </title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kavoon&family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>
    <header class="header">
    <a href="index.php" class="logo-link">
            <div class="logo"></div></a>
        <h1>Log in</h1>
        <div class="nav"><a href="index.php">← Back to menu</a></div>
    </header>
<section class ="form-section">
    <div class="form-container">
        <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        
        <form method="POST" action="login.php">
            <label>Email:</label>
            <input type="email" name="email" required>
            
            <label>Password:</label>
            <input type="password" name="password" required>
            
            <button type="submit" class="btn">Log In </button>
        </form>
        <p style="text-align:center; font-size: 14px;">New Customer? <a href="signup.php">Sign Up</a></p>
    </div>
</section>
</body>
</html>