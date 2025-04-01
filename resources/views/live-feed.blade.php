@section($landing_page)
<x-layout-downloads>
    {{-- https://nondrm.mystreamingserver.live/livecf/swarena2/playlist.m3u8 --}}
        <x-container class="px-0  bg-white">
            <video
                id="hls-example"
                class="video-js vjs-default-skin"
                width="400"
                height="300"
                controls
                >
                <source
                    type="application/x-mpegURL"
                    src="https://nondrm.mystreamingserver.live/livecf/swarena2/playlist.m3u8"
                />
                </video>
 
        </x-container>
</x-layout>
 
