<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Sajya\Server\Testing\ProceduralRequests;
use Tests\TestCase;

class PingPongTest extends TestCase
{
    use ProceduralRequests;

    /**
     * A basic RPC test example.
     *
     * @return void
     */
    public function testPingPong()
    {
        $this
            ->setRpcRoute('rpc.api')
            ->callProcedure('ApiProcedure@setData')
            ->assertJsonFragment([
                'result' => 'pong',
            ]);
    }
}
