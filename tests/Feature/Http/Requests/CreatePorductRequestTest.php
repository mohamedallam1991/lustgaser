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
        ]);

        $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['data.attributes.title']);


        $this->assertDatabaseMissing('products', [
            'id' => 1,
            'title' => 'John Doe'
        ]);
    }





}
