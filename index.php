<?php
session_start(); // Έναρξη συνεδρίας για τη χρήση των $_SESSION μεταβλητών
include 'db.php'; // Σύνδεση με τη βάση δεδομένων

// Έλεγχος αν ο χρήστης πάτησε το κουμπί "Add to Cart"
if (isset($_POST['add_to_cart'])) {
    $wallpaper_id = $_POST['wallpaper_id'];
    
    // Αν δεν υπάρχει ήδη καλάθι στη συνεδρία, το δημιουργούμε ως πίνακα (array)
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }
    
    // Προσθήκη του ID της εικόνας στο τέλος του πίνακα του καλαθιού
    array_push($_SESSION['cart'], $wallpaper_id);
    $message = "Wallpaper added to cart!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Anime Wallpapers</title> 
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kavoon&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kavoon&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Quicksand:wght@300..700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>
 
    <div class="header">
    <a href="index.php" class="logo-link">
        <div class="logo"></div></a>
     <h1>Anime Wallpapers</h1>
        <div class="nav">
    <?php 
    // Ελέγχουμε αν υπάρχει ο χρήστης (αν είναι συνδεδεμένος)
    if (isset($_SESSION['user_name'])) {
        
        // Βρίσκουμε πόσα είδη έχει το καλάθι (για να μην μπλέκουμε τον κώδικα κάτω)
        $cart_count = 0;
        if (isset($_SESSION['cart'])) {
            $cart_count = count($_SESSION['cart']);
        }
        
        // Τυπώνουμε το μενού του Συνδεδεμένου Χρήστη
        echo '<span style="color: black;">Welcome, ' . $_SESSION['user_name'] . '!</span> | ';
        echo '<a href="cart.php"> Cart (' . $cart_count . ')</a> | ';
        echo '<a href="logout.php">Log Out</a>';
        
    } else {
        
        // Τυπώνουμε το μενού του Επισκέπτη αν δεν είναι συνδεδεμένος
        echo '<a href="login.php">Log in</a> | <a href="signup.php">Sign Up</a>';
        
    } 
    ?>
</div>
    </div>

<?php if(isset($message)) echo "<h3 style='position:absolute; left:0; right:0; color:#1d9d0f; text-align:center; margin:0; z-index:10;'><b>$message</b></h3>"; ?>
    

<section class="category-section">
    <div class="wallpaper-grid">
        <?php
        // Επιλογή όλων των εγγραφών από τον πίνακα wallpapers της βάσης δεδομένων
        $sql = "SELECT * FROM wallpapers";
        $result = mysqli_query($conn, $sql);

        // Έλεγχος αν υπάρχουν αποτελέσματα στη βάση
        if (mysqli_num_rows($result) > 0) {
            // Επανάληψη για κάθε γραμμή που επιστρέφει η βάση δεδομένων
            while($row = mysqli_fetch_assoc($result)) {
                echo "<div class='wallpaper-card'>";
                
                // Εμφάνιση της εικόνας, του ονόματος και της τιμής από τη βάση
                echo "<img src='" . $row['image'] . "' alt='" . $row['name'] . "'>";
                echo "<h3>" . $row['name'] . "</h3>";
                echo "<h4>" . $row['price'] . " €</h4>";
                
                // Αν ο χρήστης είναι συνδεδεμένος, εμφανίζουμε το κουμπί αγοράς
                if (isset($_SESSION['user_name'])) {
                    echo "<form method='POST' action='index.php'>";
                    // Κρυφό πεδίο για την αποστολή του ID της συγκεκριμένης εικόνας
                    echo "<input type='hidden' name='wallpaper_id' value='" . $row['id'] . "'>";
                    echo "<button type='submit' name='add_to_cart' class='btn btn-small'>Add to Cart</button>";
                    echo "</form>";
                } else {
                    // Μήνυμα προτροπής για σύνδεση αν ο χρήστης είναι επισκέπτης
                    echo "<p style='color:gray; font-size:14px;'>Log in for Order</p>";
                }
                echo "</div>";
            }
        }
        ?>
    </div>
</section>    
</body>
</html>