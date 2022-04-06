<?php

namespace App\Http\Livewire;

use Livewire\Component;

class ArticlesTable extends Component
{
    /*
    public $articles;

    public function mount(){
        $this->articles = \App\Models\Article::all();
    }
    */

    public $search = '';

    public function render()
    {
        return view('livewire.articles-table',[
            'articles' => \App\Models\Article::where('title', 'like', "%{$this->search}%")->latest()->get()
        ])->layout('layouts.app'); // ->layout('layouts.app'), se puede quitar, porque eso usa livewire por defecto
    }
}
