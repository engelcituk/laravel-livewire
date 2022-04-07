<?php

namespace Tests\Feature\Livewire;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use App\Models\Article;
use App\Models\User;
use Livewire\Livewire;

class ArticleDeleteModalTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_delete_articles(){

        Storage::fake();
        $imagePath = UploadedFile::fake()->image('post-image.png')->store('/', 'public');

        $article = Article::factory()->create([
            'image' => $imagePath
        ]);

        $user = User::factory()->create();

        Livewire::actingAs($user)->test('article-delete-modal', ['article' => $article])
        ->call('delete')
        ->assertSessionHas('flash.bannerStyle','danger')
        ->assertSessionHas('flash.banner')
        ->assertRedirect( route('articles.index') )
        ;

        Storage::disk('public')->assertMissing($imagePath);

        $this->assertDatabaseCount('articles', 0);
        
    }
}
