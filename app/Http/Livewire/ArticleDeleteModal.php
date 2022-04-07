<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Storage;

class ArticleDeleteModal extends Component
{
    protected $listeners = ['confirmArticleDeletion'];

    public $article;
    public $showDeleteModal = false;

    public function render()
    {
        return view('livewire.article-delete-modal');
    }

    public function delete(){
        
        Storage::disk('public')->delete($this->article->image);

        $this->article->delete();

        
        session()->flash('flash.bannerStyle','danger');
        session()->flash('flash.banner',__('Artículo eliminado') );


        $this->redirect( route('articles.index') );
    }

    public function confirmArticleDeletion($article){
        if($this->article->id === $article['id']){
            $this->showDeleteModal = true;  
        }
    }
}
