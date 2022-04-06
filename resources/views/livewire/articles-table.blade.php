<div>
    <x-slot name="header">
        <h1> Listado de articulos: </h1>
    </x-slot>
    <a href="{{route('articles.create')}}">Crear</a>
    <input type="text" placeholder="Buscar" wire:model="search">
    <br>
    {{$search}}
    <ul>
        @foreach ($articles as $article)
            <li>
                <a href="{{route('articles.show', $article)}}"> {{$article->title}} </a>
                <a href="{{route('articles.edit', $article)}}"> Editar </a>
            </li>
        @endforeach
    </ul>
    
</div>