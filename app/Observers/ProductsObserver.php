<?php

namespace App\Observers;

use App\Models\Products;
use Illuminate\Support\Facades\Storage;

class ProductsObserver
{
    /**
     * Handle the Products "created" event.
     *
     * @param  \App\Models\Products  $products
     * @return void
     */
    public function created(Products $products)
    {
        //
    }

    /**
     * Handle the Products "updated" event.
     *
     * @param  \App\Models\Products  $products
     * @return void
     */
    public function updated(Products $products)
    {
        //
    }

    /**
     * Handle the Products "deleted" event.
     *
     * @param  \App\Models\Products  $products
     * @return void
     */
    public function deleted(Products $products)
    {
        
    }

    /**
     * Handle the Products "restored" event.
     *
     * @param  \App\Models\Products  $products
     * @return void
     */
    public function restored(Products $product)
    {
        $location = strstr($product->image,'products');
        if ($location == 'products/images/default.jpg') {
            return ;
        }
        Storage::disk('public')->delete($location);
    }

    /**
     * Handle the Products "force deleted" event.
     *
     * @param  \App\Models\Products  $products
     * @return void
     */
    public function forceDeleted(Products $products)
    {
        //
    }
}
