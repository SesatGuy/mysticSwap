<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MsyticSwap</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <h1>MsyticSwap</h1>
    <div class="exchange-box">
        <h2></h2>

        <label for="from_coin">From:</label>
        <select id="from_coin" onchange="validateCoinSelection()">
            <option value="DUCO">DuinoCoin (DUCO)</option>
            <option value="LKE">LikeCoin (LKE)</option>
            <option value="RDD">ReddCoin (RDD)</option>
        </select>

        <label for="to_coin">To:</label>
        <select id="to_coin" onchange="validateCoinSelection()">
            <option value="RDD">ReddCoin (RDD)</option>
            <option value="LKE">LikeCoin (LKE) (Buy Only)</option>
            <option value="DUCO">DuinoCoin (DUCO) (Buy Only)</option>
        </select>

        <label for="amount">Amount:</label>
        <input type="number" id="amount" placeholder="Enter amount" min="0.01" required>

        <p><strong>Receive:</strong> <span id="conversion_result">0</span> <span id="to_symbol"></span></p>
        <p><strong>Rate:</strong> <span id="rate">Loading...</span></p>

        <button onclick="openOrderPopup()">Place Order</button>
    </div>

    <!-- Order Popup -->
    <div id="orderPopup" class="popup-box">
        <div class="popup-content">
            <h2>Confirm Order</h2>
            <p><strong>From:</strong> <span id="popup_from"></span></p>
            <p><strong>To:</strong> <span id="popup_to"></span></p>
            <p><strong>Amount:</strong> <span id="popup_amount"></span></p>
            <p><strong>Receive:</strong> <span id="popup_receive"></span></p>

            <label for="wallet_address">Your Wallet Address:</label>
            <input type="text" id="wallet_address" placeholder="Enter your wallet address">
            
            <label for="gmail_address">Your Gmail Address: (For Contact status)</label>
            <input type="text" id="gmail_address" placeholder="Enter your gmail address">

            <button onclick="submitOrder()">Confirm</button>
            <button onclick="closePopup()">Cancel</button>
        </div>
    </div>

    <!-- FAQ Box -->
    <div class="faq-box">
        <h2>Frequently Asked Questions (FAQ)</h2>
        
        <div class="faq-item">
            <h3>Why this website is a demo</h3>
            <p>Due to lack of liquidity, this website will be act as demo for testing development but it will work with buy only.</p>
        </div>

        <div class="faq-item">
            <h3>What is MysticSwap?</h3>
            <p>MysticSwap is a platform that allows you to exchange different cryptocurrencies with ease and convenience.</p>
        </div>

        <div class="faq-item">
            <h3>How do I place an order?</h3>
            <p>To place an order, select the coin you want to swap, enter the amount, and click 'Place Order'. A confirmation popup will appear where you can enter your wallet details.</p>
        </div>

        <div class="faq-item">
            <h3>Is there a minimum amount I can exchange?</h3>
            <p>Yes, the minimum exchange amount is 0.01 of the selected coin.</p>
        </div>

        <div class="faq-item">
            <h3>How can I contact support?</h3>
            <p>There won't be any support because you won't dare to send a lot of coins to random exchange that using subdomain.</p>
        </div>
        
        <div class="faq-item">
            <h3>Notice</h3>
            <p>This thing is suck, so if you wanna to try it out, make sure to put correct address so i could transfer to you, the website won't check for it.</p>
        </div>
    </div>

    <script src="js/script.js"></script>
</body>
</html>
