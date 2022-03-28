<?php

namespace App\Http\Livewire;
use App\Models\Article;
use Livewire\Component;

class ArticleForm extends Component
{
    public Article $article;

    protected $rules = [
        'article.title' => ['required','min:4'],
        'article.content' => ['required'],
    ];
    
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
    public function save(){
         
        $this->validate();
        
        $this->article->save();
        // Article::create($data);
        //$this->reset(); //reseteo todas los propiedades del componente
        session()->flash('status',__('Artículo guardado') );

        $this->redirectRoute('articles.index'); //redirijo al listado de articulo

    }
}
