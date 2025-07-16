<div class="flex space-x-2 gap-4">
    <x-filament::button wire:click="imprimerCamionSelectionne" :disabled="!$this->camion">
        Imprimer le camion sélectionné
    </x-filament::button>
    <x-filament::button wire:click="imprimerTousLesCamions">
        Imprimer tous les camions
    </x-filament::button>
</div>
