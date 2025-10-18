<x-layouts.main>
    <x-slot:title>Escaner</x-slot:title>
    <x-slot:head>
        <script src="https://cdn.jsdelivr.net/npm/dynamsoft-barcode-reader-bundle@11.0.6000/dist/dbr.bundle.js"></script>
    </x-slot:head>
    <h2>Escaner</h2>
    <div id="camera-view-container" style="width: 100%; height: 60vh"></div>
<textarea id="results" style="width: 100%; min-height: 10vh; font-size: 3vmin; overflow: auto" disabled></textarea>
</x-layouts.main>

@vite(['resources/js/scanner.js'])