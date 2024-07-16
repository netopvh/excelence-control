<?php

namespace App\Jobs;

use App\Models\Product;
use Automattic\WooCommerce\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ImportProductsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $woocommerce;

    /**
     * Create a new job instance.
     */
    public function __construct(Client $woocommerce)
    {
        $this->woocommerce = $woocommerce;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $page = 1;
            $perPage = 100;
            $allProducts = [];

            do {
                $response = $this->woocommerce->get('products', [
                    'per_page' => $perPage,
                    'page' => $page
                ]);

                $products = json_decode(json_encode($response), true);
                $allProducts = array_merge($allProducts, $products);
                $page++;
            } while (count($products) == $perPage);

            foreach ($allProducts as $product) {
                Product::updateOrCreate(['sku' => $product['sku']], [
                    'name' => $product['name'],
                ]);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }

    private function checkProductExists($product)
    {
    }
}
