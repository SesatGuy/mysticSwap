document.addEventListener("DOMContentLoaded", function () {
    fetchAndUpdateTable();
});

// Fetch and update the table when the page loads
function fetchAndUpdateTable() {
    fetch("json/orders.json")
        .then(response => response.json())
        .then(data => {
            calculateTodayVolume(data);  // Process the orders and calculate daily volume
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

    // Get the current time and the start of today (midnight 00:00:00)
    const currentTime = new Date();  // Get the current date and time
    const startOfDay = new Date(currentTime);  // Make a copy of current date
    startOfDay.setHours(0, 0, 0, 0);  // Set to midnight today

    console.log('Current Time:', currentTime);  // Debug: Check current time
    console.log('Start of Today:', startOfDay);  // Debug: Check start of today

    // Initialize the daily volume for each coin
    let dailyVolume = {
        DUCO: { total: 0, usd: 0 },
        RDD: { total: 0, usd: 0 },
        LKE: { total: 0, usd: 0 }
    };

    // Loop through the orders and calculate volume for completed orders today
    orders.forEach(order => {
        // Only process completed orders
        if (order.status !== "completed") return;

        // Parse the order creation time
        const orderTime = new Date(order.created_at);
        console.log('Order Created At:', order.created_at);  // Debug: Check the order's creation time
        console.log('Parsed Order Time:', orderTime);  // Debug: Check the parsed order time

        // Check if the order was created today (from midnight today to now)
        if (orderTime >= startOfDay && orderTime <= currentTime) {
            let toCoin = order.to;  // Coin received (e.g., RDD)
            let amount = parseFloat(order.receive); // The amount received

            // Only process if the amount is a valid number and the coin exists in the volumeData
            if (!isNaN(amount) && coinPrices[toCoin]) {
                dailyVolume[toCoin].total += amount;
                dailyVolume[toCoin].usd += amount * coinPrices[toCoin];
            }
        }
    });

    console.log('Daily Volume:', dailyVolume);  // Debug: Check the daily volume object

    populateTable(dailyVolume);  // Populate the table after calculating the daily volume
}

// Function to populate the table with the daily volume data for today
function populateTable(dailyVolume) {
    let tbody = document.querySelector("#volumeTable tbody");
    tbody.innerHTML = "";  // Clear existing rows before adding new data

    // Ensure all coins are shown, even if their volume is zero
    const allCoins = ['DUCO', 'RDD', 'LKE'];

    allCoins.forEach(coin => {
        let row = `<tr>
            <td>${coin}</td>
            <td>${dailyVolume[coin].total.toFixed(2)}</td>
            <td>$${dailyVolume[coin].usd.toFixed(4)}</td>
        </tr>`;
        tbody.innerHTML += row;
    });
}
