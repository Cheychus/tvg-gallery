<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fotogalerie</title>
    <base href="/"/>

    <link rel="stylesheet" href="assets/css/tailwind-output.css">
    <link rel="stylesheet" href="assets/css/gradient.css">
</head>
<body class="w-full min-w-[320px] h-screen max-h-screen font-display tracking-wider">
<main class="grid grid-rows-[auto_minmax(0,_1fr)_auto] min-h-screen max-h-screen w-full min-w-[320px] diamond-gradient">

    <header class="w-full h-16 flex justify-between bg-tvg-blue-700">
        <div class="folderButtons h-full flex items-center">
            <button id="tab-1" data-id=1
                    class="tab px-1 sm:px-4 text-white text-xs sm:text-sm cursor-pointer">Galerie 1
            </button>
            <button id="tab-2" data-id=2
                    class="tab px-1 sm:px-4 text-tvg-blue-200 text-xs sm:text-sm hover:text-tvg-blue-100 cursor-pointer">
                Galerie 2
            </button>
        </div>
        <div class="h-full flex items-center">
            <label
                    for="toggle-select"
                    class="relative inline-block h-5 w-11 sm:h-6 sm:w-12 mr-1 sm:mr-4 cursor-pointer rounded-full bg-gray-300 transition [-webkit-tap-highlight-color:_transparent] has-[:checked]:bg-green-500"
            >
                <input type="checkbox" id="toggle-select" class="peer sr-only"/>

                <span
                        class="absolute inset-y-0 start-0 m-1 sm:size-4 size-3 rounded-full bg-white transition-all peer-checked:start-6"
                ></span>
            </label>
            <p id="select-count" class="text-xs text-tvg-blue-800"> 0 ausgewählt </p>
            <button id="deleteButton" class="p-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                     class="size-8 text-black cursor-pointer">
                    <path fill-rule="evenodd"
                          d="M16.5 4.478v.227a48.816 48.816 0 0 1 3.878.512.75.75 0 1 1-.256 1.478l-.209-.035-1.005 13.07a3 3 0 0 1-2.991 2.77H8.084a3 3 0 0 1-2.991-2.77L4.087 6.66l-.209.035a.75.75 0 0 1-.256-1.478A48.567 48.567 0 0 1 7.5 4.705v-.227c0-1.564 1.213-2.9 2.816-2.951a52.662 52.662 0 0 1 3.369 0c1.603.051 2.815 1.387 2.815 2.951Zm-6.136-1.452a51.196 51.196 0 0 1 3.273 0C14.39 3.05 15 3.684 15 4.478v.113a49.488 49.488 0 0 0-6 0v-.113c0-.794.609-1.428 1.364-1.452Zm-.355 5.945a.75.75 0 1 0-1.5.058l.347 9a.75.75 0 1 0 1.499-.058l-.346-9Zm5.48.058a.75.75 0 1 0-1.498-.058l-.347 9a.75.75 0 0 0 1.5.058l.345-9Z"
                          clip-rule="evenodd"/>
                </svg>
            </button>


        </div>

    </header>

    <div class="w-full p-2">
        <div id="mainContainer" class="w-full h-full bg-gray-500/30 rounded-lg">

        </div>

        <div id="imageOverlay" class="hidden fixed inset-0 flex justify-center items-center z-50">
            <div class="relative w-auto h-full flex items-center">
                <div id="loading-spinner">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="animate-spin absolute left-1/2 -translate-y-1/2">
                        <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
                    </svg>
                </div>
                <div class="transition-none z-50">
                    <svg id="arrow-left"
                         xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="ml-1 lucide arrow-left hidden
                     text-black bg-gray-300/70 rounded-full absolute left-0 top-1/2 transform -translate-y-1/2 cursor-pointer">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M16 12H8"/>
                        <path d="m12 8-4 4 4 4"/>
                    </svg>
                </div>


                <img id="overlayImage"
                     class="max-w-[100%] max-h-[100%] w-auto h-auto object-contain opacity-0 transition-[opacity] duration-300 ease-in-out">

                <div class="transition-none z-50">
                <svg id="arrow-right"
                     xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                     class="mr-1 lucide arrow-right hidden text-black bg-gray-300/70 rounded-full absolute right-0 top-1/2 transform -translate-y-1/2 cursor-pointer">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M8 12h8"/>
                    <path d="m12 16 4-4-4-4"/>
                </svg>
                </div>
            </div>
        </div>
    </div>
    <footer class="w-full h-16 bg-tvg-blue-700 flex items-center">
        <button id="uploadButton" type="button" class="w-1/2 md:w-1/3 ml-2 p-3 rounded font-bold text-sm cursor-pointer
                    bg-button hover:bg-button-hover text-white">
            UPLOAD
        </button>
        <input type="file" id="fileInput" name="file[]" multiple style="display: none;">

        <div id="progressContainer"></div>
        <div id="gallery"></div>
    </footer>

</main>

<script type="module" src="assets/js/main.js" defer></script>
</body>
</html>