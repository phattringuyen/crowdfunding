<div id="financial-overview">
  <h4><?php print t('Financial overview') ?></h4>
  <?php
  $output_table[] = array(t('Author'), theme('username', array('account' => $Project->author->value())));
  $output_table[] = array(t('Goal'), commerce_currency_format($Project->getGoal(), commerce_default_currency()));
  $output_table[] = array(t('Funded'), commerce_currency_format($Project->getFunded(), commerce_default_currency()) . ' (' . ($Project->getFunded() / $Project->getGoal() * 100) . '%)');
  $output_table[] = array(t('Funding start'), format_date($Project->getFundingStartDate(), 'short'));
  $output_table[] = array(t('Funding end'), format_date($Project->getFundingEndDate(), 'short'));
  $output_table[] = array(t('Backers amount'), $Project->getBackersAmount());
  print theme('table', array('rows' => $output_table));
  ?>
</div>

<div id="backers-per-reward">
  <h4><?php print t('Backers amount per reward option') ?></h4>
  <?php
  $map = $Project->getBackersAmountPerRewardOption();
  $output_table = $map['pledges'];
  $output_table[] = array(t('Total'), $map['total']);
  print theme('table', array('rows' => $output_table));
  ?>
</div>