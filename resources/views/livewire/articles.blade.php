<div>
    <h1> Listado de articulos: </h1>
    <a href="{{route('articles.create')}}">Crear</a>
    <input type="text" placeholder="Buscar" wire:model="search">
    <br>
    {{$search}}
    <ul>
        @foreach ($articles as $article)
            <li>{{$article->title}}</li>
        @endforeach
    </ul>
    
</div>
