<?php
/**
 * @version        $Id: default.php 104 2008-05-23 16:17:55Z julienv $
 * @package        JoomlaTracks
 * @copyright      Copyright (C) 2008 Julien Vonthron. All rights reserved.
 * @license        GNU/GPL, see LICENSE.php
 * Joomla Tracks is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
// no direct access
use Joomla\CMS\Plugin\PluginHelper;
use Tracks\Layout\LayoutHelper;

defined('_JEXEC') or die('Restricted access');
jimport('joomla.filter.output');

$dispatcher = JDispatcher::getInstance();
JPluginHelper::importPlugin('content');

$enabledCustomFields = PluginHelper::isEnabled('tracks', 'customfields');
$customFields        = $enabledCustomFields ? \FieldsHelper::getFields('com_tracks.eventresult') : [];
?>
<div id="tracks" class="tracks-roundresult f1esportsrd">

	<?= LayoutHelper::render('project.navigation', ['project' => $this->project]) ?>
	<div class="f1esportsrd__project-title">
		<?= $this->project->name; ?>
	</div>
	<div class="f1esportsrd__subtitle">
		Grand Prix de <?= $this->round->name; ?>
	</div>

	<?php if ($this->params->get('resultview_results_showrounddesc', 1) && !empty($this->round->description)): ?>
		<div class="tracks-round-description">
			<?php
			// parse description with content plugins
			echo JHTML::_('content.prepare', $this->round->description);
			?>
		</div>
	<?php endif; ?>

	<?php if (!empty($this->projectround->description)): ?>
		<div class="tracks-projectround-description">
			<?php
			// Parse description with content plugins
			echo JHTML::_('content.prepare', $this->projectround->description);
			?>
		</div>
	<?php endif; ?>

	<?php
	foreach ($this->results as $subround)
	{
		?>
		<?php if ($this->params->get('resultview_results_showsubrounddesc', 1) && !empty($subround->description)): ?>
		<div class="tracks-round-description">
			<?php
			// parse description with content plugins
			echo JHTML::_('content.prepare', $subround->description);
			?>
		</div>
	<?php endif; ?>

		<?php if ($subround->results): ?>
		<div class="f1esportsrd__table-wrap">
		<table class="raceResults" cellspacing="0" cellpadding="0" summary="">
			<thead>
			<tr>
				<th colspan="4" class="raceResults__head raceResults__head--title"><?= $this->round->name ?></th>
				<th class="raceResults__head raceResults__head--time">Tiempo</th>
				<th class="raceResults__head raceResults__head--points">Puntos</th>
			</tr>
			</thead>
			<tbody>
			<?php
			$k = 0;
			foreach ($subround->results AS $result)
			{
				$resultEntity = new TrackslibEntityEventresult($result->id);
				$resultEntity->bind($result);

				$ind_slug = $result->individual_id . ':' . JFilterOutput::stringURLSafe($result->first_name . ' ' . $result->last_name);
				$link_ind = JRoute::_(TrackslibHelperRoute::getIndividualRoute($ind_slug, $this->project->slug));
				$team_slug = $result->team_id . ':' . JFilterOutput::stringURLSafe($result->team_name);
				$link_team = JRoute::_(TrackslibHelperRoute::getTeamRoute($team_slug));

				$team       = TrackslibEntityTeam::load($result->team_id);
				$teamAccent = $team->getCustomFieldValue(11, '#aFaFaF');
				$dnf        = $resultEntity->getCustomFieldValue(10, false);
				?>
				<tr class="<?php echo($k++ % 2 ? 'd1' : 'd0'); ?>">
					<td class="raceResults__col raceResults__col--rank">
						<div class="raceResults__rank">
						<?php if ($result->rank)
						{
							echo $result->rank;
						}
						else
						{
							echo "-";
						} ?>
						</div>
					</td>

					<td class="raceResults__col raceResults__col--accent">
						<span class="raceResults__team-accent" style="background-color: <?= $teamAccent ?>"></span>
					</td>

					<td class="raceResults__col raceResults__col--nickname">
						<a href="<?php echo $link_ind; ?>"
						   title="<?php echo $result->nickname; ?>">
							<?php echo $result->nickname; ?>
						</a>
					</td>

					<td class="raceResults__col raceResults__col--team">
						<?php if ($result->team_id): ?>
							<a href="<?php echo $link_team; ?>"
							   title="<?= $result->team_name; ?>">
								<?php if (!empty($team->vehicle_picture)): ?>
									<img src="<?= $team->vehicle_picture ?>" class="raceResults__team-img" alt="<?php echo $result->team_name; ?>"/>
								<?php else: ?>
									<?php echo $result->team_name; ?>
								<?php endif; ?>
							</a>
						<?php endif; ?>
					</td>

					<td class="raceResults__col raceResults__col--perf">
						<?php echo $dnf ?: $result->performance; ?>
					</td>

					<td class="raceResults__col raceResults__col--points">
						<?php echo $result->points + $result->bonus_points ?: ''; ?>
					</td>
				</tr>
			<?php
			}
			?>
			</thead>
		</table>
		</div>
	<?php else: ?>
		<span id="no-results"><?php echo JText::_('COM_TRACKS_VIEW_ROUNDRESULT_NO_RESULTS_YET'); ?></span>
	<?php endif;?>

		<?php if (!empty($subround->comment)): ?>
			<div class="f1esportsrd__comment">
				<?= $subround->comment ?>
			</div>
		<?php endif;?>

	<?php
	}
	?>

	<p class="copyright">
		<?php echo TrackslibHelperTools::footer(); ?>
	</p>
</div>
