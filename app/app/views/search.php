<?php /** 
  * @var Lib\Systems\Views\View $this 
  * @var array[] $this->cards */ ?>

<div class="m-10 flex flex-row flex-wrap gap-4">
    <?php foreach ($this->cards as $data):
        $card = $data[0];
        $identifier = $data[1]; ?>
        <div class="relative movable card">
            <div class="absolute inset-0 w-full h-full bg-blue-500 gradient-loading loading-placeholder"></div>
            <img class="absolute inset-0 w-full h-full hidden"
                src="https://gatherer.wizards.com/Handlers/Image.ashx?multiverseid=<?= $identifier->multiverse_id() ?>&type=card"
                alt="<?= $card->name() ?>" onload="showImage(this)" data-card="true" onclick="getCardFromDeckSearch(this)">
            <div id="data" data-card-uuid="<?= $card->uuid() ?>" data-multiverse-id="<?= $identifier->multiverse_id() ?>"
                data-name="<?= $card->name() ?>" data-mana-cost="<?= $card->mana_cost() ?>"
                data-power="<?= $card->power() ?>" data-toughness="<?= $card->toughness() ?>"
                data-text="<?= $card->text() ?>">
            </div>
        </div>
    <?php endforeach; ?>
</div>