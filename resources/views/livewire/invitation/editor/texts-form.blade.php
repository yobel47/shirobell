<form wire:submit="save" class="space-y-6">
    <section class="card space-y-4">
        <h2 class="font-serif text-lg font-semibold">Teks undangan</h2>
        <x-editor.field model="form.opening_text" label="Salam pembuka" type="textarea" maxlength="1000" hint="Tampil sebelum data mempelai." />
        <x-editor.field model="form.quote_text" label="Kutipan atau ayat (opsional)" type="textarea" maxlength="1000" placeholder="Dan di antara tanda-tanda kekuasaan-Nya ..." />
        <x-editor.field model="form.quote_source" label="Sumber kutipan" placeholder="QS. Ar-Rum: 21" maxlength="150" />
        <x-editor.field model="form.closing_text" label="Penutup" type="textarea" maxlength="1000" />
    </section>

    <x-editor.save-bar />
</form>
