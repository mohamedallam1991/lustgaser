<?php

namespace Tests\Feature\Http\Requests;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreatePorductRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_validates_that_the_type_is_given_when_creating_a_product()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        // $product = Product::factory()->create();

        $response = $this->postJson('/api/v1/products', [
            'data' => [
                'type' => '',
                'attributes' => [
                    'title' => 'John Doe',
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ]);

        $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['data.type']);


        $this->assertDatabaseMissing('products', [
            'id' => 1,
            'title' => 'John Doe'
        ]);
    }

    /** @test */
    public function it_does_require_products_is_plural(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/products', [
            'data' => [
                'type' => 'product',
                'attributes' => [
                    'title' => 'John Doe',
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ]);

        $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['data.type']);


        $this->assertDatabaseMissing('products', [
            'id' => 1,
            'title' => 'John Doe'
        ]);
    }

    /** @test */
    public function it_valides_that_the_attributes_is_required(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/products', [
            'data' => [
                'type' => 'products',
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ]);

        $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['data.attributes']);


        $this->assertDatabaseMissing('products', [
            'id' => 1,
            'title' => 'John Doe'
        ]);
    }

    public function it_valides_that_the_attributes_is_object(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/products', [
            'data' => [
                'type' => 'products',
                'attributes' => 'not an object',
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ]);

        $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['data.attributes']);


        $this->assertDatabaseMissing('products', [
            'id' => 1,
            'title' => 'John Doe'
        ]);
    }

    /** @test */
    public function it_valides_that_the_title_is_required(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/products', [
            'data' => [
                'type' => 'products',
                'attributes' => [
                    'title' => '',
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ]);

        $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['data.attributes.title']);


        $this->assertDatabaseMissing('products', [
            'id' => 1,
            'title' => 'John Doe'
        ]);
    }

    /** @test */
    public function it_valides_that_the_title_is_string(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/products', [
            'data' => [
                'type' => 'products',
                'attributes' => [
                    'title' => 12,
                ]
            ]
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ]);

        $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['data.attributes.title']);


        $this->assertDatabaseMissing('products', [
            'id' => 1,
            'title' => 'John Doe'
        ]);
    }


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
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
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
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
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
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
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
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
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
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
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
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
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
        ], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
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
