<?php

namespace Tests\Feature\Livewire;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class ArticlesTableTest extends TestCase
{
    /** @test */
    public function articles_component_renders_properly(){
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get( route('articles.index') )
            ->assertSeeLivewire('articles-table');
    }
}
