// Prices in USD
const duinoPriceInUSD = 0.000011; // Price of 1 DUCO in USD
const reddcoinPriceInUSD = 0.00008029; // Price of 1 RDD in USD

// Liquidity (True = can buy and sell, False = buy only)
const liquidity = {
    DUCO: false,  
    LKE: false, 
    RDD: true,  
};

//exchange rates
const exchangeRates = {
    DUCO: {
        LKE: 3,  // 1 DUC = 3 LIKE
        RDD: duinoPriceInUSD / reddcoinPriceInUSD * 0.8,  // 1 DUC = 0.1379 RDD
    },
    LKE: {
        DUC0: 0.8 / 3,  // 1 LIKE = 1/3 DUC
        RDD: (0.6 / 3) * (duinoPriceInUSD / reddcoinPriceInUSD),  // 1 LIKE = (1/3) * 0.1379 RDD
    },
    RDD: {
        DUC0: reddcoinPriceInUSD / duinoPriceInUSD,  // 1 RDD = 7.3 DUC
        LKE: (reddcoinPriceInUSD / duinoPriceInUSD) * 3,  // 1 RDD = 7.3 * 3 LIKE
    },
};

function updateConversion() {
    const fromCoin = document.getElementById("from_coin").value;
    const toCoin = document.getElementById("to_coin").value;
    const amount = parseFloat(document.getElementById("amount").value);

    if (amount > 0) {
        const rate = exchangeRates[fromCoin][toCoin];
        const conversion = (rate * amount).toFixed(2);
        document.getElementById("conversion_result").innerText = conversion;
        document.getElementById("to_symbol").innerText = toCoin;
        document.getElementById("rate").innerText = `${rate} ${toCoin} per 1 ${fromCoin}`;
    } else {
        document.getElementById("conversion_result").innerText = "0";
        document.getElementById("rate").innerText = "Invalid amount";
    }
}

function checkLiquidity(toCoin) {
    return liquidity[toCoin];
}

function openOrderPopup() {
    const fromCoin = document.getElementById("from_coin").value;
    const toCoin = document.getElementById("to_coin").value;
    const amount = document.getElementById("amount").value;

    if (!checkLiquidity(toCoin)) {
        alert(`Sorry, you can't buy ${toCoin}. It is a buy-only coin.`);
        return;
    }

    if (fromCoin === toCoin) {
        alert("You cannot select the same coin for both 'From' and 'To'.");
        return;
    }

    const conversion = calculateConversion(fromCoin, toCoin, amount);

    // Populate popup with trade details
    document.getElementById("popup_from").innerText = fromCoin;
    document.getElementById("popup_to").innerText = toCoin;
    document.getElementById("popup_amount").innerText = amount;
    document.getElementById("popup_receive").innerText = conversion;

    document.getElementById("orderPopup").style.display = "block";
}

function closePopup() {
    document.getElementById("orderPopup").style.display = "none";
}

function calculateConversion(fromCoin, toCoin, amount) {
    return (exchangeRates[fromCoin][toCoin] * amount).toFixed(2);
}

function submitOrder() {
    const fromCoin = document.getElementById("from_coin").value;
    const toCoin = document.getElementById("to_coin").value;
    const amount = document.getElementById("amount").value;
    const receive = document.getElementById("conversion_result").innerText;
    const walletAddress = document.getElementById("wallet_address").value;
    const gmailAddress = document.getElementById("gmail_address").value;

    if (!walletAddress || !gmailAddress) {
        alert("Please fill out both your wallet address and Gmail address.");
        return;
    }

    const orderData = {
        from: fromCoin,
        to: toCoin,
        amount: amount,
        receive: receive,
        wallet: walletAddress,
        gmail: gmailAddress
    };

    fetch('/process_order.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(orderData)
    })
    .then(response => response.text())
    .then(data => {
        alert(data);

        closePopup();
    })
    .catch(error => {
        console.error('Error:', error);
        alert("There was an issue with your order. Please try again.");
    });
}

document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("from_coin").addEventListener("change", updateConversion);
    document.getElementById("to_coin").addEventListener("change", updateConversion);
    document.getElementById("amount").addEventListener("input", updateConversion);

    updateConversion();

    validateCoinSelection();
});

function validateCoinSelection() {
    const fromCoin = document.getElementById("from_coin").value;
    const toCoin = document.getElementById("to_coin").value;
    const toOptions = document.getElementById("to_coin").getElementsByTagName("option");

    for (let option of toOptions) {
        if (!liquidity[option.value]) {
            option.disabled = true;
        } else {
            option.disabled = false;
        }
    }

    for (let option of toOptions) {
        if (option.value === fromCoin) {
            option.disabled = true;
        }
    }
}
