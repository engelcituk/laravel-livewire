<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Articles extends Component
{
    /*
    public $articles;

    public function mount(){
        $this->articles = \App\Models\Article::all();
    }
    */

    public function render()
    {
        return view('livewire.articles',[
            'articles' => \App\Models\Article::all()
        ]);
    }
}
