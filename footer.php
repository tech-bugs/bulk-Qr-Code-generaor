 <?php
 
 require_once __DIR__ . '/vendor/autoload.php';
// Assigning environment variables to PHP variables
$host = $_ENV['DB_HOST'] ?? 'localhost';
$port = $_ENV['DB_PORT'] ?? 3306;
$dbname = $_ENV['DB_NAME'] ?? '';
$user = $_ENV['DB_USER'] ?? '';
$pass = $_ENV['DB_PASS'] ?? '';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
// Database connection
try {
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
// Fetch social media links from the database
    $stmt = $pdo->query("SELECT * FROM `social_media`");
    $socialMediaLinks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch list of pages to include in the footer
$stmt = $pdo->prepare("SELECT id, title FROM pages WHERE include_footer = 1");
$stmt->execute();


$footerPages = $stmt->fetchAll(PDO::FETCH_ASSOC);
$featureboxes = $_ENV['FEATURE_BOXES'] === 'true';
$tawkTowidgetlink = $_ENV['TAWKTO_WIDGET_LINK'];




$faq = $_ENV['FAQ'] === 'true';
// Get the Tawk.to widget link from the environment
$tawkToDirectLink = $_ENV['TAWKTO_WIDGET_LINK'];

// Get the Tawk.to widget link from the environment
$tawkToDirectLink = $_ENV['TAWKTO_WIDGET_LINK'];
// Access environment variables
$toolDomainSearchEnabled = $_ENV['TOOL_DOMAIN_SEARCH'] ?? false;
$toolAIDomainGeneratorEnabled = $_ENV['TOOL_AI_DOMAIN_GENERATOR'] ?? false;
$toolDomainGeneratorEnabled = $_ENV['TOOL_DOMAIN_GENERATOR'] ?? false;
$toolWhoisSearchEnabled = $_ENV['TOOL_WHOIS_SEARCH'] ?? false;
$toolDnsSearchEnabled = $_ENV['TOOL_DNS_SEARCH'] ?? false;

// Define default values for tools
$TOOL_DOMAIN_SEARCH = isset($_ENV['TOOL_DOMAIN_SEARCH']) && $_ENV['TOOL_DOMAIN_SEARCH'] === 'true';
$TOOL_AI_DOMAIN_GENERATOR = isset($_ENV['TOOL_AI_DOMAIN_GENERATOR']) && $_ENV['TOOL_AI_DOMAIN_GENERATOR'] === 'true';
$TOOL_DOMAIN_GENERATOR = isset($_ENV['TOOL_DOMAIN_GENERATOR']) && $_ENV['TOOL_DOMAIN_GENERATOR'] === 'true';
$TOOL_WHOIS_SEARCH = isset($_ENV['TOOL_WHOIS_SEARCH']) && $_ENV['TOOL_WHOIS_SEARCH'] === 'true';
$TOOL_DNS_SEARCH = isset($_ENV['TOOL_DNS_SEARCH']) && $_ENV['TOOL_DNS_SEARCH'] === 'true';
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-xyz" crossorigin="anonymous" />


   <script>
    function openTawkToChat() {
      <?php if (!empty($tawkToDirectLink)): ?>
        window.open('<?php echo $tawkToDirectLink; ?>', 'TawkToChatWindow', 'width=800,height=600');
      <?php endif; ?>
    }
  </script>
 <footer class="py40 mta bgc6">
    <div class="container">
        <div class="R gy48 gx48">
            <div class="C0-M mr72-L">
                  <div class="C0-M">
   <div class="c7 fs24 fw6 mb12">Links</div>
<div class="gy12">
    <?php foreach ($headerLinks as $link): ?>
        <div><a href="<?php echo htmlspecialchars($link['url']); ?>" class="link2 c4"><?php echo htmlspecialchars($link['title']); ?></a></div>
    <?php endforeach; ?>
    <?php if (!empty($tawkToDirectLink)): ?>
        <div><a class='link2 c4' onclick="openTawkToChat()" style="cursor: pointer;">Live Chat</a></div>
    <?php endif; ?>
</div>
          </div>
        </div>
        
<div class="C0-M" style="margin-bottom: 10px;">
                <div class="c7 fs24 fw6 mb12">Pages</div>
                <div class="gy12">
                    <?php foreach ($footerPages as $page): ?>
                        <div><a href="page.php?id=<?php echo $page['id']; ?>" class="link2 c4"><?php echo htmlspecialchars($page['title']); ?></a></div>
                    <?php endforeach; ?>
                    </div>



           

        </div>
        <div class="C-M tar-M"><img class="mb12" src="/admin/uploads/logodark.png" alt="">
<div>
    <p id="copyright" class="mt-3">
        <?= htmlspecialchars($seoData['footer_content'] ?? '') ?>
    </p>

    <div class="social-media-icons">
        <ul>
            <?php foreach ($socialMediaLinks as $socialMediaLink): ?>
                <li style="display: inline-block; margin-right: 10px;">
                    <a href="<?php echo htmlspecialchars($socialMediaLink['link']); ?>">
                        <?php echo $socialMediaLink['icon_class']; ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>


   <style>

.social-media-icons ul li a svg {
    width: 24px;
    height: 24px;
}
.social-media-icons {
    position: relative;
    text-align: right;
}

.social-media-icons ul {
    margin: 0;
    padding: 0;
    list-style-type: none;
}

.social-media-icons ul li {
    display: inline-block;
    margin-right: 10px; /* Adjust as needed */
    vertical-align: middle;
}

.social-media-icons ul li:last-child {
    margin-right: 0;
}

.social-media-icons ul li a svg {
    width: 24px;
    height: 24px;
    fill: #fff; /* Set icon color to white */
}


    </style>



    </div>
  </footer>