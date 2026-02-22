<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\User;
use App\Enums\PaymentStatus;
use Illuminate\Support\Facades\DB;

class OrderPaymentService
{
    /**
     * Repay seller's debts for sold products
     *
     * Processes each product in the sale and distributes the payment amount
     * across existing orders for the same seller and product (FIFO - oldest first).
     *
     * The amount deducted from debt = total_price_after_tax - seller_commission
     * Example: If sale amount is 250 and seller commission is 10%, then 225 is deducted from debt
     *
     * @param int $sellerId The seller/user ID
     * @param array $soldItems Array of sold products with quantities and amounts
     *                         Expected format: [['product_id' => 1, 'amount' => 100.50, 'quantity' => 5], ...]
     * @return void
     */
    public function repaySellerDebts(int $sellerId, array $soldItems): void
    {
        // 1. Get seller to calculate commission
        $seller = User::find($sellerId);
        if (!$seller) {
            return;
        }

        $commissionPercentage = $seller->commission_percentage ?? 0;

        // 2. Process each sold product separately (each product has its own account)
        foreach ($soldItems as $item) {
            // Calculate the actual amount that goes to the company (after deducting seller's commission)
            $totalAmount = $item['amount'];
            $commissionAmount = round($totalAmount * $commissionPercentage / 100, 2);
            $companyAmount = $totalAmount - $commissionAmount;

            $this->repayProductDebt($sellerId, $item['product_id'], $companyAmount);
        }
    }

    /**
     * Repay debt for a specific product
     *
     * @param int $sellerId The seller/user ID
     * @param int $productId The product ID
     * @param float $paymentAmount The amount to pay (after commission deduction)
     * @return void
     */
    private function repayProductDebt(int $sellerId, int $productId, float $paymentAmount): void
    {
        // 1. Get all orders for this seller with this product that have remaining debt
        // Ordered by creation date (oldest first - FIFO)
        $ordersWithDebt = Order::where('user_id', $sellerId)
            ->where('remaining_amount', '>', 0)
            ->whereHas('orderProducts', function ($query) use ($productId) {
                $query->where('product_id', $productId);
            })
            ->oldest('created_at')
            ->get();

        // 2. Distribute payment across orders (FIFO)
        $remainingPayment = $paymentAmount;

        foreach ($ordersWithDebt as $order) {
            if ($remainingPayment <= 0) {
                break;
            }

            // Calculate how much to deduct from this order
            $deductAmount = min($order->remaining_amount, $remainingPayment);

            // 3. Update the order
            $order->update([
                'paid_amount' => $order->paid_amount + $deductAmount,
                'remaining_amount' => $order->remaining_amount - $deductAmount,
            ]);

            // 4. Check if order is fully paid, update payment status
            if ($order->remaining_amount <= 0) {
                $order->update([
                    'payment_status' => PaymentStatus::PAID->value,
                    'remaining_amount' => 0, // Ensure no negative amounts
                ]);
            }

            // 5. Reduce remaining payment
            $remainingPayment -= $deductAmount;
        }
    }
}
