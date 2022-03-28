<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Article;

class ArticleShow extends Component
{
    public Article $article; //tipe properties, con esto no es necesario hacer el metodo mount

    /*
    public function mount(Article $article)
    {
        $this->article = $article;
    }*/

    public function render()
    {
        return view('livewire.article-show');
    }
}
