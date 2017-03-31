<?php if (!empty($rewards)) : ?>
  <div id="reward-options">
    <?php foreach ($rewards as $reward) : ?>
      <div class="reward" data-product-id="<?php print $reward['product_id'] ?>">
        <h3><?php print t('Pledge @money or more', array('@money' => $reward['price'])) ?></h3>
        <div class="text"><?php print $reward['text'] ?></div>
      </div>
    <?php endforeach ?>
  </div>
<?php endif; ?>