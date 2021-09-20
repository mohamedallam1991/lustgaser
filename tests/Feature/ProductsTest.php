<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Validation\Rules\DatabaseRule;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_a_product_as_a_resource_object()
    {
        $product = Product::factory()->create();
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->getJson('/api/v1/products/1')
             ->assertStatus(200)
             ->assertJson([
                "data" => [
                    "id" => '1',
                    "type" => "products",
                    "attributes" => [
                        'title' => $product->title,
                        'created_at' => $product->created_at->toJSON(),
                        'updated_at' => $product->updated_at->toJSON(),
                    ]
                ]
        ]);
    }

    /** @test */
    public function it_returns_all_products_as_a_collection_of_resource_objects()
    {
        $product = Product::factory()->create();
        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->get('/api/v1/products');
        $response->assertStatus(200);

        // // dd($response->decodeResponseJson()['data'][0]['data']);

        // $this->assertEquals('products',$response->decodeResponseJson()['data'][0]['data']['type']);
        // $this->assertEquals($product->title,$response->decodeResponseJson()['data'][0]['data']['attributes']['title']);
        // $this->assertEquals($product1->title,$response->decodeResponseJson()['data'][1]['data']['attributes']['title']);
        // $this->assertEquals($product2->title,$response->decodeResponseJson()['data'][2]['data']['attributes']['title']);

        $response
        ->assertJson(fn (AssertableJson $json) =>
            $json->has('data')
                 ->has('data', 3)
                 ->has('data.0.data', fn ($json) =>
                    $json->where('id', '1')
                        ->where('type', 'products')
                         ->where('attributes.title', $product->title)
                         ->where('attributes.created_at', $product->created_at->toJSON())
                         ->where('attributes.updated_at', $product->updated_at->toJSON())
                 )
                 ->has('data.1.data', fn ($json) =>
                 $json->where('id', '2')
                      ->where('type', 'products')
                      ->where('attributes.title', $product1->title)
                      ->where('attributes.created_at', $product1->created_at->toJSON())
                      ->where('attributes.updated_at', $product1->updated_at->toJSON())
              )
                 ->has('data.2.data', fn ($json) =>
                 $json->where('id', '3')
                      ->where('type', 'products')
                      ->where('attributes.title', $product2->title)
                      ->where('attributes.created_at', $product2->created_at->toJSON())
                      ->where('attributes.updated_at', $product2->updated_at->toJSON())
              )
        );

        $products = Product::all();
        $response
        ->assertJson(fn (AssertableJson $json) =>
            $json->has('data')
                 ->has('data', 3)
                 ->has('data.0.data', fn ($json) =>
                    $json->where('id', '1')
                        ->where('type', 'products')
                         ->where('attributes.title', $products[0]->title)
                         ->where('attributes.created_at', $products[0]->created_at->toJSON())
                         ->where('attributes.updated_at', $products[0]->updated_at->toJSON())
                 )
                 ->has('data.1.data', fn ($json) =>
                 $json->where('id', '2')
                      ->where('type', 'products')
                      ->where('attributes.title', $products[1]->title)
                      ->where('attributes.created_at', $products[1]->created_at->toJSON())
                      ->where('attributes.updated_at', $products[1]->updated_at->toJSON())
              )
                 ->has('data.2.data', fn ($json) =>
                 $json->where('id', '3')
                      ->where('type', 'products')
                      ->where('attributes.title', $products[2]->title)
                      ->where('attributes.created_at', $products[2]->created_at->toJSON())
                      ->where('attributes.updated_at', $products[2]->updated_at->toJSON())
              )
        );

    }

    /** @test */
    public function it_can_create_a_product_from_a_resource_object()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/products', [
            'data' => [
                'type' => 'products',
                'attributes' => [
                    'title' => 'John Doe',
                ]
            ]
        ]);

        $response->assertStatus(201)

             ->assertJson([
                "data" => [
                    "id" => '1',
                    "type" => "products",
                    "attributes" => [
                        'title' => 'John Doe',
                        'created_at' => now()->setMilliseconds(0)->toJSON(),
                        'updated_at' => now() ->setMilliseconds(0)->toJSON(),
                    ]
                ]
            ])
            ->assertHeader('Location', url('/api/v1/products/1'));


        $this->assertDatabaseHas('products', [
            'id' => 1,
            'title' => 'John Doe'
        ]);
    }


    /** @test */
    public function it_can_update_an_author_from_a_resource_object()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $product = Product::factory()->create();

        $response = $this->patchJson('/api/v1/products/1', [
            'data' => [
                'id' => '1',
                'type' => 'products',
                'attributes' => [
                    'title' => 'Jane Doe',
                ]
            ]
        ]);

        $response
        ->assertStatus(200)
        ->assertJson([
            "data" => [
                "id" => '1',
                "type" => "products",
                "attributes" => [
                    'title' => 'Jane Doe',
                    'created_at' => now()->setMilliseconds(0)->toJSON(),
                    'updated_at' => now() ->setMilliseconds(0)->toJSON(),
                ]
            ]
        ]);

        $this->assertDatabaseHas('products', [
            'id' => 1,
            'title' => 'Jane Doe',
        ]);
    }

}