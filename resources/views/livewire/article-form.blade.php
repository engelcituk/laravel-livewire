<div>
    <h1>Crear articulo</h1>
    <form wire:submit.prevent="save">
        <label for="titulo"> Título
            <input id="titulo" type="text" placeholder="título" name="title" wire:model="title">
            @error('title')<div>{{$message}}</div>@enderror
        </label>
        <label for="contenido">contenido
            <textarea name="content" id="contenido" cols="30" rows="10" wire:model="content"></textarea>
            @error('content')<div>{{$message}}</div>@enderror
        </label>
        <input type="submit" value="Guardar">
    </form>
</div>
