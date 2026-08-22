<?php

namespace App\Services;

use App\Models\CouponUsage;
use App\Models\Order;
use App\Models\Wallet;

class OrderWalletAndCouponFinalizer
{
    /**
     * بعد إتمام الطلب (موافقة أدمن أو دفع أونلاين): خصم رصيد محفظة الطالب وتسجيل استخدام الكوبون.
     *
     * @throws \Throwable
     */
    public static function run(Order $order): void
    {
        $credit = (float) ($order->wallet_credit_amount ?? 0);
        if ($credit > 0) {
            $wallet = Wallet::where('user_id', $order->user_id)->lockForUpdate()->first();
            if (! $wallet) {
                throw new \RuntimeException('لا توجد محفظة للطالب لخصم الرصيد.');
            }
            if ((float) $wallet->balance + 0.00001 < $credit) {
                throw new \RuntimeException('رصيد محفظة الطالب غير كافٍ لإتمام الطلب (مطلوب خصم '.number_format($credit, 2).' $).');
            }
            $wallet->withdraw(
                $credit,
                'خصم لشراء كورس — طلب #'.$order->id
            );
        }

        if ($order->coupon_id && (float) ($order->discount_amount ?? 0) > 0) {
            $exists = CouponUsage::where('order_id', $order->id)->exists();
            if (! $exists) {
                $coupon = \App\Models\Coupon::whereKey($order->coupon_id)->lockForUpdate()->first();
                if ($coupon) {
                    CouponUsage::create([
                        'coupon_id' => $coupon->id,
                        'user_id' => $order->user_id,
                        'order_id' => $order->id,
                        'discount_amount' => $order->discount_amount,
                        'order_amount' => $order->original_amount ?? $order->amount,
                        'final_amount' => $order->amount,
                    ]);
                    $coupon->incrementUsage();
                }
            }
        }
    }
}
