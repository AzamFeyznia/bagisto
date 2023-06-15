<?php

namespace Tests\Unit\Sales\Order;

use Webkul\Sales\Models\Invoice;
use Webkul\Sales\Models\Order;
use Webkul\Sales\Models\Refund;
use Webkul\Sales\Services\OrderService;

class OrderServiceCest
{
    public function testCalculateTotals(\UnitTester $I): void
    {
        $order = $I->have(Order::class, ['id' => 1]);
        $invoice = $I->have(Invoice::class, ['order_id' => 1]);
        $refund = $I->have(Refund::class, ['order_id' => 1]);

        $service = new OrderService();
        $service->calculateTotals($order);

        $I->assertEquals($order->sub_total_invoiced, $invoice->sub_total);
        $I->assertEquals($order->base_sub_total_invoiced, $invoice->base_sub_total);
        $I->assertEquals($order->tax_amount_invoiced, $invoice->tax_amount);
        $I->assertEquals($order->base_tax_amount_invoiced, $invoice->base_tax_amount);
        $I->assertEquals($order->grand_total_invoiced, $invoice->sub_total + $invoice->shipping_amount + $invoice->tax_amount - $invoice->discount_amount);
        $I->assertEquals($order->base_grand_total_invoiced, $invoice->base_sub_total + $invoice->base_shipping_amount + $invoice->base_tax_amount - $invoice->base_discount_amount);

        $I->assertEquals($order->sub_total_refunded, $refund->sub_total);
        $I->assertEquals($order->base_sub_total_refunded, $refund->base_sub_total);
        $I->assertEquals($order->tax_amount_refunded, $refund->tax_amount);
        $I->assertEquals($order->base_tax_amount_refunded, $refund->base_tax_amount);
        $I->assertEquals($order->grand_total_refunded, $refund->sub_total + $refund->shipping + $refund->tax_amount - $refund->discount);
        $I->assertEquals($order->base_grand_total_refunded, $refund->base_sub_total + $refund->base_shipping + $refund->base_tax_amount - $refund->base_discount);
    }
}
