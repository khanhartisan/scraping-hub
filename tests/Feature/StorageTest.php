<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class StorageTest extends TestCase
{
    public function test_storage()
    {
        $text = Str::uuid();
        $filePath = 'test.txt';
        if (Storage::exists($filePath)) {
            $this->assertTrue(Storage::delete($filePath));
        }

        $this->assertTrue(Storage::put($filePath, $text));
        $this->assertEquals($text, Storage::get($filePath));
    }
}
