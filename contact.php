<?php

// Include Composer's autoloader
require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
// Access environment variables
$reCaptchaSiteKey = $_ENV['RECAPTCHA_SITE_KEY'] ?? '';
$reCaptchaSecretKey = $_ENV['RECAPTCHA_SECRET_KEY'] ?? '';
// Convert the direct link to the embed link
function convertToEmbedLink($directLink)
{
  // Extract the chat ID from the direct link
  $matches = [];
  preg_match('/\/chat\/([a-zA-Z0-9\/]+)\/?/', $directLink, $matches);

  if (isset($matches[1])) {
    // If the chat ID is found, construct the embed link
    $embedLink = "https://embed.tawk.to/{$matches[1]}";
    return $embedLink;
  } else {
    // If no chat ID is found, return the original link
    return $directLink;
  }
}
$tawkToDirectLink = $_ENV['TAWKTO_WIDGET_LINK'];
$contact = $_ENV['contact'];


// Convert Tawk.to direct link to embed link
$tawkToEmbedLink = convertToEmbedLink($tawkToDirectLink);

// Database connection
$host = $_ENV['DB_HOST'] ?? 'localhost';
$port = $_ENV['DB_PORT'] ?? 3306;
$dbname = $_ENV['DB_NAME'] ?? '';
$user = $_ENV['DB_USER'] ?? '';
$pass = $_ENV['DB_PASS'] ?? '';

try {
    // Create a database connection using PDO
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if maintenance mode is active
    $stmt = $pdo->query("SELECT * FROM maintenance_mode WHERE active = 1");
    $maintenanceMode = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($maintenanceMode) {
        // Maintenance mode is active, display maintenance message
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Maintenance Mode</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background-color: #f4f4f4;
                    text-align: center;
                    padding: 50px;
                }

                .maintenance-message {
                    background-color: #fff;
                    border-radius: 10px;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                    padding: 20px;
                    max-width: 600px;
                    margin: 0 auto;
                }

                .maintenance-title {
                    font-size: 24px;
                    font-weight: bold;
                    margin-bottom: 20px;
                }

                .maintenance-content {
                    font-size: 18px;
                    line-height: 1.6;
                }
            </style>
        </head>
        <body>
            <div class="maintenance-message">
                <h2 class="maintenance-title"><?php echo $maintenanceMode['title']; ?></h2>
                <p class="maintenance-content"><?php echo $maintenanceMode['message']; ?></p>
            </div>
        </body>
        </html>
        <?php
        exit; // Stop executing further code
    }
} catch(PDOException $e) {
    // Log and display the error message
    error_log("Database connection error: " . $e->getMessage());
    die("ERROR: Could not connect. " . $e->getMessage());
}
// Include the header
include('header.php');

try {
    // Create a database connection using PDO
    $host = $_ENV['DB_HOST'] ?? 'localhost';
    $port = $_ENV['DB_PORT'] ?? 3306;
    $dbname = $_ENV['DB_NAME'] ?? '';
    $user = $_ENV['DB_USER'] ?? '';
    $pass = $_ENV['DB_PASS'] ?? '';

    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Retrieve existing articles from the database
    $stmt = $pdo->query("SELECT * FROM blog_articles");
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    // Log and display the error message
    error_log("Database connection error: " . $e->getMessage());
    die("ERROR: Could not connect. " . $e->getMessage());
}
// Fetch contact info data from the database
try {
    $stmt = $pdo->query("SELECT * FROM contact_info WHERE id = 1"); // Assuming only one row in the table
    $contactInfo = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Log and display the error message
    error_log("Error fetching contact info: " . $e->getMessage());
}


// Check if contact info exists
$title = $contactInfo['title'] ?? '';
$contactEmail = $contactInfo['email'] ?? '';


// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate reCAPTCHA only if the key is not empty
    if (!empty($reCaptchaSiteKey)) {
        $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';
        if (empty($recaptchaResponse)) {
            // reCAPTCHA challenge is required
            $error = "reCAPTCHA challenge is required. Please complete the reCAPTCHA challenge.";
        } else {
            // Verify reCAPTCHA response
            $recaptchaSecret = $_ENV['RECAPTCHA_SECRET_KEY'];
            $recaptcha = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$recaptchaSecret&response=$recaptchaResponse");
            $recaptchaResult = json_decode($recaptcha);

            if (!$recaptchaResult->success) {
                // reCAPTCHA verification failed
                $error = "reCAPTCHA verification failed. Please try again.";
            } else {
                // Proceed with form submission
                $name = $_POST['name'];
                $email = $_POST['email'];
                $message = $_POST['message'];

                // Insert into the database
                $stmt = $pdo->prepare("INSERT INTO support_requests (name, email, message) VALUES (:name, :email, :message)");
                $stmt->execute(['name' => $name, 'email' => $email, 'message' => $message]);

                // Display success message
                $successMessage = "Your message has been sent successfully! We will get back to you soon.";

            }
            
        }
    } else {
        // Proceed with form submission without reCAPTCHA
        $name = $_POST['name'];
        $email = $_POST['email'];
        $message = $_POST['message'];

        // Insert into the database
        $stmt = $pdo->prepare("INSERT INTO support_requests (name, email, message) VALUES (:name, :email, :message)");
        $stmt->execute(['name' => $name, 'email' => $email, 'message' => $message]);

        // Display success message
        $successMessage = "Your message has been sent successfully! We will get back to you soon.";
        

    }
    
  
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <!-- Include your CSS and JS files -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <!-- Start of Tawk.to Script -->
  <script type="text/javascript">
    var Tawk_API = Tawk_API || {},
      Tawk_LoadStart = new Date();
    (function () {
      var s1 = document.createElement("script"),
        s0 = document.getElementsByTagName("script")[0];
      s1.async = true;
      s1.src = '<?php echo $tawkToEmbedLink; ?>';
      s1.charset = 'UTF-8';
      s1.setAttribute('crossorigin', '*');
      s0.parentNode.insertBefore(s1, s0);
    })();
  </script>
    <script>
        function validateRecaptcha(event) {
            // Check if reCAPTCHA is enabled (i.e., site key is not empty)
            var siteKey = "<?php echo $reCaptchaSiteKey; ?>";
            if (siteKey !== '') {
                // Prevent form submission
                event.preventDefault();
                
                // Check if reCAPTCHA response is available
                var recaptchaResponse = grecaptcha.getResponse();
                if (recaptchaResponse.length == 0) {
                    // If reCAPTCHA challenge is not completed, display error message
                    document.getElementById('recaptcha-error').innerText = 'Please complete the reCAPTCHA challenge before submitting.';
                } else {
                    // If reCAPTCHA challenge is completed, reset error message and submit the form
                    document.getElementById('recaptcha-error').innerText = ''; // Reset error message
                    document.getElementById('contact-form').submit(); // Submit the form
                }
            }
        }
    </script>

    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="static/assets/css/styles.min.css">
</head>
<body>
    
<h1 class="h1 h2 h3    dark-c8  ">Contact Us</h1>
    
     <div class="col-md-6">
            <!-- Contact Information -->
            <div class="contact-info">
<div class="fs28 fs56-M fw7 c3 dark-c8 lh13 mb12"><?php echo htmlspecialchars($title); ?></div>
<div class="c3 dark-c8 fs20 fs32-M notranslate">
    <a href="mailto:<?php echo htmlspecialchars($contactEmail); ?>"><?php echo htmlspecialchars($contactEmail); ?></a>
</div>

            </div>
        </div>



    <div id="recaptcha-error" class="text-danger"></div> <!-- Error message for reCAPTCHA -->
    
    <?php if($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if(isset($successMessage)): ?>
        <div><?php echo $successMessage; ?></div>
    <?php endif; ?>


    <div  <?php echo ($contact === 'true') ? '' : 'hide'; ?>">
    <?php if ($contact === 'true'): ?>
        <form id="contact-form" method="post" onsubmit="validateRecaptcha(event)">
            <div>
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div>
                <label for="message">Message:</label>
                <textarea id="message" name="message" required></textarea>
            </div>
            <!-- reCAPTCHA widget -->
            <?php if (!empty($reCaptchaSiteKey)): ?>
                <div class="g-recaptcha" data-sitekey="<?php echo $reCaptchaSiteKey; ?>" required></div>
            <?php endif; ?>
            <button type="submit" >Send</button>
        </form>
        
    <?php endif; ?>
</div>
       


    <div class="col-md-6">
    <!-- Display email and message from the database -->
    <?php if(isset($supportEmail) && !empty($supportEmail)): ?>
        <div>
            <p>Email: <?php echo $supportEmail; ?></p>
            <?php
            try {
                // Retrieve the latest message from the database
                $stmt = $pdo->query("SELECT message FROM support_requests ORDER BY created_at DESC LIMIT 1");
                $latestMessage = $stmt->fetchColumn();
            } catch(PDOException $e) {
                // Log and display the error message
                error_log("Error fetching latest message: " . $e->getMessage());
                $latestMessage = ""; // Set default message if there's an error
            }
            ?>
            <?php if(!empty($latestMessage)): ?>
                <p>Latest Message:</p>
                <p><?php echo $latestMessage; ?></p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
</div>

</body>
</html>

<style>
        /* Light Mode Styles */
        body {
            background: <?php echo getCssValue('body', 'background', $pdo); ?>;
        }

        .c3 {
            color: <?php echo getCssValue('.c3', 'color', $pdo); ?>;
        }

        /* Dark Mode Styles */
        .dark body {
            background: <?php echo getCssValue('.dark body', 'background', $pdo); ?>;
        }

        .dark .dark-c8 {
            color: <?php echo getCssValue('.dark .dark-c8', 'color', $pdo); ?>;
        }
        
     .h1{
            color: <?php echo getCssValue('.c3', 'color', $pdo); ?>;
        }
  .h2{
            color: <?php echo getCssValue('.c3', 'color', $pdo); ?>;
        }
        .h3{
            color: <?php echo getCssValue('.c3', 'color', $pdo); ?>;
        }
        button[type="submit"] {
    
            background-color: <?php echo getCssValue('.c3', 'color', $pdo); ?>;
        }
        

        

    </style>

<style>


h1 {
    text-align: center;
    margin-bottom: 30px;
}
.contact-info {
    margin-top: -50; /* Adjust as needed */


}

@media only screen and (max-width: 600px) {
    .contact-info {
        margin-top: 10px; /* Adjust as needed */
        margin-bottom: 10px; /* Adjust as needed */
    }
}

form {
    background-color: #fff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    width: 350px; /* Adjust the width as needed */
    margin:  auto; /* Center the form horizontally */
            margin-bottom: 10px; /* Adjust as needed */

}

form div {
    margin-bottom: 20px; /* Maintain the margin between form elements */
}



input[type="text"],
input[type="email"],
textarea {
    width: calc(100% - 20px); /* Adjust the width of input fields */
    padding: 12px; /* Increase padding for input fields */
    border: 1px solid #ccc;
    border-radius: 8px; /* Increase border radius */
    box-sizing: border-box;
}

textarea {
    height: 120px; /* Increase the height of the textarea */
}

button[type="submit"] {
    color: #fff;
    border: none;
    border-radius: 8px; /* Increase border radius */
    padding: 12px 24px; /* Increase padding for the button */
    cursor: pointer;
    font-size: 18px; /* Increase font size */
    display: block;
    margin: 0 auto; /* Center the button horizontally */
}



    </style>
    <style>
/* Hide the side navigation menu by default */
.side-navigation-menu {
    display: none;
}

/* Show the side navigation menu when the 'show' class is applied */
.side-navigation-menu.show {
    display: block;
}
/* Side navigation menu */
.side-navigation-menu {
    display: none; /* Hide the side navigation menu by default */
    position: fixed; /* Fixed position to keep it visible even when scrolling */
    top: 0; /* Position it at the top of the viewport */
    left: 0; /* Position it at the left edge of the viewport */
    width: 250px; /* Set the width of the menu */
    height: 100%; /* Set the height to cover the entire viewport */
    background-color: #696969 ; /* Background color */
    z-index: 1000; /* Ensure it's above other content */
    padding-top: 60px; /* Add padding to accommodate any fixed header */
}

/* Side navigation menu links */
.side-navigation-menu a {
    display: block; /* Display as block to occupy entire width */
    padding: 10px 20px; /* Add padding for spacing */
    color: #fff; /* Text color */
    text-decoration: none; /* Remove underline */
}

/* Style for hover effect on links */
.side-navigation-menu a:hover {
    background-color: #555; /* Darker background color on hover */
}

/* Style the list items in the side navigation menu */
.side-navigation-menu ul {
    list-style-type: none;
    padding: 0;
    margin: 0;
}

/* Close button for mobile navigation */
.close-btn {
    position: absolute; /* Position it absolutely within the menu */
    top: 20px; /* Position it 20px from the top */
    right: 20px; /* Position it 20px from the right */
    color: #fff; /* Text color */
    cursor: pointer; /* Show pointer cursor on hover */
    font-size: 24px; /* Adjust font size */
}


</style>

<?php
// Include the footer
include('footer.php');
?>