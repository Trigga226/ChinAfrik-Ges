<div class="flex space-x-2 gap-4">
    <x-filament::button wire:click="imprimerMachineSelectionnee" :disabled="!$this->machine">
        Imprimer la machine sélectionnée
    </x-filament::button>
    <x-filament::button wire:click="imprimerToutesLesMachines">
        Imprimer toutes les machines
    </x-filament::button>
</div>
