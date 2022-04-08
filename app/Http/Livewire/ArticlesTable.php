<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Article;

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
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    public function render()
    {
        return view('livewire.articles-table',[
            'articles' => Article::query()
                ->where('title', 'like', "%{$this->search}%")
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate(5)
        ])->layout('layouts.app'); // ->layout('layouts.app'), se puede quitar, porque eso usa livewire por defecto
    }

    public function sortBy($field){

        $this->sortField === $field
            ? $this->sortDirection = $this->sortDirection === 'asc' ? 'desc': 'asc'
            : $this->sortDirection = 'asc';
        
        $this->sortField = $field;

    }
}
