<?php

namespace App\Http\Livewire;
use App\Models\Article;
use Livewire\Component;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class ArticleForm extends Component
{
    public Article $article;


    public function rules(){
        return [
            'article.title' => ['required','min:4'],
            'article.slug' => [
                'required',
                'alpha_dash',
                Rule::unique('articles', 'slug')->ignore($this->article)
            ],
                //'unique:articles,slug,'.$this->article->id],// unico en la tabla articles, el campo slug, se ignora al editar
            'article.content' => ['required'],
        ];
    }
    
    /*
    protected $messages = [
        'title.required' => 'El :attribute es obligatorio',
    ];

    protected $validationAttributes = [
        'title' => 'título',
    ];*/

    public function mount(Article $article)
    {
        $this->article = $article;
    }

    public function render()
    {
        return view('livewire.article-form');
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function updatedArticleTitle($title)
    {
        $this->article->slug = Str::slug($title);
    }

    public function save(){
         
        $this->validate();

        Auth::user()->articles()->save($this->article);
        // $this->article->user_id = auth()->id;
        // $this->article->save();
        // Article::create($data);
        //$this->reset(); //reseteo todas los propiedades del componente
        session()->flash('status',__('Artículo guardado') );

        $this->redirectRoute('articles.index'); //redirijo al listado de articulo

    }
}
