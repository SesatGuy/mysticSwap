document.addEventListener("DOMContentLoaded", function () {
    fetchAndUpdateTable();
});


function fetchAndUpdateTable() {
    fetch(`json/orders.json?t=${new Date().getTime()}`) 
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log("Fetched Data:", data);
            calculateTodayVolume(data);
        })
        .catch(error => {
            console.error("Error loading orders:", error);
        });
}

function calculateTodayVolume(orders) {
    const coinPrices = {
        DUCO: 0.000011,
        RDD: 0.000074,
        LKE: 0.000011 / 3
    };


    const currentTime = new Date();
    const startOfDay = new Date();
    startOfDay.setHours(0, 0, 0, 0);  

    console.log('Current Time:', currentTime.toLocaleString());  
    console.log('Start of Today (Local):', startOfDay.toLocaleString());  

    let dailyVolume = {
        DUCO: { total: 0, usd: 0 },
        RDD: { total: 0, usd: 0 },
        LKE: { total: 0, usd: 0 }
    };


    orders.forEach(order => {
        if (order.status !== "completed") return;  

        const orderTime = new Date(order.created_at + " UTC");  

        console.log(`Order Created At (Raw): ${order.created_at}, Parsed (UTC): ${orderTime.toLocaleString()}`);  // Debug

        if (isNaN(orderTime.getTime())) {
            console.warn("Skipping order with invalid date:", order);
            return;
        }

        if (orderTime >= startOfDay && orderTime <= currentTime) {
            let toCoin = order.to.toUpperCase();
            let amount = parseFloat(order.receive);

            console.log(`Parsed Amount for ${toCoin}: ${amount}`);

            if (!isNaN(amount) && coinPrices[toCoin]) {
                dailyVolume[toCoin].total += amount;
                dailyVolume[toCoin].usd += amount * coinPrices[toCoin];
            }
        }
    });

    console.log('Daily Volume:', dailyVolume);  

    populateTable(dailyVolume);
}


function populateTable(dailyVolume) {
    let tbody = document.querySelector("#volumeTable tbody");
    tbody.innerHTML = ""

    const allCoins = ['DUCO', 'RDD', 'LKE'];

    allCoins.forEach(coin => {
        let row = tbody.insertRow();
        row.insertCell(0).textContent = coin;
        row.insertCell(1).textContent = dailyVolume[coin].total.toFixed(2);
        row.insertCell(2).textContent = `$${dailyVolume[coin].usd.toFixed(4)}`;
    });
}
