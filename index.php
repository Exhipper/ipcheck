<?php
$IPQS_KEY = 'D4ePykpazLxnbGiRVZ5VfeOtaUcP9SRO';

$ip = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? '';

// Auto check
$result = null;
$error = '';

if (filter_var($ip, FILTER_VALIDATE_IP)) {
    $url = "https://ipqualityscore.com/api/json/ip/{$IPQS_KEY}/{$ip}";
    $params = ['strictness' => 1, 'fast' => 'true'];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url . '?' . http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);

    if (!$result || ($result['success'] ?? false) !== true) {
        $error = $result['message'] ?? 'API request failed';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NEONVOID v2.4 // IP FRAUD SCANNER</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=VT323&display=swap');
        
        body {
            margin: 0; padding: 0;
            background: #000;
            color: #00ff41;
            font-family: 'VT323', monospace;
            overflow: hidden;
        }
        .matrix { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; opacity: 0.2; }
        
        .container {
            position: relative;
            z-index: 2;
            max-width: 820px;
            margin: 40px auto;
            padding: 30px;
            background: rgba(5, 15, 5, 0.95);
            border: 2px solid #00ff41;
            box-shadow: 0 0 40px #00ff41;
        }
        
        h1 { font-size: 2.8rem; text-align: center; text-shadow: 0 0 20px #00ff41, 0 0 40px #ff0000; animation: glitch 1s infinite alternate; }
        .subtitle { text-align: center; color: #ff3333; font-size: 1.5rem; }
        
        .scan-result {
            margin: 25px 0;
            padding: 20px;
            border: 2px solid;
            animation: neonPulse 2s infinite alternate;
        }
        .clean { border-color: #00ff00; }
        .risky { border-color: #ff0000; }
        
        button {
            display: block;
            margin: 20px auto;
            padding: 12px 40px;
            background: transparent;
            color: #00ff41;
            border: 2px solid #00ff41;
            font-size: 1.4rem;
            cursor: pointer;
        }
        button:hover { background: #00ff41; color: black; }
        
        @keyframes glitch { 0%{text-shadow: 2px 0 #ff00ff;} 100%{text-shadow: -2px 0 #00ff41;} }
        @keyframes neonPulse { from {box-shadow: 0 0 15px;} to {box-shadow: 0 0 35px;} }
    </style>
</head>
<body>

<canvas class="matrix" id="matrix"></canvas>

<div class="container">
    <h1>NEONVOID v2.4</h1>
    <p class="subtitle">/// NETFLIX IP FRAUD SCANNER ///</p>
    
    <div style="text-align:center; font-size:1.5rem; margin:15px 0;">
        INTRUSION FROM: <span style="color:#ffff00;"><?php echo htmlspecialchars($ip); ?></span>
    </div>

    <?php if ($result && isset($result['fraud_score'])): ?>
        <div class="scan-result <?php 
            $clean = ($result['fraud_score'] <= 25 && 
                      ($result['proxy'] ?? false) === false && 
                      ($result['vpn'] ?? false) === false && 
                      ($result['tor'] ?? false) === false && 
                      ($result['recent_abuse'] ?? false) === false);
            echo $clean ? 'clean' : 'risky';
        ?>">
            <div style="font-size:2rem; text-align:center;">
                <?php echo $clean ? '✅ SYSTEM CLEAR - NETFLIX SAFE' : '❌ HIGH RISK - AVOID NETFLIX'; ?>
            </div>
            
            <h2>IP: <?php echo htmlspecialchars($result['ip'] ?? $ip); ?></h2>
            
            <p><strong>FRAUD SCORE:</strong> <?php echo $result['fraud_score']; ?>/100</p>
            <p><strong>PROXY:</strong> <?php echo ($result['proxy'] ?? false) ? '⚠️ DETECTED' : 'CLEAN'; ?></p>
            <p><strong>VPN:</strong> <?php echo ($result['vpn'] ?? false) ? '⚠️ DETECTED' : 'CLEAN'; ?></p>
            <p><strong>TOR:</strong> <?php echo ($result['tor'] ?? false) ? '⚠️ DETECTED' : 'CLEAN'; ?></p>
            <p><strong>RECENT ABUSE:</strong> <?php echo ($result['recent_abuse'] ?? false) ? '⚠️ YES' : 'NO'; ?></p>
            <p><strong>ISP:</strong> <?php echo htmlspecialchars($result['isp'] ?? 'Unknown'); ?></p>
            <p><strong>COUNTRY:</strong> <?php echo htmlspecialchars($result['country_code'] ?? 'Unknown'); ?></p>
        </div>
    <?php elseif ($error): ?>
        <div class="scan-result risky">
            <p>⚠️ <?php echo htmlspecialchars($error); ?></p>
        </div>
    <?php endif; ?>

    <button onclick="location.reload()">RE-SCAN CONNECTION</button>
</div>

<script>
// Matrix Rain Background
const canvas = document.getElementById('matrix');
const ctx = canvas.getContext('2d');

function resize() {
    canvas.height = window.innerHeight;
    canvas.width = window.innerWidth;
}
resize();
window.addEventListener('resize', resize);

const chars = "01ネオンVOIDHACKNETFLIX1337FRAUD";
const fontSize = 16;
const columns = canvas.width / fontSize;
const drops = Array(Math.floor(columns)).fill(1);

function draw() {
    ctx.fillStyle = 'rgba(0, 0, 0, 0.08)';
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    ctx.fillStyle = '#00ff41';
    ctx.font = `${fontSize}px monospace`;

    for (let i = 0; i < drops.length; i++) {
        const text = chars[Math.floor(Math.random() * chars.length)];
        ctx.fillText(text, i * fontSize, drops[i] * fontSize);
        
        if (drops[i] * fontSize > canvas.height && Math.random() > 0.975) {
            drops[i] = 0;
        }
        drops[i]++;
    }
}
setInterval(draw, 35);
</script>

</body>
</html>
