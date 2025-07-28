<?php
// Get PSN ID and Avatar ID from query string, with defaults
$psnId = isset($_GET['psnId']) && !empty($_GET['psnId']) ? $_GET['psnId'] : 'PlayStationUS';
$avatarId = isset($_GET['avatar']) && !empty($_GET['avatar']) ? $_GET['avatar'] : 'default-avatar-id';

$url = "https://psnprofiles.com/" . urlencode($psnId);

// Fetch the profile page
$options = [
    "http" => [
        "header" => "User-Agent: Mozilla/5.0\r\n"
    ]
];
$context = stream_context_create($options);
$html = @file_get_contents($url, false, $context);

if ($html === false) {
    // Fallback/defaults
    $username = $psnId;
    $level = 1;
    $avatar = 'https://i.psnprofiles.com/avatars/m/' . $avatarId . '.png';
    $platinum = $gold = $silver = $bronze = 0;
} else {
    // Parse the HTML using DOMDocument and XPath
    libxml_use_internal_errors(false); // Suppress HTML parsing errors
    $doc = new DOMDocument();
    $doc->loadHTML($html);
    $xpath = new DOMXPath($doc);

    // Username
    $usernameNode = $xpath->query("//div[contains(@class,'profile-header')]//h1");
    $username = $usernameNode->length ? trim($usernameNode->item(0)->textContent) : $psnId;

    // Avatar
    $avatarNode = $xpath->query("//div[contains(@class,'avatar')]/img");
    if ($avatarNode->length && $avatarNode->item(0) !== null) {
        $avatarElement = $avatarNode->item(0);
        if ($avatarElement instanceof DOMElement) {
            $avatar = $avatarElement->getAttribute('src');
        } else {
            $avatar = 'https://i.psnprofiles.com/avatars/m/' . $avatarId . '.png';
        }
    } else {
        $avatar = 'https://i.psnprofiles.com/avatars/m/' . $avatarId . '.png';
    }

    // Level
    $levelNode = $xpath->query("//li[contains(@class,'icon-sprite-level')]/span");
    $level = $levelNode->length ? trim($levelNode->item(0)->textContent) : 1;

    // Trophies
    $trophies = ['platinum'=>0, 'gold'=>0, 'silver'=>0, 'bronze'=>0];
    $trophyNodes = $xpath->query("//ul[contains(@class,'profile-trophy-counts')]/li");
    foreach ($trophyNodes as $li) {
        if ($li instanceof DOMElement) {
            $class = $li->getAttribute('class');
            $count = preg_replace('/[^\d]/', '', $li->textContent);
            if (strpos($class, 'platinum') !== false) $trophies['platinum'] = (int)$count;
            if (strpos($class, 'gold') !== false) $trophies['gold'] = (int)$count;
            if (strpos($class, 'silver') !== false) $trophies['silver'] = (int)$count;
            if (strpos($class, 'bronze') !== false) $trophies['bronze'] = (int)$count;
        }
    }
    $platinum = $trophies['platinum'];
    $gold = $trophies['gold'];
    $silver = $trophies['silver'];
    $bronze = $trophies['bronze'];

    // Clean up
    $username = trim($username);
    $level = (int)$level;
    $avatar = filter_var($avatar, FILTER_VALIDATE_URL) ? $avatar : 'https://i.psnprofiles.com/avatars/m/' . $avatarId . '.png';
    $platinum = (int)$platinum;
    $gold = (int)$gold;
    $silver = (int)$silver;
    $bronze = (int)$bronze;
}
?>
<?php header('Content-Type: text/html; charset=UTF-8'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PSN Profile: <?php echo htmlspecialchars($username); ?></title>
    <style>
        body { font-family: Arial, sans-serif; background: #222; color: #eee; }
        .profile { max-width: 400px; margin: 40px auto; background: #333; padding: 24px; border-radius: 10px; box-shadow: 0 2px 8px #111; }
        .avatar { width: 100px; height: 100px; border-radius: 50%; border: 3px solid #444; }
        .username { font-size: 1.5em; margin: 16px 0 8px; }
        .level { color: #ffd700; font-weight: bold; }
        .trophies { display: flex; justify-content: space-between; margin-top: 20px; }
        .trophy { text-align: center; }
        .trophy span { display: block; font-size: 1.2em; }
        .platinum { color: #e5e4e2; }
        .gold { color: #ffd700; }
        .silver { color: #c0c0c0; }
        .bronze { color: #cd7f32; }
    </style>
</head>
<body>
    <div class="profile">
        <img class="avatar" src="<?php echo htmlspecialchars($avatar); ?>" alt="Avatar">
        <div class="username"><?php echo htmlspecialchars($username); ?></div>
        <div class="level">Level: <?php echo htmlspecialchars($level); ?></div>
        <div class="trophies">
            <div class="trophy platinum">
                <span>🏆</span>
                <div><?php echo htmlspecialchars($platinum); ?> Platinum</div>
            </div>
            <div class="trophy gold">
                <span>🥇</span>
                <div><?php echo htmlspecialchars($gold); ?> Gold</div>
            </div>
            <div class="trophy silver">
                <span>🥈</span>
                <div><?php echo htmlspecialchars($silver); ?> Silver</div>
            </div>
            <div class="trophy bronze">
                <span>🥉</span>
                <div><?php echo htmlspecialchars($bronze); ?> Bronze</div>
            </div>
        </div>
    </div>
</body>
</html>
