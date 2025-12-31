<?php 
function generateEsewaSignature($total, $transaction_uuid, $product_code, $secret_key){
    return base64_encode(hash_hmac("sha256", "total_amount=$total,transaction_uuid=$transaction_uuid,product_code=$product_code", $secret_key, true));
}
// echo base64_encode(hash_hmac("sha256", "total_amount=110,transaction_uuid=241028,product_code=EPAYTEST", "8gBm/:&EnhH.1/q", true));