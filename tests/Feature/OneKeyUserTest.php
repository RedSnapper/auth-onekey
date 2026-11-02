<?php

namespace RedSnapper\OneKey\Tests\Feature;

use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;
use RedSnapper\OneKey\OneKeyUser;

class OneKeyUserTest extends TestCase
{
    #[Test]
    public function can_get_specialty_for_user()
    {
        $user = new OneKeyUser([
            'specialite1' => 'Cardiology',
        ]);

        $this->assertEquals(['Cardiology'], $user->getSpecialties());
    }

    #[Test]
    public function can_get_multiple_specialties_for_user()
    {
        $user = new OneKeyUser([
            'specialite1' => 'Cardiology',
            'specialite2' => 'Oncology',
            'specialite3' => 'Neurology',
        ]);

        $this->assertEquals(['Cardiology', 'Oncology', 'Neurology'], $user->getSpecialties());
    }

    #[Test]
    public function specialty_keys_with_empty_values_are_ignored()
    {
        $user = new OneKeyUser([
            'specialite1' => 'Cardiology',
            'specialite2' => '',
            'specialite3' => 'Neurology',
        ]);

        $this->assertEquals(['Cardiology', 'Neurology'], $user->getSpecialties());
    }
}