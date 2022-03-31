<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
           {{ __('New article') }} 
        </h2>
    </x-slot>
    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <x-jet-form-section submit="save">
                <x-slot name="title">
                    {{ __('New article') }} 
                </x-slot>
                <x-slot name="description">
                    {{ __('Some description') }} 
                </x-slot>
                
                <x-slot name="form">
                    <div class="col-span-6 sm:col-span-4">
                        <pre>{{$article->title}}</pre>
                        <pre>{{$article->content}}</pre> 

                        <x-jet-label :value="__('Title')" for="title"/>
                        <x-jet-input wire:model="article.title" type="text" id="title" name="title" class="mt-1 block w-full"/>
                        <x-jet-input-error for="article.title" class="mt-2"/>
                    </div>
                   
                    <div class="col-span-6 sm:col-span-4">
                        <x-jet-label :value="__('Slug')" for="slug"/>
                        <x-jet-input wire:model="article.slug" type="text" id="slug" name="slug" class="mt-1 block w-full"/>
                        <x-jet-input-error for="article.slug" class="mt-2"/>
                    </div>

                    <div class="col-span-6 sm:col-span-4">
                        <x-jet-label :value="__('Content')" for="content"/>
                        <x-html-editor rows="3" wire:model="article.content" id="content" name="content" class="mt-1 block w-full"></x-html-editor>
                        {{-- <x-textarea rows="3" wire:model="article.content" id="content" name="content" class="mt-1 block w-full"/> --}}
                        <x-jet-input-error for="article.content" class="mt-2"/>
                    </div> 
                    <x-slot name="actions">
						<x-jet-button>
							{{ __('Save') }}
						</x-jet-button>
					</x-slot>
                </x-slot>
            </x-jet-form-section>
        </div>
    </div>
</div>
