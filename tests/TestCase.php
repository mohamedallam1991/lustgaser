<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Mockery\Undefined;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function mustBe($response, $field, $option)
    {
        if ( 'required' === $option) {
              $yes = 'The '.$field .' field is required.';
        }elseif ('string' === $option) {
             $yes = 'The '.$field .' must be a string.';
        }elseif ('invalid' === $option) {
            $yes = 'The selected '.$field .' is invalid.';
        }elseif ('array' === $option){
            $yes = 'The '.$field .' must be an array.';
        }else {
            return $this->fail('The given ' . $option .' isnt a validation rule');
        }
        return $this->assertEquals($yes,$response->decodeResponseJson()['errors'][$field][0], $option . ' isnt the right validation error, failed for another validation rule');
    }
}
