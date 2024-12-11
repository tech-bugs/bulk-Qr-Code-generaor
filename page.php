<?php

// Include Composer's autoloader
require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
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

// Get page ID from the URL parameter
$pageId = $_GET['id'] ?? null;

// Check if page ID is provided
if ($pageId !== null) {
    // Create a database connection using PDO
    $host = $_ENV['DB_HOST'] ?? 'localhost';
    $port = $_ENV['DB_PORT'] ?? 3306;
    $dbname = $_ENV['DB_NAME'] ?? '';
    $user = $_ENV['DB_USER'] ?? '';
    $pass = $_ENV['DB_PASS'] ?? '';

    try {
        // Create a database connection using PDO
        $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Retrieve page content from the database
        $stmt = $pdo->prepare("SELECT title, content, meta_description, keywords FROM pages WHERE id = ?");
        $stmt->execute([$pageId]);
        $page = $stmt->fetch(PDO::FETCH_ASSOC);

        // Display page content if found
        if ($page) {
            // Output HTML head section with title, meta description, keywords, and favicon
            echo "<!DOCTYPE html>
                    <html lang='en'>
                    <head>
                    
                        <meta charset='UTF-8'>
                        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                        <title>{$page['title']}</title>
                        <meta name='description' content='{$page['meta_description']}'>
                        <meta name='keywords' content='{$page['keywords']}'>
                        <link rel='icon' href='/admin/uploads/favicon.png' type='image/x-icon'>
                          <link rel='stylesheet' href='styles.css'>
                <link rel='stylesheet' href='static/assets/css/styles.min.css'>
                        <style>
                            #content {
                                text-align: auto;
                                margin:  auto;
                                width: auto;
                                max-width: auto;
                                padding: 20px;
                                border-radius: 10px;
                            }
                        </style>
                         <style>
                         .h1 {
    text-align: center;
}

                    .content-paragraph {
                        font-size: 16px; /* Adjust the font size as needed */
                    }
                </style>
                 <script type='text/javascript'>
                var Tawk_API = Tawk_API || {},
                    Tawk_LoadStart = new Date();
                (function () {
                    var s1 = document.createElement('script'),
                        s0 = document.getElementsByTagName('script')[0];
                    s1.async = true;
                    s1.src = '<?php echo $tawkToEmbedLink; ?>';
                    s1.charset = 'UTF-8';
                    s1.setAttribute('crossorigin', '*');
                    s0.parentNode.insertBefore(s1, s0);
                })();
            </script>
                    </head>
                     <style>
        /* Light Mode Styles */
        body {
            background: " . getCssValue('body', 'background', $pdo) . ";
        }
        .c3 {
            color: " . getCssValue('.c3', 'color', $pdo) . ";
        }
        /* Dark Mode Styles */
        .dark body {
            background: " . getCssValue('.dark body', 'background', $pdo) . ";
        }
        .dark .dark-c8 {
            color: " . getCssValue('.dark .dark-c8', 'color', $pdo) . ";
        }
        h1 {
            color: " . getCssValue('.c3', 'color', $pdo) . ";
        }
        h2 {
            color: " . getCssValue('.c3', 'color', $pdo) . ";
        }
        h3 {
            color: " . getCssValue('.c3', 'color', $pdo) . ";
        }
        button[type='submit'] {
            background-color: " . getCssValue('.c3', 'color', $pdo) . ";
        }
    </style>
                    <body>";
            // Output page content
            echo "<div id='content'>
        <h1 class='h1 h2 h3 dark-c8'>{$page['title']}</h1>
         <div class='c2 dark-c4 fs16 fs20-S'>
                                <p class='mb48'>{$page['content']}</p>
                            </div>
                </div>";
            // Close HTML body and document
            echo "</body></html>";
        } else {
            echo "<p>Page not found</p>";
        }
    } catch(PDOException $e) {
        // Log and display the error message
        error_log("Database connection error: " . $e->getMessage());
        die("ERROR: Could not connect. " . $e->getMessage());
    }
} else {
    echo "<p>Page ID not provided</p>";
}


// Include the footer
include('footer.php');
?>

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

<head>
    
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
