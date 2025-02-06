# MysticSwap
#### Manual crypto exchange

# Why this exist?
It's because ___________. This thing suck anyway.

# Features
* Store orders in Json
* Everything is simple
* Price in USD
* Can toggle if the coins is for Buy/Sell of Buy only
* Foolproof
* Very unsecure admin page

# Exchange
* Reddcoin (RDD)
* Duinocoin (DUCO)
* Likecoin (LKE)

(It's manual so just add anything)

## How the price work?
Yes
```
// Prices in USD
const duinoPriceInUSD = 0.000011; // Price of 1 DUCO in USD
const reddcoinPriceInUSD = 0.00008029; // Price of 1 RDD in USD

 const exchangeRates = {
    DUCO: {
        LKE: 3,  // 1 DUC = 3 LIKE
        RDD: duinoPriceInUSD / reddcoinPriceInUSD * 0.8,  // 1 DUC = 0.109 RDD
    },
    LKE: {
        DUC0: 0.8 / 3,  // 1 LIKE = 1/3 DUC
        RDD: (0.6 / 3) * (duinoPriceInUSD / reddcoinPriceInUSD),  // 1 LIKE = (1/3) * 0.109 RDD
    },
    RDD: {
        DUC0: reddcoinPriceInUSD / duinoPriceInUSD,  // 1 RDD = 7.3 DUC
        LKE: (reddcoinPriceInUSD / duinoPriceInUSD) * 3,  // 1 RDD = 7.3 * 3 LIKE
    },
};

```


# Notice

This thing is very basic and unsecure, anyone can access admin page and mess up the orders stuff
