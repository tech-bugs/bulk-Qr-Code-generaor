<?php
 
 require_once __DIR__ . '/vendor/autoload.php';

$host = $_ENV['DB_HOST'] ?? 'localhost';
$port = $_ENV['DB_PORT'] ?? 3306;
$dbname = $_ENV['DB_NAME'] ?? '';
$user = $_ENV['DB_USER'] ?? '';
$pass = $_ENV['DB_PASS'] ?? '';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$googleAnalyticsMeasurementID = $_ENV['GOOGLE_ANALYTICS']; // Get the Google Analytics Measurement ID from the environment
$tawkTowidgetlink = $_ENV['TAWKTO_WIDGET_LINK'];

// Database connection
try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // Log and display the error message
    error_log("Database connection error: " . $e->getMessage());
    die("ERROR: Could not connect. " . $e->getMessage());
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
// Fetch SEO data from the database
try {
    $stmt = $pdo->query("SELECT * FROM seo_metadata WHERE id = 2");
    $seoData = $stmt->fetch(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    // Log and display the error message
    error_log("Error fetching SEO data: " . $e->getMessage());
    // Handle the error as needed
}

// Fetch SEO data from the database
try {
   // Fetch social media links from the database
    $stmt = $pdo->query("SELECT * FROM `social_media`");
    $socialMediaLinks = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    // Log and display the error message
    error_log("Error fetching SEO data: " . $e->getMessage());
    // Handle the error as needed
}
 try {
        // Create a database connection using PDO
        $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Function to get CSS value for a specific selector and property from the database
        function getCssValue($selector, $property, $pdo) {
            $stmt = $pdo->prepare("SELECT value FROM css_styles WHERE selector = :selector AND property = :property");
            $stmt->execute([':selector' => $selector, ':property' => $property]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['value'] : ''; // Return the value or an empty string if not found
        }
    } catch(PDOException $e) {
        // Log and display the error message
        error_log("Database error: " . $e->getMessage());
    }
  
// Fetch header links from the database
$stmt = $pdo->prepare("SELECT id, title, url FROM header_links WHERE enabled = 1");
$stmt->execute();
$headerLinks = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Fetch list of pages to include in the header
$stmt = $pdo->prepare("SELECT id, title FROM pages WHERE include_header = 1");
$stmt->execute();
$headerPages = $stmt->fetchAll(PDO::FETCH_ASSOC);
$showWidget = $_ENV['GTRANSLATER'] === 'true';
$darkmode = $_ENV['DARKMODE'] === 'true';
$showWidget = $_ENV['GTRANSLATER'] === 'true';


?>
  <link rel="icon" href="admin/uploads/favicon.png" type="image/x-icon">

  <link rel="stylesheet" href="styles.css">
  
  <link rel="stylesheet" href="static/assets/css/styles.min.css">

 
      <!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo $googleAnalyticsMeasurementID; ?>"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', '<?php echo $googleAnalyticsMeasurementID; ?>');
</script>

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
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


<header class="pt20">
    <div class="container">
        <div class="R aic gx16">
            <div class="C">
           <a class="button" href="index.php">
                    <img class="logo" src="/admin/uploads/logo.png" alt="" height="auto">
                    <img class="logo-dark" src="/admin/uploads/logodark.png" alt="" height="auto">
                </a>

  <div class="adsense-container"><?php echo $adsense_html; ?></div>
            </div>
<div class="C0    " id="headerLinks">
    <ul class="header-links">
        <?php
        foreach ($headerLinks as $link) {
            echo '<li><a href="' . htmlspecialchars($link['url']) . '" class="c2 dark-c4 fs20 fs32-M">' . htmlspecialchars($link['title']) . '</a></li>';
        }
        ?>

        <?php foreach ($headerPages as $page): ?>
            <li><a href="page.php?id=<?php echo $page['id']; ?>" class="c2 dark-c4 fs20 fs32-M"><?php echo htmlspecialchars($page['title']); ?></a></li>
        <?php endforeach; ?>
    </ul>
</div>

            <div class="C0">
                
                <div class=" gtranslate_wrapper"></div>
                <?php if ($showWidget): ?>
                    <script src="https://cdn.gtranslate.net/widgets/latest/popup.js" defer></script>
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            window.gtranslateSettings = {
                                "default_language": "en",
                                "native_language_names": true,
                                "wrapper_selector": ".gtranslate_wrapper",
                                "horizontal_position": "right",
                                "vertical_position": "top"
                                
                            };
                            
                var style = document.createElement('style');
                style.type = 'text/css';
                style.innerHTML = '.gtranslate_wrapper { padding: 2px 2px; }';

                document.head.appendChild(style);
                        });
                    </script>
                <?php endif; ?>
</div>

<div class="C0 ">
    <?php if ($darkmode): ?>
        <div class="wh60 c3 c3 dark-c8  container">
            <label class="icon wh60 c3 c3 dark-c8 mr8 toggle" for="darkModeSwitch">
                <input id="darkModeSwitch" class="  toggle-input" type="checkbox">
                <div class="icon icon--moon">
                    <svg height="25" width="25" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path clip-rule="evenodd" d="M9.528 1.718a.75.75 0 01.162.819A8.97 8.97 0 009 6a9 9 0 009 9 8.97 8.97 0 003.463-.69.75.75 0 01.981.98 10.503 10.503 0 01-9.694 6.46c-5.799 0-10.5-4.701-10.5-10.5 0-4.368 2.667-8.112 6.46-9.694a.75.75 0 01.818.162z" fill-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="   wh60 c3 c3 dark-c8 mr8 icon icon--sun">
                    <svg height="25" width="25" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2.25a.75.75 0 01.75.75v2.25a.75.75 0 01-1.5 0V3a.75.75 0 01.75-.75zM7.5 12a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM18.894 6.166a.75.75 0 00-1.06-1.06l-1.591 1.59a.75.75 0 101.06 1.061l1.591-1.59zM21.75 12a.75.75 0 01-.75.75h-2.25a.75.75 0 010-1.5H21a.75.75 0 01.75.75zM17.834 18.894a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 10-1.061 1.06l1.59 1.591zM12 18a.75.75 0 01.75.75V21a.75.75 0 01-1.5 0v-2.25A.75.75 0 0112 18zM7.758 17.303a.75.75 0 00-1.061-1.06l-1.591 1.59a.75.75 0 001.06 1.061l1.591-1.59zM6 12a.75.75 0 01-.75.75H3a.75.75 0 010-1.5h2.25A.75.75 0 016 12zM6.697 7.757a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 00-1.061 1.06l1.59 1.591z"></path>
                    </svg>
                </div>
            </label>
        </div>
    <?php endif; ?>
</div>

<style>
        body {
            background: <?php echo getCssValue('body', 'background', $pdo); ?>;
        }
        


        .c3 .h1 {
            color: <?php echo getCssValue('.c3', 'color', $pdo); ?>;
        }

     .h1{
            color: <?php echo getCssValue('.c3', 'color', $pdo); ?>;
        }
  

        .dark .dark-c8 {
            color: <?php echo getCssValue('.dark .dark-c8', 'color', $pdo); ?>;
        }
    </style>
<style>


.toggle {
    width: 10px; /* Adjust to your desired size */
    height: 10px; /* Adjust to your desired size */
    border-radius: auto;
    display: grid;
    place-items: center;
    cursor: pointer;
    line-height: -3;
    margin-left: -17px; /* Adjust this value as needed to move it to the left */
}

.toggle-input {
  display: none;
}

.icon {
  grid-column: 1 / 1;
  grid-row: 1 / 1;
  transition: transform 500ms;
}

.icon--moon {
  transition-delay: 200ms;
}

.icon--sun {
  transform: scale(0);
}

#darkModeSwitch:checked + .icon--moon {
  transform: rotate(360deg) scale(0);
}

#darkModeSwitch:checked ~ .icon--sun {
  transition-delay: 200ms;
  transform: scale(1) rotate(360deg);
}

header.pt20 {
    padding-top: 20px;
    background-color: ;
    border-bottom: 1px solid rgba(204, 204, 204, 0.5); 
}





</style>

                      <div class="C0">
<div class="mobile-nav-icon" id="mobileNavIcon" style="display: none; cursor: pointer;">
    <svg class="icon wh60 c3 c3 dark-c8 mr8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" style="width: 25px; height: 25px;">
        <path d="M0 96C0 78.3 14.3 64 32 64H416c17.7 0 32 14.3 32 32s-14.3 32-32 32H32C14.3 128 0 113.7 0 96zM0 256c0-17.7 14.3-32 32-32H416c17.7 0 32 14.3 32 32s-14.3 32-32 32H32c-17.7 0-32-14.3-32-32zM448 416c0 17.7-14.3 32-32 32H32c-17.7 0-32-14.3-32-32s14.3-32 32-32H416c17.7 0 32 14.3 32 32z"/>
    </svg>
</div>
</div>

<div id="sideNavigation" class="side-navigation-menu">
    <span class="close-btn" onclick="closeSideNavigation()">&times;</span>
    <ul>
        <?php
       

        foreach ($headerLinks as $link) {
            echo '<li><a href="' . htmlspecialchars($link['url']) . '">' . htmlspecialchars($link['title']) . '</a></li>';
        }
        ?>
        <?php foreach ($headerPages as $page): ?>
            <li><a href="page.php?id=<?php echo $page['id']; ?>"><?php echo htmlspecialchars($page['title']); ?></a></li>
        <?php endforeach; ?>
      <?php foreach ($socialMediaLinks as $socialMediaLink): ?>
    <li>
        <a class="social-media-link" href="<?php echo htmlspecialchars($socialMediaLink['link']); ?>">
            <?php echo $socialMediaLink['icon_class']; ?>
        </a>
    </li>
<?php endforeach; ?>

        <li>
            <p id="copyright" class="mt-3">
                <?= htmlspecialchars($seoData['footer_content']); ?>
            </p>
        </li>
    </ul>
</div>




   <script>
 document.addEventListener('DOMContentLoaded', function () {
    const darkModeSwitch = document.getElementById('darkModeSwitch');
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
        if (darkModeSwitch.checked) {
            body.classList.add('dark');
            localStorage.setItem('darkModeEnabled', 'true');
        } else {
            body.classList.remove('dark');
            localStorage.setItem('darkModeEnabled', 'false');
        }
    });

    const mobileNavIcon = document.getElementById('mobileNavIcon');
    const sideNav = document.getElementById('sideNavigation');

    function toggleSideNavigation() {
        if (sideNav.style.display === 'block') {
            sideNav.style.display = 'none';
        } else {
            sideNav.style.display = 'block';
        }
    }

    if (window.innerWidth <= 600) {
        mobileNavIcon.style.display = 'block';
    }

    mobileNavIcon.addEventListener('click', toggleSideNavigation);
});

function closeSideNavigation() {
    document.getElementById('sideNavigation').style.display = 'none';
}

</script>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const socialMediaLinks = document.querySelectorAll('.social-media-link');

        socialMediaLinks.forEach(link => {
            link.classList.add('social-media-icon');
        });
    });
</script>


<style>
.side-navigation-menu {
    display: none;
}

.side-navigation-menu.show {
    display: block;
}
.side-navigation-menu {
    display: none; 
    position: fixed;
    top: 0; 
    left: 0;
    width: 250px; 
    height: 100%; 
    background-color: #696969 ; 
    z-index: 1000;
    padding-top: 60px; 
}

.side-navigation-menu a {
    display: block; 
    padding: 10px 20px; 
    color: #fff; 
    text-decoration: none;
}

.side-navigation-menu a:hover {
    background-color: #555; 
}

.side-navigation-menu ul {
    list-style-type: none;
    padding: 0;
    margin: 0;
}

.close-btn {
    position: absolute; 
    top: 20px; 
    right: 20px; 
    color: #fff; 
    cursor: pointer; 
    font-size: 24px; 
}
.social-media-icons ul li a svg {
    width: 5px;
    height: 5px;
}
.social-media-icons {
    position: relative;
    text-align: center;
}

.social-media-icons ul {
    margin: 0;
    padding: 0;
    list-style-type: none;
}

.social-media-icons ul li {
    display: inline-block;
    margin-right: 10px; 
    vertical-align: middle;
}

.social-media-icons ul li:last-child {
    margin-right: 0;
}

.social-media-icons ul li a svg {
    width: 9px;
    height: 9px;
    fill: #fff; 
}
    .social-media-icon svg {
        width: 24px; 
        height: 24px; 
        fill: #fff; 
    }
</style>





</header>
