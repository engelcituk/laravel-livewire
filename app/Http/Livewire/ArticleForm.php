<?php

namespace App\Http\Livewire;
use App\Models\Article;
use Livewire\Component;

class ArticleForm extends Component
{
    public $title;
    public $content;

    protected $rules = [
        'title' => ['required','min:4'],
        'content' => ['required'],
    ];
    
    /*
    protected $messages = [
        'title.required' => 'El :attribute es obligatorio',
    ];

    protected $validationAttributes = [
        'title' => 'título',
    ];*/

    public function render()
    {
        return view('livewire.article-form');
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }
    public function save(){
        $data = $this->validate();

        Article::create($data);
        //$this->reset(); //reseteo todas los propiedades del componente
        session()->flash('status',__('Artículo creado') );

        $this->redirectRoute('articles.index'); //redirijo al listado de articulo

    }
}
