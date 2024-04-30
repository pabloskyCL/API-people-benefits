<?php

namespace Tests\Unit;

use Tests\TestCase;

class BenefitControllerTest extends TestCase
{
    public function testGetFormatedBenefitsSuccess()
    {
        $response = $this->get('/yearBenefits');

        $response->assertOk();

        $this->assertArrayHasKey('data', $response);

        $this->assertCount(5, $response['data']);

        foreach ($response['data'] as $value) {
            $this->assertArrayHasKey('year', $value);
            $this->assertArrayHasKey('num', $value);
            $this->assertArrayHasKey('beneficios', $value);
            foreach ($value['beneficios'] as $benefit) {
                $this->assertArrayHasKey('ano', $benefit);
                $this->assertArrayHasKey('ficha', $benefit);
            }
        }
    }
}
