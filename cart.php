<?php
session_start(); // Έναρξη συνεδρίας για πρόσβαση στα δεδομένα του χρήστη και του καλαθιού
include 'db.php'; // Σύνδεση με τη βάση δεδομένων

// Προστασία σελίδας: Αν ο χρήστης δεν είναι συνδεδεμένος, ανακατεύθυνση στη σελίδα login
if (!isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit();
}

// Λογική αφαίρεσης προϊόντος από το καλάθι
if (isset($_GET['remove'])) {
    $remove_id = $_GET['remove'];
    
    // Αναζήτηση του ID του προϊόντος μέσα στον πίνακα του καλαθιού στη συνεδρία
    $key = array_search($remove_id, $_SESSION['cart']);
    
    // Αν βρεθεί το προϊόν, το αφαιρούμε
    if ($key !== false) {
        unset($_SESSION['cart'][$key]);
        // Επαναφέρει τους δείκτες του πίνακα (π.χ. από 0, 2 σε 0, 1) για να μην υπάρχουν κενά
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }
    
    // Ανακατεύθυνση στην ίδια σελίδα για να ανανεωθεί η εμφάνιση του καλαθιού
    header("Location: cart.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cart</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kavoon&family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>
    <header class="header">
    <a href="index.php" class="logo-link">
            <div class="logo"></div></a>
        <h1>Cart</h1>
        <div class="nav"><a href="index.php">← Back to menu</a></div>
    </div>
</header>

    <section class ="form-section">
    <div class="form-container cart-theme" style="width: 500px;">
        <?php
        $total_price = 0; // Αρχικοποίηση της συνολικής τιμής

        // Έλεγχος αν το καλάθι υπάρχει και αν περιέχει προϊόντα
        if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
            echo "<ul style='list-style-type: none; padding: 0;'>";
                
                // Επανάληψη μέσα από τα IDs που είναι αποθηκευμένα στη συνεδρία
                foreach ($_SESSION['cart'] as $wallpaper_id) {
                    // Ανάκτηση των στοιχείων του κάθε προϊόντος από τη βάση δεδομένων βάσει του ID
                    $sql = "SELECT * FROM wallpapers WHERE id = $wallpaper_id";
                    $result = mysqli_query($conn, $sql);
                    
                    if ($row = mysqli_fetch_assoc($result)) {
                        echo "<li style='border-bottom: 1px solid #ccc; padding: 10px 0; display:flex; align-items:center;'>";
                        
                        // Εμφάνιση μικρογραφίας προϊόντος και στοιχείων (Όνομα - Τιμή)
                        echo "<img src='" . $row['image'] . "' style='width:60px; height:60px; border-radius:5px; margin-right:15px; object-fit:cover;'>";
                        echo "<span><b>{$row['name']}</b> - <span style='color: #9d0f1d;'>{$row['price']} €</span></span>";
                        
                        // Κουμπί αφαίρεσης που στέλνει το ID μέσω της μεθόδου GET (URL)
                        echo "<a href='cart.php?remove=$wallpaper_id' class='btn' style='margin-left:auto; background-color:#9d0f1d; padding:5px 10px; 
                        font-size:12px; text-decoration:none; width:auto;'>Remove</a>";
                        
                        echo "</li>";
                        
                        // Πρόσθεση της τιμής του προϊόντος στο γενικό σύνολο
                        $total_price += $row['price'];
                    }
                }
            echo "</ul>";
            
            // Εμφάνιση του τελικού ποσού με μορφοποίηση δύο δεκαδικών ψηφίων
            echo "<h3 style='text-align: right;'>Total: " . number_format($total_price, 2) . " €</h3>";
            echo "<button class='btn'>Check Out</button>";
            
        } else {
            // Μήνυμα αν το καλάθι είναι άδειο
            echo "<p style='text-align:center;'>Cart is empty.</p>";
        }
        ?>
    </div>
</section>
</body>
</html>