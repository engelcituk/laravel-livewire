<?php

namespace Tests\Feature\Livewire;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use Livewire\Livewire;
use App\Models\Article;

class ArticleFormTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function article_forms_render_properly(){

        $this->get( route('articles.create') )->assertSeeLivewire('article-form');

        $article = Article::factory()->create();

        $this->get( route('articles.create', $article) )->assertSeeLivewire('article-form');

    }
    /** @test */
    public function blade_template_is_wired_properly(){
        Livewire::test('article-form')
        ->assertSeeHtml('wire:submit.prevent="save"')
        ->assertSeeHtml('wire:model="article.title"')
        ->assertSeeHtml('wire:model="article.content"');
    }
    /** @test */
    public function can_create_new_articles(){
        Livewire::test('article-form')
        ->set('article.title','New article')
        ->set('article.content','Article content')
        ->call('save')
        ->assertSessionHas('status')
        ->assertRedirect( route('articles.index') )
        ;

        $this->assertDatabaseHas('articles',[
            'title' => 'New article',
            'content' => 'Article content'
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

        Livewire::test('article-form',['article' => $article])
        ->assertSet('article.title', $article->title)
        ->assertSet('article.content', $article->content)
        ->set('article.title','Title updated')
        ->call('save')
        ->assertSessionHas('status')
        ->assertRedirect( route('articles.index') )
        ;

        $this->assertDatabaseCount('articles', 1 );

        $this->assertDatabaseHas('articles',[
            'title' => 'Title updated',
        ]);
    }
    /**
     * php artisan test --filter real_time_validation_works_for_content
     */


}
