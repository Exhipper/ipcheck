<?php
$IPQS_KEY = 'D4ePykpazLxnbGiRVZ5VfeOtaUcP9SRO';

$ip = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? '';

// Auto check on every visit
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
    <title>NEONVOID v2.3 // IP FRAUD SCANNER</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=VT323&display=swap');

        body {
            margin: 0;
            padding: 0;
            background: #0a0a0a;
            color: #00ff41;
            font-family: 'VT323', monospace;
            overflow: hidden;
            height: 100vh;
        }

        .matrix { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; opacity: 0.18; }

        .container {
            position: relative;
            z-index: 2;
            max-width: 820px;
            margin: 30px auto;
            padding: 35px;
            background: rgba(5, 15, 5, 0.92);
            border: 2px solid #00ff41;
            box-shadow: 0 0 40px #00ff41, inset 0 0 30px rgba(0, 255, 65, 0.2);
            border-radius: 8px;
        }

        h1 {
            font-size: 3rem;
            text-align: center;
            margin: 0 0 10px 0;
            text-shadow: 0 0 20px #00ff41, 0 0 40px #ff0000;
            animation: glitch 0.8s infinite alternate;
        }

        .subtitle { text-align: center; color: #ff3333; font-size: 1.4rem; margin-bottom: 20px; }

        .current-ip { text-align: center; font-size: 1.5rem; margin: 15px 0; color: #ffff00; }

        .scan-result {
            margin-top: 25px;
            padding: 25px;
            border: 2px solid;
            background: rgba(0, 20, 0, 0.9);
            animation: neonPulse 2s infinite alternate;
        }

        .clean { border-color: #00ff00; box-shadow: 0 0 25px #00ff00; }
        .risky { border-color: #ff0000; box-shadow: 0 0 25px #ff0000; }

        .data { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 12px; font-size: 1.35rem; }

        button {
            margin: 20px auto;
            display: block;
            padding: 14px 50px;
            background: transparent;
            color: #00ff41;
            border: 3px solid #00ff41;
            font-size: 1.5rem;
            cursor: pointer;
            transition: all 0.4s;
        }
        button:hover {
            background: #00ff41;
            color: #000;
            box-shadow: 0 0 30px #00ff41;
        }

        .history {
            margin-top: 40px;
            border-top: 1px dashed #00ff41;
            padding-top: 20px;
        }

        .history-item {
            background: rgba(0, 30, 0, 0.6);
            padding: 12px;
            margin: 8px 0;
            border-left: 4px solid #ff00ff;
        }

        @keyframes glitch {
            0% { text-shadow: 3px 0 #ff00ff, -3px 0 #00ff41; }
            100% { text-shadow: -3px 0 #ff00ff, 3px 0 #00ff41; }
        }

        @keyframes neonPulse {
            from { box-shadow: 0 0 15px currentColor; }
            to { box-shadow: 0 0 35px currentColor; }
        }
    </style>
</head>
<body>

    <canvas class="matrix" id="matrix"></canvas>

    <div class="container">
        <h1>NEONVOID v2.3</h1>
        <p class="subtitle">/// NETFLIX IP FRAUD SCANNER ///</p>

        <div class="current-ip">
            INTRUSION FROM: <span id="current-ip-display"><?php echo htmlspecialchars($ip); ?></span>
        </div>

        <?php if ($result): ?>
            <div class="scan-result <?php 
                $clean = ($result['fraud_score'] <= 25 && !$result['proxy'] && !$result['vpn'] && !$result['tor'] && !$result['recent_abuse']);
                echo $clean ? 'clean' : 'risky';
            ?>">
                <div style="font-size: 2.2rem; text-align: center; margin: 15px 0;">
                    <?php echo $clean ? '✅ SYSTEM CLEAR - NETFLIX SAFE' : '❌ COMPROMISED - HIGH RISK'; ?>
                </div>

                <h2>IP: <?php echo htmlspecialchars($result['ip'] ?? $ip); ?></h2>

                <div class="data">
                    <div><strong>FRAUD SCORE:</strong> <?php echo $result['fraud_score'] ?? 'N/A'; ?>/100</div>
                    <div><strong>PROXY:</strong> <?php echo $result['proxy'] ? '⚠️ DETECTED' : 'CLEAN'; ?></div>
                    <div><strong>VPN:</strong> <?php echo $result['vpn'] ? '⚠️ DETECTED' : 'CLEAN'; ?></div>
                    <div><strong>TOR:</strong> <?php echo $result['tor'] ? '⚠️ DETECTED' : 'CLEAN'; ?></div>
                    <div><strong>RECENT ABUSE:</strong> <?php echo $result['recent_abuse'] ? '⚠️ YES' : 'NO'; ?></div>
                    <div><strong>ISP:</strong> <?php echo htmlspecialchars($result['isp'] ?? 'Unknown'); ?></div>
                    <div><strong>COUNTRY:</strong> <?php echo htmlspecialchars($result['country_code'] ?? 'Unknown'); ?></div>
                </div>
            </div>
        <?php endif; ?>

        <button onclick="location.reload()">RE-SCAN CONNECTION</button>

        <!-- History -->
        <div class="history">
            <h2 style="color:#ff00ff;">◉ RECENT SCANS</h2>
            <div id="history-list"></div>
        </div>
    </div>

    <script>
        // Matrix Rain
        const canvas = document.getElementById('matrix');
        const ctx = canvas.getContext('2d');
        canvas.height = window.innerHeight;
        canvas.width = window.innerWidth;

        const chars = "01ネオンVOIDFRAUDHACKNETFLIX1337";
        const fontSize = 16;
        const columns = canvas.width / fontSize;
        const drops = Array(Math.floor(columns)).fill(1);

        function drawMatrix() {
            ctx.fillStyle = 'rgba(0, 0, 0, 0.07)';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            ctx.fillStyle = '#00ff41';
            ctx.font = `${fontSize}px monospace`;

            for (let i = 0; i < drops.length; i++) {
                const text = chars[Math.floor(Math.random() * chars.length)];
                ctx.fillText(text, i * fontSize, drops[i] * fontSize);
                if (drops[i] * fontSize > canvas.height && Math.random() > 0.975) drops[i] = 0;
                drops[i]++;
            }
        }
        setInterval(drawMatrix, 40);

        // Sound Effects
        function playScanSound(isClean) {
            const audio = new AudioContext();
            const oscillator = audio.createOscillator();
            const gain = audio.createGain();

            oscillator.type = 'sawtooth';
            oscillator.frequency.setValueAtTime(isClean ? 800 : 200, audio.currentTime);
            gain.gain.value = 0.3;

            oscillator.connect(gain);
            gain.connect(audio.destination);

            oscillator.start();
            setTimeout(() => {
                oscillator.stop();
            }, isClean ? 400 : 800);
        }

        // Save to History
        function saveToHistory(ip, result) {
            let history = JSON.parse(localStorage.getItem('ipHistory') || '[]');
            history.unshift({
                ip: ip,
                fraud: result?.fraud_score ?? 'N/A',
                clean: (result?.fraud_score <= 25 && !result?.proxy && !result?.vpn && !result?.tor),
                time: new Date().toLocaleTimeString()
            });
            if (history.length > 8) history.pop();
            localStorage.setItem('ipHistory', JSON.stringify(history));
            renderHistory();
        }

        function renderHistory() {
            const history = JSON.parse(localStorage.getItem('ipHistory') || '[]');
            const list = document.getElementById('history-list');
            list.innerHTML = history.map(item => `
                <div class="history-item">
                    <strong>${item.ip}</strong> — ${item.time}<br>
                    Fraud: ${item.fraud} | ${item.clean ? '✅ CLEAN' : '❌ RISKY'}
                </div>
            `).join('');
        }

        // Auto play sound + save history when result is available
        <?php if ($result): ?>
            window.onload = () => {
                const isClean = <?php echo ($result['fraud_score'] <= 25 && !$result['proxy'] && !$result['vpn'] && !$result['tor'] && !$result['recent_abuse']) ? 'true' : 'false'; ?>;
                playScanSound(isClean);
                saveToHistory("<?php echo $ip; ?>", <?php echo json_encode($result); ?>);
                renderHistory();
            };
        <?php endif; ?>

        window.addEventListener('resize', () => {
            canvas.height = window.innerHeight;
            canvas.width = window.innerWidth;
        });
    </script>
</body>
</html>
