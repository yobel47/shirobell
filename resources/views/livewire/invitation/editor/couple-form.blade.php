<form wire:submit="save" class="space-y-6">
    @foreach (['groom' => 'Mempelai Pria', 'bride' => 'Mempelai Wanita'] as $side => $title)
        <section class="card space-y-4" wire:key="side-{{ $side }}">
            <h2 class="font-serif text-lg font-semibold">{{ $title }}</h2>

            <x-editor.image-field
                :label="'Foto '.strtolower($title)"
                :model="$side.'Photo'"
                :upload="$side === 'groom' ? $groomPhoto : $bridePhoto"
                :current="$invitation->mediaUrl($invitation->{$side.'_photo_path'})"
                :remove="'removePhoto(\''.$side.'\')'"
                rounded
            />

            <div class="grid gap-4 sm:grid-cols-2">
                <x-editor.field :model="'form.'.$side.'_nickname'" label="Nama panggilan" maxlength="50" />
                <x-editor.field :model="'form.'.$side.'_fullname'" label="Nama lengkap (dengan gelar)" maxlength="150" />
                <x-editor.field :model="'form.'.$side.'_child_order'" label="Urutan anak" :placeholder="$side === 'groom' ? 'Putra pertama dari' : 'Putri kedua dari'" maxlength="50" />
                <x-editor.field :model="'form.'.$side.'_instagram'" label="Username Instagram" placeholder="username" maxlength="60" />
                <x-editor.field :model="'form.'.$side.'_father'" label="Nama ayah" placeholder="Bapak ..." maxlength="150" />
                <x-editor.field :model="'form.'.$side.'_mother'" label="Nama ibu" placeholder="Ibu ..." maxlength="150" />
            </div>
        </section>
    @endforeach

    <x-editor.save-bar />
</form>
