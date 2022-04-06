<?php

namespace Tests\Feature\Livewire;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

use Livewire\Livewire;
use App\Models\Article;
use App\Models\User;
use App\Models\Category;


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

        $this->actingAs($user)->get( route('articles.create') )
            ->assertSeeLivewire('article-form')
            ->assertDontSeeText(__('Delete'))
        ;

        $article = Article::factory()->create();

        $this->actingAs($user)->get( route('articles.edit', $article) )
            ->assertSeeLivewire('article-form')
            ->assertSeeText(__('Delete'))
            ;

    }
    
    /** @test */
    public function blade_template_is_wired_properly(){
        Livewire::test('article-form')
        ->assertSeeHtml('wire:submit.prevent="save"')
        ->assertSeeHtml('wire:model="article.title"')
        ->assertSeeHtml('wire:model="article.slug"')
        ;
    }

    /** @test */
    public function can_create_new_articles(){
    
        Storage::fake('public');
        $image = UploadedFile::fake()->image('post-image.png');

        $user = User::factory()->create();
        $category = Category::factory()->create();

    
        Livewire::actingAs($user)->test('article-form')
        ->set('image', $image)
        ->set('article.title','New article')
        ->set('article.slug','new-article')
        ->set('article.content','Article content')
        ->set('article.category_id',$category->id)
        ->call('save')
        ->assertSessionHas('status')
        ->assertRedirect( route('articles.index') )
        ;


        $this->assertDatabaseHas('articles',[
            'title' => 'New article',
            'image' => $imagePath = Storage::disk('public')->files()[0],
            'slug' => 'new-article',
            'content' => 'Article content',
            'user_id' => $user->id,
            'category_id' => $category->id,

        ]);

        Storage::disk('public')->assertExists($imagePath);
    }

    /** @test */
    public function can_delete_articles(){

        Storage::fake();
        $imagePath = UploadedFile::fake()->image('post-image.png')->store('/', 'public');

        $article = Article::factory()->create([
            'image' => $imagePath
        ]);

        $user = User::factory()->create();

        Livewire::actingAs($user)->test('article-form', ['article' => $article])
        ->call('delete')
        ->assertSessionHas('status')
        ->assertRedirect( route('articles.index') )
        ;

        Storage::disk('public')->assertMissing($imagePath);

        $this->assertDatabaseCount('articles', 0);
        
    }

    /** @test */
    public function can_create_new_category(){
        
        Livewire::test('article-form')
        ->call('openCategoryForm')
        ->set('newCategory.name','New category')
        ->assertSet('newCategory.slug','new-category')
        ->call('saveNewCategory')
        ->assertSet('article.category_id', Category::first()->id)
        ->assertSet('showCategoryModal', false)
        ;

        $this->assertDatabaseCount('categories', 1);
    }
    
    /** @test */
    public function new_category_name_is_required(){
        Livewire::test('article-form')
        ->call('openCategoryForm')
        ->set('newCategory.slug','new-category')
        ->call('saveNewCategory')
        ->assertHasErrors(['newCategory.name'=> 'required'])
        ->assertSeeHtml(__('validation.required',['attribute' => 'name']))
        ;
    }

    /** @test */
    public function new_category_slug_is_required(){
        Livewire::test('article-form')
        ->call('openCategoryForm')
        ->set('newCategory.name','New category')
        ->set('newCategory.slug', null)
        ->call('saveNewCategory')
        ->assertHasErrors(['newCategory.slug'=> 'required'])
        ->assertSeeHtml(__('validation.required',['attribute' => 'slug']))
        ;
    }

    /** @test */
    public function new_category_name_must_be_unique(){

        $category = Category::factory()->create();

        Livewire::test('article-form')
        ->call('openCategoryForm')
        ->set('newCategory.name',$category->name)
        ->set('newCategory.slug', 'new-category')
        ->call('saveNewCategory')
        ->assertHasErrors(['newCategory.name'=> 'unique'])
        ->assertSeeHtml(__('validation.unique',['attribute' => 'name']));
        ;
    }

    /** @test */
    public function new_category_slug_must_be_unique(){

        $category = Category::factory()->create();

        Livewire::test('article-form')
        ->call('openCategoryForm')
        ->set('newCategory.name','new-category')
        ->set('newCategory.slug', $category->slug)
        ->call('saveNewCategory')
        ->assertHasErrors(['newCategory.slug'=> 'unique'])
        ->assertSeeHtml(__('validation.unique',['attribute' => 'slug']));
        ;
    }

    /** @test */
    public function can_update_newCategorys(){

        $article = Article::factory()->create();
        $user = User::factory()->create();

        Livewire::actingAs($user)->test('article-form',['article' => $article])
        ->assertSet('article.image', $article->image)
        ->assertSet('article.slug', $article->slug)
        ->assertSet('article.content', $article->content)
        ->assertSet('article.category_id', $article->category->id)
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
    public function can_update_articles_image(){

        Storage::fake('public');

        $oldImage = UploadedFile::fake()->image('old-image.png');
        $oldImagePath = $oldImage->store('/','public');

        $newImage = UploadedFile::fake()->image('new-image.png');

        $article = Article::factory()->create([
            'image' => $oldImagePath
        ]);

        $user = User::factory()->create();

        Livewire::actingAs($user)->test('article-form',['article' => $article])
        ->set('image', $newImage)
        ->call('save')
        ->assertSessionHas('status')
        ->assertRedirect( route('articles.index') )
        ;

        Storage::disk('public')->assertExists($article->fresh()->image); //fresh vuelve a consultar la información para tener la img actualizada
        Storage::disk('public')->assertMissing($oldImagePath);
    }

    /** @test */
    function can_update_articles(){

        $article = Article::factory()->create();

        $user = User::factory()->create();

        Livewire::actingAs($user)->test('article-form', ['article' => $article])
            ->assertSet('article.title', $article->title)
            ->assertSet('article.slug', $article->slug)
            ->assertSet('article.content', $article->content)
            ->assertSet('article.category_id', $article->category->id)
            ->set('article.title', 'Updated title')
            ->set('article.slug', 'updated-slug')
            ->call('save')
            ->assertSessionHas('status')
            ->assertRedirect(route('articles.index'));

        $this->assertDatabaseCount('articles', 1);

        $this->assertDatabaseHas('articles', [
            'title' => 'Updated title',
            'slug' => 'updated-slug',
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
    public function image_is_required(){
        Livewire::test('article-form')
        ->set('article.title','Article title')
        ->set('article.content','Article content')
        ->call('save')
        ->assertHasErrors(['image'=> 'required'])
        ->assertSeeHtml(__('validation.required',['attribute' => 'image']));
        ;
    }

     /** @test */
     public function image_field_must_be_of_type_image(){
        Livewire::test('article-form')
        ->set('image','string-not-allowed')
        ->call('save')
        ->assertHasErrors(['image'=> 'image'])
        ->assertSeeHtml(__('validation.image',['attribute' => 'image']));
        
    }

    /** @test */
    public function image_must_be_2mb_max(){
        Storage::fake('public');
        $image = UploadedFile::fake()->image('post-image.png')->size(3000);


        Livewire::test('article-form')
        ->set('image',$image)
        ->call('save')
        ->assertHasErrors(['image'=> 'max'])
        ->assertSeeHtml(__('validation.max.file',[
            'attribute' => 'image',
            'max' => '2048'
        ]));
        
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
    public function category_is_required(){
        Livewire::test('article-form')
        ->set('article.title','New article')
        ->set('article.slug', 'new-article')
        ->set('article.content','Article content')
        ->set('article.category_id', null)
        ->call('save')
        ->assertHasErrors(['article.category_id'=> 'required'])
        ->assertSeeHtml(__('validation.required',['attribute' => 'category id'])); //al parecer hay que separara category_id
        ;
    }

    /** @test */
    public function category_must_be_exist_in_database(){
        Livewire::test('article-form')
        ->set('article.title','New article')
        ->set('article.slug', 'new-article')
        ->set('article.content','Article content')
        ->set('article.category_id', 1)
        ->call('save')
        ->assertHasErrors(['article.category_id'=> 'exists'])
        ->assertSeeHtml(__('validation.exists',['attribute' => 'category id'])); //al parecer hay que separara category_id
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
     public function slug_is_generated_automatically(){
        Livewire::test('article-form')
        ->set('article.title', 'Nuevo articulo')
        ->assertSet('article.slug','nuevo-articulo')
        ;

     }
    /**
     * php artisan test 
     * php artisan test --filter real_time_validation_works_for_content
     * php artisan test --stop-on-failure
     */


}
