<?php

namespace korchasa\matched\Tests;

use korchasa\matched\JsonConstraint;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

class JsonConstraintTest extends TestCase
{
    public function testError()
    {
        $constraint = new JsonConstraint('{
            "baz": {
                "value": 1
            },
            "items": [
                { 
                    "a": "b",
                    "c": 2
                },
                "***"  
            ]
        }');

        try {
            $constraint->evaluate('{
                "baz": {
                    "value": 1
                },
                "items": [
                    { 
                        "a": "b2",
                        "c": 22
                    },
                    { 
                        "z": "x",
                        "c": 3
                    }    
                ]
            }');
            $this->fail('Test must fail with missed key items.a');
        } catch (ExpectationFailedException $e) {
            $this->assertEquals(
                trim($e->getMessage()),
                trim(<<<TEXT
Given value of `items.0.a` not match pattern `b`
--- Original
+++ Actual
@@ @@
-'b'
+'b2'
TEXT
                )
            );
        }
    }
}
