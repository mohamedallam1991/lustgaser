<?php

namespace Tests\Feature\Http\Requests;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateProductRequestTest extends TestCase
{
    use RefreshDatabase;


    /** @test */
    public function it_validates_product_id_is_required()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $product = Product::factory()->create();

        $response = $this->patchJson('/api/v1/products/1', [
            'data' => [
                'type' => 'products',
                'attributes' => [
                    'title' => 'Jane Doe',
                ]
            ]
        ]);

        $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['data.id']);

        $this->assertDatabaseHas('products', [
            'id' => 1,
            'title' => $product->title,
        ]);
    }

    /** @test */
    public function it_validates_product_id_is_string()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $product = Product::factory()->create();

        $response = $this->patchJson('/api/v1/products/1', [
            'data' => [
                'id' => 1,
                'type' => 'products',
                'attributes' => [
                    'title' => 'Jane Doe',
                ]
            ]
        ]);

        $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['data.id']);

        $this->assertDatabaseHas('products', [
            'id' => 1,
            'title' => $product->title,
        ]);
    }

    /** @test */
    public function it_validates_product_type_is_given()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $product = Product::factory()->create();

        $response = $this->patchJson('/api/v1/products/1', [
            'data' => [
                'id' => '1',
                'type' => '',
                'attributes' => [
                    'title' => 'Jane Doe',
                ]
            ]
        ]);

        $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['data.type']);

        $this->assertDatabaseHas('products', [
            'id' => 1,
            'title' => $product->title,
        ]);
    }

    /** @test */
    public function it_validates_products_type_is_given_in_plural()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $product = Product::factory()->create();

        $response = $this->patchJson('/api/v1/products/1', [
            'data' => [
                'id' => '1',
                'type' => 'product',
                'attributes' => [
                    'title' => 'Jane Doe',
                ]
            ]
        ]);

        $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['data.type']);

        $this->assertDatabaseHas('products', [
            'id' => 1,
            'title' => $product->title,
        ]);
    }

    /** @test */
    public function it_validates_attributes_are_given_in_product()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $product = Product::factory()->create();

        $response = $this->patchJson('/api/v1/products/1', [
            'data' => [
                'id' => '1',
                'type' => 'products',

            ]
        ]);

        $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['data.attributes']);

        $this->assertDatabaseHas('products', [
            'id' => 1,
            'title' => $product->title,
        ]);
    }

    /** @test */
    public function it_validates_attributes_is_given_in_product()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $product = Product::factory()->create();

        $response = $this->patchJson('/api/v1/products/1', [
            'data' => [
                'id' => '1',
                'type' => 'products',
                'attributes' => 'not object'

            ]
        ]);

        $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['data.attributes']);

        $this->assertDatabaseHas('products', [
            'id' => 1,
            'title' => $product->title,
        ]);
    }

    /** @test */
    public function it_validates_title_is_string()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $product = Product::factory()->create();

        $response = $this->patchJson('/api/v1/products/1', [
            'data' => [
                'id' => '1',
                'type' => 'products',
                'attributes' => [
                    'title' => 123,
                ]
            ]
        ]);

        $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['data.attributes.title']);

        $this->assertDatabaseHas('products', [
            'id' => 1,
            'title' => $product->title,
        ]);
    }

}