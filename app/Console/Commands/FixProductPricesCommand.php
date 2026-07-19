<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class FixProductPricesCommand extends Command
{
    protected $signature = 'salomat:fix-prices';

    protected $description = 'Normalize product prices to realistic somoni values';

    public function handle(): int
    {
        $updated = 0;

        Product::query()->each(function (Product $product) use (&$updated) {
            $price = (float) $product->price;
            $oldPrice = $product->old_price !== null ? (float) $product->old_price : null;

            if ($price > 1000) {
                $price = round($price / 1000, 2);
            }

            if ($oldPrice !== null) {
                if ($oldPrice > 1000) {
                    $oldPrice = round($oldPrice / 1000, 2);
                }

                if ($oldPrice <= $price || (($oldPrice - $price) / $oldPrice) < 0.05) {
                    $oldPrice = null;
                }

                if ($oldPrice !== null && $oldPrice > $price * 1.2) {
                    $oldPrice = round($price * 1.12, 2);
                }
            }

            if ((float) $product->price !== $price || (float) $product->old_price !== (float) $oldPrice) {
                $product->update([
                    'price' => $price,
                    'old_price' => $oldPrice,
                ]);
                $updated++;
            }
        });

        $this->info("Updated {$updated} products.");

        return self::SUCCESS;
    }
}
