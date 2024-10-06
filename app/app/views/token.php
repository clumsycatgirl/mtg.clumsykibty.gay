<?php /** 
  * @var Lib\Systems\Views\View $this
  * @var \App\Models\DataClasses\Tokens $this->token
  * @var \App\Models\DataClasses\Tokenidentifiers $this->identifier
  */
?>
<div class="relative movable card">
    <div class="absolute inset-0 w-full h-full bg-blue-500 gradient-loading loading-placeholder"></div>
    <img class="absolute inset-0 w-full h-full hidden"
        src="https://gatherer.wizards.com/Handlers/Image.ashx?multiverseid=<?= $this->identifier->multiverse_id() ?>&type=card"
        alt="<?= $this->token->name() ?>" onload="showImage(this)">
    <div id="data" data-token-uuid="<?= $this->token->uuid() ?>"
        data-multiverse-id="<?= $this->identifier->multiverse_id() ?>" data-name="<?= $this->token->name() ?>"
        data-mana-cost="<?= $this->token->mana_cost() ?>" data-power="<?= $this->token->power() ?>"
        data-toughness="<?= $this->token->toughness() ?>" data-text="<?= $this->token->text() ?>">
    </div>
</div>