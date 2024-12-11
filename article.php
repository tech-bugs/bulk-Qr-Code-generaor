<?php

// Include Composer's autoloader
require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
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

// Get article ID from the URL parameter
$articleId = $_GET['id'] ?? null;

// Check if article ID is provided
if ($articleId !== null) {
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
  $stmt = $pdo->query("SELECT * FROM seo_metadata WHERE id = 2");
    $seoData = $stmt->fetch(PDO::FETCH_ASSOC);
        // Retrieve article data from the database
        $stmt = $pdo->prepare("SELECT * FROM blog_articles WHERE id = ?");
        $stmt->execute([$articleId]);
        $article = $stmt->fetch(PDO::FETCH_ASSOC);

        // Display article content if found
        if ($article) {
            // Output HTML head section with title, meta description, keywords, and favicon
            echo "<!DOCTYPE html>
                    <html lang='en'>
                    <head>
                    
                        <meta charset='UTF-8'>
                        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                        <title>{$article['title']}</title>
                        <meta name='description' content='{$article['meta_description']}'>
                        <meta name='keywords' content='{$article['keywords']}'>
                        <link rel='icon' href='/admin/uploads/favicon.png' type='image/x-icon'>
                        
                        <!-- Open Graph / Facebook -->
                        <meta property='og:type' content='article'>
                        <meta property='og:url' content='{$_SERVER['REQUEST_SCHEME']}://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}'>
                        <meta property='og:title' content='{$article['title']}'>
                        <meta property='og:description' content='{$article['meta_description']}'>
                        <meta property='og:image' content='{$_SERVER['REQUEST_SCHEME']}://{$_SERVER['HTTP_HOST']}/admin/{$article['featured_image']}'>
                        
                        <!-- Twitter -->
                        <meta property='twitter:card' content='summary_large_image'>
                        <meta property='twitter:url' content='{$_SERVER['REQUEST_SCHEME']}://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}'>
                        <meta property='twitter:title' content='{$article['title']}'>
                        <meta property='twitter:description' content='{$article['meta_description']}'>
                        <meta property='twitter:image' content='{$_SERVER['REQUEST_SCHEME']}://{$_SERVER['HTTP_HOST']}/admin/{$article['featured_image']}'>
                        
                        <style>
                            #content {
                                text-align: left;
                                margin: 0 auto;
                                width: auto;
                                max-width: 800px;
                                padding: 20px;
                                border-radius: 10px;
                            }
                           .article-image {
    display: block;
    margin-left: auto;
    margin-right: auto;
    width: auto;
    max-height: 340px;
    object-fit: cover;
    border-radius: 10px 10px 0 0;
}

                         
                             .more-blogs {
                                list-style-type: none;
                                padding: 0;
                            }
                            .more-blogs li {
                                margin-bottom: 10px;
                            }
                          
                             .article {
                             
                                width: 65%;
                                padding-right: 20px;
                                box-sizing: border-box;
                            }
                            .social-sharing {
    text-align: center; /* Center the icons within the parent container */
}

.social-sharing a {
    font-size: 20px;
    margin: 0 10px;
    display: inline-block; /* Ensure the anchor tags are inline elements */
}

                         .h1 {
    text-align: center;
}
.article-meta p {
    text-align: center;
}

                            
                        </style>
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
                    </head>
                    <body>";
            // Output article content
        echo "<div id='content'>
        <h1 class='h1 h2 h3 dark-c8'>{$article['title']}</h1>
        <div class='article-content'>
            <img src='/admin/{$article['featured_image']}' alt='{$article['title']}' class='article-image'>
            <div class='article-meta'>
                <div class='c2 dark-c4 fs20 fs32-M'>
                    <p class='mb48' style='font-size: 14px;'>Published on " . date('Y-m-d', strtotime($article['created_at'])) . "</p>
                </div>
                <div class='social-sharing'>
                    <!-- Social Media Icons -->
                    <a href='javascript:void(0)' class='share-linkedin'><i class='bi bi-linkedin'></i></a>
                    <a href='javascript:void(0)' class='share-pinterest'><i class='bi bi-pinterest'></i></a>
                    <a href='javascript:void(0)' class='share-facebook'><i class='bi bi-facebook'></i></a>
                    <a href='javascript:void(0)' class='share-reddit'><i class='bi bi-reddit'></i></a>
                    <a href='javascript:void(0)' class='share-twitter'><i class='bi bi-twitter-x'></i></a>
                    <a href='javascript:void(0)' class='share-telegram'><i class='bi bi-telegram'></i></a>
                    <a href='javascript:void(0)' class='share-whatsapp'><i class='bi bi-whatsapp'></i></a>
                </div>
            </div>
            <div class='c2 dark-c4 fs16 fs20-S'>
                <p class='  fs16 fs18-S  c1 dark-c12 lh12'>{$article['content']}</p>
            </div>
        </div>
        <ul class='more-blogs'>";



                
             // Close the HTML tags
            echo "</ul>
                  </div>
                  </div>
                  </body>
                  </html>";
        } else {
            echo "<p>Article not found</p>";
        }
    } catch(PDOException $e) {
        // Log and display the error message
        error_log("Database connection error: " . $e->getMessage());
        die("ERROR: Could not connect. " . $e->getMessage());
    }
} else {
    echo "<p>Article ID not provided</p>";
}
// Include Bootstrap Icons CDN for icons
echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css' rel='stylesheet'>";

// JavaScript for social media sharing
echo "<script>
        // Function to share on LinkedIn
        document.querySelector('.share-linkedin i').parentElement.addEventListener('click', function() {
            window.open('https://www.linkedin.com/sharing/share-offsite/?url=' + encodeURIComponent(window.location.href), '_blank');
        });

     

        // Function to share on Pinterest
        document.querySelector('.share-pinterest i').parentElement.addEventListener('click', function() {
            window.open('https://pinterest.com/pin/create/button/?url=' + encodeURIComponent(window.location.href), '_blank');
        });

        

        // Function to share on Facebook
        document.querySelector('.share-facebook i').parentElement.addEventListener('click', function() {
            window.open('https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(window.location.href), '_blank');
        });

       

        // Function to share on Reddit
        document.querySelector('.share-reddit i').parentElement.addEventListener('click', function() {
            window.open('https://reddit.com/submit?url=' + encodeURIComponent(window.location.href), '_blank');
        });

       // Function to share on Twitter
        document.querySelector('.share-twitter i').parentElement.addEventListener('click', function() {
            window.open('https://twitter.com/intent/tweet?url=' + encodeURIComponent(window.location.href), '_blank');
        });

        // Function to share on Telegram
        document.querySelector('.share-telegram i').parentElement.addEventListener('click', function() {
            window.open('https://telegram.me/share/url?url=' + encodeURIComponent(window.location.href), '_blank');
        });

       

    



       

        // Function to share on WhatsApp
        document.querySelector('.share-whatsapp i').parentElement.addEventListener('click', function() {
            window.open('https://wa.me/?text=' + encodeURIComponent(window.location.href), '_blank');
        });
      </script>";



// Include the footer
include('footer.php');
?>

