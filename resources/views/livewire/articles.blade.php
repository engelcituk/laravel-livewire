<div>
    <h1> Listado de articulos: </h1>
    <input type="text" placeholder="Buscar" wire:model="search">
    <br>
    {{$search}}
    <ul>
        @foreach ($articles as $article)
            <li>{{$article->title}}</li>
        @endforeach
    </ul>
    
</div>
