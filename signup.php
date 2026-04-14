<?php
session_start(); // Έναρξη συνεδρίας για αυτόματη σύνδεση του χρήστη μετά την εγγραφή
include 'db.php'; // Σύνδεση με τη βάση δεδομένων

// Συνάρτηση για τον καθαρισμό και την ασφάλεια των δεδομένων εισόδου
function validate($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Έλεγχος αν η φόρμα στάλθηκε με τη μέθοδο POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = validate($_POST['fullname']);
    $email = validate($_POST['email']);
    $password = validate($_POST['password']); 

    // Κρυπτογράφηση του κωδικού πρόσβασης πριν την αποθήκευση στη βάση (για λόγους ασφαλείας)
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // SQL εντολή για την εισαγωγή του νέου πελάτη στον πίνακα customers
    $sql = "INSERT INTO customers (fullname, email, password) VALUES ('$fullname', '$email', '$hashed_password')";
    
// Χρήση try-catch για τη διαχείριση σφαλμάτων (π.χ. αν το email υπάρχει ήδη)
try {
    if (mysqli_query($conn, $sql)) {
        // Αν η εγγραφή πετύχει, αποθηκεύουμε το ID και το όνομα στη συνεδρία
        $_SESSION['user_id'] = mysqli_insert_id($conn);
        $_SESSION['user_name'] = $fullname;
        // Ανακατεύθυνση στην αρχική σελίδα
        header("Location: index.php");
        exit();
    }
} catch (mysqli_sql_exception $e) {
    // Έλεγχος αν το σφάλμα αφορά διπλότυπη εγγραφή (κωδικός σφάλματος 1062 για την MySQL)
    if ($e->getCode() == 1062) {
        $error = "Email already exists.";
    } else {
        $error = "An unexpected error occurred.";
    }
}
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sign In</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kavoon&family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>
    <header class="header">
    <a href="index.php" class="logo-link">
        <div class="logo"></div></a>
        <h1>Create Account</h1> 
        <div class="nav"><a href="index.php">← Back to menu</a></div>
    </header>
<section class ="form-section">
    <div class="form-container">
        <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        
        <form method="POST" action="signup.php">
            <label>Full Name:</label>
            <input type="text" name="fullname" required>
            
            <label>Email:</label>
            <input type="email" name="email" required>
            
            <label>Password:</label>
            <input type="password" name="password" required>
            
            <button type="submit" class="btn">Sign Up</button>
        </form>
        <p style="text-align:center; font-size: 14px;">Already have an account? <a href="login.php">Log in</a></p>
    </div>
</section>
</body>
</html>