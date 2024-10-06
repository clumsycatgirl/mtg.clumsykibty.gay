<?php /**
  * @var Lib\Systems\Views\View $this
  * @var App\Models\DataClasses\Cards $this->card
  * @var App\Models\DataClasses\Cardidentifiers $this->identifier
  */
$this->multiverse_id = $this->identifier->multiverse_id();
$this->alt_text = $this->card->name();
?>

@set(title, "mtgsym")

@extend('layout/base')

@section('include')
@js('index.js' defer)
@js('card.js' defer)
@js('ws.js' defer)
@js('chat.js' defer)
@endsection

@section('header')
@include('opponent')
@endsection

@section('content')
<aside class="bg-gray-200 flex flex-col p-5 w-1/6 h-[100vh] absolute">
    <div class="h-full flex flex-col">
        <div id="show-card-container" class="h-1/2 align-middle items-center flex flex-col overflow-scroll">
            <div class="relative big-card">
                <div class="absolute inset-0 w-full h-full bg-blue-500 gradient-loading loading-placeholder"></div>
                <img id="show-card-image" class="absolute inset-0 w-full h-full hidden"
                    src="https://gatherer.wizards.com/Handlers/Image.ashx?multiverseid=<?= $this->multiverse_id ?>&type=card"
                    onload="showImage(this)">
            </div>
            <div>
                <h1 id="show-card-name" class="text-center"><b>{{ $this->card->name() }}</b></h1>
                <span id="show-card-text" style="white-space: pre-line;">
                    {{ $this->card->text() | html_entities strip_tags }}
                </span>
                <div class="flex flex-row justify-between">
                    <span id="show-card-cost">{{ $this->card->mana_cost() }}</span>
                    <span id="show-card-pt">{{ $this->card->power() }}/{{ $this->card->toughness() }}</span>
                </div>
            </div>
        </div>
        <div class="h-[45%]">
            <!-- Chat messages area -->
            <div class="flex-1 bg-white p-4 mb-4 rounded-lg shadow-inner h-full overflow-scroll"
                id="chat-messages-container">
                <!-- <div class="mb-2 p-2 bg-blue-100 rounded-lg">
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Ab aliquid nulla accusantium,
                        voluptates
                        itaque,
                        vitae
                        non ratione nam, aliquam architecto iusto? Nihil, cupiditate repellendus. Obcaecati aliquam
                        sapiente qui
                        laboriosam fugiat!</p>
                </div>
                <div class="mb-2 p-2 bg-blue-100 rounded-lg">
                    <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Enim nesciunt animi minima officiis
                        cumque
                        pariatur
                        corrupti! Obcaecati, repellendus nobis nisi sint a vitae qui laboriosam eos modi, soluta sit
                        recusandae?</p>
                </div>
                <div class="mb-2 p-2 bg-blue-100 rounded-lg">
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Blanditiis nihil hic dignissimos ad
                        aspernatur
                        alias
                        quas. Iste tempore harum minima delectus, porro architecto exercitationem quasi voluptates,
                        soluta, cumque
                        nesciunt optio.</p>
                </div> -->
            </div>
            <!-- end chat messages area -->

            <!-- Chat input area -->
            <div class="flex items-center">
                <input type="text" placeholder=":3?..." class="flex-1 p-2 border rounded-lg" id="send-message-input">
                <button class="ml-2 p-2 bg-blue-500 text-white rounded-lg" id="send-message-button">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
            </div>
            <!-- end chat input area -->
        </div>
    </div>
</aside>

<div class="ml-[16.666667%] w-max h-max">
    <div class="flex items-center justify-center flex-auto flex-col">
        <div class="align-middle self-center">
            <!-- deck -->
            <div class="absolute right-7 bottom-[7.5%] max-w-[7.5%] w-max">
                <div class="flex flex-col gap-4">
                    <button class="bl-btn" type="button" hx-get="/search" hx-trigger="click" hx-swap="innerHtml"
                        hx-target="#search-overlay-content" onclick="showSearch()">Search from deck</button>
                    <div class="flex flex-row flex-auto ml-1 p-1 space-x-2">
                        <input type="text" name="popup-text" id="popup-text"
                            class="w-3/4 p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <button class="w-max bl-btn px-4 py-2" type="button"
                            onclick="addTextBubbleFromInput()">Tag</button>
                    </div>
                    <button class="bl-btn" type="button" onclick="drawToken()">token</button>
                    <input class="bl-btn" type="file" name="deck" id="deck-upload" onchange="uploadDeck()"
                        hx-boost="true">
                    <button class="bl-btn" type="button" hx-get="/reset" hx-trigger="click" hx-swap="none"
                        hx-boost="true">reset</button>
                    <button class="bl-btn" type="button" hx-get="/shuffle" hx-trigger="click" hx-swap="none"
                        hx-boost="true" onclick="sendShuffleMessage()">shuffle</button>

                    <div id="deck" class="relative deck" onclick="drawFromDeck()">
                        <img class="absolute inset-0 w-full h-full" src="/assets/images/back.webp" alt="deck slot">
                        <div id="deck-overlay" class="absolute inset-0 w-full h-full bg-red-500 opacity-50 hidden">
                        </div>
                    </div>
                </div>
            </div>
            <!-- end deck -->

            <div id="board"></div>
        </div>
    </div>

    <div id="search-overlay" class="hidden absolute top-0 left-0 w-full h-full">
        <div class="bg-purple-200 opacity-60 w-full h-full absolute top-0 left-0"></div>
        <div class="relative z-10 p-4">
            <h1 class="font-black">Escape to stop searching, click to get that card</h1>
            <div id="search-overlay-content"></div>
        </div>
        <!-- <div class="m-10 flex flex-row flex-wrap">
      (div.flex.flex-col.m-2>{meow})*100
    </div> -->
    </div>

    <!-- hand -->
    <div class="hidden bg-red-500"></div>
    <div id="hand"
        class="absolute bottom-2 w-[70%] h-20 ml-[2.5%] flex flex-row flex-auto gap-4 align-middle justify-center">
        <div id="hand-start"></div>
        <!-- <div class="movable card relative top-0 left-0">
            <div class="absolute w-full h-full bg-blue-500 gradient-loading loading-placeholder"></div>
            <img class="absolute w-full h-full hidden"
                src="https://gatherer.wizards.com/Handlers/Image.ashx?multiverseid=<?= $this->multiverse_id ?>&type=card"
                alt="<?= $this->alt_text ?>" onload="showImage(this)" data-card="true">
            <div id="data" data-multiverse-id="<?= $this->identifier->multiverse_id() ?>"
                data-card-uuid="<?= $this->card->uuid() ?>" data-name="<?= $this->card->name() ?>"
                data-mana-cost="<?= $this->card->mana_cost() ?>" data-power="<?= $this->card->power() ?>"
                data-toughness="<?= $this->card->toughness() ?>" data-text="<?= $this->card->text() ?>">
            </div>
        </div>
        <div class="movable card relative top-0 left-0">
            <div class="absolute w-full h-full bg-blue-500 gradient-loading loading-placeholder"></div>
            <img class="absolute w-full h-full hidden"
                src="https://gatherer.wizards.com/Handlers/Image.ashx?multiverseid=<?= $this->multiverse_id ?>&type=card"
                alt="<?= $this->alt_text ?>" onload="showImage(this)" data-card="true">
            <div id="data" data-multiverse-id="<?= $this->identifier->multiverse_id() ?>"
                data-card-uuid="<?= $this->card->uuid() ?>" data-name="<?= $this->card->name() ?>"
                data-mana-cost="<?= $this->card->mana_cost() ?>" data-power="<?= $this->card->power() ?>"
                data-toughness="<?= $this->card->toughness() ?>" data-text="<?= $this->card->text() ?>">
            </div>
        </div>
        <div id="test-card" class="movable card relative top-0 left-0">
            <div class="absolute w-full h-full bg-blue-500 gradient-loading loading-placeholder"></div>
            <img class="absolute w-full h-full hidden"
                src="https://gatherer.wizards.com/Handlers/Image.ashx?multiverseid=<?= $this->multiverse_id ?>&type=card"
                alt="<?= $this->alt_text ?>" onload="showImage(this)" data-card="true">
            <div id="data" data-multiverse-id="<?= $this->identifier->multiverse_id() ?>"
                data-card-uuid="<?= $this->card->uuid() ?>" data-name="<?= $this->card->name() ?>"
                data-mana-cost="<?= $this->card->mana_cost() ?>" data-power="<?= $this->card->power() ?>"
                data-toughness="<?= $this->card->toughness() ?>" data-text="<?= $this->card->text() ?>">
            </div>
        </div> -->
    </div>
    <!-- end hand -->

    <div class="absolute bottom-0 left-[16.666667%] flex flex-col m-1 gap-4 p-7">
        <button type="button" class="bl-btn" id="open-gy-btn">show gy</button>
        <button type="button" class="bl-btn" id="send-to-gy-btn">send to gy</button>
    </div>

    <div id="graveyard" class="hidden absolute top-0 left-0 w-full h-full">
        <div class="bg-purple-200 opacity-60 w-full h-full absolute top-0 left-0"></div>
        <div class="relative z-10 p-4">
            <h1 class="font-black">Escape to stop searching, click to get that card</h1>
            <div id="graveyard-content" class="m-10 flex flex-row flex-wrap gap-4"></div>
        </div>
        <!-- <div class="m-10 flex flex-row flex-wrap">
      (div.flex.flex-col.m-2>{meow})*100
    </div> -->
    </div>

    <div class="hidden absolute bottom-0 left-0 mb-2 ml-2 bg-white text-black text-xs px-2 py-1 rounded shadow">
    </div>
</div>

<div class="hidden absolute top-0 left-0 w-full h-full bg-purple-200 opacity-60 z-999999999999999999">
    <div class="flex flex-row flex-auto gap-4 align-middle justify-center w-full h-full">
        <?php foreach ($this->tokens as $token): ?>
            <div>
                <div class="absolute movable card">
                    <div class="absolute inset-0 w-full h-full bg-blue-500 gradient-loading loading-placeholder"></div>
                    <img class="absolute inset-0 w-full h-full hidden"
                        src="https://gatherer.wizards.com/Handlers/Image.ashx?multiverseid=<?= $token[1]->multiverse_id() ?>&type=card"
                        onload="showImage(this)">
                    <div id="data" data-card-uuid="<?= $token[0]->uuid() ?>"
                        data-multiverse-id="<?= $token[1]->multiverse_id() ?>" data-name="<?= $this->card->name() ?>"
                        data-power="<?= $token[0]->power() ?>" data-toughness="<?= $token[0]->toughness() ?>">
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div id="dragging"></div>
@endsection

@section('footer')
@include('life')
@endsection
