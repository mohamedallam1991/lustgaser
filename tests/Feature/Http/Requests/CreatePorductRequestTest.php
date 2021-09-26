<?php

namespace Tests\Feature\Http\Requests;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreatePorductRequestTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        Sanctum::actingAs(User::factory()->create());
    }

    public function sendPatchWithHeaders($arrayOfData)
    {
        return $this->postJson(
            '/api/v1/products', $arrayOfData,[
                'accept' => 'application/vnd.api+json',
                'content-type' => 'application/vnd.api+json',
            ]);
    }

      /** @test */
      public function it_can_create_a_product_from_a_resource_object()
      {
        $response = $this->sendPatchWithHeaders(
              [
              'data' => [
                  'type' => 'products',
                  'attributes' => [
                      'title' => 'John Doe',
                  ]
                  ],

              ]
          );


          $this->assertTrue(Product::whereTitle('John Doe')->exists());

          $response->assertStatus(201)
          ->assertHeader('Location', url('/api/v1/products/1'))

          ->assertJson(fn (AssertableJson $json) =>
              $json->has('data',
                      fn ($json) =>
                      $json->where('id', '1')
                          ->where('type', 'products')
                          ->has('attributes',
                                  fn ($json) =>
                              $json->where('title', 'John Doe')
                                  ->where('created_at', now()->setMilliseconds(0)->toJSON())
                                  ->where('updated_at', now()->setMilliseconds(0)->toJSON())
                          )
              )
          )
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
              ]);


      }

    /** @test */
    public function it_validates_that_the_type_is_required()
    {
        $response = $this->sendPatchWithHeaders(
            [
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
        $this->mustBe($response, 'data.type', 'required' );

        $this->assertFalse(Product::whereTitle('John Doe')->exists());
    }

    /** @test */
    public function it_does_require_products_is_valid(): void
    {
        $response = $this->sendPatchWithHeaders(
            [
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
        $this->mustBe($response, 'data.type', 'invalid' );


        $this->assertFalse(Product::whereTitle('John Doe')->exists());
    }

    /** @test */
    public function it_valides_that_the_attributes_is_required(): void
    {
        $response = $this->sendPatchWithHeaders(
            [
            'data' => [
                'type' => 'products',
            ]
        ]);

        $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['data.attributes']);
        $this->mustBe($response, 'data.attributes', 'required' );

        $this->assertFalse(Product::whereTitle('John Doe')->exists());
    }

    /** @test */
    public function it_valides_that_the_attributes_is_array(): void
    {

        $response = $this->sendPatchWithHeaders(
            [
            'data' => [
                'type' => 'products',
                'attributes' => 'not an object',
            ]
        ]);

        $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['data.attributes']);

        $this->mustBe($response, 'data.attributes', 'array' );

        $this->assertFalse(Product::whereTitle('John Doe')->exists());
    }

    /** @test */
    public function it_valides_that_the_title_is_required(): void
    {
        $response = $this->sendPatchWithHeaders(
            [
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
        $this->mustBe($response, 'data.attributes.title', 'required' );

        $this->assertFalse(Product::whereTitle('John Doe')->exists());
    }

    /** @test */
    public function it_valides_that_the_title_is_string(): void
    {
        $response = $this->sendPatchWithHeaders(
            [
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

        // dd($response->getOriginalContent());
        $response->assertJson(fn (AssertableJson $json) =>
            $json->where('message', 'The given data was invalid.')
                ->has('errors',
                    fn (AssertableJson $json) =>
                        $json->where('data.attributes.title', [0 => 'The data.attributes.title must be a string.'])
                        ->etc()
                    )
        );

        $response->assertJson([
            "message" => "The given data was invalid.",
            "errors" => [
            "data.attributes.title" => [
                "The data.attributes.title must be a string.",
                ],
            ],
        ]);

        $response->assertJson(
            fn (AssertableJson $json) =>
            $json->where('message', 'The given data was invalid.')
                 ->has('errors', 1)
                );

        $this->assertStringContainsString('must be a string',$response->decodeResponseJson()['errors']['data.attributes.title'][0]);
        $this->mustBe($response, 'data.attributes.title', 'string' );

        $this->assertFalse(Product::whereTitle('John Doe')->exists());
    }



}
