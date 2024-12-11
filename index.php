<?php



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
} catch(PDOException $e) {
    // Log and display the error message
    error_log("Database connection error: " . $e->getMessage());
    die("ERROR: Could not connect. " . $e->getMessage());
}

// Fetch SEO data from the database
try {
    $stmt = $pdo->query("SELECT * FROM seo_metadata WHERE id = 2");
    $seoData = $stmt->fetch(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    // Log and display the error message
    error_log("Error fetching SEO data: " . $e->getMessage());
    // Handle the error as needed
}

try {
    // Fetch banners and AdSense content from the database
    $stmt = $pdo->query("SELECT * FROM banners_adsense");
    $contentData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Check if content data is fetched successfully
    if ($contentData) {
        foreach ($contentData as $row) {
            switch ($row['type']) {
                case 'banner_top':
                    $banner1_html = $row['content'];
                    break;
                case 'banner_bottom':
                    $banner2_html = $row['content'];
                    break;
                case 'adsense':
                    $adsense_html = $row['content'];
                    break;
                default:
                    // Handle unknown type
                    break;
            }
        }
    } else {
        // Handle case when no content data is found
        echo "No content data found.";
    }
} catch (PDOException $e) {
    // Log and display the error message
    error_log("Error fetching content data: " . $e->getMessage());
    // Handle error gracefully
}

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
  // Fetch How It Works data
$stmt = $pdo->query('SELECT icon_svg, icon_title, icon_description FROM how_it_works');
$how_it_works = $stmt->fetchAll();


// Fetch FAQ data
$stmt = $pdo->query('SELECT question, answer FROM faqs');
$faqs = $stmt->fetchAll();
// Close the database connection
$pdo = null;


$showWidget = $_ENV['GTRANSLATER'] === 'true';
$blog = $_ENV['BLOG'] === 'true';
$featureboxes = $_ENV['FEATURE_BOXES'] === 'true';
$tawkTowidgetlink = $_ENV['TAWKTO_WIDGET_LINK'];




$faq = $_ENV['FAQ'] === 'true';
// Get the Tawk.to widget link from the environment
$tawkToDirectLink = $_ENV['TAWKTO_WIDGET_LINK'];

// Get the Tawk.to widget link from the environment
$tawkToDirectLink = $_ENV['TAWKTO_WIDGET_LINK'];

$googleAnalyticsMeasurementID = $_ENV['GOOGLE_ANALYTICS']; // Get the Google Analytics Measurement ID from the environment


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
try {
    // Create a database connection using PDO
    $host = $_ENV['DB_HOST'] ?? 'localhost';
    $port = $_ENV['DB_PORT'] ?? 3306;
    $dbname = $_ENV['DB_NAME'] ?? '';
    $user = $_ENV['DB_USER'] ?? '';
    $pass = $_ENV['DB_PASS'] ?? '';

    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Retrieve latest 4 articles from the database
    $stmt = $pdo->query("SELECT * FROM blog_articles ORDER BY created_at DESC LIMIT 4");
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    // Log and display the error message
    error_log("Database connection error: " . $e->getMessage());
    die("ERROR: Could not connect. " . $e->getMessage());
}
// Convert Tawk.to direct link to embed link
$tawkToEmbedLink = convertToEmbedLink($tawkToDirectLink);



?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title><?= htmlspecialchars($seoData['title'] ?? '') ?></title>
  <!-- Favicon -->
  <link rel="icon" href="admin/uploads/favicon.png" type="image/x-icon">



   <!-- SEO Meta Tags -->
   <meta name="description" content="<?= htmlspecialchars($seoData['seo_description'] ?? '') ?>">
    <meta name="keywords" content="<?= htmlspecialchars($seoData['seo_keywords'] ?? '') ?>">

 <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
   
    <meta property="og:title" content="<?= htmlspecialchars($seoData['open_graph_title'] ?? '') ?>">
    <meta property="og:description" content="<?= htmlspecialchars($seoData['open_graph_description'] ?? '') ?>">
    <meta property="og:image" content="admin/uploads/opengraph.png">
  <link rel="stylesheet" href="styles.css">
  
  <link rel="stylesheet" href="static/assets/css/styles.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>



  <div class="adsense-container"><?php echo $adsense_html; ?></div>





<script>
document.addEventListener('click', function(event) {
  var target = event.target;

  for (var i = 0; i < 5; i++) {
    if (target.tagName === 'A' && (target.parentElement.classList.contains('banner-container') || target.parentElement.classList.contains('secondbanner-container'))) {
      event.preventDefault();
      var url = target.getAttribute('href');
      window.open(url, '_blank');
      break;
    }
    target = target.parentElement;
    if (!target) break;
  }
});
</script>







<script>
  document.addEventListener('DOMContentLoaded', function () {
    const darkModeSwitch = document.querySelector('.input43');
    const body = document.body;

    const darkModeEnabled = localStorage.getItem('darkModeEnabled') === 'true';

    if (darkModeEnabled) {
      body.classList.add('dark');
      darkModeSwitch.checked = true; 
    } else {
      body.classList.remove('dark');
      darkModeSwitch.checked = false;
    }

    darkModeSwitch.addEventListener('change', function () {
      console.log('Switch changed:', darkModeSwitch.checked);

      if (darkModeSwitch.checked) {
        body.classList.add('dark');
        localStorage.setItem('darkModeEnabled', 'true');
      } else {
        body.classList.remove('dark');
        localStorage.setItem('darkModeEnabled', 'false');
      }

      console.log('dark class added?', body.classList.contains('dark'));
    });
  });
</script>







</head>


<?php
require_once 'header.php';
?>

 <div class="main">
    <div class="pt60 pt96-M pb48 pb72-L">
        <div class="container tac">
            <div class="mb48">
                <div class="fs28 fs56-M fw7 c3 dark-c8 lh13 mb12"><?= htmlspecialchars($seoData['index_page_title'] ?? '') ?></div>
                <div class="c2 dark-c4 fs20 fs32-M"><?= htmlspecialchars($seoData['index_page_lead_paragraph'] ?? '') ?></div>
                </div>
                <label for="dataType" class="c2 dark-c4 fs20 fs32-M"> </label>
 <div class="     dark-c8  mb72-M">
    <select id="dataType" name="dataType" class="styled-select">
        <option value="link">Link</option>
        <option value="text">Text</option>
        <option value="email">Email</option>
        <option value="location">Location</option>
        <option value="phone">Phone</option>
        <option value="sms">SMS</option>
        <option value="whatsapp">WhatsApp</option>
        <option value="skype">Skype</option>
        <option value="zoom">Zoom</option>
        <option value="wifi">WiFi</option>
        <option value="vcard">vCard</option>
        <option value="paypal">PayPal</option>
    </select>
</div>
 <script>
document.addEventListener("DOMContentLoaded", function() {
    var select = document.getElementById('dataType');

    function toggleLinkOption() {
        Array.from(select.options).forEach(function(option) {
            if (option.value === 'link') {
                option.selected = true;
                var event = new Event('change', { bubbles: true });
                select.dispatchEvent(event);
                
                var optionButton = document.querySelector('.option[data-value="link"]');
                optionButton.classList.add('selected');
            }
        });
    }

    toggleLinkOption();
});


</script>

<script>


</script>
   
<style>


        body {
            background: <?php echo getCssValue('body', 'background', $pdo); ?>;
        }

        .c3 {
            color: <?php echo getCssValue('.c3', 'color', $pdo); ?>;
        }

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
:root {
    --dark-mode-bg-color: <?php echo getCssValue('.dark .dark-c8', 'color', $pdo); ?>;
}

input[type="submit"] {
    background-color: <?php echo getCssValue('.c3', 'color', $pdo); ?>; 
}

.dark input[type="submit"] {
    background-color: var(--dark-mode-bg-color); 
}





    </style>
    
    

<body>
<div class="pb72">
        <div class="layout-wrapper">
            <div class="bgc12 dark-bgc9 py48 py72-L px16 br12 form-section">
                <div class="accordion-wrapper">
                    <div class="accordion">
                                <div id="dataInput"></div>
                                

<button class="accordion-btn tac fs24 fs40-M fw6 c3 dark-c8 mb24">
    <svg class="icon wh60 c3 c3 dark-c8 mr8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M512 256c0 .9 0 1.8 0 2.7c-.4 36.5-33.6 61.3-70.1 61.3H344c-26.5 0-48 21.5-48 48c0 3.4 .4 6.7 1 9.9c2.1 10.2 6.5 20 10.8 29.9c6.1 13.8 12.1 27.5 12.1 42c0 31.8-21.6 60.7-53.4 62c-3.5 .1-7 .2-10.6 .2C114.6 512 0 397.4 0 256S114.6 0 256 0S512 114.6 512 256zM128 288a32 32 0 1 0 -64 0 32 32 0 1 0 64 0zm0-96a32 32 0 1 0 0-64 32 32 0 1 0 0 64zM288 96a32 32 0 1 0 -64 0 32 32 0 1 0 64 0zm96 96a32 32 0 1 0 0-64 32 32 0 1 0 0 64z"/></svg>
    Colors
</button>
                        <div class="panel">
                            <label for="bgcolor" class="c2 dark-c4 fs20 fs32-M">Background Color:</label>
                            <input type="color" id="bgcolor" name="bgcolor" value="#ffffff">
                            <br>
                            <label for="dotsColor" class="c2 dark-c4 fs20 fs32-M">Dots Color:</label>
                            <input type="color" id="dotsColor" name="dotsColor" value="#000000">
                            <br>
                            <label for="cornersSquareColor" class="c2 dark-c4 fs20 fs32-M">Corners Square Color:</label>
                            <input type="color" id="cornersSquareColor" name="cornersSquareColor" value="#000000">
                            <br>
                            <label for="cornersDotColor" class="c2 dark-c4 fs20 fs32-M">Corners Dot Color:</label>
                            <input type="color" id="cornersDotColor" name="cornersDotColor" value="#000000">
                        </div>

<button class="accordion-btn tac fs24 fs40-M fw6 c3 dark-c8 mb24">
    <svg class="icon wh60 c3 c3 dark-c8 mr8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM199.4 312.6c31.2 31.2 81.9 31.2 113.1 0c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9c-50 50-131 50-181 0s-50-131 0-181s131-50 181 0c9.4 9.4 9.4 24.6 0 33.9s-24.6 9.4-33.9 0c-31.2-31.2-81.9-31.2-113.1 0s-31.2 81.9 0 113.1z"/></svg>
    Logo
</button>                       <div class="panel">
    <label for="logo" class="c2 dark-c4 fs20 fs32-M">Upload Logo:</label>
    <input type="file" id="logo" name="logo"><br>

   <label for="logoSize" class="c2 dark-c4 fs20 fs32-M">Logo Size:</label>
    <input type="range" id="logoSize" name="logoSize" min="0.1" max="1" step="0.01" value="0.8">
    <span id="logoSizeValue">100%</span><br>

    <label for="logoMargin" class="c2 dark-c4 fs20 fs32-M">Logo Margin:</label>
    <input type="range" id="logoMargin" name="logoMargin" min="0" max="100" step="1" value="0">
    <span id="logoMarginValue">0%</span><br>
</div>
<button class="accordion-btn tac fs24 fs40-M fw6 c3 dark-c8 mb24">
    <svg class="icon wh60 c3 c3 dark-c8 mr8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M0 80C0 53.5 21.5 32 48 32h96c26.5 0 48 21.5 48 48v96c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V80zM64 96v64h64V96H64zM0 336c0-26.5 21.5-48 48-48h96c26.5 0 48 21.5 48 48v96c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V336zm64 16v64h64V352H64zM304 32h96c26.5 0 48 21.5 48 48v96c0 26.5-21.5 48-48 48H304c-26.5 0-48-21.5-48-48V80c0-26.5 21.5-48 48-48zm80 64H320v64h64V96zM256 304c0-8.8 7.2-16 16-16h64c8.8 0 16 7.2 16 16s7.2 16 16 16h32c8.8 0 16-7.2 16-16s7.2-16 16-16s16 7.2 16 16v96c0 8.8-7.2 16-16 16H368c-8.8 0-16-7.2-16-16s-7.2-16-16-16s-16 7.2-16 16v64c0 8.8-7.2 16-16 16H272c-8.8 0-16-7.2-16-16V304zM368 480a16 16 0 1 1 0-32 16 16 0 1 1 0 32zm64 0a16 16 0 1 1 0-32 16 16 0 1 1 0 32z"/></svg>
    QR Style
</button>   
                        <div class="panel">
                            <label for="cornersSquareStyle" class="c2 dark-c4 fs20 fs32-M">Corners Square Style:</label>
                            <select id="cornersSquareStyle" name="cornersSquareStyle">
                                <option value="square">Square</option>
                                <option value="rounded">Rounded</option>
                            </select>
                            <br>
                            <label for="cornersDotStyle" class="c2 dark-c4 fs20 fs32-M">Corners Dot Style:</label>
                            <select id="cornersDotStyle" name="cornersDotStyle">
                                <option value="dot">Dot</option>
                                <option value="square">Square</option>
                            </select>
                            <br>
                            <label for="dotsStyle" class="c2 dark-c4 fs20 fs32-M">Dots Style:</label>
                            <select id="dotsStyle" name="dotsStyle">
                                <option value="dot">Dot</option>
                                <option value="rounded">Rounded</option>
                                <option value="classy">Classy</option>
                            </select>
                        </div>

<button class="accordion-btn tac fs24 fs40-M fw6 c3 dark-c8 mb24">
    <svg class="icon wh60 c3 c3 dark-c8 mr8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M495.9 166.6c3.2 8.7 .5 18.4-6.4 24.6l-43.3 39.4c1.1 8.3 1.7 16.8 1.7 25.4s-.6 17.1-1.7 25.4l43.3 39.4c6.9 6.2 9.6 15.9 6.4 24.6c-4.4 11.9-9.7 23.3-15.8 34.3l-4.7 8.1c-6.6 11-14 21.4-22.1 31.2c-5.9 7.2-15.7 9.6-24.5 6.8l-55.7-17.7c-13.4 10.3-28.2 18.9-44 25.4l-12.5 57.1c-2 9.1-9 16.3-18.2 17.8c-13.8 2.3-28 3.5-42.5 3.5s-28.7-1.2-42.5-3.5c-9.2-1.5-16.2-8.7-18.2-17.8l-12.5-57.1c-15.8-6.5-30.6-15.1-44-25.4L83.1 425.9c-8.8 2.8-18.6 .3-24.5-6.8c-8.1-9.8-15.5-20.2-22.1-31.2l-4.7-8.1c-6.1-11-11.4-22.4-15.8-34.3c-3.2-8.7-.5-18.4 6.4-24.6l43.3-39.4C64.6 273.1 64 264.6 64 256s.6-17.1 1.7-25.4L22.4 191.2c-6.9-6.2-9.6-15.9-6.4-24.6c4.4-11.9 9.7-23.3 15.8-34.3l4.7-8.1c6.6-11 14-21.4 22.1-31.2c5.9-7.2 15.7-9.6 24.5-6.8l55.7 17.7c13.4-10.3 28.2-18.9 44-25.4l12.5-57.1c2-9.1 9-16.3 18.2-17.8C227.3 1.2 241.5 0 256 0s28.7 1.2 42.5 3.5c9.2 1.5 16.2 8.7 18.2 17.8l12.5 57.1c15.8 6.5 30.6 15.1 44 25.4l55.7-17.7c8.8-2.8 18.6-.3 24.5 6.8c8.1 9.8 15.5 20.2 22.1 31.2l4.7 8.1c6.1 11 11.4 22.4 15.8 34.3zM256 336a80 80 0 1 0 0-160 80 80 0 1 0 0 160z"/></svg>
Options
</button>                          <div class="panel">
                            <label id="shape-label" for="shape" class="c2 dark-c4 fs20 fs32-M">Shape:</label>
<select id="shape" name="shape"></select>
<br>
<label id="quality-label" for="quality" class="c2 dark-c4 fs20 fs32-M">Quality:</label>
<select id="quality" name="quality"></select>

<label for="size" class="c2 dark-c4 fs20 fs32-M">Size:</label>
<select id="size" name="size">
    <option value="small">Small</option>
    <option value="medium">Medium</option>
    <option value="medium-large" selected>Medium-Large</option>
    <option value="large">Large</option>
</select>


                            <br>
                            <label for="ecLevel" class="c2 dark-c4 fs20 fs32-M">Error Correction:</label>
                            <select id="ecLevel" name="ecLevel">
                                <option value="L">Low</option>
                                <option value="M">Medium</option>
                                <option value="Q">Quartile</option>
                                <option value="H">High</option>
                            </select>
                            <br>
                            <label for="format" class="c2 dark-c4 fs20 fs32-M">Format:</label>
                            <select id="format" name="format">
                                <option value="png">PNG</option>
                                <option value="jpeg">JPEG</option>
                                <option value="webp">WebP</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

<div class="bgc12 dark-bgc9 py48 py72-L px16 br12 qr-section">
    <form id="qrForm">
        
        <br>
        <br>
       
    <div id="qr-container" class="icon  c3 dark-c8 ">
       <svg id="qr-placeholder" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" width="384" height="384">
    <path d="M0 80C0 53.5 21.5 32 48 32h96c26.5 0 48 21.5 48 48v96c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V80zM64 96v64h64V96H64zM0 336c0-26.5 21.5-48 48-48h96c26.5 0 48 21.5 48 48v96c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V336zm64 16v64h64V352H64zM304 32h96c26.5 0 48 21.5 48 48v96c0 26.5-21.5 48-48 48H304c-26.5 0-48-21.5-48-48V80c0-26.5 21.5-48 48-48zm80 64H320v64h64V96zM256 304c0-8.8 7.2-16 16-16h64c8.8 0 16 7.2 16 16s7.2 16 16 16h32c8.8 0 16-7.2 16-16s7.2-16 16-16s16 7.2 16 16v96c0 8.8-7.2 16-16 16H368c-8.8 0-16-7.2-16-16s-7.2-16-16-16s-16 7.2-16 16v64c0 8.8-7.2 16-16 16H272c-8.8 0-16-7.2-16-16V304zM368 480a16 16 0 1 1 0-32 16 16 0 1 1 0 32zm64 0a16 16 0 1 1 0-32 16 16 0 1 1 0 32z"/>
</svg>

        <div id="qr-code" style="display: none;"></div>
<input type="submit" value="Generate QR" class="">

    </form>
    </div>
<button id="downloadBtn" style="display:none;">
    <svg class="icon wh60 c3  dark-c8 " xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
        <path d="M288 32c0-17.7-14.3-32-32-32s-32 14.3-32 32V274.7l-73.4-73.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l128 128c12.5 12.5 32.8 12.5 45.3 0l128-128c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L288 274.7V32zM64 352c-35.3 0-64 28.7-64 64v32c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V416c0-35.3-28.7-64-64-64H346.5l-45.3 45.3c-25 25-65.5 25-90.5 0L165.5 352H64zm368 56a24 24 0 1 1 0 48 24 24 0 1 1 0-48z"/>
    </svg>
</button>
<div class="banner-container"><?php echo $banner1_html; ?></div>
</div>

</div>
</div>


<script>
var shapeLabel = document.getElementById('shape-label');
var shapeSelect = document.getElementById('shape');
var qualityLabel = document.getElementById('quality-label');
var qualitySelect = document.getElementById('quality');

if (shapeLabel && shapeSelect) {
    shapeLabel.style.display = 'none'; 
    shapeSelect.style.display = 'none'; 
}

if (qualityLabel && qualitySelect) {
    qualityLabel.style.display = 'none'; 
    qualitySelect.style.display = 'none'; 
}
</script>
 <script>

document.addEventListener("DOMContentLoaded", function() {
    var logoSizeInput = document.getElementById("logoSize");
    var logoSizeValue = document.getElementById("logoSizeValue");
    logoSizeValue.textContent = (logoSizeInput.value * 100) + "%";
    logoSizeInput.addEventListener("input", function() {
        logoSizeValue.textContent = (this.value * 100) + "%";
    });

    var logoMarginInput = document.getElementById("logoMargin");
    var logoMarginValue = document.getElementById("logoMarginValue");
    logoMarginValue.textContent = logoMarginInput.value + "%";
    logoMarginInput.addEventListener("input", function() {
        logoMarginValue.textContent = this.value + "%";
    });
});
    </script>

 <script>
 
      document.addEventListener("DOMContentLoaded", function() {
    var accordionButtons = document.querySelectorAll('.accordion-btn');

    accordionButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            var panel = this.nextElementSibling;
            if (panel.style.display === "block") {
                panel.style.display = "none";
            } else {
                panel.style.display = "block";
            }
        });
    });
});

    </script>
    


<script>



</script>
<script>
  
</script>

<script>

</script>


        <script src="https://unpkg.com/qr-code-styling@1.5.0/lib/qr-code-styling.js"></script>
<script src="js/qr.js"></script>
<script src="js/icons-types.js"></script>
<script src="js/input-Listener.js"></script>
<script src="js/qr-forms.js"></script>











  

</div>
<?php if ($featureboxes): ?>

   <div class="py48 py72-L">
        <div class="container">
        <div class="tac fs24 fs40-M fw6 c3 dark-c8 mb48 mb72-M">How it works
</div>            <div class="R gy48">
                <?php foreach ($how_it_works as $item): ?>
                    <div class="C-M tac">
                        <svg class="icon wh72 wh96-L c3 dark-c8 mb32">
                            <?php echo $item['icon_svg']; ?>
                        </svg>
                        <div class="fs24 c1 dark-c12 fw6 mb20"><?php echo htmlspecialchars($item['icon_title']); ?></div>
                        <div class="fs16 c2 dark-c4"><?php echo htmlspecialchars($item['icon_description']); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif; ?>
<style>

</style>

<?php if ($blog): ?>
    <div class="container">
        <h1 class="tac fs24 fs40-M fw6 c3 dark-c8 mb48 mb72-M">Latest Blogs</h1>
        <div class="latest-blogs">
            <?php foreach ($articles as $index => $article): ?>
                <a href="article.php?id=<?php echo $article['id']; ?>" class="blog-item">
                    <div class="blog-content">
                        <img src="/admin/<?php echo $article['featured_image']; ?>" alt="<?php echo $article['title']; ?>">
                        <h1 class="fs24 c1 dark-c12 fw6 mb20"><?php echo $article['title']; ?></h1>
                        <div class="fs20-S c2 dark-c4 fs16"><?php echo substr($article['content'], 0, 50); ?>...</div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>








<div class="secondbanner-container"><?php echo $banner2_html; ?></div>
</div>
  
<?php if ($faq): ?>
    <div class="pb72">
      <div class="container">
        <div class="bgc12 dark-bgc9 py48 py72-L px16 br12">
          <div class="tac fs24 fs40-M fw6 c3 dark-c8 mb48 mb72-M">FAQs</div>
          <div class="ma" style="max-width:800px;">
            <div class="gy48 gy52-M">
              <?php foreach ($faqs as $faq): ?>
                <div>
                  <div class="fs20 fs24-S fw6 c1 dark-c12 mb16 lh13"><?php echo htmlspecialchars($faq['question']); ?></div>
                  <div class="c2 dark-c4 fs16 fs20-S"><?php echo htmlspecialchars($faq['answer']); ?></div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
<?php endif; ?>


<?php
require_once 'footer.php';
?>


  


  

  <script>
    function openTawkToChat() {
      <?php if (!empty($tawkToDirectLink)): ?>
        window.open('<?php echo $tawkToDirectLink; ?>', 'TawkToChatWindow', 'width=800,height=600');
      <?php endif; ?>
    }
  </script>
 

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

<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo $googleAnalyticsMeasurementID; ?>"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', '<?php echo $googleAnalyticsMeasurementID; ?>');
</script>


  

</body>

</html>