<?php

namespace App\Helpers;

use App\Models\OrderDetails;
use Cart;

class OrderHelper
{
    public static function saveOrderDetails($order)
    {
        foreach (Cart::instance('shopping')->content() as $cart) {
            $detail = new OrderDetails();
            $detail->order_id = $order->id;
            $detail->product_id = $cart->id;
            $detail->product_name = $cart->name;
            $detail->purchase_price = $cart->options->purchase_price ?? null;
            $detail->sale_price = $cart->price;
            $detail->qty = $cart->qty;

            // 🟢 এই তিনটা গুরুত্বপূর্ণ লাইন
            $detail->product_color = $cart->options->product_color ?? null;
            $detail->product_size = $cart->options->product_size ?? null;
            $detail->variant_price_id = $cart->options->variant_price_id ?? null;

            $detail->save();
        }

        // ✅ সব অর্ডার হয়ে গেলে কার্ট খালি করে দাও
        Cart::instance('shopping')->destroy();
    }
}
