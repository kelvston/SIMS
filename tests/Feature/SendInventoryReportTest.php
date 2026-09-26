<?php

namespace Tests\Feature;

use App\Mail\InventoryReportMail;
use App\Models\Cashew;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendInventoryReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_daily_report_contains_sales_receipts_and_profit(): void
    {
        Mail::fake();
        config(['reports.inventory_recipient' => 'kelvinstony9@gmail.com']);
        Carbon::setTestNow(Carbon::parse('2026-09-26 00:05:00'));

        $product = Product::create(['name' => 'Raw Cashew Nuts']);
        $user = User::factory()->create();
        $sale = Sale::create([
            'customer_name' => 'Customer',
            'total_amount' => 40000,
            'discount_amount' => 2000,
            'final_amount' => 38000,
            'amount_paid' => 38000,
            'amount_due' => 0,
            'sale_date' => Carbon::parse('2026-09-25 10:00:00'),
        ]);
        SaleItem::create([
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'unit_price' => 20000,
            'unit_cost' => 10000,
            'quantity' => 2,
        ]);
        Cashew::create([
            'product_id' => $product->id,
            'status' => 'available',
            'received_at' => Carbon::parse('2026-09-25 08:00:00'),
            'quantity' => 5,
            'unit' => 'kg',
            'unit_price' => 10000,
            'selling_price' => 20000,
        ]);
        Expense::create([
            'description' => 'Transport',
            'amount' => 3000,
            'category' => 'Transport',
            'expense_date' => Carbon::parse('2026-09-25'),
            'user_id' => $user->id,
        ]);

        $this->artisan('reports:send-inventory daily')
            ->expectsOutput('Daily inventory report sent to kelvinstony9@gmail.com.')
            ->assertExitCode(0);

        Mail::assertSent(InventoryReportMail::class);

        $mail = Mail::sent(InventoryReportMail::class)->first();

        $this->assertTrue($mail->hasTo('kelvinstony9@gmail.com'));
        $this->assertSame(2, $mail->report['sold_quantity']);
        $this->assertSame(5, $mail->report['received_quantity']);
        $this->assertSame(38000.0, $mail->report['revenue']);
        $this->assertSame(18000.0, $mail->report['gross_profit']);
        $this->assertSame(15000.0, $mail->report['net_profit']);
    }
}
