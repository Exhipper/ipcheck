<?php
$IPQS_KEY = 'D4ePykpazLxnbGiRVZ5VfeOtaUcP9SRO'; // Your key

// Get user's IP
$ip = $_GET['ip'] ?? ($_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? '');

$result = null;
$error = '';

if (!empty($_GET['ip']) || isset($_GET['check'])) {
    $ip_to_check = $_GET['ip'] ?? $ip;
    
    if (filter_var($ip_to_check, FILTER_VALIDATE_IP)) {
        $url = "https://ipqualityscore.com/api/json/ip/{$IPQS_KEY}/{$ip_to_check}";
        $params = [
            'strictness' => 1,
            'fast' => 'true'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . '?' . http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 12);
        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);
        
        if (!$result || !isset($result['success']) || $result['success'] !== true) {
            $error = $result['message'] ?? 'Failed to get data from API';
        }
    } else {
        $error = 'Invalid IP address';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Netflix IP Checker</title>
    <style>
        body { font-family: Arial, sans-serif; background: #0f0f0f; color: #fff; text-align: center; padding: 40px; }
        .container { max-width: 700px; margin: auto; background: #1f1f1f; padding: 30px; border-radius: 12px; }
        input { padding: 12px; width: 300px; font-size: 18px; border-radius: 8px; border: none; }
        button { padding: 12px 24px; font-size: 18px; background: #e50914; color: white; border: none; border-radius: 8px; cursor: pointer; }
        .result { margin-top: 30px; padding: 20px; border-radius: 10px; font-size: 18px; }
        .clean { background: #006400; }
        .risky { background: #8B0000; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎥 Netflix IP Fraud Checker</h1>
        <p>Check if an IP is clean for Netflix</p>

        <form method="GET">
            <input type="text" name="ip" placeholder="Enter IP address" value="<?php echo htmlspecialchars($ip); ?>">
            <button type="submit" name="check">Check IP</button>
        </form>

        <p><strong>Your Current IP:</strong> <?php echo htmlspecialchars($ip); ?></p>

        <?php if ($result): ?>
            <div class="result <?php echo ($result['fraud_score'] <= 25 && !$result['proxy'] && !$result['vpn'] && !$result['tor']) ? 'clean' : 'risky'; ?>">
                <h2>IP: <?php echo htmlspecialchars($result['ip'] ?? $ip); ?></h2>
                
                <?php
                $clean = ($result['fraud_score'] <= 25 && !$result['proxy'] && !$result['vpn'] && !$result['tor'] && !$result['recent_abuse']);
                ?>
                <h3><?php echo $clean ? '✅ CLEAN IP - Should work on Netflix' : '❌ RISKY / DIRTY IP - High chance of block'; ?></h3>
                
                <p><strong>Fraud Score:</strong> <?php echo $result['fraud_score']; ?>/100</p>
                <p><strong>Proxy:</strong> <?php echo $result['proxy'] ? 'Yes' : 'No'; ?></p>
                <p><strong>VPN:</strong> <?php echo $result['vpn'] ? 'Yes' : 'No'; ?></p>
                <p><strong>Tor:</strong> <?php echo $result['tor'] ? 'Yes' : 'No'; ?></p>
                <p><strong>Recent Abuse:</strong> <?php echo $result['recent_abuse'] ? 'Yes' : 'No'; ?></p>
                <p><strong>ISP:</strong> <?php echo htmlspecialchars($result['isp'] ?? 'Unknown'); ?></p>
                <p><strong>Country:</strong> <?php echo htmlspecialchars($result['country_code'] ?? 'Unknown'); ?></p>
            </div>
        <?php elseif ($error): ?>
            <div class="result risky">
                <p>❌ <?php echo htmlspecialchars($error); ?></p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
