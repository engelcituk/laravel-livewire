<?php

namespace Tests\Feature\Livewire;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use Livewire\Livewire;
use App\Models\Article;
use App\Models\User;


class ArticleFormTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_can_not_create_or_update_articles(){

        $this->get( route('articles.create') )
            ->assertRedirect('login');

        $article = Article::factory()->create();

        $this->get( route('articles.edit', $article) )
            ->assertRedirect('login');

    }

    /** @test */
    public function article_forms_render_properly(){

        $user = User::factory()->create();

        $this->actingAs($user)->get( route('articles.create') )->assertSeeLivewire('article-form');

        $article = Article::factory()->create();

        $this->actingAs($user)->get( route('articles.edit', $article) )->assertSeeLivewire('article-form');

    }
    /** @test */
    public function blade_template_is_wired_properly(){
        Livewire::test('article-form')
        ->assertSeeHtml('wire:submit.prevent="save"')
        ->assertSeeHtml('wire:model="article.title"')
        ->assertSeeHtml('wire:model="article.slug"')
        ->assertSeeHtml('wire:model="article.content"');
    }
    /** @test */
    public function can_create_new_articles(){
    
        $user = User::factory()->create();
    
        Livewire::actingAs($user)->test('article-form')
        ->set('article.title','New article')
        ->set('article.slug','new-article')
        ->set('article.content','Article content')
        ->call('save')
        ->assertSessionHas('status')
        ->assertRedirect( route('articles.index') )
        ;

        $this->assertDatabaseHas('articles',[
            'title' => 'New article',
            'slug' => 'new-article',
            'content' => 'Article content',
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function title_is_required(){
        Livewire::test('article-form')
        ->set('article.content','Article content')
        ->call('save')
        ->assertHasErrors(['article.title'=> 'required'])
        ->assertSeeHtml(__('validation.required',['attribute' => 'title']));
        ;
    }

    /** @test */
    public function slug_is_required(){
        Livewire::test('article-form')
        ->set('article.title','New article')
        ->set('article.slug',null)
        ->set('article.content','Article content')
        ->call('save')
        ->assertHasErrors(['article.slug'=> 'required'])
        ->assertSeeHtml(__('validation.required',['attribute' => 'slug']));
        ;
    }

    /** @test */
    public function slug_must_be_unique(){

        $article = Article::factory()->create();

        Livewire::test('article-form')
        ->set('article.title','New article')
        ->set('article.slug', $article->slug)
        ->set('article.content','Article content')
        ->call('save')
        ->assertHasErrors(['article.slug'=> 'unique'])
        ->assertSeeHtml(__('validation.unique',['attribute' => 'slug']));
        ;
    }

    /** @test */
    public function slug_must_only_contains_letters_numbers_dashes_and_underscores(){

        $article = Article::factory()->create();

        Livewire::test('article-form')
        ->set('article.title','New article')
        ->set('article.slug', 'new-article$%^')
        ->set('article.content','Article content')
        ->call('save')
        ->assertHasErrors(['article.slug'=> 'alpha_dash'])
        ->assertSeeHtml(__('validation.alpha_dash',['attribute' => 'slug']));
        ;
    }

     /** @test */
     public function unique_rule_should_be_ignored_when_updating_the_same_slug(){

        $article = Article::factory()->create();
        $user = User::factory()->create();

        Livewire::actingAs($user)->test('article-form', ['article' => $article])
        ->set('article.title','New article')
        ->set('article.slug', $article->slug)
        ->set('article.content','Article content')
        ->call('save')
        ->assertHasNoErrors(['article.slug'=> 'unique'])
        ;
    }

    /** @test */
    public function title_must_be_4_characters(){
        Livewire::test('article-form')
        ->set('article.title','Art')
        ->set('article.content','Article content')
        ->call('save')
        ->assertHasErrors(['article.title'=> 'min'])
        ->assertSeeHtml(__('validation.min.string',[
            'attribute' => 'title',
            'min' => 4
        ]))
        ;
    }

    /** @test */
    public function content_is_required(){
        Livewire::test('article-form')
        ->set('article.title','New Article')
        ->call('save')
        ->assertHasErrors(['article.content'=> 'required'])
        ->assertSeeHtml(__('validation.required',['attribute' => 'content']));

        ;
    }

    /** @test */
    public function real_time_validation_works_for_title(){
        Livewire::test('article-form')
        ->set('article.title','')
        ->assertHasErrors(['article.title' => 'required'])
        ->set('article.title','New')
        ->assertHasErrors(['article.title' => 'min'])
        ->set('article.title','New article')
        ->assertHasNoErrors('article.title')
        ;
    }

    /** @test */
    public function real_time_validation_works_for_content(){
        Livewire::test('article-form')
        ->set('article.content','')
        ->assertHasErrors(['article.content' => 'required'])
        ->set('article.content','Article content')
        ->assertHasNoErrors('article.content')
        ;
    }

     /** @test */
    public function can_update_articles(){

        $article = Article::factory()->create();

        $user = User::factory()->create();

        Livewire::actingAs($user)->test('article-form',['article' => $article])
        ->assertSet('article.title', $article->title)
        ->assertSet('article.slug', $article->slug)
        ->assertSet('article.content', $article->content)
        ->set('article.title','Title updated')
        ->set('article.slug','updated-slug')
        ->call('save')
        ->assertSessionHas('status')
        ->assertRedirect( route('articles.index') )
        ;

        $this->assertDatabaseCount('articles', 1 );

        $this->assertDatabaseHas('articles',[
            'title' => 'Title updated',
            'slug' => 'updated-slug',
            'user_id' => $user->id,
        ]);
    }

     /** @test */
     public function slug_is_generated_automatically(){
        Livewire::test('article-form')
        ->set('article.title', 'Nuevo articulo')
        ->assertSet('article.slug','nuevo-articulo')
        ;

     }
    /**
     * php artisan test --filter real_time_validation_works_for_content
     */


}
