<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;


class ArticlesTable extends Component
{
    /*
    public $articles;

    public function mount(){
        $this->articles = \App\Models\Article::all();
    }
    */
    use WithPagination;

    public $search = '';

    public function render()
    {
        return view('livewire.articles-table',[
            'articles' => \App\Models\Article::where('title', 'like', "%{$this->search}%")->latest()->paginate(5)
        ])->layout('layouts.app'); // ->layout('layouts.app'), se puede quitar, porque eso usa livewire por defecto
    }
}
