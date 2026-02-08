<x-layout>
    {!! \App\Helpers\AdvertisementHelper::generateClickTrackingScript() !!}

    <div class="min-h-screen bg-black">
        <div class="min-h-screen">
            <livewire:reel-show :video="$video" />
        </div>
    </div>

    <livewire:report-modal />
</x-layout>
