<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_example()
    {
        // Cache::store('database')->put('email_verification_code', '55001', now()->addSecond(50));
        // dump(Cache::store('database')->get('email_verification_code'));
        $this->assertTrue(true);
    }
}
