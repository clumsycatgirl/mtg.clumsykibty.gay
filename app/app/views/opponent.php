<div>
    <div class="absolute top-0 left-[16.666667%] flex flex-col m-1 gap-4 p-7 z-[999999999999999999]">
        <button type="button" class="bl-btn" id="open-opp-gy-btn">show opp gy</button>
    </div>
    <div id="opponent-graveyard" class="hidden absolute top-0 left-0 w-full h-full">
        <div class="bg-purple-200 opacity-60 w-full h-full absolute top-0 left-0"></div>
        <div class="relative z-10 p-4">
            <h1 class="font-black">Escape to stop searching</h1>
            <div id="opponent-graveyard-content" class="m-10 flex flex-row flex-wrap gap-4"></div>
        </div>
    </div>
    <div id="opponent-hand"
        class="absolute top-0 w-[70%] rotate-180 h-20 ml-[2.5%] flex flex-row flex-auto gap-4 align-middle justify-center">
        <div id="opponent-hand-start"></div>
        <!-- <div class="card relative top-0 left-0">
            <div class="absolute w-full h-full bg-blue-500 gradient-loading loading-placeholder"></div>
            <img class="absolute w-full h-full hidden" src="/assets/images/back.webp" alt="<?= $this->alt_text ?>"
                onload="showImage(this)" data-card="true">
            <div id="data" data-multiverse-id="<?= $this->identifier->multiverse_id() ?>"
                data-card-uuid="<?= $this->card->uuid() ?>" data-name="<?= $this->card->name() ?>"
                data-mana-cost="<?= $this->card->mana_cost() ?>" data-power="<?= $this->card->power() ?>"
                data-toughness="<?= $this->card->toughness() ?>" data-text="<?= $this->card->text() ?>">
            </div>
        </div> -->
    </div>
    <div id="opp-life" class="unselectable">
        <div class="absolute top-5 right-7 cursor-default">
            <div class="bg-red-500 w-32 h-16 flex items-center justify-center flex-col">
                <h1 class="text-4xl font-extrabold" id="opp-life-text">20</h1>
            </div>
        </div>
    </div>
</div>
