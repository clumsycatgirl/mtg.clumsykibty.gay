<div id="life" class="unselectable">
    <div class="absolute bottom-5 right-7 cursor-pointer" onclick="increaseLife(event)"
        oncontextmenu="decreaseLife(event)">
        <div class="bg-red-500 w-32 h-16 flex items-center justify-center flex-col">
            <h1 class="text-4xl font-extrabold" id="life-text">{{ $this->health }}</h1>
        </div>
    </div>
</div>