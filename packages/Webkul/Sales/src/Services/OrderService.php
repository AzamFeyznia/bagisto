<?php

namespace Webkul\Sales\Services;

use Illuminate\Support\Collection;
use Webkul\Sales\Contracts\Order;

class OrderService
{
    public function calculateTotals(Order $order): void
    {
        $order->load(['invoices', 'refunds']);

        $this->calculateInvoiceTotals($order);
        $this->calculateRefundTotals($order);
    }

    private function calculateInvoiceTotals(Order $order): void
    {
        $totals = $this->getTotalFields()->mapWithKeys(function ($orderField, $invoiceField) use($order) {
            return [$orderField . '_invoiced' => $order->invoices->sum($invoiceField)];
        });

        $order->fill($totals->toArray());
        $this->calculateInvoiceGrandTotal($order);
        $this->calculateInvoiceBaseGrandTotal($order);
    }

    private function calculateInvoiceGrandTotal(Order $order): void
    {
        $order->grand_total_invoiced = $order->sub_total_invoiced
            + $order->shipping_invoiced
            + $order->tax_amount_invoiced
            - $order->discount_invoiced;
    }

    private function calculateInvoiceBaseGrandTotal(Order $order): void
    {
        $order->base_grand_total_invoiced = $order->base_sub_total_invoiced
            + $order->base_shipping_invoiced
            + $order->base_tax_amount_invoiced
            - $order->base_discount_invoiced;
    }

    private function calculateRefundTotals(Order $order): void
    {
        $totals = $this->getTotalFields()->mapWithKeys(function ($orderField, $refundField) use($order) {
            return [$orderField . '_refunded' => $order->refunds->sum($refundField)];
        });

        $order->fill($totals->toArray());
        $this->calculateRefundGrandTotal($order);
        $this->calculateRefundBaseGrandTotal($order);
    }

    private function calculateRefundGrandTotal(Order $order): void
    {
        $order->grand_total_refunded = $order->sub_total_refunded
            + $order->shipping_refunded
            + $order->tax_amount_refunded
            - $order->discount_refunded;
    }

    private function calculateRefundBaseGrandTotal(Order $order): void
    {
        $order->base_grand_total_refunded = $order->base_sub_total_refunded
            + $order->base_shipping_refunded
            + $order->base_tax_amount_refunded
            - $order->base_discount_refunded;
    }

    private function getTotalFields(): Collection
    {
        return collect([
            'sub_total' => 'sub_total',
            'base_sub_total' => 'base_sub_total',
            'shipping_amount' => 'shipping',
            'base_shipping_amount' => 'base_shipping',
            'tax_amount' => 'tax_amount',
            'base_tax_amount' => 'base_tax_amount',
            'discount_amount' => 'discount',
            'base_discount_amount' => 'base_discount'
        ]);
    }
}
