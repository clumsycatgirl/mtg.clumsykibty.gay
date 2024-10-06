<?php /** 
  * @var Lib\Systems\Views\View $this
  * @var \App\Models\DataClasses\Cards $this->card
  * @var \App\Models\DataClasses\Cardidentifiers $this->identifier
  */ ?>
<div class="absolute movable card">
  <div class="absolute inset-0 w-full h-full bg-blue-500 gradient-loading loading-placeholder"></div>
  <img class="absolute inset-0 w-full h-full hidden"
    src="https://gatherer.wizards.com/Handlers/Image.ashx?multiverseid=<?= $this->identifier->multiverse_id() ?>&type=card"
    alt="<?= $this->card->name() ?>" onload="showImage(this)" data-card="true">
  <div id="data" data-card-uuid="<?= $this->card->uuid() ?>"
    data-multiverse-id="<?= $this->identifier->multiverse_id() ?>" data-name="<?= $this->card->name() ?>"
    data-mana-cost="<?= $this->card->mana_cost() ?>" data-power="<?= $this->card->power() ?>"
    data-toughness="<?= $this->card->toughness() ?>" data-text="<?= $this->card->text() ?>">
  </div>
</div>