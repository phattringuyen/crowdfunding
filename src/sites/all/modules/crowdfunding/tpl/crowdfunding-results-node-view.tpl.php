<div id = "crowdfunding-overview">
  <div class="inner">
    <div class="backers">
      <p class="value"><?php print $backers_amount ?></p>
      <p class="label">backers</p>
    </div>
    <div class="funded">
      <p class="value"><?php print $funded ?></p>
      <p class="label"><?php print t('pledged of @amount goal', array('@amount' => $goal)) ?></p>
    </div>
    <div class="time-remaining">
      <p class="value"><?php print $time_remaining ?></p>
      <p class="label"><?php print $time_remaining_text ?></p>
    </div>
  </div>
</div>