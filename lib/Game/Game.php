<?php

namespace Gizmo\Game;

use \Gizmo\Helper;
use \Gizmo\Space\Box;
use \Gizmo\Space\Rotation;
use \Gizmo\Space\Vector;
use \Gizmo\Space\ImageXYZ;
use \Gizmo\Space\Rect;
use \GifCreator\AnimGif;

class Game
{
	/**
	 * @param array $meta
	 */
	public function __construct($meta)
	{
		$this->version             = Helper::getElem($meta, 'version1') . '.' . Helper::getElem($meta, 'version2');
		$this->build_id            = Helper::getElem($meta, ['properties', 'BuildID']);
		$this->build_version       = Helper::getElem($meta, ['properties', 'BuildVersion']);
		$this->date                = Helper::fromDateStr(Helper::getElem($meta, ['properties', 'Date']));
		$this->game_id             = Helper::getElem($meta, ['properties', 'Id']);
		$this->game_version        = Helper::getElem($meta, ['properties', 'GameVersion']);
		$this->map_name            = Helper::getElem($meta, ['properties', 'MapName']);
		$this->match_type          = Helper::getElem($meta, ['properties', 'MatchType']);
		$this->player_name         = Helper::getElem($meta, ['properties', 'PlayerName']);
		$this->primary_player_team = Helper::getElem($meta, ['properties', 'PrimaryPlayerTeam']);
		$this->replay_version      = Helper::getElem($meta, ['properties', 'ReplayVersion']);
		$this->team0_score         = Helper::getElem($meta, ['properties', 'Team0Score']);
		$this->team1_score         = Helper::getElem($meta, ['properties', 'Team1Score']);
		$this->team_size           = Helper::getElem($meta, ['properties', 'TeamSize']);

		$this->ball = null;
		$this->players = [];
		$this->teams = [];
	}

	/**
	 * @param Frame $frame
	 * @param array of Actor $actors
	 */
	public function processFrame($frame, $actors)
	{
		static $gif_frames = [];

		print $frame . "\n";
		foreach ($actors as $actor_id => $actor) {
			switch ($actor->class_name) {

				case 'TAGame.Ball_TA':
					if (!$this->ball) {
						$this->ball = new Ball();
					}
					$this->ball->update($frame, $actor);
					break;

				case 'TAGame.PRI_TA':
					$player_id_property = $actor->getProperty('Engine.PlayerReplicationInfo:PlayerID');
					if (!$player_id_property) {
						break;
					}
					$player_id = $player_id_property->value;
					if (!array_key_exists($player_id, $this->players)) {
						$this->players[$player_id] = new Player();
					}
					$this->players[$player_id]->updatePRI($frame, $actor);
					break;

				case 'TAGame.Car_TA':
					$pri_property = $actor->getProperty('Engine.Pawn:PlayerReplicationInfo');
					if (!$pri_property) {
						break;
					}
					$pri_actor_id = $pri_property->value;
					$player = null;
					foreach ($this->players as $tmp_player) {
						if ($tmp_player->pri_actor_id == $pri_actor_id) {
							$player = $tmp_player;
							break;
						}
					}
					if (!$player) {
						break;
					}
					$player->update($frame, $actor);
					break;

				case 'TAGame.Team_TA':
					if (!array_key_exists($actor_id, $this->teams)) {
						$this->teams[$actor_id] = new Team();
					}
					switch ($actor->object_name) {
						case 'Archetypes.Teams.Team1':
							$this->teams[$actor_id]->setColor('orange');
							break;
						case 'Archetypes.Teams.Team0':
							$this->teams[$actor_id]->setColor('blue');
							break;
					}
			}
		}

		if ($frame->number < 100) {
			return;
		}

		$i = new ImageXYZ(800, 10000);
		$ball_color = $i->createColor(255, 255, 0);
		$struts_color = $i->createColor(100, 100, 100);
		$i->all_drawSphere($this->ball->hitbox, $ball_color);
		foreach ($this->players as $player) {
			$team_color1 = null;
			$team_color2 = null;
			if (array_key_exists($player->team_actor_id, $this->teams)) {
				switch ($this->teams[$player->team_actor_id]->getColor()) {
					case 'orange':
						$team_color1 = $i->createColor(203, 88, 0);
						$team_color2 = $i->createColor(102, 48, 0);
						break;
					case 'blue':
						$team_color1 = $i->createColor(0, 82, 222);
						$team_color2 = $i->createColor(0, 27, 141);
						break;
				}
			}
			$i->all_drawBox($player->hitbox, $team_color1, $struts_color, $team_color2);
		}
		$path = 'img/' . $frame->number . '-game.png';
		$i->output($path);
		$gif_frames[] = $path;
		if (sizeof($gif_frames) >= 1000) {
			$durations = [3];
			print "Creating gif..";
			$anim = new AnimGif();
			$anim->create($gif_frames, $durations);
			$anim->save('gifs/game.gif');
			print "OK\n";
			$gif_frames = [];
			exit;
		}

		/*
		foreach (['z', 'y', 'x'] as $axis) {
			if (!array_key_exists($axis, $gif_frames)) {
				$gif_frames[$axis] = [];
			}
			$i = new Image(800, $field);
			$text_color = $i->createColor(0, 255, 255);
			$struts_color = $i->createColor(100, 100, 100);
			$ball_color = $i->createColor(0, 255, 25);
			$i->drawText($axis, -5000, -5000, $text_color);
			$i->drawSphere($this->ball->hitbox, $axis, $ball_color);
			foreach ($this->players as $player) {
				$team_color1 = null;
				$team_color2 = null;
				if (array_key_exists($player->team_actor_id, $this->teams)) {
					switch ($this->teams[$player->team_actor_id]->getColor()) {
						case 'orange':
							$team_color1 = $i->createColor(203, 88, 0);
							$team_color2 = $i->createColor(102, 48, 0);
							break;
						case 'blue':
							$team_color1 = $i->createColor(0, 82, 222);
							$team_color2 = $i->createColor(0, 27, 141);
							break;
					}
				}
				$view_rects = $player->hitbox->viewRectsAlongAxis($axis);
				$i->drawText($player->name, $view_rects[0]->p0->x, $view_rects[0]->p0->y + 80, $team_color1);
				$i->drawBox($player->hitbox, $axis, $team_color1, $struts_color, $team_color2);
			}
			$path = 'img/' . $frame->number . '_' . $axis . '.png';
			$i->output($path);
			$gif_frames[$axis][] = $path;
			$break =false;
			if (sizeof($gif_frames[$axis]) >= 100) {
				$break = true;
				$durations = [3];
				print "Creating {$axis} gif..\n";
				$anim = new AnimGif();
				$anim->create($gif_frames[$axis], $durations);
				$anim->save('gifs/' . $axis . '.gif');
				$gif_frames[$axis] = [];
			}
		}
		*/

		print "\t" . $this->ball . "\n";
		foreach ($this->players as $player) {
			print "\t" . $player . "\n";
		}
	}
}
