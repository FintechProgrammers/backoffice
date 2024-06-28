<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    @include('partials._styles')
</head>

<body>
    <div class="container payment-container">
        <div class="row no-gutters">
            <div class="col-md-6">
                <div class="payment-header">
                    <h4>Demo Store</h4>
                    <p>Order #123456</p>
                    <p>$10,000.00 (USD)</p>
                    <p>Demo Invoice Title</p>
                </div>
                <div class="payment-body">
                    <h5>Select Payment Currency</h5>
                    <div class="currency-list">
                        <button class="btn btn-outline-primary">BTC Bitcoin <span class="float-right">0.513351 BTC</span></button>
                        <button class="btn btn-outline-primary">ETH Ethereum <span class="float-right">7.498781 ETH</span></button>
                        <button class="btn btn-outline-primary">USDT <span class="float-right">10000.00 USDT</span></button>
                        <button class="btn btn-outline-primary">USDC USD Coin <span class="float-right">10000.00 USDC</span></button>
                    </div>
                    <button class="btn btn-primary btn-block">Next</button>
                </div>
            </div>
            <div class="col-md-6">
                <div class="payment-header">
                    <h4>Demo Store</h4>
                    <p>Order #123456</p>
                    <p>$1,000.00 (USD)</p>
                    <p>MackBook</p>
                </div>
                <div class="payment-body qr-code">
                    <p><strong>Please check the amount and address properly.</strong></p>
                    <img src="https://via.placeholder.com/150" alt="QR Code">
                    <p><strong>0.051385 BTC</strong></p>
                    <p>Address: bc1q...nk3</p>
                    <p>InvoiceID: RDg7dEFaREtD...RLwz</p>
                    <p>1 BTC = 19460.98 USD</p>
                </div>
            </div>
        </div>
    </div>

</body>

</html>
